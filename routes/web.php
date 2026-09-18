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

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

Route::get('/dashboard/business', function () {
    return view('dashboard.business.index');
})->name('business.index');

Route::get('/dashboard/outlets', function () {
    return view('dashboard.outlets.index');
})->name('outlets.index');

Route::get('/dashboard/outlets/create', function () {
    return view('dashboard.outlets.create');
})->name('outlets.create');

Route::get('/dashboard/outlets/{outlet}/edit', function ($outlet) {
    return view('dashboard.outlets.edit', ['outlet' => $outlet]);
})->name('outlets.edit');

Route::get('/dashboard/categories/create', function () {
    return view('dashboard.categories.create');
})->name('categories.create');

Route::get('/dashboard/categories/{category}/edit', function ($category) {
    return view('dashboard.categories.edit', ['category' => $category]);
})->name('categories.edit');

Route::get('/dashboard/categories', function () {
    return view('dashboard.categories.index');
})->name('categories.index');

Route::get('/dashboard/products/create', function () {
    return view('dashboard.products.create');
})->name('products.create');

Route::get('/dashboard/products/{product}/edit', function ($product) {
    return view('dashboard.products.edit', ['product' => $product]);
})->name('products.edit');

Route::get('/dashboard/products/{product}', function ($product) {
    return view('dashboard.products.show', ['product' => $product]);
})->name('products.show');

Route::get('/dashboard/products', function () {
    return view('dashboard.products.index');
})->name('products.index');

Route::get('/dashboard/inventory', function () {
    return view('dashboard.inventory.index');
})->name('inventory.index');

Route::get('/dashboard/customers', function () {
    return view('dashboard.customers.index');
})->name('customers.index');

Route::get('/dashboard/sales', function () {
    return view('dashboard.sales.index');
})->name('sales.index');

Route::get('/dashboard/payments', function () {
    return view('dashboard.payments.index');
})->name('payments.index');

Route::get('/dashboard/ai', function () {
    return view('dashboard.ai.index');
})->name('ai.index');

Route::get('/dashboard/ai/store-analysis', function () {
    return view('dashboard.ai.store-analysis');
})->name('ai.store-analysis');

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
