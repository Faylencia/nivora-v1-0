@extends('layouts.cashier')

@section('title', 'Cashier POS - Nivora')

@section('content')
    @php
        $products = [
            ['name' => 'Kopi Susu Signature', 'category' => 'Minuman', 'price' => 18000, 'stock' => 24, 'image' => 'https://images.unsplash.com/photo-1497636577773-f1231844b336?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Boba Hazelnut', 'category' => 'Minuman', 'price' => 22000, 'stock' => 12, 'image' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Croissant Matcha', 'category' => 'Cemilan', 'price' => 26000, 'stock' => 8, 'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Tas Jinjing', 'category' => 'Fashion', 'price' => 125000, 'stock' => 5, 'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=900&q=80'],
        ];

        $items = [
            ['name' => 'Kopi Susu Signature', 'price' => 18000, 'qty' => 2, 'subtotal' => 36000],
            ['name' => 'Croissant Matcha', 'price' => 26000, 'qty' => 1, 'subtotal' => 26000],
        ];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#687EFF]">POS</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-[-0.05em] text-[#1f2544]">Cashier POS</h1>
            </div>
            <div class="rounded-2xl border border-[#e7ebff] bg-white px-3 py-2 text-sm text-[#5e647f] shadow-sm">
                Outlet: Bandung Center
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.4fr_0.6fr]">
            <div class="space-y-5">
                <div class="rounded-[28px] border border-[#e7ebff] bg-white p-4 shadow-sm">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="w-full max-w-md rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] px-3 py-2.5">
                            <input type="text" placeholder="Cari produk" class="w-full bg-transparent text-sm text-[#1f2544] outline-none placeholder:text-[#6b7280]" />
                        </div>
                        <select class="w-full rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] px-3 py-2.5 text-sm text-[#1f2544] outline-none sm:max-w-[200px]">
                            <option>Semua kategori</option>
                            <option>Minuman</option>
                            <option>Cemilan</option>
                            <option>Fashion</option>
                        </select>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-3">
                        @foreach ($products as $product)
                            <button type="button" class="overflow-hidden rounded-[24px] border border-[#e7ebff] bg-white text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="h-28 w-full object-cover" />
                                <div class="p-3">
                                    <p class="text-sm font-semibold text-[#1f2544]">{{ $product['name'] }}</p>
                                    <p class="mt-1 text-xs text-[#687EFF]">{{ $product['category'] }}</p>
                                    <div class="mt-2 flex items-center justify-between gap-3">
                                        <span class="text-sm font-semibold text-[#1f2544]">Rp {{ number_format($product['price'], 0, ',', '.') }}</span>
                                        <span class="text-[11px] text-[#5e647f]">Stok {{ $product['stock'] }}</span>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="rounded-[30px] border border-[#e7ebff] bg-white p-4 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-[#1f2544]">Daftar Transaksi</h2>
                    <span class="rounded-full bg-[#eef3ff] px-2.5 py-1 text-xs font-medium text-[#687EFF]">3 item</span>
                </div>

                <div class="space-y-3">
                    @foreach ($items as $item)
                        <div class="rounded-2xl border border-[#eef3ff] bg-[#f7f9ff] p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium text-[#1f2544]">{{ $item['name'] }}</p>
                                    <p class="text-xs text-[#5e647f]">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                                <button type="button" class="text-xs font-medium text-rose-500">Hapus</button>
                            </div>
                            <div class="mt-3 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2 rounded-xl border border-[#e7ebff] bg-white px-2 py-1.5">
                                    <button type="button" class="h-7 w-7 rounded-lg bg-[#eef3ff] text-sm text-[#1f2544]">−</button>
                                    <span class="min-w-[24px] text-center text-sm font-medium text-[#1f2544]">{{ $item['qty'] }}</span>
                                    <button type="button" class="h-7 w-7 rounded-lg bg-[#eef3ff] text-sm text-[#1f2544]">+</button>
                                </div>
                                <span class="text-sm font-semibold text-[#1f2544]">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 space-y-2 border-t border-[#eef3ff] pt-4 text-sm text-[#5e647f]">
                    <div class="flex items-center justify-between"><span>Subtotal</span><span class="font-medium text-[#1f2544]">Rp 62.000</span></div>
                    <div class="flex items-center justify-between"><span>Discount</span><span class="font-medium text-[#1f2544]">Rp 0</span></div>
                    <div class="flex items-center justify-between"><span>Tax</span><span class="font-medium text-[#1f2544]">Rp 0</span></div>
                    <div class="flex items-center justify-between pt-2 text-base font-semibold text-[#1f2544]"><span>Total</span><span>Rp 62.000</span></div>
                </div>

                <div class="mt-5 rounded-[20px] border border-[#e7ebff] bg-[#f7f9ff] p-3">
                    <div class="mb-2 flex items-center justify-between text-sm text-[#5e647f]">
                        <span>Pembayaran</span>
                        <span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-medium text-emerald-600">Cash</span>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium uppercase tracking-[0.16em] text-[#687EFF]">Total</label>
                            <div class="rounded-xl border border-[#e7ebff] bg-white px-3 py-2.5 text-sm text-[#1f2544]">Rp 62.000</div>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium uppercase tracking-[0.16em] text-[#687EFF]">Jumlah Dibayar</label>
                            <input type="text" value="Rp 100.000" class="w-full rounded-xl border border-[#e7ebff] bg-white px-3 py-2.5 text-sm text-[#1f2544] outline-none" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium uppercase tracking-[0.16em] text-[#687EFF]">Kembalian</label>
                            <div class="rounded-xl border border-[#e7ebff] bg-white px-3 py-2.5 text-sm text-[#1f2544]">Rp 38.000</div>
                        </div>
                    </div>
                </div>

                <button type="button" class="mt-5 inline-flex min-h-[48px] w-full items-center justify-center rounded-2xl bg-[#687EFF] text-sm font-semibold text-white shadow-sm">Bayar Sekarang</button>
            </div>
        </div>
    </div>
@endsection
