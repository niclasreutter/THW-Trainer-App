@extends('layouts.app')
@section('title', 'Nutzerumfrage')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Progress Steps ────────────────────────── */
    .survey-progress {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .survey-progress__segment {
        flex: 1;
        height: 3px;
        border-radius: 2px;
        background: rgba(255,255,255,0.08);
        transition: background 0.4s ease;
    }

    .survey-progress__segment.active {
        background: linear-gradient(90deg, var(--gold-start), var(--gold-end));
    }

    .survey-progress__label {
        font-size: 0.625rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        font-weight: 600;
        font-family: 'IBM Plex Mono', monospace;
        white-space: nowrap;
        margin-left: 0.5rem;
    }

    /* ─── Step Card ─────────────────────────────── */
    .survey-step {
        padding: 1.25rem 1.5rem;
    }

    .survey-step__title {
        font-size: 0.625rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(255,255,255,0.4);
        font-weight: 700;
        font-family: 'IBM Plex Mono', monospace;
        margin-bottom: 0.75rem;
    }

    .survey-step__question {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.625rem;
        font-family: 'Barlow Condensed', sans-serif;
        letter-spacing: 0.01em;
    }

    .survey-step__question .required {
        color: var(--gold-start);
        font-weight: 400;
    }

    .survey-step__optional {
        color: var(--text-muted);
        font-weight: 400;
        font-size: 0.75rem;
        font-family: -apple-system, sans-serif;
    }

    /* ─── Emoji Rating (compact) ────────────────── */
    .survey-emoji-row {
        display: flex;
        gap: 0.375rem;
    }

    .survey-emoji-row .emoji-btn {
        font-size: 1.5rem;
        padding: 0.35rem 0.45rem;
        border-radius: 0.625rem;
    }

    /* ─── Rating Group (compact, horizontal on desktop) */
    .survey-ratings {
        display: flex;
        flex-direction: column;
        gap: 0.875rem;
    }

    .survey-rating-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .survey-rating-item__label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-primary);
        flex-shrink: 0;
    }

    @media (max-width: 480px) {
        .survey-rating-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.375rem;
        }
    }

    /* ─── Chips (compact) ───────────────────────── */
    .survey-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.375rem;
    }

    .survey-chips .chip-btn {
        padding: 0.375rem 0.875rem;
        font-size: 0.8125rem;
    }

    /* ─── Radio Options (compact) ───────────────── */
    .survey-radios {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }

    .survey-radios .radio-glass {
        padding: 0.625rem 0.875rem;
        font-size: 0.8125rem;
    }

    /* ─── Consent Box ───────────────────────────── */
    .survey-consent {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 0.625rem;
        padding: 0.875rem;
    }

    .survey-consent label {
        display: flex;
        align-items: flex-start;
        gap: 0.625rem;
        cursor: pointer;
    }

    .survey-consent input[type="checkbox"] {
        margin-top: 0.2rem;
        accent-color: var(--gold-start);
        width: 1rem;
        height: 1rem;
        flex-shrink: 0;
    }

    .survey-consent span {
        font-size: 0.8125rem;
        color: var(--text-muted);
        line-height: 1.45;
    }

    .survey-consent a {
        color: var(--gold-start);
        text-decoration: underline;
    }

    /* ─── Step Nav ──────────────────────────────── */
    .survey-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.25rem;
        padding-top: 0.875rem;
        border-top: 1px solid rgba(255,255,255,0.06);
    }

    /* ─── Thank You ─────────────────────────────── */
    .survey-thanks {
        text-align: center;
        padding: 2.5rem 1.5rem;
    }

    .survey-thanks__icon {
        font-size: 2.5rem;
        color: var(--gold-start);
        margin-bottom: 0.75rem;
    }

    .survey-thanks__title {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-primary);
        font-family: 'Barlow Condensed', sans-serif;
        margin-bottom: 0.35rem;
    }

    .survey-thanks__text {
        color: var(--text-muted);
        font-size: 0.8125rem;
        margin-bottom: 1.25rem;
        max-width: 28rem;
        margin-left: auto;
        margin-right: auto;
    }

    /* ─── Feedback Textareas (Step 3) ───────────── */
    .survey-feedback-group {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .survey-feedback-group .textarea-glass {
        min-height: 70px;
    }

    .survey-feedback-group label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.35rem;
    }

    /* ─── Stagger Animation ─────────────────────── */
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

    /* ─── Step transition ───────────────────────── */
    @keyframes step-in {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .survey-step-enter {
        animation: step-in 0.3s ease both;
    }
</style>
@endpush

@section('content')
<div class="dash-container">
<div class="space-y-4">

    {{-- Header --}}
    <div class="mb-2">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;font-family:'IBM Plex Mono',monospace;">Feedback</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Nutzerumfrage</h1>
        <p class="text-sm" style="color:var(--text-muted);">{{ $survey->description ?? 'Hilf uns, den THW Trainer zu verbessern!' }}</p>
    </div>

    @if ($existingResponse)
        {{-- Danke-Ansicht --}}
        <div class="glass-gold survey-thanks">
            <div class="survey-thanks__icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="survey-thanks__title">Vielen Dank!</div>
            <p class="survey-thanks__text">Du hast an dieser Umfrage bereits teilgenommen. Dein Feedback hilft uns, den THW Trainer zu verbessern.</p>

            <form action="{{ route('umfrage.destroy', $existingResponse) }}" method="POST" onsubmit="return confirm('Moechtest du deine Antwort wirklich loeschen? Dies kann nicht rueckgaengig gemacht werden.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-ghost" style="font-size:0.75rem;">Meine Antwort loeschen (DSGVO-Widerruf)</button>
            </form>
        </div>
    @else
        {{-- Progress --}}
        <div x-data="surveyWizard()" x-cloak>
            <div class="survey-progress" style="margin-bottom:0.75rem;">
                <template x-for="i in 4" :key="i">
                    <div class="survey-progress__segment" :class="{ 'active': i <= step }"></div>
                </template>
                <span class="survey-progress__label" x-text="step + '/4'"></span>
            </div>

            <form action="{{ route('umfrage.store') }}" method="POST" @submit="handleSubmit($event)">
                @csrf

                {{-- Step 1: Bewertungen --}}
                <div class="glass survey-step" x-show="step === 1" x-transition:enter="survey-step-enter">
                    <div class="survey-step__title">Schritt 1 — Bewertungen</div>

                    <div class="survey-ratings">
                        <div class="survey-rating-item">
                            <span class="survey-rating-item__label">Gesamt <span class="required">*</span></span>
                            <div class="survey-emoji-row">
                                <template x-for="r in 5" :key="'overall-' + r">
                                    <button type="button" class="emoji-btn" :class="{ 'selected': ratings.overall === r }" @click="ratings.overall = r" :aria-label="'Bewertung ' + r + ' von 5'" x-text="emojis[r - 1]"></button>
                                </template>
                            </div>
                            <input type="hidden" name="rating_overall" :value="ratings.overall">
                        </div>

                        <div class="survey-rating-item">
                            <span class="survey-rating-item__label">Bedienbarkeit <span class="required">*</span></span>
                            <div class="survey-emoji-row">
                                <template x-for="r in 5" :key="'usability-' + r">
                                    <button type="button" class="emoji-btn" :class="{ 'selected': ratings.usability === r }" @click="ratings.usability = r" x-text="emojis[r - 1]"></button>
                                </template>
                            </div>
                            <input type="hidden" name="rating_usability" :value="ratings.usability">
                        </div>

                        <div class="survey-rating-item">
                            <span class="survey-rating-item__label">Design <span class="required">*</span></span>
                            <div class="survey-emoji-row">
                                <template x-for="r in 5" :key="'design-' + r">
                                    <button type="button" class="emoji-btn" :class="{ 'selected': ratings.design === r }" @click="ratings.design = r" x-text="emojis[r - 1]"></button>
                                </template>
                            </div>
                            <input type="hidden" name="rating_design" :value="ratings.design">
                        </div>
                    </div>

                    @error('rating_overall') <p style="color:#fca5a5;font-size:0.75rem;margin-top:0.5rem;">{{ $message }}</p> @enderror
                    @error('rating_usability') <p style="color:#fca5a5;font-size:0.75rem;">{{ $message }}</p> @enderror
                    @error('rating_design') <p style="color:#fca5a5;font-size:0.75rem;">{{ $message }}</p> @enderror

                    <div class="survey-nav">
                        <span></span>
                        <button type="button" class="btn-primary" @click="nextStep()" :disabled="!canAdvance1()">Weiter</button>
                    </div>
                </div>

                {{-- Step 2: Wie gefunden --}}
                <div class="glass survey-step" x-show="step === 2" x-transition:enter="survey-step-enter">
                    <div class="survey-step__title">Schritt 2 — Herkunft</div>

                    <div class="survey-step__question">Wie bist du auf den THW Trainer aufmerksam geworden? <span class="required">*</span></div>

                    <div class="survey-chips">
                        <template x-for="option in foundOptions" :key="option.value">
                            <button type="button" class="chip-btn" :class="{ 'selected': foundVia === option.value }" @click="foundVia = option.value" x-text="option.label"></button>
                        </template>
                    </div>
                    <input type="hidden" name="found_via" :value="foundVia">

                    @error('found_via') <p style="color:#fca5a5;font-size:0.75rem;margin-top:0.5rem;">{{ $message }}</p> @enderror

                    <div class="survey-nav">
                        <button type="button" class="btn-ghost" @click="prevStep()">Zurueck</button>
                        <button type="button" class="btn-primary" @click="nextStep()" :disabled="!canAdvance2()">Weiter</button>
                    </div>
                </div>

                {{-- Step 3: Feedback + Hermine --}}
                <div class="glass survey-step" x-show="step === 3" x-transition:enter="survey-step-enter">
                    <div class="survey-step__title">Schritt 3 — Feedback & Wuensche</div>

                    <div class="survey-feedback-group">
                        <div>
                            <label>Allgemeines Feedback <span class="survey-step__optional">(optional)</span></label>
                            <textarea name="feedback_general" class="textarea-glass" rows="2" maxlength="2000" placeholder="Was moechtest du uns mitteilen?"></textarea>
                        </div>

                        <div>
                            <label>Was wuenscht du dir noch? <span class="survey-step__optional">(optional)</span></label>
                            <textarea name="feedback_wishes" class="textarea-glass" rows="2" maxlength="2000" placeholder="Neue Features, Inhalte, Verbesserungen..."></textarea>
                        </div>

                        <div>
                            <label>Was sollte geaendert werden? <span class="survey-step__optional">(optional)</span></label>
                            <textarea name="feedback_changes" class="textarea-glass" rows="2" maxlength="2000" placeholder="Was stoert dich oder sollte anders sein?"></textarea>
                        </div>

                        <div>
                            <div class="survey-step__question" style="margin-top:0.25rem;">Wuerdest du einer Hermine-Gruppe beitreten? <span class="required">*</span></div>
                            <div class="survey-chips">
                                <template x-for="option in hermineOptions" :key="option.value">
                                    <button type="button" class="chip-btn" :class="{ 'selected': hermineInterest === option.value }" @click="hermineInterest = option.value" x-text="option.label"></button>
                                </template>
                            </div>
                            <input type="hidden" name="hermine_interest" :value="hermineInterest">
                        </div>
                    </div>

                    @error('hermine_interest') <p style="color:#fca5a5;font-size:0.75rem;margin-top:0.5rem;">{{ $message }}</p> @enderror

                    <div class="survey-nav">
                        <button type="button" class="btn-ghost" @click="prevStep()">Zurueck</button>
                        <button type="button" class="btn-primary" @click="nextStep()" :disabled="!canAdvance3()">Weiter</button>
                    </div>
                </div>

                {{-- Step 4: Datenschutz & Absenden --}}
                <div class="glass survey-step" x-show="step === 4" x-transition:enter="survey-step-enter">
                    <div class="survey-step__title">Schritt 4 — Datenschutz</div>

                    <div class="survey-step__question">Wie duerfen wir deine Antworten verwenden? <span class="required">*</span></div>

                    <div class="survey-radios">
                        <label class="radio-glass" :class="{ 'selected': publishMode === 'name' }">
                            <input type="radio" name="publish_mode" value="name" x-model="publishMode" style="display:none;">
                            <span class="radio-dot"></span>
                            Mit meinem Namen als Testimonial veroeffentlichen
                        </label>
                        <label class="radio-glass" :class="{ 'selected': publishMode === 'anonymous' }">
                            <input type="radio" name="publish_mode" value="anonymous" x-model="publishMode" style="display:none;">
                            <span class="radio-dot"></span>
                            Anonym als Testimonial veroeffentlichen
                        </label>
                        <label class="radio-glass" :class="{ 'selected': publishMode === 'private' }">
                            <input type="radio" name="publish_mode" value="private" x-model="publishMode" style="display:none;">
                            <span class="radio-dot"></span>
                            Nur intern verwenden (nicht veroeffentlichen)
                        </label>
                    </div>

                    <div class="survey-consent" style="margin-top:0.75rem;">
                        <label>
                            <input type="checkbox" name="consent" x-model="consent">
                            <span>
                                Ich stimme der Verarbeitung meiner Daten gemaess der
                                <a href="{{ route('landing.datenschutz') }}" target="_blank">Datenschutzerklaerung</a>
                                zu (Art. 6 Abs. 1 lit. a DSGVO). Ich kann meine Einwilligung jederzeit widerrufen, indem ich meine Antwort loesche.
                                <span class="required">*</span>
                            </span>
                        </label>
                    </div>

                    @error('publish_mode') <p style="color:#fca5a5;font-size:0.75rem;margin-top:0.5rem;">{{ $message }}</p> @enderror
                    @error('consent') <p style="color:#fca5a5;font-size:0.75rem;">{{ $message }}</p> @enderror

                    <div class="survey-nav">
                        <button type="button" class="btn-ghost" @click="prevStep()">Zurueck</button>
                        <button type="submit" class="btn-primary" :disabled="!canSubmit()">Umfrage absenden</button>
                    </div>
                </div>
            </form>
        </div>

        @if (session('error'))
            <div class="glass-error" style="padding:0.75rem 1rem;border-radius:0.625rem;">
                <p style="color:#fca5a5;font-size:0.8125rem;">{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="glass-error" style="padding:0.75rem 1rem;border-radius:0.625rem;">
                <ul style="color:#fca5a5;font-size:0.8125rem;list-style:disc;padding-left:1.25rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif

    @if (session('success'))
        <div class="glass-success" style="padding:0.75rem 1rem;border-radius:0.625rem;">
            <p style="color:#86efac;font-size:0.8125rem;">{{ session('success') }}</p>
        </div>
    @endif

</div>
</div>

@push('scripts')
<script>
function surveyWizard() {
    return {
        step: 1,
        emojis: ['\u{1F621}', '\u{1F61F}', '\u{1F610}', '\u{1F60A}', '\u{1F60D}'],
        ratings: { overall: 0, usability: 0, design: 0 },
        foundVia: '',
        hermineInterest: '',
        publishMode: '',
        consent: false,

        foundOptions: [
            { value: 'empfehlung', label: 'Empfehlung' },
            { value: 'google', label: 'Google-Suche' },
            { value: 'social_media', label: 'Social Media' },
            { value: 'thw_ausbildung', label: 'THW Ausbildung' },
            { value: 'sonstiges', label: 'Sonstiges' },
        ],

        hermineOptions: [
            { value: 'ja', label: 'Ja' },
            { value: 'nein', label: 'Nein' },
            { value: 'unknown', label: 'Kenne ich nicht' },
        ],

        canAdvance1() {
            return this.ratings.overall > 0 && this.ratings.usability > 0 && this.ratings.design > 0;
        },
        canAdvance2() {
            return this.foundVia !== '';
        },
        canAdvance3() {
            return this.hermineInterest !== '';
        },
        canSubmit() {
            return this.publishMode !== '' && this.consent;
        },

        nextStep() {
            if (this.step < 4) this.step++;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        prevStep() {
            if (this.step > 1) this.step--;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        handleSubmit(e) {
            if (!this.canSubmit()) {
                e.preventDefault();
            }
        }
    }
}
</script>
@endpush
@endsection
