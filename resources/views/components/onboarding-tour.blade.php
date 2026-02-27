{{--
    Onboarding-Tour Komponente
    Zeigt eine schrittweise Tooltip-Tour für neue Nutzer an.
    Voraussetzung: Elemente mit data-tour-step="X" im Dashboard.
--}}
@if(auth()->check() && !auth()->user()->onboarding_tour_completed)
<div x-data="onboardingTour()" x-show="showTour" x-cloak
     style="position: fixed; inset: 0; z-index: 10003; pointer-events: none;"
     @keydown.escape.window="skipTour()">

    {{-- Overlay --}}
    <div class="tour-overlay" x-show="showTour"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
    </div>

    {{-- Spotlight glow ring around highlighted element --}}
    <div class="tour-spotlight"
         x-show="spotlightStyle.width"
         :style="`top:${spotlightStyle.top}px; left:${spotlightStyle.left}px; width:${spotlightStyle.width}px; height:${spotlightStyle.height}px;`">
    </div>

    {{-- Tooltip --}}
    <div class="tour-tooltip glass"
         x-show="showTour"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         :style="`top:${tooltipPosition.top}px; left:${tooltipPosition.left}px;`"
         @click.stop>

        {{-- Progress dots --}}
        <div class="tour-progress">
            <template x-for="(s, i) in steps" :key="i">
                <span class="tour-dot" :class="{ 'active': i === currentStep, 'done': i < currentStep }"></span>
            </template>
        </div>

        {{-- Content --}}
        <div class="tour-content">
            <h3 class="tour-title" x-text="getStepTitle(steps[currentStep])"></h3>
            <p class="tour-desc" x-text="getStepDescription(steps[currentStep])"></p>
        </div>

        {{-- Actions --}}
        <div class="tour-actions">
            <button class="tour-skip" @click="skipTour()" x-text="currentStep === steps.length - 1 ? 'Schließen' : 'Überspringen'"></button>
            <div style="display: flex; gap: 0.5rem;">
                <button class="tour-btn-back" x-show="currentStep > 0" @click="prevStep()">Zurück</button>
                <button class="tour-btn-next" @click="nextStep()" x-text="currentStep === steps.length - 1 ? 'Fertig' : 'Weiter'"></button>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }

    .tour-overlay {
        position: fixed;
        inset: 0;
        background: transparent;
        z-index: 10000;
        pointer-events: auto;
    }

    .tour-spotlight {
        position: fixed;
        z-index: 10002;
        border-radius: 12px;
        box-shadow:
            0 0 0 3px rgba(251, 191, 36, 0.6),
            0 0 30px 6px rgba(251, 191, 36, 0.2),
            0 0 0 9999px rgba(0, 0, 0, 0.7);
        transition: top 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                    left 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                    width 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                    height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
        animation: spotlight-pulse 2s ease-in-out infinite;
    }

    @keyframes spotlight-pulse {
        0%, 100% {
            box-shadow:
                0 0 0 3px rgba(251, 191, 36, 0.6),
                0 0 30px 6px rgba(251, 191, 36, 0.2),
                0 0 0 9999px rgba(0, 0, 0, 0.7);
        }
        50% {
            box-shadow:
                0 0 0 5px rgba(251, 191, 36, 0.8),
                0 0 40px 10px rgba(251, 191, 36, 0.35),
                0 0 0 9999px rgba(0, 0, 0, 0.7);
        }
    }

    .tour-tooltip {
        position: fixed;
        z-index: 10003;
        max-width: 360px;
        width: calc(100vw - 2rem);
        pointer-events: auto;
        padding: 1.5rem;
        border-radius: 1rem 0.5rem 1rem 1rem;
        transition: top 0.4s cubic-bezier(0.4, 0, 0.2, 1), left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4), 0 0 40px rgba(251, 191, 36, 0.1);
    }

    .tour-progress {
        display: flex;
        gap: 0.4rem;
        margin-bottom: 1rem;
    }

    .tour-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        transition: all 0.3s;
    }

    .tour-dot.active {
        background: var(--gold-start, #fbbf24);
        box-shadow: 0 0 8px rgba(251, 191, 36, 0.4);
        transform: scale(1.2);
    }

    .tour-dot.done {
        background: var(--gold-start, #fbbf24);
        opacity: 0.6;
    }

    .tour-content {
        margin-bottom: 1.25rem;
    }

    .tour-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-primary, #fff);
        margin-bottom: 0.4rem;
        line-height: 1.3;
    }

    .tour-desc {
        font-size: 0.85rem;
        color: var(--text-secondary, rgba(255,255,255,0.7));
        line-height: 1.6;
        margin: 0;
    }

    .tour-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .tour-skip {
        background: none;
        border: none;
        color: var(--text-muted, rgba(255,255,255,0.4));
        font-size: 0.8rem;
        font-weight: 500;
        cursor: pointer;
        padding: 0.4rem 0;
        transition: color 0.2s;
    }

    .tour-skip:hover {
        color: var(--text-secondary, rgba(255,255,255,0.7));
    }

    .tour-btn-next {
        background: linear-gradient(135deg, var(--gold-start, #fbbf24), var(--gold-end, #f59e0b));
        color: #1a1a2e;
        border: none;
        padding: 0.5rem 1.25rem;
        border-radius: 0.5rem;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
    }

    .tour-btn-next:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
    }

    .tour-btn-back {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary, #fff);
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .tour-btn-back:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    /* Light mode */
    html.light-mode .tour-spotlight {
        box-shadow:
            0 0 0 3px rgba(251, 191, 36, 0.5),
            0 0 25px 5px rgba(251, 191, 36, 0.15),
            0 0 0 9999px rgba(0, 0, 0, 0.5);
        animation: spotlight-pulse-light 2s ease-in-out infinite;
    }

    @keyframes spotlight-pulse-light {
        0%, 100% {
            box-shadow:
                0 0 0 3px rgba(251, 191, 36, 0.5),
                0 0 25px 5px rgba(251, 191, 36, 0.15),
                0 0 0 9999px rgba(0, 0, 0, 0.5);
        }
        50% {
            box-shadow:
                0 0 0 5px rgba(251, 191, 36, 0.7),
                0 0 35px 8px rgba(251, 191, 36, 0.3),
                0 0 0 9999px rgba(0, 0, 0, 0.5);
        }
    }

    html.light-mode .tour-tooltip {
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2), 0 0 40px rgba(251, 191, 36, 0.05);
    }

    @media (max-width: 600px) {
        .tour-tooltip {
            max-width: calc(100vw - 1.5rem);
            padding: 1.25rem;
        }
    }
</style>

<script>
function onboardingTour() {
    return {
        showTour: false,
        currentStep: 0,
        spotlightStyle: { top: 0, left: 0, width: 0, height: 0 },
        tooltipPosition: { top: 0, left: 0 },

        steps: [
            {
                target: '[data-tour-step="practice"]',
                title: 'Theorie lernen',
                description: 'Hier startest du mit dem Lernen der THW-Grundausbildung. Jede Frage muss 3x richtig beantwortet werden, bevor sie als gemeistert gilt.'
            },
            {
                target: '[data-tour-step="practice"]',
                title: 'Spaced Repetition',
                description: 'Richtig beantwortete Fragen werden in wachsenden Abständen wiederholt. Die Intervalle passen sich automatisch an die Schwierigkeit an – schwere Fragen kommen häufiger dran. Nach 3x richtig ist eine Frage gemeistert.'
            },
            {
                target: '[data-tour-step="streak"]',
                title: 'Streak & Tägliches Lernen',
                description: 'Dein Streak zählt, wie viele Tage in Folge du gelernt hast. Halte ihn aufrecht, um Bonuspunkte zu sammeln! Bei Vergessen schützt dich ein Streak Freeze.'
            },
            {
                target: '[data-tour-step="achievements"]',
                title: 'Erfolge & Punkte',
                description: 'Sammle Punkte für jede richtige Antwort und schalte Achievements frei. Vergleiche dich über das Leaderboard mit anderen Lernenden.'
            },
            {
                target: '[data-tour-step="exam"]',
                title: 'Prüfungssimulation',
                description: 'Sobald alle Fragen gemeistert sind, kannst du die Prüfung simulieren. 5 bestandene Prüfungen in Folge zeigen, dass du bereit bist!'
            },
            {
                target: '[data-tour-step="countdown"]',
                title: 'Prüfungs-Countdown',
                description: 'Hast du ein Prüfungsdatum hinterlegt, siehst du hier die verbleibenden Tage und ein tägliches Fragenziel. So weißt du immer, ob du im Zeitplan liegst.'
            },
            {
                target: '[data-tour-step="stats"]',
                title: 'Dein Fortschritt',
                description: 'Hier findest du Statistiken, Trefferquoten und deine Wochenaktivität. So behältst du immer den Überblick über deinen Lernstand.'
            },
            {
                target: '[data-tour-step="sidebar"]',
                mobileTarget: '[data-tour-step="bottom-nav"]',
                title: 'Navigation',
                description: 'Über die Seitenleiste erreichst du alle Bereiche: Theorie, Prüfung, Lehrgänge, Rangliste, Achievements und deinen Ortsverband.',
                mobileTitle: 'Navigation',
                mobileDescription: 'Über die untere Leiste wechselst du schnell zwischen Home, Lernen, Prüfung und Profil. Weitere Seiten wie Lehrgänge, Rangliste oder Ortsverband findest du im Menü oben rechts.'
            }
        ],

        init() {
            this.$nextTick(() => setTimeout(() => this.waitForBlockers(), 300));

            // Reposition on resize
            window.addEventListener('resize', () => {
                if (this.showTour) this.positionTooltip();
            });
        },

        waitForBlockers() {
            const hasBlocker = () => {
                // Cookie banner visible?
                const cookie = document.getElementById('cookie-banner');
                if (cookie && cookie.style.display !== 'none' && getComputedStyle(cookie).display !== 'none') return true;
                // Leaderboard modal visible?
                if (document.getElementById('leaderboard-modal')) return true;
                return false;
            };

            if (hasBlocker()) {
                const observer = new MutationObserver(() => {
                    if (!hasBlocker()) {
                        observer.disconnect();
                        setTimeout(() => this.startTour(), 600);
                    }
                });
                observer.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['style'] });
            } else {
                this.startTour();
            }
        },

        startTour() {
            this.showTour = true;
            // Lock page scroll
            document.body.style.overflow = 'hidden';
            this.$nextTick(() => this.positionTooltip());
        },

        isMobile() {
            return window.innerWidth < 1024;
        },

        getStepTarget(step) {
            if (this.isMobile() && step.mobileTarget) {
                return document.querySelector(step.mobileTarget) || document.querySelector(step.target);
            }
            return document.querySelector(step.target);
        },

        getStepTitle(step) {
            return (this.isMobile() && step.mobileTitle) ? step.mobileTitle : step.title;
        },

        getStepDescription(step) {
            return (this.isMobile() && step.mobileDescription) ? step.mobileDescription : step.description;
        },

        positionTooltip() {
            const step = this.steps[this.currentStep];
            if (!step) return;

            const el = this.getStepTarget(step);
            if (!el) {
                // Element not found, try next step
                if (this.currentStep < this.steps.length - 1) {
                    this.currentStep++;
                    this.$nextTick(() => this.positionTooltip());
                }
                return;
            }

            // Scroll element into view
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });

            setTimeout(() => {
                const rect = el.getBoundingClientRect();
                const padding = 8;

                // Spotlight position
                this.spotlightStyle = {
                    top: rect.top - padding,
                    left: rect.left - padding,
                    width: rect.width + padding * 2,
                    height: rect.height + padding * 2
                };

                // Tooltip position - prefer below the element
                const tooltipWidth = Math.min(360, window.innerWidth - 32);
                const tooltipHeight = 200; // approximate
                let top, left;

                // Check if there's space below
                if (rect.bottom + tooltipHeight + 20 < window.innerHeight) {
                    top = rect.bottom + 16;
                } else {
                    // Position above
                    top = Math.max(16, rect.top - tooltipHeight - 16);
                }

                // Horizontal: center on element, but clamp to viewport
                left = rect.left + rect.width / 2 - tooltipWidth / 2;
                left = Math.max(16, Math.min(left, window.innerWidth - tooltipWidth - 16));

                this.tooltipPosition = { top, left };
            }, 350);
        },

        nextStep() {
            if (this.currentStep < this.steps.length - 1) {
                this.currentStep++;
                this.$nextTick(() => this.positionTooltip());
            } else {
                this.completeTour();
            }
        },

        prevStep() {
            if (this.currentStep > 0) {
                this.currentStep--;
                this.$nextTick(() => this.positionTooltip());
            }
        },

        skipTour() {
            this.completeTour();
        },

        completeTour() {
            this.showTour = false;
            // Unlock page scroll
            document.body.style.overflow = '';

            // Persist to database
            fetch('{{ route("onboarding.tour.complete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                cache: 'no-store'
            }).catch(() => {
                // Silent fail - tour won't show again after page reload if DB save worked
            });
        }
    };
}
</script>
@endif
