@extends('layouts.app')
@section('title', 'Nutzerumfrage')

@section('content')
<div class="dash-container">
<div class="space-y-4">
    <header class="dashboard-header">
        <h1 class="page-title">Nutzer<span>umfrage</span></h1>
        <p class="page-subtitle">{{ $survey->description ?? 'Hilf uns, den THW Trainer zu verbessern!' }}</p>
    </header>

    @if ($existingResponse)
        {{-- Danke-Ansicht --}}
        <div class="glass-gold" style="padding: 2rem; border-radius: 1rem; text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">&#128588;</div>
            <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Vielen Dank!</h2>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Du hast an dieser Umfrage bereits teilgenommen. Dein Feedback hilft uns, den THW Trainer zu verbessern.</p>

            <form action="{{ route('umfrage.destroy', $existingResponse) }}" method="POST" onsubmit="return confirm('Moechtest du deine Antwort wirklich loeschen? Dies kann nicht rueckgaengig gemacht werden.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-ghost" style="font-size: 0.8125rem;">
                    Meine Antwort loeschen (DSGVO-Widerruf)
                </button>
            </form>
        </div>
    @else
        {{-- Wizard --}}
        <div x-data="surveyWizard()" x-cloak>
            {{-- Progress Bar --}}
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; padding: 0 0.25rem;">
                <template x-for="i in 4" :key="i">
                    <div style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                        <div :style="{
                            height: '4px',
                            flex: '1',
                            borderRadius: '2px',
                            background: i <= step ? 'linear-gradient(90deg, var(--gold-start), var(--gold-end))' : 'rgba(255,255,255,0.1)',
                            transition: 'background 0.3s ease'
                        }"></div>
                    </div>
                </template>
                <span style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap;" x-text="'Schritt ' + step + ' / 4'"></span>
            </div>

            <form action="{{ route('umfrage.store') }}" method="POST" @submit="handleSubmit($event)">
                @csrf

                {{-- Step 1: Bewertungen --}}
                <div class="glass" style="padding: 1.75rem; border-radius: 1rem;" x-show="step === 1" x-transition>
                    <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start); margin-bottom: 1.5rem;">
                        <h2 class="section-title">Bewertungen</h2>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 2rem;">
                        {{-- Overall --}}
                        <div>
                            <label style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem;">
                                Wie findest du den THW Trainer insgesamt? <span style="color: var(--gold-start);">*</span>
                            </label>
                            <div class="emoji-rating" data-field="rating_overall">
                                <template x-for="r in 5" :key="'overall-' + r">
                                    <button type="button"
                                        class="emoji-btn"
                                        :class="{ 'selected': ratings.overall === r }"
                                        @click="ratings.overall = r"
                                        :aria-label="'Bewertung ' + r + ' von 5'"
                                        x-text="emojis[r - 1]">
                                    </button>
                                </template>
                            </div>
                            <input type="hidden" name="rating_overall" :value="ratings.overall">
                        </div>

                        {{-- Usability --}}
                        <div>
                            <label style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem;">
                                Wie bewertest du die Benutzerfreundlichkeit? <span style="color: var(--gold-start);">*</span>
                            </label>
                            <div class="emoji-rating">
                                <template x-for="r in 5" :key="'usability-' + r">
                                    <button type="button"
                                        class="emoji-btn"
                                        :class="{ 'selected': ratings.usability === r }"
                                        @click="ratings.usability = r"
                                        x-text="emojis[r - 1]">
                                    </button>
                                </template>
                            </div>
                            <input type="hidden" name="rating_usability" :value="ratings.usability">
                        </div>

                        {{-- Design --}}
                        <div>
                            <label style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem;">
                                Wie bewertest du das Design? <span style="color: var(--gold-start);">*</span>
                            </label>
                            <div class="emoji-rating">
                                <template x-for="r in 5" :key="'design-' + r">
                                    <button type="button"
                                        class="emoji-btn"
                                        :class="{ 'selected': ratings.design === r }"
                                        @click="ratings.design = r"
                                        x-text="emojis[r - 1]">
                                    </button>
                                </template>
                            </div>
                            <input type="hidden" name="rating_design" :value="ratings.design">
                        </div>
                    </div>

                    @error('rating_overall') <p class="text-red-400 text-sm mt-2">{{ $message }}</p> @enderror
                    @error('rating_usability') <p class="text-red-400 text-sm mt-2">{{ $message }}</p> @enderror
                    @error('rating_design') <p class="text-red-400 text-sm mt-2">{{ $message }}</p> @enderror

                    <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                        <button type="button" class="btn-primary" @click="nextStep()" :disabled="!canAdvance1()">
                            Weiter
                        </button>
                    </div>
                </div>

                {{-- Step 2: Wie gefunden --}}
                <div class="glass" style="padding: 1.75rem; border-radius: 1rem;" x-show="step === 2" x-transition>
                    <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start); margin-bottom: 1.5rem;">
                        <h2 class="section-title">Wie hast du uns gefunden?</h2>
                    </div>

                    <label style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem;">
                        Wie bist du auf den THW Trainer aufmerksam geworden? <span style="color: var(--gold-start);">*</span>
                    </label>

                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        <template x-for="option in foundOptions" :key="option.value">
                            <button type="button"
                                class="chip-btn"
                                :class="{ 'selected': foundVia === option.value }"
                                @click="foundVia = option.value"
                                x-text="option.label">
                            </button>
                        </template>
                    </div>
                    <input type="hidden" name="found_via" :value="foundVia">

                    @error('found_via') <p class="text-red-400 text-sm mt-2">{{ $message }}</p> @enderror

                    <div style="display: flex; justify-content: space-between; margin-top: 1.5rem;">
                        <button type="button" class="btn-ghost" @click="prevStep()">Zurueck</button>
                        <button type="button" class="btn-primary" @click="nextStep()" :disabled="!canAdvance2()">
                            Weiter
                        </button>
                    </div>
                </div>

                {{-- Step 3: Feedback + Hermine --}}
                <div class="glass" style="padding: 1.75rem; border-radius: 1rem;" x-show="step === 3" x-transition>
                    <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start); margin-bottom: 1.5rem;">
                        <h2 class="section-title">Feedback & Wuensche</h2>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <div>
                            <label style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                                Allgemeines Feedback <span style="color: var(--text-muted); font-weight: 400; font-size: 0.8125rem;">(optional)</span>
                            </label>
                            <textarea name="feedback_general" class="textarea-glass" rows="3" maxlength="2000"
                                placeholder="Was moechtest du uns mitteilen?"></textarea>
                        </div>

                        <div>
                            <label style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                                Was wuenscht du dir noch? <span style="color: var(--text-muted); font-weight: 400; font-size: 0.8125rem;">(optional)</span>
                            </label>
                            <textarea name="feedback_wishes" class="textarea-glass" rows="3" maxlength="2000"
                                placeholder="Neue Features, Inhalte, Verbesserungen..."></textarea>
                        </div>

                        <div>
                            <label style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                                Was sollte geaendert werden? <span style="color: var(--text-muted); font-weight: 400; font-size: 0.8125rem;">(optional)</span>
                            </label>
                            <textarea name="feedback_changes" class="textarea-glass" rows="3" maxlength="2000"
                                placeholder="Was stoert dich oder sollte anders sein?"></textarea>
                        </div>

                        <div>
                            <label style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem;">
                                Wuerdest du einer Hermine-Gruppe beitreten? <span style="color: var(--gold-start);">*</span>
                            </label>
                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                <template x-for="option in hermineOptions" :key="option.value">
                                    <button type="button"
                                        class="chip-btn"
                                        :class="{ 'selected': hermineInterest === option.value }"
                                        @click="hermineInterest = option.value"
                                        x-text="option.label">
                                    </button>
                                </template>
                            </div>
                            <input type="hidden" name="hermine_interest" :value="hermineInterest">
                        </div>
                    </div>

                    @error('hermine_interest') <p class="text-red-400 text-sm mt-2">{{ $message }}</p> @enderror

                    <div style="display: flex; justify-content: space-between; margin-top: 1.5rem;">
                        <button type="button" class="btn-ghost" @click="prevStep()">Zurueck</button>
                        <button type="button" class="btn-primary" @click="nextStep()" :disabled="!canAdvance3()">
                            Weiter
                        </button>
                    </div>
                </div>

                {{-- Step 4: Datenschutz & Absenden --}}
                <div class="glass" style="padding: 1.75rem; border-radius: 1rem;" x-show="step === 4" x-transition>
                    <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start); margin-bottom: 1.5rem;">
                        <h2 class="section-title">Datenschutz & Absenden</h2>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <div>
                            <label style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem;">
                                Wie duerfen wir deine Antworten verwenden? <span style="color: var(--gold-start);">*</span>
                            </label>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <label class="radio-glass" :class="{ 'selected': publishMode === 'name' }">
                                    <input type="radio" name="publish_mode" value="name" x-model="publishMode" style="display: none;">
                                    <span class="radio-dot"></span>
                                    Mit meinem Namen als Testimonial veroeffentlichen
                                </label>
                                <label class="radio-glass" :class="{ 'selected': publishMode === 'anonymous' }">
                                    <input type="radio" name="publish_mode" value="anonymous" x-model="publishMode" style="display: none;">
                                    <span class="radio-dot"></span>
                                    Anonym als Testimonial veroeffentlichen
                                </label>
                                <label class="radio-glass" :class="{ 'selected': publishMode === 'private' }">
                                    <input type="radio" name="publish_mode" value="private" x-model="publishMode" style="display: none;">
                                    <span class="radio-dot"></span>
                                    Nur intern verwenden (nicht veroeffentlichen)
                                </label>
                            </div>
                        </div>

                        <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 0.75rem; padding: 1.25rem;">
                            <label style="display: flex; align-items: flex-start; gap: 0.75rem; cursor: pointer;">
                                <input type="checkbox" name="consent" x-model="consent"
                                    style="margin-top: 0.25rem; accent-color: var(--gold-start); width: 1.1rem; height: 1.1rem; flex-shrink: 0;">
                                <span style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5;">
                                    Ich stimme der Verarbeitung meiner Daten gemaess der
                                    <a href="{{ route('landing.datenschutz') }}" target="_blank" style="color: var(--gold-start); text-decoration: underline;">Datenschutzerklaerung</a>
                                    zu (Art. 6 Abs. 1 lit. a DSGVO). Ich kann meine Einwilligung jederzeit widerrufen, indem ich meine Antwort loesche.
                                    <span style="color: var(--gold-start);">*</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    @error('publish_mode') <p class="text-red-400 text-sm mt-2">{{ $message }}</p> @enderror
                    @error('consent') <p class="text-red-400 text-sm mt-2">{{ $message }}</p> @enderror

                    <div style="display: flex; justify-content: space-between; margin-top: 1.5rem;">
                        <button type="button" class="btn-ghost" @click="prevStep()">Zurueck</button>
                        <button type="submit" class="btn-primary" :disabled="!canSubmit()">
                            Umfrage absenden
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Flash Messages --}}
        @if (session('error'))
            <div class="glass-error" style="padding: 1rem; border-radius: 0.75rem; margin-top: 1rem;">
                <p style="color: #fca5a5; font-size: 0.875rem;">{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="glass-error" style="padding: 1rem; border-radius: 0.75rem; margin-top: 1rem;">
                <ul style="color: #fca5a5; font-size: 0.875rem; list-style: disc; padding-left: 1.25rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif

    @if (session('success'))
        <div class="glass-success" style="padding: 1rem; border-radius: 0.75rem; margin-top: 1rem;">
            <p style="color: #86efac; font-size: 0.875rem;">{{ session('success') }}</p>
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
