@props([
    'label' => null,
    'type' => 'text',
    'name' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'id' => null,
])

<div class="space-y-2">
    @if ($label)
        <label for="{{ $id ?? $name }}" class="block text-sm font-medium text-[#1f2544]">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <input
        id="{{ $id ?? $name }}"
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'min-h-[46px] w-full rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] px-3.5 py-2.5 text-sm text-[#1f2544] placeholder:text-[#7c859f] shadow-sm transition duration-200 ease-out focus:border-[#687EFF] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#dfe7ff]']) }}
    >
</div>
