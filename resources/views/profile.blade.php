@extends('layouts.app')

@section('title', 'Profil bearbeiten - THW Trainer')
@section('description', 'Bearbeite dein THW-Trainer Profil: Ändere deine persönlichen Daten, Passwort und verwalte deinen Account.')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Avatar ─── */
    .pf-avatar-card {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .pf-avatar-wrap {
        position: relative;
        flex-shrink: 0;
    }

    .pf-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.1);
        object-fit: cover;
        background: rgba(255, 255, 255, 0.05);
        display: block;
    }

    html.light-mode .pf-avatar {
        border-color: rgba(0, 51, 127, 0.12);
    }

    .pf-avatar-regen {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00337F, #0055cc);
        border: 2px solid var(--bg-base);
        color: #fff;
        font-size: 0.7rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 200ms, box-shadow 200ms;
        padding: 0;
    }

    .pf-avatar-regen:hover {
        transform: scale(1.15);
        box-shadow: 0 4px 12px rgba(0, 51, 127, 0.35);
    }

    .pf-avatar-regen.is-spinning i {
        animation: pf-spin 0.6s ease-in-out;
    }

    @keyframes pf-spin {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }

    html.light-mode .pf-avatar-regen {
        border-color: var(--bg-elevated);
    }

    .pf-avatar-info { flex: 1; min-width: 0; }

    .pf-avatar-name {
        font-size: 1.125rem;
        font-weight: 800;
        color: var(--text-primary);
        font-family: 'Barlow Condensed', sans-serif;
        line-height: 1.2;
        margin-bottom: 0.15rem;
    }

    .pf-avatar-email {
        font-size: 0.8125rem;
        color: var(--text-secondary);
        margin-bottom: 0.4rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pf-avatar-tags {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
    }

    .pf-tag {
        font-size: 0.5625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 0.15rem 0.4rem;
        border-radius: 2rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    .pf-tag--ok { background: rgba(34, 197, 94, 0.12); color: #22c55e; }
    .pf-tag--warn { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    .pf-tag--info { background: rgba(91, 154, 255, 0.1); color: #5b9aff; }
    html.light-mode .pf-tag--info { background: rgba(0, 51, 127, 0.08); color: #00337F; }

    /* ─── Form ─── */
    .pf-form-group { margin-bottom: 0.875rem; }
    .pf-form-hint { color: var(--text-muted); font-size: 0.6875rem; margin-top: 0.25rem; }

    .pf-form-error {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.3rem;
    }

    .pf-input-error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.15) !important;
    }

    /* ─── Consent Items ─── */
    .pf-consent {
        padding: 0.75rem;
        border-radius: 0.5rem;
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

    /* ─── Info Rows ─── */
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

    /* ─── Password Match ─── */
    .pf-pw-msg {
        font-size: 0.75rem;
        margin-top: 0.3rem;
        display: none;
        align-items: center;
        gap: 0.3rem;
    }

    /* ─── Stagger Animation ─── */
    @keyframes dash-rise {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .dash-container > .space-y-4 > * {
        animation: dash-rise 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .dash-container > .space-y-4 > *:nth-child(1) { animation-delay: 0.03s; }
    .dash-container > .space-y-4 > *:nth-child(2) { animation-delay: 0.07s; }
    .dash-container > .space-y-4 > *:nth-child(3) { animation-delay: 0.11s; }
    .dash-container > .space-y-4 > *:nth-child(4) { animation-delay: 0.15s; }
    .dash-container > .space-y-4 > *:nth-child(5) { animation-delay: 0.19s; }

    @media (prefers-reduced-motion: reduce) {
        .dash-container > .space-y-4 > * { animation: none; }
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
        padding: 0.3rem 0.6rem;
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

    .pf-acc-empty a:hover {
        text-decoration: underline;
    }

    /* ─── Responsive ─── */
    @media (max-width: 500px) {
        .pf-avatar-card {
            flex-direction: column;
            text-align: center;
        }
        .pf-avatar-tags { justify-content: center; }
    }
</style>
@endpush

@section('content')
<div class="dash-container">

    {{-- ── Header ── --}}
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Einstellungen</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Profil bearbeiten</h1>
        <p class="text-sm" style="color: var(--text-muted);">Verwalte deine persönlichen Daten und Einstellungen</p>
    </div>

    {{-- ── Status Messages ── --}}
    @if (session('status') == 'profile-updated' || session('status') == 'password-updated')
    <div class="alert-compact glass-success" style="margin-bottom: 1rem;">
        <i class="bi bi-check-circle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">
                {{ session('status') == 'profile-updated' ? 'Profil erfolgreich aktualisiert.' : 'Passwort erfolgreich geändert.' }}
            </div>
        </div>
        <button style="background:none;border:none;cursor:pointer;font-size:1.25rem;color:var(--text-secondary);" onclick="this.parentElement.remove()">&times;</button>
    </div>
    @endif

    @if (!$user->hasVerifiedEmail())
    <div class="alert-compact glass-warning" style="margin-bottom: 1rem;">
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

    {{-- ── Gami Pills ── --}}
    <div class="flex gap-3 mb-6" style="flex-wrap: wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:var(--text-primary);">{{ floor($user->created_at->diffInDays(now())) }}</div>
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

    <div class="space-y-4">

        {{-- ── Avatar Card ── --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div class="pf-avatar-card">
                <div class="pf-avatar-wrap">
                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="pf-avatar" id="pf-avatar-img">
                    <button type="button" class="pf-avatar-regen" id="pf-regen-btn" title="Avatar neu generieren">
                        <i class="bi bi-arrow-repeat"></i>
                    </button>
                </div>
                <div class="pf-avatar-info">
                    <div class="pf-avatar-name">{{ $user->name }}</div>
                    <div class="pf-avatar-email">{{ $user->email }}</div>
                    <div class="pf-avatar-tags">
                        @if($user->hasVerifiedEmail())
                            <span class="pf-tag pf-tag--ok">Verifiziert</span>
                        @else
                            <span class="pf-tag pf-tag--warn">Nicht verifiziert</span>
                        @endif
                        <span class="pf-tag pf-tag--info">Seit {{ $user->created_at->format('d.m.Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Avatar-Accessoires ── --}}
        @php
            $totalOwned = count($ownedAccessories['accessories'] ?? []) + count($ownedAccessories['top'] ?? []) + count($ownedAccessories['facialHair'] ?? []);
        @endphp
        <div class="glass p-4" style="border-radius:0.75rem;" x-data="accessoryManager()">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Avatar-Accessoires</span>
                <span style="font-size:0.6875rem;color:var(--text-muted);">{{ $totalOwned }} besitzt</span>
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
                {{-- Farben für aktive Brille --}}
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
                {{-- Farben für aktiven Bart --}}
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

        {{-- ── Persönliche Daten ── --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="margin-bottom:0.75rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Persönliche Daten</span>
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

                <button type="submit" class="btn-primary" style="width:100%;">Profil speichern</button>
            </form>
        </div>

        {{-- ── Sicherheit + Konto-Details ── --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="margin-bottom:0.75rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Sicherheit</span>
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

                <button type="submit" class="btn-secondary" style="width:100%;margin-bottom:1.25rem;">Passwort ändern</button>
            </form>

            {{-- Konto-Details (integriert) --}}
            <div style="padding-top:1rem;border-top:1px solid rgba(255,255,255,0.06);">
                <div style="margin-bottom:0.5rem;">
                    <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Konto-Details</span>
                </div>

                <div class="pf-info-row">
                    <span class="pf-info-label">Beitrittsdatum</span>
                    <span class="pf-info-value">
                        {{ $user->created_at->format('d.m.Y') }}
                        <span class="pf-info-sub">vor {{ floor($user->created_at->diffInDays(now())) }} Tagen</span>
                    </span>
                </div>
                <div class="pf-info-row">
                    <span class="pf-info-label">Konto-Status</span>
                    <span class="pf-info-value">
                        @if($user->hasVerifiedEmail())
                            <span style="color:#22c55e;">Bestätigt</span>
                        @else
                            <span style="color:#f59e0b;">Ausstehend</span>
                        @endif
                        <span class="pf-info-sub">E-Mail {{ $user->hasVerifiedEmail() ? 'verifiziert' : 'nicht verifiziert' }}</span>
                    </span>
                </div>
                <div class="pf-info-row">
                    <span class="pf-info-label">Zuletzt angemeldet</span>
                    <span class="pf-info-value">
                        {{ $user->last_login_at ? $user->last_login_at->format('d.m.Y H:i') : 'Gerade eben' }}
                        <span class="pf-info-sub">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Erste Anmeldung' }}</span>
                    </span>
                </div>
            </div>
        </div>

        {{-- ── Gefahrenzone ── --}}
        <div class="glass p-4" style="border-radius:0.75rem;border:1px solid rgba(239,68,68,0.15);">
            <div style="margin-bottom:0.75rem;">
                <span class="text-xs uppercase tracking-wider" style="color:rgba(239,68,68,0.6);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Gefahrenzone</span>
            </div>

            <p style="font-size:0.75rem;color:var(--text-secondary);margin-bottom:0.75rem;display:flex;align-items:flex-start;gap:0.4rem;line-height:1.5;">
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

    </div>{{-- /space-y-4 --}}

</div>{{-- /dash-container --}}

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
                        // Rebuild active state from response
                        var active = {};
                        ['accessories', 'top', 'facialHair'].forEach(function(type) {
                            var items = data.owned_accessories[type] || {};
                            Object.values(items).forEach(function(item) {
                                if (item.is_active) {
                                    active[item.accessory_type] = item.accessory_value;
                                }
                            });
                        });
                        // Preserve color keys from response avatar URL
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
                        // Update color in active items
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
