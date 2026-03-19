@extends('layouts.app')

@section('title', $ortsverband->name . ' bearbeiten')

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
.dash-container > .space-y-4 > *:nth-child(4) { animation-delay: 0.15s; }
@media (prefers-reduced-motion: reduce) {
    .dash-container > .space-y-4 > * { animation: none; }
}
</style>
@endpush

@section('content')
<div class="dash-container">

    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Einstellungen</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Ortsverband bearbeiten</h1>
        <p class="text-sm" style="color: var(--text-muted);">{{ $ortsverband->name }}</p>
    </div>

    @if($errors->any())
    <div class="glass p-4" style="border-radius:0.75rem;margin-bottom:1rem;display:flex;align-items:start;gap:0.75rem;border-left:3px solid #ef4444;">
        <i class="bi bi-exclamation-triangle" style="color:#ef4444;font-size:1.1rem;flex-shrink:0;margin-top:0.1rem;"></i>
        <div style="flex:1;">
            <span style="font-size:0.875rem;font-weight:600;color:var(--text-primary);">Fehler bei der Eingabe</span>
            <ul style="margin: 0.25rem 0 0 1rem; padding: 0; font-size: 0.8rem; color: var(--text-secondary);">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="space-y-4" style="max-width: 600px;">

        {{-- Edit Form --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">DETAILS</span>

            <form action="{{ route('ortsverband.update', $ortsverband) }}" method="POST" style="margin-top: 1rem;">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 1.5rem;">
                    <label for="name" style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; display: block; font-size: 0.875rem;">
                        Name des Ortsverbands <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           class="input-glass"
                           value="{{ old('name', $ortsverband->name) }}"
                           placeholder="z.B. OV Musterstadt"
                           required>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Der offizielle Name deines Ortsverbands</p>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="description" style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; display: block; font-size: 0.875rem;">
                        Beschreibung <span style="color: var(--text-muted); font-weight: normal;">(optional)</span>
                    </label>
                    <textarea id="description"
                              name="description"
                              class="textarea-glass"
                              rows="4"
                              placeholder="Kurze Beschreibung des Ortsverbands...">{{ old('description', $ortsverband->description) }}</textarea>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Eine optionale Beschreibung für deine Mitglieder</p>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">
                    Änderungen speichern
                </button>
            </form>
        </div>

        {{-- Danger Zone --}}
        <div class="glass p-4" style="border-radius:0.75rem;border-left:3px solid #ef4444;">
            <span class="text-xs uppercase tracking-wider" style="color:#ef4444;font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">GEFAHRENZONE</span>

            <div style="margin-top: 0.75rem;">
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1rem;">
                    Wenn du den Ortsverband löschst, werden alle Mitgliedschaften und Einladungen entfernt. Diese Aktion kann nicht rückgängig gemacht werden.
                </p>
                <form action="{{ route('ortsverband.destroy', $ortsverband) }}"
                      method="POST"
                      onsubmit="return confirm('Bist du sicher? Alle Mitgliedschaften und Einladungen werden gelöscht!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger btn-sm">
                        Ortsverband löschen
                    </button>
                </form>
            </div>
        </div>

        {{-- Back Link --}}
        <div style="text-align: center; padding-top: 0.5rem;">
            <a href="{{ route('ortsverband.dashboard', $ortsverband) }}" class="btn-ghost btn-sm">
                Zurück zum Dashboard
            </a>
        </div>

    </div>
</div>
@endsection
