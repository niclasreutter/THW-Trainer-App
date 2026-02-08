@extends('layouts.app')

@section('title', 'Session abgeschlossen')

@push('styles')
<style>
    .summary-container {
        max-width: 640px;
        margin: 0 auto;
        padding: 2rem;
        min-height: 70vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .summary-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .summary-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.25rem;
        font-size: 2rem;
    }

    .summary-icon-great {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
        box-shadow: 0 0 30px rgba(34, 197, 94, 0.2);
    }

    .summary-icon-good {
        background: rgba(251, 191, 36, 0.15);
        color: #fbbf24;
        box-shadow: 0 0 30px rgba(251, 191, 36, 0.2);
    }

    .summary-icon-okay {
        background: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
        box-shadow: 0 0 30px rgba(59, 130, 246, 0.2);
    }

    .summary-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .summary-mode {
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .summary-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .summary-stat {
        padding: 1.25rem;
        text-align: center;
    }

    .summary-stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.35rem;
    }

    .summary-stat-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .summary-accuracy-bar {
        margin-bottom: 1.5rem;
    }

    .accuracy-bar-track {
        height: 8px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 4px;
        overflow: hidden;
        margin-top: 0.75rem;
    }

    .accuracy-bar-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 1.5s ease-out;
        width: 0;
    }

    .accuracy-bar-fill.great { background: linear-gradient(90deg, #22c55e, #16a34a); }
    .accuracy-bar-fill.good { background: var(--gradient-gold); }
    .accuracy-bar-fill.okay { background: linear-gradient(90deg, #3b82f6, #2563eb); }
    .accuracy-bar-fill.low { background: linear-gradient(90deg, #ef4444, #dc2626); }

    .accuracy-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .accuracy-label-text {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-secondary);
    }

    .accuracy-label-value {
        font-size: 1.1rem;
        font-weight: 800;
    }

    .summary-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    @media (min-width: 480px) {
        .summary-actions {
            flex-direction: row;
        }
        .summary-actions a {
            flex: 1;
            text-align: center;
        }
    }

    @media (max-width: 480px) {
        .summary-container { padding: 1rem; }
        .summary-stats { grid-template-columns: 1fr 1fr; gap: 0.5rem; }
        .summary-stat { padding: 1rem; }
        .summary-stat-value { font-size: 1.5rem; }
    }
</style>
@endpush

@section('content')
<div class="summary-container">
    <div class="summary-header">
        @php
            $iconClass = $accuracy >= 80 ? 'summary-icon-great' : ($accuracy >= 50 ? 'summary-icon-good' : 'summary-icon-okay');
            $iconName = $accuracy >= 80 ? 'bi-check-circle-fill' : ($accuracy >= 50 ? 'bi-star-fill' : 'bi-lightning-charge-fill');
            $titleText = $accuracy >= 80 ? 'Stark!' : ($accuracy >= 50 ? 'Gut gemacht' : 'Weiter so');
        @endphp
        <div class="summary-icon {{ $iconClass }}">
            <i class="bi {{ $iconName }}"></i>
        </div>
        <h1 class="summary-title">{{ $titleText }}</h1>
        <div class="summary-mode">{{ $modeName }} &middot; {{ $durationMinutes }} Min.</div>
    </div>

    <div class="summary-stats">
        <div class="glass-tl summary-stat">
            <div class="summary-stat-value text-gradient-gold" data-count="{{ $totalAnswered }}">0</div>
            <div class="summary-stat-label">Beantwortet</div>
        </div>
        <div class="glass-br summary-stat">
            <div class="summary-stat-value text-success" data-count="{{ $stats['correct'] }}">0</div>
            <div class="summary-stat-label">Richtig</div>
        </div>
        <div class="glass summary-stat">
            <div class="summary-stat-value" style="color: var(--gold-start);" data-count="{{ $stats['points'] }}">0</div>
            <div class="summary-stat-label">Punkte</div>
        </div>
        <div class="glass summary-stat">
            <div class="summary-stat-value" style="color: #a855f7;" data-count="{{ $stats['mastered'] }}">0</div>
            <div class="summary-stat-label">Gemeistert</div>
        </div>
    </div>

    <div class="summary-accuracy-bar glass" style="padding: 1.25rem;">
        <div class="accuracy-label">
            <span class="accuracy-label-text">Genauigkeit</span>
            <span class="accuracy-label-value {{ $accuracy >= 80 ? 'text-success' : ($accuracy >= 50 ? 'text-gold' : 'text-error') }}">
                <span data-count="{{ $accuracy }}">0</span>%
            </span>
        </div>
        @php
            $barClass = $accuracy >= 80 ? 'great' : ($accuracy >= 50 ? 'good' : ($accuracy >= 30 ? 'okay' : 'low'));
        @endphp
        <div class="accuracy-bar-track">
            <div class="accuracy-bar-fill {{ $barClass }}" data-width="{{ $accuracy }}"></div>
        </div>
    </div>

    <div class="summary-actions">
        <a href="{{ route('practice.menu') }}" class="btn-primary">Weiter lernen</a>
        <a href="{{ route('dashboard') }}" class="btn-secondary">Dashboard</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Counter animation
    document.querySelectorAll('[data-count]').forEach(function(el) {
        const target = parseInt(el.getAttribute('data-count'));
        const duration = 1200;
        const start = performance.now();

        function update(currentTime) {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            const easeOut = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(target * easeOut);
            if (progress < 1) requestAnimationFrame(update);
            else el.textContent = target;
        }

        setTimeout(function() { requestAnimationFrame(update); }, 300);
    });

    // Accuracy bar animation
    setTimeout(function() {
        document.querySelectorAll('.accuracy-bar-fill').forEach(function(bar) {
            bar.style.width = bar.getAttribute('data-width') + '%';
        });
    }, 500);
});
</script>
@endsection
