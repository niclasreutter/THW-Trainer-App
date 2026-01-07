@extends('layouts.app')

@section('title', 'Statistiken - THW Trainer')
@section('description', 'Öffentliche Statistiken über alle beantworteten Fragen im THW-Trainer. Sehen Sie, welche Fragen am häufigsten richtig oder falsch beantwortet wurden.')

@push('styles')
<style>
    * { box-sizing: border-box; }

    .statistics-wrapper {
        min-height: 100vh;
        background: #f3f4f6;
        position: relative;
        overflow-x: hidden;
    }

    .statistics-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
        position: relative;
        z-index: 1;
    }

    .statistics-header {
        text-align: center;
        margin-bottom: 2.5rem;
        padding-top: 1rem;
    }

    .statistics-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #00337F;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .statistics-subtitle {
        font-size: 1.1rem;
        color: #4b5563;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .stat-card {
        border-radius: 1.25rem;
        padding: 1.75rem;
        color: white;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .stat-card.blue { background: linear-gradient(135deg, #00337F 0%, #002a66 100%); }
    .stat-card.green { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); }
    .stat-card.red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .stat-card.orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

    .stat-card-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .stat-card-info {
        flex: 1;
    }

    .stat-label {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .stat-subtext {
        font-size: 0.85rem;
        opacity: 0.85;
    }

    .stat-icon {
        font-size: 3rem;
        opacity: 0.3;
        flex-shrink: 0;
    }

    .section-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1.25rem;
        padding: 2rem;
        margin-bottom: 2.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-title-icon {
        font-size: 1.75rem;
    }

    .section-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1rem;
    }

    .stat-item {
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.25rem;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .stat-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }

    .stat-item-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
        line-height: 1.3;
    }

    .stat-item-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }

    .stat-item-row:last-of-type { margin-bottom: 0.75rem; }

    .stat-item-count { font-weight: 600; }
    .stat-item-correct { color: #22c55e; }
    .stat-item-wrong { color: #ef4444; }

    .stat-item > div:nth-child(2) {
        flex: 1;
    }

    .stat-item > div:last-child {
        margin-top: auto;
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
        display: flex;
        margin-bottom: 0.5rem;
    }

    .progress-correct {
        height: 100%;
        background: linear-gradient(90deg, #22c55e, #16a34a);
        transition: width 0.5s ease;
    }

    .progress-wrong {
        height: 100%;
        background: linear-gradient(90deg, #ef4444, #dc2626);
        transition: width 0.5s ease;
    }

    .progress-label {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .questions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .questions-grid { grid-template-columns: 1fr; }
    }

    .question-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .question-item {
        border-left: 4px solid;
        border-radius: 0.75rem;
        padding: 1.25rem;
        background: #f9fafb;
        transition: all 0.2s ease;
    }

    .question-item:hover {
        transform: translateX(4px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .question-item.wrong {
        border-left-color: #ef4444;
    }

    .question-item.correct {
        border-left-color: #22c55e;
    }

    .question-rank {
        font-size: 1.25rem;
        font-weight: 800;
        color: #1f2937;
        display: inline-block;
        width: 32px;
        margin-right: 0.5rem;
    }

    .question-text {
        font-size: 0.9rem;
        color: #1f2937;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .question-section {
        font-size: 0.75rem;
        color: #6b7280;
        margin-bottom: 0.75rem;
    }

    .question-stats {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 0.75rem;
    }

    .question-rate {
        font-size: 1.25rem;
        font-weight: 800;
        text-align: right;
        min-width: 80px;
    }

    .question-rate.wrong { color: #ef4444; }
    .question-rate.correct { color: #22c55e; }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #6b7280;
    }

    .empty-icon { font-size: 2rem; margin-bottom: 1rem; }

    .info-banner {
        background: rgba(59, 130, 246, 0.1);
        border-left: 4px solid #00337F;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .info-banner-title {
        font-weight: 700;
        color: #00337F;
        margin-bottom: 0.5rem;
    }

    .info-banner-text {
        font-size: 0.9rem;
        color: #4b5563;
        line-height: 1.5;
    }

    @media (max-width: 640px) {
        .statistics-container { padding: 1rem; }
        .statistics-title { font-size: 1.75rem; }
        .section-stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="statistics-wrapper">
    <div class="statistics-container">
        <div class="statistics-header">
            <h1 class="statistics-title">📊 THW-Trainer Statistiken</h1>
            <p class="statistics-subtitle">Anonyme Statistiken über alle beantworteten Fragen</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-card-content">
                    <div class="stat-card-info">
                        <div class="stat-label">Gesamt beantwortet</div>
                        <div class="stat-value">{{ number_format($totalAnswered) }}</div>
                        <div class="stat-subtext">{{ number_format($totalAnsweredToday) }} heute</div>
                    </div>
                    <div class="stat-icon">📊</div>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-card-content">
                    <div class="stat-card-info">
                        <div class="stat-label">Richtig beantwortet</div>
                        <div class="stat-value">{{ number_format($totalCorrect) }}</div>
                        <div class="stat-subtext">{{ $successRate }}% Erfolgsrate</div>
                    </div>
                    <div class="stat-icon">✓</div>
                </div>
            </div>

            <div class="stat-card red">
                <div class="stat-card-content">
                    <div class="stat-card-info">
                        <div class="stat-label">Falsch beantwortet</div>
                        <div class="stat-value">{{ number_format($totalWrong) }}</div>
                        <div class="stat-subtext">{{ $errorRate }}% Fehlerrate</div>
                    </div>
                    <div class="stat-icon">✗</div>
                </div>
            </div>

            <div class="stat-card orange">
                <div class="stat-card-content">
                    <div class="stat-card-info">
                        <div class="stat-label">Prüfungen</div>
                        <div class="stat-value">{{ number_format($totalExams) }}</div>
                        <div class="stat-subtext">{{ number_format($passedExams) }} bestanden ({{ $examPassRate }}%)</div>
                    </div>
                    <div class="stat-icon">🏆</div>
                </div>
            </div>
        </div>

        <!-- Lernabschnitt-Statistiken -->
        @if($sectionStats->isNotEmpty())
        <div class="section-card">
            <h2 class="section-title">
                <span class="section-title-icon">📚</span>
                Statistik nach Lernabschnitten
            </h2>
            
            <div class="section-stats-grid">
                @foreach($sectionStats as $stat)
                    @php
                        $sectionNames = [
                            1 => 'Das THW im Gefüge des Zivil- und Katastrophenschutzes',
                            2 => 'Arbeitssicherheit und Gesundheitsschutz', 
                            3 => 'Arbeiten mit Leinen, Drahtseilen, Ketten, Rund- und Bandschlingen',
                            4 => 'Arbeiten mit Leitern',
                            5 => 'Stromerzeugung und Beleuchtung',
                            6 => 'Metall-, Holz- und Steinbearbeitung',
                            7 => 'Bewegen von Lasten',
                            8 => 'Arbeiten am und auf dem Wasser',
                            9 => 'Einsatzgrundlagen',
                            10 => 'Grundlagen der Rettung und Bergung'
                        ];
                        $sectionName = $sectionNames[$stat->lernabschnitt] ?? 'Unbekannt';
                    @endphp
                    <div class="stat-item">
                        <div class="stat-item-name">{{ $stat->lernabschnitt }}. {{ $sectionName }}</div>
                        
                        <div class="stat-item-row">
                            <span>Versuche:</span>
                            <span class="stat-item-count">{{ number_format($stat->total_attempts) }}</span>
                        </div>
                        
                        <div class="stat-item-row">
                            <span class="stat-item-correct">✓ Richtig</span>
                            <span class="stat-item-count stat-item-correct">{{ number_format($stat->correct_count) }}</span>
                        </div>
                        
                        <div class="stat-item-row">
                            <span class="stat-item-wrong">✗ Falsch</span>
                            <span class="stat-item-count stat-item-wrong">{{ number_format($stat->wrong_count) }}</span>
                        </div>
                        
                        <div class="progress-bar">
                            <div class="progress-correct" style="width: {{ $stat->success_rate }}%"></div>
                            <div class="progress-wrong" style="width: {{ 100 - $stat->success_rate }}%"></div>
                        </div>
                        
                        <div class="progress-label">{{ $stat->success_rate }}% Erfolgsrate</div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Top 10 Questions -->
        <div class="questions-grid">
            <!-- Top 10 Schwierigste Fragen -->
            <div class="section-card">
                <h2 class="section-title">
                    <span class="section-title-icon">⚠️</span>
                    Top 10 Schwierigste Fragen
                </h2>
                
                @if($topWrongQuestionsWithDetails->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon">📊</div>
                        <p>Noch nicht genügend Daten verfügbar</p>
                        <p style="font-size: 0.8rem;">(mindestens 5 Versuche pro Frage erforderlich)</p>
                    </div>
                @else
                    <div class="question-list">
                        @foreach($topWrongQuestionsWithDetails as $index => $item)
                            <div class="question-item wrong">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-bottom: 0.5rem;">
                                            <span class="question-rank">{{ $index + 1 }}.</span>
                                            <div class="question-text">{{ Str::limit($item['question']->frage, 120) }}</div>
                                        </div>
                                        <div class="question-section">Lernabschnitt {{ $item['question']->lernabschnitt }}</div>
                                        <div class="question-stats">
                                            <span>{{ number_format($item['total_attempts']) }} Versuche</span>
                                            <span><span class="stat-item-correct">✓ {{ number_format($item['correct_count']) }}</span> | <span class="stat-item-wrong">✗ {{ number_format($item['wrong_count']) }}</span></span>
                                        </div>
                                    </div>
                                    <div class="question-rate wrong">{{ $item['error_rate'] }}%</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Top 10 Einfachste Fragen -->
            <div class="section-card">
                <h2 class="section-title">
                    <span class="section-title-icon">⭐</span>
                    Top 10 Einfachste Fragen
                </h2>
                
                @if($topCorrectQuestionsWithDetails->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon">📊</div>
                        <p>Noch nicht genügend Daten verfügbar</p>
                        <p style="font-size: 0.8rem;">(mindestens 5 Versuche pro Frage erforderlich)</p>
                    </div>
                @else
                    <div class="question-list">
                        @foreach($topCorrectQuestionsWithDetails as $index => $item)
                            <div class="question-item correct">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-bottom: 0.5rem;">
                                            <span class="question-rank">{{ $index + 1 }}.</span>
                                            <div class="question-text">{{ Str::limit($item['question']->frage, 120) }}</div>
                                        </div>
                                        <div class="question-section">Lernabschnitt {{ $item['question']->lernabschnitt }}</div>
                                        <div class="question-stats">
                                            <span>{{ number_format($item['total_attempts']) }} Versuche</span>
                                            <span><span class="stat-item-correct">✓ {{ number_format($item['correct_count']) }}</span> | <span class="stat-item-wrong">✗ {{ number_format($item['wrong_count']) }}</span></span>
                                        </div>
                                    </div>
                                    <div class="question-rate correct">{{ $item['success_rate'] }}%</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Lehrgang-Statistiken -->
        @if($lehrgangStats->isNotEmpty())
        <div class="section-card">
            <h2 class="section-title">
                <span class="section-title-icon">🎓</span>
                Lehrgänge
            </h2>
            
            <div class="section-stats-grid">
                @foreach($lehrgangStats as $lehrgang)
                    <div class="stat-item">
                        <div class="stat-item-name">{{ $lehrgang->name }}</div>
                        
                        <div class="stat-item-row">
                            <span>👥 Nutzer eingeschrieben:</span>
                            <span class="stat-item-count">{{ number_format($lehrgang->users_count) }}</span>
                        </div>
                        
                        <div class="stat-item-row">
                            <span>❓ Fragen:</span>
                            <span class="stat-item-count">{{ $lehrgang->questions_count }}</span>
                        </div>
                        
                        <div class="stat-item-row">
                            <span>📊 Beantwortet:</span>
                            <span class="stat-item-count">{{ number_format($lehrgang->total_answered) }}</span>
                        </div>
                        
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                            <div class="stat-item-row" style="margin-bottom: 0.5rem;">
                                <span>Erfolgsrate</span>
                                <span class="stat-item-count" style="color: {{ $lehrgang->success_rate >= 70 ? '#22c55e' : ($lehrgang->success_rate >= 50 ? '#f59e0b' : '#ef4444') }}">
                                    {{ $lehrgang->success_rate }}%
                                </span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-correct" style="width: {{ $lehrgang->success_rate }}%; background: {{ $lehrgang->success_rate >= 70 ? 'linear-gradient(90deg, #22c55e, #16a34a)' : ($lehrgang->success_rate >= 50 ? 'linear-gradient(90deg, #f59e0b, #d97706)' : 'linear-gradient(90deg, #ef4444, #dc2626)') }}"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Info Banner -->
        <div class="info-banner">
            <div class="info-banner-title">ℹ️ Über diese Statistiken</div>
            <div class="info-banner-text">
                <p style="margin-bottom: 0.5rem;">
                    Diese Statistiken basieren auf anonymen Daten aller Nutzer (angemeldet und Gäste). 
                    Es werden keine persönlichen Informationen gespeichert - nur ob eine Frage richtig oder falsch beantwortet wurde.
                </p>
                <p>
                    Fragen in den Top-10-Listen benötigen mindestens 5 Versuche, um aussagekräftige Statistiken zu liefern.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

