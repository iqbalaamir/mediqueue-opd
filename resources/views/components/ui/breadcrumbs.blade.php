@props([
    'items' => [],
])

@if (count($items))
    <nav {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2 text-sm text-slate-500']) }} aria-label="Breadcrumb">
        @foreach ($items as $index => $item)
            @if ($index > 0)
                <span aria-hidden="true" class="text-slate-300">/</span>
            @endif

            @if (! empty($item['url']) && $index < count($items) - 1)
                <a href="{{ $item['url'] }}" class="hover:text-brand-700">{{ $item['label'] }}</a>
            @else
                <span class="font-medium text-brand-800">{{ $item['label'] }}</span>
            @endif
        @endforeach
    </nav>
@endif
