@extends('layouts.app')

@section('title', 'Ortsverband erstellen')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
@keyframes dash-rise {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
.dash-container > .space-y-4 > * {
    animation: dash-rise 0.45s cubic-bezier(0.22,1,0.36,1) both;
}
.dash-container > .space-y-4 > *:nth-child(1) { animation-delay: 0.03s; }
.dash-container > .space-y-4 > *:nth-child(2) { animation-delay: 0.07s; }
.dash-container > .space-y-4 > *:nth-child(3) { animation-delay: 0.11s; }
@media (prefers-reduced-motion: reduce) {
    .dash-container > .space-y-4 > * { animation: none; }
}
</style>
@endpush

@section('content')
<div class="dash-container">

    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Ortsverband</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Neuen Ortsverband erstellen</h1>
        <p class="text-sm" style="color: var(--text-muted);">Als Ausbildungsbeauftragter kannst du Mitglieder einladen</p>
    </div>

    @if ($errors->any())
    <div class="glass p-4" style="border-radius:0.75rem;margin-bottom:1rem;display:flex;align-items:start;gap:0.75rem;border-left:3px solid #ef4444;">
        <i class="bi bi-exclamation-triangle" style="color:#ef4444;font-size:1.1rem;flex-shrink:0;margin-top:0.1rem;"></i>
        <div style="flex:1;">
            <span style="font-size:0.875rem;font-weight:600;color:var(--text-primary);">Fehler bei der Eingabe</span>
            <ul style="margin: 0.25rem 0 0 1rem; padding: 0; font-size: 0.8rem; color: var(--text-secondary);">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="space-y-4" style="max-width: 600px;">

        <div class="glass p-4" style="border-radius:0.75rem;">
            <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">DETAILS</span>

            <form action="{{ route('ortsverband.store') }}" method="POST" style="margin-top: 1rem;">
                @csrf

                <div style="margin-bottom: 1.5rem;">
                    <label for="name" style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; display: block; font-size: 0.875rem;">
                        Name des Ortsverbands <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           class="input-glass"
                           value="{{ old('name') }}"
                           placeholder="z.B. THW München"
                           required>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="description" style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; display: block; font-size: 0.875rem;">
                        Beschreibung <span style="color: var(--text-muted); font-weight: normal;">(optional)</span>
                    </label>
                    <textarea id="description"
                              name="description"
                              class="textarea-glass"
                              rows="4"
                              placeholder="Eine kurze Beschreibung des Ortsverbands...">{{ old('description') }}</textarea>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <button type="submit" class="btn-primary" style="width: 100%;">
                        Ortsverband erstellen
                    </button>
                    <a href="{{ route('ortsverband.index') }}" class="btn-ghost" style="width: 100%; text-align: center;">
                        Abbrechen
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
