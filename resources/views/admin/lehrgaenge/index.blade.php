@extends('layouts.app')

@section('title', 'Lehrgänge Verwalten')

@push('styles')
<style>
    * {
        box-sizing: border-box;
    }

    .dashboard-wrapper {
        min-height: 100vh;
        background: #f3f4f6;
        position: relative;
        overflow-x: hidden;
    }

    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        position: relative;
        z-index: 1;
    }

    .dashboard-header {
        text-align: center;
        margin-bottom: 3rem;
        padding-top: 1rem;
    }

    .dashboard-greeting {
        font-size: 2.5rem;
        font-weight: 800;
        color: #00337F;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .dashboard-greeting span {
        display: inline-block;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dashboard-subtitle {
        font-size: 1.1rem;
        color: #4b5563;
        margin-bottom: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 1.25rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
    }

    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
    }

    .stat-value {
        font-size: 2.25rem;
        font-weight: 800;
        color: #00337F;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #6b7280;
    }

    .info-card {
        background: white;
        padding: 2rem;
        border-radius: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
    }

    .info-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #00337F;
        margin: 0 0 1.5rem 0;
    }

    .button-group {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #00337F;
        border: 1px solid #e5e7eb;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .lehrgang-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    .lehrgang-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1.25rem;
        padding: 1.5rem;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
    }

    .lehrgang-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 40px rgba(15, 23, 42, 0.12);
        border-color: #cbd5f5;
    }

    .lehrgang-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .lehrgang-card-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: #00337F;
        flex: 1;
    }

    .lehrgang-card-desc {
        color: #4b5563;
        font-size: 0.95rem;
        margin: 0 0 1rem 0;
        line-height: 1.5;
    }

    .lehrgang-card-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin: 1.25rem 0;
        padding: 1rem 0;
        border-top: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
    }

    .lehrgang-stat {
        text-align: center;
    }

    .lehrgang-stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #00337F;
        margin-bottom: 0.25rem;
    }

    .lehrgang-stat-label {
        font-size: 0.8rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .lehrgang-card-actions {
        margin-top: auto;
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }

    .action-link {
        font-size: 0.9rem;
        font-weight: 600;
        color: #2563eb;
        text-decoration: none;
        transition: color 0.2s;
        padding: 0.35rem 0.75rem;
        border-radius: 0.5rem;
    }

    .action-link:hover {
        color: #1e40af;
        background: rgba(37, 99, 235, 0.08);
    }

    .action-link-danger {
        color: #dc2626;
    }

    .action-link-danger:hover {
        background: rgba(220, 38, 38, 0.08);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6b7280;
    }

    .empty-state p {
        font-size: 1.05rem;
        margin-bottom: 1.5rem;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    @media (max-width: 480px) {
        .dashboard-container { padding: 1rem; }
        .info-card { padding: 1.25rem; }
        .dashboard-greeting { font-size: 1.75rem; }
        .lehrgang-grid { grid-template-columns: 1fr; }
        .lehrgang-card-stats { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-greeting"><span>Lehrgänge</span></h1>
            <p class="dashboard-subtitle">Verwaltung aller THW-Lehrgänge</p>
        </div>

        <!-- Schnellstatistiken -->
        @php
            $totalQuestions = $lehrgaenge->sum('questions_count');
            $totalUsers = $lehrgaenge->sum(fn($l) => $l->users_count ?? 0);
            $avgQuestions = $lehrgaenge->count() ? round($lehrgaenge->avg('questions_count')) : 0;
        @endphp

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-book"></i></div>
                <div class="stat-value">{{ $lehrgaenge->total() }}</div>
                <div class="stat-label">Lehrgänge</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-question-circle"></i></div>
                <div class="stat-value">{{ $totalQuestions }}</div>
                <div class="stat-label">Fragen</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-people"></i></div>
                <div class="stat-value">{{ $totalUsers }}</div>
                <div class="stat-label">Teilnehmer</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-bar-chart"></i></div>
                <div class="stat-value">{{ $avgQuestions }}</div>
                <div class="stat-label">Ø Fragen/Lehrgang</div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <i class="bi bi-x-circle"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Lehrgänge -->
        <div class="info-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 class="info-title" style="margin: 0;">Alle Lehrgänge</h2>
                <span style="color: #6b7280; font-weight: 600;">{{ $lehrgaenge->total() }} Lehrgänge</span>
            </div>

            <div class="button-group">
                <a href="{{ route('admin.lehrgaenge.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Neuer Lehrgang
                </a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Zurück zum Dashboard
                </a>
            </div>

            @if($lehrgaenge->count() > 0)
                <div class="lehrgang-grid">
                    @foreach($lehrgaenge as $lehrgang)
                        <div class="lehrgang-card">
                            <div class="lehrgang-card-header">
                                <h3>{{ $lehrgang->lehrgang }}</h3>
                            </div>

                            <p class="lehrgang-card-desc">{{ Str::limit($lehrgang->beschreibung, 120) }}</p>

                            <div class="lehrgang-card-stats">
                                <div class="lehrgang-stat">
                                    <div class="lehrgang-stat-value">{{ $lehrgang->questions_count }}</div>
                                    <div class="lehrgang-stat-label">Fragen</div>
                                </div>
                                <div class="lehrgang-stat">
                                    <div class="lehrgang-stat-value">{{ $lehrgang->users_count ?? 0 }}</div>
                                    <div class="lehrgang-stat-label">Teilnehmer</div>
                                </div>
                            </div>

                            <div class="lehrgang-card-actions">
                                <a href="{{ url('admin/lehrgaenge/' . $lehrgang->id) }}" class="action-link">
                                    <i class="bi bi-eye"></i> Details
                                </a>
                                <a href="{{ url('admin/lehrgaenge/' . $lehrgang->id . '/edit') }}" class="action-link">
                                    <i class="bi bi-pencil"></i> Bearbeiten
                                </a>
                                <form action="{{ url('admin/lehrgaenge/' . $lehrgang->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Wirklich löschen? Alle Fragen werden gelöscht!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-link action-link-danger" style="background: none; border: none; padding: 0.35rem 0.75rem; cursor: pointer;">
                                        <i class="bi bi-trash"></i> Löschen
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div style="margin-top: 2rem;">
                    {{ $lehrgaenge->links() }}
                </div>
            @else
                <div class="empty-state">
                    <p><i class="bi bi-inbox"></i> Noch keine Lehrgänge vorhanden</p>
                    <a href="{{ route('admin.lehrgaenge.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Neuen Lehrgang erstellen
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
