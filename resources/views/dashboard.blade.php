@extends('layouts.app')

@section('title', 'Dashboard - Dein Lernfortschritt')
@section('description', 'Dein persönliches THW-Trainer Dashboard: Verfolge deinen Lernfortschritt, wiederhole falsche Fragen und bereite dich optimal auf deine THW-Prüfung vor.')

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
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 900px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
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
        background: white;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .stat-icon { font-size: 2rem; margin-bottom: 0.5rem; }
    .stat-value { font-size: 1.75rem; font-weight: 800; color: #00337F; line-height: 1; margin-bottom: 0.25rem; }
    .stat-label { font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }

    .stat-progress {
        width: 100%;
        height: 4px;
        background: #e5e7eb;
        border-radius: 2px;
        margin-top: 0.75rem;
        overflow: hidden;
    }

    .stat-progress-fill { height: 100%; border-radius: 2px; transition: width 1s ease-out; }
    .stat-progress-fill.yellow { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
    .stat-progress-fill.blue { background: linear-gradient(90deg, #3b82f6, #2563eb); }
    .stat-progress-fill.green { background: linear-gradient(90deg, #22c55e, #16a34a); }
    .stat-progress-fill.purple { background: linear-gradient(90deg, #a855f7, #9333ea); }

    .main-actions {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    @media (max-width: 700px) {
        .main-actions { grid-template-columns: 1fr; }
    }

    .action-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        flex-direction: column;
    }

    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .action-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .action-card-icon {
        width: 56px;
        height: 56px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
    }

    .action-card-icon.yellow { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); }
    .action-card-icon.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }

    .action-card-badge {
        background: rgba(0, 51, 127, 0.1);
        color: #00337F;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .action-card-title { font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem; }
    .action-card-description { font-size: 0.95rem; color: #6b7280; line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1; }

    .action-card-progress { margin-bottom: 1rem; }
    .action-card-progress-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
    .action-card-progress-label { font-size: 0.8rem; font-weight: 600; color: #6b7280; }
    .action-card-progress-value { font-size: 0.8rem; font-weight: 700; color: #00337F; }

    .action-card-progress-bar {
        width: 100%;
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
    }

    .action-card-progress-fill { height: 100%; border-radius: 4px; transition: width 1s ease-out; }
    .action-card-progress-fill.yellow { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
    .action-card-progress-fill.blue { background: linear-gradient(90deg, #3b82f6, #2563eb); }

    .action-card-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .action-card-btn.primary {
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.2);
    }

    .action-card-btn.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 51, 127, 0.3);
    }

    .action-card-btn.secondary {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #1e40af;
        box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
    }

    .action-card-btn.secondary:hover {
        box-shadow: 0 8px 25px rgba(251, 191, 36, 0.4);
    }

    .action-card-btn svg { width: 20px; height: 20px; }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-link {
        font-size: 0.875rem;
        font-weight: 600;
        color: #00337F;
        text-decoration: none;
        transition: color 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .section-link:hover { color: #002a66; }

    .lehrgaenge-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .lehrgang-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 1rem;
        padding: 1.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        flex-direction: column;
    }

    .lehrgang-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    }

    .lehrgang-card-title { font-size: 1.1rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem; }
    .lehrgang-card-description { font-size: 0.85rem; color: #6b7280; margin-bottom: 1rem; flex-grow: 1; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .lehrgang-card-progress { margin-bottom: 1rem; }
    .lehrgang-card-progress-header { display: flex; justify-content: space-between; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.35rem; }

    .lehrgang-card-progress-bar {
        width: 100%;
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
    }

    .lehrgang-card-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        border-radius: 3px;
        transition: width 0.5s ease-out;
    }

    .lehrgang-card-progress-fill.complete { background: linear-gradient(90deg, #22c55e, #16a34a); }

    .lehrgang-card-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        padding: 0.625rem 1rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #1e40af;
    }

    .lehrgang-card-btn:hover { box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4); }
    .lehrgang-card-btn.complete { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; }

    .empty-state-card {
        background: white;
        border: 2px dashed #e5e7eb;
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .empty-state-icon { font-size: 3rem; margin-bottom: 1rem; }
    .empty-state-title { font-size: 1.25rem; font-weight: 700; color: #00337F; margin-bottom: 0.5rem; }
    .empty-state-description { font-size: 0.95rem; color: #6b7280; margin-bottom: 1.5rem; }

    .empty-state-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #1e40af;
        font-weight: 700;
        border-radius: 0.75rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .empty-state-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(251, 191, 36, 0.4);
    }



    .alert-banner {
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(10px);
        border-radius: 1.25rem;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
        border: 1px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }

    .alert-banner.info { background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.3); }
    .alert-banner.warning { background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.3); }
    .alert-banner.error { background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.3); }

    .alert-banner-icon { font-size: 1.5rem; flex-shrink: 0; opacity: 0.9; }
    .alert-banner-content { flex: 1; }
    .alert-banner-title { font-size: 0.95rem; font-weight: 700; color: #1f2937; margin-bottom: 0.25rem; }
    .alert-banner-description { font-size: 0.85rem; color: #4b5563; }
    .alert-banner-action { flex-shrink: 0; }

    .alert-banner-btn {
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
        border-radius: 0.625rem;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.2);
    }

    .alert-banner-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0, 51, 127, 0.3); }

    .alert-banner-close {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        border: none;
        color: #4b5563;
        font-size: 1.25rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .alert-banner-close:hover { background: rgba(255, 255, 255, 0.7); color: #1f2937; }

    .leaderboard-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
        animation: fadeIn 0.3s ease-out;
    }

    .leaderboard-modal {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        border-radius: 24px;
        max-width: 520px;
        width: 100%;
        position: relative;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), 0 0 100px rgba(251, 191, 36, 0.3);
        animation: slideUp 0.4s ease-out;
        overflow: hidden;
    }

    .leaderboard-modal-content { padding: 2rem; position: relative; }

    .leaderboard-modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.5);
        color: white;
        font-size: 1.25rem;
        line-height: 1;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .leaderboard-modal-close:hover { background: rgba(255, 255, 255, 0.3); transform: rotate(90deg); }

    .leaderboard-trophy-bg {
        position: absolute;
        top: -30px;
        right: -30px;
        font-size: 150px;
        opacity: 0.15;
        transform: rotate(-15deg);
        pointer-events: none;
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes emojiFall { 0% { transform: translateY(0) rotate(0deg); opacity: 1; } 100% { transform: translateY(100vh) rotate(360deg); opacity: 0; } }

    @media (max-width: 640px) {
        .dashboard-container { padding: 1rem; }
        .dashboard-greeting { font-size: 1.75rem; }
        .dashboard-subtitle { font-size: 0.95rem; }
        .action-card { padding: 1.5rem; }
        .action-card-title { font-size: 1.25rem; }
        .leaderboard-modal { margin: 0; max-height: 90vh; overflow-y: auto; }
        .leaderboard-modal-content { padding: 1.5rem; }
        .leaderboard-trophy-bg { font-size: 100px; }
    }
</style>
@endpush

@section('content')
@php 
    $user = Auth::user();
    $total = $totalQuestions ?? \App\Models\Question::count();
    if (empty($total)) { $total = \App\Models\Question::count(); }
    
    $progressArr = is_array($user->solved_questions ?? null) 
        ? $user->solved_questions 
        : (is_string($user->solved_questions) ? json_decode($user->solved_questions, true) ?? [] : []);
    $progress = count($progressArr);
    
    // Hole Prüfungs-Streak: Nur aufeinanderfolgende bestandene Prüfungen zählen
    $allExams = \App\Models\ExamStatistic::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();
    
    $exams = 0; // Streak-Zähler
    foreach ($allExams as $exam) {
        if ($exam->is_passed) {
            $exams++; // Nur weiterzählen wenn bestanden
        } else {
            break; // Bei durchgefallener Prüfung stoppen (Streak unterbrochen)
        }
    }
    
    try {
        $progressData = \App\Models\UserQuestionProgress::where('user_id', $user->id)->get();
        $totalProgressPoints = 0;
        if ($progressData && $progressData->count() > 0) {
            foreach ($progressData as $prog) { $totalProgressPoints += min($prog->consecutive_correct ?? 0, 2); }
        }
        $maxProgressPoints = $total * 2;
        $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
    } catch (\Exception $e) { $progressPercent = 0; $totalProgressPoints = 0; }
    
    $enrolledLehrgaenge = Auth::user()->enrolledLehrgaenge()->get();
    
    $gamificationService = new \App\Services\GamificationService();
    $userAchievements = $gamificationService->getUserAchievements($user);
    $totalAchievements = count(\App\Services\GamificationService::ACHIEVEMENTS);
    $unlockedCount = count(array_filter($userAchievements, fn($a) => $a['unlocked']));
    
    $failedArr = is_array($user->exam_failed_questions ?? null) 
        ? $user->exam_failed_questions 
        : (is_string($user->exam_failed_questions) ? json_decode($user->exam_failed_questions, true) ?? [] : []);
    $hasFailedQuestions = $failedArr && count($failedArr) > 0;
    $canStartExam = $progress >= $total && !$hasFailedQuestions;
    
    if ($progressPercent == 100 && $exams >= 5) {
        $motivationalMessage = "Du hast alle Fragen gemeistert und 5+ Prüfungen bestanden!";
        $motivationalEmoji = "🎉";
    } elseif ($progressPercent == 100) {
        $motivationalMessage = "Alle Fragen gelöst! Zeit für die Prüfungen!";
        $motivationalEmoji = "🚀";
    } elseif ($progressPercent >= 75) {
        $motivationalMessage = "Fast geschafft! Noch " . (100 - $progressPercent) . "% bis zum Ziel!";
        $motivationalEmoji = "⚡";
    } elseif ($progressPercent >= 50) {
        $motivationalMessage = "Halbzeit! Du machst das großartig!";
        $motivationalEmoji = "💪";
    } elseif ($progressPercent >= 25) {
        $motivationalMessage = "Super Start! Bleib dran!";
        $motivationalEmoji = "🌟";
    } elseif ($progressPercent > 0) {
        $motivationalMessage = "Guter Anfang! Weiter so!";
        $motivationalEmoji = "✨";
    } else {
        $motivationalMessage = "Starte deine Reise zur Grundausbildung!";
        $motivationalEmoji = "🎯";
    }
@endphp

@if(!$user->leaderboard_banner_dismissed && !$user->leaderboard_consent)
<div class="leaderboard-modal-overlay" id="leaderboard-modal">
    <div class="leaderboard-modal">
        <div class="leaderboard-trophy-bg">🏆</div>
        <div class="leaderboard-modal-content">
            <button class="leaderboard-modal-close" onclick="dismissModal(false)" aria-label="Schließen">×</button>
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="display: inline-block; background: rgba(255, 255, 255, 0.2); border-radius: 50%; padding: 1rem; margin-bottom: 1rem;">
                    <svg style="width: 48px; height: 48px; color: white;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
                <h2 style="font-size: 1.75rem; font-weight: 800; color: white; margin-bottom: 0.5rem;">🎉 Öffentliches Leaderboard!</h2>
                <p style="color: white; font-size: 1rem; opacity: 0.9;">Messe dich mit anderen THW-Lernenden!</p>
            </div>
            <div style="background: rgba(255, 255, 255, 0.15); border-radius: 1rem; padding: 1.25rem; margin-bottom: 1.5rem; border: 2px solid rgba(255, 255, 255, 0.3);">
                <p style="color: white; font-size: 0.9rem; margin-bottom: 0.75rem;">📊 <strong>Dein Name & Punkte</strong> werden im Ranking angezeigt</p>
                <p style="color: white; font-size: 0.9rem; margin-bottom: 0.75rem;">🔄 <strong>Jederzeit änderbar</strong> in deinem Profil</p>
                <p style="color: white; font-size: 0.9rem; margin: 0;">🏆 <strong>Motiviere andere</strong> und lass dich motivieren</p>
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <form action="{{ route('profile.dismiss.leaderboard.banner') }}" method="POST" id="acceptForm">
                    @csrf
                    <input type="hidden" name="accept" value="1">
                    <button type="submit" style="width: 100%; background: white; color: #d97706; font-weight: 700; font-size: 1rem; padding: 1rem; border-radius: 0.75rem; border: none; cursor: pointer;">✅ Ja, ich möchte teilnehmen!</button>
                </form>
                <form action="{{ route('profile.dismiss.leaderboard.banner') }}" method="POST" id="declineForm">
                    @csrf
                    <input type="hidden" name="accept" value="0">
                    <button type="submit" style="width: 100%; background: rgba(255,255,255,0.2); color: white; font-weight: 600; padding: 0.875rem; border-radius: 0.75rem; border: 2px solid rgba(255,255,255,0.5); cursor: pointer;">❌ Nein, danke</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function dismissModal(accept) {
    const modal = document.getElementById('leaderboard-modal');
    if (modal) { modal.style.animation = 'fadeOut 0.3s ease-out forwards'; setTimeout(() => modal.remove(), 300); }
    if (accept === false) { document.getElementById('declineForm').submit(); }
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') dismissModal(false); });
</script>
@endif

<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1 class="dashboard-greeting">Hallo, <span>{{ $user->name }}!</span></h1>
            <p class="dashboard-subtitle">{{ $motivationalEmoji }} {{ $motivationalMessage }}</p>
        </header>

        @if(session('error'))
        <div class="alert-banner error" id="error-message">
            <div class="alert-banner-icon">🔥</div>
            <div class="alert-banner-content"><div class="alert-banner-title">{{ session('error') }}</div></div>
            <button class="alert-banner-close" onclick="this.parentElement.remove()">×</button>
        </div>
        @endif

        @if(!$user->email_consent && !session('email_consent_banner_dismissed'))
        <div class="alert-banner info" id="email-consent-banner">
            <div class="alert-banner-icon">📧</div>
            <div class="alert-banner-content">
                <div class="alert-banner-title">E-Mail-Benachrichtigungen aktivieren</div>
                <div class="alert-banner-description">Erhalte Updates zu deinem Lernfortschritt und neuen Features.</div>
            </div>
            <div class="alert-banner-action"><a href="{{ route('profile') }}" class="alert-banner-btn">Aktivieren</a></div>
            <button class="alert-banner-close" onclick="dismissEmailConsentBanner()">×</button>
        </div>
        @endif

        @if($hasFailedQuestions)
        <div class="alert-banner warning">
            <div class="alert-banner-icon">🔄</div>
            <div class="alert-banner-content">
                <div class="alert-banner-title">{{ count($failedArr) }} Frage{{ count($failedArr) == 1 ? '' : 'n' }} zum Wiederholen</div>
                <div class="alert-banner-description">Beantworte diese Fragen, bevor du eine neue Prüfung starten kannst.</div>
            </div>
            <div class="alert-banner-action"><a href="{{ route('failed.index') }}" class="alert-banner-btn">Jetzt lösen</a></div>
        </div>
        @endif

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🔥</div>
                <div class="stat-value">{{ $user->streak_days ?? 0 }}</div>
                <div class="stat-label">Tage Streak</div>
                <div class="stat-progress"><div class="stat-progress-fill yellow" style="width: {{ min(100, (($user->streak_days ?? 0) / 7) * 100) }}%"></div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⭐</div>
                <div class="stat-value">{{ $user->level ?? 1 }}</div>
                <div class="stat-label">Level</div>
                @php $levelUpPoints = 100 * pow(1.5, ($user->level ?? 1) - 1); $currentProgress = ($user->points ?? 0) % $levelUpPoints; $levelProgressPercent = $levelUpPoints > 0 ? ($currentProgress / $levelUpPoints) * 100 : 0; @endphp
                <div class="stat-progress"><div class="stat-progress-fill blue" style="width: {{ $levelProgressPercent }}%"></div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⚡</div>
                <div class="stat-value">{{ $user->daily_questions_solved ?? 0 }}/20</div>
                <div class="stat-label">Heute</div>
                <div class="stat-progress"><div class="stat-progress-fill green" style="width: {{ min(100, (($user->daily_questions_solved ?? 0) / 20) * 100) }}%"></div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏆</div>
                <div class="stat-value">{{ $unlockedCount }}/{{ $totalAchievements }}</div>
                <div class="stat-label">Erfolge</div>
                <div class="stat-progress"><div class="stat-progress-fill purple" style="width: {{ $totalAchievements > 0 ? ($unlockedCount / $totalAchievements) * 100 : 0 }}%"></div></div>
            </div>
        </div>

        <div class="main-actions">
            <a href="{{ route('practice.menu') }}" class="action-card" style="text-decoration: none;">
                <div class="action-card-header">
                    <div class="action-card-icon yellow">📚</div>
                    @if($progressPercent == 100)
                        <span class="action-card-badge" style="background: rgba(34, 197, 94, 0.15); color: #16a34a;">✓ Abgeschlossen</span>
                    @else
                        <span class="action-card-badge">Grundausbildung</span>
                    @endif
                </div>
                <h3 class="action-card-title">Theorie Lernen</h3>
                <p class="action-card-description">Lerne alle {{ $total }} Fragen der THW-Grundausbildung. Jede Frage muss 2x richtig beantwortet werden.</p>
                <div class="action-card-progress">
                    <div class="action-card-progress-header">
                        <span class="action-card-progress-label">{{ $progress }}/{{ $total }} gemeistert</span>
                        <span class="action-card-progress-value">{{ $progressPercent }}%</span>
                    </div>
                    <div class="action-card-progress-bar"><div class="action-card-progress-fill yellow" id="theoryProgressBar" style="width: 0%"></div></div>
                </div>
                <span onclick="event.preventDefault(); event.stopPropagation(); window.location='{{ route('practice.all') }}';" class="action-card-btn secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Fragen üben
                </span>
            </a>

            <div class="action-card">
                <div class="action-card-header">
                    <div class="action-card-icon blue">🎓</div>
                    @if($exams >= 5)
                        <span class="action-card-badge" style="background: rgba(34, 197, 94, 0.15); color: #16a34a;">✓ Bereit!</span>
                    @elseif($canStartExam)
                        <span class="action-card-badge" style="background: rgba(59, 130, 246, 0.15); color: #2563eb;">Freigeschaltet</span>
                    @else
                        <span class="action-card-badge">Gesperrt</span>
                    @endif
                </div>
                <h3 class="action-card-title">Prüfungssimulation</h3>
                <p class="action-card-description">
                    @if($canStartExam) Simuliere echte THW-Prüfungen mit 40 zufälligen Fragen und 45 Minuten Zeit.
                    @elseif($hasFailedQuestions) Beantworte zuerst deine {{ count($failedArr) }} offenen Fragen, um Prüfungen zu starten.
                    @else Beantworte zuerst alle Theorie-Fragen mindestens einmal richtig. @endif
                </p>
                <div class="action-card-progress">
                    <div class="action-card-progress-header">
                        <span class="action-card-progress-label">{{ $exams }}/5 bestanden</span>
                        <span class="action-card-progress-value">{{ min(100, $exams * 20) }}%</span>
                    </div>
                    <div class="action-card-progress-bar"><div class="action-card-progress-fill blue" id="examProgressBar" style="width: 0%"></div></div>
                </div>
                @if($canStartExam)
                    <a href="{{ route('exam.index') }}" class="action-card-btn primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Prüfung starten
                    </a>
                @elseif($hasFailedQuestions)
                    <a href="{{ route('failed.index') }}" class="action-card-btn primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Fehler wiederholen
                    </a>
                @else
                    <a href="{{ route('guest.practice.menu') }}" class="action-card-btn primary" style="opacity: 0.9;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Gästemodus nutzen
                    </a>
                @endif
            </div>
        </div>

        @if(!empty($recentExams) && $recentExams->count() > 0)
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 1.25rem; padding: 1.75rem; margin-bottom: 1.75rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <h2 class="section-title">📊 Deine letzten Prüfungen</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                @php
                    $totalPercentage = 0;
                    $passedCount = 0;
                    $totalQuestionsPerExam = 40; // Standard: immer 40 Fragen pro Prüfung
                @endphp
                @foreach($recentExams as $exam)
                    @php
                        $percentage = round(($exam->correct_answers / $totalQuestionsPerExam) * 100);
                        $totalPercentage += $percentage;
                        if ($exam->is_passed) $passedCount++;
                    @endphp
                    <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 1rem; padding: 1.25rem; transition: all 0.2s ease; display: flex; flex-direction: column;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem;">
                            <div>
                                <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 0.25rem;">{{ $exam->created_at->format('d.m.Y H:i') }} Uhr</div>
                                <div style="font-size: 1.5rem; font-weight: 800; color: #00337F;">{{ $percentage }}%</div>
                            </div>
                            <div style="font-size: 1.5rem;">{{ $exam->is_passed ? '✅' : '❌' }}</div>
                        </div>
                        <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 0.75rem;">{{ $exam->correct_answers }}/{{ $totalQuestionsPerExam }} richtig</div>
                        <span style="font-size: 0.8rem; font-weight: 700; padding: 0.35rem 0.75rem; border-radius: 0.5rem; text-align: center; background: {{ $exam->is_passed ? 'rgba(34, 197, 94, 0.15); color: #16a34a;' : 'rgba(239, 68, 68, 0.15); color: #dc2626;' }}">
                            {{ $exam->is_passed ? '✓ BESTANDEN' : 'NICHT BESTANDEN' }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; border-top: 1px solid #e5e7eb; padding-top: 1rem;">
                <div>
                    <div style="font-size: 0.8rem; color: #6b7280; margin-bottom: 0.25rem;">Durchschnitt</div>
                    <div style="font-size: 1.75rem; font-weight: 800; color: #00337F;">{{ round($totalPercentage / $recentExams->count()) }}%</div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: #6b7280; margin-bottom: 0.25rem;">Erfolgsrate</div>
                    <div style="font-size: 1.75rem; font-weight: 800; color: {{ $passedCount >= 3 ? '#16a34a' : '#dc2626' }};">{{ round(($passedCount / $recentExams->count()) * 100) }}%</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Ortsverband-Karte: Nur für Ausbilder mit erweiterten Infos --}}
        @php
            $userOV = auth()->user()->ortsverbände->first();
            $isAusbilder = false;
            $ovStats = null;
            
            if ($userOV) {
                $memberPivot = $userOV->members()->where('user_id', auth()->id())->first();
                $isAusbilder = $memberPivot && $memberPivot->pivot->role === 'ausbildungsbeauftragter';
                
                if ($isAusbilder) {
                    // Hole Statistiken für Ausbilder - NUR normale Mitglieder zählen (keine Ausbilder)
                    $regularMembers = $userOV->members()->wherePivot('role', 'member')->get();
                    $memberCount = $regularMembers->count();
                    $memberProgress = $userOV->getMemberProgress()->filter(fn($m) => $m['role'] === 'member');
                    $avgProgress = $memberProgress->avg('theory_progress_percent') ?? 0;
                    $needHelpCount = $memberProgress->filter(fn($m) => $m['theory_progress_percent'] < 25)->count();
                    $ovStats = [
                        'members' => $memberCount,
                        'avg_progress' => round($avgProgress),
                        'need_help' => $needHelpCount
                    ];
                }
            }
        @endphp

        @if($isAusbilder && $userOV)
        {{-- Ausbilder-Karte mit Statistiken --}}
        <div class="action-card">
            <div class="action-card-header">
                <div class="action-card-icon" style="background: linear-gradient(135deg, #00337F 0%, #0047b3 100%);">🏠</div>
                <span class="action-card-badge" style="background: rgba(0, 51, 127, 0.15); color: #00337F;">Ausbilder</span>
            </div>
            <h3 class="action-card-title">{{ $userOV->name }}</h3>
            
            @if($ovStats['members'] > 0)
            {{-- Statistik-Grid für Ausbilder --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1rem;">
                <div style="background: #f3f4f6; border-radius: 0.75rem; padding: 0.75rem; text-align: center;">
                    <div style="font-size: 1.25rem; font-weight: 800; color: #00337F;">{{ $ovStats['members'] }}</div>
                    <div style="font-size: 0.7rem; color: #6b7280; text-transform: uppercase;">Mitglieder</div>
                </div>
                <div style="background: #f3f4f6; border-radius: 0.75rem; padding: 0.75rem; text-align: center;">
                    <div style="font-size: 1.25rem; font-weight: 800; color: {{ $ovStats['avg_progress'] >= 50 ? '#16a34a' : '#f59e0b' }};">{{ $ovStats['avg_progress'] }}%</div>
                    <div style="font-size: 0.7rem; color: #6b7280; text-transform: uppercase;">Ø Fortschritt</div>
                </div>
                <div style="background: {{ $ovStats['need_help'] > 0 ? '#fef3c7' : '#f3f4f6' }}; border-radius: 0.75rem; padding: 0.75rem; text-align: center;">
                    <div style="font-size: 1.25rem; font-weight: 800; color: {{ $ovStats['need_help'] > 0 ? '#d97706' : '#16a34a' }};">{{ $ovStats['need_help'] }}</div>
                    <div style="font-size: 0.7rem; color: #6b7280; text-transform: uppercase;">{{ $ovStats['need_help'] > 0 ? 'Brauchen Hilfe' : 'Alle gut!' }}</div>
                </div>
            </div>

            <div class="action-card-progress">
                <div class="action-card-progress-header">
                    <span class="action-card-progress-label">Team-Fortschritt</span>
                    <span class="action-card-progress-value">{{ $ovStats['avg_progress'] }}%</span>
                </div>
                <div class="action-card-progress-bar"><div class="action-card-progress-fill blue" style="width: {{ $ovStats['avg_progress'] }}%"></div></div>
            </div>
            @else
            <p style="color: #6b7280; margin-bottom: 1rem; font-size: 0.9rem;">
                👋 Noch keine Mitglieder im Ortsverband. Lade Helfer über einen Einladungslink ein!
            </p>
            @endif

            <a href="{{ route('ortsverband.index') }}" class="action-card-btn primary" style="text-decoration: none;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Mitglieder verwalten
            </a>
        </div>
        @endif

        <div class="section-header">
            <h2 class="section-title">📚 Deine Lehrgänge</h2>
            <a href="{{ route('lehrgaenge.index') }}" class="section-link">Alle anzeigen <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></a>
        </div>

        @if($enrolledLehrgaenge->isNotEmpty())
        <div class="lehrgaenge-grid">
            @foreach($enrolledLehrgaenge->take(3) as $lehrgang)
                @php
                    $solvedCount = \App\Models\UserLehrgangProgress::where('user_id', Auth::id())->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))->where('solved', true)->count();
                    $totalCount = \App\Models\LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->count();
                    $progressData = \App\Models\UserLehrgangProgress::where('user_id', Auth::id())->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))->get();
                    $totalProgressPoints = 0;
                    foreach ($progressData as $prog) { $totalProgressPoints += min($prog->consecutive_correct, 2); }
                    $maxProgressPoints = $totalCount * 2;
                    $lehrgangProgressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
                    $isCompleted = $lehrgangProgressPercent == 100 && $solvedCount > 0;
                @endphp
                <div class="lehrgang-card">
                    <h4 class="lehrgang-card-title">{{ $lehrgang->lehrgang }}</h4>
                    <p class="lehrgang-card-description">{{ $lehrgang->beschreibung }}</p>
                    <div class="lehrgang-card-progress">
                        <div class="lehrgang-card-progress-header"><span>{{ $solvedCount }}/{{ $totalCount }} Fragen</span><span>{{ $lehrgangProgressPercent }}%</span></div>
                        <div class="lehrgang-card-progress-bar"><div class="lehrgang-card-progress-fill {{ $isCompleted ? 'complete' : '' }}" style="width: {{ $lehrgangProgressPercent }}%"></div></div>
                    </div>
                    @if($isCompleted)
                        <span class="lehrgang-card-btn complete">✓ Abgeschlossen</span>
                    @else
                        <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="lehrgang-card-btn">📖 Weitermachen</a>
                    @endif
                </div>
            @endforeach
        </div>
        @else
        <div class="empty-state-card">
            <div class="empty-state-icon">🎓</div>
            <h3 class="empty-state-title">Entdecke Lehrgänge!</h3>
            <p class="empty-state-description">Lerne spezifische THW-Themen mit unseren Lehrgängen. Mehr Inhalte kommen bald!</p>
            <a href="{{ route('lehrgaenge.index') }}" class="empty-state-btn">🚀 Lehrgänge erkunden</a>
        </div>
        @endif


    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.0/dist/confetti.browser.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const theoryBar = document.getElementById('theoryProgressBar');
        const examBar = document.getElementById('examProgressBar');
        if (theoryBar) { theoryBar.style.transition = 'width 1s ease-out'; theoryBar.style.width = '{{ $progressPercent }}%'; }
        if (examBar) { examBar.style.transition = 'width 1s ease-out'; examBar.style.width = '{{ min(100, $exams * 20) }}%'; }
        
        // Konfetti wenn Theorie Lernen 100% hat
        @if($progressPercent == 100)
        setTimeout(() => {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 }
            });
        }, 1500);
        @endif
    }, 200);
    
    @if($exams >= 5)
    setTimeout(() => {
        const emojis = ['🎊', '🎉', '🥳'];
        for (let i = 0; i < 15; i++) {
            setTimeout(() => {
                const emoji = document.createElement('div');
                emoji.textContent = emojis[Math.floor(Math.random() * emojis.length)];
                emoji.style.cssText = 'position: fixed; font-size: 2rem; left: ' + (Math.random() * 100) + 'vw; top: -50px; z-index: 9999; pointer-events: none; animation: emojiFall 3s linear forwards;';
                document.body.appendChild(emoji);
                setTimeout(() => emoji.remove(), 3000);
            }, i * 100);
        }
    }, 1000);
    @endif
});

function dismissEmailConsentBanner() {
    const banner = document.getElementById('email-consent-banner');
    if (banner) { banner.style.transition = 'opacity 0.3s ease-out'; banner.style.opacity = '0'; setTimeout(() => banner.remove(), 300); }
    fetch('/dashboard/dismiss-email-consent-banner', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } }).catch(console.log);
}
</script>
@endsection
