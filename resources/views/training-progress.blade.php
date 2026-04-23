@extends('layouts.app')

@section('title', 'Ausbildungsfortschritt - THW Trainer')
@section('description', 'Dokumentiere deinen Fortschritt durch die THW-Grundausbildung und Zusatzausbildungen.')

@push('styles')
<style>
    .dash-container { max-width: 1180px; padding: 0 1rem; }

    .tp-page-title { margin: 1rem 0 1.25rem; }
    .tp-page-title__eyebrow {
        font-size: 0.6875rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
    }
    .tp-page-title__h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0.15rem 0 0.25rem;
        letter-spacing: -0.01em;
    }
    .tp-page-title__sub {
        font-size: 0.875rem;
        color: var(--text-muted);
        max-width: 640px;
    }

    .tp-card { padding: 1.5rem; border-radius: 0.75rem; }

    .tp-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 640px) { .tp-summary { grid-template-columns: 1fr; } }
    .tp-summary__pill {
        padding: 0.875rem 1rem;
        border-radius: 0.625rem;
        background: rgba(0, 51, 127, 0.04);
        border: 1px solid rgba(0, 51, 127, 0.1);
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }
    html:not(.light-mode) .tp-summary__pill {
        background: rgba(91, 154, 255, 0.04);
        border-color: rgba(91, 154, 255, 0.12);
    }
    .tp-summary__label { font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); font-family: 'IBM Plex Mono', monospace; }
    .tp-summary__value { font-size: 1.375rem; font-weight: 700; color: var(--text-primary); }
    .tp-summary__bar {
        height: 4px;
        border-radius: 999px;
        background: rgba(0, 51, 127, 0.08);
        overflow: hidden;
        margin-top: 0.45rem;
    }
    html:not(.light-mode) .tp-summary__bar { background: rgba(255, 255, 255, 0.06); }
    .tp-summary__fill {
        height: 100%;
        background: linear-gradient(90deg, #00337F, #0055cc);
        border-radius: 999px;
        transition: width 300ms ease;
    }
    html:not(.light-mode) .tp-summary__fill {
        background: linear-gradient(90deg, #5b9aff, #8fb8ff);
    }

    .tp-subhead {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-family: 'IBM Plex Mono', monospace;
        color: var(--text-muted);
        margin: 1.5rem 0 0.75rem;
        padding-left: 0.625rem;
        border-left: 2px solid var(--thw-blue);
    }
    html:not(.light-mode) .tp-subhead { border-left-color: var(--thw-blue-glow, #5b9aff); }

    .tp-section {
        border: 1px solid rgba(0, 51, 127, 0.1);
        border-radius: 0.625rem;
        margin-bottom: 0.5rem;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.3);
    }
    html:not(.light-mode) .tp-section {
        border-color: rgba(255, 255, 255, 0.06);
        background: rgba(255, 255, 255, 0.02);
    }
    .tp-section__head {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 0.875rem;
        cursor: pointer;
        user-select: none;
        transition: background 200ms;
    }
    .tp-section__head:hover { background: rgba(0, 51, 127, 0.03); }
    html:not(.light-mode) .tp-section__head:hover { background: rgba(91, 154, 255, 0.04); }

    .tp-section__nr {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.6875rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 0.25rem;
        background: rgba(0, 51, 127, 0.08);
        color: #00337F;
        letter-spacing: 0.04em;
        flex-shrink: 0;
    }
    html:not(.light-mode) .tp-section__nr {
        background: rgba(91, 154, 255, 0.12);
        color: #5b9aff;
    }
    .tp-section__title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
        flex: 1;
        min-width: 0;
    }
    .tp-section__meta {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
        flex-shrink: 0;
    }
    .tp-section__chev {
        font-size: 0.875rem;
        color: var(--text-muted);
        transition: transform 250ms;
        flex-shrink: 0;
    }
    .tp-section.is-open .tp-section__chev { transform: rotate(90deg); }

    .tp-section__body {
        padding: 0.25rem 0.875rem 0.75rem;
        border-top: 1px solid rgba(0, 51, 127, 0.06);
        display: none;
    }
    html:not(.light-mode) .tp-section__body { border-top-color: rgba(255, 255, 255, 0.04); }
    .tp-section.is-open .tp-section__body { display: block; }

    .tp-check {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.5rem 0.25rem;
        cursor: pointer;
        border-radius: 0.375rem;
        transition: background 200ms;
    }
    .tp-check:hover { background: rgba(0, 51, 127, 0.03); }
    html:not(.light-mode) .tp-check:hover { background: rgba(91, 154, 255, 0.04); }

    .tp-check input[type="checkbox"] {
        appearance: none;
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 4px;
        border: 1.5px solid rgba(0, 51, 127, 0.3);
        background: var(--bg-elevated);
        cursor: pointer;
        position: relative;
        flex-shrink: 0;
        transition: all 150ms;
    }
    html:not(.light-mode) .tp-check input[type="checkbox"] {
        border-color: rgba(91, 154, 255, 0.25);
        background: #1a1a1d;
    }
    .tp-check input[type="checkbox"]:hover { border-color: var(--thw-blue); }
    .tp-check input[type="checkbox"]:checked {
        background: var(--thw-blue);
        border-color: var(--thw-blue);
    }
    html:not(.light-mode) .tp-check input[type="checkbox"]:checked {
        background: var(--thw-blue-glow, #5b9aff);
        border-color: var(--thw-blue-glow, #5b9aff);
    }
    .tp-check input[type="checkbox"]:checked::after {
        content: '';
        position: absolute;
        top: 2px; left: 5px;
        width: 5px; height: 9px;
        border: solid #fff;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }
    .tp-check input[type="checkbox"]:indeterminate {
        background: var(--thw-blue);
        border-color: var(--thw-blue);
    }
    html:not(.light-mode) .tp-check input[type="checkbox"]:indeterminate {
        background: var(--thw-blue-glow, #5b9aff);
        border-color: var(--thw-blue-glow, #5b9aff);
    }
    .tp-check input[type="checkbox"]:indeterminate::after {
        content: '';
        position: absolute;
        top: 7px; left: 3px;
        width: 10px; height: 2px;
        background: #fff;
        border-radius: 1px;
    }
    .tp-check input[type="checkbox"]:disabled {
        opacity: 0.6;
        cursor: wait;
    }
    .tp-check__label {
        font-size: 0.8125rem;
        color: var(--text-primary);
        line-height: 1.4;
    }
    .tp-check.is-done .tp-check__label {
        color: var(--text-muted);
        text-decoration: line-through;
    }

    .tp-zusatz-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.25rem;
    }
    @media (max-width: 640px) { .tp-zusatz-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="dash-container">

    <div class="tp-page-title">
        <div class="tp-page-title__eyebrow">Profil</div>
        <h1 class="tp-page-title__h1">Ausbildungsfortschritt</h1>
        <div class="tp-page-title__sub">
            Halte deinen Fortschritt durch die THW-Grundausbildung fest. Ganze Lernabschnitte lassen sich über die Überschrift auf einmal abhaken.
        </div>
    </div>

    <div class="glass tp-card" x-data="trainingProgress({
        completed: @js($trainingOverview['completed_keys']),
        overview: @js($trainingOverview),
        lernabschnitte: @js($trainingLernabschnitte),
        zusatz: @js($trainingZusatzausbildungen),
        csrfToken: @js(csrf_token()),
        itemUrl: @js(route('training-progress.item')),
        sectionUrl: @js(route('training-progress.section'))
    })">
        <div class="tp-summary">
            <div class="tp-summary__pill">
                <span class="tp-summary__label">Lernabschnitte</span>
                <span class="tp-summary__value"><span x-text="overview.la_done"></span> / <span x-text="overview.la_total"></span></span>
                <div class="tp-summary__bar"><div class="tp-summary__fill" :style="'width: ' + overview.la_percent + '%'"></div></div>
            </div>
            <div class="tp-summary__pill">
                <span class="tp-summary__label">Zusatzausbildungen</span>
                <span class="tp-summary__value"><span x-text="overview.zusatz_done"></span> / <span x-text="overview.zusatz_total"></span></span>
                <div class="tp-summary__bar"><div class="tp-summary__fill" :style="'width: ' + overview.zusatz_percent + '%'"></div></div>
            </div>
            <div class="tp-summary__pill">
                <span class="tp-summary__label">Gesamt</span>
                <span class="tp-summary__value"><span x-text="overview.percent"></span>%</span>
                <div class="tp-summary__bar"><div class="tp-summary__fill" :style="'width: ' + overview.percent + '%'"></div></div>
            </div>
        </div>

        <div class="tp-subhead">Lernabschnitte</div>
        <template x-for="la in lernabschnitte" :key="la.key">
            <div class="tp-section" :class="{ 'is-open': openSections[la.key] }">
                <div class="tp-section__head" @click="openSections[la.key] = !openSections[la.key]">
                    <label class="tp-check" style="padding: 0; margin: 0;" @click.stop>
                        <input type="checkbox"
                               :checked="sectionState(la).allDone"
                               x-effect="$el.indeterminate = sectionState(la).someDone && !sectionState(la).allDone"
                               :disabled="busy[la.key]"
                               @change="toggleSection(la, $event.target.checked)">
                    </label>
                    <span class="tp-section__nr" x-text="la.nr"></span>
                    <span class="tp-section__title" x-text="la.title"></span>
                    <span class="tp-section__meta" x-text="sectionState(la).doneCount + '/' + la.items.length"></span>
                    <i class="bi bi-chevron-right tp-section__chev"></i>
                </div>
                <div class="tp-section__body">
                    <template x-for="item in la.items" :key="item.key">
                        <label class="tp-check" :class="{ 'is-done': isDone(item.key) }">
                            <input type="checkbox"
                                   :checked="isDone(item.key)"
                                   :disabled="busy[item.key]"
                                   @change="toggleItem(item.key, $event.target.checked)">
                            <span class="tp-check__label" x-text="item.label"></span>
                        </label>
                    </template>
                </div>
            </div>
        </template>

        <div class="tp-subhead">Zusatzausbildungen</div>
        <div class="tp-zusatz-grid">
            <template x-for="item in zusatz" :key="item.key">
                <label class="tp-check" :class="{ 'is-done': isDone(item.key) }">
                    <input type="checkbox"
                           :checked="isDone(item.key)"
                           :disabled="busy[item.key]"
                           @change="toggleItem(item.key, $event.target.checked)">
                    <span class="tp-check__label" x-text="item.label"></span>
                </label>
            </template>
        </div>
    </div>
</div>

<script>
    function trainingProgress(config) {
        return {
            completedSet: (config.completed || []).reduce(function(acc, k) { acc[k] = true; return acc; }, {}),
            overview: config.overview,
            lernabschnitte: config.lernabschnitte,
            zusatz: config.zusatz,
            csrfToken: config.csrfToken,
            itemUrl: config.itemUrl,
            sectionUrl: config.sectionUrl,
            openSections: {},
            busy: {},

            isDone(key) {
                return !!this.completedSet[key];
            },

            sectionState(la) {
                var done = 0;
                for (var i = 0; i < la.items.length; i++) {
                    if (this.completedSet[la.items[i].key]) done++;
                }
                return {
                    doneCount: done,
                    allDone: done === la.items.length && la.items.length > 0,
                    someDone: done > 0,
                };
            },

            async toggleItem(key, checked) {
                if (this.busy[key]) return;
                this.busy[key] = true;
                var prev = !!this.completedSet[key];
                this.completedSet[key] = !!checked;
                try {
                    var res = await fetch(this.itemUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                        },
                        body: JSON.stringify({ item_key: key, completed: !!checked }),
                        cache: 'no-store',
                    });
                    var data = await res.json();
                    if (data && data.success && data.overview) {
                        this.overview = data.overview;
                    } else {
                        this.completedSet[key] = prev;
                    }
                } catch (e) {
                    console.error('Training toggle error:', e);
                    this.completedSet[key] = prev;
                } finally {
                    this.busy[key] = false;
                }
            },

            async toggleSection(la, checked) {
                if (this.busy[la.key]) return;
                this.busy[la.key] = true;
                var prev = {};
                for (var i = 0; i < la.items.length; i++) {
                    var k = la.items[i].key;
                    prev[k] = !!this.completedSet[k];
                    this.completedSet[k] = !!checked;
                }
                try {
                    var res = await fetch(this.sectionUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                        },
                        body: JSON.stringify({ section_key: la.key, completed: !!checked }),
                        cache: 'no-store',
                    });
                    var data = await res.json();
                    if (data && data.success && data.overview) {
                        this.overview = data.overview;
                    } else {
                        for (var j = 0; j < la.items.length; j++) {
                            this.completedSet[la.items[j].key] = prev[la.items[j].key];
                        }
                    }
                } catch (e) {
                    console.error('Training section toggle error:', e);
                    for (var j = 0; j < la.items.length; j++) {
                        this.completedSet[la.items[j].key] = prev[la.items[j].key];
                    }
                } finally {
                    this.busy[la.key] = false;
                }
            },
        };
    }
</script>
@endsection
