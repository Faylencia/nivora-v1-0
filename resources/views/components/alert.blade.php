@props([
    'type' => 'info',
    'title' => null,
])

@php
    $styles = [
        'info' => 'border-sky-200 bg-sky-50 text-sky-800',
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'danger' => 'border-rose-200 bg-rose-50 text-rose-800',
    ][$type] ?? 'border-sky-200 bg-sky-50 text-sky-800';
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border p-4 ' . $styles]) }}>
    @if ($title)
        <div class="mb-1 text-sm font-semibold">{{ $title }}</div>
    @endif
    <div class="text-sm">{{ $slot }}</div>
</div>
