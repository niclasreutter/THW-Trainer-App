@extends('layouts.app')
@section('title', 'Fehlermeldungen - Admin')

@push('styles')
<style>
    .issues-table { width: 100%; border-collapse: collapse; }
    .issues-table th { color: var(--text-secondary); padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.875rem; }
    .issues-table th.text-center { text-align: center; }
    .issues-table th.text-right { text-align: right; }
    .issues-table td { padding: 0.875rem 1rem; }

    .issue-card-mobile { display: none; }

    @media (max-width: 768px) {
        .issues-table-wrapper { display: none; }
        .issue-card-mobile { display: block; }
        .issue-card-mobile + .issue-card-mobile { margin-top: 0.75rem; }
        .filter-label { min-width: unset !important; width: 100% !important; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Fehlermeldungen <span>Übersicht</span></h1>
        <p class="page-subtitle">Alle Fehlermeldungen aus Lehrgängen und Grundausbildung</p>
    </header>

    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-thw-blue">
                <i class="bi bi-exclamation-circle"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $totalIssues }}</div>
                <div class="stat-pill-label">Gesamt</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-error">
                <i class="bi bi-circle-fill"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $openIssues }}</div>
                <div class="stat-pill-label">Offen</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-gold">
                <i class="bi bi-clock"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $inReviewIssues }}</div>
                <div class="stat-pill-label">In Bearbeitung</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-success">
                <i class="bi bi-check-circle"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $resolvedIssues }}</div>
                <div class="stat-pill-label">Gelöst</div>
            </div>
        </div>
    </div>

    <div class="glass hover-lift" style="padding: 1.5rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 1rem 0;">Filter</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem;">
            <span style="color: var(--text-muted); font-size: 0.8rem; font-weight: 600; align-self: center; min-width: 50px;">Status:</span>
            <a href="{{ route('admin.issues.index', ['status' => 'all', 'source' => $source]) }}"
               class="btn-{{ $status === 'all' ? 'primary' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                Alle
            </a>
            <a href="{{ route('admin.issues.index', ['status' => 'open', 'source' => $source]) }}"
               class="btn-{{ $status === 'open' ? 'primary' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                Offen
            </a>
            <a href="{{ route('admin.issues.index', ['status' => 'in_review', 'source' => $source]) }}"
               class="btn-{{ $status === 'in_review' ? 'primary' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                In Bearbeitung
            </a>
            <a href="{{ route('admin.issues.index', ['status' => 'resolved', 'source' => $source]) }}"
               class="btn-{{ $status === 'resolved' ? 'primary' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                Gelöst
            </a>
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
            <span style="color: var(--text-muted); font-size: 0.8rem; font-weight: 600; align-self: center; min-width: 50px;">Quelle:</span>
            <a href="{{ route('admin.issues.index', ['status' => $status, 'source' => 'all']) }}"
               class="btn-{{ $source === 'all' ? 'primary' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                Alle
            </a>
            <a href="{{ route('admin.issues.index', ['status' => $status, 'source' => 'lehrgang']) }}"
               class="btn-{{ $source === 'lehrgang' ? 'primary' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                Lehrgänge
            </a>
            <a href="{{ route('admin.issues.index', ['status' => $status, 'source' => 'question']) }}"
               class="btn-{{ $source === 'question' ? 'primary' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                Grundausbildung
            </a>
        </div>
    </div>

    @if($issues->count() > 0)
        {{-- Desktop: Table --}}
        <div class="glass hover-lift issues-table-wrapper" style="padding: 1.5rem; overflow-x: auto;">
            <table class="issues-table">
                <thead>
                    <tr style="border-bottom: 2px solid rgba(255, 255, 255, 0.1);">
                        <th>Frage</th>
                        <th>Quelle</th>
                        <th class="text-center">Meldungen</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($issues as $issue)
                        <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.06); transition: all 0.2s;"
                            onmouseover="this.style.background='rgba(255, 255, 255, 0.03)'"
                            onmouseout="this.style.background='transparent'">
                            <td>
                                <div style="font-weight: 600; color: var(--text-primary); font-size: 0.9rem; max-width: 300px;">
                                    {{ Str::limit($issue->question_text, 50) }}
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">ID: {{ $issue->question_id }}</div>
                            </td>
                            <td>
                                @if($issue->type === 'lehrgang')
                                    <span style="display: inline-block; background: rgba(59, 130, 246, 0.15); color: #3b82f6; padding: 0.25rem 0.6rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 600;">
                                        Lehrgang
                                    </span>
                                @else
                                    <span style="display: inline-block; background: rgba(168, 85, 247, 0.15); color: #a855f7; padding: 0.25rem 0.6rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 600;">
                                        Grundausbildung
                                    </span>
                                @endif
                                @if($issue->context)
                                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">{{ $issue->context }}</div>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span style="display: inline-block; background: rgba(59, 130, 246, 0.2); color: #3b82f6; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                                    {{ $issue->report_count }}x
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;
                                    @if($issue->status === 'open')
                                        background: rgba(239, 68, 68, 0.2); color: #ef4444;
                                    @elseif($issue->status === 'in_review')
                                        background: rgba(251, 191, 36, 0.2); color: #fbbf24;
                                    @elseif($issue->status === 'resolved')
                                        background: rgba(34, 197, 94, 0.2); color: #22c55e;
                                    @else
                                        background: rgba(255, 255, 255, 0.1); color: var(--text-secondary);
                                    @endif">
                                    @if($issue->status === 'open') Offen
                                    @elseif($issue->status === 'in_review') In Bearbeitung
                                    @elseif($issue->status === 'resolved') Gelöst
                                    @else Abgelehnt
                                    @endif
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('admin.issues.show', ['issue' => $issue->id, 'type' => $issue->type]) }}"
                                   class="btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem; display: inline-block;">
                                    Anschauen
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile: Cards --}}
        @foreach($issues as $issue)
            <a href="{{ route('admin.issues.show', ['issue' => $issue->id, 'type' => $issue->type]) }}" class="issue-card-mobile glass hover-lift" style="display: none; padding: 1rem 1.25rem; text-decoration: none; color: inherit;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div style="font-weight: 600; color: var(--text-primary); font-size: 0.9rem; flex: 1; line-height: 1.4;">
                        {{ Str::limit($issue->question_text, 60) }}
                    </div>
                    <span style="display: inline-block; padding: 0.25rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; white-space: nowrap;
                        @if($issue->status === 'open')
                            background: rgba(239, 68, 68, 0.2); color: #ef4444;
                        @elseif($issue->status === 'in_review')
                            background: rgba(251, 191, 36, 0.2); color: #fbbf24;
                        @elseif($issue->status === 'resolved')
                            background: rgba(34, 197, 94, 0.2); color: #22c55e;
                        @else
                            background: rgba(255, 255, 255, 0.1); color: var(--text-secondary);
                        @endif">
                        @if($issue->status === 'open') Offen
                        @elseif($issue->status === 'in_review') In Bearbeitung
                        @elseif($issue->status === 'resolved') Gelöst
                        @else Abgelehnt
                        @endif
                    </span>
                </div>
                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; font-size: 0.8rem;">
                    @if($issue->type === 'lehrgang')
                        <span style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; padding: 0.2rem 0.5rem; border-radius: 9999px; font-weight: 600;">Lehrgang</span>
                    @else
                        <span style="background: rgba(168, 85, 247, 0.15); color: #a855f7; padding: 0.2rem 0.5rem; border-radius: 9999px; font-weight: 600;">Grundausbildung</span>
                    @endif
                    <span style="color: var(--text-muted);">ID: {{ $issue->question_id }}</span>
                    <span style="background: rgba(59, 130, 246, 0.2); color: #3b82f6; padding: 0.2rem 0.5rem; border-radius: 9999px; font-weight: 600; margin-left: auto;">{{ $issue->report_count }}x</span>
                </div>
            </a>
        @endforeach
    @else
        <div class="glass hover-lift" style="padding: 3rem 1rem; text-align: center; color: var(--text-muted);">
            <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="bi bi-check-circle"></i></div>
            <p style="margin: 0;">Keine Fehlermeldungen in dieser Kategorie.</p>
        </div>
    @endif
</div>
@endsection
