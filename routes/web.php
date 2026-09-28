<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('public.home');
})->name('home');

Route::get('/explore', function () {
    return view('public.explore');
})->name('explore');

Route::get('/business/{business}', function ($business) {
    return view('public.business.show', ['business' => $business]);
})->name('business.show');

Route::get('/product/{product}', function ($product) {
    return view('public.product.show', ['product' => $product]);
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

    Route::get('/business', function () {
        return view('owner.business.index');
    })->name('business.index');

    Route::get('/outlets', function () {
        return view('owner.outlets.index');
    })->name('outlets.index');

    Route::get('/outlets/{outlet}/edit', function ($outlet) {
        return view('owner.outlets.edit', ['outlet' => $outlet]);
    })->name('outlets.edit');

    Route::get('/products/create', function () {
        return view('owner.products.create');
    })->name('products.create');

    Route::get('/products/{product}/edit', function ($product) {
        return view('owner.products.edit', ['product' => $product]);
    })->name('products.edit');

    Route::get('/products/{product}', function ($product) {
        return view('owner.products.show', ['product' => $product]);
    })->name('products.show');

    Route::get('/products', function () {
        return view('owner.products.index');
    })->name('products.index');

    Route::get('/inventory', function () {
        return view('owner.inventory.index');
    })->name('inventory.index');

    Route::get('/customers', function () {
        return view('owner.customers.index');
    })->name('customers.index');

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
});

Route::get('/super-admin', function () {
    return view('super-admin.dashboard');
})->name('super-admin.dashboard');

Route::prefix('/super-admin/categories')->name('super-admin.categories.')->group(function () {
    Route::get('/', function () {
        return view('super-admin.categories.index');
    })->name('index');

    Route::get('/create', function () {
        return view('super-admin.categories.create');
    })->name('create');

    Route::get('/{category}/edit', function ($category) {
        return view('super-admin.categories.edit', ['category' => $category]);
    })->name('edit');
});

Route::get('/super-admin/stores', function () {
    return view('super-admin.stores');
})->name('super-admin.stores');

Route::get('/super-admin/store-admins', function () {
    return view('super-admin.store-admins');
})->name('super-admin.store-admins');

Route::get('/super-admin/activity', function () {
    return view('super-admin.activity');
})->name('super-admin.activity');

Route::get('/super-admin/settings', function () {
    return view('super-admin.settings');
})->name('super-admin.settings');

Route::get('/super-admin/stores/create', function () {
    return view('super-admin.stores.create');
})->name('super-admin.stores.create');

Route::get('/super-admin/stores/{store}', function ($store) {
    return view('super-admin.stores.show', ['store' => $store]);
})->name('super-admin.stores.show');

Route::get('/super-admin/stores/{store}/edit', function ($store) {
    return view('super-admin.stores.edit', ['store' => $store]);
})->name('super-admin.stores.edit');

Route::get('/super-admin/store-admins/create', function () {
    return view('super-admin.store-admins.create');
})->name('super-admin.store-admins.create');

Route::get('/super-admin/store-admins/{admin}', function ($admin) {
    return view('super-admin.store-admins.show', ['admin' => $admin]);
})->name('super-admin.store-admins.show');

Route::get('/super-admin/store-admins/{admin}/edit', function ($admin) {
    return view('super-admin.store-admins.edit', ['admin' => $admin]);
})->name('super-admin.store-admins.edit');

Route::get('/cashier', function () {
    return view('cashier.dashboard');
})->name('cashier.dashboard');

Route::get('/cashier/pos', function () {
    return view('cashier.pos');
})->name('cashier.pos');

Route::get('/cashier/products', function () {
    return view('cashier.products');
})->name('cashier.products');

Route::get('/cashier/customers', function () {
    return view('cashier.customers');
})->name('cashier.customers');

Route::get('/cashier/sales', function () {
    return view('cashier.sales');
})->name('cashier.sales');
