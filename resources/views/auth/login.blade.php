@extends('layouts.auth')

@section('title', 'Anmelden - THW Trainer')
@section('description', 'Melde dich bei THW-Trainer an und greife auf deinen persönlichen Lernfortschritt zu.')

@section('content')
<div class="auth-split">
    <!-- Left: Showcase -->
    <div class="auth-showcase" x-data="authShowcase()" x-init="start()">
        <div class="auth-showcase-inner">
            <div class="auth-showcase-brand">THW-Trainer</div>
            <h1 class="auth-showcase-headline">Lerne smarter.<br><span>Werde besser.</span></h1>
            <p class="auth-showcase-subtitle">Bereite dich optimal auf deine THW-Prüfung vor – mit intelligenten Lernmethoden und Fortschrittstracking.</p>

            <div class="auth-demo-carousel">
                <!-- Demo 1: Quiz -->
                <div class="auth-demo-panel" x-show="active === 0" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <div class="demo-quiz-card">
                        <div class="demo-quiz-badge">Grundausbildung</div>
                        <div class="demo-quiz-question" x-text="questions[currentQ].text"></div>
                        <div class="demo-quiz-options">
                            <template x-for="(answer, idx) in questions[currentQ].answers" :key="idx">
                                <div class="demo-quiz-option"
                                     :class="{
                                         'selected': quizStep >= 1 && idx === quizSelectedIdx,
                                         'correct': quizStep >= 2 && questions[currentQ].correctIdxs.includes(idx),
                                         'wrong': quizStep >= 2 && idx === quizSelectedIdx && !questions[currentQ].correctIdxs.includes(idx),
                                         'neutral': quizStep >= 2 && idx !== quizSelectedIdx && !questions[currentQ].correctIdxs.includes(idx)
                                     }">
                                    <span class="demo-letter" x-text="['A', 'B', 'C'][idx]"></span>
                                    <span x-text="answer"></span>
                                </div>
                            </template>
                        </div>
                        <div class="demo-quiz-result" :class="quizIsCorrect ? 'success' : 'error'" x-show="quizStep >= 2" x-transition>
                            <span x-text="quizIsCorrect ? 'Richtig beantwortet!' : 'Die richtige Antwort wurde markiert.'"></span>
                        </div>
                    </div>
                </div>

                <!-- Demo 2: Progress -->
                <div class="auth-demo-panel" x-show="active === 1" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
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
                <div class="auth-demo-panel" x-show="active === 2" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
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

            <!-- Demo indicators -->
            <div class="auth-demo-indicators">
                <div class="auth-demo-dot" :class="{ 'active': active === 0 }" @click="goTo(0)"></div>
                <div class="auth-demo-dot" :class="{ 'active': active === 1 }" @click="goTo(1)"></div>
                <div class="auth-demo-dot" :class="{ 'active': active === 2 }" @click="goTo(2)"></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="auth-showcase-footer">
            &copy; 2026 THW-Trainer
            <span class="auth-showcase-footer-divider">&middot;</span>
            <a href="{{ route('landing.datenschutz') }}">Datenschutz</a>
            <span class="auth-showcase-footer-divider">&middot;</span>
            <a href="{{ route('landing.impressum') }}">Impressum</a>
        </div>
    </div>

    <!-- Right: Login Form -->
    <div class="auth-form-panel">
        <div class="auth-form-card">
            <h2 class="auth-form-title">Willkommen zurueck</h2>
            <p class="auth-form-subtitle">Melde dich an, um auf deinen Lernfortschritt zuzugreifen.</p>

            @if ($errors->any())
                <div class="auth-error-box">
                    <strong>Anmeldung fehlgeschlagen</strong>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <x-auth-session-status class="auth-success-box" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="auth-field">
                    <label for="email" class="auth-label">E-Mail-Adresse</label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           class="auth-input"
                           placeholder="max@beispiel.de">
                </div>

                <div class="auth-field">
                    <label for="password" class="auth-label">Passwort</label>
                    <input id="password"
                           type="password"
                           name="password"
                           required
                           class="auth-input"
                           placeholder="••••••••">
                </div>

                <div class="auth-actions">
                    <label class="auth-checkbox-label">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Angemeldet bleiben</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="auth-forgot-link">Passwort vergessen?</a>
                </div>

                <button type="submit" class="auth-submit-btn">Anmelden</button>
            </form>

            <div class="auth-divider"><span>oder</span></div>

            <div class="auth-switch-link">
                Noch kein Konto? <a href="{{ route('register') }}">Jetzt registrieren</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('authShowcase', () => ({
        active: 0,
        interval: null,
        currentQ: 0,

        // Questions from database
        questions: @json($demoQuestions->map(fn($q) => [
            'text' => $q->frage,
            'answers' => [$q->antwort_a, $q->antwort_b, $q->antwort_c],
            'correctIdxs' => collect(explode(',', $q->loesung))->map(fn($l) => ['A' => 0, 'B' => 1, 'C' => 2][trim($l)] ?? 0)->values()->toArray(),
        ])->values()),

        // Quiz state
        quizStep: 0,
        quizSelectedIdx: -1,
        quizIsCorrect: false,

        // Progress state
        progressAnswered: 0,
        progressCorrectPct: 0,
        progressXP: 0,
        progressPct: 0,
        floatingXPs: [],
        xpCounter: 0,

        // Stats state
        statUsers: 0,
        statQuestions: 0,
        statFree: 0,

        start() {
            if (this.questions.length === 0) return;
            this.runDemo(0);
            this.interval = setInterval(() => {
                this.active = (this.active + 1) % 3;
                this.resetAll();
                this.$nextTick(() => this.runDemo(this.active));
            }, 7000);
        },

        goTo(idx) {
            clearInterval(this.interval);
            this.active = idx;
            this.resetAll();
            this.$nextTick(() => this.runDemo(idx));
            this.interval = setInterval(() => {
                this.active = (this.active + 1) % 3;
                this.resetAll();
                this.$nextTick(() => this.runDemo(this.active));
            }, 7000);
        },

        resetAll() {
            this.quizStep = 0;
            this.quizSelectedIdx = -1;
            this.quizIsCorrect = false;
            this.progressAnswered = 0;
            this.progressCorrectPct = 0;
            this.progressXP = 0;
            this.progressPct = 0;
            this.floatingXPs = [];
            this.statUsers = 0;
            this.statQuestions = 0;
            this.statFree = 0;
        },

        runDemo(idx) {
            if (idx === 0) this.runQuiz();
            if (idx === 1) this.runProgress();
            if (idx === 2) this.runStats();
        },

        runQuiz() {
            const q = this.questions[this.currentQ];
            // Pick a correct answer to "select"
            this.quizSelectedIdx = q.correctIdxs[0];
            this.quizIsCorrect = true;

            setTimeout(() => { this.quizStep = 1; }, 1200);
            setTimeout(() => {
                this.quizStep = 2;
                // Advance to next question for next cycle
                this.currentQ = (this.currentQ + 1) % this.questions.length;
            }, 3000);
        },

        runProgress() {
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
            this.animateValue('statUsers', 0, 200, 2000, v => v + '+');
            this.animateValue('statQuestions', 0, 1000, 2000, v => v.toLocaleString('de-DE') + '+');
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
