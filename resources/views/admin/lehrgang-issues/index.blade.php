@extends('layouts.app')
@section('title', 'Lehrgang Fehlermeldungen - Admin')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Fehlermeldungen <span>Übersicht</span></h1>
        <p class="page-subtitle">Verwalte Fehlermeldungen von Fragen</p>
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
        <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 1rem 0;">Filter nach Status</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
            <a href="{{ route('admin.lehrgang-issues.index', ['status' => 'all']) }}"
               class="btn-{{ $status === 'all' ? 'primary' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                Alle
            </a>
            <a href="{{ route('admin.lehrgang-issues.index', ['status' => 'open']) }}"
               class="btn-{{ $status === 'open' ? 'primary' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                Offen
            </a>
            <a href="{{ route('admin.lehrgang-issues.index', ['status' => 'in_review']) }}"
               class="btn-{{ $status === 'in_review' ? 'primary' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                In Bearbeitung
            </a>
            <a href="{{ route('admin.lehrgang-issues.index', ['status' => 'resolved']) }}"
               class="btn-{{ $status === 'resolved' ? 'primary' : 'secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                Gelöst
            </a>
        </div>
    </div>

    <div class="glass hover-lift" style="padding: 1.5rem; overflow-x: auto;">
        @if($issues->count() > 0)
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid rgba(255, 255, 255, 0.1);">
                        <th style="color: var(--text-secondary); padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.875rem;">Frage</th>
                        <th style="color: var(--text-secondary); padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.875rem;">Lehrgang</th>
                        <th style="color: var(--text-secondary); padding: 0.75rem 1rem; text-align: center; font-weight: 600; font-size: 0.875rem;">Meldungen</th>
                        <th style="color: var(--text-secondary); padding: 0.75rem 1rem; text-align: center; font-weight: 600; font-size: 0.875rem;">Status</th>
                        <th style="color: var(--text-secondary); padding: 0.75rem 1rem; text-align: right; font-weight: 600; font-size: 0.875rem;">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($issues as $issue)
                        <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.06); transition: all 0.2s;"
                            onmouseover="this.style.background='rgba(255, 255, 255, 0.03)'"
                            onmouseout="this.style.background='transparent'">
                            <td style="padding: 0.875rem 1rem;">
                                <div style="font-weight: 600; color: var(--text-primary); font-size: 0.9rem; max-width: 300px;">
                                    {{ Str::limit($issue->lehrgangQuestion->frage, 50) }}
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">ID: {{ $issue->lehrgangQuestion->id }}</div>
                            </td>
                            <td style="padding: 0.875rem 1rem; color: var(--text-secondary); font-size: 0.9rem;">
                                {{ $issue->lehrgangQuestion->lehrgang->lehrgang }}
                            </td>
                            <td style="padding: 0.875rem 1rem; text-align: center;">
                                <span style="display: inline-block; background: rgba(59, 130, 246, 0.2); color: #3b82f6; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                                    {{ $issue->report_count }}x
                                </span>
                            </td>
                            <td style="padding: 0.875rem 1rem; text-align: center;">
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
                                    {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                                </span>
                            </td>
                            <td style="padding: 0.875rem 1rem; text-align: right;">
                                <a href="{{ route('admin.lehrgang-issues.show', $issue) }}"
                                   class="btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem; display: inline-block;">
                                    <i class="bi bi-arrow-right"></i> Anschauen
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 2rem;">
                {{ $issues->links() }}
            </div>
        @else
            <div style="padding: 3rem 1rem; text-align: center; color: var(--text-muted);">
                <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="bi bi-check-circle"></i></div>
                <p style="margin: 0;">Keine Fehlermeldungen in dieser Kategorie.</p>
            </div>
        @endif
    </div>
</div>
@endsection
