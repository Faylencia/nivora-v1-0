@props([
    'type' => 'primary',
    'size' => 'md',
    'href' => null,
    'variant' => 'solid',
])

@php
    $baseClasses = 'inline-flex min-h-[44px] items-center justify-center gap-2 rounded-2xl font-medium transition-all duration-200 ease-out focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60';

    $sizeClasses = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-5 py-3 text-base',
    ][$size] ?? 'px-4 py-2.5 text-sm';

    $typeStyles = [
        'primary' => 'bg-[#687EFF] text-white shadow-[0_10px_22px_rgba(104,126,255,0.22)] hover:-translate-y-0.5 hover:bg-[#5d70f2] focus:ring-[#687EFF]/20',
        'secondary' => 'border border-[#dfe7ff] bg-white text-[#1f2544] shadow-sm hover:-translate-y-0.5 hover:bg-[#f7f9ff] focus:ring-[#dfe7ff]',
        'accent' => 'bg-[#B6FFFA] text-[#1f2544] shadow-sm hover:-translate-y-0.5 hover:bg-[#a5f6f4] focus:ring-[#B6FFFA]/40',
        'dark' => 'bg-[#1F2544] text-white shadow-sm hover:-translate-y-0.5 hover:bg-[#191f3c] focus:ring-[#1F2544]/20',
        'ghost' => 'border border-transparent bg-transparent text-[#5e647f] hover:-translate-y-0.5 hover:bg-[#eef3ff] hover:text-[#1f2544] focus:ring-[#dfe7ff]',
        'danger' => 'bg-rose-500 text-white shadow-sm hover:-translate-y-0.5 hover:bg-rose-600 focus:ring-rose-200',
    ];

    $classes = $baseClasses . ' ' . $sizeClasses . ' ' . ($typeStyles[$type] ?? $typeStyles['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}>
        {{ $slot }}
    </button>
@endif
