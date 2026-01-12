@extends('layouts.app')

@section('title', $ortsverband->name)

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

    @media (max-width: 480px) {
        .dashboard-container { padding: 1rem; }
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

    @media (max-width: 480px) {
        .dashboard-greeting { font-size: 1.75rem; }
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
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; gap: 0.75rem; }
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

    @media (max-width: 480px) {
        .section-card { padding: 1.25rem; }
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1.5rem;
    }

    .member-list {
        display: grid;
        gap: 1rem;
    }

    .member-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 1rem;
        transition: all 0.3s ease;
    }

    .member-item:hover {
        background: #f3f4f6;
    }

    .member-avatar {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .member-info {
        flex: 1;
    }

    .member-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .member-role {
        font-size: 0.85rem;
        color: #6b7280;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-left: 0.5rem;
    }

    .badge-primary {
        background: rgba(0, 51, 127, 0.1);
        color: #00337F;
    }

    .badge-success {
        background: rgba(34, 197, 94, 0.1);
        color: #16a34a;
    }

    .lernpools-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }

    @media (max-width: 480px) {
        .lernpools-grid { grid-template-columns: 1fr; }
    }

    .lernpool-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
    }

    .lernpool-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        border-color: #d1d5db;
    }

    .lernpool-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .lernpool-description {
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 1rem;
        flex: 1;
    }

    .lernpool-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1rem;
        font-size: 0.85rem;
    }

    .lernpool-stat {
        background: #f9fafb;
        padding: 0.75rem;
        border-radius: 0.5rem;
        text-align: center;
    }

    .lernpool-stat-value {
        font-weight: 600;
        color: #00337F;
    }

    .lernpool-stat-label {
        color: #6b7280;
        font-size: 0.75rem;
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6, #0369a1);
        transition: width 0.3s ease;
    }

    .btn-group {
        display: flex;
        gap: 0.75rem;
        margin-top: auto;
    }

    .btn {
        flex: 1;
        padding: 0.65rem;
        border-radius: 0.5rem;
        text-align: center;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 51, 127, 0.3);
    }

    .btn-success {
        background: #10b981;
        color: white;
    }

    .btn-success:hover {
        background: #059669;
    }

    .btn-danger {
        background: #fee2e2;
        color: #dc2626;
        font-weight: 600;
    }

    .btn-danger:hover {
        background: #fca5a5;
    }

    .btn-sm {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .alert-success {
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.3);
        color: #166534;
    }

    .info-section {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 1px solid #bae6fd;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .section-title-info {
        color: #0369a1;
    }

    .btn-section {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        background: #3b82f6;
        color: white;
        border-radius: 0.5rem;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-section:hover {
        background: #2563eb;
    }

    .info-list {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        line-height: 1.8;
        color: #0c4a6e;
    }

    .info-list ul {
        margin: 1rem 0;
        padding-left: 1.5rem;
    }

    .info-list li {
        margin-bottom: 0.75rem;
    }

    .info-list strong {
        color: #0369a1;
    }

    .back-link {
        text-align: center;
        margin-top: 2rem;
    }

    .back-link a {
        color: #6b7280;
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s ease;
    }

    .back-link a:hover {
        color: #00337F;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #6b7280;
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-greeting">🏠 <span>{{ $ortsverband->name }}</span></h1>
            @if($ortsverband->description)
                <p class="dashboard-subtitle">{{ $ortsverband->description }}</p>
            @endif
        </div>

        @if($isAdminViewing)
        <div class="alert alert-success" style="background: #dbeafe; border: 1px solid rgba(59,130,246,0.3); color: #1e40af;">
            <span>🔍 Du betrachtest diesen Ortsverband als Admin. Einige Funktionen sind beschränkt.</span>
            <form method="POST" action="{{ route('admin.ortsverband.exit-view') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-sm" style="background: #dbeafe; color: #1e40af; border: 1px solid #0ea5e9;">
                    ← Admin-View beenden
                </button>
            </form>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success">
            <span>✓ {{ session('success') }}</span>
        </div>
        @endif

        <!-- Statistiken -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value">{{ $ortsverband->members()->count() }}</div>
                <div class="stat-label">Mitglieder</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-value">{{ $ortsverband->created_at->format('d.m.Y') }}</div>
                <div class="stat-label">Gegründet</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">👨‍🏫</div>
                <div class="stat-value">{{ $ortsverband->members()->wherePivot('role', 'ausbildungsbeauftragter')->count() }}</div>
                <div class="stat-label">Ausbilder</div>
            </div>
        </div>

        <!-- Mitgliederliste / Ausbilderliste -->
        @php
            $userMember = $ortsverband->members()->where('user_id', auth()->id())->first();
            $userIsAusbilder = $userMember && $userMember->pivot->role === 'ausbildungsbeauftragter';
            $ausbilder = $ortsverband->members()->wherePivot('role', 'ausbildungsbeauftragter')->get();
        @endphp

        <!-- Lernpools Sektion -->
        <div class="section-card info-section">
            <div class="section-header">
                <h2 class="section-title section-title-info">📚 Lernpools</h2>
                @if($userIsAusbilder)
                    <a href="{{ route('ortsverband.lernpools.index', $ortsverband) }}" class="btn-section">
                        Verwalten →
                    </a>
                @endif
            </div>

            @php
                $activeLernpools = $ortsverband->activeLernpools;
                $userEnrollments = auth()->user()->lernpoolEnrollments->pluck('lernpool_id')->toArray();
            @endphp

            @if($activeLernpools->count() > 0)
                <div class="lernpools-grid">
                    @foreach($activeLernpools as $pool)
                        @php
                            $isEnrolled = in_array($pool->id, $userEnrollments);
                            $totalQuestions = $pool->getQuestionCount();
                            $enrollment = auth()->user()->lernpoolEnrollments()->where('lernpool_id', $pool->id)->first();
                            $progress = $enrollment ? $enrollment->getProgress() : 0;
                        @endphp
                        <div class="lernpool-card">
                            <div class="lernpool-name">{{ $pool->name }}</div>
                            <div class="lernpool-description">{{ Str::limit($pool->description, 80) }}</div>
                            
                            <div class="lernpool-stats">
                                <div class="lernpool-stat">
                                    <div class="lernpool-stat-value">{{ $totalQuestions }}</div>
                                    <div class="lernpool-stat-label">Fragen</div>
                                </div>
                                <div class="lernpool-stat">
                                    <div class="lernpool-stat-value">{{ round($progress) }}%</div>
                                    <div class="lernpool-stat-label">Fortschritt</div>
                                </div>
                            </div>

                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $progress }}%;"></div>
                            </div>

                            @if($isEnrolled)
                                <a href="{{ route('ortsverband.lernpools.practice', [$ortsverband, $pool]) }}" class="btn btn-success">
                                    ✓ Weitermachen
                                </a>
                            @else
                                <form action="{{ route('ortsverband.lernpools.enroll', [$ortsverband, $pool]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                                        + Beitreten
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>Noch keine Lernpools verfügbar.</p>
                    @if($userIsAusbilder)
                        <a href="{{ route('ortsverband.lernpools.index', $ortsverband) }}" class="btn-section">
                            Erstelle jetzt einen →
                        </a>
                    @endif
                </div>
            @endif
        </div>

        @if($userIsAusbilder)
        <!-- Ausbilder sieht alle Mitglieder -->
        <div class="section-card">
            <h2 class="section-title">👥 Mitglieder</h2>
            
            <div class="member-list">
                @foreach($ortsverband->members as $member)
                <div class="member-item">
                    <div class="member-avatar">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div class="member-info">
                        <div class="member-name">
                            {{ $member->name }}
                            @if($member->pivot->role === 'ausbildungsbeauftragter')
                                <span class="badge badge-primary">Ausbilder</span>
                            @else
                                <span class="badge badge-success">Mitglied</span>
                            @endif
                        </div>
                        <div class="member-role">
                            Beigetreten: {{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d.m.Y') : 'N/A' }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <!-- Normale Mitglieder sehen nur Ausbilder -->
        <div class="section-card">
            <h2 class="section-title">👨‍🏫 Deine Ausbilder</h2>
            <p style="color: #6b7280; margin-bottom: 1.5rem; font-size: 0.9rem;">Diese Personen sind deine Ansprechpartner im Ortsverband:</p>
            
            <div class="member-list">
                @foreach($ausbilder as $member)
                <div class="member-item">
                    <div class="member-avatar">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div class="member-info">
                        <div class="member-name">
                            {{ $member->name }}
                            <span class="badge badge-primary">Ausbilder</span>
                        </div>
                        <div class="member-role">
                            Beigetreten: {{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d.m.Y') : 'N/A' }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Info: Was sehen Ausbildungsbeauftragte? -->
        <div class="section-card info-section">
            <h2 class="section-title section-title-info">ℹ️ Was können Ausbildungsbeauftragte einsehen?</h2>
            <div class="info-list">
                <p style="margin-bottom: 1rem;">Deine Ausbildungsbeauftragten haben Zugriff auf folgende Informationen, um dich optimal beim Lernen zu unterstützen:</p>
                <ul>
                    <li><strong>📊 Theorie-Fortschritt:</strong> Wie viele Fragen du bereits richtig beantwortet hast</li>
                    <li><strong>✅ Prüfungs-Streak:</strong> Anzahl der hintereinander bestandenen Prüfungen</li>
                    <li><strong>🔥 Lern-Streak:</strong> Wie viele Tage in Folge du gelernt hast</li>
                    <li><strong>⭐ Level & Punkte:</strong> Dein aktuelles Level und Punktestand</li>
                    <li><strong>📅 Letzte Aktivität:</strong> Wann du zuletzt in der App aktiv warst</li>
                    <li><strong>📉 Schwachstellen:</strong> Lernabschnitte, bei denen du Unterstützung brauchst</li>
                </ul>
                <p style="margin-top: 1rem; font-size: 0.9rem; opacity: 0.8;">
                    💡 Diese Daten helfen deinen Ausbildern, den Ausbildungsfortschritt zu verfolgen und gezielt Hilfe anzubieten.
                </p>
            </div>
        </div>

        <!-- Verlassen -->
        @php
            $currentMember = $ortsverband->members()->where('user_id', auth()->id())->first();
            $isAusbildungsbeauftragter = $currentMember && $currentMember->pivot->role === 'ausbildungsbeauftragter';
            $ausbilderCount = $ortsverband->members()->wherePivot('role', 'ausbildungsbeauftragter')->count();
            $canLeave = !$isAusbildungsbeauftragter || $ausbilderCount > 1;
        @endphp

        <div class="section-card">
            <div style="text-align: center;">
                @if($canLeave)
                <form action="{{ route('ortsverband.leave', $ortsverband) }}" 
                      method="POST"
                      onsubmit="return confirm('Möchtest du diesen Ortsverband wirklich verlassen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        🚪 Ortsverband verlassen
                    </button>
                </form>
                @else
                <p style="color: #6b7280; margin: 0;">
                    ⚠️ Du bist der einzige Ausbildungsbeauftragte und kannst den Ortsverband nicht verlassen.<br>
                    <span style="font-size: 0.85rem;">Ernenne zuerst einen anderen Ausbilder oder lösche den Ortsverband.</span>
                </p>
                @endif
            </div>
        </div>

        <div class="back-link">
            <a href="{{ route('dashboard') }}">← Zurück zum Dashboard</a>
        </div>
    </div>
</div>
@endsection
