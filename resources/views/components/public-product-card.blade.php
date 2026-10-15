@props(['product'])

@php
    $productImage = $product->image
        ? (\Illuminate\Support\Str::startsWith($product->image, ['http://', 'https://', '/'])
            ? $product->image
            : (config('filesystems.disks.public.driver') === 'local'
                ? '/storage/'.ltrim($product->image, '/')
                : \Illuminate\Support\Facades\Storage::disk('public')->url($product->image)))
        : null;
@endphp

<article class="public-product-card public-product-animate group overflow-hidden rounded-[22px] border border-[#e7ebff] bg-white shadow-[0_12px_32px_rgba(31,37,68,.045)] transition duration-300 hover:-translate-y-1 hover:border-[#c5d0ff] hover:shadow-[0_20px_42px_rgba(31,37,68,.1)]">
    <a href="{{ route('product.show', ['product' => $product->id]) }}" class="block h-full" aria-label="Lihat {{ $product->name }}">
        <div class="public-product-photo-frame relative aspect-[4/3] overflow-hidden bg-[#f0f3ff]">
            @if ($productImage)
                <img src="{{ $productImage }}" alt="Foto {{ $product->name }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden')">
                <div class="public-product-placeholder hidden h-full w-full items-center justify-center text-[#687EFF]"><svg viewBox="0 0 24 24" class="h-12 w-12" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5"/></svg></div>
            @else
                <div class="public-product-placeholder flex h-full w-full items-center justify-center text-[#687EFF]"><svg viewBox="0 0 24 24" class="h-12 w-12" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5"/></svg></div>
            @endif
            <span class="absolute left-3 top-3 max-w-[calc(100%-1.5rem)] truncate rounded-full border border-white/70 bg-white/90 px-2.5 py-1 text-[10px] font-semibold text-[#536dfe] shadow-sm">{{ $product->category?->name ?? 'Produk lokal' }}</span>
        </div>
        <div class="public-product-card-body p-4">
            <h3 class="truncate text-sm font-semibold text-[#20284a] transition group-hover:text-[#536dfe]">{{ $product->name }}</h3>
            <p class="mt-1 truncate text-xs text-[#7b8299]">{{ $product->business?->name ?? 'Toko lokal' }}</p>
            <div class="mt-3 flex items-center justify-between gap-2">
                <strong class="truncate text-sm font-bold text-[#25345d]">Rp{{ number_format((float) $product->selling_price, 0, ',', '.') }}</strong>
                <span class="public-product-card-action">Lihat detail <span aria-hidden="true">↗</span></span>
            </div>
        </div>
    </a>
</article>
