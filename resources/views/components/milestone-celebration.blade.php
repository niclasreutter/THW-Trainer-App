{{-- Milestone Celebration Component
     Checks for milestone events and shows fullscreen celebration animations.
     Include this in layouts/app.blade.php --}}

@auth
@php
    $milestoneData = session('milestone_celebration');
    if ($milestoneData) {
        session()->forget('milestone_celebration');
    }
@endphp

@if($milestoneData)
<div class="milestone-overlay" id="milestoneOverlay" onclick="dismissMilestone()">
    <div class="milestone-content">
        @if($milestoneData['type'] === 'level_up')
            <span class="milestone-icon">
                <i class="bi bi-arrow-up-circle-fill" style="color: var(--gold-start);"></i>
            </span>
            <div class="milestone-title">Level Up!</div>
            <div class="milestone-subtitle">Du hast Level {{ $milestoneData['level'] ?? '?' }} erreicht</div>
            <div class="milestone-badge milestone-glow" style="background: var(--gradient-gold); color: #0a0a0b;">
                <i class="bi bi-star-fill"></i>
                Level {{ $milestoneData['level'] ?? '?' }}
            </div>

        @elseif($milestoneData['type'] === 'first_exam_passed')
            <span class="milestone-icon">
                <i class="bi bi-patch-check-fill" style="color: var(--success);"></i>
            </span>
            <div class="milestone-title">Erste Prüfung bestanden!</div>
            <div class="milestone-subtitle">Du hast deine erste THW-Prüfung bestanden</div>
            <div class="milestone-badge" style="background: rgba(34, 197, 94, 0.2); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.3);">
                <i class="bi bi-check-circle-fill"></i>
                Bestanden
            </div>

        @elseif($milestoneData['type'] === 'lehrgang_complete')
            <span class="milestone-icon">
                <i class="bi bi-mortarboard-fill" style="color: #a855f7;"></i>
            </span>
            <div class="milestone-title">Lehrgang abgeschlossen!</div>
            <div class="milestone-subtitle">{{ $milestoneData['name'] ?? 'Lehrgang' }} zu 100% gemeistert</div>
            <div class="milestone-badge" style="background: rgba(168, 85, 247, 0.2); color: #a855f7; border: 1px solid rgba(168, 85, 247, 0.3);">
                <i class="bi bi-trophy-fill"></i>
                100% Gemeistert
            </div>

        @elseif($milestoneData['type'] === 'streak')
            <span class="milestone-icon">
                <i class="bi bi-fire" style="color: #f59e0b;"></i>
            </span>
            <div class="milestone-title">{{ $milestoneData['days'] ?? '?' }} Tage Streak!</div>
            <div class="milestone-subtitle">Du lernst {{ $milestoneData['days'] ?? '?' }} Tage in Folge</div>
            <div class="milestone-badge milestone-streak-glow" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">
                <i class="bi bi-fire"></i>
                {{ $milestoneData['days'] ?? '?' }} Tage
            </div>

        @elseif($milestoneData['type'] === 'theory_complete')
            <span class="milestone-icon">
                <i class="bi bi-stars" style="color: var(--gold-start);"></i>
            </span>
            <div class="milestone-title">Alle Fragen gemeistert!</div>
            <div class="milestone-subtitle">Du hast die gesamte Theorie durchgearbeitet</div>
            <div class="milestone-badge milestone-glow" style="background: var(--gradient-gold); color: #0a0a0b;">
                <i class="bi bi-patch-check-fill"></i>
                100% Theorie
            </div>
        @endif

        <div style="margin-top: 2rem;">
            <button onclick="dismissMilestone()" class="btn-ghost" style="color: var(--text-muted); font-size: 0.85rem;">
                Weiter
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.0/dist/confetti.browser.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('milestoneOverlay');
    if (!overlay) return;

    // Trigger confetti based on type
    const type = '{{ $milestoneData['type'] ?? '' }}';

    setTimeout(() => {
        if (type === 'level_up') {
            // Gold confetti burst
            confetti({ particleCount: 100, spread: 70, colors: ['#fbbf24', '#f59e0b', '#fcd34d'], origin: { y: 0.6 } });
        } else if (type === 'first_exam_passed' || type === 'theory_complete') {
            // Multi-color celebration
            const duration = 3000;
            const end = Date.now() + duration;
            (function frame() {
                confetti({ particleCount: 5, angle: 60, spread: 55, origin: { x: 0 }, colors: ['#fbbf24', '#22c55e', '#3b82f6'] });
                confetti({ particleCount: 5, angle: 120, spread: 55, origin: { x: 1 }, colors: ['#fbbf24', '#22c55e', '#3b82f6'] });
                if (Date.now() < end) requestAnimationFrame(frame);
            }());
        } else if (type === 'streak') {
            // Fire-themed confetti
            confetti({ particleCount: 80, spread: 60, colors: ['#ef4444', '#f59e0b', '#fbbf24'], origin: { y: 0.6 } });
        } else if (type === 'lehrgang_complete') {
            // Purple celebration
            confetti({ particleCount: 100, spread: 70, colors: ['#a855f7', '#8b5cf6', '#fbbf24'], origin: { y: 0.6 } });
        }
    }, 400);

    // Auto-dismiss after 8 seconds
    setTimeout(dismissMilestone, 8000);
});

function dismissMilestone() {
    const overlay = document.getElementById('milestoneOverlay');
    if (overlay) {
        overlay.style.opacity = '0';
        overlay.style.transition = 'opacity 0.3s ease-out';
        setTimeout(() => overlay.remove(), 300);
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') dismissMilestone();
});
</script>
@endif
@endauth
