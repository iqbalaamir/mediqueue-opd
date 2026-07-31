@props([
    'variant' => 'guest',
])

<header {{ $attributes->merge(['class' => 'sticky top-0 z-40 border-b border-brand-100/80 bg-white/90 backdrop-blur']) }}>
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6 {{ $variant === 'admin' ? 'max-w-none' : '' }}">
        <div class="min-w-0">
            {{ $brand }}
        </div>
        <div class="flex items-center gap-2">
            {{ $actions ?? '' }}
        </div>
    </div>
</header>
