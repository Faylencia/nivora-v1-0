<?php

use App\Models\Business;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $activeProducts = fn (Builder|Relation $query) => $query
        ->where('is_active', true)
        ->whereHas('business', fn (Builder $businessQuery) => $businessQuery->where('status', 'active'));

    $featuredProducts = Product::query()
        ->with(['business', 'category'])
        ->where($activeProducts)
        ->withSum(['saleItems as completed_sold_quantity' => fn (Builder $query) => $query->whereHas('sale', fn (Builder $saleQuery) => $saleQuery->where('status', 'completed'))], 'quantity')
        ->orderByDesc('completed_sold_quantity')
        ->orderByRaw("CASE WHEN image IS NULL OR image = '' THEN 1 ELSE 0 END")
        ->orderByDesc('id')
        ->limit(8)
        ->get();

    $categories = Category::query()
        ->whereHas('products', $activeProducts)
        ->withCount(['products as active_products_count' => $activeProducts])
        ->with(['products' => function (Builder|Relation $query) use ($activeProducts): void {
            $activeProducts($query);
            $query->with(['business', 'category'])
                ->orderByRaw("CASE WHEN image IS NULL OR image = '' THEN 1 ELSE 0 END")
                ->orderByDesc('id')
                ->limit(4);
        }])
        ->orderBy('name')
        ->get();

    $categories = $categories
        ->groupBy(fn (Category $category): string => mb_strtolower(trim($category->name)))
        ->map(function (Collection $matchingCategories): Category {
            $category = $matchingCategories->first();
            $category->active_products_count = $matchingCategories->sum('active_products_count');
            $category->setRelation('products', $matchingCategories
                ->flatMap(fn (Category $matchingCategory) => $matchingCategory->products)
                ->unique('id')
                ->take(4)
                ->values());

            return $category;
        })
        ->values();

    return view('public.home', [
        'businessCount' => Business::query()->where('status', 'active')->count(),
        'productCount' => Product::query()
            ->where('is_active', true)
            ->whereHas('business', fn (Builder $query) => $query->where('status', 'active'))
            ->count(),
        'featuredProducts' => $featuredProducts,
        'categories' => $categories,
    ]);
})->name('home');

Route::get('/explore', function (Request $request) {
    $businesses = Business::query()
        ->where('status', 'active')
        ->when($request->filled('search'), function (Builder $query) use ($request): void {
            $search = $request->string('search')->toString();
            $query->where(function (Builder $businessQuery) use ($search): void {
                $businessQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('business_type', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        })
        ->withCount(['products as active_products_count' => fn (Builder $query) => $query->where('is_active', true)])
        ->orderBy('name')
        ->paginate(12)
        ->withQueryString();

    return view('public.explore', compact('businesses'));
})->name('explore');

Route::get('/business/{business}', function ($business) {
    $business = Business::query()->where('status', 'active')->findOrFail($business);

    return view('public.business.show', compact('business'));
})->name('business.show');

Route::get('/product/{product}', function ($product) {
    $product = Product::query()
        ->where('is_active', true)
        ->whereHas('business', fn (Builder $query) => $query->where('status', 'active'))
        ->findOrFail($product);

    return view('public.product.show', compact('product'));
})->name('product.show');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::prefix('owner')->name('owner.')->group(function () {
    Route::get('/', function () {
        return view('owner.index');
    })->name('dashboard');

    Route::get('/settings', function () {
        return view('owner.settings');
    })->name('settings');

    Route::get('/business', function () {
        return view('owner.business.index');
    })->name('business.index');

    Route::get('/outlets', function () {
        return redirect()->route('owner.business.index');
    })->name('outlets.index');

    Route::get('/outlets/{outlet}/edit', function ($outlet) {
        return redirect()->route('owner.business.index');
    })->name('outlets.edit');

    Route::get('/products/create', function () {
        return view('owner.products.create');
    })->name('products.create');

    Route::get('/products/{product}/edit', function ($product) {
        return redirect()->route('owner.products.index', ['edit' => $product]);
    })->name('products.edit');

    Route::get('/products/{product}', function ($product) {
        return view('owner.products.show', ['product' => $product]);
    })->name('products.show');

    Route::get('/products', function () {
        return view('owner.products.index');
    })->name('products.index');

    Route::get('/customers', function () {
        return view('owner.customers.index');
    })->name('customers.index');

    Route::get('/promotions', function () {
        return view('owner.promotions.index');
    })->name('promotions.index');

    Route::get('/sales', function () {
        return view('owner.sales.index');
    })->name('sales.index');

    Route::get('/payments', function () {
        return view('owner.payments.index');
    })->name('payments.index');

    Route::get('/ai', function () {
        return view('owner.ai.index');
    })->name('ai.index');

    Route::get('/ai/store-analysis', function () {
        return view('owner.ai.store-analysis');
    })->name('ai.store-analysis');

    Route::get('/cashiers', function () {
        return view('owner.cashiers.index');
    })->name('cashiers.index');
});

Route::get('/super-admin', function () {
    return view('super-admin.dashboard', [
        'businessCount' => Business::query()->count(),
        'ownerCount' => User::query()->whereHas('roles', fn (Builder $query) => $query->where('roles.name', 'owner'))->count(),
        'productCount' => Product::query()->count(),
        'categoryCount' => Category::query()->count(),
        'businesses' => Business::query()->withCount('products')->latest()->limit(5)->get(),
        'activities' => DB::table('platform_activities')->latest('created_at')->limit(6)->get(),
    ]);
})->name('super-admin.dashboard');

Route::get('/super-admin/accounts', function () {
    return view('super-admin.members');
})->name('super-admin.accounts');

Route::prefix('/super-admin/categories')->name('super-admin.categories.')->group(function () {
    Route::get('/', function () {
        $categories = Category::query()->with('business')->withCount('products')->orderBy('name')->paginate(20);

        return view('super-admin.categories.index', compact('categories'));
    })->name('index');

    Route::get('/create', function () {
        return view('super-admin.categories.create');
    })->name('create');

    Route::get('/{category}/edit', function ($category) {
        return view('super-admin.categories.edit', ['category' => Category::query()->with('business')->withCount('products')->findOrFail($category)]);
    })->name('edit');
});

Route::get('/super-admin/stores', function (Request $request) {
    $businesses = Business::query()
        ->withCount('products')
        ->when($request->filled('search'), function (Builder $query) use ($request): void {
            $search = $request->string('search')->toString();
            $query->where(function (Builder $businessQuery) use ($search): void {
                $businessQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('business_type', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        })
        ->orderBy('name')
        ->paginate(20);

    return view('super-admin.stores', compact('businesses'));
})->name('super-admin.stores');

Route::get('/super-admin/store-admins', function () {
    $admins = User::query()
        ->whereHas('roles', fn (Builder $query) => $query->where('roles.name', 'owner'))
        ->with(['roles', 'businesses'])
        ->orderBy('name')
        ->paginate(20);
    $businesses = Business::query()->orderBy('name')->get();

    return view('super-admin.store-admins', compact('admins', 'businesses'));
})->name('super-admin.store-admins');

Route::get('/super-admin/activity', function (Request $request) {
    $types = [
        'business_application' => 'Pengajuan toko',
        'business' => 'Bisnis',
        'account' => 'Akun',
        'category' => 'Kategori',
    ];
    $selectedType = $request->string('type')->toString();
    $selectedType = array_key_exists($selectedType, $types) ? $selectedType : '';
    $query = DB::table('platform_activities')->latest('created_at');

    if ($selectedType !== '') {
        $query->where('event', 'like', $selectedType.'.%');
    }

    $activities = $query->paginate(20)->withQueryString();

    return view('super-admin.activity', compact('activities', 'types', 'selectedType'));
})->name('super-admin.activity');

Route::get('/super-admin/settings', function () {
    return view('super-admin.settings');
})->name('super-admin.settings');

Route::get('/super-admin/business-applications', function () {
    return view('super-admin.business-applications');
})->name('super-admin.business-applications');

Route::get('/super-admin/stores/create', function () {
    return redirect()->route('super-admin.stores');
})->name('super-admin.stores.create');

Route::get('/super-admin/stores/{store}', function ($store) {
    $store = Business::query()->with(['users.roles'])->withCount('products')->findOrFail($store);

    return view('super-admin.stores.show', compact('store'));
})->name('super-admin.stores.show');

Route::get('/super-admin/stores/{store}/edit', function ($store) {
    $store = Business::query()->findOrFail($store);
    $owners = User::query()
        ->whereHas('roles', fn (Builder $query) => $query->where('roles.name', 'owner')->where('user_roles.business_id', $store->id))
        ->orderBy('name')
        ->get();

    return view('super-admin.stores.edit', compact('store', 'owners'));
})->name('super-admin.stores.edit');

Route::get('/super-admin/store-admins/create', function () {
    return redirect()->route('super-admin.business-applications');
})->name('super-admin.store-admins.create');

Route::get('/super-admin/store-admins/{admin}', function ($admin) {
    $admin = User::query()->with(['roles', 'businesses'])->findOrFail($admin);

    return view('super-admin.store-admins.show', compact('admin'));
})->name('super-admin.store-admins.show');

Route::get('/super-admin/store-admins/{admin}/edit', function ($admin) {
    $admin = User::query()->with(['roles', 'businesses'])->findOrFail($admin);
    $businesses = Business::query()->orderBy('name')->get();

    return view('super-admin.store-admins.edit', compact('admin', 'businesses'));
})->name('super-admin.store-admins.edit');

Route::get('/cashier', function () {
    return view('cashier.dashboard');
})->name('cashier.dashboard');

Route::get('/cashier/settings', function () {
    return view('cashier.settings');
})->name('cashier.settings');

Route::get('/cashier/pos', function () {
    return view('cashier.pos');
})->name('cashier.pos');

Route::get('/cashier/products', function () {
    return view('cashier.products');
})->name('cashier.products');

Route::get('/cashier/promos', function () {
    return view('cashier.promos');
})->name('cashier.promos');

Route::get('/cashier/customers', function () {
    return redirect()->route('cashier.pos');
})->name('cashier.customers');

Route::get('/cashier/sales', function () {
    return view('cashier.sales');
})->name('cashier.sales');

Route::prefix('member')->name('member.')->group(function (): void {
    Route::get('/', fn () => view('member.dashboard'))->name('dashboard');
    Route::get('/sales', fn () => view('member.sales'))->name('sales');
    Route::get('/coupons', fn () => view('member.coupons'))->name('coupons');
    Route::get('/wallet', fn () => view('member.wallet'))->name('wallet');
    Route::get('/notifications', fn () => view('member.notifications'))->name('notifications');
    Route::get('/business-application', fn () => view('member.business-application'))->name('business-application');
});
