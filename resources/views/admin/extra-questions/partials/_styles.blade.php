{{-- Shared Zusatz-Fragen styles (aligned with Claude Design mockup).
     Scoped with .zf-* prefix so the global design system stays intact. --}}
<style>
    .zf-page-title { margin-bottom: 1.5rem; }
    .zf-page-title__eyebrow {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }
    .zf-page-title__h1 {
        font-family: 'Barlow Condensed', sans-serif;
        font-weight: 800;
        font-size: 1.5rem;
        line-height: 1.2;
        background: linear-gradient(135deg, #5b9aff, #0055cc);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0 0 0.25rem;
    }
    .zf-page-title__sub {
        font-size: 1rem;
        color: var(--text-secondary);
        margin: 0;
    }

    .zf-section-label {
        display: block;
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
    }

    .zf-stat-pills {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        width: 100%;
        margin: 1.5rem 0 1.75rem;
    }
    @media (max-width: 768px) { .zf-stat-pills { grid-template-columns: repeat(2, 1fr); } }
    .zf-stat-pill {
        background: var(--bg-elevated);
        border: 1px solid rgba(0, 51, 127, 0.10);
        border-radius: 0.75rem;
        padding: 0.875rem 1rem;
        text-align: center;
    }
    html.light-mode .zf-stat-pill,
    .light-mode .zf-stat-pill {
        background: #ffffff;
        border-color: rgba(0, 51, 127, 0.06);
        box-shadow: 0 1px 3px rgba(0, 51, 127, 0.04);
    }
    .zf-stat-pill__value {
        font-family: var(--font-sans, 'Figtree', sans-serif);
        font-weight: 800;
        font-size: 1.75rem;
        line-height: 1.1;
        letter-spacing: -0.015em;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }
    .zf-stat-pill__value--blue  { color: var(--thw-blue); }
    html:not(.light-mode) .zf-stat-pill__value--blue { color: #5b9aff; }
    .zf-stat-pill__value--ok    { color: #16a34a; }
    .zf-stat-pill__value--warn  { color: #d97706; }
    .zf-stat-pill__label {
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--text-muted);
    }

    .zf-action-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .zf-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.6rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-secondary);
        background: transparent;
        border: 1px solid transparent;
        border-radius: 0.5rem;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .zf-back-btn:hover {
        color: var(--thw-blue);
        background: rgba(0, 51, 127, 0.06);
    }
    html:not(.light-mode) .zf-back-btn:hover { color: #5b9aff; background: rgba(91, 154, 255, 0.08); }

    .zf-form-card {
        padding: 1.5rem;
        border-radius: 1rem;
        background: var(--bg-elevated);
        border: 1px solid rgba(0, 51, 127, 0.08);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 2px 12px rgba(0, 51, 127, 0.06);
    }
    html:not(.light-mode) .zf-form-card {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }
    .zf-form-card + .zf-form-card { margin-top: 1rem; }

    .zf-form-stack { display: flex; flex-direction: column; gap: 1rem; }

    .zf-form-card__label {
        margin-bottom: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }
    .zf-form-card__label .zf-hint {
        font-family: var(--font-sans, 'Figtree', sans-serif);
        font-weight: 500;
        letter-spacing: 0;
        color: var(--text-muted);
        font-size: 0.75rem;
    }

    .zf-type-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }
    @media (max-width: 768px) { .zf-type-grid { grid-template-columns: 1fr; } }
    .zf-type-card {
        position: relative;
        text-align: left;
        padding: 1.125rem 1rem 1rem;
        border-radius: 0.875rem;
        background: var(--bg-elevated);
        border: 1.5px solid rgba(0, 51, 127, 0.10);
        cursor: pointer;
        transition: all 0.25s ease;
        font-family: inherit;
        display: flex;
        flex-direction: column;
        gap: 0.625rem;
        color: inherit;
    }
    html:not(.light-mode) .zf-type-card {
        background: rgba(255, 255, 255, 0.03);
        border-color: rgba(255, 255, 255, 0.08);
    }
    .zf-type-card:hover {
        border-color: rgba(0, 51, 127, 0.40);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 51, 127, 0.08);
    }
    .zf-type-card.is-selected {
        border-color: var(--thw-blue);
        background: rgba(0, 51, 127, 0.04);
        box-shadow: 0 0 0 3px rgba(0, 51, 127, 0.12), 0 4px 12px rgba(0, 51, 127, 0.15);
    }
    html:not(.light-mode) .zf-type-card.is-selected {
        border-color: #5b9aff;
        background: rgba(91, 154, 255, 0.06);
        box-shadow: 0 0 0 3px rgba(91, 154, 255, 0.18), 0 4px 12px rgba(0, 51, 127, 0.25);
    }
    .zf-type-card__icon {
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        display: grid;
        place-items: center;
        background: rgba(0, 51, 127, 0.10);
        color: var(--thw-blue);
        font-size: 1rem;
    }
    html:not(.light-mode) .zf-type-card__icon {
        background: rgba(91, 154, 255, 0.15);
        color: #5b9aff;
    }
    .zf-type-card__title {
        font-weight: 700;
        font-size: 0.9375rem;
        color: var(--text-primary);
    }
    .zf-type-card__desc {
        font-size: 0.75rem;
        line-height: 1.45;
        color: var(--text-secondary);
    }
    .zf-type-card__check {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        width: 20px;
        height: 20px;
        border-radius: 999px;
        background: var(--thw-blue);
        color: #fff;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
    }
    .zf-type-card.is-selected .zf-type-card__check { display: grid; place-items: center; }

    .zf-field { margin-bottom: 1rem; }
    .zf-field:last-child { margin-bottom: 0; }
    .zf-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.375rem;
    }
    .zf-label .zf-req { color: #ef4444; }
    .zf-help {
        display: block;
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.375rem;
    }
    .zf-input, .zf-textarea, .zf-select {
        width: 100%;
        padding: 0.625rem 0.875rem;
        font-family: inherit;
        font-size: 0.9375rem;
        color: var(--text-primary);
        background: var(--bg-elevated);
        border: 1px solid rgba(0, 51, 127, 0.15);
        border-radius: 0.5rem;
        outline: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    html:not(.light-mode) .zf-input,
    html:not(.light-mode) .zf-textarea,
    html:not(.light-mode) .zf-select {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.12);
    }
    .zf-input:focus, .zf-textarea:focus, .zf-select:focus {
        border-color: var(--thw-blue);
        box-shadow: 0 0 0 3px rgba(0, 51, 127, 0.12);
    }
    html:not(.light-mode) .zf-input:focus,
    html:not(.light-mode) .zf-textarea:focus,
    html:not(.light-mode) .zf-select:focus {
        border-color: #5b9aff;
        box-shadow: 0 0 0 3px rgba(91, 154, 255, 0.18);
    }
    .zf-textarea { min-height: 92px; resize: vertical; line-height: 1.5; }
    .zf-select { appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2300337F' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1rem; padding-right: 2.25rem; }

    .zf-option-row {
        display: grid;
        grid-template-columns: 1.75rem 1fr auto;
        gap: 0.625rem;
        align-items: center;
        padding: 0.625rem 0;
    }
    .zf-option-row + .zf-option-row { border-top: 1px solid rgba(0, 51, 127, 0.06); }
    html:not(.light-mode) .zf-option-row + .zf-option-row { border-top-color: rgba(255, 255, 255, 0.06); }
    .zf-option-num {
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 0.5rem;
        background: rgba(0, 51, 127, 0.08);
        display: grid;
        place-items: center;
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--thw-blue);
    }
    html:not(.light-mode) .zf-option-num {
        background: rgba(91, 154, 255, 0.12);
        color: #5b9aff;
    }
    .zf-option-row.is-correct .zf-option-num { background: #22c55e; color: #fff; }
    .zf-option-body {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        flex-wrap: wrap;
        min-width: 0;
    }
    .zf-option-body > .zf-input { flex: 1 1 160px; }
    .zf-option-remove {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        background: transparent;
        border: 1px solid rgba(0, 51, 127, 0.10);
        color: var(--text-muted);
        cursor: pointer;
        display: grid;
        place-items: center;
        transition: all 0.15s ease;
    }
    html:not(.light-mode) .zf-option-remove { border-color: rgba(255, 255, 255, 0.12); }
    .zf-option-remove:hover {
        border-color: #ef4444;
        color: #ef4444;
        background: rgba(239, 68, 68, 0.06);
    }
    .zf-option-remove:disabled { opacity: 0.4; cursor: not-allowed; }

    .zf-add-row {
        display: flex;
        justify-content: center;
        padding: 0.75rem 0 0;
        margin-top: 0.5rem;
        border-top: 1px dashed rgba(0, 51, 127, 0.12);
    }
    html:not(.light-mode) .zf-add-row { border-top-color: rgba(255, 255, 255, 0.10); }
    .zf-add-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.5rem 0.875rem;
        border-radius: 0.5rem;
        border: 1px dashed rgba(0, 51, 127, 0.2);
        background: transparent;
        color: var(--thw-blue);
        font-weight: 600;
        font-size: 0.8125rem;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s ease;
    }
    html:not(.light-mode) .zf-add-btn {
        color: #5b9aff;
        border-color: rgba(91, 154, 255, 0.25);
    }
    .zf-add-btn:hover:not(:disabled) {
        background: rgba(0, 51, 127, 0.04);
        border-color: var(--thw-blue);
        border-style: solid;
    }
    html:not(.light-mode) .zf-add-btn:hover:not(:disabled) {
        background: rgba(91, 154, 255, 0.08);
        border-color: #5b9aff;
    }
    .zf-add-btn:disabled { opacity: 0.45; cursor: not-allowed; }

    .zf-pair {
        padding: 0.875rem;
        border-radius: 0.75rem;
        background: rgba(0, 51, 127, 0.025);
        border: 1px solid rgba(0, 51, 127, 0.06);
        margin-bottom: 0.625rem;
        position: relative;
    }
    html:not(.light-mode) .zf-pair {
        background: rgba(255, 255, 255, 0.02);
        border-color: rgba(255, 255, 255, 0.06);
    }
    .zf-pair__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.625rem;
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
    }
    .zf-pair__arrow {
        display: grid;
        place-items: center;
        padding: 0.4rem 0;
        color: var(--thw-blue);
        font-size: 0.875rem;
    }
    html:not(.light-mode) .zf-pair__arrow { color: #5b9aff; }
    .zf-pair > .zf-input + .zf-pair__arrow { margin-top: 0.25rem; }

    .zf-upload {
        padding: 1.5rem 1.25rem;
        border-radius: 0.75rem;
        background: rgba(0, 51, 127, 0.025);
        border: 2px dashed rgba(0, 51, 127, 0.18);
        text-align: center;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    html:not(.light-mode) .zf-upload {
        background: rgba(255, 255, 255, 0.02);
        border-color: rgba(255, 255, 255, 0.12);
    }
    .zf-upload:hover {
        border-color: var(--thw-blue);
        background: rgba(0, 51, 127, 0.04);
    }
    html:not(.light-mode) .zf-upload:hover {
        border-color: #5b9aff;
        background: rgba(91, 154, 255, 0.06);
    }
    .zf-upload__icon {
        font-size: 1.5rem;
        color: var(--thw-blue);
        margin-bottom: 0.5rem;
    }
    html:not(.light-mode) .zf-upload__icon { color: #5b9aff; }
    .zf-upload__title {
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }
    .zf-upload__sub {
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    .zf-upload input[type="file"] {
        position: absolute;
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        z-index: -1;
    }

    .zf-image-preview {
        margin-top: 0.75rem;
        max-width: 320px;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1px solid rgba(0, 51, 127, 0.10);
    }
    html:not(.light-mode) .zf-image-preview { border-color: rgba(255, 255, 255, 0.10); }
    .zf-image-preview img { width: 100%; height: auto; display: block; }

    .zf-image-tile {
        display: grid;
        grid-template-columns: 120px 1fr;
        gap: 0.75rem;
        padding: 0.875rem;
        border-radius: 0.75rem;
        background: rgba(0, 51, 127, 0.025);
        border: 1px solid rgba(0, 51, 127, 0.06);
        margin-bottom: 0.625rem;
    }
    .zf-image-tile--no-thumb { grid-template-columns: 1fr; }
    html:not(.light-mode) .zf-image-tile {
        background: rgba(255, 255, 255, 0.02);
        border-color: rgba(255, 255, 255, 0.06);
    }
    .zf-image-tile__head {
        grid-column: 1 / -1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
    }
    .zf-image-tile__num {
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
    }
    .zf-image-tile__thumb {
        width: 120px;
        height: 90px;
        border-radius: 0.5rem;
        overflow: hidden;
        background: rgba(0, 51, 127, 0.05);
        display: grid;
        place-items: center;
        color: var(--thw-blue);
        border: 1px dashed rgba(0, 51, 127, 0.18);
    }
    html:not(.light-mode) .zf-image-tile__thumb {
        background: rgba(91, 154, 255, 0.08);
        color: #5b9aff;
        border-color: rgba(255, 255, 255, 0.10);
    }
    .zf-image-tile__thumb img { width: 100%; height: 100%; object-fit: cover; }
    .zf-image-tile__body {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        min-width: 0;
    }

    .zf-correct-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: flex-start;
        gap: 0.5rem;
        min-width: 6.2rem;
        padding: 0.375rem 0.75rem 0.375rem 0.5rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.06);
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', ui-monospace, 'SF Mono', 'Cascadia Mono', Menlo, Consolas, monospace;
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
    html.light-mode .zf-correct-toggle {
        background: #f8fafc;
        border-color: rgba(0, 51, 127, 0.08);
    }
    .zf-correct-toggle:hover {
        color: var(--text-primary);
        border-color: rgba(255, 255, 255, 0.10);
    }
    html.light-mode .zf-correct-toggle:hover { border-color: rgba(0, 51, 127, 0.14); }
    .zf-correct-toggle.is-correct {
        background: rgba(34, 197, 94, 0.18);
        border-color: rgba(34, 197, 94, 0.40);
        color: #22c55e;
    }
    .zf-correct-toggle .check-icon {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.06);
        display: grid;
        place-items: center;
        color: transparent;
        font-size: 0.625rem;
        transition: all 150ms ease;
    }
    html.light-mode .zf-correct-toggle .check-icon { background: rgba(0, 51, 127, 0.08); }
    .zf-correct-toggle.is-correct .check-icon { background: #22c55e; color: #fff; }
    .zf-correct-toggle input[type="checkbox"] {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0 0 0 0);
        white-space: nowrap;
        margin: -1px;
        padding: 0;
        border: 0;
    }

    .zf-form-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 0.25rem;
        flex-wrap: wrap;
    }

    .zf-alert {
        padding: 0.875rem 1rem;
        border-radius: 0.75rem;
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }
    .zf-alert--error {
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.22);
        color: #ef4444;
    }
    .zf-alert--success {
        background: rgba(22, 163, 74, 0.08);
        border: 1px solid rgba(22, 163, 74, 0.22);
        color: #16a34a;
    }
    .zf-alert--info {
        background: rgba(0, 51, 127, 0.05);
        border: 1px solid rgba(0, 51, 127, 0.15);
        color: var(--thw-blue);
    }
    html:not(.light-mode) .zf-alert--info {
        background: rgba(91, 154, 255, 0.08);
        border-color: rgba(91, 154, 255, 0.20);
        color: #5b9aff;
    }
    .zf-alert strong { display: block; color: inherit; margin-bottom: 0.15rem; }
    .zf-alert ul { margin: 0.15rem 0 0 1.25rem; padding: 0; }

    .zf-field-error {
        display: block;
        margin-top: 0.35rem;
        color: #ef4444;
        font-size: 0.8rem;
    }

    [x-cloak] { display: none !important; }
</style>
