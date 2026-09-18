@props([
    'title' => null,
    'subtitle' => null,
    'action' => null,
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'rounded-[26px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_16px_38px_rgba(31,37,68,0.05)] transition hover:-translate-y-0.5 hover:shadow-[0_20px_44px_rgba(31,37,68,0.07)] ' . $class]) }}>
    @if ($title || $action || $subtitle)
        <div class="mb-4 flex items-start justify-between gap-3">
            <div>
                @if ($title)
                    <h3 class="text-base font-semibold text-[#1f2544]">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="mt-1 text-sm text-[#5e647f]">{{ $subtitle }}</p>
                @endif
            </div>

            @if ($action)
                <div>{{ $action }}</div>
            @endif
        </div>
    @endif

    {{ $slot }}
</div>
