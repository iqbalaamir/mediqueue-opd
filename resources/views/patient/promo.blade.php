@extends('layouts.guest')

@section('title', 'Live Demo — Share')
@section('footer-variant', 'compact')

@push('head')
    <meta name="description" content="{{ config('hospital.name') }} — Book OPD appointments, get queue tokens with QR codes, and track live position. Live demo for hospitals across India.">
    <meta property="og:title" content="{{ config('hospital.name') }} — Smart Hospital Queue Management">
    <meta property="og:description" content="Live demo: online OPD booking, token + QR, queue tracking. Try it now — 76+ cities, 150+ hospitals seeded.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $shareUrl }}">
    <meta name="twitter:card" content="summary_large_image">
@endpush

@section('content')
    <section class="overflow-hidden rounded-2xl border border-brand-100 bg-gradient-to-br from-white via-surface to-brand-50 p-6 sm:p-8">
        <span class="badge-brand">Live demo · Ready to share</span>
        <h1 class="mt-3 font-display text-2xl font-bold text-brand-950 sm:text-3xl">
            {{ config('hospital.name') }}
        </h1>
        <p class="mt-2 text-sm text-slate-600 sm:text-base">{{ config('hospital.tagline') }}</p>

        <div class="mt-6 grid gap-3 sm:grid-cols-2">
            <div class="card p-4 text-sm">
                <h2 class="font-semibold text-brand-900">For patients</h2>
                <ul class="mt-2 space-y-1 text-slate-600">
                    <li>Book across 76+ cities</li>
                    <li>Auto fee (first / follow-up)</li>
                    <li>Token + QR + queue ETA</li>
                </ul>
                <a href="{{ route('book.index', absolute: false) }}" class="btn-primary mt-3 inline-flex w-full justify-center text-sm">Try booking demo</a>
            </div>
            <div class="card p-4 text-sm">
                <h2 class="font-semibold text-brand-900">For hospitals</h2>
                <ul class="mt-2 space-y-1 text-slate-600">
                    <li>Queue desk &amp; slot management</li>
                    <li>Offline / online payments</li>
                    <li>Multi-hospital SaaS ready</li>
                </ul>
                <a href="{{ route('admin.login', absolute: false) }}" class="btn-secondary mt-3 inline-flex w-full justify-center text-sm">Admin demo login</a>
                <p class="mt-2 text-center text-xs text-slate-500">admin@mediqueue.local / password</p>
            </div>
        </div>
    </section>

    <section class="mt-6 card p-5 sm:p-6">
        <h2 class="font-display text-lg font-semibold text-brand-900">Share on WhatsApp</h2>
        <p class="mt-1 text-sm text-slate-600">
            Personal message from <strong>{{ $contactName }}</strong> ({{ $contactEmail }}) — no links, reply by WhatsApp or email.
            @if ($recipientName)
                Greeting: <strong>Hi {{ $recipientName }}</strong>
            @else
                Add <code class="rounded bg-brand-50 px-1">?to=TheirName</code> to personalize.
            @endif
        </p>

        <a
            href="{{ $whatsappShareUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#25D366] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#1fb855] sm:w-auto"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.881 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Share demo via WhatsApp
        </a>

        <div class="mt-6">
            <label class="label" for="share-message">Copy-paste message</label>
            <textarea id="share-message" readonly rows="10" class="input font-mono text-xs leading-relaxed">{{ $shareText }}</textarea>
            <button type="button" id="copy-share-btn" class="btn-secondary mt-2 text-sm">Copy message</button>
        </div>

        <div class="mt-6 border-t border-brand-100 pt-4">
            <p class="text-xs font-medium text-slate-700">Demo links (for you — not included in WhatsApp message)</p>
            <ul class="mt-2 space-y-1 break-all text-xs text-brand-700">
                <li><strong>Promo:</strong> {{ $shareUrl }}</li>
                <li><strong>Book:</strong> {{ $bookUrl }}</li>
                <li><strong>Admin:</strong> {{ $adminUrl }}</li>
            </ul>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.getElementById('copy-share-btn')?.addEventListener('click', async () => {
        const textarea = document.getElementById('share-message');
        const btn = document.getElementById('copy-share-btn');
        if (!textarea || !btn) return;

        try {
            await navigator.clipboard.writeText(textarea.value);
            const original = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(() => { btn.textContent = original; }, 2000);
        } catch {
            textarea.select();
            btn.textContent = 'Press Ctrl+C to copy';
        }
    });
</script>
@endpush
