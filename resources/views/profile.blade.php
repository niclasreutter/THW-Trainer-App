@extends('layouts.app')

@section('title', 'Profil - THW Trainer')
@section('description', 'Bearbeite dein THW-Trainer Profil: Ändere deine persönlichen Daten, Passwort und verwalte deinen Account.')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Layout ─── */
    .pf-layout {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 1.5rem;
        align-items: start;
    }

    @media (max-width: 860px) {
        .pf-layout {
            grid-template-columns: 1fr;
        }
    }

    .pf-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        position: sticky;
        top: 5rem;
    }

    @media (max-width: 860px) {
        .pf-sidebar { position: static; }
    }

    .pf-main {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    /* ─── Hero Card ─── */
    .pf-hero {
        border-radius: 1.25rem;
        padding: 1.75rem 1.5rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .pf-hero-avatar-wrap {
        position: relative;
        display: inline-block;
        margin-bottom: 1rem;
    }

    .pf-hero-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.15);
        object-fit: cover;
        background: rgba(255, 255, 255, 0.05);
        display: block;
        box-shadow: 0 0 0 6px rgba(91, 154, 255, 0.1), 0 8px 32px rgba(0, 51, 127, 0.35);
    }

    html.light-mode .pf-hero-avatar {
        border-color: rgba(0, 51, 127, 0.2);
        box-shadow: 0 0 0 6px rgba(0, 51, 127, 0.07), 0 8px 24px rgba(0, 51, 127, 0.15);
    }

    .pf-hero-regen {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00337F, #0055cc);
        border: 2.5px solid var(--bg-base);
        color: #fff;
        font-size: 0.75rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 200ms, box-shadow 200ms;
        padding: 0;
    }

    .pf-hero-regen:hover {
        transform: scale(1.15);
        box-shadow: 0 4px 14px rgba(0, 51, 127, 0.45);
    }

    .pf-hero-regen.is-spinning i {
        animation: pf-spin 0.6s ease-in-out;
    }

    @keyframes pf-spin {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }

    html.light-mode .pf-hero-regen {
        border-color: var(--bg-elevated);
    }

    .pf-hero-name {
        font-size: 1.375rem;
        font-weight: 800;
        font-family: 'Barlow Condensed', sans-serif;
        color: var(--text-primary);
        line-height: 1.2;
        margin-bottom: 0.2rem;
    }

    .pf-hero-email {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-bottom: 0.75rem;
        word-break: break-all;
    }

    .pf-hero-tags {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pf-tag {
        font-size: 0.5625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 0.2rem 0.5rem;
        border-radius: 2rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    .pf-tag--ok { background: rgba(34, 197, 94, 0.12); color: #22c55e; }
    .pf-tag--warn { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    .pf-tag--info { background: rgba(91, 154, 255, 0.1); color: #5b9aff; }
    html.light-mode .pf-tag--info { background: rgba(0, 51, 127, 0.08); color: #00337F; }

    /* ─── Sidebar Stats ─── */
    .pf-stat-list {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .pf-stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.6rem 0;
    }

    .pf-stat-row + .pf-stat-row {
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    html.light-mode .pf-stat-row + .pf-stat-row {
        border-top-color: rgba(0, 51, 127, 0.07);
    }

    .pf-stat-label {
        font-size: 0.75rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .pf-stat-label i { opacity: 0.7; font-size: 0.8rem; }

    .pf-stat-value {
        font-size: 0.8125rem;
        font-weight: 700;
        color: var(--text-primary);
        text-align: right;
    }

    .pf-stat-sub {
        display: block;
        font-size: 0.625rem;
        color: var(--text-muted);
        font-weight: 400;
    }

    /* ─── Active Effects ─── */
    .pf-effect-chip {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 0.625rem;
        border-radius: 0.625rem;
        background: rgba(168, 85, 247, 0.08);
        border: 1px solid rgba(168, 85, 247, 0.18);
        margin-bottom: 0.4rem;
        font-size: 0.75rem;
    }

    .pf-effect-chip i { color: #a855f7; font-size: 0.875rem; flex-shrink: 0; }
    .pf-effect-chip-label { color: var(--text-primary); font-weight: 600; }
    .pf-effect-chip-sub { color: var(--text-muted); font-size: 0.6875rem; }

    .pf-effect-chip--gold {
        background: rgba(251, 191, 36, 0.08);
        border-color: rgba(251, 191, 36, 0.2);
    }
    .pf-effect-chip--gold i { color: #fbbf24; }

    /* ─── Section Cards ─── */
    .pf-section {
        border-radius: 1rem;
        padding: 1.25rem 1.375rem;
        position: relative;
    }

    .pf-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .pf-section-title {
        font-size: 0.5625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ─── Locked Sections ─── */
    .pf-section--locked {
        position: relative;
        border: 1px dashed rgba(255, 255, 255, 0.08) !important;
    }

    html.light-mode .pf-section--locked {
        border-color: rgba(0, 51, 127, 0.1) !important;
    }

    .pf-locked-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        background: rgba(91, 154, 255, 0.1);
        color: #5b9aff;
        border: 1px solid rgba(91, 154, 255, 0.2);
        border-radius: 2rem;
        font-size: 0.5625rem;
        font-weight: 700;
        font-family: 'IBM Plex Mono', monospace;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.15rem 0.5rem;
    }

    html.light-mode .pf-locked-badge {
        background: rgba(0, 51, 127, 0.06);
        color: #00337F;
        border-color: rgba(0, 51, 127, 0.12);
    }

    .pf-locked-content {
        opacity: 0.38;
        pointer-events: none;
        user-select: none;
    }

    /* ─── Form Elements ─── */
    .pf-form-group { margin-bottom: 0.875rem; }
    .pf-form-hint { color: var(--text-muted); font-size: 0.6875rem; margin-top: 0.25rem; }
    .pf-form-error { color: #ef4444; font-size: 0.75rem; margin-top: 0.3rem; }
    .pf-input-error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.15) !important;
    }

    /* ─── Consent Items ─── */
    .pf-consent {
        padding: 0.75rem;
        border-radius: 0.625rem;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        margin-bottom: 0.625rem;
    }

    html.light-mode .pf-consent {
        background: rgba(0, 51, 127, 0.02);
        border-color: rgba(0, 51, 127, 0.06);
    }

    .pf-consent-label {
        display: flex;
        align-items: flex-start;
        gap: 0.625rem;
        cursor: pointer;
    }

    .pf-consent-label input { margin-top: 0.2rem; flex-shrink: 0; }
    .pf-consent-body { flex: 1; }

    .pf-consent-title {
        font-weight: 600;
        color: var(--text-primary);
        display: block;
        margin-bottom: 0.15rem;
        font-size: 0.8125rem;
    }

    .pf-consent-desc {
        color: var(--text-secondary);
        font-size: 0.75rem;
        display: block;
        line-height: 1.5;
    }

    .pf-consent-ok {
        color: #22c55e;
        font-size: 0.6875rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        margin-top: 0.3rem;
    }

    /* ─── Password Match ─── */
    .pf-pw-msg {
        font-size: 0.75rem;
        margin-top: 0.3rem;
        display: none;
        align-items: center;
        gap: 0.3rem;
    }

    /* ─── Accessoire Chips ─── */
    .pf-acc-section-label {
        font-size: 0.625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        margin-bottom: 0.4rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    .pf-acc-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.375rem;
        margin-bottom: 0.625rem;
    }

    .pf-acc-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.625rem;
        border-radius: 2rem;
        font-size: 0.6875rem;
        font-weight: 600;
        cursor: pointer;
        background: rgba(255,255,255,0.04);
        color: var(--text-secondary);
        border: 1px solid rgba(255,255,255,0.08);
        transition: all 150ms ease;
    }

    html.light-mode .pf-acc-chip {
        background: rgba(0,0,0,0.03);
        border-color: rgba(0,0,0,0.08);
    }

    .pf-acc-chip:hover {
        background: rgba(168,85,247,0.08);
        color: var(--text-primary);
    }

    .pf-acc-chip.is-active {
        background: rgba(168,85,247,0.12);
        color: #a855f7;
        border-color: rgba(168,85,247,0.3);
    }

    .pf-acc-chip__preview {
        width: 28px;
        height: 28px;
        border-radius: 50%;
    }

    .pf-acc-colors {
        display: flex;
        gap: 0.3rem;
        margin-bottom: 0.75rem;
        padding-left: 0.25rem;
    }

    .pf-acc-color-dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 150ms ease;
    }

    .pf-acc-color-dot:hover, .pf-acc-color-dot.selected {
        transform: scale(1.2);
    }

    .pf-acc-color-dot.selected {
        border-color: var(--text-primary);
    }

    .pf-acc-empty {
        padding: 1rem;
        text-align: center;
        color: var(--text-muted);
        font-size: 0.8125rem;
    }

    .pf-acc-empty a {
        color: #a855f7;
        font-weight: 600;
        text-decoration: none;
    }

    .pf-acc-empty a:hover { text-decoration: underline; }

    /* ─── Info Rows (Konto-Details) ─── */
    .pf-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
    }

    .pf-info-row + .pf-info-row {
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    html.light-mode .pf-info-row + .pf-info-row {
        border-top-color: rgba(0, 51, 127, 0.08);
    }

    .pf-info-label { font-size: 0.75rem; color: var(--text-secondary); }

    .pf-info-value {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-primary);
        text-align: right;
    }

    .pf-info-sub {
        display: block;
        font-size: 0.625rem;
        color: var(--text-muted);
        font-weight: 400;
    }

    /* ─── Locked toggle (placeholder) ─── */
    .pf-toggle-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.625rem 0;
    }

    .pf-toggle-row + .pf-toggle-row {
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    html.light-mode .pf-toggle-row + .pf-toggle-row {
        border-top-color: rgba(0, 51, 127, 0.07);
    }

    .pf-toggle-info { flex: 1; }

    .pf-toggle-title {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.15rem;
    }

    .pf-toggle-desc {
        font-size: 0.75rem;
        color: var(--text-secondary);
        line-height: 1.4;
    }

    .pf-fake-toggle {
        width: 38px;
        height: 22px;
        border-radius: 11px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.1);
        flex-shrink: 0;
        position: relative;
        margin-top: 0.1rem;
    }

    .pf-fake-toggle::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.35);
    }

    html.light-mode .pf-fake-toggle {
        background: rgba(0, 51, 127, 0.08);
        border-color: rgba(0, 51, 127, 0.1);
    }

    html.light-mode .pf-fake-toggle::after {
        background: rgba(0, 51, 127, 0.3);
    }

    /* ─── Stagger Animation ─── */
    @keyframes dash-rise {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .pf-sidebar > * {
        animation: dash-rise 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .pf-sidebar > *:nth-child(1) { animation-delay: 0.04s; }
    .pf-sidebar > *:nth-child(2) { animation-delay: 0.09s; }
    .pf-sidebar > *:nth-child(3) { animation-delay: 0.14s; }

    .pf-main > * {
        animation: dash-rise 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .pf-main > *:nth-child(1) { animation-delay: 0.06s; }
    .pf-main > *:nth-child(2) { animation-delay: 0.11s; }
    .pf-main > *:nth-child(3) { animation-delay: 0.16s; }
    .pf-main > *:nth-child(4) { animation-delay: 0.21s; }
    .pf-main > *:nth-child(5) { animation-delay: 0.26s; }
    .pf-main > *:nth-child(6) { animation-delay: 0.31s; }
    .pf-main > *:nth-child(7) { animation-delay: 0.36s; }

    @media (prefers-reduced-motion: reduce) {
        .pf-sidebar > *, .pf-main > * { animation: none; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">

    {{-- ── Header ── --}}
    <header class="dashboard-header">
        <h1 class="page-title">Dein <span>Profil</span></h1>
        <p class="page-subtitle">Verwalte deine persönlichen Daten und Einstellungen</p>
    </header>

    {{-- ── Status Messages ── --}}
    @if (session('status') == 'profile-updated' || session('status') == 'password-updated' || session('status') == 'email-change-cancelled' || session('status') == 'extras-enabled-updated')
    <div class="alert-compact glass-success" style="margin-bottom: 1.25rem;">
        <i class="bi bi-check-circle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">
                @if(session('status') == 'profile-updated') Profil erfolgreich aktualisiert.
                @elseif(session('status') == 'password-updated') Passwort erfolgreich geändert.
                @elseif(session('status') == 'email-change-cancelled') E-Mail-Änderung abgebrochen.
                @elseif(session('status') == 'extras-enabled-updated') Zusatz-Fragen-Einstellung gespeichert.
                @endif
            </div>
        </div>
        <button style="background:none;border:none;cursor:pointer;font-size:1.25rem;color:var(--text-secondary);" onclick="this.parentElement.remove()">&times;</button>
    </div>
    @endif

    @if ($user->pending_email)
    <div class="alert-compact glass-warning" style="margin-bottom: 1.25rem;">
        <i class="bi bi-clock-history alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">E-Mail-Änderung ausstehend</div>
            <div class="alert-compact-desc">
                Ein Bestätigungscode wurde an <strong>{{ $user->pending_email }}</strong> gesendet.
                <a href="{{ route('verification.notice') }}" style="color:var(--gold);text-decoration:underline;">Jetzt bestätigen</a>
                <form method="POST" action="{{ route('profile.cancel-email-change') }}" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:none;border:none;color:var(--text-secondary);text-decoration:underline;cursor:pointer;padding:0;font-size:inherit;">Abbrechen</button>
                </form>
            </div>
        </div>
    </div>
    @elseif (!$user->hasVerifiedEmail())
    <div class="alert-compact glass-warning" style="margin-bottom: 1.25rem;">
        <i class="bi bi-clock-history alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">E-Mail-Bestätigung erforderlich</div>
            <div class="alert-compact-desc">
                Deine E-Mail-Adresse muss innerhalb von 5 Minuten bestätigt werden.
                @if (session('status') == 'email-verification-sent')
                    Eine neue Bestätigungs-E-Mail wurde gesendet.
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ── Two-Column Layout ── --}}
    <div class="pf-layout">

        {{-- ──────────────────────────────────────────
             SIDEBAR (Left)
        ────────────────────────────────────────── --}}
        <aside class="pf-sidebar">

            {{-- Hero Card: Avatar + Name --}}
            <div class="glass-blue pf-hero">
                <div class="pf-hero-avatar-wrap">
                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="pf-hero-avatar" id="pf-avatar-img">
                    <button type="button" class="pf-hero-regen" id="pf-regen-btn" title="Avatar neu generieren">
                        <i class="bi bi-arrow-repeat"></i>
                    </button>
                </div>

                <div class="pf-hero-name">{{ $user->name }}</div>
                <div class="pf-hero-email">{{ $user->email }}</div>

                <div class="pf-hero-tags">
                    @if($user->pending_email)
                        <span class="pf-tag pf-tag--warn">E-Mail ausstehend</span>
                    @elseif($user->hasVerifiedEmail())
                        <span class="pf-tag pf-tag--ok">Verifiziert</span>
                    @else
                        <span class="pf-tag pf-tag--warn">Nicht verifiziert</span>
                    @endif
                    <span class="pf-tag pf-tag--info">Seit {{ $user->created_at->format('d.m.Y') }}</span>
                </div>
            </div>

            {{-- Stats Card --}}
            <div class="glass" style="border-radius:1rem;padding:1rem 1.25rem;">
                <div class="pf-section-title" style="margin-bottom:0.625rem;">Konto-Details</div>
                <div class="pf-stat-list">
                    <div class="pf-stat-row">
                        <span class="pf-stat-label"><i class="bi bi-calendar3"></i> Dabei seit</span>
                        <span class="pf-stat-value">
                            {{ $user->created_at->format('d.m.Y') }}
                            <span class="pf-stat-sub">{{ floor($user->created_at->diffInDays(now())) }} Tage</span>
                        </span>
                    </div>
                    <div class="pf-stat-row">
                        <span class="pf-stat-label"><i class="bi bi-shield-check"></i> Status</span>
                        <span class="pf-stat-value">
                            @if($user->pending_email)
                                <span style="color:#f59e0b;">E-Mail-Änderung</span>
                                <span class="pf-stat-sub">Bestätigung ausstehend</span>
                            @elseif($user->hasVerifiedEmail())
                                <span style="color:#22c55e;">Bestätigt</span>
                                <span class="pf-stat-sub">E-Mail verifiziert</span>
                            @else
                                <span style="color:#f59e0b;">Ausstehend</span>
                                <span class="pf-stat-sub">Nicht verifiziert</span>
                            @endif
                        </span>
                    </div>
                    <div class="pf-stat-row">
                        <span class="pf-stat-label"><i class="bi bi-clock"></i> Letzter Login</span>
                        <span class="pf-stat-value">
                            {{ $user->last_login_at ? $user->last_login_at->format('d.m.Y') : 'Gerade eben' }}
                            <span class="pf-stat-sub">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Erste Anmeldung' }}</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Active Shop Effects (if any) --}}
            @php
                $hasActiveEffects = ($user->profile_frame_until && $user->profile_frame_until->isFuture())
                    || ($user->glowing_name_until && $user->glowing_name_until->isFuture())
                    || ($user->double_xp_until && $user->double_xp_until->isFuture())
                    || $user->active_title;
            @endphp
            @if($hasActiveEffects)
            <div class="glass" style="border-radius:1rem;padding:1rem 1.25rem;">
                <div class="pf-section-title" style="margin-bottom:0.625rem;">Aktive Effekte</div>

                @if($user->active_title)
                <div class="pf-effect-chip pf-effect-chip--gold">
                    <i class="bi bi-award"></i>
                    <div>
                        <div class="pf-effect-chip-label">{{ $user->active_title }}</div>
                        <div class="pf-effect-chip-sub">Aktiver Titel</div>
                    </div>
                </div>
                @endif

                @if($user->profile_frame_until && $user->profile_frame_until->isFuture())
                <div class="pf-effect-chip">
                    <i class="bi bi-circle-square"></i>
                    <div>
                        <div class="pf-effect-chip-label">Profil-Rahmen</div>
                        <div class="pf-effect-chip-sub">Bis {{ $user->profile_frame_until->format('d.m.Y') }}</div>
                    </div>
                </div>
                @endif

                @if($user->glowing_name_until && $user->glowing_name_until->isFuture())
                <div class="pf-effect-chip">
                    <i class="bi bi-stars"></i>
                    <div>
                        <div class="pf-effect-chip-label">Leuchtender Name</div>
                        <div class="pf-effect-chip-sub">Bis {{ $user->glowing_name_until->format('d.m.Y') }}</div>
                    </div>
                </div>
                @endif

                @if($user->double_xp_until && $user->double_xp_until->isFuture())
                <div class="pf-effect-chip pf-effect-chip--gold">
                    <i class="bi bi-lightning-charge"></i>
                    <div>
                        <div class="pf-effect-chip-label">Doppel-XP</div>
                        <div class="pf-effect-chip-sub">Bis {{ $user->double_xp_until->format('d.m.Y') }}</div>
                    </div>
                </div>
                @endif
            </div>
            @endif

        </aside>{{-- /pf-sidebar --}}

        {{-- ──────────────────────────────────────────
             MAIN (Right): Settings Sections
        ────────────────────────────────────────── --}}
        <main class="pf-main">

            {{-- ── Persönliche Daten ── --}}
            <div class="glass pf-section">
                <div class="pf-section-header">
                    <span class="pf-section-title">Persönliche Daten</span>
                </div>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="pf-form-group">
                        <label for="name" class="label-glass">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                               class="input-glass @error('name') pf-input-error @enderror" required maxlength="255">
                        @error('name')
                            <div class="pf-form-error">{{ $message }}</div>
                        @else
                            <p class="pf-form-hint">Dieser Name erscheint im Leaderboard</p>
                        @enderror
                    </div>

                    <div class="pf-form-group">
                        <label for="email" class="label-glass">E-Mail-Adresse</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                               class="input-glass @error('email') pf-input-error @enderror" required>
                        @error('email')
                            <div class="pf-form-error">{{ $message }}</div>
                        @enderror
                        @if($user->pending_email)
                            <div style="margin-top:0.35rem;font-size:0.8rem;color:var(--warning);">
                                Ausstehende Änderung zu: {{ $user->pending_email }}
                            </div>
                        @endif
                    </div>

                    {{-- Prüfungsdatum --}}
                    <div class="pf-consent">
                        <div class="pf-consent-body" style="width:100%;">
                            <span class="pf-consent-title">Prüfungsdatum (optional)</span>
                            <span class="pf-consent-desc" style="margin-bottom:0.5rem;display:block;">Für eine personalisierte Lernempfehlung auf dem Dashboard.</span>
                            <div style="display:flex;gap:0.5rem;align-items:center;">
                                <input type="date" name="exam_date" id="exam_date"
                                       value="{{ old('exam_date', $user->exam_date?->format('Y-m-d')) }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       class="input-glass" style="flex:1;">
                                @if($user->exam_date)
                                <button type="button" onclick="document.getElementById('exam_date').value=''"
                                        class="btn-ghost btn-sm" style="flex-shrink:0;">Zurücksetzen</button>
                                @endif
                            </div>
                            @error('exam_date')
                                <div class="pf-form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- E-Mail Consent --}}
                    <div class="pf-consent">
                        <label class="pf-consent-label">
                            <input type="checkbox" name="email_consent" value="1"
                                   {{ old('email_consent', $user->email_consent) ? 'checked' : '' }}
                                   class="checkbox-glass">
                            <div class="pf-consent-body">
                                <span class="pf-consent-title">E-Mail-Benachrichtigungen</span>
                                <span class="pf-consent-desc">Erhalte E-Mails zu Lernfortschritt, neuen Features und Systeminformationen.</span>
                                @if($user->email_consent_at)
                                    <span class="pf-consent-ok">
                                        <i class="bi bi-check-circle"></i> Zustimmung am {{ $user->email_consent_at->format('d.m.Y \u\m H:i') }} Uhr
                                    </span>
                                @endif
                            </div>
                        </label>
                    </div>

                    {{-- Leaderboard Consent --}}
                    <div class="pf-consent">
                        <label class="pf-consent-label">
                            <input type="checkbox" name="leaderboard_consent" value="1"
                                   {{ $user->leaderboard_consent ? 'checked' : '' }}
                                   class="checkbox-glass">
                            <div class="pf-consent-body">
                                <span class="pf-consent-title">Leaderboard-Teilnahme</span>
                                <span class="pf-consent-desc">Dein Name erscheint im öffentlichen Leaderboard und andere Nutzer können deine Punkte sehen.</span>
                                @if($user->leaderboard_consent)
                                    <span class="pf-consent-ok">
                                        <i class="bi bi-check-circle"></i> Zustimmung am {{ $user->leaderboard_consent_at->format('d.m.Y \u\m H:i') }} Uhr
                                    </span>
                                @endif
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="btn-primary" style="width:100%;">Profil speichern</button>
                </form>
            </div>

            {{-- ── Avatar-Accessoires ── --}}
            @php
                $totalOwned = count($ownedAccessories['accessories'] ?? []) + count($ownedAccessories['top'] ?? []) + count($ownedAccessories['facialHair'] ?? []);
            @endphp
            <div class="glass pf-section" x-data="accessoryManager()">
                <div class="pf-section-header">
                    <span class="pf-section-title">Avatar-Accessoires</span>
                    <span style="font-size:0.6875rem;color:var(--text-muted);">{{ $totalOwned }} besessen</span>
                </div>

                @if($totalOwned > 0)
                    {{-- Brillen --}}
                    @if(count($ownedAccessories['accessories'] ?? []) > 0)
                    <div class="pf-acc-section-label">Brillen</div>
                    <div class="pf-acc-chips">
                        @foreach($ownedAccessories['accessories'] as $slug => $acc)
                        <button class="pf-acc-chip" :class="activeItems.accessories === '{{ $acc['accessory_value'] }}' && 'is-active'"
                                @click="toggle('{{ $slug }}', '{{ $acc['accessory_value'] }}', 'accessories')" :disabled="toggling">
                            <img src="https://dicebear.niclas-reutter.de/9.x/avataaars/svg?radius=50&seed=Preview&backgroundType=gradientLinear&backgroundColor=00337f,0055cc&backgroundRotation=135&accessories={{ $acc['accessory_value'] }}&accessoriesProbability=100&eyes=default&mouth=smile" alt="{{ $acc['name'] }}" class="pf-acc-chip__preview" loading="lazy">
                            {{ $acc['name'] }}
                        </button>
                        @endforeach
                    </div>
                    <template x-if="activeItems.accessories">
                        <div class="pf-acc-colors">
                            @php $glassColors = ['000000', '4a312c', '5b9aff', 'fbbf24', 'ef4444']; @endphp
                            @foreach($glassColors as $hex)
                            <div class="pf-acc-color-dot" :class="activeItems.accessoriesColor === '{{ $hex }}' && 'selected'"
                                 style="background:#{{ $hex }};" @click="changeColor(activeSlug('accessories'), '{{ $hex }}')"></div>
                            @endforeach
                        </div>
                    </template>
                    @endif

                    {{-- Kopfbedeckungen --}}
                    @if(count($ownedAccessories['top'] ?? []) > 0)
                    <div class="pf-acc-section-label">Kopfbedeckungen</div>
                    <div class="pf-acc-chips">
                        @foreach($ownedAccessories['top'] as $slug => $acc)
                        <button class="pf-acc-chip" :class="activeItems.top === '{{ $acc['accessory_value'] }}' && 'is-active'"
                                @click="toggle('{{ $slug }}', '{{ $acc['accessory_value'] }}', 'top')" :disabled="toggling">
                            <img src="https://dicebear.niclas-reutter.de/9.x/avataaars/svg?radius=50&seed=Preview&backgroundType=gradientLinear&backgroundColor=00337f,0055cc&backgroundRotation=135&top={{ $acc['accessory_value'] }}&eyes=default&mouth=smile" alt="{{ $acc['name'] }}" class="pf-acc-chip__preview" loading="lazy">
                            {{ $acc['name'] }}
                        </button>
                        @endforeach
                    </div>
                    @endif

                    {{-- Bärte --}}
                    @if(count($ownedAccessories['facialHair'] ?? []) > 0)
                    <div class="pf-acc-section-label">Bärte</div>
                    <div class="pf-acc-chips">
                        @foreach($ownedAccessories['facialHair'] as $slug => $acc)
                        <button class="pf-acc-chip" :class="activeItems.facialHair === '{{ $acc['accessory_value'] }}' && 'is-active'"
                                @click="toggle('{{ $slug }}', '{{ $acc['accessory_value'] }}', 'facialHair')" :disabled="toggling">
                            <img src="https://dicebear.niclas-reutter.de/9.x/avataaars/svg?radius=50&seed=Preview&backgroundType=gradientLinear&backgroundColor=00337f,0055cc&backgroundRotation=135&facialHair={{ $acc['accessory_value'] }}&facialHairProbability=100&eyes=default&mouth=smile" alt="{{ $acc['name'] }}" class="pf-acc-chip__preview" loading="lazy">
                            {{ $acc['name'] }}
                        </button>
                        @endforeach
                    </div>
                    <template x-if="activeItems.facialHair">
                        <div class="pf-acc-colors">
                            @php $beardColors = ['2c1b18', '4a312c', '724133', 'a55728', 'b58143', 'c93305', 'd6b370', '000000']; @endphp
                            @foreach($beardColors as $hex)
                            <div class="pf-acc-color-dot" :class="activeItems.facialHairColor === '{{ $hex }}' && 'selected'"
                                 style="background:#{{ $hex }};" @click="changeColor(activeSlug('facialHair'), '{{ $hex }}')"></div>
                            @endforeach
                        </div>
                    </template>
                    @endif
                @else
                    <div class="pf-acc-empty">
                        Noch keine Accessoires. <a href="{{ route('shop.index') }}">Besuche den Shop!</a>
                    </div>
                @endif
            </div>

            {{-- ── Zusatz-Fragen ── --}}
            <div class="glass pf-section" id="extras-enabled" style="scroll-margin-top:5rem;">
                <div class="pf-section-header">
                    <span class="pf-section-title">Zusatz-Fragen</span>
                </div>

                <form method="post" action="{{ route('profile.extras-enabled') }}">
                    @csrf
                    <input type="hidden" name="extras_enabled" value="0">

                    <div class="pf-consent">
                        <label class="pf-consent-label" for="extras_enabled" style="cursor:pointer;">
                            <input id="extras_enabled" name="extras_enabled" type="checkbox" value="1"
                                   class="checkbox-glass"
                                   {{ old('extras_enabled', $user->extras_enabled) ? 'checked' : '' }}>
                            <div class="pf-consent-body">
                                <span class="pf-consent-title">Zusatz-Fragen aktivieren</span>
                                <span class="pf-consent-desc">Zusatz-Fragen sind abwechslungsreiche Übungsformate (Zuordnung, Bilder) außerhalb der offiziellen THW-Prüfungsfragen. Sie erscheinen zusätzlich im Übungsmodus und beeinflussen nicht deine Prüfungsfreischaltung.</span>
                            </div>
                        </label>
                    </div>

                    @error('extras_enabled')
                        <div class="pf-form-error" style="margin-bottom:0.5rem;">{{ $message }}</div>
                    @enderror

                    <button type="submit" class="btn-primary" style="width:100%;">Einstellung speichern</button>
                </form>
            </div>

            {{-- ── Sicherheit ── --}}
            <div class="glass pf-section">
                <div class="pf-section-header">
                    <span class="pf-section-title">Passwort ändern</span>
                </div>

                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="pf-form-group">
                        <label for="current_password" class="label-glass">Aktuelles Passwort</label>
                        <input type="password" name="current_password" id="current_password"
                               class="input-glass @error('current_password') pf-input-error @enderror"
                               placeholder="Aktuelles Passwort">
                        @error('current_password')
                            <div class="pf-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="pf-form-group">
                        <label for="password" class="label-glass">Neues Passwort</label>
                        <input type="password" name="password" id="password"
                               class="input-glass @error('password') pf-input-error @enderror"
                               placeholder="Mindestens 8 Zeichen"
                               oninput="checkPasswordMatch()">
                        @error('password')
                            <div class="pf-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="pf-form-group">
                        <label for="password_confirmation" class="label-glass">Passwort bestätigen</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="input-glass"
                               placeholder="Passwort wiederholen"
                               oninput="checkPasswordMatch()">
                        <div id="password-match-message" class="pf-pw-msg"></div>
                    </div>

                    <button type="submit" class="btn-secondary" style="width:100%;">Passwort ändern</button>
                </form>
            </div>

            {{-- ── [GREYED OUT] Benachrichtigungen ── --}}
            <div class="glass pf-section pf-section--locked">
                <div class="pf-section-header">
                    <span class="pf-section-title">Benachrichtigungen</span>
                    <span class="pf-locked-badge"><i class="bi bi-lock"></i> Bald</span>
                </div>
                <div class="pf-locked-content">
                    <div class="pf-toggle-row">
                        <div class="pf-toggle-info">
                            <div class="pf-toggle-title">Push-Benachrichtigungen</div>
                            <div class="pf-toggle-desc">Erhalte Erinnerungen und Lernhinweise direkt im Browser.</div>
                        </div>
                        <div class="pf-fake-toggle"></div>
                    </div>
                    <div class="pf-toggle-row">
                        <div class="pf-toggle-info">
                            <div class="pf-toggle-title">Lernstreak-Erinnerungen</div>
                            <div class="pf-toggle-desc">Benachrichtigung, wenn dein Streak in Gefahr ist.</div>
                        </div>
                        <div class="pf-fake-toggle"></div>
                    </div>
                    <div class="pf-toggle-row">
                        <div class="pf-toggle-info">
                            <div class="pf-toggle-title">Prüfungsvorbereitungs-Tipps</div>
                            <div class="pf-toggle-desc">Wöchentliche Tipps basierend auf deinem Lernfortschritt.</div>
                        </div>
                        <div class="pf-fake-toggle"></div>
                    </div>
                </div>
            </div>

            {{-- ── [GREYED OUT] Zwei-Faktor-Authentifizierung ── --}}
            <div class="glass pf-section pf-section--locked">
                <div class="pf-section-header">
                    <span class="pf-section-title">Zwei-Faktor-Authentifizierung</span>
                    <span class="pf-locked-badge"><i class="bi bi-lock"></i> Bald</span>
                </div>
                <div class="pf-locked-content">
                    <div class="pf-toggle-row">
                        <div class="pf-toggle-info">
                            <div class="pf-toggle-title">2FA aktivieren</div>
                            <div class="pf-toggle-desc">Schütze deinen Account mit einem zweiten Faktor (Authenticator-App oder SMS).</div>
                        </div>
                        <div class="pf-fake-toggle"></div>
                    </div>
                    <div style="margin-top: 0.75rem; padding: 0.75rem; border-radius: 0.5rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                        <div style="font-size:0.75rem;color:var(--text-secondary);display:flex;align-items:center;gap:0.4rem;">
                            <i class="bi bi-qr-code" style="font-size:1rem;"></i>
                            QR-Code-Setup mit Authenticator-App
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Gefahrenzone ── --}}
            <div class="glass pf-section" style="border:1px solid rgba(239,68,68,0.15);">
                <div class="pf-section-header">
                    <span class="pf-section-title" style="color:rgba(239,68,68,0.7);">Gefahrenzone</span>
                </div>

                <p style="font-size:0.75rem;color:var(--text-secondary);margin-bottom:0.875rem;display:flex;align-items:flex-start;gap:0.4rem;line-height:1.5;">
                    <i class="bi bi-exclamation-triangle" style="color:#ef4444;flex-shrink:0;margin-top:0.1rem;"></i>
                    Diese Aktion kann nicht rückgängig gemacht werden. Alle deine Daten werden permanent gelöscht.
                </p>

                <form method="POST" action="{{ route('profile.destroy') }}"
                      onsubmit="return confirm('Bist du dir absolut sicher? Alle deine Daten werden permanent gelöscht.')">
                    @csrf
                    @method('DELETE')

                    <div class="pf-form-group" style="margin-bottom:0.75rem;">
                        <label for="password_delete" class="label-glass">Passwort zur Bestätigung</label>
                        <input type="password" name="password" id="password_delete"
                               class="input-glass @error('password', 'userDeletion') pf-input-error @enderror"
                               placeholder="Passwort eingeben">
                        @error('password', 'userDeletion')
                            <div class="pf-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-danger" style="width:100%;">Account permanent löschen</button>
                </form>
            </div>

        </main>{{-- /pf-main --}}

    </div>{{-- /pf-layout --}}

</div>{{-- /dashboard-container --}}

<script>
    function checkPasswordMatch() {
        var pw = document.getElementById('password').value;
        var confirm = document.getElementById('password_confirmation').value;
        var msg = document.getElementById('password-match-message');

        if (pw && confirm) {
            var match = pw === confirm;
            document.getElementById('password').classList.toggle('pf-input-error', !match);
            document.getElementById('password_confirmation').classList.toggle('pf-input-error', !match);
            msg.style.display = 'flex';
            msg.style.color = match ? '#22c55e' : '#ef4444';
            msg.innerHTML = '<i class="bi bi-' + (match ? 'check' : 'x') + '-circle"></i> Passwörter stimmen ' + (match ? 'überein' : 'nicht überein');
        } else {
            document.getElementById('password').classList.remove('pf-input-error');
            document.getElementById('password_confirmation').classList.remove('pf-input-error');
            msg.style.display = 'none';
        }
    }

    // Accessory Manager
    function accessoryManager() {
        return {
            activeItems: @json($user->active_accessories ?? (object)[]),
            toggling: false,
            slugMap: @json(collect($ownedAccessories)->flatMap(function($items) { return collect($items)->mapWithKeys(function($item, $slug) { return [$item['accessory_value'] => $slug]; }); })),

            activeSlug(paramName) {
                var val = this.activeItems[paramName];
                return val ? (this.slugMap[val] || '') : '';
            },

            async toggle(slug, value, paramName) {
                if (this.toggling) return;
                this.toggling = true;

                try {
                    var response = await fetch('{{ route("profile.accessory.toggle") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ accessory_slug: slug }),
                        cache: 'no-store',
                    });

                    var data = await response.json();
                    if (data.success) {
                        var active = {};
                        ['accessories', 'top', 'facialHair'].forEach(function(type) {
                            var items = data.owned_accessories[type] || {};
                            Object.values(items).forEach(function(item) {
                                if (item.is_active) {
                                    active[item.accessory_type] = item.accessory_value;
                                }
                            });
                        });
                        if (data.avatar_url) {
                            var url = new URL(data.avatar_url);
                            var accColor = url.searchParams.get('accessoriesColor');
                            var fhColor = url.searchParams.get('facialHairColor');
                            if (accColor) active.accessoriesColor = accColor;
                            if (fhColor) active.facialHairColor = fhColor;
                        }
                        this.activeItems = active;
                        document.getElementById('pf-avatar-img').src = data.avatar_url + '&_t=' + Date.now();
                    }
                } catch (e) {
                    console.error('Accessory toggle error:', e);
                } finally {
                    this.toggling = false;
                }
            },

            async changeColor(slug, color) {
                if (!slug || this.toggling) return;
                this.toggling = true;

                try {
                    var response = await fetch('{{ route("profile.accessory.color") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ accessory_slug: slug, color: color }),
                        cache: 'no-store',
                    });

                    var data = await response.json();
                    if (data.success) {
                        var url = new URL(data.avatar_url);
                        var accColor = url.searchParams.get('accessoriesColor');
                        var fhColor = url.searchParams.get('facialHairColor');
                        if (accColor) this.activeItems.accessoriesColor = accColor;
                        if (fhColor) this.activeItems.facialHairColor = fhColor;
                        document.getElementById('pf-avatar-img').src = data.avatar_url + '&_t=' + Date.now();
                    }
                } catch (e) {
                    console.error('Color change error:', e);
                } finally {
                    this.toggling = false;
                }
            }
        };
    }

    // Avatar regeneration
    var regenBtn = document.getElementById('pf-regen-btn');
    if (regenBtn) {
        regenBtn.addEventListener('click', function() {
            var btn = this;
            var img = document.getElementById('pf-avatar-img');
            if (btn.disabled) return;
            btn.classList.add('is-spinning');
            btn.disabled = true;

            fetch('{{ route("profile.avatar.regenerate") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.avatar_url) {
                    img.src = data.avatar_url + '&_t=' + Date.now();
                }
            })
            .catch(function(e) { console.error('Avatar error:', e); })
            .finally(function() {
                setTimeout(function() {
                    btn.classList.remove('is-spinning');
                    btn.disabled = false;
                }, 600);
            });
        });
    }
</script>
@endsection
