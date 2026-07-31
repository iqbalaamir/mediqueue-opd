@props([
    'current' => 1,
    'steps' => [],
])

<nav aria-label="Booking progress" class="mb-8">
    <ol class="flex flex-wrap items-center gap-2 sm:gap-4">
        @foreach ($steps as $index => $step)
            @php $stepNumber = $index + 1; @endphp
            <li class="flex items-center gap-2">
                <span @class([
                    'flex h-8 w-8 items-center justify-center rounded-full text-xs font-semibold',
                    'bg-brand-700 text-white' => $stepNumber <= $current,
                    'bg-brand-100 text-brand-600' => $stepNumber > $current,
                ])>{{ $stepNumber }}</span>
                <span @class([
                    'hidden text-sm sm:inline',
                    'font-medium text-brand-900' => $stepNumber <= $current,
                    'text-slate-500' => $stepNumber > $current,
                ])>{{ $step }}</span>
                @if ($index < count($steps) - 1)
                    <span class="hidden h-px w-6 bg-brand-200 sm:block" aria-hidden="true"></span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
