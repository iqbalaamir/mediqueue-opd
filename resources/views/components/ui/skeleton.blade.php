@props([
    'lines' => 3,
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'animate-pulse ' . $class]) }} aria-hidden="true">
    @for ($i = 0; $i < $lines; $i++)
        <div @class([
            'rounded-lg bg-brand-100/70',
            'mb-3 h-4 w-full' => $i < $lines - 1,
            'h-4 w-2/3' => $i === $lines - 1,
        ])></div>
    @endfor
</div>
