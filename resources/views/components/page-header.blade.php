@props([
    'eyebrow',
    'title',
    'description',
])

<header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        <p class="ni-section-label">{{ $eyebrow }}</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tighter text-[#1f2544]">{{ $title }}</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-[#5e647f]">{{ $description }}</p>
    </div>
    @if ($slot->isNotEmpty())
        <div class="flex shrink-0 flex-wrap items-center gap-3">
            {{ $slot }}
        </div>
    @endif
</header>
