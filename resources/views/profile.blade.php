@extends('layouts.app')

@section('title', 'Profil bearbeiten - THW Trainer')
@section('description', 'Bearbeite dein THW-Trainer Profil: Ändere deine persönlichen Daten, Passwort und verwalte deinen Account.')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Avatar Hero ─── */
    .pf-hero {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.5rem;
        border-radius: 0.75rem;
    }

    .pf-avatar-wrap {
        position: relative;
        flex-shrink: 0;
    }

    .pf-avatar {
        width: 96px;
        height: 96px;
        border-radius: 1rem;
        border: 2px solid rgba(255, 255, 255, 0.12);
        object-fit: cover;
        background: rgba(255, 255, 255, 0.05);
        display: block;
    }

    html.light-mode .pf-avatar {
        border-color: rgba(0, 51, 127, 0.15);
    }

    .pf-avatar-regen {
        position: absolute;
        bottom: -6px;
        right: -6px;
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, #00337F, #0055cc);
        border: 2px solid var(--bg-base);
        color: #fff;
        font-size: 0.8rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 200ms, box-shadow 200ms;
    }

    .pf-avatar-regen:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0, 51, 127, 0.3);
    }

    .pf-avatar-regen.pf-spinning i {
        animation: pf-spin 0.6s ease-in-out;
    }

    @keyframes pf-spin {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }

    html.light-mode .pf-avatar-regen {
        border-color: var(--bg-elevated);
    }

    .pf-hero-info {
        flex: 1;
        min-width: 0;
    }

    .pf-hero-name {
        font-size: 1.375rem;
        font-weight: 800;
        color: var(--text-primary);
        font-family: 'Barlow Condensed', sans-serif;
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }

    .pf-hero-email {
        font-size: 0.8125rem;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pf-hero-meta {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .pf-hero-tag {
        font-size: 0.625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 0.2rem 0.5rem;
        border-radius: 2rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    .pf-hero-tag--verified {
        background: rgba(34, 197, 94, 0.12);
        color: #22c55e;
    }

    .pf-hero-tag--unverified {
        background: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
    }

    .pf-hero-tag--joined {
        background: rgba(91, 154, 255, 0.1);
        color: #5b9aff;
    }

    html.light-mode .pf-hero-tag--joined {
        background: rgba(0, 51, 127, 0.08);
        color: #00337F;
    }

    /* ─── Section Label (IBM Plex) ─── */
    .pf-section-label {
        font-size: 0.5625rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        font-weight: 700;
        font-family: 'IBM Plex Mono', monospace;
        margin-bottom: 0.75rem;
    }

    /* ─── Bento Grid ─── */
    .pf-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 1rem;
    }

    .pf-card {
        padding: 1.25rem;
        border-radius: 0.75rem;
    }

    .pf-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }

    .pf-main { grid-row: span 2; padding: 1.5rem; }
    .pf-wide { grid-column: span 2; }

    /* ─── Form Styles ─── */
    .pf-form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .pf-form-group { margin-bottom: 1rem; }

    .pf-form-error {
        color: #ef4444;
        font-size: 0.8rem;
        margin-top: 0.35rem;
    }

    .pf-form-hint {
        color: var(--text-muted);
        font-size: 0.75rem;
        margin-top: 0.35rem;
        margin-bottom: 0;
    }

    .pf-input-error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.15) !important;
    }

    /* ─── Consent Cards ─── */
    .pf-consent {
        padding: 1rem;
        border-radius: 0.75rem;
        margin-bottom: 1rem;
    }

    .pf-consent-label {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        cursor: pointer;
    }

    .pf-consent-label input { margin-top: 0.15rem; flex-shrink: 0; }

    .pf-consent-body { flex: 1; }

    .pf-consent-title {
        font-weight: 600;
        color: var(--text-primary);
        display: block;
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    .pf-consent-desc {
        color: var(--text-secondary);
        font-size: 0.8rem;
        display: block;
        line-height: 1.5;
    }

    .pf-consent-ok {
        color: #22c55e;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        margin-top: 0.5rem;
    }

    /* ─── Account Info Items ─── */
    .pf-info-grid {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .pf-info-item {
        padding: 0.75rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 0.5rem;
    }

    html.light-mode .pf-info-item {
        background: rgba(0, 51, 127, 0.03);
    }

    .pf-info-label {
        font-size: 0.5625rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.2rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    .pf-info-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .pf-info-meta {
        font-size: 0.6875rem;
        color: var(--text-muted);
        margin-top: 0.1rem;
    }

    /* ─── Streak Freeze Indicators ─── */
    .pf-freeze-row {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .pf-freeze-slot {
        flex: 1;
        text-align: center;
        padding: 0.75rem 0.25rem;
        border-radius: 0.5rem;
        transition: background 150ms;
    }

    .pf-freeze-slot--active {
        background: rgba(147, 197, 253, 0.15);
        border: 1px solid rgba(147, 197, 253, 0.3);
    }

    .pf-freeze-slot--used {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    html.light-mode .pf-freeze-slot--used {
        background: rgba(0, 0, 0, 0.03);
        border-color: rgba(0, 0, 0, 0.08);
    }

    .pf-freeze-icon {
        font-size: 1.25rem;
        display: block;
    }

    .pf-freeze-label {
        font-size: 0.6rem;
        font-weight: 600;
        margin-top: 0.25rem;
    }

    .pf-freeze-log-item {
        font-size: 0.75rem;
        color: var(--text-secondary);
        padding: 0.3rem 0;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    /* ─── Danger Zone ─── */
    .pf-danger-text {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        line-height: 1.5;
    }

    .pf-danger-text i {
        color: #ef4444;
        flex-shrink: 0;
        margin-top: 0.1rem;
    }

    /* ─── Alert Compact ─── */
    .pf-alert {
        padding: 0.875rem 1rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .pf-alert-icon { font-size: 1.25rem; }
    .pf-alert-body { flex: 1; }

    .pf-alert-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .pf-alert-desc {
        font-size: 0.75rem;
        color: var(--text-secondary);
    }

    .pf-alert-close {
        background: none;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        font-size: 1.25rem;
        padding: 0;
        line-height: 1;
    }

    .pf-alert-close:hover { color: var(--text-primary); }

    /* ─── Password Match ─── */
    .pf-pw-msg {
        font-size: 0.8rem;
        margin-top: 0.35rem;
        display: none;
    }

    .pf-pw-msg--ok {
        color: #22c55e;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .pf-pw-msg--err {
        color: #ef4444;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    /* ─── Text Colors ─── */
    .pf-text-success { color: #22c55e; }
    .pf-text-warning { color: #f59e0b; }

    /* ─── Stagger Animation ─── */
    @keyframes pf-rise {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .pf-stagger > * {
        animation: pf-rise 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .pf-stagger > *:nth-child(1) { animation-delay: 0.03s; }
    .pf-stagger > *:nth-child(2) { animation-delay: 0.07s; }
    .pf-stagger > *:nth-child(3) { animation-delay: 0.11s; }
    .pf-stagger > *:nth-child(4) { animation-delay: 0.15s; }
    .pf-stagger > *:nth-child(5) { animation-delay: 0.19s; }
    .pf-stagger > *:nth-child(6) { animation-delay: 0.23s; }
    .pf-stagger > *:nth-child(7) { animation-delay: 0.27s; }

    @media (prefers-reduced-motion: reduce) {
        .pf-stagger > * { animation: none; }
    }

    /* ─── Responsive ─── */
    @media (max-width: 900px) {
        .pf-grid {
            grid-template-columns: 1fr;
        }

        .pf-main { grid-row: span 1; }
        .pf-wide { grid-column: span 1; }
    }

    @media (max-width: 768px) {
        .dash-container { padding: 1rem; }
    }

    @media (max-width: 500px) {
        .pf-hero {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .pf-hero-meta { justify-content: center; }

        .pf-form-row {
            grid-template-columns: 1fr;
        }

        .pf-avatar {
            width: 80px;
            height: 80px;
        }
    }
</style>
@endpush

@section('content')
<div class="dash-container">

    {{-- ── Header ── --}}
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Einstellungen</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Profil bearbeiten</h1>
        <p class="text-sm" style="color:var(--text-muted);">Verwalte deine persönlichen Daten und Einstellungen</p>
    </div>

    {{-- ── Status Messages ── --}}
    @if (session('status') == 'profile-updated' || session('status') == 'password-updated' || session('status') == 'avatar-updated')
    <div class="pf-alert glass-success" style="margin-bottom:1rem;">
        <i class="bi bi-check-circle pf-alert-icon"></i>
        <div class="pf-alert-body">
            <div class="pf-alert-title">Erfolgreich aktualisiert</div>
            <div class="pf-alert-desc">
                @if (session('status') == 'profile-updated')
                    Dein Profil wurde erfolgreich aktualisiert.
                @elseif (session('status') == 'avatar-updated')
                    Dein Avatar wurde neu generiert.
                @else
                    Dein Passwort wurde erfolgreich geändert.
                @endif
            </div>
        </div>
        <button onclick="this.parentElement.remove()" class="pf-alert-close">&times;</button>
    </div>
    @endif

    @if (!$user->hasVerifiedEmail())
    <div class="pf-alert glass-warning" style="margin-bottom:1rem;">
        <i class="bi bi-clock-history pf-alert-icon"></i>
        <div class="pf-alert-body">
            <div class="pf-alert-title">E-Mail-Bestätigung erforderlich</div>
            <div class="pf-alert-desc">
                Deine E-Mail-Adresse muss innerhalb von 5 Minuten bestätigt werden.
                @if (session('status') == 'email-verification-sent')
                    Eine neue Bestätigungs-E-Mail wurde gesendet.
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="pf-stagger" style="display:flex;flex-direction:column;gap:1rem;">

        {{-- ── Avatar Hero Card ── --}}
        <div class="glass-gold pf-hero">
            <div class="pf-avatar-wrap">
                <img src="{{ $user->avatar_url }}" alt="Avatar" class="pf-avatar" id="pf-avatar-img">
                <button type="button" class="pf-avatar-regen" id="pf-regen-btn" title="Avatar neu generieren">
                    <i class="bi bi-arrow-repeat"></i>
                </button>
            </div>
            <div class="pf-hero-info">
                <div class="pf-hero-name">{{ $user->name }}</div>
                <div class="pf-hero-email">{{ $user->email }}</div>
                <div class="pf-hero-meta">
                    @if($user->hasVerifiedEmail())
                        <span class="pf-hero-tag pf-hero-tag--verified">Verifiziert</span>
                    @else
                        <span class="pf-hero-tag pf-hero-tag--unverified">Nicht verifiziert</span>
                    @endif
                    <span class="pf-hero-tag pf-hero-tag--joined">Seit {{ $user->created_at->format('d.m.Y') }}</span>
                </div>
            </div>
        </div>

        {{-- ── Gami Pills ── --}}
        <div class="flex gap-3" style="flex-wrap:wrap;">
            <div class="gami-pill">
                <div class="gami-pill__value" style="color:var(--text-primary);">{{ $user->created_at->diffInDays(now()) }}</div>
                <div class="gami-pill__label">Tage dabei</div>
            </div>
            <div class="gami-pill">
                <div class="gami-pill__value" style="color:{{ $user->hasVerifiedEmail() ? 'var(--success)' : 'var(--warning)' }};-webkit-text-fill-color:{{ $user->hasVerifiedEmail() ? 'var(--success)' : 'var(--warning)' }};">{{ $user->hasVerifiedEmail() ? 'OK' : '---' }}</div>
                <div class="gami-pill__label">E-Mail</div>
            </div>
            <div class="gami-pill">
                <div class="gami-pill__value" style="color:#5b9aff;-webkit-text-fill-color:#5b9aff;">{{ $user->last_login_at ? $user->last_login_at->diffForHumans(null, true) : 'Jetzt' }}</div>
                <div class="gami-pill__label">Letzter Login</div>
            </div>
        </div>

        {{-- ── Bento Grid ── --}}
        <div class="pf-grid">

            {{-- Profildaten (Main) --}}
            <div class="glass-tl pf-card pf-main">
                <div class="pf-section-label">Persönliche Daten</div>
                <div class="pf-card-title">Profildaten</div>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="pf-form-row">
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
                        </div>
                    </div>

                    {{-- E-Mail Consent --}}
                    <div class="pf-consent glass-subtle">
                        <label class="pf-consent-label">
                            <input type="checkbox" name="email_consent" value="1"
                                   {{ old('email_consent', $user->email_consent) ? 'checked' : '' }}
                                   class="checkbox-glass">
                            <div class="pf-consent-body">
                                <span class="pf-consent-title">E-Mail-Benachrichtigungen</span>
                                <span class="pf-consent-desc">
                                    Erhalte E-Mails zu deinem Lernfortschritt, neuen Features und wichtigen Systeminformationen.
                                </span>
                                @if($user->email_consent_at)
                                    <span class="pf-consent-ok">
                                        <i class="bi bi-check-circle"></i> Zustimmung erteilt am {{ $user->email_consent_at->format('d.m.Y \u\m H:i') }} Uhr
                                    </span>
                                @endif
                            </div>
                        </label>
                    </div>

                    {{-- Leaderboard Consent --}}
                    <div class="pf-consent glass-subtle">
                        <label class="pf-consent-label">
                            <input type="checkbox" name="leaderboard_consent" value="1"
                                   {{ $user->leaderboard_consent ? 'checked' : '' }}
                                   class="checkbox-glass">
                            <div class="pf-consent-body">
                                <span class="pf-consent-title">Leaderboard-Teilnahme</span>
                                <span class="pf-consent-desc">
                                    Wenn aktiviert, erscheint dein Name im öffentlichen Leaderboard und andere Nutzer können deine Punkte sehen.
                                </span>
                                @if($user->leaderboard_consent)
                                    <span class="pf-consent-ok">
                                        <i class="bi bi-check-circle"></i> Zustimmung erteilt am {{ $user->leaderboard_consent_at->format('d.m.Y \u\m H:i') }} Uhr
                                    </span>
                                @endif
                            </div>
                        </label>
                    </div>

                    {{-- Prüfungsdatum --}}
                    <div class="pf-consent glass-subtle">
                        <div class="pf-consent-body" style="width:100%;">
                            <span class="pf-consent-title">Prüfungsdatum (optional)</span>
                            <span class="pf-consent-desc" style="margin-bottom:0.75rem;display:block;">
                                Setze dein Prüfungsdatum für eine personalisierte Lernempfehlung auf dem Dashboard.
                            </span>
                            <div style="display:flex;gap:0.75rem;align-items:center;">
                                <input type="date"
                                       name="exam_date"
                                       id="exam_date"
                                       value="{{ old('exam_date', $user->exam_date?->format('Y-m-d')) }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       class="input-glass"
                                       style="flex:1;">
                                @if($user->exam_date)
                                <button type="button"
                                        onclick="document.getElementById('exam_date').value = ''"
                                        class="btn-ghost btn-sm"
                                        style="flex-shrink:0;">
                                    Zurücksetzen
                                </button>
                                @endif
                            </div>
                            @error('exam_date')
                                <div class="pf-form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" style="width:100%;">Profil speichern</button>
                </form>
            </div>

            {{-- Passwort ändern --}}
            <div class="glass-br pf-card">
                <div class="pf-section-label">Sicherheit</div>
                <div class="pf-card-title">Passwort ändern</div>

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

            {{-- Konto-Details --}}
            <div class="glass pf-card">
                <div class="pf-section-label">Konto</div>
                <div class="pf-card-title" style="font-size:0.9375rem;">Konto-Details</div>

                <div class="pf-info-grid">
                    <div class="pf-info-item">
                        <div class="pf-info-label">Beitrittsdatum</div>
                        <div class="pf-info-value">{{ $user->created_at->format('d.m.Y') }}</div>
                        <div class="pf-info-meta">vor {{ $user->created_at->diffInDays(now()) }} Tagen</div>
                    </div>
                    <div class="pf-info-item">
                        <div class="pf-info-label">Konto-Status</div>
                        <div class="pf-info-value">
                            @if($user->hasVerifiedEmail())
                                <span class="pf-text-success">Bestätigt</span>
                            @else
                                <span class="pf-text-warning">Ausstehend</span>
                            @endif
                        </div>
                        <div class="pf-info-meta">E-Mail {{ $user->hasVerifiedEmail() ? 'verifiziert' : 'nicht verifiziert' }}</div>
                    </div>
                    <div class="pf-info-item">
                        <div class="pf-info-label">Zuletzt angemeldet</div>
                        <div class="pf-info-value">
                            {{ $user->last_login_at ? $user->last_login_at->format('d.m.Y H:i') : 'Gerade eben' }}
                        </div>
                        <div class="pf-info-meta">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Erste Anmeldung' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Streak Freeze --}}
            <div class="glass pf-card pf-wide">
                @php
                    $gamificationService = new \App\Services\GamificationService();
                    $freezeStatus = $gamificationService->getStreakFreezeStatus($user);
                @endphp

                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                    <div>
                        <div class="pf-section-label" style="margin-bottom:0.25rem;">Streak</div>
                        <div class="pf-card-title" style="margin-bottom:0;">Streak Freeze</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <span style="font-size:0.6875rem;color:var(--text-muted);">{{ $freezeStatus['remaining'] }}/{{ $freezeStatus['available'] }} verfügbar</span>
                    </div>
                </div>

                <p style="font-size:0.8rem;color:var(--text-secondary);margin-bottom:1rem;line-height:1.5;">
                    Streak Freezes schützen deinen Streak automatisch, wenn du einen Tag verpasst.
                </p>

                <div class="pf-freeze-row">
                    @for($i = 0; $i < $freezeStatus['available']; $i++)
                        <div class="pf-freeze-slot {{ $i < $freezeStatus['remaining'] ? 'pf-freeze-slot--active' : 'pf-freeze-slot--used' }}">
                            <i class="bi bi-snow pf-freeze-icon" style="color:{{ $i < $freezeStatus['remaining'] ? '#93c5fd' : 'var(--text-muted)' }};"></i>
                            <div class="pf-freeze-label" style="color:{{ $i < $freezeStatus['remaining'] ? '#93c5fd' : 'var(--text-muted)' }};">
                                {{ $i < $freezeStatus['remaining'] ? 'Verfügbar' : 'Verbraucht' }}
                            </div>
                        </div>
                    @endfor
                </div>

                @if(count($freezeStatus['recent_log']) > 0)
                    <div style="font-size:0.5625rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:0.5rem;font-family:'IBM Plex Mono',monospace;font-weight:700;">Zuletzt verwendet</div>
                    @foreach(array_slice(array_reverse($freezeStatus['recent_log']), 0, 3) as $entry)
                        <div class="pf-freeze-log-item">
                            <i class="bi bi-snow" style="color:#93c5fd;font-size:0.7rem;"></i>
                            {{ \Carbon\Carbon::parse($entry['date'])->format('d.m.Y') }}
                        </div>
                    @endforeach
                @else
                    <div style="font-size:0.8rem;color:var(--text-muted);text-align:center;padding:0.5rem 0;">Noch keine Freezes verwendet</div>
                @endif
            </div>

            {{-- Danger Zone --}}
            <div class="glass-error pf-card pf-wide">
                <div class="pf-section-label" style="color:rgba(239,68,68,0.6);">Gefahrenzone</div>
                <div class="pf-card-title" style="color:#ef4444;">Account löschen</div>

                <p class="pf-danger-text">
                    <i class="bi bi-exclamation-triangle"></i>
                    Diese Aktion kann nicht rückgängig gemacht werden. Alle deine Daten werden permanent gelöscht.
                </p>

                <form method="POST" action="{{ route('profile.destroy') }}"
                      onsubmit="return confirm('Bist du dir absolut sicher? Alle deine Daten werden permanent gelöscht.')">
                    @csrf
                    @method('DELETE')

                    <div class="pf-form-group" style="margin-bottom:1rem;">
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

        </div>{{-- /pf-grid --}}

    </div>{{-- /pf-stagger --}}

</div>{{-- /dash-container --}}

<script>
    // Password match check
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirmation').value;
        const msg = document.getElementById('password-match-message');

        if (password && confirm) {
            if (password === confirm) {
                document.getElementById('password').classList.remove('pf-input-error');
                document.getElementById('password_confirmation').classList.remove('pf-input-error');
                msg.className = 'pf-pw-msg pf-pw-msg--ok';
                msg.innerHTML = '<i class="bi bi-check-circle"></i> Passwörter stimmen überein';
                msg.style.display = 'flex';
            } else {
                document.getElementById('password').classList.add('pf-input-error');
                document.getElementById('password_confirmation').classList.add('pf-input-error');
                msg.className = 'pf-pw-msg pf-pw-msg--err';
                msg.innerHTML = '<i class="bi bi-x-circle"></i> Passwörter stimmen nicht überein';
                msg.style.display = 'flex';
            }
        } else {
            document.getElementById('password').classList.remove('pf-input-error');
            document.getElementById('password_confirmation').classList.remove('pf-input-error');
            msg.style.display = 'none';
        }
    }

    // Avatar regeneration (AJAX)
    document.getElementById('pf-regen-btn').addEventListener('click', function() {
        const btn = this;
        const img = document.getElementById('pf-avatar-img');

        btn.classList.add('pf-spinning');
        btn.disabled = true;

        fetch('{{ route("profile.avatar.regenerate") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            cache: 'no-store',
        })
        .then(r => r.json())
        .then(data => {
            if (data.avatar_url) {
                img.src = data.avatar_url + '&_t=' + Date.now();
            }
        })
        .catch(() => {})
        .finally(() => {
            setTimeout(() => {
                btn.classList.remove('pf-spinning');
                btn.disabled = false;
            }, 600);
        });
    });
</script>
@endsection
