@extends('layouts.app')

@section('title', $ortsverband->name . ' - Dashboard')

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
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 900px) {
        .stats-grid { grid-template-columns: repeat(3, 1fr); }
    }

    @media (max-width: 600px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
        .dashboard-greeting { font-size: 1.75rem; }
    }

    .stat-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.25rem;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .stat-icon { font-size: 2rem; margin-bottom: 0.5rem; }
    .stat-value { font-size: 1.75rem; font-weight: 800; color: #00337F; line-height: 1; margin-bottom: 0.25rem; }
    .stat-label { font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }

    .section-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .member-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 0.75rem;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }

    .member-item:hover {
        background: #f3f4f6;
        transform: translateX(4px);
    }

    .member-rank {
        font-size: 1.5rem;
        min-width: 50px;
        text-align: center;
    }

    .member-info {
        flex: 1;
        margin-left: 1rem;
    }

    .member-name {
        font-weight: 700;
        color: #00337F;
        margin-bottom: 0.25rem;
    }

    .member-stats {
        display: flex;
        gap: 1rem;
        font-size: 0.85rem;
        color: #6b7280;
    }

    .progress-bar {
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #00337F, #0047b3);
        transition: width 0.5s ease;
    }

    .badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-left: 0.5rem;
    }

    .badge-primary {
        background: rgba(0, 51, 127, 0.1);
        color: #00337F;
    }

    .weakness-item {
        padding: 1rem;
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        border-radius: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .error-item {
        padding: 1rem;
        background: #fee2e2;
        border-left: 4px solid #ef4444;
        border-radius: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .quick-action {
        background: white;
        padding: 1.5rem;
        border-radius: 1rem;
        text-decoration: none;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .quick-action:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .quick-action-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .quick-action-label {
        font-weight: 700;
        color: #00337F;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 1rem;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.3);
        color: #166534;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #6b7280;
    }

    @media (max-width: 480px) {
        .dashboard-container { padding: 1rem; }
        .section-card { padding: 1.5rem; }
        .member-stats { flex-wrap: wrap; gap: 0.5rem; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-greeting">📊 <span>{{ $ortsverband->name }}</span></h1>
            <p class="dashboard-subtitle">Ausbildungsbeauftragter Dashboard</p>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            ✓ {{ session('success') }}
        </div>
        @endif

        {{-- Statistiken --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value">{{ $stats['total_members'] ?? 0 }}</div>
                <div class="stat-label">Mitglieder</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-value">{{ $stats['active_members'] ?? 0 }}</div>
                <div class="stat-label">Aktiv (7 Tage)</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-value">{{ $stats['avg_theory'] ?? 0 }}%</div>
                <div class="stat-label">Ø Theorie</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🎯</div>
                <div class="stat-value">{{ $stats['avg_exams'] ?? 0 }}</div>
                <div class="stat-label">Ø Prüfungen</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🔥</div>
                <div class="stat-value">{{ $stats['avg_streak'] ?? 0 }}</div>
                <div class="stat-label">Ø Streak</div>
            </div>
        </div>

        {{-- Rangliste --}}
        <div class="section-card">
            <h2 class="section-title">🏆 Rangliste</h2>
            
            @forelse($memberProgress->take(10) as $index => $member)
            <div class="member-item">
                <div class="member-rank">
                    @if($index === 0) 🥇
                    @elseif($index === 1) 🥈
                    @elseif($index === 2) 🥉
                    @else <span style="color: #9ca3af; font-weight: 700;">{{ $index + 1 }}</span>
                    @endif
                </div>
                <div class="member-info">
                    <div class="member-name">
                        {{ $member['user']->name }}
                        @if($member['role'] === 'ausbildungsbeauftragter')
                            <span class="badge badge-primary">👨‍🏫</span>
                        @endif
                    </div>
                    <div class="member-stats">
                        <span>📚 {{ $member['theory_progress_percent'] }}%</span>
                        <span>🎯 {{ $member['exams_passed'] }}/5</span>
                        <span>🔥 {{ $member['streak'] }} Tage</span>
                        <span>⚡ Lvl {{ $member['level'] }}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $member['theory_progress_percent'] }}%"></div>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                Noch keine Mitglieder vorhanden.
            </div>
            @endforelse
        </div>

        {{-- Schwachstellen-Analyse --}}
        <div class="section-card">
            <h2 class="section-title">📉 Schwachstellen-Analyse</h2>
            
            @if($weaknesses['weak_sections']->isNotEmpty())
            <h3 style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem;">
                Schwierigste Lernabschnitte
            </h3>
            @foreach($weaknesses['weak_sections'] as $section)
                @php
                    $sectionNames = [
                        1 => 'Das THW im Gefüge des Zivil- und Katastrophenschutzes',
                        2 => 'Grundlagen Rettung und Bergung',
                        3 => 'Arbeiten mit Leinen, Rundschlingen, Ketten und Seilen',
                        4 => 'Holz-, Gesteins- und Metallbearbeitung',
                        5 => 'Umgang mit Leitern',
                        6 => 'Stromerzeugung und Beleuchtung',
                        7 => 'Arbeiten am und auf dem Wasser',
                        8 => 'Einsatzgrundlagen',
                        9 => 'Arbeiten im und am Wasser',
                        10 => 'Grundlagen der Rettung und Bergung'
                    ];
                @endphp
                <div class="weakness-item">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>LA {{ $section['section'] }}: {{ $sectionNames[$section['section']] ?? 'Unbekannt' }}</strong>
                            <div style="font-size: 0.85rem; color: #92400e; margin-top: 0.25rem;">
                                {{ $section['total_attempts'] }} Versuche
                            </div>
                        </div>
                        <div style="font-size: 1.25rem; font-weight: 800; color: #d97706;">
                            {{ $section['success_rate'] }}%
                        </div>
                    </div>
                </div>
            @endforeach
            @endif

            @if($weaknesses['common_errors']->isNotEmpty())
            <h3 style="font-size: 1rem; font-weight: 600; color: #374151; margin: 1.5rem 0 1rem;">
                Häufigste Fehler
            </h3>
            @foreach($weaknesses['common_errors']->take(5) as $error)
                @if($error['question'])
                <div class="error-item">
                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 1rem;">
                        <div style="flex: 1;">
                            <strong>Frage #{{ $error['question']->id }}</strong>
                            <p style="margin: 0.25rem 0 0; color: #7f1d1d; font-size: 0.9rem;">
                                {{ Str::limit($error['question']->frage, 120) }}
                            </p>
                        </div>
                        <div style="font-size: 1.25rem; font-weight: 800; color: #dc2626; white-space: nowrap;">
                            {{ $error['error_count'] }}×
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
            @endif

            @if($weaknesses['weak_sections']->isEmpty() && $weaknesses['common_errors']->isEmpty())
            <div class="empty-state">
                Noch keine Daten verfügbar. Deine Mitglieder müssen erst Fragen beantworten.
            </div>
            @endif
        </div>

        {{-- Schnellzugriff --}}
        <div class="quick-actions">
            <a href="{{ route('ortsverband.members', $ortsverband) }}" class="quick-action">
                <div class="quick-action-icon">👥</div>
                <div class="quick-action-label">Mitglieder</div>
            </a>
            <a href="{{ route('ortsverband.invitations.index', $ortsverband) }}" class="quick-action">
                <div class="quick-action-icon">🔗</div>
                <div class="quick-action-label">Einladungen</div>
            </a>
            <a href="{{ route('ortsverband.edit', $ortsverband) }}" class="quick-action">
                <div class="quick-action-icon">⚙️</div>
                <div class="quick-action-label">Einstellungen</div>
            </a>
            <a href="{{ route('ortsverband.index') }}" class="quick-action">
                <div class="quick-action-icon">←</div>
                <div class="quick-action-label">Zurück</div>
            </a>
        </div>
    </div>
</div>
@endsection
