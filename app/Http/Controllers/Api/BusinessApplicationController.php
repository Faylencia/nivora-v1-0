<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreBusinessApplicationRequest;
use App\Models\Business;
use App\Models\BusinessApplication;
use App\Models\MemberNotification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessApplicationController extends ApiController
{
    public function businessOptions(): JsonResponse
    {
        $businesses = Business::query()->where('status', 'active')
            ->orderBy('name')->get();

        return $this->success($businesses->map(fn (Business $business): array => [
            'id' => $business->id,
            'name' => $business->name,
            'business_id' => $business->id,
            'location_name' => $business->locationName(),
            'outlet_id' => $business->id,
            'outlet_name' => $business->locationName(),
        ])->values(), 'Bisnis aktif untuk konteks Super Admin.');
    }

    public function store(StoreBusinessApplicationRequest $request): JsonResponse
    {
        $user = $this->user($request);

        abort_if($user->roles()->where('roles.name', 'owner')->exists(), 409, 'Akun ini sudah memiliki akses owner.');
        abort_if(
            BusinessApplication::query()->where('applicant_id', $user->id)->where('status', 'pending')->exists(),
            409,
            'Pengajuan toko yang masih menunggu sudah ada.',
        );

        $application = BusinessApplication::query()->create([
            ...$request->validated(),
            'applicant_id' => $user->id,
            'status' => 'pending',
        ]);
        $this->activityLogger->record(
            $user,
            'business_application.submitted',
            "mengirim pengajuan toko {$application->business_name}",
            $application,
        );

        return $this->success($application, 'Pengajuan toko dikirim untuk ditinjau super admin.', 201);
    }

    public function index(Request $request): JsonResponse
    {
        $applications = BusinessApplication::query()
            ->with(['applicant:id,name,email,phone', 'reviewer:id,name', 'business:id,name'])
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')->toString()))
            ->latest()
            ->paginate(min(100, max(1, $request->integer('limit', 20))));

        return $this->success([
            'items' => $applications->items(),
            'pagination' => [
                'page' => $applications->currentPage(),
                'limit' => $applications->perPage(),
                'total' => $applications->total(),
            ],
        ], 'Pengajuan bisnis berhasil diambil.');
    }

    public function myApplications(Request $request): JsonResponse
    {
        $applications = BusinessApplication::query()->where('applicant_id', $this->user($request)->id)
            ->with(['reviewer:id,name', 'business:id,name'])
            ->latest()
            ->get();

        return $this->success($applications, 'Status pengajuan toko Anda.');
    }

    public function approve(Request $request, BusinessApplication $application): JsonResponse
    {
        $result = DB::transaction(function () use ($application, $request): array {
            $application = BusinessApplication::query()->lockForUpdate()->findOrFail($application->id);
            abort_unless($application->status === 'pending', 409, 'Pengajuan ini sudah ditinjau.');

            $owner = User::query()->lockForUpdate()->findOrFail($application->applicant_id);
            $business = Business::query()->create([
                'name' => $application->business_name,
                'location_name' => $application->outlet_name ?: $application->business_name,
                'business_type' => $application->business_type,
                'phone' => $application->phone,
                'address' => $application->outlet_address ?: $application->address,
                'status' => 'active',
            ]);
            $ownerRole = Role::query()->firstOrCreate(['name' => 'owner'], ['description' => 'Business owner or store administrator.']);

            if (! $owner->roles()->where('roles.id', $ownerRole->id)->wherePivot('business_id', $business->id)->exists()) {
                $owner->roles()->attach($ownerRole->id, ['business_id' => $business->id]);
            }

            $application->update([
                'status' => 'approved',
                'reviewed_by' => $request->user()->id,
                'business_id' => $business->id,
            ]);
            MemberNotification::query()->create([
                'user_id' => $owner->id,
                'business_id' => $business->id,
                'type' => 'business_application',
                'title' => 'Pengajuan toko disetujui',
                'message' => "Pengajuan {$business->name} disetujui. Akun Anda sekarang dapat mengelola toko.",
                'data' => ['business_id' => $business->id],
            ]);

            return [$application->fresh(), $business];
        });
        $this->activityLogger->record(
            $request->user(),
            'business_application.approved',
            "menyetujui pengajuan dan membuat bisnis {$result[1]->name}",
            $result[1],
            ['application_id' => $result[0]->id],
        );

        return $this->success([
            'application' => $result[0],
            'business' => $result[1],
            'location' => [
                'id' => $result[1]->id,
                'name' => $result[1]->locationName(),
                'address' => $result[1]->address,
                'phone' => $result[1]->phone,
            ],
        ], 'Toko disetujui dan akses owner telah dibuat.');
    }

    public function reject(Request $request, BusinessApplication $application): JsonResponse
    {
        $data = $request->validate(['review_note' => ['nullable', 'string', 'max:2000']]);
        $application = DB::transaction(function () use ($application, $request, $data): BusinessApplication {
            $application = BusinessApplication::query()->lockForUpdate()->findOrFail($application->id);
            abort_unless($application->status === 'pending', 409, 'Pengajuan ini sudah ditinjau.');
            $application->update([
                'status' => 'rejected',
                'reviewed_by' => $request->user()->id,
                'review_note' => $data['review_note'] ?? null,
            ]);
            MemberNotification::query()->create([
                'user_id' => $application->applicant_id,
                'type' => 'business_application',
                'title' => 'Status pengajuan toko diperbarui',
                'message' => $data['review_note'] ?? 'Pengajuan toko belum dapat disetujui saat ini.',
                'data' => ['application_id' => $application->id, 'status' => 'rejected'],
            ]);

            return $application->fresh();
        });
        $this->activityLogger->record(
            $request->user(),
            'business_application.rejected',
            "menolak pengajuan toko {$application->business_name}",
            $application,
        );

        return $this->success($application, 'Pengajuan toko ditolak.');
    }
}
