@extends('layouts.app')

@section('title', 'Prüfungs-Feedback - Admin')

@push('styles')
<style>
    .star-display { color: #FFD700; letter-spacing: 2px; font-size: 1.1rem; }
    .star-empty { color: rgba(255, 255, 255, 0.15); }
    .feedback-card {
        padding: 1.25rem;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 0.75rem;
        transition: all 0.2s;
    }
    .feedback-card:hover {
        background: rgba(255, 255, 255, 0.03);
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Prüfungs-Feedback <span>Übersicht</span></h1>
        <p class="page-subtitle">Rückmeldungen von Nutzern nach ihrer THW-Prüfung</p>
    </header>

    @if(session('success'))
        <div class="glass-success" style="padding: 1.25rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-start;">
            <i class="bi bi-check-circle" style="font-size: 1.25rem; flex-shrink: 0;"></i>
            <div>
                <strong>Erfolg!</strong>
                <p style="margin: 0.25rem 0 0 0;">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-thw-blue">
                <i class="bi bi-clipboard-data"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $stats['total'] }}</div>
                <div class="stat-pill-label">Feedbacks</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-success">
                <i class="bi bi-check-circle"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $stats['pass_rate'] }}%</div>
                <div class="stat-pill-label">Bestanden</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-gold">
                <i class="bi bi-star"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $stats['avg_rating'] ? number_format($stats['avg_rating'], 1) : '-' }}</div>
                <div class="stat-pill-label">Bewertung ({{ $stats['rating_count'] }})</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-thw-blue">
                <i class="bi bi-chat-left-text"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $stats['with_text'] }}</div>
                <div class="stat-pill-label">Mit Kommentar</div>
            </div>
        </div>
    </div>

    {{-- Bestanden/Durchgefallen Balken --}}
    @if($stats['total'] > 0)
    <div class="glass" style="padding: 1.25rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
            <span style="color: #22c55e; font-weight: 600;">{{ $stats['passed'] }} bestanden</span>
            <span style="color: #ef4444; font-weight: 600;">{{ $stats['failed'] }} durchgefallen</span>
        </div>
        <div style="height: 10px; background: rgba(239, 68, 68, 0.3); border-radius: 5px; overflow: hidden;">
            <div style="height: 100%; width: {{ $stats['pass_rate'] }}%; background: #22c55e; border-radius: 5px; transition: width 0.5s;"></div>
        </div>
    </div>
    @endif

    {{-- Filter --}}
    <div class="glass hover-lift" style="padding: 1.5rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 1rem 0;">Filter</h3>
        <form method="GET" action="{{ route('admin.exam-feedback.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div>
                <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Ergebnis</label>
                <select name="result" style="padding: 0.625rem 1rem; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.5rem; color: var(--text-primary); outline: none;">
                    <option value="">Alle</option>
                    <option value="passed" {{ request('result') == 'passed' ? 'selected' : '' }}>Bestanden</option>
                    <option value="failed" {{ request('result') == 'failed' ? 'selected' : '' }}>Durchgefallen</option>
                </select>
            </div>

            <div>
                <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Bewertung</label>
                <select name="rating" style="padding: 0.625rem 1rem; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.5rem; color: var(--text-primary); outline: none;">
                    <option value="">Alle</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Stern{{ $i > 1 ? 'e' : '' }}</option>
                    @endfor
                </select>
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <button type="submit" class="btn-primary" style="padding: 0.625rem 1.5rem;">Filtern</button>
                <a href="{{ route('admin.exam-feedback.index') }}" class="btn-ghost" style="padding: 0.625rem 1.5rem;">Zurücksetzen</a>
            </div>
        </form>
    </div>

    {{-- Feedback-Liste --}}
    <div class="glass" style="padding: 1.5rem;">
        @if($feedbacks->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($feedbacks as $fb)
                    <div class="feedback-card">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                            <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                                @if($fb->passed)
                                    <span style="display: inline-block; background: rgba(34, 197, 94, 0.15); color: #22c55e; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 600;">Bestanden</span>
                                @else
                                    <span style="display: inline-block; background: rgba(239, 68, 68, 0.15); color: #ef4444; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 600;">Durchgefallen</span>
                                @endif

                                @if($fb->rating)
                                    <span class="star-display">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $fb->rating)
                                                <i class="bi bi-star-fill"></i>
                                            @else
                                                <i class="bi bi-star star-empty"></i>
                                            @endif
                                        @endfor
                                    </span>
                                @endif

                                <span style="font-size: 0.8rem; color: var(--text-muted); background: rgba(255,255,255,0.05); padding: 0.25rem 0.6rem; border-radius: 4px;">
                                    {{ $fb->publish_mode === 'named' ? 'Klarname' : 'Anonym' }}
                                </span>
                            </div>
                            <span style="font-size: 0.85rem; color: var(--text-muted); white-space: nowrap;">
                                {{ $fb->updated_at->format('d.m.Y H:i') }}
                            </span>
                        </div>

                        <div style="margin-bottom: 0.75rem;">
                            <span style="font-weight: 700; color: var(--text-primary);">
                                {{ $fb->user?->name ?? 'Gelöschter User' }}
                            </span>
                            @if($fb->user)
                                <span style="font-size: 0.85rem; color: var(--text-muted); margin-left: 0.5rem;">
                                    {{ $fb->user->email }} (ID: {{ $fb->user->id }})
                                </span>
                            @endif
                        </div>

                        @if($fb->feedback)
                            <div style="background: rgba(255, 255, 255, 0.03); border-radius: 8px; padding: 1rem; margin-bottom: 0.75rem; border-left: 3px solid rgba(255, 215, 0, 0.3);">
                                <p style="margin: 0; color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; white-space: pre-line;">{{ $fb->feedback }}</p>
                            </div>
                        @else
                            <p style="margin: 0 0 0.75rem 0; color: var(--text-muted); font-size: 0.9rem; font-style: italic;">Kein Kommentar</p>
                        @endif

                        <div style="display: flex; gap: 0.5rem;">
                            <form method="POST" action="{{ route('admin.exam-feedback.destroy', $fb) }}"
                                  onsubmit="return confirm('Feedback wirklich löschen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-ghost" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; color: var(--error);">
                                    Löschen
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 2rem;">
                {{ $feedbacks->links() }}
            </div>
        @else
            <div style="padding: 3rem 1rem; text-align: center; color: var(--text-muted);">
                <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="bi bi-clipboard-x"></i></div>
                <p style="margin: 0 0 0.5rem 0; font-weight: 700;">Noch kein Feedback vorhanden</p>
                <p style="margin: 0; font-size: 0.95rem;">Sobald Nutzer nach ihrer Prüfung Feedback geben, erscheint es hier.</p>
            </div>
        @endif
    </div>
</div>
@endsection
