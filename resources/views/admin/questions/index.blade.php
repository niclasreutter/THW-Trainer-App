@extends('layouts.app')
@section('title', 'Fragen-Editor - THW Trainer Admin')
@section('description', 'Bearbeite alle Fragen des THW-Trainer im Live-Editor.')

@push('styles')
<style>
/* =========================================================
   FRAGEN-EDITOR · admin page
   Dark-mode glassmorphism, eingebettet in das Admin-Layout.
   ========================================================= */

/* Vollbild: Hauptbereich + Footer ausblenden, Padding entfernen */
body.fq-fullbleed main#main-content { padding: 0 !important; overflow: hidden !important; }
body.fq-fullbleed footer.footer-glass { display: none !important; }
body.fq-fullbleed .min-h-screen { min-height: 100vh; }

/* 2-Pane: rail + editor (sidebar kommt aus dem Layout) */
.fq-shell {
    display: grid;
    grid-template-columns: 360px 1fr;
    height: calc(100vh - 0px);
    background: var(--bg-base);
    color: var(--text-primary);
    overflow: hidden;
}

/* ---- Mittlere Rail: Fragenliste ---- */
.fq-rail {
    display: flex;
    flex-direction: column;
    background: var(--bg-elevated);
    border-right: 1px solid var(--glass-border-lo, rgba(255,255,255,0.06));
    min-height: 0;
    overflow: hidden;
}
.fq-rail__head {
    padding: 1rem 1.125rem 0.75rem;
    border-bottom: 1px solid var(--glass-border-lo, rgba(255,255,255,0.06));
    flex-shrink: 0;
}
.fq-rail__title-row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}
.fq-rail__title {
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--text-muted);
}
.fq-rail__count {
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    color: var(--text-muted);
}

.fq-search {
    position: relative;
    margin-bottom: 0.5rem;
}
.fq-search > i {
    position: absolute;
    left: 0.625rem; top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.875rem;
    pointer-events: none;
}
.fq-search input {
    width: 100%;
    padding: 0.5rem 0.5rem 0.5rem 2rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 0.5rem;
    color: var(--text-primary);
    font-size: 0.8125rem;
    outline: none;
    transition: border-color 150ms ease;
}
.fq-search input:focus {
    border-color: var(--thw-blue-glow, #5b9aff);
    background: rgba(255,255,255,0.06);
}
.fq-search input::placeholder { color: var(--text-muted); }

.fq-seg {
    display: flex;
    gap: 2px;
    padding: 2px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 0.5rem;
}
.fq-seg button {
    flex: 1;
    padding: 0.375rem 0.5rem;
    border: 0;
    background: transparent;
    color: var(--text-muted);
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 150ms ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
}
.fq-seg button:hover { color: var(--text-primary); }
.fq-seg button.active {
    background: var(--bg-elevated, #121214);
    color: var(--text-primary);
    box-shadow: 0 1px 2px rgba(0,0,0,0.20);
}
.fq-seg button .count {
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    color: var(--text-muted);
    font-weight: 500;
}
.fq-seg button.active .count { color: var(--text-secondary); }

.fq-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
    margin-top: 0.625rem;
}
.fq-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.2rem 0.5rem;
    border-radius: 999px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.06);
    color: var(--text-muted);
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 150ms ease;
}
.fq-chip:hover { color: var(--text-primary); border-color: rgba(255,255,255,0.10); }
.fq-chip.active {
    background: rgba(91,154,255,0.14);
    border-color: rgba(91,154,255,0.30);
    color: var(--thw-blue-glow, #5b9aff);
}

.fq-list {
    flex: 1;
    overflow-y: auto;
    padding: 0.375rem 0.5rem 4rem;
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.06) transparent;
}
.fq-list::-webkit-scrollbar { width: 6px; }
.fq-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.06); border-radius: 3px; }

.fq-list-item {
    display: grid;
    grid-template-columns: auto 1fr auto;
    grid-template-rows: auto auto;
    column-gap: 0.625rem;
    row-gap: 0.25rem;
    padding: 0.625rem;
    border-radius: 0.625rem;
    cursor: pointer;
    border: 1px solid transparent;
    transition: background 150ms ease, border-color 150ms ease;
    margin-bottom: 0.125rem;
    position: relative;
}
.fq-list-item:hover { background: rgba(255,255,255,0.05); }
.fq-list-item.active {
    background: rgba(91,154,255,0.10);
    border-color: rgba(91,154,255,0.25);
}
.fq-list-item.active::before {
    content: '';
    position: absolute;
    left: -0.5rem; top: 50%;
    transform: translateY(-50%);
    width: 3px; height: 60%;
    background: var(--thw-blue-glow, #5b9aff);
    border-radius: 0 2px 2px 0;
}
.fq-list-num {
    grid-row: 1 / span 2;
    align-self: start;
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--text-muted);
    min-width: 2.25rem;
    padding-top: 0.125rem;
}
.fq-list-item.active .fq-list-num { color: var(--thw-blue-glow, #5b9aff); }
.fq-list-text {
    grid-column: 2;
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--text-primary);
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.fq-list-meta {
    grid-column: 2;
    display: flex;
    flex-wrap: wrap;
    gap: 0.375rem;
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.625rem;
    color: var(--text-muted);
    align-items: center;
}
.fq-list-meta .dot {
    width: 3px; height: 3px;
    border-radius: 50%;
    background: currentColor;
    opacity: 0.5;
}
.fq-list-status {
    grid-row: 1 / span 2;
    align-self: center;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
}
.fq-status-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}
.fq-status-dot--published { background: var(--success, #22c55e); }
.fq-status-dot--draft     { background: var(--warning, #f59e0b); }
.fq-status-dot--review    { background: var(--thw-blue-glow, #5b9aff); }
.fq-list-success {
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.625rem;
    font-weight: 700;
    color: var(--text-muted);
}
.fq-list-success.high { color: var(--success, #22c55e); }
.fq-list-success.low  { color: var(--warning, #f59e0b); }
.fq-list-success.crit { color: var(--error, #ef4444); }

/* difficulty mark */
.fq-diff { display: inline-flex; align-items: center; gap: 1px; }
.fq-diff i {
    width: 4px; height: 8px;
    border-radius: 1px;
    background: rgba(255,255,255,0.06);
}
.fq-diff--easy i:nth-child(1) { background: var(--success, #22c55e); }
.fq-diff--medium i:nth-child(1),
.fq-diff--medium i:nth-child(2) { background: var(--warning, #f59e0b); }
.fq-diff--hard i:nth-child(1),
.fq-diff--hard i:nth-child(2),
.fq-diff--hard i:nth-child(3) { background: var(--error, #ef4444); }

.fq-rail__foot {
    padding: 0.75rem;
    border-top: 1px solid rgba(255,255,255,0.06);
    background: var(--bg-elevated);
}
.fq-newbtn {
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.625rem 0.875rem;
    border-radius: 0.5rem;
    background: var(--thw-blue, #00337F);
    color: #fff;
    border: 0;
    font-size: 0.8125rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 150ms ease;
    box-shadow: 0 4px 15px rgba(0, 51, 127, 0.40);
}
.fq-newbtn:hover {
    background: var(--thw-blue-light, #004db3);
    transform: translateY(-1px);
}

/* =========================================================
   EDITOR
   ========================================================= */
.fq-editor {
    display: flex;
    flex-direction: column;
    min-height: 0;
    overflow: hidden;
    background: var(--bg-base);
}
.fq-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.875rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    background: var(--bg-elevated);
    flex-shrink: 0;
    gap: 1rem;
}
.fq-topbar__left {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    min-width: 0;
    flex: 1;
}
.fq-crumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
}
.fq-crumb b { color: var(--text-primary); font-weight: 700; }
.fq-crumb-sep { opacity: 0.5; }
.fq-q-id {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.625rem;
    border-radius: 0.375rem;
    background: rgba(91,154,255,0.12);
    color: var(--thw-blue-glow, #5b9aff);
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.04em;
}
.fq-topbar__right {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}

.fq-save-status {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 0.25rem 0.625rem;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.06);
    background: rgba(255,255,255,0.05);
}
.fq-save-status .save-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--success, #22c55e);
    flex-shrink: 0;
}
.fq-save-status[data-status="saving"] .save-dot {
    background: var(--warning, #f59e0b);
    animation: fq-pulse-dot 0.9s ease-in-out infinite;
}
.fq-save-status[data-status="error"] .save-dot { background: var(--error, #ef4444); }
.fq-save-status[data-status="dirty"] .save-dot { background: var(--warning, #f59e0b); }
@keyframes fq-pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(0.8); }
}

.fq-iconbtn {
    width: 34px; height: 34px;
    border-radius: 0.5rem;
    background: transparent;
    border: 1px solid rgba(255,255,255,0.06);
    color: var(--text-secondary);
    display: grid;
    place-items: center;
    cursor: pointer;
    transition: all 150ms ease;
    font-size: 0.95rem;
}
.fq-iconbtn:hover {
    border-color: var(--thw-blue-glow, #5b9aff);
    color: var(--thw-blue-glow, #5b9aff);
    background: rgba(91,154,255,0.06);
}
.fq-iconbtn.danger:hover {
    border-color: var(--error, #ef4444);
    color: var(--error, #ef4444);
    background: rgba(239,68,68,0.06);
}

.fq-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.475rem 0.875rem;
    border-radius: 0.5rem;
    background: transparent;
    border: 1px solid rgba(255,255,255,0.06);
    color: var(--text-secondary);
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 150ms ease;
}
.fq-btn:hover { color: var(--text-primary); border-color: rgba(255,255,255,0.10); background: rgba(255,255,255,0.05); }
.fq-btn--primary {
    background: var(--thw-blue, #00337F);
    color: #fff;
    border-color: var(--thw-blue, #00337F);
    box-shadow: 0 4px 15px rgba(0, 51, 127, 0.40);
}
.fq-btn--primary:hover {
    background: var(--thw-blue-light, #004db3);
    color: #fff;
    border-color: var(--thw-blue-light, #004db3);
    box-shadow: 0 8px 25px rgba(0, 51, 127, 0.50);
}

.fq-nav {
    display: inline-flex;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 0.5rem;
    overflow: hidden;
}
.fq-nav button {
    width: 32px; height: 32px;
    background: transparent;
    border: 0;
    color: var(--text-secondary);
    cursor: pointer;
    transition: background 150ms ease;
}
.fq-nav button:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }
.fq-nav button + button { border-left: 1px solid rgba(255,255,255,0.06); }

/* editor body */
.fq-body {
    flex: 1;
    display: grid;
    grid-template-columns: 1fr 360px;
    min-height: 0;
    overflow: hidden;
}
.fq-main {
    overflow-y: auto;
    padding: 1.75rem 2rem 4rem;
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.06) transparent;
}
.fq-main::-webkit-scrollbar { width: 8px; }
.fq-main::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.06); border-radius: 4px; }

.fq-aside {
    border-left: 1px solid rgba(255,255,255,0.06);
    background: var(--bg-elevated);
    overflow-y: auto;
    padding: 1.5rem 1.5rem 4rem;
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.06) transparent;
}

.fq-field { margin-bottom: 1.25rem; }
.fq-field__label-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.4rem;
}
.fq-field__label {
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--text-muted);
}
.fq-field__hint {
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    color: var(--text-muted);
    opacity: 0.8;
}

.fq-question-input {
    width: 100%;
    min-height: 96px;
    padding: 1rem 1.125rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.10);
    border-radius: 0.875rem;
    color: var(--text-primary);
    font-size: 1.125rem;
    font-weight: 600;
    line-height: 1.45;
    resize: vertical;
    outline: none;
    transition: all 150ms ease;
    font-family: inherit;
}
.fq-question-input:focus {
    border-color: var(--thw-blue-glow, #5b9aff);
    background: rgba(255,255,255,0.06);
}

.fq-answers { display: flex; flex-direction: column; gap: 0.625rem; }
.fq-answer-row {
    display: grid;
    grid-template-columns: 44px 1fr auto;
    align-items: stretch;
    gap: 0.625rem;
    padding: 0.625rem 0.75rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.10);
    border-radius: 0.875rem;
    transition: all 150ms ease;
}
.fq-answer-row:focus-within { border-color: var(--thw-blue-glow, #5b9aff); }
.fq-answer-row.correct {
    border-color: rgba(34,197,94,0.40);
    background: linear-gradient(0deg, rgba(34,197,94,0.04), rgba(34,197,94,0.04)), rgba(255,255,255,0.05);
}
.fq-letter {
    display: grid;
    place-items: center;
    width: 44px;
    align-self: stretch;
    border-radius: 0.5rem;
    background: var(--bg-overlay, #222226);
    color: var(--text-secondary);
    font-family: var(--font-display, "Barlow Condensed");
    font-weight: 800;
    font-size: 1.5rem;
    letter-spacing: -0.02em;
    transition: all 150ms ease;
}
.fq-answer-row.correct .fq-letter { background: var(--success, #22c55e); color: #fff; }
.fq-answer-input {
    width: 100%;
    align-self: center;
    padding: 0.5rem 0;
    background: transparent;
    border: 0;
    color: var(--text-primary);
    font-size: 0.9375rem;
    line-height: 1.4;
    resize: none;
    outline: none;
    min-height: 1.4em;
    font-family: inherit;
}
.fq-correct-toggle {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.75rem 0.375rem 0.5rem;
    border-radius: 999px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.06);
    color: var(--text-muted);
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    cursor: pointer;
    align-self: center;
    transition: all 150ms ease;
    user-select: none;
    white-space: nowrap;
}
.fq-correct-toggle:hover { color: var(--text-primary); border-color: rgba(255,255,255,0.10); }
.fq-answer-row.correct .fq-correct-toggle {
    background: rgba(34,197,94,0.18);
    border-color: rgba(34,197,94,0.40);
    color: var(--success, #22c55e);
}
.fq-correct-toggle .check-icon {
    width: 14px; height: 14px;
    border-radius: 50%;
    background: rgba(255,255,255,0.10);
    display: grid;
    place-items: center;
    color: transparent;
    font-size: 0.625rem;
    transition: all 150ms ease;
}
.fq-answer-row.correct .fq-correct-toggle .check-icon { background: var(--success, #22c55e); color: #fff; }

.fq-correct-summary {
    margin-top: 0.625rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    color: var(--text-muted);
}
.fq-correct-summary .badge-ok { color: var(--success, #22c55e); }
.fq-correct-summary .badge-warn { color: var(--warning, #f59e0b); }

.fq-validation {
    display: flex;
    align-items: flex-start;
    gap: 0.625rem;
    padding: 0.625rem 0.875rem;
    border-radius: 0.625rem;
    margin-top: 0.875rem;
    font-size: 0.8125rem;
    color: var(--warning, #f59e0b);
    background: rgba(245,158,11,0.08);
    border: 1px solid rgba(245,158,11,0.25);
}
.fq-validation.error {
    color: var(--error, #ef4444);
    background: rgba(239,68,68,0.08);
    border-color: rgba(239,68,68,0.25);
}
.fq-validation.ok {
    color: var(--success, #22c55e);
    background: rgba(34,197,94,0.06);
    border-color: rgba(34,197,94,0.20);
}
.fq-validation i { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }

.fq-explanation-card {
    margin-top: 1.5rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 1rem;
    padding: 1.125rem 1.25rem;
    position: relative;
    overflow: hidden;
}
.fq-explanation-card::before {
    content: '';
    position: absolute;
    inset: 0 0 auto 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--gold-start, #fbbf24), transparent);
}
.fq-explanation-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.625rem;
    gap: 0.625rem;
    flex-wrap: wrap;
}
.fq-explanation-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--text-muted);
}
.fq-explanation-title i { color: var(--gold, #fbbf24); font-size: 0.875rem; }
.fq-explanation-input {
    width: 100%;
    min-height: 96px;
    padding: 0.625rem 0;
    background: transparent;
    border: 0;
    color: var(--text-primary);
    font-size: 0.9375rem;
    line-height: 1.55;
    resize: vertical;
    outline: none;
    font-family: inherit;
}

.fq-ai-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.75rem;
    border-radius: 999px;
    background: linear-gradient(135deg, rgba(167,139,250,0.18), rgba(91,154,255,0.18));
    border: 1px solid rgba(167,139,250,0.30);
    color: var(--purple, #a78bfa);
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    cursor: pointer;
    transition: all 150ms ease;
    white-space: nowrap;
}
.fq-ai-btn:hover {
    transform: translateY(-1px);
    border-color: rgba(167,139,250,0.50);
    background: linear-gradient(135deg, rgba(167,139,250,0.26), rgba(91,154,255,0.26));
}
.fq-ai-btn .bi { font-size: 0.875rem; }
.fq-ai-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
.fq-ai-btn.thinking i { animation: fq-ai-spin 1s linear infinite; }
@keyframes fq-ai-spin { to { transform: rotate(360deg); } }

.fq-ai-suggestion {
    margin-top: 0.625rem;
    padding: 0.875rem 1rem;
    border-radius: 0.75rem;
    background: linear-gradient(135deg, rgba(167,139,250,0.08), rgba(91,154,255,0.06));
    border: 1px dashed rgba(167,139,250,0.40);
    color: var(--text-primary);
    font-size: 0.875rem;
    line-height: 1.5;
    display: flex;
    flex-direction: column;
    gap: 0.625rem;
    animation: fq-fade-rise 0.4s ease;
}
@keyframes fq-fade-rise {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}
.fq-ai-suggestion__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--purple, #a78bfa);
}
.fq-ai-suggestion__actions { display: flex; gap: 0.375rem; }
.fq-ai-suggestion__btn {
    padding: 0.25rem 0.625rem;
    border-radius: 0.375rem;
    background: transparent;
    border: 1px solid rgba(167,139,250,0.30);
    color: var(--purple, #a78bfa);
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    cursor: pointer;
    transition: all 150ms ease;
}
.fq-ai-suggestion__btn:hover { background: rgba(167,139,250,0.10); }
.fq-ai-suggestion__btn--primary {
    background: var(--purple, #a78bfa);
    color: #fff;
    border-color: var(--purple, #a78bfa);
}
.fq-ai-suggestion__btn--primary:hover { background: #8b5cf6; }

/* Aside */
.fq-aside-section + .fq-aside-section {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.06);
}
.fq-aside-h {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.625rem;
}
.fq-aside-title {
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--text-muted);
}

.fq-input,
.fq-select {
    width: 100%;
    padding: 0.625rem 0.875rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 0.5rem;
    color: var(--text-primary);
    font-size: 0.875rem;
    font-weight: 500;
    outline: none;
    transition: border-color 150ms ease;
    font-family: inherit;
}
.fq-input { font-family: var(--font-mono, ui-monospace); font-weight: 600; }
.fq-input:focus,
.fq-select:focus { border-color: var(--thw-blue-glow, #5b9aff); }
.fq-select-wrap { position: relative; }
.fq-select {
    appearance: none;
    -webkit-appearance: none;
    padding-right: 2rem;
    cursor: pointer;
}
.fq-select-wrap > .fq-select-icon {
    position: absolute;
    right: 0.625rem; top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    pointer-events: none;
    font-size: 0.75rem;
}

.fq-diff-seg {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2px;
    padding: 2px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 0.5rem;
}
.fq-diff-seg button {
    padding: 0.5rem;
    border: 0;
    background: transparent;
    color: var(--text-muted);
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 150ms ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
}
.fq-diff-seg button:hover { color: var(--text-primary); }
.fq-diff-seg button.active {
    background: var(--bg-elevated, #121214);
    color: var(--text-primary);
    box-shadow: 0 1px 2px rgba(0,0,0,0.20);
}
.fq-diff-seg button .bars {
    display: inline-flex;
    align-items: flex-end;
    gap: 1.5px;
    height: 9px;
}
.fq-diff-seg button .bars i {
    width: 3px;
    border-radius: 1px;
    background: rgba(255,255,255,0.10);
}
.fq-diff-seg button .bars i:nth-child(1) { height: 3px; }
.fq-diff-seg button .bars i:nth-child(2) { height: 6px; }
.fq-diff-seg button .bars i:nth-child(3) { height: 9px; }
.fq-diff-seg button[data-val="easy"].active .bars i:nth-child(1) { background: var(--success, #22c55e); }
.fq-diff-seg button[data-val="medium"].active .bars i:nth-child(1),
.fq-diff-seg button[data-val="medium"].active .bars i:nth-child(2) { background: var(--warning, #f59e0b); }
.fq-diff-seg button[data-val="hard"].active .bars i { background: var(--error, #ef4444); }

.fq-status-pill-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.375rem;
}
.fq-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.3rem 0.625rem;
    border-radius: 999px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.06);
    color: var(--text-muted);
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    cursor: pointer;
    transition: all 150ms ease;
}
.fq-status-pill:hover { color: var(--text-primary); }
.fq-status-pill.active[data-val="published"] {
    background: rgba(34,197,94,0.16);
    border-color: rgba(34,197,94,0.35);
    color: var(--success, #22c55e);
}
.fq-status-pill.active[data-val="draft"] {
    background: rgba(245,158,11,0.16);
    border-color: rgba(245,158,11,0.35);
    color: var(--warning, #f59e0b);
}
.fq-status-pill.active[data-val="review"] {
    background: rgba(91,154,255,0.16);
    border-color: rgba(91,154,255,0.35);
    color: var(--thw-blue-glow, #5b9aff);
}

.fq-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
}
.fq-stat-tile {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 0.625rem;
    padding: 0.625rem 0.75rem;
}
.fq-stat-tile__val {
    font-weight: 800;
    font-size: 1.25rem;
    line-height: 1;
    color: var(--text-primary);
    letter-spacing: -0.01em;
}
.fq-stat-tile__val--ok { color: var(--success, #22c55e); }
.fq-stat-tile__val--warn { color: var(--warning, #f59e0b); }
.fq-stat-tile__val--err { color: var(--error, #ef4444); }
.fq-stat-tile__val--blue { color: var(--thw-blue-glow, #5b9aff); }
.fq-stat-tile__lbl {
    margin-top: 0.25rem;
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.625rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-muted);
}

.fq-rate-bar {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    color: var(--text-muted);
}
.fq-rate-track {
    flex: 1;
    height: 6px;
    border-radius: 3px;
    background: rgba(255,255,255,0.06);
    overflow: hidden;
}
.fq-rate-fill {
    height: 100%;
    background: var(--success, #22c55e);
    border-radius: 3px;
    transition: width 400ms ease;
}
.fq-rate-fill.warn { background: var(--warning, #f59e0b); }
.fq-rate-fill.err  { background: var(--error, #ef4444); }

.fq-kbd-hint {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.6875rem;
    color: var(--text-muted);
}
.fq-kbd {
    display: inline-grid;
    place-items: center;
    min-width: 1.4rem;
    height: 1.4rem;
    padding: 0 0.375rem;
    border-radius: 0.25rem;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.06);
    border-bottom-width: 2px;
    color: var(--text-primary);
    font-family: var(--font-mono, ui-monospace);
    font-size: 0.625rem;
    font-weight: 700;
}

.fq-empty {
    flex: 1;
    display: grid;
    place-items: center;
    color: var(--text-muted);
    text-align: center;
    padding: 2rem;
}
.fq-empty i { font-size: 2.5rem; display: block; margin-bottom: 0.75rem; color: var(--text-muted); opacity: 0.5; }

/* responsive: aside ausblenden bei schmalen Viewports */
@media (max-width: 1280px) {
    .fq-shell { grid-template-columns: 320px 1fr; }
    .fq-body  { grid-template-columns: 1fr 320px; }
}
@media (max-width: 1080px) {
    .fq-body { grid-template-columns: 1fr; }
    .fq-aside { display: none; }
}
@media (max-width: 900px) {
    .fq-shell { grid-template-columns: 1fr; }
    .fq-rail { display: none; }
}

[x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div
    id="fragen-editor"
    x-data="fragenEditor()"
    x-init="init()"
    class="fq-shell"
    x-cloak
>
    {{-- ========== LIST RAIL ========== --}}
    <div class="fq-rail">
        <div class="fq-rail__head">
            <div class="fq-rail__title-row">
                <span class="fq-rail__title">Alle Fragen</span>
                <span class="fq-rail__count"><span x-text="filtered.length"></span> / <span x-text="questions.length"></span></span>
            </div>

            <div class="fq-search">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    placeholder="Suchen nach Text oder #ID…"
                    x-model="search"
                >
            </div>

            <div class="fq-seg">
                <button type="button" :class="{ active: filter === 'all' }" @click="filter = 'all'">
                    Alle <span class="count" x-text="counts.all"></span>
                </button>
                <button type="button" :class="{ active: filter === 'draft' }" @click="filter = 'draft'">
                    Entwurf <span class="count" x-text="counts.draft"></span>
                </button>
                <button type="button" :class="{ active: filter === 'review' }" @click="filter = 'review'">
                    Review <span class="count" x-text="counts.review"></span>
                </button>
                <button type="button" :class="{ active: filter === 'published' }" @click="filter = 'published'">
                    Live <span class="count" x-text="counts.published"></span>
                </button>
            </div>

            <div class="fq-chips">
                <template x-for="a in abschnittOptions" :key="a">
                    <button
                        type="button"
                        class="fq-chip"
                        :class="{ active: abschnitt === a }"
                        @click="abschnitt = a"
                        x-text="a === '' ? 'Alle Abschnitte' : 'A' + a"
                    ></button>
                </template>
            </div>
        </div>

        <div class="fq-list">
            <template x-if="filtered.length === 0">
                <div style="padding: 2rem 1rem; text-align: center; color: var(--text-muted); font-size: 0.8125rem;">
                    Keine Fragen gefunden.
                </div>
            </template>

            <template x-for="q in filtered" :key="q.id">
                <div
                    class="fq-list-item"
                    :class="{ active: active && active.id === q.id }"
                    @click="selectQuestion(q.id)"
                >
                    <div class="fq-list-num" x-text="'#' + String(q.id).padStart(2, '0')"></div>
                    <div class="fq-list-text" x-text="q.frage"></div>
                    <div class="fq-list-meta">
                        <span x-text="'Abschnitt ' + q.lernabschnitt"></span>
                        <span class="dot"></span>
                        <span class="fq-diff" :class="'fq-diff--' + q.difficulty"><i></i><i></i><i></i></span>
                        <template x-if="q.stats.total > 0">
                            <span style="display: inline-flex; gap: 0.375rem; align-items: center;">
                                <span class="dot"></span>
                                <span x-text="q.stats.total + '×'"></span>
                            </span>
                        </template>
                    </div>
                    <div class="fq-list-status">
                        <span class="fq-status-dot" :class="'fq-status-dot--' + q.status"></span>
                        <template x-if="q.stats.total > 0">
                            <span class="fq-list-success" :class="rateClass(q.stats.rate)" x-text="q.stats.rate + '%'"></span>
                        </template>
                        <template x-if="q.stats.total === 0">
                            <span class="fq-list-success">—</span>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <div class="fq-rail__foot">
            <button type="button" class="fq-newbtn" @click="createQuestion()">
                <i class="bi bi-plus-lg"></i>
                Neue Frage anlegen
            </button>
        </div>
    </div>

    {{-- ========== EDITOR ========== --}}
    <section class="fq-editor">
        <template x-if="!active">
            <div class="fq-empty">
                <div>
                    <i class="bi bi-arrow-left-circle"></i>
                    <div>Wähle links eine Frage zum Bearbeiten aus, oder lege eine neue Frage an.</div>
                </div>
            </div>
        </template>

        <template x-if="active">
            <div style="display: contents;">
                {{-- TOP BAR --}}
                <header class="fq-topbar">
                    <div class="fq-topbar__left">
                        <span class="fq-crumb">
                            <i class="bi bi-question-circle"></i>
                            <span>Admin</span>
                            <span class="fq-crumb-sep">/</span>
                            <b>Fragen</b>
                            <span class="fq-crumb-sep">/</span>
                            <span>Bearbeiten</span>
                        </span>
                        <span class="fq-q-id">
                            <i class="bi bi-hash"></i>
                            <span x-text="String(active.id).padStart(3, '0')"></span>
                        </span>
                        <span class="fq-save-status" :data-status="saveStatus">
                            <span class="save-dot"></span>
                            <span x-text="saveStatusLabel"></span>
                        </span>
                    </div>

                    <div class="fq-topbar__right">
                        <div class="fq-nav">
                            <button type="button" @click="prev()" title="Vorherige Frage (⌘+↑)"><i class="bi bi-chevron-up"></i></button>
                            <button type="button" @click="next()" title="Nächste Frage (⌘+↓)"><i class="bi bi-chevron-down"></i></button>
                        </div>
                        <button type="button" class="fq-iconbtn danger" title="Frage löschen" @click="deleteActive()">
                            <i class="bi bi-trash3"></i>
                        </button>
                        <button type="button" class="fq-btn fq-btn--primary" @click="flushNow()">
                            <i class="bi bi-cloud-check"></i>
                            Speichern
                        </button>
                    </div>
                </header>

                {{-- BODY --}}
                <div class="fq-body">
                    <div class="fq-main">
                        {{-- Frage --}}
                        <div class="fq-field">
                            <div class="fq-field__label-row">
                                <label class="fq-field__label">Fragetext</label>
                                <span class="fq-field__hint"><span x-text="(active.frage || '').length"></span> Zeichen</span>
                            </div>
                            <textarea
                                class="fq-question-input"
                                x-model="active.frage"
                                @input.debounce.700ms="patch('frage', active.frage)"
                                placeholder="Frage eingeben…"
                            ></textarea>
                        </div>

                        {{-- Antworten --}}
                        <div class="fq-field">
                            <div class="fq-field__label-row">
                                <label class="fq-field__label">Antworten · A · B · C</label>
                                <span class="fq-field__hint">Eine bis drei können richtig sein</span>
                            </div>
                            <div class="fq-answers">
                                <template x-for="letter in ['A', 'B', 'C']" :key="letter">
                                    <div class="fq-answer-row" :class="{ correct: active.loesung.includes(letter) }">
                                        <div class="fq-letter" x-text="letter"></div>
                                        <textarea
                                            class="fq-answer-input"
                                            :value="active['antwort_' + letter.toLowerCase()]"
                                            @input="active['antwort_' + letter.toLowerCase()] = $event.target.value; autoGrow($event.target);"
                                            @input.debounce.700ms="patch('antwort_' + letter.toLowerCase(), active['antwort_' + letter.toLowerCase()])"
                                            x-init="autoGrow($el)"
                                            rows="1"
                                            :placeholder="'Antwort ' + letter + ' eingeben…'"
                                        ></textarea>
                                        <button type="button" class="fq-correct-toggle" @click="toggleCorrect(letter)">
                                            <span class="check-icon"><i class="bi bi-check-lg"></i></span>
                                            <span x-text="active.loesung.includes(letter) ? 'Richtig' : 'Falsch'"></span>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <template x-if="correctCount === 0">
                                <div class="fq-validation error">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <span>Mindestens eine Antwort muss als richtig markiert werden.</span>
                                </div>
                            </template>
                            <template x-if="correctCount === 3">
                                <div class="fq-validation">
                                    <i class="bi bi-info-circle-fill"></i>
                                    <span>Alle drei Antworten sind als richtig markiert. Das ist erlaubt, aber pädagogisch ungewöhnlich – noch mal prüfen?</span>
                                </div>
                            </template>
                            <template x-if="correctCount === 1 || correctCount === 2">
                                <div class="fq-validation ok">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span><span x-text="correctCount"></span> von 3 Antworten richtig markiert.</span>
                                </div>
                            </template>

                            <div class="fq-correct-summary">
                                <span>
                                    <span :class="correctCount > 0 ? 'badge-ok' : 'badge-warn'">
                                        <span x-text="correctCount"></span> richtig
                                    </span>
                                    ·
                                    <span><span x-text="3 - correctCount"></span> falsch</span>
                                </span>
                                <span style="opacity: 0.8;">
                                    <span class="fq-kbd">⌘</span>
                                    <span class="fq-kbd">1</span><span class="fq-kbd">2</span><span class="fq-kbd">3</span>
                                    umschalten
                                </span>
                            </div>
                        </div>

                        {{-- Erklärung --}}
                        <div class="fq-explanation-card">
                            <div class="fq-explanation-head">
                                <div class="fq-explanation-title">
                                    <i class="bi bi-lightbulb-fill"></i>
                                    Erklärung / Lösungsweg
                                </div>
                                <button
                                    type="button"
                                    class="fq-ai-btn"
                                    :class="{ thinking: aiThinking }"
                                    :disabled="aiThinking || !aiEnabled"
                                    :title="aiEnabled ? 'Erklärung per OpenAI generieren' : 'OpenAI API-Key fehlt (config/services.php)'"
                                    @click="requestAi()"
                                >
                                    <i :class="aiThinking ? 'bi bi-arrow-repeat' : 'bi bi-stars'"></i>
                                    <span x-text="aiThinking ? 'Generiere…' : 'Mit KI vorschlagen'"></span>
                                </button>
                            </div>
                            <textarea
                                class="fq-explanation-input"
                                x-model="active.loesungsweg"
                                @input.debounce.700ms="patch('loesungsweg', active.loesungsweg)"
                                placeholder="Erkläre, warum die Antwort(en) richtig sind. Bezug zu Lehrunterlagen, THW-DV oder Praxis ist hilfreich."
                            ></textarea>

                            <template x-if="aiError">
                                <div class="fq-validation error" style="margin-top: 0.5rem;">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <span x-text="aiError"></span>
                                </div>
                            </template>

                            <template x-if="aiSuggestion">
                                <div class="fq-ai-suggestion">
                                    <div class="fq-ai-suggestion__head">
                                        <span><i class="bi bi-stars"></i> KI-Vorschlag</span>
                                        <div class="fq-ai-suggestion__actions">
                                            <button type="button" class="fq-ai-suggestion__btn" @click="aiSuggestion = ''">Verwerfen</button>
                                            <button type="button" class="fq-ai-suggestion__btn fq-ai-suggestion__btn--primary" @click="acceptAi()">Übernehmen</button>
                                        </div>
                                    </div>
                                    <div x-text="aiSuggestion"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- ASIDE --}}
                    <aside class="fq-aside">
                        {{-- Klassifizierung --}}
                        <div class="fq-aside-section">
                            <div class="fq-aside-h">
                                <span class="fq-aside-title">Klassifizierung</span>
                            </div>

                            <div class="fq-field">
                                <div class="fq-field__label-row">
                                    <label class="fq-field__label">Lernabschnitt</label>
                                    <span class="fq-field__hint" x-text="'A' + active.lernabschnitt"></span>
                                </div>
                                <div class="fq-select-wrap">
                                    <select
                                        class="fq-select"
                                        x-model="active.lernabschnitt"
                                        @change="patch('lernabschnitt', active.lernabschnitt)"
                                    >
                                        <template x-for="a in lernabschnitte" :key="a.id">
                                            <option :value="String(a.id)" x-text="a.title ? 'A' + a.id + ' · ' + a.title : 'A' + a.id"></option>
                                        </template>
                                    </select>
                                    <i class="bi bi-chevron-down fq-select-icon"></i>
                                </div>
                            </div>

                            <div class="fq-field">
                                <div class="fq-field__label-row">
                                    <label class="fq-field__label">Frage-Nummer</label>
                                    <span class="fq-field__hint">eindeutig im Abschnitt</span>
                                </div>
                                <input
                                    class="fq-input"
                                    type="number"
                                    x-model.number="active.nummer"
                                    @change="patch('nummer', active.nummer)"
                                >
                            </div>

                            <div class="fq-field">
                                <div class="fq-field__label-row">
                                    <label class="fq-field__label">Schwierigkeit</label>
                                </div>
                                <div class="fq-diff-seg">
                                    <template x-for="d in difficultyOptions" :key="d.value">
                                        <button
                                            type="button"
                                            :data-val="d.value"
                                            :class="{ active: active.difficulty === d.value }"
                                            @click="active.difficulty = d.value; patch('difficulty', d.value)"
                                        >
                                            <span class="bars"><i></i><i></i><i></i></span>
                                            <span x-text="d.label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div class="fq-field">
                                <div class="fq-field__label-row">
                                    <label class="fq-field__label">Status</label>
                                </div>
                                <div class="fq-status-pill-row">
                                    <template x-for="s in statusOptions" :key="s.value">
                                        <button
                                            type="button"
                                            :data-val="s.value"
                                            class="fq-status-pill"
                                            :class="{ active: active.status === s.value }"
                                            @click="active.status = s.value; patch('status', s.value)"
                                        >
                                            <span class="fq-status-dot" :class="'fq-status-dot--' + s.value"></span>
                                            <span x-text="s.label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Statistik --}}
                        <div class="fq-aside-section">
                            <div class="fq-aside-h">
                                <span class="fq-aside-title">Statistik</span>
                                <span class="fq-field__hint">aktuell</span>
                            </div>

                            <template x-if="active.stats.total === 0">
                                <div style="font-size: 0.8125rem; color: var(--text-muted); padding: 0.5rem 0;">
                                    Noch keine Beantwortungen. Sobald die Frage live ist, sammeln wir hier Daten.
                                </div>
                            </template>

                            <template x-if="active.stats.total > 0">
                                <div>
                                    <div class="fq-stats">
                                        <div class="fq-stat-tile">
                                            <div class="fq-stat-tile__val fq-stat-tile__val--blue" x-text="active.stats.total"></div>
                                            <div class="fq-stat-tile__lbl">Beantwortet</div>
                                        </div>
                                        <div class="fq-stat-tile">
                                            <div class="fq-stat-tile__val" :class="rateValClass(active.stats.rate)" x-text="active.stats.rate + '%'"></div>
                                            <div class="fq-stat-tile__lbl">Lösungsquote</div>
                                        </div>
                                    </div>
                                    <div class="fq-rate-bar">
                                        <div class="fq-rate-track">
                                            <div class="fq-rate-fill" :class="rateFillClass(active.stats.rate)" :style="`width: ${active.stats.rate}%`"></div>
                                        </div>
                                        <span x-text="active.stats.correct + '/' + active.stats.total"></span>
                                    </div>
                                    <template x-if="active.stats.rate < 60">
                                        <div class="fq-validation" style="margin-top: 0.875rem;">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <span>Niedrige Lösungsquote – evtl. Frage oder Antworten verbessern.</span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- Shortcuts --}}
                        <div class="fq-aside-section">
                            <div class="fq-aside-h">
                                <span class="fq-aside-title">Shortcuts</span>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <div class="fq-kbd-hint"><span class="fq-kbd">⌘</span> <span class="fq-kbd">S</span> Speichern</div>
                                <div class="fq-kbd-hint"><span class="fq-kbd">⌘</span> <span class="fq-kbd">↑</span><span class="fq-kbd">↓</span> Frage wechseln</div>
                                <div class="fq-kbd-hint"><span class="fq-kbd">⌘</span> <span class="fq-kbd">1</span><span class="fq-kbd">2</span><span class="fq-kbd">3</span> Antwort A/B/C umschalten</div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </template>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.body.classList.add('fq-fullbleed');
window.__FRAGEN_EDITOR__ = {
    questions: @json($questionsJson),
    aiEnabled: @json($aiEnabled),
    csrfToken: @json(csrf_token()),
    initialFocus: @json((int) request('focus', 0)),
    routes: {
        updateField: @json(url('/admin/questions')) + '/{id}/update-field',
        aiSuggest: @json(url('/admin/questions')) + '/{id}/ai-suggest-explanation',
        store: @json(route('admin.questions.store')),
        destroy: @json(url('/admin/questions')) + '/{id}',
    },
    lernabschnitte: [
        { id: '1',  title: 'THW im Gefüge des Zivil- und Katastrophenschutzes' },
        { id: '2',  title: 'Aufbau und Aufgaben des THW' },
        { id: '3',  title: 'Rechtsgrundlagen' },
        { id: '4',  title: 'Persönliche Schutzausrüstung' },
        { id: '5',  title: 'Einsatzhygiene und Infektionsschutz' },
        { id: '6',  title: 'Unfallverhütung und Erste Hilfe' },
        { id: '7',  title: 'Verhalten im Einsatz' },
        { id: '8',  title: 'Sicherheit im Einsatz' },
        { id: '9',  title: 'Kommunikation und Funk' },
        { id: '10', title: 'Grundlagen der Rettung und Bergung' },
    ],
};

function fragenEditor() {
    return {
        questions: [],
        activeId: null,
        search: '',
        filter: 'all',
        abschnitt: '',
        saveStatus: 'saved',
        aiEnabled: false,
        aiThinking: false,
        aiSuggestion: '',
        aiError: '',
        pendingFields: {},
        flushTimer: null,
        difficultyOptions: [
            { value: 'easy',   label: 'Leicht' },
            { value: 'medium', label: 'Mittel' },
            { value: 'hard',   label: 'Schwer' },
        ],
        statusOptions: [
            { value: 'draft',     label: 'Entwurf' },
            { value: 'review',    label: 'In Review' },
            { value: 'published', label: 'Live' },
        ],

        init() {
            const data = window.__FRAGEN_EDITOR__;
            this.questions = data.questions.map(q => ({
                ...q,
                lernabschnitt: String(q.lernabschnitt),
            }));
            this.aiEnabled = data.aiEnabled;

            // Lernabschnitte aus DB-Werten + 1..10 mergen
            const fromDb = new Set(this.questions.map(q => String(q.lernabschnitt)));
            const known = new Map(data.lernabschnitte.map(a => [String(a.id), a]));
            data.lernabschnitte.forEach(a => fromDb.add(String(a.id)));
            this.lernabschnitte = Array.from(fromDb)
                .sort((a, b) => {
                    const na = parseInt(a, 10);
                    const nb = parseInt(b, 10);
                    if (!isNaN(na) && !isNaN(nb)) return na - nb;
                    return String(a).localeCompare(String(b));
                })
                .map(id => known.get(id) || { id, title: '' });

            const focus = data.initialFocus;
            if (focus && this.questions.find(q => q.id === focus)) {
                this.activeId = focus;
            } else if (this.questions.length) {
                this.activeId = this.questions[0].id;
            }

            this.bindShortcuts();
        },

        get active() {
            if (!this.activeId) return null;
            return this.questions.find(q => q.id === this.activeId) || null;
        },

        get correctCount() {
            return this.active ? this.active.loesung.length : 0;
        },

        get counts() {
            const c = { all: this.questions.length, draft: 0, review: 0, published: 0 };
            this.questions.forEach(q => { c[q.status] = (c[q.status] || 0) + 1; });
            return c;
        },

        get filtered() {
            const s = this.search.toLowerCase().trim();
            return this.questions.filter(q => {
                if (this.filter !== 'all' && q.status !== this.filter) return false;
                if (this.abschnitt !== '' && String(q.lernabschnitt) !== String(this.abschnitt)) return false;
                if (s && !(q.frage.toLowerCase().includes(s) || String(q.id).includes(s))) return false;
                return true;
            });
        },

        get abschnittOptions() {
            const set = new Set(['']);
            this.questions.forEach(q => set.add(String(q.lernabschnitt)));
            return Array.from(set).sort((a, b) => {
                if (a === '') return -1;
                if (b === '') return 1;
                return parseInt(a, 10) - parseInt(b, 10);
            });
        },

        get saveStatusLabel() {
            return {
                saved: 'Gespeichert',
                saving: 'Speichert…',
                dirty: 'Ungespeichert',
                error: 'Fehler',
            }[this.saveStatus] || 'Gespeichert';
        },

        rateClass(rate) {
            if (rate >= 80) return 'high';
            if (rate >= 60) return 'low';
            return 'crit';
        },
        rateValClass(rate) {
            if (rate >= 80) return 'fq-stat-tile__val--ok';
            if (rate >= 60) return 'fq-stat-tile__val--warn';
            return 'fq-stat-tile__val--err';
        },
        rateFillClass(rate) {
            if (rate >= 80) return '';
            if (rate >= 60) return 'warn';
            return 'err';
        },

        autoGrow(el) {
            if (!el) return;
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
        },

        selectQuestion(id) {
            this.flushNow();
            this.activeId = id;
            this.aiSuggestion = '';
            this.aiError = '';
            this.aiThinking = false;
            this.$nextTick(() => {
                document.querySelectorAll('.fq-answer-input').forEach(el => this.autoGrow(el));
            });
        },

        prev() {
            const idx = this.filtered.findIndex(q => q.id === this.activeId);
            if (idx > 0) this.selectQuestion(this.filtered[idx - 1].id);
        },
        next() {
            const idx = this.filtered.findIndex(q => q.id === this.activeId);
            if (idx >= 0 && idx < this.filtered.length - 1) this.selectQuestion(this.filtered[idx + 1].id);
        },

        toggleCorrect(letter) {
            if (!this.active) return;
            const idx = this.active.loesung.indexOf(letter);
            if (idx >= 0) {
                this.active.loesung.splice(idx, 1);
            } else {
                this.active.loesung.push(letter);
                this.active.loesung.sort();
            }
            if (this.active.loesung.length === 0) {
                this.active.loesung.push(letter);
                return;
            }
            this.patch('loesung', this.active.loesung);
        },

        patch(field, value) {
            if (!this.active) return;
            this.pendingFields[field] = value;
            this.saveStatus = 'dirty';
            if (this.flushTimer) clearTimeout(this.flushTimer);
            this.flushTimer = setTimeout(() => this.flushNow(), 500);
        },

        async flushNow() {
            if (this.flushTimer) {
                clearTimeout(this.flushTimer);
                this.flushTimer = null;
            }
            if (!this.active) return;
            const fields = this.pendingFields;
            const keys = Object.keys(fields);
            if (keys.length === 0) return;
            this.pendingFields = {};
            this.saveStatus = 'saving';

            const id = this.active.id;
            const url = window.__FRAGEN_EDITOR__.routes.updateField.replace('{id}', id);

            try {
                for (const field of keys) {
                    const value = fields[field];
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.__FRAGEN_EDITOR__.csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ field, value }),
                    });
                    const json = await res.json().catch(() => ({}));
                    if (!res.ok || !json.success) {
                        throw new Error(json.message || 'Speichern fehlgeschlagen');
                    }
                }
                this.saveStatus = 'saved';
            } catch (e) {
                console.error(e);
                this.saveStatus = 'error';
            }
        },

        async requestAi() {
            if (!this.active || this.aiThinking) return;
            this.aiThinking = true;
            this.aiSuggestion = '';
            this.aiError = '';
            await this.flushNow();
            const id = this.active.id;
            const url = window.__FRAGEN_EDITOR__.routes.aiSuggest.replace('{id}', id);

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.__FRAGEN_EDITOR__.csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok || !json.success) {
                    throw new Error(json.message || 'KI-Vorschlag fehlgeschlagen');
                }
                this.aiSuggestion = json.suggestion;
            } catch (e) {
                console.error(e);
                this.aiError = e.message || 'KI-Vorschlag fehlgeschlagen';
            } finally {
                this.aiThinking = false;
            }
        },

        acceptAi() {
            if (!this.active || !this.aiSuggestion) return;
            this.active.loesungsweg = this.aiSuggestion;
            this.patch('loesungsweg', this.aiSuggestion);
            this.aiSuggestion = '';
        },

        async createQuestion() {
            const lernabschnitt = window.prompt('Neue Frage in welchem Lernabschnitt? (1–10)', '1');
            if (!lernabschnitt) return;
            const nummer = window.prompt('Frage-Nummer im Abschnitt?', '1');
            if (!nummer) return;

            try {
                const res = await fetch(window.__FRAGEN_EDITOR__.routes.store, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.__FRAGEN_EDITOR__.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        lernabschnitt: lernabschnitt,
                        nummer: parseInt(nummer, 10) || 1,
                        frage: 'Neue Frage – Text bearbeiten…',
                        antwort_a: 'Antwort A',
                        antwort_b: 'Antwort B',
                        antwort_c: 'Antwort C',
                        loesung: ['A'],
                        difficulty: 'medium',
                        status: 'draft',
                    }),
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok || !json.success) {
                    throw new Error(json.message || 'Fehler beim Anlegen');
                }
                window.location.href = window.location.pathname + '?focus=' + json.id;
            } catch (e) {
                alert(e.message);
            }
        },

        async deleteActive() {
            if (!this.active) return;
            if (!window.confirm('Frage #' + this.active.id + ' wirklich löschen?')) return;
            const id = this.active.id;
            const url = window.__FRAGEN_EDITOR__.routes.destroy.replace('{id}', id);
            try {
                const res = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': window.__FRAGEN_EDITOR__.csrfToken,
                        'Accept': 'application/json',
                    },
                });
                if (!res.ok) throw new Error('Löschen fehlgeschlagen');
                this.questions = this.questions.filter(q => q.id !== id);
                this.activeId = this.questions.length ? this.questions[0].id : null;
            } catch (e) {
                alert(e.message);
            }
        },

        bindShortcuts() {
            window.addEventListener('keydown', (e) => {
                const meta = e.metaKey || e.ctrlKey;
                if (!meta) return;
                if (e.key === 's') {
                    e.preventDefault();
                    this.flushNow();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    this.prev();
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    this.next();
                } else if (['1', '2', '3'].includes(e.key)) {
                    e.preventDefault();
                    const map = { '1': 'A', '2': 'B', '3': 'C' };
                    this.toggleCorrect(map[e.key]);
                }
            });
            window.addEventListener('beforeunload', () => {
                if (Object.keys(this.pendingFields).length) {
                    this.flushNow();
                }
            });
        },
    };
}
</script>
@endpush
