@props(['name' => ''])

@php
    $categoryName = mb_strtolower($name);
    $categoryType = match (true) {
        str_contains($categoryName, 'minum'), str_contains($categoryName, 'drink'), str_contains($categoryName, 'kopi') => 'drink',
        str_contains($categoryName, 'makan'), str_contains($categoryName, 'food'), str_contains($categoryName, 'snack'), str_contains($categoryName, 'roti') => 'food',
        str_contains($categoryName, 'buah'), str_contains($categoryName, 'sayur'), str_contains($categoryName, 'fresh') => 'produce',
        str_contains($categoryName, 'pakaian'), str_contains($categoryName, 'fashion'), str_contains($categoryName, 'busana') => 'fashion',
        str_contains($categoryName, 'kecantikan'), str_contains($categoryName, 'beauty'), str_contains($categoryName, 'kosmetik') => 'beauty',
        str_contains($categoryName, 'elektronik'), str_contains($categoryName, 'electronic') => 'electronic',
        str_contains($categoryName, 'rumah'), str_contains($categoryName, 'peralatan') => 'home',
        default => 'general',
    };

    $icon = match ($categoryType) {
        'drink' => 'coffee',
        'food' => 'bowl-hot',
        'produce' => 'leaf',
        'fashion' => 'closet',
        'beauty' => 'brush',
        'electronic' => 'mobile-alt',
        'home' => 'home-alt-2',
        default => 'package',
    };
@endphp

<span {{ $attributes->class(['public-category-art-icon', 'is-' . $categoryType]) }} aria-hidden="true">
    <x-public-icon :name="$icon" />
</span>
