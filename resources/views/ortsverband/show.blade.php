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

    .info-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .info-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #00337F;
        margin-bottom: 1rem;
    }

    .info-text {
        color: #4b5563;
        line-height: 1.6;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; }
    }

    .stat-card {
        background: white;
        border-radius: 1.5rem;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .stat-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: #00337F;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #6b7280;
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
    }

    .member-info {
        flex: 1;
    }

    .member-name {
        font-weight: 600;
        color: #00337F;
    }

    .member-role {
        font-size: 0.85rem;
        color: #6b7280;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge-primary {
        background: rgba(0, 51, 127, 0.1);
        color: #00337F;
    }

    .badge-success {
        background: rgba(34, 197, 94, 0.1);
        color: #16a34a;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
    }

    .btn-danger {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-danger:hover {
        background: #fca5a5;
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
        padding: 1rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 480px) {
        .dashboard-container { padding: 1rem; }
        .info-card { padding: 1.25rem; }
        .dashboard-greeting { font-size: 1.75rem; }
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
        <div class="alert alert-success" style="background: #dbeafe; border: 1px solid rgba(59,130,246,0.3); color: #1e40af; display: flex; justify-content: space-between; align-items: center;">
            <span>🔍 Du betrachtest diesen Ortsverband als Admin. Einige Funktionen sind beschränkt.</span>
            <form method="POST" action="{{ route('admin.ortsverband.exit-view') }}" style="margin: 0;">
                @csrf
                <button type="submit" style="background: #dbeafe; color: #1e40af; border: none; cursor: pointer; font-weight: 600; text-decoration: underline;">
                    ← Admin-View beenden
                </button>
            </form>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success">
            ✓ {{ session('success') }}
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

        @if($userIsAusbilder)
        <!-- Ausbilder sieht alle Mitglieder -->
        <div class="info-card">
            <h2 class="info-title">👥 Mitglieder</h2>
            
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
        <div class="info-card">
            <h2 class="info-title">👨‍🏫 Deine Ausbilder</h2>
            <p style="color: #6b7280; margin-bottom: 1rem; font-size: 0.9rem;">Diese Personen sind deine Ansprechpartner im Ortsverband:</p>
            
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
        <div class="info-card" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd;">
            <h2 class="info-title" style="color: #0369a1;">ℹ️ Was können Ausbildungsbeauftragte einsehen?</h2>
            <div style="color: #0c4a6e; line-height: 1.8;">
                <p style="margin-bottom: 1rem;">Deine Ausbildungsbeauftragten haben Zugriff auf folgende Informationen, um dich optimal beim Lernen zu unterstützen:</p>
                <ul style="margin: 0; padding-left: 1.5rem;">
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

        <div class="info-card" style="text-align: center;">
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

        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{ route('dashboard') }}" style="color: #6b7280; text-decoration: none; font-size: 0.9rem;">
                ← Zurück zum Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
