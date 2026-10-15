<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\BusinessResource;
use App\Http\Resources\UserResource;
use App\Models\Business;
use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SuperAdminController extends ApiController
{
    public function accounts(Request $request): JsonResponse
    {
        $accounts = User::query()
            ->whereHas('roles', fn (Builder $query) => $query->whereIn('roles.name', ['owner', 'member']))
            ->with(['roles', 'businesses'])
            ->when($request->filled('role'), function (Builder $query) use ($request): void {
                $query->whereHas('roles', fn (Builder $roleQuery) => $roleQuery->where('roles.name', $request->string('role')->toString()));
            })
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function (Builder $userQuery) use ($search): void {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(min(100, max(1, $request->integer('limit', 20))));

        return $this->success([
            'items' => $accounts->getCollection()->map(fn (User $account): array => [
                ...(new UserResource($account))->toArray($request),
                'businesses' => $account->businesses->pluck('name')->unique()->values()->all(),
            ]),
            'pagination' => [
                'page' => $accounts->currentPage(),
                'limit' => $accounts->perPage(),
                'total' => $accounts->total(),
            ],
        ], 'Daftar akun member dan owner berhasil diambil.');
    }

    public function storeAccount(Request $request): JsonResponse
    {
        if ($request->filled('phone')) {
            $request->merge(['phone' => preg_replace('/\\D+/', '', (string) $request->input('phone'))]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['required', 'string', 'min:7', 'max:30', 'regex:/^\\d+$/', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in(['member', 'owner'])],
            'business_id' => ['required_if:role,owner', 'nullable', 'integer', Rule::exists('businesses', 'id')->where('status', 'active')],
        ]);

        $account = DB::transaction(function () use ($data): User {
            $account = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'status' => 'active',
            ]);
            $role = Role::query()->firstOrCreate(
                ['name' => $data['role']],
                ['description' => $data['role'] === 'owner' ? 'Business owner or store administrator.' : 'Nivora member account.'],
            );

            $account->roles()->attach($role->id, [
                'business_id' => $data['role'] === 'owner' ? $data['business_id'] : null,
            ]);

            return $account;
        });
        $this->activityLogger->record($request->user(), 'account.created', "membuat akun {$account->name}", $account, ['role' => $data['role']]);

        return $this->success(new UserResource($account->load('roles')), 'Akun berhasil dibuat.', 201);
    }

    public function members(Request $request): JsonResponse
    {
        $members = User::query()
            ->whereHas('roles', fn (Builder $query) => $query->where('roles.name', 'member')->whereNull('user_roles.business_id'))
            ->with('roles')
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function (Builder $userQuery) use ($search): void {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(min(100, max(1, $request->integer('limit', 20))));

        return $this->success([
            'items' => $members->getCollection()->map(fn (User $member): array => (new UserResource($member))->toArray($request)),
            'pagination' => [
                'page' => $members->currentPage(),
                'limit' => $members->perPage(),
                'total' => $members->total(),
            ],
        ], 'Daftar akun member berhasil diambil.');
    }

    public function updateUser(Request $request, User $user): JsonResponse
    {
        if ($request->filled('phone')) {
            $request->merge(['phone' => preg_replace('/\D+/', '', (string) $request->input('phone'))]);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'email' => ['sometimes', 'required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['sometimes', 'required', 'string', 'min:7', 'max:30', 'regex:/^\d+$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'status' => ['sometimes', 'required', Rule::in(['active', 'inactive'])],
            'password' => ['sometimes', 'required', 'confirmed', Password::min(8)],
        ]);
        abort_if($data === [], 422, 'Masukkan setidaknya satu perubahan akun.');

        $user->update($data);
        $this->activityLogger->record($request->user(), 'account.updated', "memperbarui akun {$user->name}", $user, ['changes' => array_keys($data)]);

        return $this->success(new UserResource($user->fresh()->load('roles')), 'Akun pengguna berhasil diperbarui.');
    }

    public function destroyUser(Request $request, User $user): JsonResponse
    {
        abort_if($user->isSuperAdmin(), 403, 'Akun Super Admin tidak dapat dihapus dari halaman ini.');
        abort_unless($user->roles()->whereIn('roles.name', ['owner', 'member'])->exists(), 404, 'Akun tidak ditemukan.');
        abort_if(
            $user->sales()->exists() || $user->stockMovements()->exists(),
            409,
            'Akun ini memiliki riwayat transaksi atau stok. Nonaktifkan akun jika aksesnya perlu dihentikan.',
        );

        $this->activityLogger->record($request->user(), 'account.deleted', "menghapus akun {$user->name}", $user);
        $user->delete();

        return $this->success(null, 'Akun berhasil dihapus.');
    }

    public function storeBusiness(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'location_name' => ['nullable', 'string', 'max:150'],
            'business_type' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'photo' => ['nullable', 'image', 'max:3072'],
        ]);

        if ($request->hasFile('photo')) {
            $data['logo'] = $request->file('photo')->store('businesses', 'public');
        } else {
            $data['logo'] = '/images/default-store.svg';
        }

        unset($data['photo']);
        $business = Business::query()->create($data);
        $this->activityLogger->record($request->user(), 'business.created', "menambahkan bisnis {$business->name}", $business);

        return $this->success(new BusinessResource($business), 'Toko berhasil ditambahkan.', 201);
    }

    public function updateBusiness(Request $request, Business $business): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'location_name' => ['sometimes', 'nullable', 'string', 'max:150'],
            'business_type' => ['sometimes', 'required', 'string', 'max:100'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'address' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'status' => ['sometimes', 'required', Rule::in(['active', 'inactive'])],
            'photo' => ['sometimes', 'nullable', 'image', 'max:3072'],
            'remove_photo' => ['sometimes', 'boolean'],
        ]);
        abort_if($data === [], 422, 'Masukkan setidaknya satu perubahan bisnis.');

        $oldLogo = $business->logo;
        if ($request->hasFile('photo')) {
            $data['logo'] = $request->file('photo')->store('businesses', 'public');
        } elseif ($request->boolean('remove_photo')) {
            $data['logo'] = null;
        }
        unset($data['photo'], $data['remove_photo']);
        $business->update($data);
        $this->activityLogger->record($request->user(), 'business.updated', "memperbarui bisnis {$business->name}", $business, ['changes' => array_keys($data)]);
        if ($oldLogo && $business->logo !== $oldLogo) {
            Storage::disk('public')->delete($oldLogo);
        }

        return $this->success(new BusinessResource($business->fresh()), 'Profil bisnis berhasil diperbarui.');
    }

    public function destroyBusiness(Request $request, Business $business): JsonResponse
    {
        $hasRelatedRecords = $business->users()->exists()
            || $business->categories()->exists()
            || $business->products()->exists()
            || $business->sales()->exists()
            || $business->promos()->exists()
            || $business->customers()->exists()
            || $business->aiRecommendations()->exists()
            || $business->storeAnalyses()->exists();

        abort_if($hasRelatedRecords, 409, 'Toko ini masih memiliki akun atau data operasional. Nonaktifkan toko jika aksesnya perlu dihentikan.');

        $this->activityLogger->record($request->user(), 'business.deleted', "menghapus bisnis {$business->name}", $business);
        if ($business->logo) {
            Storage::disk('public')->delete($business->logo);
        }

        $business->delete();

        return $this->success(null, 'Toko berhasil dihapus.');
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
        $duplicate = Category::query()->whereNull('business_id')->where('name', $data['name'])->exists();
        if ($duplicate) {
            return response()->json(['success' => false, 'message' => 'Nama kategori umum tersebut sudah digunakan.'], 409);
        }

        $category = Category::query()->create([...$data, 'business_id' => null]);
        $this->activityLogger->record($request->user(), 'category.created', "membuat kategori {$category->name}", $category);

        return $this->success($category, 'Kategori berhasil dibuat.', 201);
    }

    public function updateCategory(Request $request, Category $category): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ]);
        abort_if($data === [], 422, 'Masukkan setidaknya satu perubahan kategori.');

        if (isset($data['name']) && Category::query()
            ->where('name', $data['name'])
            ->where(fn (Builder $query): Builder => $query->whereNull('business_id')->orWhere('business_id', $category->business_id))
            ->whereKeyNot($category->id)
            ->exists()) {
            return response()->json(['success' => false, 'message' => $category->business_id === null ? 'Nama kategori umum tersebut sudah digunakan.' : 'Nama kategori sudah digunakan pada bisnis ini.'], 409);
        }

        $category->update($data);
        $this->activityLogger->record($request->user(), 'category.updated', "memperbarui kategori {$category->name}", $category, ['changes' => array_keys($data)]);

        return $this->success($category->fresh(), 'Kategori berhasil diperbarui.');
    }

    public function destroyCategory(Request $request, Category $category): JsonResponse
    {
        abort_if($category->products()->exists(), 409, 'Kategori masih digunakan oleh produk. Hapus atau pindahkan produk terlebih dahulu.');

        $this->activityLogger->record($request->user(), 'category.deleted', "menghapus kategori {$category->name}", $category);
        $category->delete();

        return $this->success(null, 'Kategori berhasil dihapus.');
    }
}
