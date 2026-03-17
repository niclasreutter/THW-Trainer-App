# Exam Page Redesign — Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign der /exam Seite (aktive Prüfung + Ergebnis) passend zum Design-System von Dashboard, Practice, Bookmarks und Exam-History.

**Architecture:** Single-file Blade view (`exam.blade.php`) mit eingebettetem CSS + Vanilla JS. Die bestehende Single-Form/Show-Hide Architektur bleibt erhalten. Controller-Logik unverändert. Neue CSS-Klassen mit `exam-` Prefix. Sliding-Bubbles Navigation mit 9 sichtbaren Bubbles.

**Tech Stack:** Laravel 12, Blade, Tailwind CSS (compiled), Vanilla JavaScript, SVG für Ergebnis-Ring

**Spec:** `docs/superpowers/specs/2026-03-17-exam-redesign-design.md`

---

## File Structure

| File | Action | Responsibility |
|------|--------|----------------|
| `resources/views/exam.blade.php` | Rewrite | Aktive Prüfung + Ergebnis (CSS, HTML, JS) |
| `resources/views/exam_result.blade.php` | Delete | Legacy — ersetzt durch integrierte Ergebnisseite |
| `app/Http/Controllers/ExamController.php` | No change | `showResult()` rendert bereits `exam` View mit `$submitted = true` |
| `resources/css/app.css` | No change | Design-System bleibt unverändert |

---

## Chunk 1: CSS Foundation + Active Exam HTML

### Task 1: CSS — Mobile Fullscreen + Topbar + Progress Bar

**Files:**
- Modify: `resources/views/exam.blade.php:5-861` (CSS-Block komplett ersetzen)

- [ ] **Step 1: Lies die bestehende CSS-Sektion**

Lies `resources/views/exam.blade.php` Zeilen 5-861 um alle bestehenden CSS-Klassen zu verstehen.

- [ ] **Step 2: Schreibe den neuen CSS-Block — Teil 1: Shell + Topbar + Progress**

Ersetze den gesamten `@push('styles')<style>...</style>@endpush` Block. **Wichtig: CSS muss in `@push('styles')<style>...</style>@endpush` gewrapped bleiben.** Beginne mit dem Grundgerüst:

```css
/* ============================================
   EXAM PAGE — Dashboard-Matched Redesign
   Mobile-First, Fullscreen, Sliding Bubbles
   ============================================ */

/* ── Mobile: Fullscreen Takeover ─────────────── */
@media (max-width: 640px) {
    html, body {
        height: 100dvh !important;
        overflow: hidden !important;
    }
    .exam-active-mode footer,
    .exam-active-mode nav,
    .exam-active-mode header {
        display: none !important;
    }
    .exam-active-mode main {
        padding: 0 !important;
        margin: 0 !important;
        height: 100dvh !important;
        overflow: hidden !important;
    }
}

/* ── Exam Shell ──────────────────────────── */
.exam-shell {
    max-width: 900px;
    margin: 0 auto;
    padding: 1.5rem;
}

@media (max-width: 640px) {
    .exam-shell {
        padding: 0;
        max-width: 100%;
        height: 100dvh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
}

/* ── Topbar (Mobile) ────────────────────────── */
.exam-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.625rem 1rem;
    padding-top: calc(0.625rem + env(safe-area-inset-top, 0px));
    background: rgba(255, 255, 255, 0.03);
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    flex-shrink: 0;
}

html.light-mode .exam-topbar {
    background: rgba(0, 51, 127, 0.03);
    border-bottom-color: rgba(0, 51, 127, 0.08);
}

@media (min-width: 641px) {
    .exam-topbar { display: none; }
    .exam-topbar--result { display: none; } /* Result topbar auch auf Desktop hidden */
}

.exam-topbar-back {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 0.5rem;
    color: var(--text-secondary, rgba(255, 255, 255, 0.5));
    background: rgba(255, 255, 255, 0.04);
    transition: all 0.2s;
    cursor: pointer;
    border: none;
}

html.light-mode .exam-topbar-back {
    background: rgba(0, 51, 127, 0.04);
    color: #999;
}

.exam-topbar-center {
    text-align: center;
}

.exam-topbar-label {
    font-size: 0.5rem;
    color: rgba(255, 255, 255, 0.35);
    font-family: 'IBM Plex Mono', monospace;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

html.light-mode .exam-topbar-label {
    color: rgba(0, 51, 127, 0.45);
}

.exam-topbar-title {
    font-size: 0.8125rem;
    font-weight: 700;
    color: var(--text-primary, white);
}

.exam-topbar-title .exam-qnum {
    background: linear-gradient(135deg, #00337F, #3b82f6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.exam-topbar-title .exam-qtotal {
    color: rgba(255, 255, 255, 0.3);
    font-weight: 400;
}

html.light-mode .exam-topbar-title .exam-qtotal {
    color: rgba(0, 0, 0, 0.3);
}

.exam-timer {
    font-weight: 700;
    font-size: 0.75rem;
    font-family: 'IBM Plex Mono', monospace;
    color: #fbbf24;
    transition: color 0.3s;
}

.exam-timer.warning {
    color: #ef4444;
}

.exam-timer.critical {
    color: #ef4444;
    animation: exam-timer-pulse 1s ease-in-out infinite;
}

@keyframes exam-timer-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* ── Progress Bar ──────────────────────────── */
.exam-progress-bar {
    height: 3px;
    background: rgba(255, 255, 255, 0.06);
    flex-shrink: 0;
}

html.light-mode .exam-progress-bar {
    background: rgba(0, 51, 127, 0.08);
}

.exam-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #00337F, #3b82f6);
    border-radius: 0 2px 2px 0;
    transition: width 0.3s ease;
}
```

- [ ] **Step 3: CSS Teil 2 — Desktop Header + Stat Pills**

```css
/* ── Desktop Header ──────────────────────────── */
.exam-header {
    margin-bottom: 1rem;
}

@media (max-width: 640px) {
    .exam-header { display: none; }
}

.exam-greeting {
    font-size: 0.625rem;
    color: rgba(255, 255, 255, 0.35);
    font-family: 'IBM Plex Mono', monospace;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

html.light-mode .exam-greeting {
    color: rgba(0, 51, 127, 0.45);
}

.exam-title {
    font-size: 1.375rem;
    font-weight: 800;
    color: var(--text-primary, white);
    font-family: 'Barlow Condensed', sans-serif;
    margin: 0.2rem 0;
}

.exam-title .exam-qnum {
    background: linear-gradient(135deg, #00337F, #3b82f6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.exam-title .exam-qtotal {
    color: rgba(255, 255, 255, 0.25);
    font-weight: 400;
    font-size: 1rem;
}

html.light-mode .exam-title .exam-qtotal {
    color: rgba(0, 0, 0, 0.25);
}

.exam-subtitle {
    font-size: 0.625rem;
    color: rgba(255, 255, 255, 0.4);
}

html.light-mode .exam-subtitle {
    color: #666;
}

/* ── Stat Pills ──────────────────────────── */
.exam-stats-row {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

@media (max-width: 640px) {
    .exam-stats-row { display: none; }
}

.exam-stat-pill {
    border-radius: 0.5rem;
    padding: 0.3rem 0.7rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.exam-stat-pill .exam-stat-value {
    font-size: 0.875rem;
    font-weight: 700;
    font-family: 'IBM Plex Mono', monospace;
}

.exam-stat-pill .exam-stat-label {
    font-size: 0.5rem;
    color: rgba(255, 255, 255, 0.4);
    text-transform: uppercase;
}

html.light-mode .exam-stat-pill .exam-stat-label {
    color: #888;
}

.exam-stat-pill--timer {
    background: rgba(251, 191, 36, 0.08);
    border: 1px solid rgba(251, 191, 36, 0.2);
}

.exam-stat-pill--timer .exam-stat-value {
    color: #fbbf24;
}

.exam-stat-pill--timer.warning {
    background: rgba(239, 68, 68, 0.08);
    border-color: rgba(239, 68, 68, 0.2);
}

.exam-stat-pill--timer.warning .exam-stat-value {
    color: #ef4444;
}

.exam-stat-pill--answered {
    background: rgba(34, 197, 94, 0.08);
    border: 1px solid rgba(34, 197, 94, 0.2);
}

.exam-stat-pill--answered .exam-stat-value {
    color: #22c55e;
}

.exam-stat-pill--open {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
}

html.light-mode .exam-stat-pill--open {
    background: rgba(0, 51, 127, 0.04);
    border-color: rgba(0, 51, 127, 0.08);
}

.exam-stat-pill--open .exam-stat-value {
    color: rgba(255, 255, 255, 0.5);
}

html.light-mode .exam-stat-pill--open .exam-stat-value {
    color: #666;
}

.exam-stat-pill--marked {
    background: rgba(251, 191, 36, 0.08);
    border: 1px solid rgba(251, 191, 36, 0.2);
}

.exam-stat-pill--marked .exam-stat-value {
    color: #fbbf24;
}
```

- [ ] **Step 4: CSS Teil 3 — Question Card + Answers**

```css
/* ── Content Area ──────────────────────────── */
.exam-content {
    flex: 1;
    overflow-y: auto;
    padding: 0.85rem;
    -webkit-overflow-scrolling: touch;
}

@media (min-width: 641px) {
    .exam-content {
        padding: 0;
        overflow: visible;
    }
}

/* ── Question Card ──────────────────────────── */
.exam-question-card {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 1.25rem 0.4rem 1.25rem 1.25rem;
    padding: 1rem;
    margin-bottom: 0.75rem;
    position: relative;
}

html.light-mode .exam-question-card {
    background: rgba(255, 255, 255, 0.85);
    border-color: rgba(0, 51, 127, 0.1);
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
}

.exam-question-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #00337F, #3b82f6);
    border-radius: 1.25rem 0.4rem 0 0;
}

.exam-question-la {
    font-size: 0.53rem;
    color: #60a5fa;
    font-family: 'IBM Plex Mono', monospace;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    margin-bottom: 0.4rem;
}

html.light-mode .exam-question-la {
    color: #00337F;
}

.exam-question-text {
    font-size: 0.875rem;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.85);
}

html.light-mode .exam-question-text {
    color: #333;
}

@media (min-width: 641px) {
    .exam-question-card {
        padding: 1.25rem;
    }
    .exam-question-la {
        font-size: 0.625rem;
    }
    .exam-question-text {
        font-size: 0.9375rem;
    }
}

/* ── Answer Options ──────────────────────────── */
.exam-answers {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.exam-answer {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 0.5rem;
    padding: 0.6rem 0.75rem;
    cursor: pointer;
    transition: all 0.15s ease;
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.8125rem;
}

html.light-mode .exam-answer {
    background: rgba(255, 255, 255, 0.7);
    border-color: rgba(0, 0, 0, 0.08);
    color: #666;
}

.exam-answer:hover {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(255, 255, 255, 0.1);
}

html.light-mode .exam-answer:hover {
    background: rgba(0, 51, 127, 0.04);
    border-color: rgba(0, 51, 127, 0.12);
}

.exam-answer.selected {
    background: rgba(0, 51, 127, 0.15);
    border-color: rgba(0, 85, 204, 0.3);
    color: #93c5fd;
}

html.light-mode .exam-answer.selected {
    background: rgba(0, 51, 127, 0.08);
    border-color: rgba(0, 51, 127, 0.2);
    color: #00337F;
}

.exam-checkbox {
    width: 18px;
    height: 18px;
    border-radius: 4px;
    border: 1.5px solid rgba(255, 255, 255, 0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.15s ease;
    font-size: 0.5rem;
}

html.light-mode .exam-checkbox {
    border-color: rgba(0, 0, 0, 0.12);
}

.exam-answer.selected .exam-checkbox {
    background: rgba(0, 51, 127, 0.4);
    border-color: #3b82f6;
    color: #3b82f6;
}

html.light-mode .exam-answer.selected .exam-checkbox {
    background: rgba(0, 51, 127, 0.15);
    border-color: #00337F;
    color: #00337F;
}

@media (min-width: 641px) {
    .exam-answer {
        padding: 0.7rem 0.85rem;
        font-size: 0.875rem;
    }
}
```

- [ ] **Step 5: CSS Teil 4 — Sliding Bubbles + Bottom Bar + Buttons**

```css
/* ── Fixed Bottom Bar (Mobile) ──────────────── */
.exam-bottom-bar {
    background: rgba(255, 255, 255, 0.03);
    border-top: 1px solid rgba(255, 255, 255, 0.06);
    padding: 0.5rem 0.75rem;
    padding-bottom: calc(0.5rem + env(safe-area-inset-bottom, 0px));
    flex-shrink: 0;
}

html.light-mode .exam-bottom-bar {
    background: rgba(255, 255, 255, 0.7);
    border-top-color: rgba(0, 51, 127, 0.08);
    backdrop-filter: blur(12px);
}

@media (min-width: 641px) {
    .exam-bottom-bar {
        background: transparent;
        border-top: none;
        padding: 0;
        margin-top: 1rem;
    }
    html.light-mode .exam-bottom-bar {
        background: transparent;
        border-top: none;
        backdrop-filter: none;
    }
}

/* ── Sliding Bubbles ──────────────────────────── */
.exam-bubbles-track {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    margin-bottom: 0.5rem;
    transition: transform 0.3s ease;
}

@media (min-width: 641px) {
    .exam-bubbles-track {
        gap: 6px;
        margin-bottom: 0.75rem;
    }
}

.exam-bubble {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    font-size: 0.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    padding: 0;
}

@media (min-width: 641px) {
    .exam-bubble {
        width: 24px;
        height: 24px;
        font-size: 0.5625rem;
    }
}

.exam-bubble--open {
    background: rgba(255, 255, 255, 0.06);
    color: rgba(255, 255, 255, 0.25);
}

html.light-mode .exam-bubble--open {
    background: rgba(0, 0, 0, 0.05);
    color: rgba(0, 0, 0, 0.25);
}

.exam-bubble--answered {
    background: #22c55e;
    color: white;
}

.exam-bubble--marked {
    background: #fbbf24;
    color: white;
}

.exam-bubble--active {
    background: rgba(0, 51, 127, 0.5);
    border: 2px solid #3b82f6;
    color: #93c5fd;
    font-weight: 700;
    box-shadow: 0 0 8px rgba(59, 130, 246, 0.4);
}

html.light-mode .exam-bubble--active {
    background: rgba(0, 51, 127, 0.15);
    border-color: #00337F;
    color: #00337F;
    box-shadow: 0 0 6px rgba(0, 51, 127, 0.25);
}

@media (min-width: 641px) {
    .exam-bubble--active {
        width: 28px;
        height: 28px;
        font-size: 0.625rem;
    }
}

/* ── Action Buttons ──────────────────────────── */
.exam-actions {
    display: flex;
    gap: 0.5rem;
}

.exam-btn {
    flex: 1;
    border-radius: 0.5rem;
    padding: 0.45rem;
    font-size: 0.5625rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
    border: none;
    text-align: center;
}

@media (min-width: 641px) {
    .exam-btn {
        padding: 0.55rem;
        font-size: 0.6875rem;
    }
}

.exam-btn--back {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.5);
}

html.light-mode .exam-btn--back {
    background: rgba(0, 51, 127, 0.04);
    border: 1px solid rgba(0, 51, 127, 0.12);
    color: #666;
}

.exam-btn--mark {
    background: rgba(251, 191, 36, 0.08);
    border: 1px solid rgba(251, 191, 36, 0.25);
    color: #fbbf24;
}

html.light-mode .exam-btn--mark {
    color: #b88a00;
    border-color: rgba(251, 191, 36, 0.3);
}

.exam-btn--mark.active {
    background: rgba(251, 191, 36, 0.2);
    border-color: rgba(251, 191, 36, 0.5);
}

.exam-btn--next {
    background: linear-gradient(135deg, #00337F, #0055cc);
    color: white;
    font-weight: 600;
}

.exam-btn--next:hover {
    filter: brightness(1.1);
}

.exam-btn--submit {
    background: linear-gradient(135deg, #16a34a, #22c55e);
    color: white;
    font-weight: 600;
}

/* ── Question Slide ──────────────────────────── */
.exam-slide {
    display: none;
}

.exam-slide.active {
    display: block;
}

/* ── Animations ──────────────────────────── */
.exam-rise {
    opacity: 0;
    transform: translateY(12px);
    animation: examRise 0.5s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

@keyframes examRise {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .exam-rise {
        animation: none;
        opacity: 1;
        transform: none;
    }
}
```

- [ ] **Step 6: CSS Teil 5 — Result Summary + Review Mode**

```css
/* ============================================
   RESULT MODE
   ============================================ */

/* ── Result Summary ──────────────────────────── */
.exam-result-summary {
    text-align: center;
    padding: 1.5rem 1rem;
    max-width: 400px;
    margin: 0 auto;
}

@media (min-width: 641px) {
    .exam-result-summary {
        padding: 2rem;
    }
}

.exam-result-ring {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto 1rem;
}

.exam-result-ring svg {
    width: 100%;
    height: 100%;
}

.exam-result-ring .ring-track {
    fill: none;
    stroke: rgba(255, 255, 255, 0.06);
    stroke-width: 6;
}

html.light-mode .exam-result-ring .ring-track {
    stroke: rgba(0, 51, 127, 0.08);
}

.exam-result-ring .ring-fill {
    fill: none;
    stroke-width: 6;
    stroke-linecap: round;
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
    transition: stroke-dashoffset 1s ease;
}

.exam-result-ring .ring-fill.passed {
    stroke: #22c55e;
}

.exam-result-ring .ring-fill.failed {
    stroke: #ef4444;
}

.exam-result-center {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.exam-result-percent {
    font-size: 1.75rem;
    font-weight: 800;
}

.exam-result-percent.passed {
    background: linear-gradient(135deg, #22c55e, #4ade80);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.exam-result-percent.failed {
    background: linear-gradient(135deg, #ef4444, #f87171);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.exam-result-count {
    font-size: 0.5rem;
    color: rgba(255, 255, 255, 0.4);
    text-transform: uppercase;
    font-family: 'IBM Plex Mono', monospace;
}

html.light-mode .exam-result-count {
    color: #888;
}

.exam-result-status {
    font-size: 0.9375rem;
    font-weight: 700;
    margin-bottom: 0.2rem;
}

.exam-result-status.passed { color: #22c55e; }
.exam-result-status.failed { color: #ef4444; }

.exam-result-meta {
    font-size: 0.5625rem;
    color: rgba(255, 255, 255, 0.4);
    margin-bottom: 1rem;
}

html.light-mode .exam-result-meta {
    color: #888;
}

/* ── XP Badge ──────────────────────────── */
.exam-xp-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    background: rgba(251, 191, 36, 0.1);
    border: 1px solid rgba(251, 191, 36, 0.25);
    border-radius: 2rem;
    padding: 0.25rem 0.75rem;
    font-size: 0.6875rem;
    font-weight: 700;
    color: #fbbf24;
    margin-bottom: 1rem;
}

/* ── Result Stats Pills ──────────────────────── */
.exam-result-stats {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.exam-result-stat {
    border-radius: 0.5rem;
    padding: 0.4rem 0.75rem;
    text-align: center;
}

.exam-result-stat--correct {
    background: rgba(34, 197, 94, 0.08);
    border: 1px solid rgba(34, 197, 94, 0.2);
}

.exam-result-stat--wrong {
    background: rgba(239, 68, 68, 0.08);
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.exam-result-stat .stat-value {
    font-size: 0.9375rem;
    font-weight: 700;
}

.exam-result-stat--correct .stat-value { color: #22c55e; }
.exam-result-stat--wrong .stat-value { color: #ef4444; }

.exam-result-stat .stat-label {
    font-size: 0.4375rem;
    color: rgba(255, 255, 255, 0.4);
    text-transform: uppercase;
}

html.light-mode .exam-result-stat .stat-label {
    color: #888;
}

/* ── Weakness Analysis ──────────────────────── */
.exam-weakness-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 0.75rem;
    padding: 0.75rem;
    margin-bottom: 1rem;
    text-align: left;
}

html.light-mode .exam-weakness-card {
    background: rgba(255, 255, 255, 0.85);
    border-color: rgba(0, 51, 127, 0.1);
}

.exam-weakness-title {
    font-size: 0.5rem;
    color: rgba(255, 255, 255, 0.4);
    text-transform: uppercase;
    font-family: 'IBM Plex Mono', monospace;
    margin-bottom: 0.5rem;
}

html.light-mode .exam-weakness-title {
    color: #888;
}

.exam-weakness-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.35rem;
}

.exam-weakness-la {
    font-size: 0.5rem;
    width: 2rem;
    flex-shrink: 0;
}

.exam-weakness-bar {
    flex: 1;
    height: 4px;
    background: rgba(255, 255, 255, 0.06);
    border-radius: 2px;
}

html.light-mode .exam-weakness-bar {
    background: rgba(0, 51, 127, 0.08);
}

.exam-weakness-fill {
    height: 100%;
    border-radius: 2px;
    transition: width 0.5s ease;
}

.exam-weakness-fill.bad { background: #ef4444; }
.exam-weakness-fill.medium { background: #f59e0b; }

.exam-weakness-score {
    font-size: 0.5rem;
    color: rgba(255, 255, 255, 0.4);
    width: 2rem;
    text-align: right;
}

html.light-mode .exam-weakness-score {
    color: #888;
}

/* ── Result Action Buttons ──────────────────── */
.exam-result-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.exam-result-btn {
    width: 100%;
    border-radius: 0.5rem;
    padding: 0.65rem;
    font-size: 0.6875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    border: none;
    text-align: center;
}

.exam-result-btn--primary {
    background: linear-gradient(135deg, #00337F, #0055cc);
    color: white;
}

.exam-result-btn--wrong {
    background: rgba(239, 68, 68, 0.08);
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: #ef4444;
}

.exam-result-btn--close {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: rgba(255, 255, 255, 0.5);
}

html.light-mode .exam-result-btn--close {
    background: rgba(0, 51, 127, 0.04);
    border-color: rgba(0, 51, 127, 0.12);
    color: #666;
}

/* ── Review Mode (Question-by-Question) ─────── */
.exam-question-card.result-correct::before {
    background: #22c55e;
}

.exam-question-card.result-wrong::before {
    background: #ef4444;
}

.exam-result-badge {
    font-size: 0.5rem;
    font-weight: 600;
    text-transform: uppercase;
    font-family: 'IBM Plex Mono', monospace;
}

.exam-result-badge.correct { color: #22c55e; }
.exam-result-badge.wrong { color: #ef4444; }

.exam-answer.result-correct {
    background: rgba(34, 197, 94, 0.12);
    border-color: rgba(34, 197, 94, 0.3);
    color: #86efac;
}

html.light-mode .exam-answer.result-correct {
    background: rgba(34, 197, 94, 0.08);
    border-color: rgba(34, 197, 94, 0.2);
    color: #166534;
}

.exam-answer.result-correct .exam-checkbox {
    background: rgba(34, 197, 94, 0.3);
    border-color: #22c55e;
    color: #22c55e;
}

.exam-answer.result-wrong {
    background: rgba(239, 68, 68, 0.12);
    border-color: rgba(239, 68, 68, 0.3);
    color: #fca5a5;
}

html.light-mode .exam-answer.result-wrong {
    background: rgba(239, 68, 68, 0.08);
    border-color: rgba(239, 68, 68, 0.2);
    color: #991b1b;
}

.exam-answer.result-wrong .exam-checkbox {
    background: rgba(239, 68, 68, 0.3);
    border-color: #ef4444;
    color: #ef4444;
}

.exam-answer.result-missed {
    background: rgba(34, 197, 94, 0.06);
    border-color: rgba(34, 197, 94, 0.15);
    color: rgba(134, 239, 172, 0.5);
}

html.light-mode .exam-answer.result-missed {
    color: rgba(22, 101, 52, 0.5);
}

/* Review bubbles */
.exam-bubble--correct {
    background: #22c55e;
    color: white;
}

.exam-bubble--wrong {
    background: #ef4444;
    color: white;
}
```

- [ ] **Step 7: Verifiziere CSS-Vollständigkeit**

Prüfe dass alle CSS-Klassen aus der Spec abgedeckt sind:
- [x] Shell + Topbar + Progress Bar
- [x] Desktop Header + Stat Pills
- [x] Question Card + Answers
- [x] Sliding Bubbles + Bottom Bar + Buttons
- [x] Result Summary + Ring + Stats
- [x] Weakness Analysis
- [x] Review Mode + Result Badges
- [x] Dark + Light Mode für alles
- [x] Responsive Breakpoints (640px)
- [x] Animations (rise, timer pulse, progress transition)

- [ ] **Step 8: Commit CSS**

```bash
git add resources/views/exam.blade.php
git commit -m "🎨: Exam Redesign CSS Foundation"
```

---

### Task 2: HTML — Active Exam Structure (Mobile + Desktop)

**Files:**
- Modify: `resources/views/exam.blade.php` (HTML-Sektion nach `</style>`)

- [ ] **Step 1: Lies die bestehende HTML-Sektion**

Lies `resources/views/exam.blade.php` ab Zeile ~862 bis zum Beginn der Results-Sektion, um die bestehende Form-Struktur und Variablen zu verstehen.

- [ ] **Step 2: Schreibe die neue Active-Exam HTML-Struktur**

Ersetze den `@if(!isset($submitted))` Block. Behalte die bestehende `<form>` mit allen hidden Inputs und `@csrf` bei. Die Struktur:

```blade
@section('content')

@if(!isset($submitted))
{{-- Sofort exam-active-mode setzen (vor dem DOM, kein FOUC) --}}
<script>document.body.classList.add('exam-active-mode');</script>

<div class="exam-shell exam-rise">
    {{-- ── Mobile Topbar ─────────────────────────── --}}
    <div class="exam-topbar">
        <button class="exam-topbar-back" onclick="handleExamExit(event)" aria-label="Prüfung verlassen">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>
        </button>
        <div class="exam-topbar-center">
            <div class="exam-topbar-label">PRÜFUNG</div>
            <div class="exam-topbar-title">
                Frage <span class="exam-qnum" id="topbar-qnum">1</span> <span class="exam-qtotal">/ {{ count($fragen) }}</span>
            </div>
        </div>
        <div class="exam-timer" id="exam-timer-mobile">30:00</div>
    </div>

    {{-- ── Progress Bar ─────────────────────────── --}}
    <div class="exam-progress-bar">
        <div class="exam-progress-fill" id="exam-progress" style="width: 0%"></div>
    </div>

    {{-- ── Desktop Header ─────────────────────────── --}}
    <div class="exam-header">
        <p class="exam-greeting">PRÜFUNGSSIMULATION</p>
        <h1 class="exam-title">
            Frage <span class="exam-qnum" id="desktop-qnum">1</span> <span class="exam-qtotal">/ {{ count($fragen) }}</span>
        </h1>
        <p class="exam-subtitle">40 Fragen — 30 Minuten — 80% zum Bestehen</p>
    </div>

    {{-- ── Stat Pills (Desktop) ─────────────────── --}}
    <div class="exam-stats-row">
        <div class="exam-stat-pill exam-stat-pill--timer" id="stat-timer-pill">
            <span class="exam-stat-value exam-timer" id="exam-timer-desktop">30:00</span>
            <span class="exam-stat-label">Restzeit</span>
        </div>
        <div class="exam-stat-pill exam-stat-pill--answered">
            <span class="exam-stat-value" id="stat-answered">0</span>
            <span class="exam-stat-label">Beantwortet</span>
        </div>
        <div class="exam-stat-pill exam-stat-pill--open">
            <span class="exam-stat-value" id="stat-open">{{ count($fragen) }}</span>
            <span class="exam-stat-label">Offen</span>
        </div>
        <div class="exam-stat-pill exam-stat-pill--marked">
            <span class="exam-stat-value" id="stat-marked">0</span>
            <span class="exam-stat-label">Markiert</span>
        </div>
    </div>

    {{-- Desktop Progress Bar wird über das Mobile .exam-progress-bar mitgenutzt --}}
    {{-- Auf Desktop ist es durch CSS sichtbar (kein display:none nötig) --}}

    {{-- ── Content Area ─────────────────────────── --}}
    <div class="exam-content">
        <form id="exam-form" method="POST" action="{{ route('exam.submit') }}">
            @csrf
            <input type="hidden" name="exam_token" value="{{ $examToken }}">

            @foreach($fragen as $index => $frage)
                <input type="hidden" name="fragen_ids[]" value="{{ $frage->id }}">
                <input type="hidden" name="answer_mappings[{{ $index }}]" value="{{ json_encode($answerMappings[$index] ?? []) }}">

                <div class="exam-slide" data-question="{{ $index }}" @if($index === 0) style="display: block;" @endif>
                    {{-- Question Card --}}
                    <div class="exam-question-card">
                        <div class="exam-question-la">LA {{ $frage->lernabschnitt ?? '-' }}.{{ $frage->nummer ?? '-' }}</div>
                        <div class="exam-question-text">
                            {{ $frage->frage }}
                            @if(substr_count($frage->loesung, ',') > 0)
                                <span style="opacity: 0.5; font-size: 0.75em;">({{ substr_count($frage->loesung, ',') + 1 }} richtige Antworten)</span>
                            @endif
                        </div>
                    </div>

                    {{-- Answers --}}
                    <div class="exam-answers">
                        @php
                            $mapping = $answerMappings[$index] ?? [0 => 'A', 1 => 'B', 2 => 'C'];
                            $reverseMapping = array_flip($mapping);
                        @endphp
                        @foreach($mapping as $position => $letter)
                            @php
                                $answerField = 'antwort_' . strtolower($letter);
                                $answerText = $frage->$answerField;
                            @endphp
                            <label class="exam-answer" data-index="{{ $index }}" data-position="{{ $position }}" onclick="toggleAnswer({{ $index }}, {{ $position }})">
                                <div class="exam-checkbox"></div>
                                <input type="checkbox" name="answer[{{ $index }}][]" value="{{ $position }}" style="display:none;">
                                <span>{{ $answerText }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </form>
    </div>

    {{-- ── Bottom Bar (Bubbles + Actions) ─────── --}}
    <div class="exam-bottom-bar">
        <div class="exam-bubbles-track" id="exam-bubbles">
            {{-- Bubbles werden per JS generiert --}}
        </div>
        <div class="exam-actions">
            <button class="exam-btn exam-btn--back" onclick="prevQuestion()" id="btn-back">Zurück</button>
            <button class="exam-btn exam-btn--mark" onclick="toggleMark()" id="btn-mark">Markieren</button>
            <button class="exam-btn exam-btn--next" onclick="nextQuestion()" id="btn-next">Weiter</button>
        </div>
    </div>
</div>
@else
{{-- Results section follows (see Task 3) --}}
@endif

@endsection
```

**Wichtig:** Die `<form>` Struktur, hidden inputs (`fragen_ids[]`, `answer_mappings[]`, `exam_token`), `@csrf` und `name="answer[index][]"` Attribute müssen exakt beibehalten werden — der Controller erwartet dieses Format.

- [ ] **Step 3: Prüfe dass alle Blade-Variablen korrekt referenziert werden**

Variablen vom Controller:
- `$fragen` — Collection, foreach als `$index => $frage`
- `$timeLeft` — int (Sekunden)
- `$examToken` — string
- `$answerMappings` — array
- `$submitted` — nur bei Results gesetzt

- [ ] **Step 4: Commit Active Exam HTML**

```bash
git add resources/views/exam.blade.php
git commit -m "🎨: Exam Active Mode HTML"
```

---

### Task 3: HTML — Result Summary + Review Mode

**Files:**
- Modify: `resources/views/exam.blade.php` (Results-Sektion)
- Modify: `app/Http/Controllers/ExamController.php:303-353` (`showResult` View-Name)

- [ ] **Step 1: Schreibe die Result Summary HTML**

Im `@else` Block (nach `@if(!isset($submitted))`):

```blade
@else
{{-- ============================================
     RESULT MODE
     ============================================ --}}
<div class="exam-shell exam-rise" id="exam-result-container">
    {{-- ── Result Topbar (Mobile — via CSS class statt inline style) --}}
    <div class="exam-topbar exam-topbar--result">
        <a href="{{ route('exam.history') }}" style="color: rgba(255,255,255,0.5); font-size: 0.6875rem; text-decoration: none;">Schliessen</a>
        <div class="exam-topbar-label">ERGEBNIS</div>
        <div style="width: 3.5rem;"></div>
    </div>

    {{-- ── Desktop Header ─────────────────────── --}}
    <div class="exam-header" style="text-align: center;">
        <p class="exam-greeting">AUSWERTUNG</p>
        <h1 class="exam-title" style="justify-content: center;">
            Prüfung <span style="color: {{ $passed ? '#22c55e' : '#ef4444' }};">{{ $passed ? 'Bestanden' : 'Nicht bestanden' }}</span>
        </h1>
    </div>

    <div class="exam-content" style="overflow-y: auto;">
        {{-- ── Summary View ─────────────────────── --}}
        <div id="result-summary">
            <div class="exam-result-summary">
                {{-- Result Ring --}}
                @php
                    $percentage = round(($correctCount / $total) * 100);
                    $circumference = 2 * M_PI * 50;
                    $offset = $circumference - ($percentage / 100) * $circumference;
                    $statusClass = $passed ? 'passed' : 'failed';
                @endphp
                <div class="exam-result-ring">
                    <svg viewBox="0 0 120 120">
                        <circle class="ring-track" cx="60" cy="60" r="50"/>
                        <circle class="ring-fill {{ $statusClass }}" cx="60" cy="60" r="50"
                            stroke-dasharray="{{ $circumference }}"
                            stroke-dashoffset="{{ $circumference }}"
                            data-target-offset="{{ $offset }}"/>
                    </svg>
                    <div class="exam-result-center">
                        <div class="exam-result-percent {{ $statusClass }}">{{ $percentage }}%</div>
                        <div class="exam-result-count">{{ $correctCount }}/{{ $total }}</div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="exam-result-status {{ $statusClass }}">
                    {{ $passed ? 'Bestanden!' : 'Nicht bestanden' }}
                </div>
                <div class="exam-result-meta">
                    30:00 Min — 80% benötigt
                </div>

                {{-- XP Badge --}}
                @if(isset($gamification_result) && $gamification_result && isset($gamification_result['points_awarded']))
                    <div class="exam-xp-badge">
                        +{{ $gamification_result['points_awarded'] }} XP
                        @if(isset($gamification_result['level_up']) && $gamification_result['level_up'])
                            — Level Up!
                        @endif
                    </div>
                @endif

                {{-- Stats --}}
                <div class="exam-result-stats">
                    <div class="exam-result-stat exam-result-stat--correct">
                        <div class="stat-value">{{ $correctCount }}</div>
                        <div class="stat-label">Richtig</div>
                    </div>
                    <div class="exam-result-stat exam-result-stat--wrong">
                        <div class="stat-value">{{ $total - $correctCount }}</div>
                        <div class="stat-label">Falsch</div>
                    </div>
                </div>

                {{-- Weakness Analysis --}}
                @php
                    $sectionResults = [];
                    foreach ($results as $r) {
                        $la = $r['frage']->lernabschnitt;
                        if (!isset($sectionResults[$la])) {
                            $sectionResults[$la] = ['correct' => 0, 'total' => 0];
                        }
                        $sectionResults[$la]['total']++;
                        if ($r['isCorrect']) $sectionResults[$la]['correct']++;
                    }
                    $weakSections = collect($sectionResults)
                        ->map(fn($v, $k) => ['la' => $k, 'correct' => $v['correct'], 'total' => $v['total'], 'pct' => $v['total'] > 0 ? round($v['correct'] / $v['total'] * 100) : 100])
                        ->filter(fn($v) => $v['pct'] < 75)
                        ->sortBy('pct');
                @endphp

                @if($weakSections->isNotEmpty())
                    <div class="exam-weakness-card">
                        <div class="exam-weakness-title">Schwachstellen</div>
                        @foreach($weakSections as $ws)
                            <div class="exam-weakness-item">
                                <div class="exam-weakness-la" style="color: {{ $ws['pct'] < 50 ? '#ef4444' : '#f59e0b' }};">LA {{ $ws['la'] }}</div>
                                <div class="exam-weakness-bar">
                                    <div class="exam-weakness-fill {{ $ws['pct'] < 50 ? 'bad' : 'medium' }}" style="width: {{ $ws['pct'] }}%"></div>
                                </div>
                                <div class="exam-weakness-score">{{ $ws['correct'] }}/{{ $ws['total'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="exam-result-actions">
                    <button class="exam-result-btn exam-result-btn--primary" onclick="startReview('all')">
                        Alle Fragen durchgehen
                    </button>
                    @if($total - $correctCount > 0)
                        <button class="exam-result-btn exam-result-btn--wrong" onclick="startReview('wrong')">
                            Nur falsche Fragen ({{ $total - $correctCount }})
                        </button>
                    @endif
                    <a href="{{ route('exam.history') }}" class="exam-result-btn exam-result-btn--close">
                        Zur Prüfungshistorie
                    </a>
                </div>
            </div>
        </div>

        {{-- ── Review View (hidden initially) ───── --}}
        <div id="result-review" style="display: none;">
            @foreach($results as $index => $result)
                <div class="exam-slide" data-review="{{ $index }}">
                    <div class="exam-question-card {{ $result['isCorrect'] ? 'result-correct' : 'result-wrong' }}">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                            <div class="exam-question-la">LA {{ $result['frage']->lernabschnitt ?? '-' }}.{{ $result['frage']->nummer ?? '-' }}</div>
                            <div class="exam-result-badge {{ $result['isCorrect'] ? 'correct' : 'wrong' }}">
                                {{ $result['isCorrect'] ? 'RICHTIG' : 'FALSCH' }}
                            </div>
                        </div>
                        <div class="exam-question-text">{{ $result['frage']->frage }}</div>
                    </div>

                    <div class="exam-answers">
                        @php
                            $mapping = $result['mapping'] ?? [0 => 'A', 1 => 'B', 2 => 'C'];
                            $solution = $result['solution'];
                            $userAnswer = $result['userAnswer'];
                        @endphp
                        @foreach($mapping as $position => $letter)
                            @php
                                $answerField = 'antwort_' . strtolower($letter);
                                $answerText = $result['frage']->$answerField;
                                $isCorrectAnswer = $solution->contains($letter);
                                $wasSelected = $userAnswer && $userAnswer->contains($letter);

                                if ($wasSelected && $isCorrectAnswer) {
                                    $answerClass = 'result-correct';
                                    $checkContent = '✓';
                                } elseif ($wasSelected && !$isCorrectAnswer) {
                                    $answerClass = 'result-wrong';
                                    $checkContent = '✕';
                                } elseif (!$wasSelected && $isCorrectAnswer) {
                                    $answerClass = 'result-missed';
                                    $checkContent = '✓';
                                } else {
                                    $answerClass = '';
                                    $checkContent = '';
                                }
                            @endphp
                            <div class="exam-answer {{ $answerClass }}" style="cursor: default;">
                                <div class="exam-checkbox">{{ $checkContent }}</div>
                                <span>{{ $answerText }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Review Bottom Bar (hidden initially) ── --}}
    <div class="exam-bottom-bar" id="review-bottom-bar" style="display: none;">
        <div class="exam-bubbles-track" id="review-bubbles"></div>
        <div class="exam-actions">
            <button class="exam-btn exam-btn--back" onclick="prevReview()" id="review-btn-back">Zurück</button>
            <button class="exam-btn exam-btn--next" onclick="nextReview()" id="review-btn-next">Weiter</button>
        </div>
    </div>
</div>
@endif
```

- [ ] **Step 2: Commit Results HTML**

`ExamController::showResult()` rendert bereits `exam` View — keine Controller-Änderung nötig.

```bash
git add resources/views/exam.blade.php
git commit -m "🎨: Exam Result Summary + Review HTML"
```

---

## Chunk 2: JavaScript — Navigation, Timer, Sliding Bubbles

### Task 4: JavaScript — Core State + Timer + Question Navigation

**Files:**
- Modify: `resources/views/exam.blade.php` (Script-Block am Ende)

- [ ] **Step 1: Schreibe den neuen JavaScript-Block**

Ersetze den gesamten `<script>` Block im Active-Exam Bereich:

```javascript
// ============================================
// EXAM - State Management & Core Logic
// ============================================

const TOTAL_QUESTIONS = {{ count($fragen) }};
const VISIBLE_BUBBLES = 9;
const CENTER_INDEX = 4; // 0-based middle of 9

// State
let currentQuestion = 0;
let answers = new Array(TOTAL_QUESTIONS).fill(false);
let marked = new Array(TOTAL_QUESTIONS).fill(false);
let timeLeft = {{ $timeLeft }};
let examSubmitting = false;

// DOM refs
const examForm = document.getElementById('exam-form');
const timerMobile = document.getElementById('exam-timer-mobile');
const timerDesktop = document.getElementById('exam-timer-desktop');
const progressBar = document.getElementById('exam-progress');
const topbarQnum = document.getElementById('topbar-qnum');
const desktopQnum = document.getElementById('desktop-qnum');
const bubblesTrack = document.getElementById('exam-bubbles');
const btnBack = document.getElementById('btn-back');
const btnMark = document.getElementById('btn-mark');
const btnNext = document.getElementById('btn-next');
const statAnswered = document.getElementById('stat-answered');
const statOpen = document.getElementById('stat-open');
const statMarked = document.getElementById('stat-marked');
const statTimerPill = document.getElementById('stat-timer-pill');

// ── Timer ─────────────────────────────────
function formatTime(seconds) {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return m.toString().padStart(2, '0') + ':' + s.toString().padStart(2, '0');
}

function updateTimer() {
    if (timeLeft <= 0) {
        submitExam();
        return;
    }
    timeLeft--;
    const display = formatTime(timeLeft);

    if (timerMobile) timerMobile.textContent = display;
    if (timerDesktop) timerDesktop.textContent = display;

    // Warning states
    const timerEls = document.querySelectorAll('.exam-timer');
    timerEls.forEach(el => {
        el.classList.toggle('warning', timeLeft < 600 && timeLeft >= 300);
        el.classList.toggle('critical', timeLeft < 300);
    });

    if (statTimerPill) {
        statTimerPill.classList.toggle('warning', timeLeft < 600);
    }
}

const timerInterval = setInterval(updateTimer, 1000);

// ── Question Navigation ──────────────────
function showQuestion(index) {
    if (index < 0 || index >= TOTAL_QUESTIONS) return;
    currentQuestion = index;

    // Hide all slides, show current
    document.querySelectorAll('.exam-slide').forEach(s => s.style.display = 'none');
    const slide = document.querySelector(`.exam-slide[data-question="${index}"]`);
    if (slide) slide.style.display = 'block';

    // Update question numbers
    const qNum = index + 1;
    if (topbarQnum) topbarQnum.textContent = qNum;
    if (desktopQnum) desktopQnum.textContent = qNum;

    // Update progress
    const pct = ((index + 1) / TOTAL_QUESTIONS * 100).toFixed(1);
    if (progressBar) progressBar.style.width = pct + '%';

    // Update buttons
    if (btnBack) btnBack.style.visibility = index === 0 ? 'hidden' : 'visible';
    if (btnMark) btnMark.classList.toggle('active', marked[index]);

    // Update next/submit button
    if (btnNext) {
        if (index === TOTAL_QUESTIONS - 1) {
            btnNext.textContent = 'Abgeben';
            btnNext.classList.remove('exam-btn--next');
            btnNext.classList.add('exam-btn--submit');
        } else {
            btnNext.textContent = 'Weiter';
            btnNext.classList.remove('exam-btn--submit');
            btnNext.classList.add('exam-btn--next');
        }
    }

    updateBubbles();
    updateStats();

    // Scroll content to top
    const content = document.querySelector('.exam-content');
    if (content) content.scrollTop = 0;
}

function nextQuestion() {
    if (currentQuestion === TOTAL_QUESTIONS - 1) {
        // Last question — confirm submit
        if (confirm('Prüfung abgeben? Du hast ' + answers.filter(Boolean).length + ' von ' + TOTAL_QUESTIONS + ' Fragen beantwortet.')) {
            submitExam();
        }
        return;
    }
    showQuestion(currentQuestion + 1);
}

function prevQuestion() {
    showQuestion(currentQuestion - 1);
}

// ── Answer Handling ──────────────────────
function toggleAnswer(questionIndex, position) {
    const slide = document.querySelector(`.exam-slide[data-question="${questionIndex}"]`);
    const label = slide.querySelector(`.exam-answer[data-position="${position}"]`);
    const checkbox = label.querySelector('input[type="checkbox"]');

    checkbox.checked = !checkbox.checked;
    label.classList.toggle('selected', checkbox.checked);

    // Update answered state
    const anyChecked = slide.querySelectorAll('input[type="checkbox"]:checked').length > 0;
    answers[questionIndex] = anyChecked;

    updateBubbles();
    updateStats();
}

// ── Mark Handling ──────────────────────
function toggleMark() {
    marked[currentQuestion] = !marked[currentQuestion];
    if (btnMark) btnMark.classList.toggle('active', marked[currentQuestion]);
    updateBubbles();
    updateStats();
}
```

- [ ] **Step 2: Commit Core JS**

```bash
git add resources/views/exam.blade.php
git commit -m "⚡: Exam Core JS (Timer, Navigation, Answers)"
```

---

### Task 5: JavaScript — Sliding Bubbles

**Files:**
- Modify: `resources/views/exam.blade.php` (nach Core JS)

- [ ] **Step 1: Implementiere die Sliding-Bubbles Logik**

```javascript
// ============================================
// SLIDING BUBBLES — 9 visible, active centered
// ============================================

function getSlidingWindow(current, total, windowSize) {
    const center = Math.floor(windowSize / 2); // 4 for window of 9

    let start;
    if (current <= center) {
        // Beginning: window starts at 0
        start = 0;
    } else if (current >= total - windowSize + center) {
        // End: window fixed at end
        start = total - windowSize;
    } else {
        // Middle: current is centered
        start = current - center;
    }

    return { start, end: start + windowSize };
}

function updateBubbles() {
    if (!bubblesTrack) return;

    const { start, end } = getSlidingWindow(currentQuestion, TOTAL_QUESTIONS, VISIBLE_BUBBLES);

    bubblesTrack.innerHTML = '';
    for (let i = start; i < end; i++) {
        const bubble = document.createElement('button');
        bubble.className = 'exam-bubble';
        bubble.textContent = i + 1;
        bubble.setAttribute('type', 'button');
        bubble.onclick = () => showQuestion(i);

        if (i === currentQuestion) {
            bubble.classList.add('exam-bubble--active');
        } else if (marked[i]) {
            bubble.classList.add('exam-bubble--marked');
        } else if (answers[i]) {
            bubble.classList.add('exam-bubble--answered');
        } else {
            bubble.classList.add('exam-bubble--open');
        }

        bubblesTrack.appendChild(bubble);
    }
}

// ── Stats Update ──────────────────────
function updateStats() {
    const answeredCount = answers.filter(Boolean).length;
    const markedCount = marked.filter(Boolean).length;
    const openCount = TOTAL_QUESTIONS - answeredCount;

    if (statAnswered) statAnswered.textContent = answeredCount;
    if (statOpen) statOpen.textContent = openCount;
    if (statMarked) statMarked.textContent = markedCount;
}

// ── Submit ──────────────────────────
function submitExam() {
    if (examSubmitting) return;
    examSubmitting = true;
    clearInterval(timerInterval);
    examForm.submit();
}

// ── Exit Protection ──────────────────
function handleExamExit(e) {
    e.preventDefault();
    if (confirm('Prüfung wirklich abbrechen? Dein Fortschritt geht verloren.')) {
        window.location.href = '/';
    }
}

// Prevent accidental navigation
window.addEventListener('beforeunload', function(e) {
    if (!examSubmitting) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Prevent back button
history.pushState(null, '', location.href);
window.addEventListener('popstate', function() {
    history.pushState(null, '', location.href);
});

// ── Initialize ──────────────────────
// (exam-active-mode wird per inline-script am Anfang gesetzt, kein FOUC)
showQuestion(0);
updateTimer();
```

- [ ] **Step 2: Commit Sliding Bubbles JS**

```bash
git add resources/views/exam.blade.php
git commit -m "⚡: Exam Sliding Bubbles Navigation"
```

---

### Task 6: JavaScript — Result Review Mode

**Files:**
- Modify: `resources/views/exam.blade.php` (Script-Block im Results-Bereich)

- [ ] **Step 1: Implementiere die Review-Logik**

Im `@else` Block, am Ende vor `@endif`:

```html
@push('scripts')
<script>
// ============================================
// RESULT REVIEW MODE
// ============================================

const reviewResults = @json(collect($results)->map(fn($r, $i) => ['index' => $i, 'isCorrect' => $r['isCorrect']])->values());
let reviewMode = null; // 'all' or 'wrong'
let reviewQuestions = []; // indices to show
let reviewCurrent = 0;

const reviewContainer = document.getElementById('result-review');
const summaryContainer = document.getElementById('result-summary');
const reviewBottomBar = document.getElementById('review-bottom-bar');
const reviewBubbles = document.getElementById('review-bubbles');

function startReview(mode) {
    reviewMode = mode;
    if (mode === 'all') {
        reviewQuestions = reviewResults.map((_, i) => i);
    } else {
        reviewQuestions = reviewResults.filter(r => !r.isCorrect).map(r => r.index);
    }
    reviewCurrent = 0;

    summaryContainer.style.display = 'none';
    reviewContainer.style.display = 'block';
    reviewBottomBar.style.display = 'block';

    // Update topbar for review mode
    const topbar = document.querySelector('.exam-topbar');
    if (topbar) {
        topbar.querySelector('.exam-topbar-label').textContent = 'ERGEBNIS — {{ round(($correctCount / $total) * 100) }}%';
    }

    showReviewQuestion(0);
}

function showReviewQuestion(idx) {
    if (idx < 0 || idx >= reviewQuestions.length) return;
    reviewCurrent = idx;

    // Hide all, show current
    document.querySelectorAll('[data-review]').forEach(s => s.style.display = 'none');
    const slide = document.querySelector(`[data-review="${reviewQuestions[idx]}"]`);
    if (slide) slide.style.display = 'block';

    // Update topbar title
    const topbarTitle = document.querySelector('.exam-topbar-title');
    if (topbarTitle) {
        topbarTitle.innerHTML = `Frage <span class="exam-qnum">${idx + 1}</span> <span class="exam-qtotal">/ ${reviewQuestions.length}</span>`;
    }

    // Update buttons
    const btnBack = document.getElementById('review-btn-back');
    const btnNext = document.getElementById('review-btn-next');
    if (btnBack) btnBack.style.visibility = idx === 0 ? 'hidden' : 'visible';
    if (btnNext) btnNext.textContent = idx === reviewQuestions.length - 1 ? 'Fertig' : 'Weiter';

    updateReviewBubbles();
}

function nextReview() {
    if (reviewCurrent === reviewQuestions.length - 1) {
        // Back to summary
        reviewContainer.style.display = 'none';
        reviewBottomBar.style.display = 'none';
        summaryContainer.style.display = 'block';
        return;
    }
    showReviewQuestion(reviewCurrent + 1);
}

function prevReview() {
    showReviewQuestion(reviewCurrent - 1);
}

function updateReviewBubbles() {
    if (!reviewBubbles) return;

    const total = reviewQuestions.length;
    const windowSize = Math.min(total, 9);
    const center = Math.floor(windowSize / 2);

    let start;
    if (total <= 9) {
        start = 0;
    } else if (reviewCurrent <= center) {
        start = 0;
    } else if (reviewCurrent >= total - windowSize + center) {
        start = total - windowSize;
    } else {
        start = reviewCurrent - center;
    }
    const end = Math.min(start + windowSize, total);

    reviewBubbles.innerHTML = '';
    for (let i = start; i < end; i++) {
        const bubble = document.createElement('button');
        bubble.className = 'exam-bubble';
        bubble.textContent = i + 1;
        bubble.setAttribute('type', 'button');
        bubble.onclick = () => showReviewQuestion(i);

        const originalIndex = reviewQuestions[i];
        if (i === reviewCurrent) {
            bubble.classList.add('exam-bubble--active');
        } else if (reviewResults[originalIndex].isCorrect) {
            bubble.classList.add('exam-bubble--correct');
        } else {
            bubble.classList.add('exam-bubble--wrong');
        }

        reviewBubbles.appendChild(bubble);
    }
}

// ── Result Ring Animation ──────────────
document.addEventListener('DOMContentLoaded', function() {
    const ringFill = document.querySelector('.ring-fill');
    if (ringFill) {
        const targetOffset = ringFill.getAttribute('data-target-offset');
        setTimeout(() => {
            ringFill.style.strokeDashoffset = targetOffset;
        }, 100);
    }
});
</script>
@endpush
```

- [ ] **Step 2: Commit Review JS**

```bash
git add resources/views/exam.blade.php
git commit -m "⚡: Exam Result Review Mode JS"
```

---

## Chunk 3: Cleanup + Verification

### Task 7: Legacy File Removal

**Files:**
- Delete: `resources/views/exam_result.blade.php`

- [ ] **Step 1: Prüfe ob exam_result.blade.php noch referenziert wird**

```bash
grep -r "exam_result" app/ resources/ routes/ --include="*.php" --include="*.blade.php"
```

Falls nur in `ExamController::showResult()` → sicher zu löschen nach View-Name Änderung.

- [ ] **Step 2: Lösche Legacy-Datei falls sicher**

```bash
rm resources/views/exam_result.blade.php
```

- [ ] **Step 3: Commit Cleanup**

```bash
git add -A
git commit -m "🗑️: Legacy exam_result View entfernt"
```

---

### Task 8: Build + Cache Clear + Manueller Test

- [ ] **Step 1: Build & Clear Caches**

```bash
npm run build && php artisan view:clear && php artisan cache:clear
```

- [ ] **Step 2: Manueller Test-Checkliste**

Öffne die App im Browser und teste:

**Aktive Prüfung:**
- [ ] `/exam` lädt ohne Fehler
- [ ] Mobile: Topbar mit Timer (Gold), Frage-Nummer (Blau), Close-Button
- [ ] Mobile: Progress-Bar (Blau-Gradient) unter Topbar
- [ ] Mobile: Question Card mit blauer Top-Line, LA-Label
- [ ] Mobile: Antworten klickbar, Checkbox wird blau
- [ ] Mobile: Fixed Bottom Bar mit 9 Sliding Bubbles
- [ ] Mobile: Zurück/Markieren/Weiter Buttons
- [ ] Desktop: dash-container Layout mit Navbar
- [ ] Desktop: Header "PRÜFUNGSSIMULATION" + Stat-Pills
- [ ] Desktop: Sliding Bubbles inline (nicht fixed)
- [ ] Sliding: Frage 1 → Punkt links, Frage 20 → Punkt Mitte, Frage 40 → Punkt rechts
- [ ] Markieren: Gold-Bubble, Button-Highlight
- [ ] Timer: Gold → Rot bei <10 Min → Puls bei <5 Min
- [ ] Frage 40: "Weiter" wird zu "Abgeben" (Grün)
- [ ] Abgeben: Bestätigungs-Dialog
- [ ] Light Mode: Alle Farben korrekt umgeschaltet

**Ergebnis:**
- [ ] Summary: Ring-Animation, Prozent, Status (Bestanden/Nicht bestanden)
- [ ] Summary: XP-Badge (falls bestanden)
- [ ] Summary: Stats-Pills (Richtig/Falsch)
- [ ] Summary: Schwachstellen-Analyse (nur schwache LAs)
- [ ] "Alle Fragen durchgehen": Review-Modus mit Grün/Rot Bubbles
- [ ] "Nur falsche Fragen": Nur falsche Fragen, Bubble-Anzahl = Anzahl Fehler
- [ ] Review: Korrekte Antworten grün, falsche rot, verpasste gedimmt
- [ ] "Fertig" → zurück zum Summary
- [ ] "Schliessen" / "Zur Prüfungshistorie" → /exam-history
- [ ] Light Mode: Ergebnis korrekt dargestellt

- [ ] **Step 3: Final Commit**

```bash
git add -A
git commit -m "🎨: Exam Redesign komplett"
```
