@extends('layouts.app')
@section('title', 'Übungsmenü - THW Trainer')

@push('styles')
<style>
    * {
        box-sizing: border-box;
    }

    .practice-wrapper {
        min-height: 100vh;
        background: #f3f4f6;
        position: relative;
        overflow-x: hidden;
    }

    .practice-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        position: relative;
        z-index: 1;
    }

    .practice-header {
        text-align: center;
        margin-bottom: 2.5rem;
        padding-top: 1rem;
    }

    .practice-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        line-height: 1.2;
        display: inline-block;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .practice-subtitle {
        font-size: 1.1rem;
        color: #4b5563;
    }

    .section-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1.25rem;
        padding: 1.75rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #00337F;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-description {
        font-size: 0.95rem;
        color: #6b7280;
        margin-bottom: 1.25rem;
    }

    /* Search Form */
    .search-form {
        display: flex;
        gap: 1rem;
    }

    @media (max-width: 640px) {
        .search-form { flex-direction: column; }
    }

    .search-input {
        flex: 1;
        padding: 0.875rem 1.25rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        font-size: 1rem;
        transition: all 0.2s ease;
        outline: none;
    }

    .search-input:focus {
        border-color: #00337F;
        box-shadow: 0 0 0 3px rgba(0, 51, 127, 0.1);
    }

    .search-btn {
        padding: 0.875rem 1.75rem;
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
        font-weight: 600;
        border: none;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.2);
    }

    .search-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 51, 127, 0.3);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; }
    }

    .stat-item {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s ease;
    }

    .stat-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }

    .stat-item.failed { border-color: rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.05); }
    .stat-item.unsolved { border-color: rgba(59, 130, 246, 0.3); background: rgba(59, 130, 246, 0.05); }
    .stat-item.solved { border-color: rgba(34, 197, 94, 0.3); background: rgba(34, 197, 94, 0.05); }

    .stat-icon { font-size: 1.75rem; flex-shrink: 0; }
    .stat-content { flex: 1; min-width: 0; }
    .stat-value { font-size: 1.5rem; font-weight: 800; line-height: 1; margin-bottom: 0.25rem; }
    .stat-item.failed .stat-value { color: #dc2626; }
    .stat-item.unsolved .stat-value { color: #2563eb; }
    .stat-item.solved .stat-value { color: #16a34a; }
    .stat-label { font-size: 0.8rem; color: #6b7280; font-weight: 500; }

    .stat-progress {
        width: 100%;
        height: 4px;
        background: #e5e7eb;
        border-radius: 2px;
        margin-top: 0.5rem;
        overflow: hidden;
    }

    .stat-progress-fill { height: 100%; border-radius: 2px; transition: width 0.8s ease-out; }
    .stat-item.failed .stat-progress-fill { background: linear-gradient(90deg, #ef4444, #dc2626); }
    .stat-item.unsolved .stat-progress-fill { background: linear-gradient(90deg, #3b82f6, #2563eb); }
    .stat-item.solved .stat-progress-fill { background: linear-gradient(90deg, #22c55e, #16a34a); }

    .priority-hint {
        font-size: 0.9rem;
        color: #4b5563;
        margin-bottom: 1.25rem;
        padding: 0.75rem 1rem;
        background: rgba(0, 51, 127, 0.05);
        border-radius: 0.75rem;
        border: 1px solid rgba(0, 51, 127, 0.1);
    }

    .priority-hint strong { color: #00337F; }

    /* Start Training Button */
    .start-training-btn {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        border-radius: 1rem;
        text-decoration: none;
        color: #1e40af;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
        max-width: 450px;
        margin: 0 auto;
    }

    .start-training-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(251, 191, 36, 0.4);
    }

    .start-training-icon { font-size: 2rem; }
    .start-training-content { flex: 1; }
    .start-training-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem; }
    .start-training-subtitle { font-size: 0.85rem; opacity: 0.9; }

    /* Section Grid */
    .sections-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    @media (max-width: 768px) {
        .sections-grid { grid-template-columns: 1fr; }
    }

    .section-link {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .section-link:hover {
        background: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        border-color: #00337F;
    }

    .section-number {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
        font-size: 1.25rem;
        font-weight: 800;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .section-info { flex: 1; min-width: 0; }
    .section-name { font-size: 0.95rem; font-weight: 600; color: #1f2937; margin-bottom: 0.25rem; line-height: 1.3; }
    .section-stats { font-size: 0.8rem; color: #6b7280; margin-bottom: 0.5rem; }

    .section-progress {
        width: 100%;
        height: 4px;
        background: #e5e7eb;
        border-radius: 2px;
        overflow: hidden;
    }

    .section-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        border-radius: 2px;
        transition: width 1s ease-out;
    }

    .section-percent {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    /* Back Button */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        color: #4b5563;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .back-link:hover {
        background: #f9fafb;
        border-color: #00337F;
        color: #00337F;
    }

    .text-center { text-align: center; }
    .mt-8 { margin-top: 2rem; }

    @media (max-width: 640px) {
        .practice-container { padding: 1rem; }
        .practice-title { font-size: 1.75rem; }
        .section-card { padding: 1.25rem; }
    }
</style>
@endpush

@section('content')
<div class="practice-wrapper">
    <div class="practice-container">
        <header class="practice-header">
            <h1 class="practice-title">Übungsmenü</h1>
            <p class="practice-subtitle">Lernmodus auswählen</p>
        </header>

        <!-- Suchfeld -->
        <div class="section-card">
            <h2 class="section-title">Fragen suchen</h2>
            <form action="{{ route('practice.search') }}" method="GET" class="search-form">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Suchbegriff eingeben..." 
                       class="search-input">
                <button type="submit" class="search-btn">Suchen</button>
            </form>
        </div>

        <!-- Alle Fragen Modus -->
        <div class="section-card">
            <h2 class="section-title">Alle Fragen</h2>

            <!-- Statistiken -->
            <div class="stats-grid">
                <div class="stat-item failed">
                    <div class="stat-icon"><i class="bi bi-x-circle text-red-500"></i></div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $failedCount }}</div>
                        <div class="stat-label">Fehlgeschlagen</div>
                        @php $failedProgressPercent = $totalQuestions > 0 ? ($failedCount / $totalQuestions) * 100 : 0; @endphp
                        <div class="stat-progress"><div class="stat-progress-fill" style="width: {{ $failedProgressPercent }}%"></div></div>
                    </div>
                </div>

                <div class="stat-item unsolved">
                    <div class="stat-icon"><i class="bi bi-question-circle text-yellow-500"></i></div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $unsolvedCount }}</div>
                        <div class="stat-label">Ungelöst</div>
                        @php $unsolvedProgressPercent = $totalQuestions > 0 ? ($unsolvedCount / $totalQuestions) * 100 : 0; @endphp
                        <div class="stat-progress"><div class="stat-progress-fill" style="width: {{ $unsolvedProgressPercent }}%"></div></div>
                    </div>
                </div>

                <div class="stat-item solved">
                    <div class="stat-icon"><i class="bi bi-check-circle text-green-500"></i></div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $solvedCount }}</div>
                        <div class="stat-label">Gemeistert</div>
                        <div class="stat-progress"><div class="stat-progress-fill" style="width: {{ $progressPercentage }}%"></div></div>
                    </div>
                </div>
            </div>
            
            @if($failedCount > 0 || $unsolvedCount > 0)
            <div class="priority-hint">
                <strong>Intelligente Priorisierung:</strong> 
                @if($failedCount > 0)
                    Zuerst werden {{ $failedCount }} fehlgeschlagene Fragen geübt{{ $unsolvedCount > 0 ? ',' : '.' }}
                @endif
                @if($unsolvedCount > 0)
                    {{ $failedCount > 0 ? 'dann' : 'Zuerst werden' }} {{ $unsolvedCount }} ungelöste Fragen.
                @endif
            </div>
            @else
            <div class="priority-hint">
                <strong>Alle Fragen gemeistert!</strong> Jetzt kannst du alle Fragen in zufälliger Reihenfolge wiederholen.
            </div>
            @endif
            
            <a href="{{ route('practice.all') }}" class="start-training-btn">
                <div class="start-training-icon"><i class="bi bi-bullseye"></i></div>
                <div class="start-training-content">
                    <div class="start-training-title">
                        @if($failedCount > 0 || $unsolvedCount > 0)
                            Training starten
                        @else
                            Alle Fragen wiederholen
                        @endif
                    </div>
                    <div class="start-training-subtitle">
                        @if($failedCount > 0)
                            Schwierige Fragen zuerst
                        @elseif($unsolvedCount > 0)
                            Ungelöste Fragen zuerst
                        @else
                            Zufällige Reihenfolge
                        @endif
                    </div>
                </div>
            </a>
        </div>

        <!-- Lernabschnitte -->
        <div class="section-card">
            <h2 class="section-title">Lernabschnitte</h2>
            <p class="section-description">Übe gezielt nach Themengebieten strukturiert.</p>
            
            <div class="sections-grid">
                @foreach(range(1, 10) as $section)
                    @php
                        $sectionTotal = $sectionStats[$section]['total'] ?? 0;
                        $sectionSolved = $sectionStats[$section]['solved'] ?? 0;
                        $sectionPercent = $sectionTotal > 0 ? round(($sectionSolved / $sectionTotal) * 100) : 0;
                        $sectionName = $sectionNames[$section] ?? "Abschnitt $section";
                    @endphp
                    
                    <a href="{{ route('practice.section', $section) }}" class="section-link">
                        <div class="section-number">{{ $section }}</div>
                        <div class="section-info">
                            <div class="section-name">{{ $sectionName }}</div>
                            <div class="section-stats">{{ $sectionSolved }}/{{ $sectionTotal }} Fragen</div>
                            <div class="section-progress">
                                <div class="section-progress-fill" id="progressBar{{ $section }}" style="width: 0%"></div>
                            </div>
                            <div class="section-percent">{{ $sectionPercent }}%</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Zurück zum Dashboard -->
        <div class="text-center mt-8">
            <a href="{{ route('dashboard') }}" class="back-link">
                ← Zurück zum Dashboard
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @foreach(range(1, 10) as $section)
        @php
            $sectionTotal = $sectionStats[$section]['total'] ?? 0;
            $sectionSolved = $sectionStats[$section]['solved'] ?? 0;
            $sectionPercent = $sectionTotal > 0 ? round(($sectionSolved / $sectionTotal) * 100) : 0;
        @endphp
        
        setTimeout(() => {
            const bar{{ $section }} = document.getElementById('progressBar{{ $section }}');
            if (bar{{ $section }}) {
                bar{{ $section }}.style.transition = 'width 0.8s ease-out';
                bar{{ $section }}.style.width = '{{ $sectionPercent }}%';
            }
        }, 200 + ({{ $section }} * 80));
    @endforeach
});
</script>
@endsection
