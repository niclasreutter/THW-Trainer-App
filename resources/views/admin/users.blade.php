@extends('layouts.app')
@section('title', 'Nutzerverwaltung - THW Trainer Admin')

@push('styles')
<style>
    /* ======================================================
       Nutzerverwaltung Redesign
       ====================================================== */

    .admin-container { width: 100%; max-width: 1280px; margin: 0 auto; }

    /* --------- DSGVO BANNER --------- */
    .dsgvo-notice {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1rem;
        background: rgba(0,51,127,0.05);
        border: 1px solid rgba(0,51,127,0.10);
        border-radius: 0.5rem;
        font-size: 0.8125rem;
        color: var(--text-muted);
        margin-bottom: 1.5rem;
    }

    /* --------- HEADER STATS --------- */
    .nv-header-stats {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 1100px) { .nv-header-stats { grid-template-columns: repeat(4, 1fr); } }
    @media (max-width: 640px)  { .nv-header-stats { grid-template-columns: repeat(2, 1fr); } }

    .nv-hstat {
        background: #fff;
        border: 1px solid rgba(0,51,127,0.08);
        border-radius: 0.75rem;
        padding: 0.875rem 1rem;
        box-shadow: 0 1px 3px rgba(0,51,127,0.04);
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }
    html:not(.light-mode) .nv-hstat {
        background: var(--bg-card, #1c1c1f);
        border-color: rgba(255,255,255,0.07);
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .nv-hstat__icon {
        width: 28px; height: 28px;
        border-radius: 0.5rem;
        display: grid;
        place-items: center;
        font-size: 0.875rem;
        margin-bottom: 0.4rem;
    }
    .nv-hstat__icon--blue   { background: rgba(0,51,127,0.10);   color: var(--thw-blue); }
    .nv-hstat__icon--gold   { background: rgba(217,119,6,0.12);  color: var(--gold-dark, #b45309); }
    .nv-hstat__icon--green  { background: rgba(34,197,94,0.12);  color: var(--success); }
    .nv-hstat__icon--red    { background: rgba(239,68,68,0.12);  color: var(--error); }
    .nv-hstat__icon--purple { background: rgba(167,139,250,0.14);color: #7c3aed; }
    .nv-hstat__icon--slate  { background: rgba(100,116,139,0.12);color: #64748b; }
    html:not(.light-mode) .nv-hstat__icon--slate { color: #94a3b8; }
    .nv-hstat__value {
        font-weight: 800;
        font-size: 1.5rem;
        line-height: 1.1;
        letter-spacing: -0.015em;
        color: var(--text-primary);
    }
    .nv-hstat__label {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        margin-top: 0.1rem;
    }

    /* --------- ACTION BAR --------- */
    .nv-action-bar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    /* --------- TABLE CARD --------- */
    .nv-table-card {
        background: #fff;
        border: 1px solid rgba(0,51,127,0.08);
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(15,23,42,0.04), 0 2px 12px rgba(0,51,127,0.06);
        overflow: hidden;
    }
    html:not(.light-mode) .nv-table-card {
        background: var(--bg-card, #1c1c1f);
        border-color: rgba(255,255,255,0.07);
        box-shadow: 0 1px 3px rgba(0,0,0,0.25);
    }
    .nv-table-head {
        padding: 1.25rem 1.5rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        border-bottom: 1px solid rgba(0,51,127,0.06);
    }
    html:not(.light-mode) .nv-table-head { border-bottom-color: rgba(255,255,255,0.06); }
    .nv-title-row { display: flex; align-items: baseline; gap: 0.75rem; }
    .nv-count {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* --------- FILTERS --------- */
    .nv-filters { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }
    .nv-search { position: relative; display: flex; align-items: center; }
    .nv-search .bi {
        position: absolute; left: 0.75rem;
        color: var(--text-muted); font-size: 0.875rem; pointer-events: none;
    }
    .nv-search input {
        width: 280px;
        padding: 0.5rem 0.75rem 0.5rem 2rem;
        border-radius: 0.5rem;
        background: #f8fafc;
        border: 1px solid rgba(0,51,127,0.10);
        color: var(--text-primary);
        font-size: 0.875rem;
        transition: border-color 150ms, box-shadow 150ms;
    }
    html:not(.light-mode) .nv-search input { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.10); }
    .nv-search input::placeholder { color: var(--text-muted); }
    .nv-search input:focus { outline: none; border-color: var(--thw-blue); box-shadow: 0 0 0 3px rgba(0,51,127,0.12); background: #fff; }
    html:not(.light-mode) .nv-search input:focus { background: rgba(255,255,255,0.08); }
    .nv-filter-select {
        padding: 0.5rem 2rem 0.5rem 0.75rem;
        border-radius: 0.5rem;
        background: #f8fafc;
        border: 1px solid rgba(0,51,127,0.10);
        color: var(--text-primary);
        font-size: 0.875rem;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%2364748b'%3E%3Cpath d='M4 6l4 4 4-4z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 14px;
        cursor: pointer;
    }
    html:not(.light-mode) .nv-filter-select {
        background-color: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.10);
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%2394a3b8'%3E%3Cpath d='M4 6l4 4 4-4z'/%3E%3C/svg%3E");
    }
    .nv-filter-select:focus { outline: none; border-color: var(--thw-blue); box-shadow: 0 0 0 3px rgba(0,51,127,0.12); }

    /* --------- TABLE --------- */
    .nv-table { width: 100%; border-collapse: collapse; table-layout: auto; }
    .nv-table thead th {
        text-align: left;
        padding: 0.75rem 1rem;
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        background: #f8fafc;
        border-bottom: 1px solid rgba(0,51,127,0.06);
        white-space: nowrap;
    }
    html:not(.light-mode) .nv-table thead th {
        background: rgba(255,255,255,0.03);
        border-bottom-color: rgba(255,255,255,0.06);
    }
    .nv-table tbody td {
        padding: 0.875rem 1rem;
        font-size: 0.9375rem;
        color: var(--text-primary);
        border-bottom: 1px solid rgba(0,51,127,0.05);
        vertical-align: middle;
    }
    html:not(.light-mode) .nv-table tbody td { border-bottom-color: rgba(255,255,255,0.04); }
    .nv-table tbody tr.nv-main-row { transition: background 150ms; }
    .nv-table tbody tr.nv-main-row:hover { background: rgba(0,51,127,0.02); }
    html:not(.light-mode) .nv-table tbody tr.nv-main-row:hover { background: rgba(255,255,255,0.02); }
    .nv-table tbody tr.nv-main-row.nv-expanded { background: rgba(0,51,127,0.03); }
    html:not(.light-mode) .nv-table tbody tr.nv-main-row.nv-expanded { background: rgba(255,255,255,0.03); }
    .nv-table tbody tr.nv-main-row.nv-expanded td { border-bottom-color: transparent; }
    .nv-table tbody tr.nv-detail-row td { padding: 0; }

    /* --------- USER CELL --------- */
    .nv-user-cell { display: flex; align-items: center; gap: 0.75rem; }
    .nv-avatar {
        width: 40px; height: 40px;
        border-radius: 50%;
        flex-shrink: 0;
        background: #f1f5f9;
        border: 1px solid rgba(0,51,127,0.08);
        overflow: hidden;
    }
    .nv-avatar img { width: 100%; height: 100%; display: block; }
    .nv-user-name { font-weight: 600; line-height: 1.2; }
    .nv-user-email { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.1rem; font-family: var(--font-mono, 'IBM Plex Mono', monospace); }

    /* --------- BADGES --------- */
    .nv-role {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .nv-role--admin       { background: rgba(217,119,6,0.12);  color: var(--gold-dark, #b45309); }
    .nv-role--contributor { background: rgba(167,139,250,0.14);color: #7c3aed; }
    .nv-role--user        { background: rgba(0,51,127,0.08);   color: var(--thw-blue); }

    .nv-verif-ok { font-size: 1.125rem; color: var(--success); }
    .nv-verif-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.6rem;
        border-radius: 999px;
        background: rgba(239,68,68,0.08);
        border: 1px solid rgba(239,68,68,0.25);
        color: var(--error);
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 150ms;
        white-space: nowrap;
    }
    .nv-verif-btn:hover { background: var(--success); border-color: var(--success); color: #fff; }

    /* --------- ACTION BUTTONS --------- */
    .nv-actions { display: flex; gap: 0.375rem; justify-content: flex-end; }
    .nv-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 0.75rem;
        border-radius: 0.5rem;
        background: #f8fafc;
        border: 1px solid rgba(0,51,127,0.08);
        color: var(--text-secondary);
        font-size: 0.8125rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 150ms;
        white-space: nowrap;
        font-family: var(--font-sans);
    }
    html:not(.light-mode) .nv-btn { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.10); }
    .nv-btn:hover { background: rgba(0,51,127,0.04); border-color: rgba(0,51,127,0.18); color: var(--thw-blue); }
    html:not(.light-mode) .nv-btn:hover { background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.20); }
    .nv-btn--primary { background: var(--thw-blue); color: #fff; border-color: var(--thw-blue); }
    .nv-btn--primary:hover { background: var(--thw-blue-light, #0044a8); color: #fff; }
    .nv-btn--danger { background: #fff; color: var(--error); border-color: rgba(239,68,68,0.20); }
    html:not(.light-mode) .nv-btn--danger { background: transparent; }
    .nv-btn--danger:hover { background: rgba(239,68,68,0.06); border-color: var(--error); }
    .nv-btn--icon { padding: 0.45rem; width: 34px; justify-content: center; gap: 0; }

    /* --------- DETAIL PANEL --------- */
    .nv-detail-panel {
        padding: 1.5rem;
        background: #f8fafc;
        border-bottom: 1px solid rgba(0,51,127,0.06);
        animation: nv-slide-down 200ms ease-out;
    }
    html:not(.light-mode) .nv-detail-panel {
        background: rgba(255,255,255,0.02);
        border-bottom-color: rgba(255,255,255,0.05);
    }
    @keyframes nv-slide-down {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .nv-panel-label {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
    }
    .nv-form-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    @media (max-width: 900px) { .nv-form-grid { grid-template-columns: repeat(2, 1fr); } }
    .nv-field { display: flex; flex-direction: column; gap: 0.35rem; }
    .nv-field label { font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); }
    .nv-field input, .nv-field select {
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid rgba(0,51,127,0.12);
        background: #fff;
        font-size: 0.875rem;
        color: var(--text-primary);
        font-family: var(--font-sans);
        transition: all 150ms;
    }
    html:not(.light-mode) .nv-field input,
    html:not(.light-mode) .nv-field select {
        background: rgba(255,255,255,0.06);
        border-color: rgba(255,255,255,0.12);
        color: var(--text-primary);
    }
    .nv-field input:focus, .nv-field select:focus {
        outline: none; border-color: var(--thw-blue); box-shadow: 0 0 0 3px rgba(0,51,127,0.12);
    }
    .nv-info-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem 1.5rem;
        padding: 1rem 1.25rem;
        background: #fff;
        border-radius: 0.5rem;
        border: 1px solid rgba(0,51,127,0.06);
        margin-bottom: 1rem;
    }
    html:not(.light-mode) .nv-info-row {
        background: rgba(255,255,255,0.04);
        border-color: rgba(255,255,255,0.06);
    }
    @media (max-width: 700px) { .nv-info-row { grid-template-columns: 1fr; } }
    .nv-info-label {
        font-size: 0.6875rem; font-weight: 600; text-transform: uppercase;
        letter-spacing: 0.06em; color: var(--text-muted); margin-bottom: 0.25rem;
    }
    .nv-info-value { font-size: 0.875rem; font-weight: 500; color: var(--text-primary); }
    .nv-info-value--ok { color: var(--success); }
    .nv-info-value--muted { color: var(--text-muted); }
    .nv-panel-footer { display: flex; justify-content: flex-end; gap: 0.5rem; }

    /* --------- PAGINATION --------- */
    .nv-pager {
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        background: #f8fafc;
        border-top: 1px solid rgba(0,51,127,0.06);
    }
    html:not(.light-mode) .nv-pager { background: rgba(255,255,255,0.02); border-top-color: rgba(255,255,255,0.06); }
    .nv-pager__info { font-size: 0.8125rem; color: var(--text-muted); }
    .nv-pager__nav { display: flex; gap: 0.25rem; }
    .nv-pager__btn {
        width: 32px; height: 32px;
        display: grid; place-items: center;
        border-radius: 0.4rem;
        background: #fff;
        border: 1px solid rgba(0,51,127,0.10);
        color: var(--text-secondary);
        font-size: 0.8125rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 150ms;
    }
    html:not(.light-mode) .nv-pager__btn { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.10); }
    .nv-pager__btn:hover { background: rgba(0,51,127,0.04); border-color: rgba(0,51,127,0.18); }
    .nv-pager__btn.is-active { background: var(--thw-blue); color: #fff; border-color: var(--thw-blue); }
    .nv-pager__btn:disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }

    /* --------- EMPTY STATE --------- */
    .nv-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-muted);
    }
    .nv-empty__icon { font-size: 2rem; color: rgba(0,51,127,0.18); margin-bottom: 0.5rem; }

    /* --------- PROGRESS MODAL --------- */
    .nv-modal-overlay {
        position: fixed; inset: 0;
        background: rgba(15,23,42,0.55);
        backdrop-filter: blur(4px);
        display: grid; place-items: center;
        z-index: 1000;
        padding: 1rem;
        animation: nv-fade-in 150ms ease-out;
    }
    @keyframes nv-fade-in { from { opacity: 0; } to { opacity: 1; } }

    .nv-modal {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.30);
        width: 100%;
        max-width: 920px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: nv-modal-rise 200ms ease-out;
    }
    html:not(.light-mode) .nv-modal {
        background: var(--bg-card, #1c1c1f);
        box-shadow: 0 20px 60px rgba(0,0,0,0.55);
    }
    @keyframes nv-modal-rise {
        from { opacity: 0; transform: translateY(12px) scale(0.98); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .nv-modal__head {
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        border-bottom: 1px solid rgba(0,51,127,0.08);
    }
    html:not(.light-mode) .nv-modal__head { border-bottom-color: rgba(255,255,255,0.08); }
    .nv-modal__user { display: flex; align-items: center; gap: 0.875rem; min-width: 0; }
    .nv-modal__avatar {
        width: 48px; height: 48px; border-radius: 50%;
        background: #f1f5f9; overflow: hidden; flex-shrink: 0;
        border: 1px solid rgba(0,51,127,0.10);
    }
    .nv-modal__avatar img { width: 100%; height: 100%; display: block; }
    .nv-modal__eyebrow {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.6875rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.1em;
        color: var(--text-muted);
    }
    .nv-modal__title {
        font-size: 1.25rem; font-weight: 700;
        color: var(--text-primary);
        line-height: 1.15;
        letter-spacing: -0.01em;
    }
    .nv-modal__sub {
        font-size: 0.8125rem; color: var(--text-muted);
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        margin-top: 0.15rem;
    }
    .nv-modal__close {
        width: 36px; height: 36px;
        display: grid; place-items: center;
        border-radius: 0.5rem;
        border: 1px solid rgba(0,51,127,0.10);
        background: #f8fafc;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 150ms;
    }
    html:not(.light-mode) .nv-modal__close {
        background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.10);
    }
    .nv-modal__close:hover { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.30); color: var(--error); }

    .nv-modal__tabs {
        display: flex;
        gap: 0.25rem;
        padding: 0.5rem 1.5rem 0;
        background: #f8fafc;
        border-bottom: 1px solid rgba(0,51,127,0.08);
    }
    html:not(.light-mode) .nv-modal__tabs { background: rgba(255,255,255,0.02); border-bottom-color: rgba(255,255,255,0.08); }
    .nv-modal__tab {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.6rem 1rem 0.7rem;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-secondary);
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        transition: all 150ms;
        font-family: var(--font-sans);
    }
    .nv-modal__tab:hover { color: var(--text-primary); }
    .nv-modal__tab--active {
        color: var(--thw-blue);
        border-bottom-color: var(--thw-blue);
    }
    .nv-modal__tab-count {
        display: inline-grid; place-items: center;
        min-width: 22px; height: 18px; padding: 0 0.4rem;
        border-radius: 999px;
        background: rgba(0,51,127,0.10);
        color: var(--thw-blue);
        font-size: 0.6875rem;
        font-weight: 700;
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
    }
    .nv-modal__tab--active .nv-modal__tab-count { background: var(--thw-blue); color: #fff; }

    .nv-modal__body { padding: 1.5rem; overflow-y: auto; flex: 1; }

    .nv-modal-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 700px) { .nv-modal-stats { grid-template-columns: repeat(2, 1fr); } }
    .nv-modal-stat {
        padding: 0.875rem 1rem;
        background: #f8fafc;
        border: 1px solid rgba(0,51,127,0.08);
        border-radius: 0.625rem;
        text-align: center;
    }
    html:not(.light-mode) .nv-modal-stat { background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.07); }
    .nv-modal-stat__value { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.015em; line-height: 1.1; }
    .nv-modal-stat__value--blue { color: var(--thw-blue); }
    .nv-modal-stat__value--ok   { color: var(--success); }
    .nv-modal-stat__value--warn { color: var(--warning, #f59e0b); }
    .nv-modal-stat__value--err  { color: var(--error); }
    .nv-modal-stat__label {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        margin-top: 0.2rem;
    }

    .nv-prog-list { display: flex; flex-direction: column; gap: 0.5rem; }
    .nv-prog-row {
        background: #fff;
        border: 1px solid rgba(0,51,127,0.08);
        border-radius: 0.625rem;
        padding: 0.875rem 1rem;
        transition: border-color 150ms;
    }
    html:not(.light-mode) .nv-prog-row { background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.07); }
    .nv-prog-row:hover { border-color: rgba(0,51,127,0.18); }

    .nv-prog-row__head {
        display: grid;
        grid-template-columns: 48px 1fr auto;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.6rem;
    }
    .nv-prog-row__num {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-weight: 700;
        font-size: 0.875rem;
        height: 30px;
        display: grid; place-items: center;
        border-radius: 0.4rem;
        background: rgba(0,51,127,0.08);
        color: var(--thw-blue);
        padding: 0 0.5rem;
    }
    .nv-prog-row__num--done { background: rgba(34,197,94,0.14); color: var(--success); }
    .nv-prog-row__num--mid  { background: rgba(245,158,11,0.14); color: var(--warning, #f59e0b); }
    .nv-prog-row__num--low  { background: rgba(239,68,68,0.12); color: var(--error); }
    .nv-prog-row__num--none { background: rgba(100,116,139,0.12); color: #64748b; }
    .nv-prog-row__title { font-weight: 600; color: var(--text-primary); }
    .nv-prog-row__pct {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-weight: 700; font-size: 0.875rem;
        color: var(--text-secondary);
    }
    .nv-prog-row__pct--done { color: var(--success); }
    .nv-prog-row__pct--mid  { color: var(--warning, #f59e0b); }
    .nv-prog-row__pct--low  { color: var(--error); }
    .nv-prog-row__pct--none { color: var(--text-muted); }

    .nv-prog-row__bar {
        position: relative;
        height: 6px;
        background: rgba(0,51,127,0.06);
        border-radius: 999px;
        overflow: hidden;
    }
    html:not(.light-mode) .nv-prog-row__bar { background: rgba(255,255,255,0.06); }
    .nv-prog-row__fill {
        position: absolute; left: 0; top: 0; bottom: 0;
        border-radius: 999px;
        transition: width 200ms;
    }
    .nv-prog-row__fill--done { background: var(--success); }
    .nv-prog-row__fill--mid  { background: var(--warning, #f59e0b); }
    .nv-prog-row__fill--low  { background: var(--error); }
    .nv-prog-row__fill--none { background: rgba(100,116,139,0.5); }

    .nv-prog-row__meta {
        margin-top: 0.5rem;
        display: flex;
        gap: 0.875rem;
        font-size: 0.75rem;
        color: var(--text-muted);
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        flex-wrap: wrap;
    }
    .nv-prog-row__meta-item { display: inline-flex; align-items: center; gap: 0.3rem; }
    .nv-prog-row__meta-item--ok   { color: var(--success); }
    .nv-prog-row__meta-item--warn { color: var(--warning, #f59e0b); }
    .nv-prog-row__meta-item--err  { color: var(--error); }
    .nv-prog-row__meta-item .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    .nv-modal__footer {
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        border-top: 1px solid rgba(0,51,127,0.08);
        background: #f8fafc;
        flex-wrap: wrap;
    }
    html:not(.light-mode) .nv-modal__footer { background: rgba(255,255,255,0.02); border-top-color: rgba(255,255,255,0.08); }
    .nv-modal__footer-hint {
        font-size: 0.75rem;
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .nv-modal__footer-actions { display: flex; gap: 0.5rem; }

    .nv-modal-loading {
        padding: 3rem 1rem;
        text-align: center;
        color: var(--text-muted);
        font-size: 0.875rem;
    }
    .nv-modal-loading i { font-size: 1.5rem; display: block; margin-bottom: 0.5rem; opacity: 0.5; }

    /* --------- BULK BUTTONS PRO MODUL --------- */
    .nv-prog-row__bulk {
        display: inline-flex;
        gap: 0.25rem;
        margin-left: auto;
        flex-wrap: wrap;
    }
    .nv-bulk-btn {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.3rem 0.55rem;
        border-radius: 0.4rem;
        background: #fff;
        border: 1px solid rgba(0,51,127,0.12);
        color: var(--text-secondary);
        font-size: 0.6875rem;
        font-weight: 600;
        font-family: var(--font-sans);
        cursor: pointer;
        transition: all 150ms;
        white-space: nowrap;
    }
    html:not(.light-mode) .nv-bulk-btn { background: rgba(255,255,255,0.04); border-color: rgba(255,255,255,0.10); }
    .nv-bulk-btn:hover:not(:disabled) { background: rgba(0,51,127,0.04); border-color: rgba(0,51,127,0.20); color: var(--text-primary); }
    .nv-bulk-btn:disabled { opacity: 0.45; cursor: not-allowed; }
    .nv-bulk-btn--ok:hover:not(:disabled)    { background: rgba(34,197,94,0.08);  border-color: rgba(34,197,94,0.30);  color: var(--success); }
    .nv-bulk-btn--sr:hover:not(:disabled)    { background: rgba(239,68,68,0.06);  border-color: rgba(239,68,68,0.25);  color: var(--error); }
    .nv-bulk-btn--reset:hover:not(:disabled) { background: rgba(245,158,11,0.06); border-color: rgba(245,158,11,0.25); color: var(--warning, #f59e0b); }

    .nv-prog-row--open { border-color: rgba(0,51,127,0.18); }

    /* --------- QUESTION LIST IM AUFGEKLAPPTEN MODUL --------- */
    .nv-q-expand {
        margin-top: 0.75rem;
        padding-top: 0.875rem;
        border-top: 1px dashed rgba(0,51,127,0.10);
    }
    html:not(.light-mode) .nv-q-expand { border-top-color: rgba(255,255,255,0.08); }

    .nv-q-legend {
        display: flex; gap: 1rem; flex-wrap: wrap;
        padding: 0.5rem 0.75rem;
        background: rgba(0,51,127,0.03);
        border-radius: 0.4rem;
        font-size: 0.6875rem;
        color: var(--text-secondary);
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.75rem;
    }
    html:not(.light-mode) .nv-q-legend { background: rgba(255,255,255,0.03); }
    .nv-q-legend__item { display: inline-flex; align-items: center; gap: 0.35rem; }
    .nv-q-legend__item .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }

    .nv-q-list { display: flex; flex-direction: column; gap: 0.3rem; max-height: 360px; overflow-y: auto; padding-right: 0.25rem; }

    .nv-q-item {
        display: grid;
        grid-template-columns: 48px 180px 1fr auto;
        align-items: center;
        gap: 0.6rem;
        padding: 0.5rem 0.75rem;
        background: #fff;
        border: 1px solid rgba(0,51,127,0.06);
        border-left-width: 3px;
        border-radius: 0.4rem;
        font-size: 0.8125rem;
        transition: all 120ms;
    }
    html:not(.light-mode) .nv-q-item { background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.06); }
    .nv-q-item:hover { border-color: rgba(0,51,127,0.18); }
    .nv-q-item--done    { border-left-color: var(--success); }
    .nv-q-item--partial { border-left-color: var(--warning, #f59e0b); }
    .nv-q-item--sr      { border-left-color: var(--error); }
    .nv-q-item--open    { border-left-color: #94a3b8; }

    .nv-q-item__num {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 600;
    }
    .nv-q-item__status { display: inline-flex; align-items: center; gap: 0.35rem; min-width: 0; }
    .nv-q-item__label {
        font-size: 0.6875rem;
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-secondary);
        white-space: nowrap;
    }
    .nv-q-item__id {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .nv-q-item__actions { display: inline-flex; gap: 0.2rem; }

    .nv-q-streak { display: inline-flex; gap: 0.15rem; }
    .nv-q-streak__dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: rgba(100,116,139,0.30);
        display: inline-block;
    }
    .nv-q-streak__dot--on { background: var(--success); }

    .nv-q-act {
        width: 28px; height: 28px;
        display: grid; place-items: center;
        border-radius: 0.35rem;
        background: #f8fafc;
        border: 1px solid rgba(0,51,127,0.08);
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 120ms;
        font-size: 0.75rem;
    }
    html:not(.light-mode) .nv-q-act { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.10); }
    .nv-q-act:hover:not(:disabled) { background: rgba(0,51,127,0.06); border-color: rgba(0,51,127,0.20); color: var(--text-primary); }
    .nv-q-act:disabled { opacity: 0.35; cursor: not-allowed; }
    .nv-q-act--ok.is-active    { background: var(--success); border-color: var(--success); color: #fff; }
    .nv-q-act--sr.is-active    { background: var(--error);   border-color: var(--error);   color: #fff; }
    .nv-q-act--plus:hover:not(:disabled)  { background: rgba(0,51,127,0.06);  border-color: rgba(0,51,127,0.25);  color: var(--thw-blue); }
    .nv-q-act--ok:hover:not(:disabled)    { background: rgba(34,197,94,0.10); border-color: rgba(34,197,94,0.35); color: var(--success); }
    .nv-q-act--sr:hover:not(:disabled)    { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.30); color: var(--error); }
    .nv-q-act--reset:hover:not(:disabled) { background: rgba(245,158,11,0.08); border-color: rgba(245,158,11,0.30); color: var(--warning, #f59e0b); }

    @media (max-width: 720px) {
        .nv-q-item { grid-template-columns: 40px 1fr auto; }
        .nv-q-item__id { display: none; }
        .nv-prog-row__bulk .nv-bulk-btn span { display: none; }
    }
</style>
@endpush

@php
$usersJson = $users->map(fn($u) => [
    'id'         => $u->id,
    'name'       => $u->name,
    'email'      => $u->email,
    'role'       => $u->useroll ?? 'user',
    'verified'   => !is_null($u->email_verified_at),
    'verifiedAt' => $u->email_verified_at?->format('d.m.Y H:i'),
    'newsletter' => (bool) ($u->email_consent ?? false),
    'xp'         => (int) ($u->points ?? 0),
    'level'      => (int) ($u->level ?? 1),
    'avatarUrl'  => $u->avatar_url,
])->values();
@endphp

@push('alpine-components')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('usersTable', () => ({
        search: '',
        roleFilter: 'all',
        verifFilter: 'all',
        expanded: null,
        page: 1,
        perPage: 8,
        users: @json($usersJson),

        // Progress modal state
        modalUser: null,
        modalLoading: false,
        modalData: null,
        modalTab: 'grundausbildung',

        get filtered() {
            const q = this.search.trim().toLowerCase();
            return this.users.filter(u => {
                if (q && !u.name.toLowerCase().includes(q) && !u.email.toLowerCase().includes(q)) return false;
                if (this.roleFilter !== 'all' && u.role !== this.roleFilter) return false;
                if (this.verifFilter === 'verified'   && !u.verified) return false;
                if (this.verifFilter === 'unverified' &&  u.verified) return false;
                return true;
            });
        },
        get totalPages() {
            return Math.max(1, Math.ceil(this.filtered.length / this.perPage));
        },
        get pageItems() {
            const s = (this.page - 1) * this.perPage;
            return this.filtered.slice(s, s + this.perPage);
        },
        toggleExpand(id) { this.expanded = (this.expanded === id) ? null : id; },
        roleLabel(r) { return r === 'admin' ? 'Admin' : (r === 'contributor' ? 'Contributor' : 'Benutzer'); },

        modalOpenModule: null,    // key like "grundausbildung-1"
        modalModuleQuestions: {}, // cache: { 'grundausbildung-1': [...] }
        modalThreshold: 3,
        modalDirty: false,
        modalBusy: false,

        async openProgress(u) {
            this.modalUser           = u;
            this.modalTab            = 'grundausbildung';
            this.modalData           = null;
            this.modalLoading        = true;
            this.modalOpenModule     = null;
            this.modalModuleQuestions = {};
            this.modalDirty          = false;
            document.body.style.overflow = 'hidden';
            try {
                const res = await fetch(`{{ url('admin/users') }}/${u.id}/progress-json`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                this.modalData = await res.json();
                this.modalThreshold = this.modalData.threshold ?? 3;
            } catch (e) {
                console.error('Fortschritts-Modal: Laden fehlgeschlagen', e);
                this.modalData = { error: true };
            } finally {
                this.modalLoading = false;
            }
        },
        closeProgress() {
            this.modalUser            = null;
            this.modalData            = null;
            this.modalLoading         = false;
            this.modalOpenModule      = null;
            this.modalModuleQuestions = {};
            this.modalDirty           = false;
            this.modalBusy            = false;
            document.body.style.overflow = '';
        },

        moduleKey(row) {
            return this.modalTab + '-' + row.id;
        },
        async toggleModule(row) {
            const key = this.moduleKey(row);
            if (this.modalOpenModule === key) {
                this.modalOpenModule = null;
                return;
            }
            this.modalOpenModule = key;
            if (!this.modalModuleQuestions[key]) {
                try {
                    const url = `{{ url('admin/users') }}/${this.modalUser.id}/progress-module-json?type=${encodeURIComponent(this.modalTab)}&module_id=${encodeURIComponent(row.id)}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const data = await res.json();
                    this.modalModuleQuestions[key] = data.questions || [];
                    this.modalThreshold = data.threshold ?? this.modalThreshold;
                } catch (e) {
                    console.error('Modul-Fragen Laden fehlgeschlagen', e);
                    this.modalModuleQuestions[key] = [];
                }
            }
        },
        async questionAction(row, qi, action) {
            if (this.modalBusy) return;
            const key = this.moduleKey(row);
            const q = this.modalModuleQuestions[key]?.[qi];
            if (!q) return;
            this.modalBusy = true;
            try {
                const res = await fetch(`{{ url('admin/users') }}/${this.modalUser.id}/progress-question`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({ type: this.modalTab, question_id: q.id, action }),
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                Object.assign(this.modalModuleQuestions[key][qi], {
                    streak:     data.streak,
                    sr:         data.sr,
                    isMastered: data.isMastered,
                });
                this.recalcModuleCounts(row);
                this.modalDirty = true;
            } catch (e) {
                console.error('Frage-Aktion fehlgeschlagen', e);
                alert('Aktion fehlgeschlagen. Bitte erneut versuchen.');
            } finally {
                this.modalBusy = false;
            }
        },
        async moduleBulkAction(row, action) {
            if (this.modalBusy) return;
            const verb = action === 'mastered' ? 'als gemeistert markieren' : (action === 'sr' ? 'auf SR setzen' : 'zurücksetzen');
            if (!confirm(`Alle ${row.total} Fragen von "${row.title}" ${verb}?`)) return;
            this.modalBusy = true;
            try {
                const res = await fetch(`{{ url('admin/users') }}/${this.modalUser.id}/progress-module-bulk`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({ type: this.modalTab, module_id: row.id, action }),
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                Object.assign(row, data.counts);
                // Invalidate cached questions for this module so next expand reloads
                const key = this.moduleKey(row);
                delete this.modalModuleQuestions[key];
                if (this.modalOpenModule === key) {
                    this.modalOpenModule = null;
                }
                this.modalDirty = true;
            } catch (e) {
                console.error('Bulk-Aktion fehlgeschlagen', e);
                alert('Aktion fehlgeschlagen. Bitte erneut versuchen.');
            } finally {
                this.modalBusy = false;
            }
        },
        recalcModuleCounts(row) {
            const key = this.moduleKey(row);
            const qs = this.modalModuleQuestions[key] || [];
            const t = this.modalThreshold;
            let mastered = 0, partial = 0, sr = 0, open = 0;
            for (const q of qs) {
                if (q.sr) sr++;
                else if (q.streak >= t) mastered++;
                else if (q.streak > 0) partial++;
                else open++;
            }
            row.mastered = mastered;
            row.partial  = partial;
            row.sr       = sr;
            row.open     = open;
        },
        questionStatus(q) {
            if (q.sr) return 'sr';
            if (q.streak >= this.modalThreshold) return 'done';
            if (q.streak > 0) return 'partial';
            return 'open';
        },
        questionStatusLabel(q) {
            if (q.sr) return 'SR · Wiederholung';
            if (q.streak >= this.modalThreshold) return 'Gemeistert · ' + this.modalThreshold + '/' + this.modalThreshold;
            if (q.streak > 0) return q.streak + '/' + this.modalThreshold + ' richtig';
            return 'Neu · 0/' + this.modalThreshold;
        },
        get modalRows() {
            if (!this.modalData || this.modalData.error) return [];
            return this.modalData[this.modalTab] || [];
        },
        get modalActiveRows() {
            return this.modalTab === 'lehrgaenge'
                ? this.modalRows.filter(r => r.enrolled)
                : this.modalRows;
        },
        get modalTotals() {
            const rows = this.modalActiveRows;
            const acc = { total: 0, mastered: 0, partial: 0, sr: 0 };
            for (const r of rows) {
                acc.total    += r.total;
                acc.mastered += r.mastered;
                acc.partial  += r.partial;
                acc.sr       += r.sr;
            }
            acc.pct = acc.total > 0 ? Math.round((acc.mastered / acc.total) * 100) : 0;
            return acc;
        },
        pctCls(pct) {
            if (pct >= 80) return 'done';
            if (pct >= 50) return 'mid';
            if (pct >  0)  return 'low';
            return 'none';
        },
        rowPct(r) {
            return r.total > 0 ? Math.round((r.mastered / r.total) * 100) : 0;
        },
        tabCount(tab) {
            if (!this.modalData) return 0;
            const rows = this.modalData[tab] || [];
            return tab === 'lehrgaenge' ? rows.filter(r => r.enrolled).length : rows.length;
        },
    }));
});
</script>
@endpush

@section('content')

<div class="dashboard-container">
<div class="admin-container">

    {{-- Page title --}}
    <header class="dashboard-header" style="margin-bottom: 1.5rem;">
        <div style="font-family: var(--font-mono, 'IBM Plex Mono', monospace); font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); margin-bottom: 0.25rem;">Admin · Nutzer</div>
        <h1 class="page-title">Nutzerverwaltung</h1>
        <p class="page-subtitle">Verwalte alle Benutzer, Rollen und Lernfortschritte</p>
    </header>

    {{-- Header Stats --}}
    <div class="nv-header-stats">
        <div class="nv-hstat">
            <div class="nv-hstat__icon nv-hstat__icon--blue"><i class="bi bi-people-fill"></i></div>
            <div class="nv-hstat__value">{{ $users->count() }}</div>
            <div class="nv-hstat__label">Gesamt</div>
        </div>
        <div class="nv-hstat">
            <div class="nv-hstat__icon nv-hstat__icon--gold"><i class="bi bi-shield-fill"></i></div>
            <div class="nv-hstat__value">{{ $users->where('useroll', 'admin')->count() }}</div>
            <div class="nv-hstat__label">Admins</div>
        </div>
        <div class="nv-hstat">
            <div class="nv-hstat__icon nv-hstat__icon--purple"><i class="bi bi-pencil-square"></i></div>
            <div class="nv-hstat__value">{{ $users->where('useroll', 'contributor')->count() }}</div>
            <div class="nv-hstat__label">Contributors</div>
        </div>
        <div class="nv-hstat">
            <div class="nv-hstat__icon nv-hstat__icon--slate"><i class="bi bi-person-fill"></i></div>
            <div class="nv-hstat__value">{{ $users->where('useroll', 'user')->count() }}</div>
            <div class="nv-hstat__label">Benutzer</div>
        </div>
        <div class="nv-hstat">
            <div class="nv-hstat__icon nv-hstat__icon--green"><i class="bi bi-envelope-check-fill"></i></div>
            <div class="nv-hstat__value">{{ $users->whereNotNull('email_verified_at')->count() }}</div>
            <div class="nv-hstat__label">Verifiziert</div>
        </div>
        <div class="nv-hstat">
            <div class="nv-hstat__icon nv-hstat__icon--red"><i class="bi bi-envelope-x-fill"></i></div>
            <div class="nv-hstat__value">{{ $users->whereNull('email_verified_at')->count() }}</div>
            <div class="nv-hstat__label">Nicht verifiziert</div>
        </div>
        <div class="nv-hstat">
            <div class="nv-hstat__icon nv-hstat__icon--gold"><i class="bi bi-megaphone-fill"></i></div>
            <div class="nv-hstat__value">{{ $users->where('email_consent', true)->count() }}</div>
            <div class="nv-hstat__label">Newsletter</div>
        </div>
    </div>

    {{-- DSGVO Banner --}}
    <div class="dsgvo-notice">
        <i class="bi bi-shield-lock-fill" style="color: var(--thw-blue); flex-shrink: 0;"></i>
        Änderungen werden protokolliert und sind für den Nutzer einsehbar.
    </div>

    {{-- Action Bar --}}
    <div class="nv-action-bar">
        <a href="{{ route('admin.newsletter.create') }}" class="btn-primary">
            <i class="bi bi-megaphone-fill"></i> Newsletter senden
        </a>
        <a href="{{ route('admin.dashboard') }}" class="btn-ghost">
            <i class="bi bi-arrow-left"></i> Zum Dashboard
        </a>
        <a href="{{ route('admin.users.index') }}?export=csv" class="btn-ghost">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>

    @if(session('success'))
        <div class="alert-glass success" style="margin-bottom: 1rem;">
            <i class="bi bi-check-circle" style="font-size: 1.25rem; color: var(--success);"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert-glass error" style="margin-bottom: 1rem;">
            <i class="bi bi-x-circle" style="font-size: 1.25rem; color: var(--error);"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Main Table with Alpine.js --}}
    <div class="nv-table-card" x-data="usersTable">

        {{-- Table head / filters --}}
        <div class="nv-table-head">
            <div class="nv-title-row">
                <span class="section-label">Alle Benutzer</span>
                <span class="nv-count">· <span x-text="filtered.length"></span> von {{ $users->count() }}</span>
            </div>
            <div class="nv-filters">
                <div class="nv-search">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Name oder E-Mail suchen…"
                           x-model="search" @input="page = 1" />
                </div>
                <select class="nv-filter-select" x-model="roleFilter" @change="page = 1">
                    <option value="all">Alle Rollen</option>
                    <option value="admin">Admin</option>
                    <option value="contributor">Contributor</option>
                    <option value="user">Benutzer</option>
                </select>
                <select class="nv-filter-select" x-model="verifFilter" @change="page = 1">
                    <option value="all">Alle Status</option>
                    <option value="verified">Verifiziert</option>
                    <option value="unverified">Nicht verifiziert</option>
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div style="overflow-x: auto;">
            <table class="nv-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Rolle</th>
                        <th style="text-align: center;">Verif.</th>
                        <th style="text-align: right;">Aktionen</th>
                    </tr>
                </thead>

                {{-- Empty state as its own tbody --}}
                <tbody x-show="pageItems.length === 0">
                    <tr>
                        <td colspan="4">
                            <div class="nv-empty">
                                <div class="nv-empty__icon"><i class="bi bi-search"></i></div>
                                <div style="font-weight: 600; color: var(--text-secondary);">Keine Treffer</div>
                                <div style="font-size: 0.8125rem; margin-top: 0.25rem;">Passe deine Such- oder Filterkriterien an.</div>
                            </div>
                        </td>
                    </tr>
                </tbody>

                {{--
                    One <tbody> per user — keeps main row and detail row paired so the
                    expandable panel slides out directly beneath the corresponding row.
                    Browsers permit multiple <tbody> elements within one <table>.
                --}}
                <template x-for="u in pageItems" :key="u.id">
                    <tbody class="nv-row-group">
                        <tr class="nv-main-row" :class="expanded === u.id ? 'nv-expanded' : ''">
                            <td>
                                <div class="nv-user-cell">
                                    <div class="nv-avatar">
                                        <img :src="u.avatarUrl" :alt="u.name" loading="lazy" />
                                    </div>
                                    <div>
                                        <div class="nv-user-name" x-text="u.name"></div>
                                        <div class="nv-user-email" x-text="u.email"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span :class="`nv-role nv-role--${u.role}`" x-text="roleLabel(u.role)"></span>
                            </td>
                            <td style="text-align: center;">
                                <template x-if="u.verified">
                                    <i class="bi bi-check-circle-fill nv-verif-ok" title="Verifiziert"></i>
                                </template>
                                <template x-if="!u.verified">
                                    <form :action="`{{ url('admin/users') }}/${u.id}/verify`" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="nv-verif-btn" title="Jetzt verifizieren">
                                            <i class="bi bi-x-circle-fill"></i> Verifizieren
                                        </button>
                                    </form>
                                </template>
                            </td>
                            <td>
                                <div class="nv-actions">
                                    <button class="nv-btn" @click="toggleExpand(u.id)">
                                        <i :class="`bi ${expanded === u.id ? 'bi-chevron-up' : 'bi-chevron-down'}`"></i>
                                        Details
                                    </button>
                                    <button type="button" class="nv-btn nv-btn--primary" @click="openProgress(u)">
                                        <i class="bi bi-graph-up"></i> Fortschritt
                                    </button>
                                    <a :href="`{{ url('admin/users') }}/${u.id}/xp-history`" class="nv-btn nv-btn--icon" title="XP-Verlauf">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
                                    <form :action="`{{ url('admin/users') }}/${u.id}`" method="POST" style="display:inline;"
                                          @submit.prevent="
                                              const reason = prompt('DSGVO: Bitte gib den Grund für die Löschung von ' + u.name + ' an.\n\nAlle personenbezogenen Daten werden unwiderruflich gelöscht.');
                                              if (reason === null) return;
                                              if (reason.trim().length < 3) { alert('Bitte gib einen Grund mit mindestens 3 Zeichen an.'); return; }
                                              $el.querySelector('input[name=reason]').value = reason;
                                              $el.submit();
                                          ">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="reason" value="">
                                        <button type="submit" class="nv-btn nv-btn--danger nv-btn--icon" title="Löschen">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Inline detail row, directly beneath the corresponding user row --}}
                        <tr class="nv-detail-row" x-show="expanded === u.id" style="display: none;">
                            <td colspan="4">
                                <div class="nv-detail-panel">
                                    <div class="nv-panel-label">Benutzer bearbeiten · #<span x-text="u.id"></span></div>

                                    <form :action="`{{ url('admin/users') }}/${u.id}`" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="nv-form-grid">
                                            <div class="nv-field">
                                                <label>Name</label>
                                                <input type="text" name="name" :value="u.name" required />
                                            </div>
                                            <div class="nv-field">
                                                <label>E-Mail</label>
                                                <input type="email" name="email" :value="u.email" required />
                                            </div>
                                            <div class="nv-field">
                                                <label>Rolle</label>
                                                <select name="useroll">
                                                    <option value="user"        :selected="u.role === 'user'">Benutzer</option>
                                                    <option value="contributor" :selected="u.role === 'contributor'">Contributor</option>
                                                    <option value="admin"       :selected="u.role === 'admin'">Administrator</option>
                                                </select>
                                            </div>
                                            <div class="nv-field">
                                                <label>Punkte (XP)</label>
                                                <input type="number" name="points" :value="u.xp" min="0" />
                                            </div>
                                        </div>

                                        <div class="nv-info-row">
                                            <div>
                                                <div class="nv-info-label">Level &amp; XP</div>
                                                <div class="nv-info-value">
                                                    <strong>Lvl <span x-text="u.level"></span></strong>
                                                    <span style="color:var(--text-muted);font-weight:500;margin-left:0.5rem;">
                                                        · <span x-text="u.xp.toLocaleString('de-DE')"></span> XP
                                                    </span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="nv-info-label">E-Mail Status</div>
                                                <template x-if="u.verified">
                                                    <div class="nv-info-value nv-info-value--ok">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                        Bestätigt am <span x-text="u.verifiedAt || '—'"></span>
                                                    </div>
                                                </template>
                                                <template x-if="!u.verified">
                                                    <div class="nv-info-value" style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                                                        <span style="color:var(--error);"><i class="bi bi-x-circle-fill"></i> Nicht bestätigt</span>
                                                        <form :action="`{{ url('admin/users') }}/${u.id}/verify`" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="nv-btn" style="padding:0.3rem 0.65rem;font-size:0.75rem;color:var(--success);border-color:rgba(34,197,94,0.30);">
                                                                <i class="bi bi-check-lg"></i> Jetzt verifizieren
                                                            </button>
                                                        </form>
                                                    </div>
                                                </template>
                                            </div>
                                            <div>
                                                <div class="nv-info-label">Newsletter</div>
                                                <template x-if="u.newsletter">
                                                    <div class="nv-info-value nv-info-value--ok">
                                                        <i class="bi bi-envelope-check-fill"></i> Aktiv seit Registrierung
                                                    </div>
                                                </template>
                                                <template x-if="!u.newsletter">
                                                    <div class="nv-info-value nv-info-value--muted">
                                                        <i class="bi bi-envelope-slash"></i> Keine Zustimmung
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="nv-panel-footer">
                                            <button type="button" class="btn-ghost" @click="toggleExpand(u.id)">Abbrechen</button>
                                            <button type="submit" class="btn-primary">
                                                <i class="bi bi-check-lg"></i> Änderungen speichern
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </template>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="nv-pager" x-show="filtered.length > 0">
            <div class="nv-pager__info">
                Zeige
                <strong x-text="Math.min((page - 1) * perPage + 1, filtered.length)"></strong>–<strong x-text="Math.min(page * perPage, filtered.length)"></strong>
                von <strong x-text="filtered.length"></strong>
            </div>
            <div class="nv-pager__nav">
                <button class="nv-pager__btn" :disabled="page === 1" @click="page--">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <template x-for="p in Array.from({length: totalPages}, (_, i) => i + 1)" :key="p">
                    <button class="nv-pager__btn" :class="p === page ? 'is-active' : ''" @click="page = p" x-text="p"></button>
                </template>
                <button class="nv-pager__btn" :disabled="page === totalPages" @click="page++">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>

        {{-- Progress Modal --}}
        <div class="nv-modal-overlay" x-show="modalUser !== null" @click="closeProgress()" @keydown.escape.window="closeProgress()" style="display: none;">
            <div class="nv-modal" @click.stop>
                <div class="nv-modal__head">
                    <div class="nv-modal__user">
                        <div class="nv-modal__avatar">
                            <img :src="modalUser?.avatarUrl" :alt="modalUser?.name || ''" />
                        </div>
                        <div style="min-width: 0;">
                            <div class="nv-modal__eyebrow">Lernfortschritt</div>
                            <div class="nv-modal__title" x-text="modalUser?.name"></div>
                            <div class="nv-modal__sub">
                                <span x-text="modalUser?.email"></span>
                                <span> · Lvl <span x-text="modalUser?.level"></span></span>
                                <span> · <span x-text="(modalUser?.xp ?? 0).toLocaleString('de-DE')"></span> XP</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="nv-modal__close" @click="closeProgress()" aria-label="Schließen">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="nv-modal__tabs">
                    <button type="button" class="nv-modal__tab" :class="modalTab === 'grundausbildung' ? 'nv-modal__tab--active' : ''" @click="modalTab = 'grundausbildung'">
                        <i class="bi bi-mortarboard-fill"></i> Grundausbildung
                        <span class="nv-modal__tab-count" x-text="tabCount('grundausbildung')"></span>
                    </button>
                    <button type="button" class="nv-modal__tab" :class="modalTab === 'zusatzfragen' ? 'nv-modal__tab--active' : ''" @click="modalTab = 'zusatzfragen'">
                        <i class="bi bi-lightbulb-fill"></i> Zusatzfragen
                        <span class="nv-modal__tab-count" x-text="tabCount('zusatzfragen')"></span>
                    </button>
                    <button type="button" class="nv-modal__tab" :class="modalTab === 'lehrgaenge' ? 'nv-modal__tab--active' : ''" @click="modalTab = 'lehrgaenge'">
                        <i class="bi bi-award-fill"></i> Lehrgänge
                        <span class="nv-modal__tab-count" x-text="tabCount('lehrgaenge')"></span>
                    </button>
                </div>

                <div class="nv-modal__body">
                    {{-- Loading state --}}
                    <template x-if="modalLoading">
                        <div class="nv-modal-loading">
                            <i class="bi bi-arrow-clockwise"></i>
                            Fortschrittsdaten werden geladen…
                        </div>
                    </template>

                    {{-- Error state --}}
                    <template x-if="!modalLoading && modalData && modalData.error">
                        <div class="nv-modal-loading" style="color: var(--error);">
                            <i class="bi bi-exclamation-triangle"></i>
                            Fortschrittsdaten konnten nicht geladen werden.
                        </div>
                    </template>

                    {{-- Data state --}}
                    <template x-if="!modalLoading && modalData && !modalData.error">
                        <div>
                            {{-- Summary stats --}}
                            <div class="nv-modal-stats">
                                <div class="nv-modal-stat">
                                    <div class="nv-modal-stat__value nv-modal-stat__value--blue"><span x-text="modalTotals.pct"></span>%</div>
                                    <div class="nv-modal-stat__label">Fortschritt</div>
                                </div>
                                <div class="nv-modal-stat">
                                    <div class="nv-modal-stat__value nv-modal-stat__value--ok" x-text="modalTotals.mastered.toLocaleString('de-DE')"></div>
                                    <div class="nv-modal-stat__label">Gemeistert · 3/3</div>
                                </div>
                                <div class="nv-modal-stat">
                                    <div class="nv-modal-stat__value nv-modal-stat__value--warn" x-text="modalTotals.partial"></div>
                                    <div class="nv-modal-stat__label">In Lernphase</div>
                                </div>
                                <div class="nv-modal-stat">
                                    <div class="nv-modal-stat__value nv-modal-stat__value--err" x-text="modalTotals.sr"></div>
                                    <div class="nv-modal-stat__label">SR · Wiederholung</div>
                                </div>
                            </div>

                            {{-- Empty per-tab --}}
                            <template x-if="modalActiveRows.length === 0">
                                <div class="nv-modal-loading">
                                    <i class="bi bi-inbox"></i>
                                    <template x-if="modalTab === 'lehrgaenge'">
                                        <span>Dieser Nutzer ist in keinen Lehrgang eingeschrieben.</span>
                                    </template>
                                    <template x-if="modalTab !== 'lehrgaenge'">
                                        <span>Keine Daten vorhanden.</span>
                                    </template>
                                </div>
                            </template>

                            {{-- Progress list --}}
                            <div class="nv-prog-list" x-show="modalActiveRows.length > 0">
                                <template x-for="(row, i) in modalActiveRows" :key="modalTab + '-' + row.id">
                                    <div class="nv-prog-row" :class="modalOpenModule === moduleKey(row) ? 'nv-prog-row--open' : ''">
                                        <div class="nv-prog-row__head">
                                            <div class="nv-prog-row__num" :class="`nv-prog-row__num--${pctCls(rowPct(row))}`"
                                                 x-text="modalTab === 'lehrgaenge' ? row.code : (i + 1)"></div>
                                            <div class="nv-prog-row__title" x-text="row.title"></div>
                                            <div class="nv-prog-row__pct" :class="`nv-prog-row__pct--${pctCls(rowPct(row))}`">
                                                <span x-text="rowPct(row)"></span>%
                                            </div>
                                        </div>
                                        <div class="nv-prog-row__bar">
                                            <div class="nv-prog-row__fill" :class="`nv-prog-row__fill--${pctCls(rowPct(row))}`"
                                                 :style="`width: ${rowPct(row)}%;`"></div>
                                        </div>
                                        <div class="nv-prog-row__meta">
                                            <span class="nv-prog-row__meta-item nv-prog-row__meta-item--ok"><span class="dot"></span> <span x-text="row.mastered"></span> gemeistert</span>
                                            <span class="nv-prog-row__meta-item nv-prog-row__meta-item--warn" x-show="row.partial > 0"><span class="dot"></span> <span x-text="row.partial"></span> in Lernphase</span>
                                            <span class="nv-prog-row__meta-item nv-prog-row__meta-item--err" x-show="row.sr > 0"><span class="dot"></span> <span x-text="row.sr"></span> SR</span>
                                            <span class="nv-prog-row__meta-item"><span x-text="row.total"></span> gesamt</span>

                                            <div class="nv-prog-row__bulk">
                                                <button type="button" class="nv-bulk-btn nv-bulk-btn--ok" :disabled="modalBusy" @click="moduleBulkAction(row, 'mastered')" title="Alle als gemeistert markieren">
                                                    <i class="bi bi-check2-all"></i> Alle gemeistert
                                                </button>
                                                <button type="button" class="nv-bulk-btn nv-bulk-btn--sr" :disabled="modalBusy" @click="moduleBulkAction(row, 'sr')" title="Alle auf SR-Wiederholung setzen">
                                                    <i class="bi bi-arrow-repeat"></i> Alle SR
                                                </button>
                                                <button type="button" class="nv-bulk-btn nv-bulk-btn--reset" :disabled="modalBusy" @click="moduleBulkAction(row, 'reset')" title="Alle zurücksetzen">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                                <button type="button" class="nv-bulk-btn" @click="toggleModule(row)" :title="modalOpenModule === moduleKey(row) ? 'Einklappen' : 'Fragen anzeigen'">
                                                    <i :class="`bi bi-chevron-${modalOpenModule === moduleKey(row) ? 'up' : 'down'}`"></i>
                                                    <span x-text="modalOpenModule === moduleKey(row) ? 'Schließen' : 'Fragen'"></span>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Expanded question list --}}
                                        <div class="nv-q-expand" x-show="modalOpenModule === moduleKey(row)" style="display: none;">
                                            <template x-if="!modalModuleQuestions[moduleKey(row)]">
                                                <div class="nv-modal-loading" style="padding: 1rem;">
                                                    <i class="bi bi-arrow-clockwise"></i> Fragen werden geladen…
                                                </div>
                                            </template>
                                            <template x-if="modalModuleQuestions[moduleKey(row)] && modalModuleQuestions[moduleKey(row)].length === 0">
                                                <div class="nv-modal-loading" style="padding: 1rem;">Keine Fragen vorhanden.</div>
                                            </template>
                                            <template x-if="modalModuleQuestions[moduleKey(row)] && modalModuleQuestions[moduleKey(row)].length > 0">
                                                <div>
                                                    <div class="nv-q-legend">
                                                        <span class="nv-q-legend__item"><span class="dot" style="background: var(--success);"></span> Gemeistert · 3/3</span>
                                                        <span class="nv-q-legend__item"><span class="dot" style="background: var(--warning, #f59e0b);"></span> Lernphase · 1/3 · 2/3</span>
                                                        <span class="nv-q-legend__item"><span class="dot" style="background: var(--error);"></span> SR · Wiederholung</span>
                                                        <span class="nv-q-legend__item"><span class="dot" style="background: #94a3b8;"></span> Neu · 0/3</span>
                                                        <span style="margin-left: auto; color: var(--text-muted); font-size: 0.75rem;">
                                                            <i class="bi bi-info-circle"></i> 3× in Folge richtig = gemeistert
                                                        </span>
                                                    </div>
                                                    <div class="nv-q-list">
                                                        <template x-for="(q, qi) in modalModuleQuestions[moduleKey(row)]" :key="q.id">
                                                            <div class="nv-q-item" :class="`nv-q-item--${questionStatus(q)}`">
                                                                <div class="nv-q-item__num" x-text="'#' + (qi + 1)"></div>
                                                                <div class="nv-q-item__status">
                                                                    <template x-if="!q.sr">
                                                                        <span class="nv-q-streak" :title="`Streak ${q.streak}/${modalThreshold}`">
                                                                            <template x-for="k in modalThreshold" :key="k">
                                                                                <span class="nv-q-streak__dot" :class="k <= q.streak ? 'nv-q-streak__dot--on' : ''"></span>
                                                                            </template>
                                                                        </span>
                                                                    </template>
                                                                    <template x-if="q.sr">
                                                                        <i class="bi bi-arrow-repeat" style="color: var(--error);"></i>
                                                                    </template>
                                                                    <span class="nv-q-item__label" x-text="questionStatusLabel(q)"></span>
                                                                </div>
                                                                <div class="nv-q-item__id" x-text="q.label"></div>
                                                                <div class="nv-q-item__actions">
                                                                    <button type="button" class="nv-q-act nv-q-act--plus" :disabled="q.isMastered || modalBusy" @click="questionAction(row, qi, 'increment')" title="+1 Streak">
                                                                        <i class="bi bi-plus-lg"></i>
                                                                    </button>
                                                                    <button type="button" class="nv-q-act nv-q-act--ok" :class="q.isMastered ? 'is-active' : ''" :disabled="modalBusy" @click="questionAction(row, qi, 'mastered')" title="Auf gemeistert (3/3)">
                                                                        <i class="bi bi-check2-all"></i>
                                                                    </button>
                                                                    <button type="button" class="nv-q-act nv-q-act--sr" :class="q.sr ? 'is-active' : ''" :disabled="modalBusy" @click="questionAction(row, qi, 'sr')" :title="q.sr ? 'SR-Flag entfernen' : 'Auf SR setzen'">
                                                                        <i class="bi bi-arrow-repeat"></i>
                                                                    </button>
                                                                    <button type="button" class="nv-q-act nv-q-act--reset" :disabled="modalBusy" @click="questionAction(row, qi, 'reset')" title="Zurücksetzen">
                                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="nv-modal__footer">
                    <div class="nv-modal__footer-hint">
                        <template x-if="modalDirty">
                            <span style="color: var(--success);"><i class="bi bi-check-circle-fill"></i> Änderungen gespeichert (live)</span>
                        </template>
                        <template x-if="!modalDirty">
                            <span><i class="bi bi-info-circle"></i> Klick auf eine Frage öffnet die Aktionen</span>
                        </template>
                    </div>
                    <div class="nv-modal__footer-actions">
                        <a :href="modalUser ? `{{ url('admin/users') }}/${modalUser.id}/progress` : '#'" class="btn-ghost" title="Zur vollständigen Bearbeitungsseite">
                            <i class="bi bi-arrow-up-right-square"></i> Vollansicht
                        </a>
                        <button type="button" class="btn-primary" @click="closeProgress()">Fertig</button>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /.nv-table-card --}}

    {{-- Footer nav --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:2rem;flex-wrap:wrap;gap:1rem;">
        <a href="{{ route('admin.questions.index') }}" class="btn-ghost">
            Zur Fragenverwaltung
        </a>
        <div style="color:var(--text-muted);font-size:0.875rem;">
            Gesamt: {{ $users->count() }} Benutzer
        </div>
    </div>

</div>{{-- /.admin-container --}}
</div>{{-- /.dashboard-container --}}
@endsection
