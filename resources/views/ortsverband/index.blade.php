@extends('layouts.app')

@section('title', 'Meine Ortsverbände')

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
    .dash-container > .space-y-4 > *:nth-child(5) { animation-delay: 0.19s; }
    .dash-container > .space-y-4 > *:nth-child(6) { animation-delay: 0.23s; }
    .dash-container > .space-y-4 > *:nth-child(7) { animation-delay: 0.27s; }

    @media (prefers-reduced-motion: reduce) {
        .dash-container > .space-y-4 > * { animation: none; }
    }

    .ov-alert {
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.625rem;
        margin-bottom: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="dash-container">

    {{-- ── Header ── --}}
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Ortsverband</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Mein Ortsverband</h1>
        <p class="text-sm" style="color: var(--text-muted);">Tritt einem Ortsverband bei oder erstelle deinen eigenen</p>
    </div>

    @if(session('success'))
    <div class="ov-alert glass-success">
        <i class="bi bi-check-circle" style="font-size:1rem;flex-shrink:0;"></i>
        <span style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);flex:1;">{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:1.125rem;line-height:1;">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="ov-alert glass-error">
        <i class="bi bi-exclamation-triangle" style="font-size:1rem;flex-shrink:0;"></i>
        <span style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);flex:1;">{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:1.125rem;line-height:1;">&times;</button>
    </div>
    @endif

    <div class="space-y-4">

        {{-- ── Beitreten: Smart Action Card ── --}}
        <div style="display:block;background:linear-gradient(135deg,#00337F,#0055cc);border-radius:0.75rem;padding:1.25rem;position:relative;overflow:hidden;color:#fff;">
            <div style="font-size:0.625rem;text-transform:uppercase;letter-spacing:0.1em;color:rgba(255,255,255,0.7);margin-bottom:0.375rem;">Einladung</div>
            <div style="font-size:1.125rem;font-weight:700;margin-bottom:0.25rem;">Ortsverband beitreten</div>
            <div style="font-size:0.8125rem;color:rgba(255,255,255,0.6);margin-bottom:0.75rem;">Dein Ausbildungsbeauftragter hat einen Einladungscode für dich? Gib ihn hier ein.</div>
            <form action="{{ route('ortsverband.join.code') }}" method="POST">
                @csrf
                <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                    <input type="text"
                           name="code"
                           placeholder="Einladungscode (z.B. THW-XXXXXXXX)"
                           required
                           style="flex:1;min-width:180px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.2);border-radius:0.5rem;padding:0.5rem 0.75rem;font-size:0.8125rem;color:#fff;outline:none;font-family:inherit;"
                           class="placeholder-white-50">
                    <button type="submit" style="display:inline-flex;align-items:center;gap:0.5rem;background:rgba(255,255,255,0.2);color:#fff;border-radius:0.5rem;padding:0.5rem 0.875rem;font-size:0.75rem;font-weight:600;border:none;cursor:pointer;">
                        Beitreten
                    </button>
                </div>
            </form>
        </div>

        {{-- ── Erstellen Card ── --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Ausbilder</span>
            </div>
            <div style="font-size:1rem;font-weight:700;color:var(--text-primary);margin-bottom:0.375rem;">Ortsverband erstellen</div>
            <p style="font-size:0.8125rem;color:var(--text-muted);margin-bottom:1rem;line-height:1.5;">
                Erstelle deinen eigenen Ortsverband als Ausbildungsbeauftragter und lade Mitglieder ein.
            </p>
            <a href="{{ route('ortsverband.create') }}" class="btn-secondary btn-sm" style="text-decoration:none;">
                Erstellen
            </a>
        </div>

        {{-- ── Info Card ── --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Info</span>
            </div>
            <div style="font-size:0.875rem;font-weight:600;color:var(--text-primary);margin-bottom:0.375rem;">Was ist ein Ortsverband?</div>
            <p style="font-size:0.8125rem;color:var(--text-muted);margin:0;line-height:1.5;">
                Ortsverbände ermöglichen Ausbildern, den Lernfortschritt ihrer Mitglieder zu verfolgen und eigene Lernpools zu erstellen.
            </p>
        </div>

        {{-- ── Zurück ── --}}
        <div style="text-align:center;padding-top:0.5rem;">
            <a href="{{ route('dashboard') }}" class="btn-ghost btn-sm" style="text-decoration:none;">Zurück zum Dashboard</a>
        </div>

    </div>
</div>
@endsection
