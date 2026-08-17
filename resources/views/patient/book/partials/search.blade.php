<form method="GET" action="{{ $action }}" class="mb-6">
    <label for="search-q" class="sr-only">Search</label>
    <div class="flex gap-2">
        <input
            type="search"
            id="search-q"
            name="q"
            value="{{ request('q') }}"
            placeholder="{{ $placeholder ?? 'Search...' }}"
            class="input flex-1"
        >
        <button type="submit" class="btn-secondary shrink-0">Search</button>
        @if (request('q'))
            <a href="{{ $action }}" class="btn-ghost shrink-0">Clear</a>
        @endif
    </div>
</form>
