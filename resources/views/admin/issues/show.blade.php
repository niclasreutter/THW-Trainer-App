@extends('layouts.app')
@section('title', 'Fehlermeldung #' . $issue->id . ' - Admin')

@php
    if (! function_exists('fmdRenderMentions')) {
        /**
         * Escapes a comment message and wraps @mentions in a styled span.
         */
        function fmdRenderMentions(?string $text): string {
            $escaped = e((string) $text);
            return preg_replace(
                '/@([A-Za-zÄÖÜäöüß]+(?:\s[A-Za-zÄÖÜäöüß]+)?)/u',
                '<span class="mention-chip">@$1</span>',
                $escaped
            );
        }
    }
@endphp

@push('styles')
<style>
/* =========================================================
   FMD (Fehlermeldung Details) — Design 2026-05
   1:1 nach Claude Design Handoff. Anchored in THW design system:
     — .glass cards, .section-label eyebrows
     — THW-blue actions, semantic colors (error/warning/success)
     — Jira-style touches: status pill, assignee/reporter rail,
       tabbed activity feed, @mention chip in composer.
   ========================================================= */
.fmd-container { width: 100%; max-width: 76rem; margin: 0 auto; min-width: 0; }
.fmd-container, .fmd-container * { box-sizing: border-box; }

/* Topbar — breadcrumb / actions */
.fmd-topbar {
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap;
    min-width: 0;
}
.fmd-crumb {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.6875rem; font-weight: 600;
    letter-spacing: 0.1em; text-transform: uppercase;
    color: var(--text-muted);
    display: inline-flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;
}
.fmd-crumb a { color: var(--text-muted); text-decoration: none; transition: color 0.15s ease; }
.fmd-crumb a:hover { color: var(--text-primary); }
.fmd-crumb__sep { opacity: 0.45; }
.fmd-crumb__current { color: var(--text-secondary); }
.fmd-actions { display: inline-flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }

/* Hero — title + status pill */
.fmd-hero {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 1.25rem; margin-bottom: 1.25rem; flex-wrap: wrap;
    min-width: 0;
}
.fmd-hero__main { min-width: 0; flex: 1 1 16rem; word-wrap: break-word; overflow-wrap: anywhere; }
.fmd-hero__id {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.6875rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.12em;
    color: var(--text-muted);
    display: inline-flex; align-items: center; gap: 0.5rem;
    margin-bottom: 0.5rem; flex-wrap: wrap;
}
.fmd-hero__id .dot {
    width: 4px; height: 4px; border-radius: 50%;
    background: var(--text-muted); opacity: 0.5;
}
.fmd-hero h1 {
    font-family: 'Figtree', sans-serif;
    font-weight: 800; font-size: 1.875rem; line-height: 1.2;
    letter-spacing: -0.015em; color: #5b9aff;
    margin: 0 0 0.5rem;
    overflow-wrap: anywhere; word-break: break-word;
}
html.light-mode .fmd-hero h1 { color: var(--thw-blue); }
.fmd-hero__meta {
    display: flex; flex-wrap: wrap; align-items: center;
    gap: 0.5rem 0.875rem;
    font-size: 0.8125rem; color: var(--text-secondary);
}
.fmd-hero__meta .dot {
    width: 3px; height: 3px; border-radius: 50%;
    background: var(--text-muted); opacity: 0.5;
}
.fmd-hero__status {
    display: inline-flex; flex-direction: column;
    align-items: flex-end; gap: 0.4rem;
}
.fmd-hero__status-label {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.625rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.12em;
    color: var(--text-muted);
}

/* Status pill */
.status-pill {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.35rem 0.7rem; border-radius: 999px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.6875rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    border: 1px solid transparent; white-space: nowrap;
}
.status-pill .dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: currentColor; flex-shrink: 0;
}
.status-pill--open     { background: rgba(91,154,255,0.14); color: #5b9aff;          border-color: rgba(91,154,255,0.32); }
.status-pill--in_review{ background: rgba(245,158,11,0.14); color: #f59e0b;          border-color: rgba(245,158,11,0.32); }
.status-pill--resolved { background: rgba(34,197,94,0.14);  color: #22c55e;          border-color: rgba(34,197,94,0.32); }
.status-pill--rejected { background: rgba(113,113,122,0.18); color: #a1a1aa;         border-color: rgba(113,113,122,0.32); }
html.light-mode .status-pill--open      { background: rgba(0,51,127,0.08);  color: var(--thw-blue);   border-color: rgba(0,51,127,0.18); }
html.light-mode .status-pill--in_review { background: rgba(217,119,6,0.10); color: #b45309;           border-color: rgba(217,119,6,0.30); }
html.light-mode .status-pill--resolved  { background: rgba(34,197,94,0.10); color: #166534;           border-color: rgba(34,197,94,0.30); }
html.light-mode .status-pill--rejected  { background: rgba(100,116,139,0.12); color: #475569;         border-color: rgba(100,116,139,0.28); }

.status-pill--lg { padding: 0.5rem 1rem; font-size: 0.75rem; }

button.status-pill {
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
    font-family: 'IBM Plex Mono', monospace;
}
button.status-pill:hover { filter: brightness(1.08); }
button.status-pill .chev { font-size: 0.6875rem; opacity: 0.7; margin-left: 0.15rem; }

/* Layout grid */
.fmd-grid {
    display: grid; grid-template-columns: minmax(0, 1fr);
    gap: 1rem; align-items: start;
}
.fmd-grid > * { min-width: 0; }
@media (min-width: 1024px) {
    .fmd-grid { grid-template-columns: minmax(0, 1fr) 340px; gap: 1.25rem; }
}

/* Cards */
.fmd-card {
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    border-radius: 0.875rem; padding: 1.25rem;
}
html.light-mode .fmd-card { background: #fff; box-shadow: 0 1px 3px rgba(0,51,127,0.04); }
.fmd-card + .fmd-card { margin-top: 1rem; }
.fmd-card__h {
    display: flex; align-items: center; justify-content: space-between;
    gap: 0.75rem; margin-bottom: 0.875rem; flex-wrap: wrap;
}
.fmd-card__h-actions { display: inline-flex; gap: 0.375rem; align-items: center; }
.section-label {
    display: inline-block; font-family: 'IBM Plex Mono', monospace;
    font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.1em; color: var(--text-muted);
}

/* Question display */
.q-text {
    font-family: 'Figtree', sans-serif;
    font-weight: 600; font-size: 1.1875rem; line-height: 1.4;
    color: var(--text-primary); margin: 0 0 1.125rem;
    text-wrap: pretty;
    overflow-wrap: anywhere; word-break: break-word;
}
.ans-list { display: flex; flex-direction: column; gap: 0.5rem; }
.ans-row {
    display: flex; align-items: flex-start; gap: 0.75rem;
    padding: 0.75rem 0.875rem; border-radius: 0.625rem;
    background: var(--glass-bg); border: 1px solid var(--glass-border);
}
html.light-mode .ans-row { background: rgba(0,51,127,0.025); border-color: rgba(0,51,127,0.08); }
.ans-row.is-correct {
    background: rgba(34,197,94,0.07);
    border-color: rgba(34,197,94,0.30);
}
.ans-letter {
    width: 1.75rem; height: 1.75rem; border-radius: 0.4rem;
    display: grid; place-items: center;
    background: rgba(91,154,255,0.14); color: #5b9aff;
    font-family: 'IBM Plex Mono', monospace;
    font-weight: 700; font-size: 0.8125rem; flex-shrink: 0;
}
html.light-mode .ans-letter { background: rgba(0,51,127,0.06); color: var(--thw-blue); }
.ans-row.is-correct .ans-letter { background: #22c55e; color: #fff; }
.ans-text {
    font-size: 0.9375rem; line-height: 1.45;
    color: var(--text-primary); margin: 0.05rem 0 0;
    text-wrap: pretty; flex: 1; min-width: 0;
    overflow-wrap: anywhere; word-break: break-word;
}
.ans-flag {
    margin-left: auto; align-self: center;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.625rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: #22c55e;
    display: inline-flex; align-items: center; gap: 0.3rem; flex-shrink: 0;
}
.q-footer {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: 1rem; padding-top: 0.75rem;
    border-top: 1px solid var(--glass-border-lo);
    flex-wrap: wrap; gap: 0.5rem;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.6875rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--text-muted);
}
.q-footer__right { color: #22c55e; font-weight: 700; }

/* Inline form */
.form-field { margin-bottom: 0.875rem; }
.form-field__label {
    display: block; font-family: 'IBM Plex Mono', monospace;
    font-size: 0.6875rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--text-muted); margin-bottom: 0.4rem;
}
.form-input, .form-textarea, .form-select {
    width: 100%;
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    border-radius: 0.5rem; padding: 0.55rem 0.75rem;
    font-family: 'Figtree', sans-serif; font-size: 0.875rem;
    color: var(--text-primary);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
html.light-mode .form-input,
html.light-mode .form-textarea,
html.light-mode .form-select {
    background: #ffffff; border-color: rgba(0,51,127,0.12);
}
.form-input:focus, .form-textarea:focus, .form-select:focus {
    outline: none;
    border-color: var(--thw-blue);
    box-shadow: 0 0 0 3px rgba(0,51,127,0.12);
}
.form-textarea { min-height: 5.5rem; resize: vertical; line-height: 1.45; }

.ans-edit-row {
    display: grid; grid-template-columns: 1.75rem 1fr auto;
    gap: 0.625rem; align-items: center; margin-bottom: 0.5rem;
}
.ans-edit-correct {
    display: inline-flex; align-items: center; gap: 0.375rem;
    padding: 0.35rem 0.625rem; border-radius: 999px;
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.625rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--text-muted); cursor: pointer;
    transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
    white-space: nowrap;
}
html.light-mode .ans-edit-correct { background: #fff; border-color: rgba(0,51,127,0.10); }
.ans-edit-correct.is-on {
    background: rgba(34,197,94,0.12);
    border-color: rgba(34,197,94,0.35); color: #22c55e;
}
.ans-edit-correct input { display: none; }
.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }

/* Activity tabs */
.activity-tabs {
    display: inline-flex; gap: 0.15rem;
    padding: 3px; border-radius: 0.5rem;
    background: var(--glass-bg); border: 1px solid var(--glass-border-lo);
    max-width: 100%; overflow-x: auto;
    scrollbar-width: none;
}
.activity-tabs::-webkit-scrollbar { display: none; }
html.light-mode .activity-tabs { background: rgba(0,51,127,0.04); border-color: rgba(0,51,127,0.08); }
.activity-tab {
    appearance: none; border: 0; background: transparent;
    color: var(--text-muted);
    font-family: 'Figtree', sans-serif;
    font-size: 0.75rem; font-weight: 600;
    padding: 0.3rem 0.7rem; border-radius: 0.375rem;
    cursor: pointer;
    transition: background 0.15s ease, color 0.15s ease;
    display: inline-flex; align-items: center; gap: 0.35rem;
    white-space: nowrap; flex-shrink: 0;
}
.activity-tab:hover { color: var(--text-primary); }
.activity-tab.is-active {
    background: var(--bg-elevated); color: #5b9aff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.18);
}
html.light-mode .activity-tab.is-active {
    background: #ffffff; color: var(--thw-blue);
    box-shadow: 0 1px 3px rgba(0,51,127,0.10);
}
.activity-tab__count {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.625rem; font-weight: 700;
    padding: 0 0.32rem; height: 1rem; line-height: 1rem;
    border-radius: 999px;
    background: var(--glass-border-lo); color: var(--text-muted);
}
.activity-tab.is-active .activity-tab__count {
    background: rgba(91,154,255,0.18); color: #5b9aff;
}
html.light-mode .activity-tab.is-active .activity-tab__count {
    background: rgba(0,51,127,0.10); color: var(--thw-blue);
}

/* Activity feed */
.activity-feed { position: relative; margin-top: 1rem; }
.activity-feed::before {
    content: ""; position: absolute;
    left: 18px; top: 8px; bottom: 8px;
    width: 2px;
    background: var(--glass-border-lo); border-radius: 1px;
}
html.light-mode .activity-feed::before { background: rgba(0,51,127,0.08); }
.activity-item {
    position: relative; display: grid;
    grid-template-columns: 36px 1fr; gap: 0.75rem;
    padding: 0.6rem 0 0.85rem;
}
.activity-item__avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg, #5b9aff, #00337F);
    color: #fff;
    display: grid; place-items: center;
    font-family: 'Figtree', sans-serif;
    font-weight: 700; font-size: 0.8125rem;
    flex-shrink: 0;
    border: 3px solid var(--bg-elevated);
    box-sizing: content-box; z-index: 1; margin-left: -3px;
    overflow: hidden;
}
html.light-mode .activity-item__avatar { border-color: #ffffff; }
.activity-item__avatar img {
    width: 100%; height: 100%; object-fit: cover; display: block;
}
.activity-item__avatar--status {
    background: rgba(91,154,255,0.18);
    color: #5b9aff; font-size: 1rem;
}
html.light-mode .activity-item__avatar--status {
    background: rgba(0,51,127,0.10); color: var(--thw-blue);
}
.activity-item__avatar--report-icon {
    background: linear-gradient(135deg, #ef4444, #b91c1c);
}

.activity-item__head {
    display: flex; align-items: center;
    gap: 0.5rem; flex-wrap: wrap;
    margin-bottom: 0.25rem;
}
.activity-item__name {
    font-size: 0.875rem; font-weight: 700;
    color: var(--text-primary);
}
.activity-item__type {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.625rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--text-muted);
    padding: 0.15rem 0.5rem; border-radius: 999px;
    border: 1px solid var(--glass-border-lo);
}
.activity-item__type--note { color: #5b9aff; border-color: rgba(91,154,255,0.32); }
html.light-mode .activity-item__type--note { color: var(--thw-blue); border-color: rgba(0,51,127,0.18); }
.activity-item__type--report { color: #ef4444; border-color: rgba(239,68,68,0.30); }
.activity-item__type--status { color: var(--text-muted); }
.activity-item__type--assignment { color: #a78bfa; border-color: rgba(167,139,250,0.30); }
.activity-item__time {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.6875rem; color: var(--text-muted);
    margin-left: auto;
}
.activity-item__msg {
    font-size: 0.9375rem; line-height: 1.5;
    color: var(--text-primary); margin: 0.15rem 0 0;
    text-wrap: pretty; white-space: pre-wrap;
    word-break: break-word; overflow-wrap: anywhere;
}
.activity-item__status-line {
    font-size: 0.8125rem; color: var(--text-secondary);
    display: inline-flex; align-items: center;
    gap: 0.5rem; flex-wrap: wrap;
}

/* Composer */
.composer {
    display: grid; grid-template-columns: 36px 1fr;
    gap: 0.75rem; padding: 0.85rem;
    border-radius: 0.75rem;
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    margin-top: 0.5rem; position: relative;
}
html.light-mode .composer { background: rgba(0,51,127,0.025); border-color: rgba(0,51,127,0.10); }
.composer__avatar {
    width: 36px; height: 36px; border-radius: 50%;
    overflow: hidden;
    background: linear-gradient(135deg, #5b9aff, #00337F);
    color: #fff;
    display: grid; place-items: center;
    font-family: 'Figtree', sans-serif;
    font-weight: 700; font-size: 0.8125rem;
}
.composer__avatar img { width: 100%; height: 100%; object-fit: cover; display: block; }
.composer__body { display: flex; flex-direction: column; gap: 0.5rem; min-width: 0; }
.composer textarea {
    width: 100%; background: transparent; border: 0;
    resize: vertical; min-height: 3.25rem;
    font-family: 'Figtree', sans-serif;
    font-size: 0.9375rem; line-height: 1.5;
    color: var(--text-primary); padding: 0.4rem 0.65rem;
}
.composer textarea:focus { outline: none; }
.composer textarea::placeholder { color: var(--text-muted); }
.composer__bar {
    display: flex; align-items: center; justify-content: space-between;
    gap: 0.5rem; flex-wrap: wrap;
    padding-top: 0.3rem;
    border-top: 1px solid var(--glass-border-lo);
}
.composer__hint {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.625rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--text-muted);
    display: inline-flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;
}
.composer__hint kbd {
    font-family: 'IBM Plex Mono', monospace;
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    border-radius: 0.25rem; padding: 0.05rem 0.35rem;
    font-size: 0.625rem; color: var(--text-secondary);
}

/* Mention popup */
.mention-pop {
    position: absolute; z-index: 20; min-width: 240px; max-width: 320px;
    background: var(--bg-elevated);
    border: 1px solid var(--glass-border);
    border-radius: 0.625rem;
    box-shadow: 0 12px 28px rgba(0,0,0,0.35);
    padding: 0.35rem; overflow: hidden;
}
html.light-mode .mention-pop {
    background: #ffffff; border-color: rgba(0,51,127,0.10);
    box-shadow: 0 12px 28px rgba(0,51,127,0.12);
}
.mention-item {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.4rem 0.5rem; border-radius: 0.4rem;
    cursor: pointer; font-size: 0.8125rem;
}
.mention-item.is-active { background: rgba(91,154,255,0.14); }
html.light-mode .mention-item.is-active { background: rgba(0,51,127,0.06); }
.mention-item__avatar {
    width: 22px; height: 22px; border-radius: 50%;
    background: linear-gradient(135deg, #5b9aff, #00337F);
    color: #fff;
    display: grid; place-items: center;
    font-size: 0.625rem; font-weight: 700;
    flex-shrink: 0; overflow: hidden;
}
.mention-item__avatar img { width: 100%; height: 100%; object-fit: cover; display: block; }
.mention-item__role {
    margin-left: auto;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.5625rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--text-muted);
}
.mention-chip {
    display: inline-block; padding: 0 0.3rem;
    border-radius: 0.3rem;
    background: rgba(91,154,255,0.16); color: #5b9aff;
    font-weight: 600;
}
html.light-mode .mention-chip { background: rgba(0,51,127,0.08); color: var(--thw-blue); }

/* Right rail */
.meta-list { display: flex; flex-direction: column; }
.meta-row {
    display: flex; align-items: center; justify-content: space-between;
    gap: 0.5rem; padding: 0.6rem 0;
    border-bottom: 1px solid var(--glass-border-lo);
    font-size: 0.8125rem;
}
.meta-row:last-child { border-bottom: 0; }
.meta-row__label {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.6875rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--text-muted);
}
.meta-row__value {
    color: var(--text-primary); font-weight: 500; text-align: right;
}
.meta-row__value .ts {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.75rem; color: var(--text-secondary);
}

.person-card {
    display: flex; align-items: center; gap: 0.625rem;
    padding: 0.55rem; border-radius: 0.5rem;
    cursor: pointer;
    background: var(--glass-bg); border: 1px solid var(--glass-border-lo);
    transition: border-color 0.15s ease, background 0.15s ease;
    width: 100%; text-align: left;
}
html.light-mode .person-card { background: #ffffff; border-color: rgba(0,51,127,0.08); }
.person-card:hover { border-color: var(--thw-blue); }
.person-card__avatar {
    width: 28px; height: 28px; border-radius: 50%;
    overflow: hidden;
    background: linear-gradient(135deg, #5b9aff, #00337F);
    color: #fff;
    display: grid; place-items: center;
    font-size: 0.6875rem; font-weight: 700; flex-shrink: 0;
}
.person-card__avatar img { width: 100%; height: 100%; object-fit: cover; display: block; }
.person-card__avatar--empty {
    background: var(--glass-border-lo); color: var(--text-muted);
}
html.light-mode .person-card__avatar--empty { background: rgba(0,51,127,0.08); }
.person-card__name {
    font-size: 0.8125rem; font-weight: 600; color: var(--text-primary);
}
.person-card__sub {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.625rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--text-muted);
}
.person-card__chev {
    margin-left: auto;
    color: var(--text-muted); font-size: 0.875rem;
}

.rail-row {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 0.4rem;
}
.rail-row__hint {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.5625rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--text-muted);
}
button.rail-row__hint { background: transparent; border: 0; cursor: pointer; }
button.rail-row__hint:hover { color: var(--thw-blue); }

/* Danger zone */
.danger-card { padding: 1rem 1.25rem; }
.danger-row {
    display: flex; align-items: center; justify-content: space-between;
    gap: 0.75rem;
}
.danger-row__txt {
    font-size: 0.75rem; color: var(--text-secondary); line-height: 1.4;
}
.btn-danger {
    display: inline-flex; align-items: center; gap: 0.4rem;
    background: transparent;
    border: 1px solid rgba(239,68,68,0.40);
    color: #ef4444;
    border-radius: 0.5rem; padding: 0.45rem 0.75rem;
    font-family: 'Figtree', sans-serif;
    font-size: 0.8125rem; font-weight: 700;
    cursor: pointer;
    transition: background 0.15s ease, color 0.15s ease;
    white-space: nowrap;
}
.btn-danger:hover { background: #ef4444; color: #fff; }

/* Menu popup (status / assignee) */
.menu-pop {
    position: absolute; z-index: 30;
    min-width: 240px;
    background: var(--bg-elevated);
    border: 1px solid var(--glass-border);
    border-radius: 0.625rem;
    box-shadow: 0 12px 28px rgba(0,0,0,0.35);
    padding: 0.35rem; overflow: hidden;
}
html.light-mode .menu-pop {
    background: #ffffff; border-color: rgba(0,51,127,0.10);
    box-shadow: 0 12px 28px rgba(0,51,127,0.12);
}
.menu-item {
    display: flex; align-items: center; gap: 0.6rem;
    width: 100%; padding: 0.5rem 0.55rem;
    border-radius: 0.4rem; cursor: pointer;
    font-size: 0.8125rem; color: var(--text-primary);
    background: transparent; border: 0; text-align: left;
    transition: background 0.15s ease;
}
.menu-item:hover { background: var(--glass-bg); }
html.light-mode .menu-item:hover { background: rgba(0,51,127,0.04); }
.menu-item__check {
    margin-left: auto;
    color: #5b9aff; font-size: 0.875rem;
}
html.light-mode .menu-item__check { color: var(--thw-blue); }

/* Role hint badge */
.role-hint {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.2rem 0.55rem; border-radius: 999px;
    background: rgba(167,139,250,0.14);
    color: #a78bfa;
    border: 1px solid rgba(167,139,250,0.30);
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.625rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
}
.role-hint--admin {
    background: rgba(91,154,255,0.14);
    color: #5b9aff;
    border-color: rgba(91,154,255,0.32);
}
html.light-mode .role-hint--admin {
    background: rgba(0,51,127,0.06);
    color: var(--thw-blue);
    border-color: rgba(0,51,127,0.18);
}

/* Prev/Next navigation between issues */
.fmd-nav {
    display: inline-flex; align-items: center;
    gap: 0; padding: 0;
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    border-radius: 0.5rem; overflow: hidden;
}
html.light-mode .fmd-nav { background: #ffffff; border-color: rgba(0,51,127,0.10); }
.fmd-nav__btn {
    display: inline-flex; align-items: center; gap: 0.35rem;
    padding: 0.4rem 0.7rem;
    background: transparent; border: 0; cursor: pointer;
    color: var(--text-secondary);
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.6875rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    text-decoration: none;
    transition: color 0.15s ease, background 0.15s ease;
}
.fmd-nav__btn:hover:not(.is-disabled) {
    color: var(--thw-blue);
    background: rgba(91,154,255,0.08);
}
html.light-mode .fmd-nav__btn:hover:not(.is-disabled) { background: rgba(0,51,127,0.05); }
.fmd-nav__btn.is-disabled {
    color: var(--text-muted); opacity: 0.45;
    cursor: not-allowed; pointer-events: none;
}
.fmd-nav__sep {
    width: 1px; align-self: stretch;
    background: var(--glass-border);
}
html.light-mode .fmd-nav__sep { background: rgba(0,51,127,0.10); }
.fmd-nav__pos {
    padding: 0.4rem 0.6rem;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.6875rem; font-weight: 600;
    color: var(--text-muted);
    white-space: nowrap;
}

/* Icon button (toolbar) */
.icon-btn-sm {
    width: 30px; height: 30px; border-radius: 0.4rem;
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    color: var(--text-secondary);
    display: grid; place-items: center;
    cursor: pointer;
    transition: border-color 0.15s ease, color 0.15s ease;
    font-size: 0.875rem; text-decoration: none;
}
html.light-mode .icon-btn-sm { background: #ffffff; border-color: rgba(0,51,127,0.10); }
.icon-btn-sm:hover { border-color: var(--thw-blue); color: var(--thw-blue); }
.icon-btn-sm.is-active {
    background: var(--thw-blue); color: #fff;
    border-color: var(--thw-blue);
}

/* Subtle alert */
.alert-flash {
    display: flex; align-items: center; gap: 0.625rem;
    padding: 0.6rem 0.875rem; border-radius: 0.5rem;
    background: rgba(34,197,94,0.10);
    border: 1px solid rgba(34,197,94,0.28);
    color: #22c55e;
    font-size: 0.8125rem; font-weight: 600;
    margin-bottom: 1rem;
}
.alert-flash--error {
    background: rgba(239,68,68,0.10);
    border-color: rgba(239,68,68,0.28);
    color: #ef4444;
}

/* Buttons (re-anchor) */
.fmd-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;
    padding: 0.55rem 1rem; border-radius: 0.5rem;
    font-size: 0.875rem; font-weight: 600;
    border: 1px solid transparent; cursor: pointer; text-decoration: none;
    transition: all 0.15s ease; font-family: 'Figtree', sans-serif;
}
.fmd-btn--primary { background: #5b9aff; color: #fff; }
.fmd-btn--primary:hover { background: #3b82f6; }
html.light-mode .fmd-btn--primary { background: var(--thw-blue); }
html.light-mode .fmd-btn--primary:hover { background: #002a66; }
.fmd-btn--ghost {
    background: transparent; color: var(--text-secondary);
    border-color: rgba(255,255,255,0.10);
}
html.light-mode .fmd-btn--ghost { border-color: rgba(0,51,127,0.10); }
.fmd-btn--ghost:hover { color: var(--text-primary); border-color: #5b9aff; }
.fmd-btn[disabled] { opacity: 0.55; cursor: not-allowed; }

/* Mobile (≤ 640px) */
@media (max-width: 640px) {
    .fmd-hero h1 { font-size: 1.4rem; }
    .fmd-hero { gap: 0.75rem; margin-bottom: 1rem; }
    .fmd-hero__status { align-items: flex-start; width: 100%; }
    .fmd-card { padding: 1rem; border-radius: 0.75rem; }
    .fmd-card__h { flex-wrap: wrap; row-gap: 0.5rem; }
    .q-text { font-size: 1.0625rem; }
    .ans-row { padding: 0.625rem 0.75rem; gap: 0.625rem; }
    .ans-letter { width: 1.5rem; height: 1.5rem; font-size: 0.75rem; }
    .ans-flag { font-size: 0.5625rem; }
    .form-row-2 { grid-template-columns: 1fr; }
    .ans-edit-row { grid-template-columns: 1.5rem 1fr; row-gap: 0.4rem; }
    .ans-edit-row .ans-edit-correct { grid-column: 2; justify-self: start; }
    .meta-row { flex-direction: column; align-items: flex-start; gap: 0.2rem; }
    .meta-row__value { text-align: left; }
    .fmd-topbar { gap: 0.5rem; }
    .fmd-actions { width: 100%; justify-content: flex-start; }
    .danger-row { flex-direction: column; align-items: stretch; gap: 0.625rem; }
    .danger-row .btn-danger { align-self: flex-start; }
    .composer__bar { flex-direction: column; align-items: stretch; gap: 0.5rem; }
    .composer__bar > div { justify-content: flex-end; }
    .activity-item { grid-template-columns: 32px 1fr; gap: 0.625rem; }
    .activity-item__avatar { width: 32px; height: 32px; }
    .activity-feed::before { left: 16px; }
    .activity-item__time { margin-left: 0; flex-basis: 100%; }
    .fmd-hero__id { font-size: 0.625rem; letter-spacing: 0.08em; }
}

[x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
@php
    $statusLabels = [
        'open'      => 'Offen',
        'in_review' => 'In Bearbeitung',
        'resolved'  => 'Gelöst',
        'rejected'  => 'Abgelehnt',
    ];
    $statusDescriptions = [
        'open'      => 'Wartet auf Sichtung',
        'in_review' => 'Wird gerade bearbeitet',
        'resolved'  => 'Problem behoben',
        'rejected'  => 'Keine Aktion nötig',
    ];

    $sol = $question
        ? collect(explode(',', (string) ($question->loesung ?? '')))
            ->map(fn ($s) => strtoupper(trim($s)))
            ->filter()
            ->all()
        : [];

    $editAction = ($question && $type !== 'extra')
        ? ($type === 'lehrgang'
            ? route('admin.lehrgaenge.update-question', ['lehrgang' => $question->lehrgang_id, 'question' => $question->id])
            : route('admin.questions.update', $question))
        : null;
    $editMethod = $type === 'lehrgang' ? 'PATCH' : 'PUT';

    // Aktivitäten zusammenstellen — wie bisher (synthetischer Initial-Eintrag falls keine 'report'-Zeile existiert)
    $activities = $issue->reports->sortBy('created_at')->values();
    $hasReportRow = $activities->contains(fn ($a) => ($a->type ?? 'report') === 'report');
    if (! $hasReportRow && $issue->reportedByUser) {
        $activities = collect([(object) [
            '_synthetic' => true,
            'type' => 'report',
            'message' => $issue->latest_message,
            'user' => $issue->reportedByUser,
            'created_at' => $issue->created_at,
            'meta' => null,
        ]])->merge($activities)->values();
    }

    $reportCount = $issue->report_count;
    $lastReport = $activities->where('type', 'report')->last();
    $firstReporter = $activities->where('type', 'report')->first()?->user ?? $issue->reportedByUser;

    $currentUser = auth()->user();
    $currentRole = $currentUser->useroll === 'admin' ? 'admin' : 'contributor';

    $activityCounts = [
        'all'    => $activities->count(),
        'note'   => $activities->where('type', 'note')->count(),
        'status' => $activities->where('type', 'status_change')->count() + $activities->where('type', 'assignment')->count(),
        'report' => $activities->where('type', 'report')->count(),
    ];
@endphp

<div class="fmd-container"
     x-data="fmdDetail({
        issueId: {{ $issue->id }},
        type: '{{ $type }}',
        currentUser: {{ Js::from([
            'id' => $currentUser->id,
            'name' => $currentUser->name,
            'avatar_url' => $currentUser->avatar_url,
            'role' => $currentRole === 'admin' ? 'Admin' : 'Contributor',
        ]) }},
        mentionables: {{ Js::from($mentionables) }},
        urls: {
            assign: '{{ route('admin.issues.assign', array_filter(['issue' => $issue->id, 'type' => $type, 'status' => $filterStatus !== 'all' ? $filterStatus : null, 'source' => $filterSource !== 'all' ? $filterSource : null, 'sort' => $filterSort !== 'recent' ? $filterSort : null])) }}',
            update: '{{ route('admin.issues.update', array_filter(['issue' => $issue->id, 'type' => $type, 'status' => $filterStatus !== 'all' ? $filterStatus : null, 'source' => $filterSource !== 'all' ? $filterSource : null, 'sort' => $filterSort !== 'recent' ? $filterSort : null])) }}',
        },
        initialStatus: '{{ $issue->status }}',
        initialAssignee: {{ Js::from($issue->assignee ? [
            'id' => $issue->assignee->id,
            'name' => $issue->assignee->name,
            'avatar_url' => $issue->assignee->avatar_url,
            'role' => $issue->assignee->useroll === 'admin' ? 'Admin' : 'Contributor',
        ] : null) }},
        activityCounts: {{ Js::from($activityCounts) }},
     })">

    {{-- Top bar — breadcrumb + actions --}}
    <div class="fmd-topbar">
        <div class="fmd-crumb">
            <a href="{{ route('admin.dashboard') }}"><i class="bi bi-arrow-left" style="margin-right: 4px;"></i>Admin</a>
            <span class="fmd-crumb__sep">/</span>
            <a href="{{ route('admin.issues.index', array_filter(['status' => $filterStatus !== 'all' ? $filterStatus : null, 'source' => $filterSource !== 'all' ? $filterSource : null, 'sort' => $filterSort !== 'recent' ? $filterSort : null])) }}">Fehlermeldungen</a>
            <span class="fmd-crumb__sep">/</span>
            <span class="fmd-crumb__current">#{{ $issue->id }}</span>
        </div>
        <div class="fmd-actions">
            @if($listTotal !== null && $listTotal > 1)
                @php
                    $navParams = ['status' => $filterStatus, 'source' => $filterSource, 'sort' => $filterSort];
                    $prevUrl = $prevIssue
                        ? route('admin.issues.show', array_merge(['issue' => $prevIssue->id, 'type' => $prevIssue->type], $navParams))
                        : null;
                    $nextUrl = $nextIssue
                        ? route('admin.issues.show', array_merge(['issue' => $nextIssue->id, 'type' => $nextIssue->type], $navParams))
                        : null;
                @endphp
                <div class="fmd-nav" role="group" aria-label="Navigation zwischen Fehlermeldungen">
                    @if($prevUrl)
                        <a href="{{ $prevUrl }}" class="fmd-nav__btn" title="Vorherige Fehlermeldung" rel="prev">
                            <i class="bi bi-chevron-left"></i>
                            <span>Vorherige</span>
                        </a>
                    @else
                        <span class="fmd-nav__btn is-disabled" aria-disabled="true">
                            <i class="bi bi-chevron-left"></i>
                            <span>Vorherige</span>
                        </span>
                    @endif
                    <span class="fmd-nav__sep"></span>
                    <span class="fmd-nav__pos">{{ $listPosition }} / {{ $listTotal }}</span>
                    <span class="fmd-nav__sep"></span>
                    @if($nextUrl)
                        <a href="{{ $nextUrl }}" class="fmd-nav__btn" title="Nächste Fehlermeldung" rel="next">
                            <span>Nächste</span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    @else
                        <span class="fmd-nav__btn is-disabled" aria-disabled="true">
                            <span>Nächste</span>
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    @endif
                </div>
            @endif
            <span class="role-hint {{ $currentRole === 'admin' ? 'role-hint--admin' : '' }}">
                <i class="bi {{ $currentRole === 'admin' ? 'bi-shield-fill' : 'bi-pencil-square' }}"></i>
                {{ $currentRole === 'admin' ? 'Du bist Admin' : 'Du bist Contributor' }}
            </span>
        </div>
    </div>

    {{-- Hero --}}
    <div class="fmd-hero">
        <div class="fmd-hero__main">
            <div class="fmd-hero__id">
                <i class="bi bi-bug-fill" style="color: #ef4444; font-size: 0.75rem;"></i>
                Fehlermeldung · FM-{{ str_pad($issue->id, 3, '0', STR_PAD_LEFT) }}
                @if($question)
                    <span class="dot"></span>
                    <span>{{ $type === 'lehrgang' ? 'Lehrgang' : 'Grundausbildung' }}</span>
                    @if($contextLabel && $type === 'lehrgang')
                        <span class="dot"></span><span>{{ $contextLabel }}</span>
                    @endif
                    <span class="dot"></span>
                    <span>Lernabschnitt {{ $question->lernabschnitt ?? '-' }}</span>
                    <span class="dot"></span>
                    <span>Frage #{{ $question->nummer ?? '-' }}</span>
                @endif
            </div>
            <h1>{{ $question?->frage ?? 'Frage wurde gelöscht' }}</h1>
            <div class="fmd-hero__meta">
                <span>
                    Erstmals gemeldet von
                    <strong style="color: var(--text-primary);">{{ $firstReporter?->name ?? 'Anonym' }}</strong>
                </span>
                <span class="dot"></span>
                <span>{{ $reportCount }}× gemeldet</span>
                <span class="dot"></span>
                <span>Aktualisiert {{ $issue->updated_at?->format('d.m.Y H:i') ?? '-' }}</span>
            </div>
        </div>
        <div class="fmd-hero__status">
            <span class="fmd-hero__status-label">Status</span>
            <div x-ref="statusHeader" style="position: relative;">
                <button type="button"
                        class="status-pill status-pill--lg"
                        :class="'status-pill--' + status"
                        @click="statusOpen = !statusOpen; assigneeOpen = false">
                    <span class="dot"></span>
                    <span x-text="statusLabels[status]"></span>
                    <i class="bi bi-chevron-down chev"></i>
                </button>
                <div class="menu-pop" x-show="statusOpen" x-cloak
                     @click.outside="statusOpen = false"
                     style="top: calc(100% + 6px); right: 0;">
                    <template x-for="opt in statusOptions" :key="opt.id">
                        <button type="button" class="menu-item" @click="changeStatus(opt.id)">
                            <span class="status-pill" :class="'status-pill--' + opt.id">
                                <span class="dot"></span>
                                <span x-text="opt.label"></span>
                            </span>
                            <span style="color: var(--text-muted); font-size: 0.6875rem; margin-left: auto;" x-text="opt.desc"></span>
                            <i class="bi bi-check2 menu-item__check" x-show="opt.id === status"></i>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-flash">
            <i class="bi bi-check-circle-fill"></i>
            <div><strong>{{ session('success') }}</strong></div>
        </div>
    @endif

    <div x-show="flash" x-cloak class="alert-flash">
        <i class="bi bi-check-circle-fill"></i>
        <div>
            <strong>Status aktualisiert</strong>
            <span style="margin-left: 8px; font-weight: 500; color: var(--text-secondary);"
                  x-text="'Neuer Status: ' + statusLabels[status]"></span>
        </div>
        <button type="button" class="icon-btn-sm" style="margin-left: auto;" @click="flash = false" title="Schließen">
            <i class="bi bi-x"></i>
        </button>
    </div>

    @if($errors->any())
        <div class="alert-flash alert-flash--error">
            <i class="bi bi-x-circle-fill"></i>
            <div>
                <strong>Fehler beim Speichern</strong>
                <ul style="margin: 0.25rem 0 0 1.25rem; padding: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Two-col grid --}}
    <div class="fmd-grid">
        {{-- Linke Spalte --}}
        <div>
            {{-- Frage Card --}}
            <div class="fmd-card" x-data="{ editing: false, saving: false, saveError: '' }">
                <div class="fmd-card__h">
                    <span class="section-label">
                        <i class="bi bi-patch-question-fill" style="margin-right: 6px; color: #5b9aff;"></i>
                        Frage
                    </span>
                    <div class="fmd-card__h-actions">
                        @if($question && $type !== 'extra')
                            <a href="{{ $type === 'lehrgang' && $question->lehrgang_id ? route('admin.lehrgaenge.edit', $question->lehrgang_id) : '#' }}"
                               class="icon-btn-sm" title="Frage öffnen">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                            <button type="button"
                                    class="icon-btn-sm"
                                    :class="{ 'is-active': editing }"
                                    @click="editing = !editing"
                                    title="Frage bearbeiten">
                                <i class="bi" :class="editing ? 'bi-x-lg' : 'bi-pencil'"></i>
                            </button>
                        @endif
                    </div>
                </div>

                @if($question)
                    @if($type === 'extra')
                        {{-- Zusatzfragen: nur Frage-Text + Typ-Hinweis; Editor liegt in /admin/extra-questions --}}
                        <p class="q-text">{{ $question->frage }}</p>
                        <div style="margin-top: 0.875rem; padding: 0.875rem 1rem; border-radius: 0.625rem; background: var(--glass-bg); font-size: 0.875rem; color: var(--text-secondary); line-height: 1.55;">
                            <div class="section-label" style="margin-bottom: 0.4rem;">Typ</div>
                            {{ $question->typ ?? 'unbekannt' }}
                            <div style="margin-top: 0.5rem; font-size: 0.8125rem; color: var(--text-muted);">
                                Antworten und Lösungsoptionen siehe Zusatzfragen-Editor.
                            </div>
                        </div>
                        <div class="q-footer">
                            <span>Frage-ID #{{ $question->id }}</span>
                            <span class="q-footer__right">
                                <a href="{{ route('admin.extra-questions.edit', $question->id) }}" style="color: #5b9aff; text-decoration: none;">
                                    Editor öffnen <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </span>
                        </div>
                    @else
                    {{-- View-Modus --}}
                    <div x-show="!editing" x-cloak>
                        <p class="q-text">{{ $question->frage }}</p>
                        <div class="ans-list">
                            @foreach(['A' => $question->antwort_a, 'B' => $question->antwort_b, 'C' => $question->antwort_c] as $letter => $text)
                                <div class="ans-row {{ in_array($letter, $sol, true) ? 'is-correct' : '' }}">
                                    <div class="ans-letter">{{ $letter }}</div>
                                    <p class="ans-text">{{ $text }}</p>
                                    @if(in_array($letter, $sol, true))
                                        <span class="ans-flag"><i class="bi bi-check-circle-fill"></i> Richtig</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="q-footer">
                            <span>Frage-ID #{{ $question->id }}</span>
                            <span class="q-footer__right">Lösung: {{ $question->loesung }}</span>
                        </div>
                        @if(!empty($question->loesungsweg ?? null))
                            <div style="margin-top: 0.875rem; padding: 0.875rem 1rem; border-radius: 0.625rem; background: var(--glass-bg); font-size: 0.875rem; color: var(--text-secondary); line-height: 1.55;">
                                <div class="section-label" style="margin-bottom: 0.4rem;">Lösungsweg</div>
                                {{ $question->loesungsweg }}
                            </div>
                        @endif
                    </div>

                    {{-- Edit-Modus --}}
                    <div x-show="editing" x-cloak>
                        <form id="iss-question-edit-form" method="POST" action="{{ $editAction }}"
                              x-data="{ correct: @js($sol) }"
                              @submit.prevent="
                                  saving = true; saveError = '';
                                  fetch($event.target.action, {
                                      method: 'POST',
                                      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                                      body: new FormData($event.target),
                                  })
                                  .then(r => r.ok ? r.json() : Promise.reject(new Error('HTTP ' + r.status)))
                                  .then(() => window.location.reload())
                                  .catch(err => { saving = false; saveError = 'Speichern fehlgeschlagen — bitte erneut versuchen.'; console.error(err); });
                              ">
                            @csrf
                            @method($editMethod)

                            <div class="form-row-2">
                                <div class="form-field">
                                    <label class="form-field__label">Lernabschnitt</label>
                                    <input type="{{ $type === 'lehrgang' ? 'number' : 'text' }}"
                                           name="lernabschnitt" class="form-input"
                                           value="{{ old('lernabschnitt', $question->lernabschnitt) }}" required>
                                </div>
                                <div class="form-field">
                                    <label class="form-field__label">Frage-Nr.</label>
                                    <input type="number" name="nummer" class="form-input"
                                           value="{{ old('nummer', $question->nummer) }}" required>
                                </div>
                            </div>

                            <div class="form-field">
                                <label class="form-field__label">Frage</label>
                                <textarea name="frage" class="form-textarea" required>{{ old('frage', $question->frage) }}</textarea>
                            </div>

                            <div class="form-field">
                                <label class="form-field__label">Antworten — Richtige markieren</label>
                                @foreach(['A' => 'antwort_a', 'B' => 'antwort_b', 'C' => 'antwort_c'] as $letter => $field)
                                    <div class="ans-edit-row">
                                        <div class="ans-letter">{{ $letter }}</div>
                                        <input type="text" name="{{ $field }}" class="form-input"
                                               value="{{ old($field, $question->{$field}) }}" required>
                                        <label class="ans-edit-correct"
                                               :class="{ 'is-on': correct.includes('{{ $letter }}') }">
                                            <input type="checkbox" value="{{ $letter }}" x-model="correct">
                                            <i class="bi" :class="correct.includes('{{ $letter }}') ? 'bi-check-circle-fill' : 'bi-circle'"></i>
                                            Richtig
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <input type="hidden" name="loesung" x-bind:value="[...correct].sort().join(',')">

                            @if($type === 'question')
                                <div class="form-field">
                                    <label class="form-field__label">Lösungsweg <span style="text-transform: none; color: var(--text-muted); font-weight: 500;">(optional)</span></label>
                                    <textarea name="loesungsweg" class="form-textarea">{{ old('loesungsweg', $question->loesungsweg ?? '') }}</textarea>
                                </div>
                            @endif

                            <div style="display: flex; gap: 0.5rem; margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid var(--glass-border-lo); align-items: center; flex-wrap: wrap;">
                                <button type="submit" class="fmd-btn fmd-btn--primary" :disabled="saving">
                                    <i class="bi" :class="saving ? 'bi-hourglass-split' : 'bi-check-lg'"></i>
                                    <span x-text="saving ? 'Speichere…' : 'Speichern'"></span>
                                </button>
                                <button type="button" class="fmd-btn fmd-btn--ghost" :disabled="saving" @click="editing = false; saveError = ''">
                                    Abbrechen
                                </button>
                                <span x-show="saveError" x-cloak x-text="saveError"
                                      style="color: #ef4444; font-size: 0.85rem;"></span>
                            </div>
                        </form>
                    </div>
                    @endif {{-- /if $type === 'extra' --}}
                @else
                    <div class="alert-flash alert-flash--error" style="margin: 0;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            <strong>Frage nicht gefunden</strong>
                            <div>Diese Frage wurde gelöscht oder existiert nicht mehr.</div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Activity Card --}}
            <div class="fmd-card">
                <div class="fmd-card__h">
                    <span class="section-label">
                        <i class="bi bi-activity" style="margin-right: 6px; color: #5b9aff;"></i>
                        Aktivität
                    </span>
                    <div class="activity-tabs">
                        @foreach([
                            ['id' => 'all',    'label' => 'Alle',           'icon' => 'bi-stack'],
                            ['id' => 'note',   'label' => 'Kommentare',     'icon' => 'bi-chat-square-text'],
                            ['id' => 'status', 'label' => 'Statuswechsel',  'icon' => 'bi-arrow-left-right'],
                            ['id' => 'report', 'label' => 'Meldungen',      'icon' => 'bi-flag'],
                        ] as $t)
                            <button type="button"
                                    class="activity-tab"
                                    :class="{ 'is-active': activeTab === '{{ $t['id'] }}' }"
                                    @click="activeTab = '{{ $t['id'] }}'">
                                <i class="bi {{ $t['icon'] }}"></i>
                                {{ $t['label'] }}
                                <span class="activity-tab__count">{{ $activityCounts[$t['id']] ?? 0 }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="activity-feed">
                    @foreach($activities as $act)
                        @php
                            $actType = $act->type ?? 'report';
                            $actUser = $act->user ?? null;
                            $actUserName = $actUser?->name ?? 'Anonym';
                            $actAvatar = $actUser?->avatar_url;
                            $matchTab = match ($actType) {
                                'note' => 'note',
                                'status_change', 'assignment' => 'status',
                                default => 'report',
                            };
                        @endphp

                        @if($actType === 'status_change')
                            @php
                                $old = $act->meta['old'] ?? null;
                                $new = $act->meta['new'] ?? null;
                            @endphp
                            <div class="activity-item"
                                 x-show="activeTab === 'all' || activeTab === '{{ $matchTab }}'">
                                <div class="activity-item__avatar activity-item__avatar--status">
                                    <i class="bi bi-arrow-left-right"></i>
                                </div>
                                <div style="min-width: 0;">
                                    <div class="activity-item__head">
                                        <span class="activity-item__name">{{ $actUserName }}</span>
                                        <span class="activity-item__type activity-item__type--status">Statuswechsel</span>
                                        <span class="activity-item__time">{{ $act->created_at?->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <div class="activity-item__status-line">
                                        änderte Status
                                        @if($old)
                                            von <span class="status-pill status-pill--{{ $old }}"><span class="dot"></span>{{ $statusLabels[$old] ?? $old }}</span>
                                        @endif
                                        @if($new)
                                            auf <span class="status-pill status-pill--{{ $new }}"><span class="dot"></span>{{ $statusLabels[$new] ?? $new }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @elseif($actType === 'assignment')
                            @php
                                $oldName = $act->meta['old_name'] ?? null;
                                $newName = $act->meta['new_name'] ?? null;
                            @endphp
                            <div class="activity-item"
                                 x-show="activeTab === 'all' || activeTab === '{{ $matchTab }}'">
                                <div class="activity-item__avatar activity-item__avatar--status">
                                    <i class="bi bi-person-check"></i>
                                </div>
                                <div style="min-width: 0;">
                                    <div class="activity-item__head">
                                        <span class="activity-item__name">{{ $actUserName }}</span>
                                        <span class="activity-item__type activity-item__type--assignment">Zuweisung</span>
                                        <span class="activity-item__time">{{ $act->created_at?->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <div class="activity-item__status-line">
                                        @if($newName && $oldName)
                                            verschob Bearbeiter von <strong style="color: var(--text-primary);">{{ $oldName }}</strong>
                                            auf <strong style="color: var(--text-primary);">{{ $newName }}</strong>
                                        @elseif($newName)
                                            wies <strong style="color: var(--text-primary);">{{ $newName }}</strong> zu
                                        @elseif($oldName)
                                            entfernte Zuweisung von <strong style="color: var(--text-primary);">{{ $oldName }}</strong>
                                        @else
                                            änderte die Zuweisung
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @elseif($actType === 'note')
                            <div class="activity-item"
                                 x-show="activeTab === 'all' || activeTab === '{{ $matchTab }}'">
                                <div class="activity-item__avatar">
                                    @if($actAvatar)
                                        <img src="{{ $actAvatar }}" alt="{{ $actUserName }}">
                                    @else
                                        {{ strtoupper(substr($actUserName, 0, 1)) }}
                                    @endif
                                </div>
                                <div style="min-width: 0;">
                                    <div class="activity-item__head">
                                        <span class="activity-item__name">{{ $actUserName }}</span>
                                        <span class="activity-item__type activity-item__type--note">Kommentar</span>
                                        <span class="activity-item__time">{{ $act->created_at?->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <p class="activity-item__msg">{!! fmdRenderMentions($act->message) !!}</p>
                                </div>
                            </div>
                        @else
                            <div class="activity-item"
                                 x-show="activeTab === 'all' || activeTab === '{{ $matchTab }}'">
                                <div class="activity-item__avatar">
                                    @if($actAvatar)
                                        <img src="{{ $actAvatar }}" alt="{{ $actUserName }}">
                                    @else
                                        {{ strtoupper(substr($actUserName, 0, 1)) }}
                                    @endif
                                </div>
                                <div style="min-width: 0;">
                                    <div class="activity-item__head">
                                        <span class="activity-item__name">{{ $actUserName }}</span>
                                        <span class="activity-item__type activity-item__type--report">
                                            <i class="bi bi-flag-fill" style="margin-right: 4px;"></i>Meldung
                                        </span>
                                        <span class="activity-item__time">{{ $act->created_at?->format('d.m.Y H:i') }}</span>
                                    </div>
                                    @if(!empty($act->message))
                                        <p class="activity-item__msg">{{ $act->message }}</p>
                                    @else
                                        <p class="activity-item__msg" style="color: var(--text-muted); font-style: italic;">Keine Nachricht</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <div x-show="filteredEmpty" x-cloak
                         style="padding: 1.5rem 0; text-align: center; color: var(--text-muted); font-size: 0.8125rem;">
                        Aktuell keine Einträge in dieser Ansicht.
                    </div>
                </div>

                {{-- Composer mit @mention — für Zusatzfragen (noch) nicht verfügbar --}}
                @if($type === 'extra')
                <div style="padding: 0.875rem 1rem; border-radius: 0.625rem; background: var(--glass-bg); font-size: 0.8125rem; color: var(--text-muted); line-height: 1.5;">
                    Kommentare und Erwähnungen sind für Zusatzfragen aktuell nicht verfügbar.
                </div>
                @else
                <form method="POST"
                      action="{{ route('admin.issues.comments.store', array_filter(['issue' => $issue->id, 'type' => $type, 'status' => $filterStatus !== 'all' ? $filterStatus : null, 'source' => $filterSource !== 'all' ? $filterSource : null, 'sort' => $filterSort !== 'recent' ? $filterSort : null])) }}"
                      class="composer"
                      @submit="onSubmit($event)">
                    @csrf
                    <div class="composer__avatar">
                        @if($currentUser->avatar_url)
                            <img src="{{ $currentUser->avatar_url }}" alt="{{ $currentUser->name }}">
                        @else
                            {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="composer__body">
                        <textarea name="message"
                                  x-ref="composer"
                                  x-model="comment"
                                  @input="onComposerInput($event)"
                                  @keydown="onComposerKeydown($event)"
                                  placeholder="Kommentar hinzufügen… Tippe @ um jemanden zu erwähnen"
                                  rows="2"
                                  maxlength="2000"
                                  required></textarea>
                        <div class="composer__bar">
                            <span class="composer__hint">
                                <kbd>@</kbd> erwähnen · <kbd>⌘</kbd>+<kbd>↵</kbd> senden
                            </span>
                            <div style="display: flex; gap: 0.4rem;">
                                <button type="button" class="fmd-btn fmd-btn--ghost" @click="comment = ''" :disabled="!comment.trim()">
                                    Verwerfen
                                </button>
                                <button type="submit" class="fmd-btn fmd-btn--primary" :disabled="!comment.trim()">
                                    <i class="bi bi-send-fill"></i> Kommentieren
                                </button>
                            </div>
                        </div>
                        <div class="mention-pop" x-show="mentionOpen && filteredMentions.length"
                             x-cloak
                             style="left: 0; right: 0; bottom: calc(100% + 4px);">
                            <template x-for="(p, i) in filteredMentions" :key="p.id">
                                <div class="mention-item"
                                     :class="{ 'is-active': i === mentionActive }"
                                     @mousedown.prevent="insertMention(p)"
                                     @mouseenter="mentionActive = i">
                                    <div class="mention-item__avatar">
                                        <template x-if="p.avatar_url"><img :src="p.avatar_url" :alt="p.name"></template>
                                        <template x-if="!p.avatar_url"><span x-text="initials(p.name)"></span></template>
                                    </div>
                                    <div x-text="p.name"></div>
                                    <span class="mention-item__role" x-text="p.role"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </form>
                @endif {{-- /if $type === 'extra' (composer) --}}
            </div>
        </div>

        {{-- Right rail --}}
        <div>
            {{-- Status Card --}}
            <div class="fmd-card">
                <div class="rail-row">
                    <span class="section-label">Status</span>
                    <span class="rail-row__hint" x-text="statusDesc[status]"></span>
                </div>
                <div x-ref="statusRail" style="position: relative;">
                    <button type="button"
                            class="status-pill"
                            :class="'status-pill--' + status"
                            @click="statusRailOpen = !statusRailOpen; assigneeOpen = false"
                            style="width: 100%; justify-content: space-between;">
                        <span style="display: inline-flex; align-items: center; gap: 0.4rem;">
                            <span class="dot"></span>
                            <span x-text="statusLabels[status]"></span>
                        </span>
                        <i class="bi bi-chevron-down chev"></i>
                    </button>
                    <div class="menu-pop" x-show="statusRailOpen" x-cloak
                         @click.outside="statusRailOpen = false"
                         style="top: calc(100% + 6px); left: 0; right: 0;">
                        <template x-for="opt in statusOptions" :key="opt.id">
                            <button type="button" class="menu-item" @click="changeStatus(opt.id); statusRailOpen = false">
                                <span class="status-pill" :class="'status-pill--' + opt.id">
                                    <span class="dot"></span>
                                    <span x-text="opt.label"></span>
                                </span>
                                <span style="color: var(--text-muted); font-size: 0.6875rem; margin-left: auto;" x-text="opt.desc"></span>
                                <i class="bi bi-check2 menu-item__check" x-show="opt.id === status"></i>
                            </button>
                        </template>
                    </div>
                </div>
                <div style="font-family: 'IBM Plex Mono', monospace; font-size: 0.625rem; color: var(--text-muted); margin-top: 0.5rem; letter-spacing: 0.06em;">
                    Statuswechsel erscheinen als Eintrag in der Aktivität.
                </div>
            </div>

            {{-- Assignee Card --}}
            <div class="fmd-card">
                <div class="rail-row">
                    <span class="section-label">Zugewiesen an</span>
                    <button type="button" class="rail-row__hint"
                            x-show="!assignee || assignee.id !== currentUser.id"
                            @click="assignTo(currentUser)">
                        Mir zuweisen
                    </button>
                </div>
                <div x-ref="assigneeWrap" style="position: relative;">
                    <button type="button" class="person-card"
                            @click="assigneeOpen = !assigneeOpen; statusOpen = false; statusRailOpen = false">
                        <template x-if="assignee">
                            <div class="person-card__avatar">
                                <template x-if="assignee.avatar_url"><img :src="assignee.avatar_url" :alt="assignee.name"></template>
                                <template x-if="!assignee.avatar_url"><span x-text="initials(assignee.name)"></span></template>
                            </div>
                        </template>
                        <template x-if="!assignee">
                            <div class="person-card__avatar person-card__avatar--empty">
                                <i class="bi bi-person"></i>
                            </div>
                        </template>
                        <div style="min-width: 0; flex: 1;">
                            <div class="person-card__name" x-text="assignee ? assignee.name : 'Niemand zugewiesen'"></div>
                            <div class="person-card__sub" x-text="assignee ? assignee.role : 'Klicken zum Zuweisen'"></div>
                        </div>
                        <i class="bi bi-chevron-down person-card__chev"></i>
                    </button>

                    <div class="menu-pop" x-show="assigneeOpen" x-cloak
                         @click.outside="assigneeOpen = false"
                         style="top: calc(100% + 6px); left: 0; right: 0;">
                        <button type="button" class="menu-item" @click="assignTo(null)">
                            <span class="person-card__avatar person-card__avatar--empty"
                                  style="width: 22px; height: 22px; font-size: 0.625rem;">
                                <i class="bi bi-x"></i>
                            </span>
                            <span style="flex: 1;">Niemand</span>
                            <i class="bi bi-check2 menu-item__check" x-show="!assignee"></i>
                        </button>
                        <template x-for="p in mentionables" :key="p.id">
                            <button type="button" class="menu-item" @click="assignTo(p)">
                                <span class="person-card__avatar"
                                      style="width: 22px; height: 22px; font-size: 0.625rem;">
                                    <template x-if="p.avatar_url"><img :src="p.avatar_url" :alt="p.name"></template>
                                    <template x-if="!p.avatar_url"><span x-text="initials(p.name)"></span></template>
                                </span>
                                <span style="flex: 1;" x-text="p.name"></span>
                                <span class="person-card__sub" style="margin-right: 4px;" x-text="p.role"></span>
                                <i class="bi bi-check2 menu-item__check" x-show="assignee && assignee.id === p.id"></i>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Reporter Card --}}
            <div class="fmd-card">
                <div class="rail-row">
                    <span class="section-label">Meldender</span>
                </div>
                <div class="person-card" style="cursor: default;">
                    <div class="person-card__avatar">
                        @if($firstReporter?->avatar_url)
                            <img src="{{ $firstReporter->avatar_url }}" alt="{{ $firstReporter->name }}">
                        @elseif($firstReporter)
                            {{ strtoupper(substr($firstReporter->name, 0, 1)) }}
                        @else
                            <i class="bi bi-person"></i>
                        @endif
                    </div>
                    <div>
                        <div class="person-card__name">{{ $firstReporter?->name ?? 'Anonym' }}</div>
                        <div class="person-card__sub">{{ $reportCount > 1 ? '+' . ($reportCount - 1) . ' weitere' : 'Erster Bericht' }}</div>
                    </div>
                </div>
            </div>

            {{-- Details Card --}}
            <div class="fmd-card">
                <div class="rail-row">
                    <span class="section-label">Details</span>
                </div>
                <div class="meta-list">
                    <div class="meta-row">
                        <span class="meta-row__label">Quelle</span>
                        <span class="meta-row__value">
                            <span class="status-pill status-pill--open" style="padding: 0.2rem 0.55rem;">
                                <i class="bi bi-bookmark-fill" style="font-size: 0.625rem;"></i>
                                {{ $type === 'lehrgang' ? 'Lehrgang' : 'Grundausbildung' }}
                            </span>
                        </span>
                    </div>
                    @if($question)
                        @foreach($contextDetails as $label => $value)
                            <div class="meta-row">
                                <span class="meta-row__label">{{ $label }}</span>
                                <span class="meta-row__value">{{ $value }}</span>
                            </div>
                        @endforeach
                        <div class="meta-row">
                            <span class="meta-row__label">Frage-ID</span>
                            <span class="meta-row__value"><span class="ts">#{{ $question->id }}</span></span>
                        </div>
                    @endif
                    <div class="meta-row">
                        <span class="meta-row__label">Meldungen</span>
                        <span class="meta-row__value">{{ $reportCount }}×</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-row__label">Erstellt</span>
                        <span class="meta-row__value"><span class="ts">{{ $issue->created_at?->format('d.m.Y H:i') ?? '-' }}</span></span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-row__label">Aktualisiert</span>
                        <span class="meta-row__value"><span class="ts">{{ $issue->updated_at?->format('d.m.Y H:i') ?? '-' }}</span></span>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            @if($currentUser->isAdmin())
                <div class="fmd-card danger-card">
                    <div class="rail-row">
                        <span class="section-label" style="color: #ef4444;">Gefahrenzone</span>
                    </div>
                    <div class="danger-row">
                        <div class="danger-row__txt">Die Fehlermeldung dauerhaft entfernen. Die Aktivitätshistorie geht verloren.</div>
                        <button type="button" class="btn-danger" onclick="confirmIssueDelete()">
                            <i class="bi bi-trash"></i> Löschen
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<form id="deleteForm" method="POST"
      action="{{ route('admin.issues.destroy', ['issue' => $issue->id, 'type' => $type]) }}"
      style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function confirmIssueDelete() {
        if (confirm('Willst du diese Fehlermeldung wirklich löschen?')) {
            document.getElementById('deleteForm').submit();
        }
    }
</script>

@push('alpine-components')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('fmdDetail', (config) => ({
            issueId: config.issueId,
            type: config.type,
            currentUser: config.currentUser,
            mentionables: config.mentionables,
            urls: config.urls,
            status: config.initialStatus,
            assignee: config.initialAssignee,

            statusOpen: false,
            statusRailOpen: false,
            assigneeOpen: false,
            flash: false,
            activityCounts: config.activityCounts,

            activeTab: 'all',
            comment: '',
            mentionOpen: false,
            mentionQuery: '',
            mentionActive: 0,

            statusLabels: {
                open: 'Offen',
                in_review: 'In Bearbeitung',
                resolved: 'Gelöst',
                rejected: 'Abgelehnt',
            },
            statusDesc: {
                open: 'Wartet auf Sichtung',
                in_review: 'Wird gerade bearbeitet',
                resolved: 'Problem behoben',
                rejected: 'Keine Aktion nötig',
            },
            statusOptions: [
                { id: 'open',      label: 'Offen',          desc: 'Wartet auf Sichtung' },
                { id: 'in_review', label: 'In Bearbeitung', desc: 'Wird gerade bearbeitet' },
                { id: 'resolved',  label: 'Gelöst',         desc: 'Problem behoben' },
                { id: 'rejected',  label: 'Abgelehnt',      desc: 'Keine Aktion nötig' },
            ],

            get filteredMentions() {
                const q = (this.mentionQuery || '').toLowerCase();
                return this.mentionables
                    .filter(p => !q || p.name.toLowerCase().includes(q))
                    .slice(0, 6);
            },

            get filteredEmpty() {
                return (this.activityCounts[this.activeTab] ?? 0) === 0;
            },

            initials(name) {
                if (!name) return '?';
                return name.split(' ').map(p => p[0]).slice(0, 2).join('').toUpperCase();
            },

            csrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.content || '';
            },

            async changeStatus(next) {
                if (next === this.status) {
                    this.statusOpen = false;
                    this.statusRailOpen = false;
                    return;
                }
                const oldStatus = this.status;
                this.status = next;
                this.statusOpen = false;
                this.statusRailOpen = false;
                this.flash = true;
                setTimeout(() => { this.flash = false; }, 3200);

                try {
                    const fd = new FormData();
                    fd.append('_method', 'PUT');
                    fd.append('_token', this.csrfToken());
                    fd.append('status', next);

                    const res = await fetch(this.urls.update, {
                        method: 'POST',
                        body: fd,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) throw new Error('http ' + res.status);
                    // Seite neu laden, damit der Aktivitäts-Feed den Statuswechsel zeigt
                    setTimeout(() => window.location.reload(), 600);
                } catch (e) {
                    this.status = oldStatus;
                    this.flash = false;
                    alert('Status konnte nicht gespeichert werden.');
                }
            },

            async assignTo(person) {
                const previous = this.assignee;
                this.assignee = person;
                this.assigneeOpen = false;

                try {
                    const fd = new FormData();
                    fd.append('_method', 'PUT');
                    fd.append('_token', this.csrfToken());
                    if (person) fd.append('assignee_id', person.id);

                    const res = await fetch(this.urls.assign, {
                        method: 'POST',
                        body: fd,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) throw new Error('http ' + res.status);
                    setTimeout(() => window.location.reload(), 600);
                } catch (e) {
                    this.assignee = previous;
                    alert('Zuweisung konnte nicht gespeichert werden.');
                }
            },

            onComposerInput(e) {
                this.comment = e.target.value;
                const caret = e.target.selectionStart;
                const before = this.comment.slice(0, caret);
                const m = before.match(/@([\wÄÖÜäöüß]*)$/);
                if (m) {
                    this.mentionQuery = m[1];
                    this.mentionActive = 0;
                    this.mentionOpen = true;
                } else {
                    this.mentionOpen = false;
                }
            },

            onComposerKeydown(e) {
                if (this.mentionOpen && this.filteredMentions.length) {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        this.mentionActive = (this.mentionActive + 1) % this.filteredMentions.length;
                        return;
                    }
                    if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        this.mentionActive = (this.mentionActive - 1 + this.filteredMentions.length) % this.filteredMentions.length;
                        return;
                    }
                    if (e.key === 'Enter' || e.key === 'Tab') {
                        e.preventDefault();
                        this.insertMention(this.filteredMentions[this.mentionActive]);
                        return;
                    }
                    if (e.key === 'Escape') {
                        this.mentionOpen = false;
                        return;
                    }
                }
                if (e.key === 'Enter' && (e.metaKey || e.ctrlKey)) {
                    e.preventDefault();
                    e.target.form?.requestSubmit();
                }
            },

            insertMention(person) {
                if (!person) return;
                const ta = this.$refs.composer;
                const caret = ta.selectionStart;
                const before = this.comment.slice(0, caret);
                const after = this.comment.slice(caret);
                const firstName = person.name.split(' ')[0];
                const newBefore = before.replace(/@([\wÄÖÜäöüß]*)$/, '@' + firstName);
                this.comment = newBefore + ' ' + after;
                this.mentionOpen = false;
                this.mentionQuery = '';
                this.$nextTick(() => {
                    ta.focus();
                    const pos = newBefore.length + 1;
                    ta.setSelectionRange(pos, pos);
                });
            },

            onSubmit(e) {
                if (!this.comment.trim()) e.preventDefault();
            },
        }));
    });
</script>
@endpush

@endsection
