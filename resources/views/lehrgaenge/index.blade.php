@extends('layouts.app')

@section('title', 'Lehrgänge')

@push('styles')
<style>
    * { box-sizing: border-box; }

    .lehrgaenge-wrapper {
        min-height: 100vh;
        background: #f3f4f6;
        position: relative;
        overflow-x: hidden;
    }

    .lehrgaenge-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        position: relative;
        z-index: 1;
    }

    .lehrgaenge-header {
        text-align: center;
        margin-bottom: 2.5rem;
        padding-top: 1rem;
    }

    .lehrgaenge-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #00337F;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .lehrgaenge-title span {
        display: inline-block;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .lehrgaenge-subtitle {
        font-size: 1.1rem;
        color: #4b5563;
    }

    .lehrgaenge-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .lehrgaenge-grid { grid-template-columns: 1fr; }
    }

    .lehrgang-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1.25rem;
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .lehrgang-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }

    .lehrgang-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .lehrgang-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1.3;
        flex: 1;
        padding-right: 1rem;
    }

    .lehrgang-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        flex-shrink: 0;
    }

    .lehrgang-badge.enrolled {
        background: rgba(34, 197, 94, 0.15);
        color: #16a34a;
    }

    .lehrgang-description {
        font-size: 0.9rem;
        color: #6b7280;
        margin-bottom: 1.25rem;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .lehrgang-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        padding: 1rem 0;
        border-top: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 1.25rem;
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: #00337F;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .lehrgang-progress {
        margin-bottom: 1.25rem;
    }

    .lehrgang-progress-header {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }

    .lehrgang-progress-bar {
        width: 100%;
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
    }

    .lehrgang-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        border-radius: 3px;
        transition: width 0.5s ease-out;
    }

    .lehrgang-progress-fill.complete {
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    .lehrgang-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: auto;
    }

    .lehrgang-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .lehrgang-btn.primary {
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.2);
    }

    .lehrgang-btn.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 51, 127, 0.3);
    }

    .lehrgang-btn.secondary {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #1e40af;
        box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
    }

    .lehrgang-btn.secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(251, 191, 36, 0.4);
    }

    .lehrgang-btn.success {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
    }

    .empty-state {
        background: white;
        border: 2px dashed #e5e7eb;
        border-radius: 1.25rem;
        padding: 3rem;
        text-align: center;
    }

    .empty-state-icon { font-size: 3rem; margin-bottom: 1rem; }
    .empty-state-title { font-size: 1.25rem; font-weight: 700; color: #00337F; margin-bottom: 0.5rem; }
    .empty-state-description { font-size: 0.95rem; color: #6b7280; }

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
        .lehrgaenge-container { padding: 1rem; }
        .lehrgaenge-title { font-size: 1.75rem; }
        .lehrgang-card { padding: 1.25rem; }
    }
</style>
@endpush

@section('content')
<div class="lehrgaenge-wrapper">
    <div class="lehrgaenge-container">
        <header class="lehrgaenge-header">
            <h1 class="lehrgaenge-title"><span>Lehrgänge</span></h1>
            <p class="lehrgaenge-subtitle">Wähle einen Lehrgang, um dein Wissen zu erweitern</p>
        </header>

        @if($lehrgaenge->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-mortarboard" style="color: #6b7280;"></i></div>
                <h3 class="empty-state-title">Noch keine Lehrgänge verfügbar</h3>
                <p class="empty-state-description">Bald werden hier Lehrgänge erscheinen!</p>
            </div>
        @else
            <div class="lehrgaenge-grid">
                @foreach($lehrgaenge as $lehrgang)
                    @php
                        $isEnrolled = in_array($lehrgang->id, $enrolledIds);
                        $progressPercent = 0;
                        $solvedCount = 0;
                        $totalCount = 0;
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
                    @endphp
                    
                    <div class="lehrgang-card">
                        <div class="lehrgang-header">
                            <h2 class="lehrgang-name">{{ $lehrgang->lehrgang }}</h2>
                            @if($isEnrolled)
                                <span class="lehrgang-badge enrolled"><i class="bi bi-check"></i> Eingeschrieben</span>
                            @endif
                        </div>
                        
                        <p class="lehrgang-description">{{ $lehrgang->beschreibung }}</p>
                        
                        <div class="lehrgang-stats">
                            <div class="stat-item">
                                <div class="stat-value">{{ $questionCount }}</div>
                                <div class="stat-label">Fragen</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">{{ $sectionCount }}</div>
                                <div class="stat-label">Abschnitte</div>
                            </div>
                        </div>
                        
                        @if($isEnrolled)
                            <div class="lehrgang-progress">
                                <div class="lehrgang-progress-header">
                                    <span>{{ $solvedCount }}/{{ $totalCount }} Fragen</span>
                                    <span>{{ $progressPercent }}%</span>
                                </div>
                                <div class="lehrgang-progress-bar">
                                    <div class="lehrgang-progress-fill {{ $isCompleted ? 'complete' : '' }}" style="width: {{ $progressPercent }}%"></div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="lehrgang-actions">
                            <a href="{{ route('lehrgaenge.show', $lehrgang->slug) }}" class="lehrgang-btn primary">
                                Details anzeigen
                            </a>
                            
                            @if($isEnrolled)
                                @if($isCompleted)
                                    <span class="lehrgang-btn success">Abgeschlossen</span>
                                @else
                                    <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="lehrgang-btn secondary">
                                        Weitermachen
                                    </a>
                                @endif
                            @else
                                <form action="{{ route('lehrgaenge.enroll', $lehrgang->slug) }}" method="POST" style="width: 100%;">
                                    @csrf
                                    <button type="submit" class="lehrgang-btn secondary" style="width: 100%;">
                                        Beitreten
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="text-center mt-8">
            <a href="{{ route('dashboard') }}" class="back-link">
                Zurück zum Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
