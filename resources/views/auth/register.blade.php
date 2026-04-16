@extends('layouts.auth')

@section('title', 'Registrierung - THW Trainer')
@section('description', 'Erstelle deinen kostenlosen THW-Trainer Account und starte sofort mit dem Lernen. Verfolge deinen Fortschritt und bereite dich optimal auf deine THW-Prüfung vor.')

@section('content')
<div class="auth-split">
    <!-- Left: Showcase -->
    <div class="auth-showcase" x-data="authShowcase()" x-init="start()">
        <div class="auth-showcase-inner">
            <div class="auth-showcase-brand">THW-Trainer</div>
            <h1 class="auth-showcase-headline">Starte jetzt.<br><span>Lerne effektiv.</span></h1>
            <p class="auth-showcase-subtitle">Kostenlos registrieren und sofort mit dem Lernen beginnen. Dein Fortschritt wird gespeichert.</p>

            <div class="auth-demo-carousel">
                <!-- Demo 1: Quiz -->
                <div class="auth-demo-panel" x-show="active === 0"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     style="position: absolute; inset: 0;">
                    <div class="demo-quiz-card">
                        <div class="demo-quiz-badge">Grundausbildung</div>
                        <div class="demo-quiz-question" x-text="questions[displayQ].text"></div>
                        <div class="demo-quiz-options">
                            <template x-for="(answer, idx) in questions[displayQ].answers" :key="displayQ + '-' + idx">
                                <div class="demo-quiz-option"
                                     :class="{
                                         'selected': quizStep >= 1 && idx === quizSelectedIdx,
                                         'correct': quizStep >= 2 && questions[displayQ].correctIdxs.includes(idx),
                                         'wrong': quizStep >= 2 && idx === quizSelectedIdx && !questions[displayQ].correctIdxs.includes(idx),
                                         'neutral': quizStep >= 2 && idx !== quizSelectedIdx && !questions[displayQ].correctIdxs.includes(idx)
                                     }">
                                    <span class="demo-letter" x-text="['A', 'B', 'C'][idx]"></span>
                                    <span x-text="answer"></span>
                                </div>
                            </template>
                        </div>
                        <div class="demo-quiz-result success" x-show="quizStep >= 2" x-transition>Richtig beantwortet!</div>
                    </div>
                </div>

                <!-- Demo 2: Progress -->
                <div class="auth-demo-panel" x-show="active === 1"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     style="position: absolute; inset: 0;">
                    <div class="demo-progress-card" style="position: relative;">
                        <div class="demo-progress-header">
                            <div class="demo-progress-title">Dein Lernfortschritt</div>
                            <div class="demo-streak-badge">
                                <span>5 Tage Streak</span>
                            </div>
                        </div>
                        <div class="demo-progress-stats">
                            <div class="demo-stat-item">
                                <div class="demo-stat-value text-blue" x-text="progressAnswered">0</div>
                                <div class="demo-stat-label">Beantwortet</div>
                            </div>
                            <div class="demo-stat-item">
                                <div class="demo-stat-value text-green" x-text="progressCorrectPct + '%'">0%</div>
                                <div class="demo-stat-label">Richtig</div>
                            </div>
                            <div class="demo-stat-item">
                                <div class="demo-stat-value text-blue" x-text="progressXP">0</div>
                                <div class="demo-stat-label">XP</div>
                            </div>
                        </div>
                        <div class="demo-progress-bar-wrap">
                            <div class="demo-progress-bar-label">
                                <span>Grundausbildung</span>
                                <span x-text="progressPct + '%'">0%</span>
                            </div>
                            <div class="demo-progress-track">
                                <div class="demo-progress-fill" :style="'width: ' + progressPct + '%'"></div>
                            </div>
                        </div>
                        <template x-for="xp in floatingXPs" :key="xp.id">
                            <div class="demo-xp-float" :style="'top: ' + xp.top + 'px'" x-text="'+' + xp.value + ' XP'"></div>
                        </template>
                    </div>
                </div>

                <!-- Demo 3: Stats -->
                <div class="auth-demo-panel" x-show="active === 2"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     style="position: absolute; inset: 0;">
                    <div class="demo-stats-grid">
                        <div class="demo-stats-card">
                            <div class="demo-stats-number blue" x-text="statUsers">0</div>
                            <div class="demo-stats-label">Aktive User</div>
                        </div>
                        <div class="demo-stats-card">
                            <div class="demo-stats-number blue" x-text="statQuestions">0</div>
                            <div class="demo-stats-label">Fragen</div>
                        </div>
                        <div class="demo-stats-card wide">
                            <div class="demo-stats-number green" x-text="statFree">0%</div>
                            <div class="demo-stats-label">Kostenlos nutzbar</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="auth-demo-indicators">
                <div class="auth-demo-dot" :class="{ 'active': active === 0 }" @click="goTo(0)"></div>
                <div class="auth-demo-dot" :class="{ 'active': active === 1 }" @click="goTo(1)"></div>
                <div class="auth-demo-dot" :class="{ 'active': active === 2 }" @click="goTo(2)"></div>
            </div>
        </div>

        <div class="auth-showcase-footer">
            &copy; 2026 THW-Trainer
            <span class="auth-showcase-footer-divider">&middot;</span>
            <a href="{{ route('landing.datenschutz') }}">Datenschutz</a>
            <span class="auth-showcase-footer-divider">&middot;</span>
            <a href="{{ route('landing.impressum') }}">Impressum</a>
        </div>
    </div>

    <!-- Right: Register Form -->
    <div class="auth-form-panel">
        <div class="auth-form-card">
            <a href="{{ url('/') }}" class="auth-form-brand">THW-Trainer</a>
            <h2 class="auth-form-title">Konto erstellen</h2>
            <p class="auth-form-subtitle">Starte jetzt mit dem THW-Trainer.</p>

            @php
            $inviteCode = request('code') ?? request('invite');
            $inviteInfo = null;
            if ($inviteCode) {
                $inviteInfo = \App\Models\OrtsverbandInvitation::where('code', $inviteCode)->with('ortsverband', 'creator')->first();
            }
            @endphp

            @if($inviteInfo && $inviteInfo->isValid())
            <div class="auth-invite-banner">
                <strong>Einladung:</strong> {{ $inviteInfo->ortsverband->name }}
                <span style="opacity: 0.7; font-size: 0.75rem;">&middot; Du trittst automatisch bei</span>
            </div>
            @endif

            @if(session('error'))
            <div class="auth-error-box">
                <strong>{{ session('error') }}</strong>
            </div>
            @endif

            @if ($errors->any())
                <div class="auth-error-box">
                    <strong>Fehler bei der Registrierung</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="auth-field">
                    <label for="name" class="auth-label">Vollständiger Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="auth-input" placeholder="Max Mustermann">
                </div>

                <div class="auth-field">
                    <label for="email" class="auth-label">E-Mail-Adresse</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required class="auth-input" placeholder="max@beispiel.de">
                </div>

                <div class="auth-row">
                    <div class="auth-field">
                        <label for="password" class="auth-label">Passwort</label>
                        <input id="password" type="password" name="password" required class="auth-input" placeholder="Mind. 8 Zeichen">
                    </div>
                    <div class="auth-field">
                        <label for="password_confirmation" class="auth-label">Bestätigen</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required class="auth-input" placeholder="Wiederholen">
                    </div>
                </div>

                @if(!($inviteInfo && $inviteInfo->isValid()))
                <div class="auth-field">
                    <label for="invitation_code" class="auth-label">OV-Code (optional)</label>
                    <input id="invitation_code" type="text" name="invitation_code" value="{{ old('invitation_code', request('code') ?? request('invite') ?? '') }}" class="auth-input" placeholder="z.B. THW-XXXXXXXX">
                    <p class="auth-helper">Hast du einen Einladungscode von deinem Ortsverband erhalten?</p>
                </div>
                @else
                <input type="hidden" name="invitation_code" value="{{ $inviteInfo->code }}">
                @endif

                <div class="auth-consent-box">
                    <div class="auth-consent-inner">
                        <input type="checkbox" name="email_consent" id="email_consent" value="1" {{ old('email_consent') ? 'checked' : '' }}>
                        <div>
                            <label for="email_consent" class="auth-consent-label">E-Mail-Benachrichtigungen</label>
                            <p class="auth-consent-desc">Erhalte Mails zu deinem Lernfortschritt und neuen Features</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="auth-submit-btn">Account erstellen</button>
            </form>

            <a href="{{ route('landing.guest.practice.menu') }}" class="auth-ghost-btn">Als Gast üben</a>

            <div class="auth-divider"><span>oder</span></div>

            <div class="auth-switch-link">
                Bereits registriert? <a href="{{ route('login') }}">Jetzt anmelden</a>
            </div>

            @if(!app()->isProduction())
            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(239,68,68,0.2); text-align: center;">
                <span style="display: block; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.08em; color: rgba(239,68,68,0.5); text-transform: uppercase; margin-bottom: 0.5rem;">Dev-Tools</span>
                <button type="button" onclick="document.cookie.split(';').forEach(c => document.cookie = c.trim().replace(/=.*/, '=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/')); location.reload();"
                    style="font-size: 0.78rem; padding: 0.35rem 0.9rem; background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.25); color: rgba(239,68,68,0.7); border-radius: 6px; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.background='rgba(239,68,68,0.15)'; this.style.color='rgba(239,68,68,0.95)'"
                    onmouseout="this.style.background='rgba(239,68,68,0.08)'; this.style.color='rgba(239,68,68,0.7)'">
                    Cookies leeren &amp; neu laden
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('authShowcase', () => ({
        active: 0,
        interval: null,
        currentQ: 0,
        displayQ: 0,
        questions: @json($demoQuestions),
        dbUsers: {{ $authStats['users'] }},
        dbQuestions: {{ $authStats['questions'] }},

        quizStep: 0,
        quizSelectedIdx: -1,

        progressAnswered: 0,
        progressCorrectPct: 0,
        progressXP: 0,
        progressPct: 0,
        floatingXPs: [],
        xpCounter: 0,

        statUsers: 0,
        statQuestions: 0,
        statFree: 0,

        start() {
            if (this.questions.length === 0) return;
            this.runDemo(0);
            this.interval = setInterval(() => {
                const next = (this.active + 1) % 3;
                this.active = next;
                this.$nextTick(() => this.runDemo(next));
            }, 7000);
        },

        goTo(idx) {
            clearInterval(this.interval);
            this.active = idx;
            this.$nextTick(() => this.runDemo(idx));
            this.interval = setInterval(() => {
                const next = (this.active + 1) % 3;
                this.active = next;
                this.$nextTick(() => this.runDemo(next));
            }, 7000);
        },

        runDemo(idx) {
            if (idx === 0) this.runQuiz();
            if (idx === 1) this.runProgress();
            if (idx === 2) this.runStats();
        },

        runQuiz() {
            this.displayQ = this.currentQ;
            this.quizStep = 0;
            this.quizSelectedIdx = this.questions[this.displayQ].correctIdxs[0];

            setTimeout(() => { this.quizStep = 1; }, 1200);
            setTimeout(() => { this.quizStep = 2; }, 3000);
        },

        runProgress() {
            this.progressAnswered = 0;
            this.progressCorrectPct = 0;
            this.progressXP = 0;
            this.progressPct = 0;
            this.floatingXPs = [];
            this.animateValue('progressAnswered', 0, 147, 1800);
            this.animateValue('progressCorrectPct', 0, 82, 1800);
            this.animateValue('progressXP', 0, 1240, 2000);
            this.animateValue('progressPct', 0, 68, 2000);

            setTimeout(() => this.addXP(25, 60), 800);
            setTimeout(() => this.addXP(10, 30), 1600);
            setTimeout(() => this.addXP(50, 80), 2800);
        },

        addXP(value, top) {
            const id = ++this.xpCounter;
            this.floatingXPs.push({ id, value, top });
            setTimeout(() => {
                this.floatingXPs = this.floatingXPs.filter(x => x.id !== id);
            }, 1500);
        },

        runStats() {
            this.statUsers = 0;
            this.statQuestions = 0;
            this.statFree = 0;
            this.currentQ = (this.currentQ + 1) % this.questions.length;
            this.animateValue('statUsers', 0, this.dbUsers, 2000, v => v.toLocaleString('de-DE') + '+');
            this.animateValue('statQuestions', 0, this.dbQuestions, 2000, v => v.toLocaleString('de-DE') + '+');
            this.animateValue('statFree', 0, 100, 1800);
        },

        animateValue(prop, from, to, duration, format) {
            const start = performance.now();
            const step = (now) => {
                const elapsed = now - start;
                const progress = Math.min(elapsed / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const raw = Math.round(from + (to - from) * eased);
                this[prop] = format ? format(raw) : raw;
                if (progress < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        }
    }));
});
</script>
@endsection
