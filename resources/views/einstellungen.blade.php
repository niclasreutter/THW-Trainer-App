@extends('layouts.app')

@section('title', 'Einstellungen - THW Trainer')
@section('description', 'Verwalte dein Konto, Benachrichtigungen, Datenschutz und Sicherheit.')

@push('styles')
<style>
    .dash-container { max-width: 1180px; padding: 0 1rem; }

    .pf-page-title { margin-bottom: 1.5rem; }
    .pf-page-title__eyebrow { font-family: 'IBM Plex Mono', monospace; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); margin-bottom: 0.25rem; }
    .pf-page-title__h1 { font-family: 'Barlow Condensed', 'Figtree', sans-serif; font-weight: 800; font-size: 2rem; line-height: 1.1; color: var(--text-primary); margin: 0 0 0.25rem; letter-spacing: -0.02em; }
    html.light-mode .pf-page-title__h1 { color: var(--thw-blue, #00337F); }
    .pf-page-title__sub { font-size: 0.875rem; color: var(--text-secondary); }

    .bento { display: grid; grid-template-columns: repeat(12, 1fr); gap: 1rem; margin-top: 1rem; }
    .bento > .card-account       { grid-column: span 6; }
    .bento > .card-exam          { grid-column: span 6; }
    .bento > .card-notifications { grid-column: span 6; }
    .bento > .card-privacy       { grid-column: span 6; }
    .bento > .card-security      { grid-column: span 6; }
    .bento > .card-meta          { grid-column: span 12; }
    .bento > .card-danger        { grid-column: span 12; }

    @media (max-width: 1024px) {
        .bento > .card-account, .bento > .card-exam, .bento > .card-notifications,
        .bento > .card-privacy, .bento > .card-security { grid-column: span 12; }
    }

    .card { padding: 1.25rem; border-radius: 0.75rem; }
    .card-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; gap: 1rem; }
    .card-head .section-label { flex: 1; min-width: 0; }
    .card-head__hint { font-size: 0.75rem; color: var(--text-muted); }

    .section-label { font-family: 'IBM Plex Mono', monospace; font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); }
    html.light-mode .section-label { color: #6b7280; }

    .field { display: block; margin-bottom: 1rem; }
    .field:last-child { margin-bottom: 0; }
    .field__label { display: block; font-size: 0.8125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.375rem; }
    .field__help { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.375rem; }
    .field__error { font-size: 0.75rem; color: #ef4444; margin-top: 0.3rem; }
    .field__row { display: flex; gap: 0.5rem; align-items: stretch; }
    .field__row > .input { flex: 1; }

    .input { width: 100%; padding: 0.625rem 0.75rem; border-radius: 0.5rem; border: 1px solid rgba(0,51,127,0.14); background: var(--bg-elevated, #fff); color: var(--text-primary); font-family: 'Figtree', system-ui, sans-serif; font-size: 0.9375rem; line-height: 1.35; transition: border-color 150ms ease, box-shadow 150ms ease; }
    html:not(.light-mode) .input { background: #121214; border-color: rgba(255,255,255,0.1); }
    .input:hover { border-color: rgba(0,51,127,0.25); }
    html:not(.light-mode) .input:hover { border-color: rgba(91,154,255,0.35); }
    .input:focus { outline: none; border-color: #00337F; box-shadow: 0 0 0 3px rgba(0,51,127,0.12); }
    html:not(.light-mode) .input:focus { border-color: #5b9aff; box-shadow: 0 0 0 3px rgba(91,154,255,0.20); }
    .input--error { border-color: #ef4444 !important; box-shadow: 0 0 0 2px rgba(239,68,68,0.15) !important; }

    .toggle-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid rgba(0,51,127,0.06); }
    html:not(.light-mode) .toggle-row { border-bottom-color: rgba(255,255,255,0.06); }
    .toggle-row:last-child { border-bottom: 0; padding-bottom: 0; }
    .toggle-row:first-child { padding-top: 0; }
    .toggle-row__body { flex: 1; min-width: 0; }
    .toggle-row__title { font-size: 0.875rem; font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem; }
    .toggle-row__desc { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; line-height: 1.5; }

    .switch { position: relative; display: inline-block; width: 40px; height: 22px; flex-shrink: 0; }
    .switch input { opacity: 0; width: 0; height: 0; position: absolute; }
    .switch__slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,51,127,0.15); border-radius: 999px; transition: background 150ms ease; }
    html:not(.light-mode) .switch__slider { background: rgba(255,255,255,0.12); }
    .switch__slider::before { content: ''; position: absolute; height: 16px; width: 16px; left: 3px; top: 3px; background: #fff; border-radius: 50%; transition: transform 150ms ease; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
    .switch input:checked + .switch__slider { background: #00337F; }
    html:not(.light-mode) .switch input:checked + .switch__slider { background: #5b9aff; }
    .switch input:checked + .switch__slider::before { transform: translateX(18px); }
    .switch input:disabled + .switch__slider { cursor: not-allowed; opacity: 0.45; }
    .switch--mini { width: 32px; height: 18px; }
    .switch--mini .switch__slider::before { height: 12px; width: 12px; left: 3px; top: 3px; }
    .switch--mini input:checked + .switch__slider::before { transform: translateX(14px); }

    .notif-master { display: flex; flex-direction: column; }
    .notif-categories { margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid rgba(0,51,127,0.08); }
    html:not(.light-mode) .notif-categories { border-top-color: rgba(255,255,255,0.08); }
    .notif-categories__header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem; }
    .notif-categories__label { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: var(--text-muted); }
    .notif-categories__legend { display: flex; gap: 0.875rem; font-size: 0.85rem; color: var(--text-muted); padding-right: 0.25rem; }
    .notif-categories__legend span { display: inline-flex; align-items: center; justify-content: center; width: 32px; }
    .notif-cat-switches { display: flex; gap: 0.625rem; align-items: center; }
    .notif-cat-row.is-saving { opacity: 0.6; pointer-events: none; }

    .meta-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    @media (max-width: 720px) { .meta-grid { grid-template-columns: 1fr; } }
    .meta-item__label { font-family: 'IBM Plex Mono', monospace; font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); margin-bottom: 0.25rem; }
    .meta-item__value { font-size: 0.9375rem; font-weight: 600; color: var(--text-primary); }
    .meta-item__sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.1rem; }

    .danger-card { padding: 1.25rem; border: 1px solid rgba(239,68,68,0.3); background: rgba(239,68,68,0.04); border-radius: 0.75rem; }
    html:not(.light-mode) .danger-card { background: rgba(239,68,68,0.08); }
    .danger-card .section-label { color: #ef4444; }
    .danger-head { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; }
    .danger-icon { width: 36px; height: 36px; border-radius: 0.5rem; background: rgba(239,68,68,0.12); color: #ef4444; display: grid; place-items: center; font-size: 1.125rem; flex-shrink: 0; }
    .danger-title { font-size: 1rem; font-weight: 700; color: var(--text-primary); }
    .danger-desc { font-size: 0.8125rem; color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.5; }

    .btn-danger-inline { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; border-radius: 0.5rem; background: #ef4444; color: #fff; border: 0; font-family: 'Figtree', system-ui, sans-serif; font-weight: 700; font-size: 0.875rem; cursor: pointer; transition: background 150ms ease, box-shadow 150ms ease; }
    .btn-danger-inline:hover { background: #dc2626; box-shadow: 0 4px 14px rgba(239,68,68,0.35); }
    .btn-danger-inline:disabled { opacity: 0.5; cursor: not-allowed; }

    .card-foot { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(0,51,127,0.06); flex-wrap: wrap; }
    html:not(.light-mode) .card-foot { border-top-color: rgba(255,255,255,0.06); }
    .card-foot__note { font-size: 0.75rem; color: var(--text-muted); }

    .btn-pf-primary { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.125rem; border-radius: 0.5rem; background: linear-gradient(135deg, #00337F, #0055cc); color: #fff; border: 0; font-family: 'Figtree', system-ui, sans-serif; font-weight: 700; font-size: 0.875rem; cursor: pointer; transition: box-shadow 150ms ease, transform 150ms ease; }
    .btn-pf-primary:hover { box-shadow: 0 8px 25px rgba(0,51,127,0.45); transform: translateY(-1px); }

    .btn-pf-ghost { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.875rem; border-radius: 0.5rem; background: transparent; color: var(--text-primary); border: 1px solid rgba(0,51,127,0.2); font-family: 'Figtree', system-ui, sans-serif; font-weight: 600; font-size: 0.875rem; cursor: pointer; transition: background 150ms ease, border-color 150ms ease; text-decoration: none; }
    html:not(.light-mode) .btn-pf-ghost { border-color: rgba(255,255,255,0.1); }
    .btn-pf-ghost:hover { background: rgba(0,51,127,0.05); border-color: rgba(0,51,127,0.35); }
    html:not(.light-mode) .btn-pf-ghost:hover { background: rgba(91,154,255,0.1); border-color: rgba(91,154,255,0.35); }

    .info-row { display: flex; gap: 0.75rem; padding: 0.875rem; margin: 0.25rem 0; border-radius: 0.5rem; background: rgba(0, 51, 127, 0.04); border: 1px solid rgba(0, 51, 127, 0.1); }
    html:not(.light-mode) .info-row { background: rgba(91,154,255,0.08); border-color: rgba(91,154,255,0.2); }
    .info-row__icon { width: 28px; height: 28px; border-radius: 50%; background: #00337F; color: #fff; display: grid; place-items: center; font-size: 0.8125rem; flex-shrink: 0; }
    html:not(.light-mode) .info-row__icon { background: #5b9aff; }
    .info-row__title { font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.2rem; }
    .info-row__desc { font-size: 0.75rem; color: var(--text-secondary); line-height: 1.55; }

    .link-row { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid rgba(0,51,127,0.06); text-decoration: none; color: inherit; cursor: pointer; }
    html:not(.light-mode) .link-row { border-bottom-color: rgba(255,255,255,0.06); }
    .link-row:last-child { border-bottom: 0; }
    .link-row__title { font-size: 0.875rem; font-weight: 600; color: var(--text-primary); }
    .link-row__sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem; }

    .is-locked { position: relative; }
    .is-locked .card-body-locked { opacity: 0.42; pointer-events: none; user-select: none; }
    .locked-badge { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.15rem 0.5rem; border-radius: 999px; background: rgba(91,154,255,0.12); border: 1px solid rgba(91,154,255,0.25); color: #5b9aff; font-family: 'IBM Plex Mono', monospace; font-size: 0.5625rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; }
    html.light-mode .locked-badge { background: rgba(0,51,127,0.06); border-color: rgba(0,51,127,0.15); color: #00337F; }

    .pf-pw-msg { font-size: 0.75rem; margin-top: 0.3rem; display: none; align-items: center; gap: 0.3rem; }

    @keyframes pf-rise { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    .bento > * { animation: pf-rise 0.45s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .bento > *:nth-child(1) { animation-delay: 0.03s; }
    .bento > *:nth-child(2) { animation-delay: 0.07s; }
    .bento > *:nth-child(3) { animation-delay: 0.11s; }
    .bento > *:nth-child(4) { animation-delay: 0.15s; }
    .bento > *:nth-child(5) { animation-delay: 0.19s; }
    .bento > *:nth-child(6) { animation-delay: 0.23s; }
    .bento > *:nth-child(7) { animation-delay: 0.27s; }
    @media (prefers-reduced-motion: reduce) { .bento > * { animation: none; } }
</style>
@endpush

@section('content')
@php
    $daysSince = (int) floor($user->created_at->diffInDays(now()));
    $lastActive = $user->last_activity_at
        ? (\Illuminate\Support\Carbon::parse($user->last_activity_at))
        : ($user->last_login_at ?? null);
    $notifCategories = [
        'daily_reminder' => ['title' => 'Tägliche Lernerinnerung', 'desc' => 'Eine kurze Erinnerung, wenn du deinen Streak zu verlieren drohst.'],
        'spaced_repetition' => ['title' => 'Spaced-Repetition-Reviews', 'desc' => 'Benachrichtigung, sobald Fragen zur Wiederholung fällig sind.'],
        'league_updates' => ['title' => 'Liga- & Achievement-Updates', 'desc' => 'Wenn du aufsteigst, fällst oder ein neues Achievement freischaltest.'],
    ];
@endphp

<div class="dash-container">

    <div class="pf-page-title">
        <div class="pf-page-title__eyebrow">Konto</div>
        <h1 class="pf-page-title__h1">Einstellungen</h1>
        <div class="pf-page-title__sub">Persönliche Daten, Benachrichtigungen, Datenschutz und Sicherheit.</div>
    </div>

    @if (session('status'))
    @php
        $isError = in_array(session('status'), ['data-export-rate-limit', 'data-export-error']);
        $alertClass = $isError ? 'glass-warning' : 'glass-success';
        $alertIcon = $isError ? 'bi-exclamation-triangle' : 'bi-check-circle';
    @endphp
    <div class="alert-compact {{ $alertClass }}" style="margin-bottom: 1rem;">
        <i class="bi {{ $alertIcon }} alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">
                @if(session('status') == 'profile-updated') Profil erfolgreich aktualisiert.
                @elseif(session('status') == 'password-updated') Passwort erfolgreich geändert.
                @elseif(session('status') == 'email-change-cancelled') E-Mail-Änderung abgebrochen.
                @elseif(session('status') == 'extras-enabled-updated') Zusatz-Fragen-Einstellung gespeichert.
                @elseif(session('status') == 'email-verification-sent') Bestätigungs-E-Mail wurde gesendet.
                @elseif(session('status') == 'data-export-sent') Dein Datenexport wurde an {{ $user->email }} versendet.
                @elseif(session('status') == 'data-export-rate-limit') {{ session('data_export_message', 'Datenexport derzeit nicht möglich.') }}
                @elseif(session('status') == 'data-export-error') Beim Erstellen des Datenexports ist ein Fehler aufgetreten. Bitte versuche es später erneut.
                @else {{ session('status') }}
                @endif
            </div>
        </div>
        <button style="background:none;border:none;cursor:pointer;font-size:1.25rem;color:var(--text-secondary);" onclick="this.parentElement.remove()">&times;</button>
    </div>
    @endif

    @if ($user->pending_email)
    <div class="alert-compact glass-warning" style="margin-bottom: 1rem;">
        <i class="bi bi-clock-history alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">E-Mail-Änderung ausstehend</div>
            <div class="alert-compact-desc">
                Bestätigungscode gesendet an <strong>{{ $user->pending_email }}</strong>.
                <a href="{{ route('verification.notice') }}" style="color:var(--gold);text-decoration:underline;">Jetzt bestätigen</a>
                <form method="POST" action="{{ route('einstellungen.cancel-email-change') }}" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:none;border:none;color:var(--text-secondary);text-decoration:underline;cursor:pointer;padding:0;font-size:inherit;">Abbrechen</button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <div class="bento">

        {{-- Persönliche Daten --}}
        <div class="glass card-account card">
            <div class="card-head">
                <span class="section-label">Persönliche Daten</span>
            </div>
            <form method="POST" action="{{ route('einstellungen.update') }}" id="profile-main-form">
                @csrf
                @method('PATCH')
                <div class="field">
                    <label class="field__label" for="name">Name</label>
                    <input class="input @error('name') input--error @enderror" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="255">
                    @error('name')
                        <div class="field__error">{{ $message }}</div>
                    @else
                        <div class="field__help">Dieser Name erscheint im Leaderboard.</div>
                    @enderror
                </div>
                <div class="field">
                    <label class="field__label" for="email">E-Mail-Adresse</label>
                    <input class="input @error('email') input--error @enderror" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="field__error">{{ $message }}</div>
                    @else
                        <div class="field__help">Änderungen erfordern eine Bestätigung per E-Mail.</div>
                    @enderror
                </div>
                <div class="field">
                    <label class="field__label">Ortsverband</label>
                    @if(($ortsverbande ?? collect())->isNotEmpty())
                        <div style="display:flex;flex-direction:column;gap:0.5rem;">
                            @foreach($ortsverbande as $ov)
                                <a href="{{ route('ortsverband.show', $ov) }}" style="display:flex;align-items:center;gap:0.75rem;padding:0.625rem 0.875rem;border:1px solid rgba(0,51,127,0.15);border-radius:10px;background:rgba(0,51,127,0.04);text-decoration:none;color:var(--text-primary);">
                                    <i class="bi bi-building" style="color:var(--thw-blue);font-size:1.125rem;"></i>
                                    <div style="flex:1;">
                                        <div style="font-weight:600;font-size:0.9375rem;">{{ $ov->name }}</div>
                                        @if($ov->pivot->role ?? null)
                                            <div style="font-size:0.75rem;color:var(--text-muted);">{{ ucfirst($ov->pivot->role) }}</div>
                                        @endif
                                    </div>
                                    <i class="bi bi-chevron-right" style="color:var(--text-muted);font-size:0.875rem;"></i>
                                </a>
                            @endforeach
                        </div>
                        <div class="field__help">Mitgliedschaften verwalten auf der Ortsverband-Seite.</div>
                    @else
                        <div style="padding:1rem;border:1px dashed rgba(0,51,127,0.2);border-radius:10px;background:rgba(0,51,127,0.03);text-align:center;">
                            <div style="font-size:0.875rem;color:var(--text-secondary);margin-bottom:0.75rem;">Du bist noch keinem Ortsverband beigetreten.</div>
                            <a href="{{ route('ortsverband.index') }}" class="btn-pf-primary" style="display:inline-flex;align-items:center;gap:0.5rem;">
                                <i class="bi bi-plus-circle"></i> Ortsverband beitreten
                            </a>
                        </div>
                    @endif
                </div>
                <input type="hidden" name="email_consent" value="{{ $user->email_consent ? 1 : 0 }}">
                <input type="hidden" name="leaderboard_consent" value="{{ $user->leaderboard_consent ? 1 : 0 }}">
                <input type="hidden" name="exam_date" value="{{ $user->exam_date?->format('Y-m-d') }}">
                <div class="card-foot">
                    <span class="card-foot__note">Änderungen werden nach dem Speichern aktiv.</span>
                    <button class="btn-pf-primary" type="submit">Speichern</button>
                </div>
            </form>
        </div>

        {{-- Prüfung & Zusatz-Fragen --}}
        <div class="glass card-exam card">
            <div class="card-head">
                <span class="section-label">Prüfung & Zusatz-Fragen</span>
            </div>
            <form method="POST" action="{{ route('einstellungen.update') }}" id="exam-date-form">
                @csrf
                @method('PATCH')
                <input type="hidden" name="name" value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">
                <input type="hidden" name="email_consent" value="{{ $user->email_consent ? 1 : 0 }}">
                <input type="hidden" name="leaderboard_consent" value="{{ $user->leaderboard_consent ? 1 : 0 }}">
                <div class="field">
                    <label class="field__label" for="exam-date">Prüfungsdatum (optional)</label>
                    <div class="field__row">
                        <input class="input @error('exam_date') input--error @enderror" type="date" id="exam-date" name="exam_date"
                               value="{{ old('exam_date', $user->exam_date?->format('Y-m-d')) }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        <button class="btn-pf-ghost" type="button" onclick="document.getElementById('exam-date').value=''">Zurücksetzen</button>
                    </div>
                    @error('exam_date')
                        <div class="field__error">{{ $message }}</div>
                    @else
                        <div class="field__help">Für eine personalisierte Lernempfehlung auf dem Dashboard.</div>
                    @enderror
                </div>
            </form>
            <form method="POST" action="{{ route('einstellungen.extras-enabled') }}">
                @csrf
                <input type="hidden" name="extras_enabled" value="0">
                <div class="toggle-row">
                    <div class="toggle-row__body">
                        <div class="toggle-row__title">Zusatz-Fragen aktivieren</div>
                        <div class="toggle-row__desc">Zusätzliche außerhalb-der-Prüfung Übungsfragen. Erscheinen im Übungsmodus und beeinflussen nicht deine Prüfungsauswertung.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="extras_enabled" value="1" onchange="this.form.submit()" {{ $user->extras_enabled ? 'checked' : '' }}>
                        <span class="switch__slider"></span>
                    </label>
                </div>
            </form>
            <div class="card-foot">
                <span class="card-foot__note">Änderungen am Prüfungsdatum nach dem Speichern aktiv.</span>
                <button class="btn-pf-primary" type="button" onclick="document.getElementById('exam-date-form').submit()">Speichern</button>
            </div>
        </div>

        {{-- Benachrichtigungen --}}
        <div class="glass card-notifications card" id="notif-card">
            <div class="card-head">
                <span class="section-label">Benachrichtigungen</span>
                <span class="card-head__hint" id="notif-consent-hint">
                    @if($user->email_consent_at && $user->push_consent_at)
                        E-Mail seit {{ $user->email_consent_at->format('d.m.Y') }} · Push seit {{ $user->push_consent_at->format('d.m.Y') }}
                    @elseif($user->email_consent_at)
                        E-Mail seit {{ $user->email_consent_at->format('d.m.Y') }}
                    @elseif($user->push_consent_at)
                        Push seit {{ $user->push_consent_at->format('d.m.Y') }}
                    @else
                        Noch nicht aktiviert
                    @endif
                </span>
            </div>
            <div class="notif-master">
                <div class="toggle-row notif-master-row">
                    <div class="toggle-row__body">
                        <div class="toggle-row__title">
                            <i class="bi bi-envelope-fill" style="color: var(--thw-blue, #00337F);"></i>
                            E-Mail-Benachrichtigungen
                        </div>
                        <div class="toggle-row__desc">Erhalte E-Mails zu Lernfortschritt, neuen Features und Systeminformationen.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" data-notif-master="email" {{ $user->email_consent ? 'checked' : '' }}>
                        <span class="switch__slider"></span>
                    </label>
                </div>
                <div class="toggle-row notif-master-row">
                    <div class="toggle-row__body">
                        <div class="toggle-row__title">
                            <i class="bi bi-bell-fill" style="color: var(--thw-blue, #00337F);"></i>
                            Push-Benachrichtigungen
                        </div>
                        <div class="toggle-row__desc">Browser- und App-Push für wichtige Updates direkt auf dein Gerät.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" data-notif-master="push" {{ $user->push_consent ? 'checked' : '' }}>
                        <span class="switch__slider"></span>
                    </label>
                </div>
            </div>
            <div class="notif-categories">
                <div class="notif-categories__header">
                    <span class="notif-categories__label">Einzelne Benachrichtigungen</span>
                    <div class="notif-categories__legend">
                        <span title="E-Mail"><i class="bi bi-envelope"></i></span>
                        <span title="Push"><i class="bi bi-bell"></i></span>
                    </div>
                </div>
                @foreach($notifCategories as $key => $cat)
                    <div class="toggle-row notif-cat-row">
                        <div class="toggle-row__body">
                            <div class="toggle-row__title">{{ $cat['title'] }}</div>
                            <div class="toggle-row__desc">{{ $cat['desc'] }}</div>
                        </div>
                        <div class="notif-cat-switches">
                            <label class="switch switch--mini" title="E-Mail">
                                <input type="checkbox" data-notif-channel="email" data-notif-category="{{ $key }}"
                                       {{ $user->{"notify_{$key}_email"} ? 'checked' : '' }}
                                       {{ $user->email_consent ? '' : 'disabled' }}>
                                <span class="switch__slider"></span>
                            </label>
                            <label class="switch switch--mini" title="Push">
                                <input type="checkbox" data-notif-channel="push" data-notif-category="{{ $key }}"
                                       {{ $user->{"notify_{$key}_push"} ? 'checked' : '' }}
                                       {{ $user->push_consent ? '' : 'disabled' }}>
                                <span class="switch__slider"></span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Privatsphäre & Datenschutz --}}
        <div class="glass card-privacy card">
            <div class="card-head">
                <span class="section-label">Privatsphäre & Datenschutz</span>
                <a href="/datenschutz" class="card-head__hint" style="color: var(--thw-blue, #00337F); text-decoration: none; font-weight: 600;">Datenschutzerklärung →</a>
            </div>
            <form method="POST" action="{{ route('einstellungen.update') }}" id="privacy-form">
                @csrf
                @method('PATCH')
                <input type="hidden" name="name" value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">
                <input type="hidden" name="email_consent" value="{{ $user->email_consent ? 1 : 0 }}">
                <input type="hidden" name="exam_date" value="{{ $user->exam_date?->format('Y-m-d') }}">
                <input type="hidden" name="leaderboard_consent" value="0">
                <div class="toggle-row">
                    <div class="toggle-row__body">
                        <div class="toggle-row__title">Leaderboard-Teilnahme</div>
                        <div class="toggle-row__desc">Dein Name erscheint im öffentlichen Leaderboard. Deaktivieren macht dein Konto anonym.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="leaderboard_consent" value="1" onchange="this.form.submit()" {{ $user->leaderboard_consent ? 'checked' : '' }}>
                        <span class="switch__slider"></span>
                    </label>
                </div>
            </form>
            <div class="toggle-row is-locked">
                <div class="toggle-row__body card-body-locked">
                    <div class="toggle-row__title">
                        OV-Ranking sichtbar
                        <span class="locked-badge"><i class="bi bi-lock"></i> Bald</span>
                    </div>
                    <div class="toggle-row__desc">Mitglieder deines Ortsverbandes sehen deinen Fortschritt im OV-Dashboard.</div>
                </div>
                <label class="switch"><input type="checkbox" disabled><span class="switch__slider"></span></label>
            </div>
            <div class="info-row">
                <div class="info-row__icon"><i class="bi bi-info-circle-fill"></i></div>
                <div class="info-row__body">
                    <div class="info-row__title">Aggregierte Nutzungsstatistik</div>
                    <div class="info-row__desc">
                        Zur Produktverbesserung erheben wir ausschließlich <strong>aggregierte, anonyme Statistiken</strong>. Keine personenbezogenen Daten, keine Weitergabe an Dritte, kein externes Tracking.
                    </div>
                </div>
            </div>
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(0,51,127,0.06);">
                <form method="POST" action="{{ route('einstellungen.data-export') }}" id="data-export-form"
                      onsubmit="return confirm('Datenexport jetzt anfordern? Du erhältst die CSV-Dateien als Anhang an {{ $user->email }}.\n\nLimit: max. 1× pro Woche und 2× pro Monat.');">
                    @csrf
                    <button type="submit" class="link-row" style="width:100%;background:none;border:0;text-align:left;font:inherit;cursor:pointer;">
                        <div>
                            <div class="link-row__title"><i class="bi bi-download" style="margin-right: 0.4rem;"></i> Datenexport anfordern (Art. 20 DSGVO)</div>
                            <div class="link-row__sub">CSV-Export deiner Lerndaten, Antworten und Profilfelder — per E-Mail an {{ $user->email }}. Max. 1× pro Woche, 2× pro Monat.</div>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </form>
                <div class="is-locked">
                    <div class="card-body-locked">
                        <a class="link-row" href="#" onclick="event.preventDefault()">
                            <div>
                                <div class="link-row__title"><i class="bi bi-shield-lock" style="margin-right: 0.4rem;"></i> Aktive Sessions & Geräte <span class="locked-badge" style="margin-left:0.5rem;"><i class="bi bi-lock"></i> Bald</span></div>
                                <div class="link-row__sub">Aktive Geräte und letzte Anmeldungen verwalten.</div>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sicherheit --}}
        <div class="glass card-security card">
            <div class="card-head">
                <span class="section-label">Sicherheit</span>
            </div>
            <form method="POST" action="{{ route('einstellungen.password.update') }}">
                @csrf
                @method('PATCH')
                <div class="field">
                    <label class="field__label" for="current_password">Aktuelles Passwort</label>
                    <input class="input @error('current_password') input--error @enderror" type="password" id="current_password" name="current_password" placeholder="Aktuelles Passwort">
                    @error('current_password')<div class="field__error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label class="field__label" for="password">Neues Passwort</label>
                    <input class="input @error('password') input--error @enderror" type="password" id="password" name="password" placeholder="Mindestens 8 Zeichen" oninput="checkPasswordMatch()">
                    @error('password')<div class="field__error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label class="field__label" for="password_confirmation">Passwort bestätigen</label>
                    <input class="input" type="password" id="password_confirmation" name="password_confirmation" placeholder="Passwort wiederholen" oninput="checkPasswordMatch()">
                    <div id="password-match-message" class="pf-pw-msg"></div>
                </div>
                <div class="toggle-row is-locked">
                    <div class="toggle-row__body card-body-locked">
                        <div class="toggle-row__title">
                            Zwei-Faktor-Authentifizierung (2FA)
                            <span class="locked-badge"><i class="bi bi-lock"></i> Bald</span>
                        </div>
                        <div class="toggle-row__desc">Zusätzlicher Schutz per TOTP-App (z. B. Aegis, Authy). Empfohlen für Admin-Accounts.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" disabled>
                        <span class="switch__slider"></span>
                    </label>
                </div>
                <div class="card-foot">
                    <span class="card-foot__note">Nach Passwortänderung bleibst du angemeldet.</span>
                    <button class="btn-pf-primary" type="submit">Passwort ändern</button>
                </div>
            </form>
        </div>

        {{-- Konto-Details --}}
        <div class="glass card-meta card">
            <div class="card-head">
                <span class="section-label">Konto-Details</span>
            </div>
            <div class="meta-grid">
                <div class="meta-item">
                    <div class="meta-item__label">Beitrittsdatum</div>
                    <div class="meta-item__value">{{ $user->created_at->format('d.m.Y') }}</div>
                    <div class="meta-item__sub">vor {{ $daysSince }} {{ $daysSince === 1 ? 'Tag' : 'Tagen' }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-item__label">Konto-Status</div>
                    @if($user->pending_email)
                        <div class="meta-item__value" style="color: #f59e0b;">E-Mail-Änderung</div>
                        <div class="meta-item__sub">Bestätigung an {{ $user->pending_email }}</div>
                    @elseif($user->hasVerifiedEmail())
                        <div class="meta-item__value" style="color: #22c55e;">Bestätigt</div>
                        <div class="meta-item__sub">E-Mail verifiziert</div>
                    @else
                        <div class="meta-item__value" style="color: #f59e0b;">Ausstehend</div>
                        <div class="meta-item__sub">E-Mail nicht verifiziert</div>
                    @endif
                </div>
                <div class="meta-item">
                    <div class="meta-item__label">Zuletzt aktiv</div>
                    <div class="meta-item__value">{{ $lastActive ? $lastActive->diffForHumans() : 'Gerade eben' }}</div>
                    <div class="meta-item__sub">{{ $lastActive ? $lastActive->format('d.m.Y H:i') : 'Erste Anmeldung' }}</div>
                </div>
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="card-danger">
            <div class="danger-card">
                <div class="danger-head">
                    <div class="danger-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div>
                        <span class="section-label">Danger Zone</span>
                        <div class="danger-title">Account permanent löschen</div>
                    </div>
                </div>
                <div class="danger-desc">
                    Diese Aktion kann <strong>nicht rückgängig</strong> gemacht werden. Alle deine Daten — Antworten, Fortschritt, Achievements — werden unwiderruflich gelöscht (Art. 17 DSGVO · Recht auf Löschung).
                </div>
                <form method="POST" action="{{ route('einstellungen.destroy') }}"
                      onsubmit="return confirm('Bist du dir absolut sicher? Alle deine Daten werden permanent gelöscht.')">
                    @csrf
                    @method('DELETE')
                    <div class="field">
                        <label class="field__label" for="password_delete">Passwort zur Bestätigung</label>
                        <input class="input @error('password', 'userDeletion') input--error @enderror" type="password"
                               id="password_delete" name="password" placeholder="Passwort eingeben" style="max-width: 320px;">
                        @error('password', 'userDeletion')<div class="field__error">{{ $message }}</div>@enderror
                    </div>
                    <div style="display: flex; gap: 0.75rem; align-items: center; margin-top: 1rem; flex-wrap: wrap;">
                        <button class="btn-danger-inline" type="submit"><i class="bi bi-trash3-fill"></i> Account permanent löschen</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    function checkPasswordMatch() {
        var pw = document.getElementById('password').value;
        var confirm = document.getElementById('password_confirmation').value;
        var msg = document.getElementById('password-match-message');
        if (pw && confirm) {
            var match = pw === confirm;
            document.getElementById('password').classList.toggle('input--error', !match);
            document.getElementById('password_confirmation').classList.toggle('input--error', !match);
            msg.style.display = 'flex';
            msg.style.color = match ? '#22c55e' : '#ef4444';
            msg.innerHTML = '<i class="bi bi-' + (match ? 'check' : 'x') + '-circle"></i> Passwörter stimmen ' + (match ? 'überein' : 'nicht überein');
        } else {
            document.getElementById('password').classList.remove('input--error');
            document.getElementById('password_confirmation').classList.remove('input--error');
            msg.style.display = 'none';
        }
    }

    (function () {
        var card = document.getElementById('notif-card');
        if (!card) return;
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var endpoint = '{{ route("einstellungen.notification-preferences") }}';
        var vapidPublicKey = @json(config('services.webpush.public_key'));

        function setRowSaving(input, saving) {
            var row = input.closest('.toggle-row');
            if (row) row.classList.toggle('is-saving', saving);
        }
        function showToast(msg, type) {
            var toast = document.createElement('div');
            var bg = type === 'error' ? '#ef4444' : '#22c55e';
            toast.style.cssText = 'position:fixed;bottom:1rem;right:1rem;background:' + bg + ';color:#fff;padding:0.75rem 1rem;border-radius:0.5rem;box-shadow:0 4px 12px rgba(0,0,0,0.2);z-index:9999;font-size:0.875rem;max-width:320px;';
            toast.textContent = msg;
            document.body.appendChild(toast);
            setTimeout(function () { toast.remove(); }, 3500);
        }
        async function patch(payload) {
            var res = await fetch(endpoint, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify(payload),
                cache: 'no-store',
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        }
        function syncSubToggles(channel, masterEnabled, forceCheckAll) {
            card.querySelectorAll('input[data-notif-channel="' + channel + '"]').forEach(function (input) {
                input.disabled = !masterEnabled;
                if (forceCheckAll && masterEnabled) input.checked = true;
            });
        }
        function updateConsentHint() {
            var hint = document.getElementById('notif-consent-hint');
            if (!hint) return;
            var emailMaster = card.querySelector('input[data-notif-master="email"]');
            var pushMaster = card.querySelector('input[data-notif-master="push"]');
            var parts = [];
            if (emailMaster && emailMaster.checked) parts.push('E-Mail aktiv');
            if (pushMaster && pushMaster.checked) parts.push('Push aktiv');
            hint.textContent = parts.length ? parts.join(' · ') : 'Noch nicht aktiviert';
        }
        function urlBase64ToUint8Array(base64String) {
            var padding = '='.repeat((4 - base64String.length % 4) % 4);
            var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            var raw = atob(base64);
            var arr = new Uint8Array(raw.length);
            for (var i = 0; i < raw.length; ++i) arr[i] = raw.charCodeAt(i);
            return arr;
        }
        async function enableBrowserPush() {
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) throw new Error('Push wird in diesem Browser nicht unterstützt.');
            if (!vapidPublicKey) throw new Error('Push-Konfiguration fehlt (VAPID).');
            var permission = await Notification.requestPermission();
            if (permission !== 'granted') throw new Error('Du hast Push-Benachrichtigungen im Browser nicht erlaubt.');
            var registration = await navigator.serviceWorker.register('/sw.js');
            await navigator.serviceWorker.ready;
            var subscription = await registration.pushManager.getSubscription();
            if (!subscription) {
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
                });
            }
            var sub = subscription.toJSON();
            await fetch('{{ route("push.subscribe") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ endpoint: sub.endpoint, keys: sub.keys, content_encoding: 'aes128gcm' }),
            });
        }
        async function disableBrowserPush() {
            if (!('serviceWorker' in navigator)) return;
            var registration = await navigator.serviceWorker.getRegistration();
            if (!registration) return;
            var subscription = await registration.pushManager.getSubscription();
            if (!subscription) return;
            var endpointUrl = subscription.endpoint;
            try { await subscription.unsubscribe(); } catch (e) { console.warn(e); }
            await fetch('{{ route("push.unsubscribe") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ endpoint: endpointUrl }),
            });
        }

        card.querySelectorAll('input[data-notif-master]').forEach(function (input) {
            input.addEventListener('change', async function () {
                var master = input.getAttribute('data-notif-master');
                var enabled = input.checked;
                setRowSaving(input, true);
                try {
                    if (master === 'push') {
                        if (enabled) await enableBrowserPush();
                        else await disableBrowserPush();
                    }
                    var data = await patch({ master: master, enabled: enabled });
                    if (!data.success) throw new Error(data.message || 'Speichern fehlgeschlagen');
                    syncSubToggles(master, enabled, enabled);
                    updateConsentHint();
                    showToast(master === 'email'
                        ? (enabled ? 'E-Mail-Benachrichtigungen aktiviert.' : 'E-Mail-Benachrichtigungen deaktiviert.')
                        : (enabled ? 'Push-Benachrichtigungen aktiviert.' : 'Push-Benachrichtigungen deaktiviert.'), 'success');
                } catch (err) {
                    console.error(err);
                    input.checked = !enabled;
                    showToast(err.message || 'Aktion fehlgeschlagen.', 'error');
                } finally {
                    setRowSaving(input, false);
                }
            });
        });

        card.querySelectorAll('input[data-notif-category]').forEach(function (input) {
            input.addEventListener('change', async function () {
                var channel = input.getAttribute('data-notif-channel');
                var category = input.getAttribute('data-notif-category');
                var enabled = input.checked;
                setRowSaving(input, true);
                try {
                    var data = await patch({ channel: channel, category: category, enabled: enabled });
                    if (!data.success) throw new Error(data.message || 'Speichern fehlgeschlagen');
                } catch (err) {
                    console.error(err);
                    input.checked = !enabled;
                    showToast(err.message || 'Speichern fehlgeschlagen.', 'error');
                } finally {
                    setRowSaving(input, false);
                }
            });
        });
    })();
</script>
@endsection
