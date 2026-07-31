@props([
    'id',
    'title' => '',
    'size' => 'md',
])

@php
    $sizeClasses = match ($size) {
        'sm' => 'max-w-md',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
        default => 'max-w-lg',
    };
@endphp

<div
    id="{{ $id }}"
    class="fixed inset-0 z-50 hidden items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $id }}-title"
    data-modal
>
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" data-modal-backdrop data-modal-close></div>
    <div class="relative w-full {{ $sizeClasses }} rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h2 id="{{ $id }}-title" class="font-display text-lg font-semibold text-brand-900">{{ $title }}</h2>
            <button type="button" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600" data-modal-close aria-label="Close">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="px-5 py-4">
            {{ $slot }}
        </div>
        @isset($footer)
            <div class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
