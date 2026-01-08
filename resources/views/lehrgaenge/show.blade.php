@extends('layouts.app')

@section('title', $lehrgang->lehrgang)

@push('styles')
<style>
    * { box-sizing: border-box; }

    .lehrgang-wrapper {
        min-height: 100vh;
        background: #f3f4f6;
        position: relative;
        overflow-x: hidden;
    }

    .lehrgang-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem;
        position: relative;
        z-index: 1;
    }

    .lehrgang-hero {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1.25rem;
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .lehrgang-hero-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }

    .lehrgang-title {
        font-size: 2rem;
        font-weight: 800;
        color: #00337F;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .lehrgang-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.4rem 0.85rem;
        border-radius: 2rem;
        flex-shrink: 0;
    }

    .lehrgang-badge.enrolled {
        background: rgba(34, 197, 94, 0.15);
        color: #16a34a;
    }

    .lehrgang-description {
        font-size: 1rem;
        color: #6b7280;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .lehrgang-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        padding: 1.5rem 0;
        border-top: 1px solid #e5e7eb;
    }

    @media (max-width: 640px) {
        .lehrgang-stats-grid { grid-template-columns: 1fr; gap: 1rem; }
    }

    .stat-card {
        text-align: center;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 0.75rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: #00337F;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    .progress-section {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1.25rem;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .progress-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
    }

    .progress-percentage {
        font-size: 1.5rem;
        font-weight: 800;
        color: #00337F;
    }

    .progress-bar-wrapper {
        width: 100%;
        height: 12px;
        background: #e5e7eb;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        border-radius: 6px;
        transition: width 0.5s ease-out;
    }

    .progress-bar-fill.complete {
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    .progress-details {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        color: #6b7280;
    }

    .sections-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #00337F;
        margin-bottom: 1.25rem;
    }

    .sections-grid {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .section-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .section-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .section-info {
        flex: 1;
    }

    .section-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .section-meta {
        display: flex;
        gap: 1.5rem;
        font-size: 0.85rem;
        color: #6b7280;
    }

    .section-progress {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .section-progress-bar {
        width: 100px;
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
    }

    .section-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        border-radius: 3px;
    }

    .section-progress-fill.complete {
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    .section-progress-text {
        font-size: 0.85rem;
        font-weight: 600;
        color: #4b5563;
        min-width: 60px;
        text-align: right;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    @media (min-width: 640px) {
        .action-buttons {
            flex-direction: row;
        }
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 1rem 2rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 1rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        flex: 1;
    }

    .action-btn.primary {
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.2);
    }

    .action-btn.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 51, 127, 0.3);
    }

    .action-btn.secondary {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #1e40af;
        box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
    }

    .action-btn.secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(251, 191, 36, 0.4);
    }

    .action-btn.success {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
    }

    .action-btn.outline {
        background: white;
        border: 2px solid #e5e7eb;
        color: #4b5563;
    }

    .action-btn.outline:hover {
        border-color: #00337F;
        color: #00337F;
    }

    .enroll-section {
        background: white;
        border: 2px dashed #e5e7eb;
        border-radius: 1.25rem;
        padding: 3rem;
        text-align: center;
        margin-bottom: 2rem;
    }

    .enroll-icon { font-size: 3rem; margin-bottom: 1rem; }
    .enroll-title { font-size: 1.5rem; font-weight: 700; color: #00337F; margin-bottom: 0.5rem; }
    .enroll-description { font-size: 1rem; color: #6b7280; margin-bottom: 1.5rem; max-width: 500px; margin-left: auto; margin-right: auto; line-height: 1.6; }

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
    .mt-6 { margin-top: 1.5rem; }

    @media (max-width: 640px) {
        .lehrgang-container { padding: 1rem; }
        .lehrgang-title { font-size: 1.5rem; }
        .lehrgang-hero { padding: 1.5rem; }
        .section-card { flex-direction: column; gap: 1rem; align-items: flex-start; }
        .section-progress { width: 100%; }
        .section-progress-bar { flex: 1; }
    }
</style>
@endpush

@section('content')
<div class="lehrgang-wrapper">
    <div class="lehrgang-container">
        @php
            $isEnrolled = in_array($lehrgang->id, $enrolledIds ?? []);
            $solvedCount = 0;
            $totalCount = 0;
            $progressPercent = 0;
            $isCompleted = false;
            
            if ($isEnrolled) {
                $solvedCount = \App\Models\UserLehrgangProgress::where('user_id', auth()->id())
                    ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))
                    ->where('solved', true)
                    ->count();
                $totalCount = \App\Models\LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->count();
                
                $progressData = \App\Models\UserLehrgangProgress::where('user_id', auth()->id())
                    ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))
                    ->get();
                
                $totalProgressPoints = 0;
                foreach ($progressData as $prog) {
                    $totalProgressPoints += min($prog->consecutive_correct, 2);
                }
                $maxProgressPoints = $totalCount * 2;
                $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
                $isCompleted = $progressPercent == 100 && $solvedCount > 0;
            }
            
            $questionCount = $lehrgang->questions()->count();
            $sectionCount = $lehrgang->questions()->distinct('lernabschnitt')->count('lernabschnitt');
            $sections = $lehrgang->questions()->select('lernabschnitt')->distinct()->orderBy('lernabschnitt')->get();
        @endphp

        <!-- Hero Section -->
        <div class="lehrgang-hero">
            <div class="lehrgang-hero-header">
                <div>
                    <h1 class="lehrgang-title">🎓 {{ $lehrgang->lehrgang }}</h1>
                </div>
                @if($isEnrolled)
                    <span class="lehrgang-badge enrolled">✓ Eingeschrieben</span>
                @endif
            </div>
            
            <p class="lehrgang-description">{{ $lehrgang->beschreibung }}</p>
            
            <div class="lehrgang-stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $questionCount }}</div>
                    <div class="stat-label">Fragen</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $sectionCount }}</div>
                    <div class="stat-label">Abschnitte</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $isEnrolled ? $progressPercent : 0 }}%</div>
                    <div class="stat-label">Fortschritt</div>
                </div>
            </div>
        </div>

        @if($isEnrolled)
            <!-- Progress Section -->
            <div class="progress-section">
                <div class="progress-header">
                    <h2 class="progress-title">📊 Dein Fortschritt</h2>
                    <span class="progress-percentage">{{ $progressPercent }}%</span>
                </div>
                <div class="progress-bar-wrapper">
                    <div class="progress-bar-fill {{ $isCompleted ? 'complete' : '' }}" style="width: {{ $progressPercent }}%"></div>
                </div>
                <div class="progress-details">
                    <span>{{ $solvedCount }}/{{ $totalCount }} Fragen beantwortet</span>
                    @if($isCompleted)
                        <span style="color: #16a34a; font-weight: 600;">✓ Abgeschlossen!</span>
                    @endif
                </div>
            </div>

            <!-- Sections -->
            <h2 class="sections-title">📚 Lernabschnitte</h2>
            
            @php
                // Lade Lernabschnitt-Namen direkt aus der DB (Cache-Bust Workaround)
                $lernabschnittNamen = \App\Models\LehrgangLernabschnitt::where('lehrgang_id', $lehrgang->id)
                    ->pluck('lernabschnitt', 'lernabschnitt_nr')
                    ->toArray();
            @endphp
            
            <div class="sections-grid">
                @foreach($sections as $section)
                    @php
                        // Hole die Nummer (kompatibel mit altem und neuem Code)
                        $sectionNr = $section->lernabschnitt_nr ?? $section->lernabschnitt ?? null;
                        
                        // Suche den Namen in der DB-Map
                        $sectionName = $lernabschnittNamen[(int)$sectionNr] ?? $lernabschnittNamen[$sectionNr] ?? "Lernabschnitt {$sectionNr}";
                        
                        $sectionQuestionCount = $lehrgang->questions()->where('lernabschnitt', $sectionNr)->count();
                        $sectionSolvedCount = \App\Models\UserLehrgangProgress::where('user_id', auth()->id())
                            ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id)->where('lernabschnitt', $sectionNr))
                            ->where('solved', true)
                            ->count();
                        $sectionProgress = $sectionQuestionCount > 0 ? round(($sectionSolvedCount / $sectionQuestionCount) * 100) : 0;
                        $sectionComplete = $sectionProgress == 100 && $sectionSolvedCount > 0;
                    @endphp
                    <div class="section-card">
                        <div class="section-info">
                            <h3 class="section-name">{{ $sectionName }}</h3>
                            <div class="section-meta">
                                <span>{{ $sectionQuestionCount }} Fragen</span>
                                <span>{{ $sectionSolvedCount }} gelöst</span>
                            </div>
                        </div>
                        <div class="section-progress">
                            <div class="section-progress-bar">
                                <div class="section-progress-fill {{ $sectionComplete ? 'complete' : '' }}" style="width: {{ $sectionProgress }}%"></div>
                            </div>
                            <span class="section-progress-text">{{ $sectionProgress }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                @if($isCompleted)
                    <span class="action-btn success">✓ Lehrgang abgeschlossen</span>
                @else
                    <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="action-btn secondary">
                        📚 Jetzt üben
                    </a>
                @endif
                <a href="{{ route('lehrgaenge.index') }}" class="action-btn outline">
                    ← Alle Lehrgänge
                </a>
            </div>
        @else
            <!-- Enroll Section -->
            <div class="enroll-section">
                <div class="enroll-icon">🚀</div>
                <h2 class="enroll-title">Bereit für diesen Lehrgang?</h2>
                <p class="enroll-description">
                    Schreibe dich jetzt ein und beginne mit dem Lernen. Du wirst Zugang zu allen {{ $questionCount }} Fragen erhalten und deinen Fortschritt verfolgen können.
                </p>
                <form action="{{ route('lehrgaenge.enroll', $lehrgang->slug) }}" method="POST" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="action-btn secondary" style="min-width: 200px;">
                        ✨ Jetzt beitreten
                    </button>
                </form>
            </div>

            <!-- Preview Sections -->
            <h2 class="sections-title">📚 Lernabschnitte (Vorschau)</h2>
            
            @php
                // Lade Lernabschnitt-Namen direkt aus der DB (Cache-Bust Workaround)
                $lernabschnittNamen = \App\Models\LehrgangLernabschnitt::where('lehrgang_id', $lehrgang->id)
                    ->pluck('lernabschnitt', 'lernabschnitt_nr')
                    ->toArray();
            @endphp
            
            <div class="sections-grid">
                @foreach($sections as $section)
                    @php
                        // Hole die Nummer (kompatibel mit altem und neuem Code)
                        $sectionNr = $section->lernabschnitt_nr ?? $section->lernabschnitt ?? null;
                        
                        // Suche den Namen in der DB-Map
                        $sectionName = $lernabschnittNamen[(int)$sectionNr] ?? $lernabschnittNamen[$sectionNr] ?? "Lernabschnitt {$sectionNr}";
                        
                        $sectionQuestionCount = $lehrgang->questions()->where('lernabschnitt', $sectionNr)->count();
                    @endphp
                    <div class="section-card">
                        <div class="section-info">
                            <h3 class="section-name">{{ $sectionName }}</h3>
                            <div class="section-meta">
                                <span>{{ $sectionQuestionCount }} Fragen</span>
                            </div>
                        </div>
                        <div class="section-progress">
                            <div class="section-progress-bar">
                                <div class="section-progress-fill" style="width: 0%"></div>
                            </div>
                            <span class="section-progress-text">0%</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-6">
                <a href="{{ route('lehrgaenge.index') }}" class="back-link">
                    ← Zurück zur Übersicht
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
