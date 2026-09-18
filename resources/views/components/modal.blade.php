@props([
    'id' => 'modal',
    'title' => 'Modal',
])

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#1f2544]/45 px-4">
    <div class="w-full max-w-lg rounded-[30px] border border-[#e7ebff] bg-white p-6 shadow-[0_28px_80px_rgba(31,37,68,0.12)]">
        <div class="mb-5 flex items-start justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-[#1f2544]">{{ $title }}</h3>
            </div>
            <button type="button" class="rounded-xl p-2 text-[#5e647f] transition hover:bg-[#eef3ff] hover:text-[#1f2544]" data-close-modal="{{ $id }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div>{{ $slot }}</div>
    </div>
</div>
