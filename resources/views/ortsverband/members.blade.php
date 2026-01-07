@extends('layouts.app')

@section('title', $ortsverband->name . ' - Mitglieder')

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

    .summary-card {
        background: white;
        border-radius: 1.5rem;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .summary-stat {
        text-align: center;
    }

    .summary-value {
        font-size: 2rem;
        font-weight: 800;
        color: #00337F;
    }

    .summary-label {
        font-size: 0.85rem;
        color: #6b7280;
    }

    .member-card {
        background: white;
        border-radius: 1.5rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .member-card:hover {
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .member-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .member-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: #00337F;
        margin-bottom: 0.25rem;
    }

    .member-meta {
        font-size: 0.85rem;
        color: #6b7280;
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

    .badge-success {
        background: rgba(34, 197, 94, 0.1);
        color: #16a34a;
    }

    .progress-section {
        margin: 1rem 0;
    }

    .progress-label {
        font-size: 0.9rem;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .progress-bar {
        height: 10px;
        background: #e5e7eb;
        border-radius: 5px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #00337F, #0047b3);
        transition: width 0.5s ease;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        margin-top: 1rem;
    }

    @media (max-width: 600px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }

    .stat-box {
        text-align: center;
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 0.75rem;
    }

    .stat-value {
        font-size: 1.25rem;
        font-weight: 800;
        color: #00337F;
    }

    .stat-label {
        font-size: 0.7rem;
        color: #6b7280;
        text-transform: uppercase;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
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
    }

    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #991b1b;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #6b7280;
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 480px) {
        .dashboard-container { padding: 1rem; }
        .member-card { padding: 1.25rem; }
        .dashboard-greeting { font-size: 1.75rem; }
        .summary-card { flex-direction: column; gap: 1rem; text-align: center; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-greeting">👥 <span>Mitgliederverwaltung</span></h1>
            <p class="dashboard-subtitle">{{ $ortsverband->name }}</p>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-error">
            ✗ {{ session('error') }}
        </div>
        @endif

        <div class="summary-card">
            <div class="summary-stat">
                <div class="summary-value">{{ $memberProgress->count() }}</div>
                <div class="summary-label">Mitglieder</div>
            </div>
            <div class="summary-stat">
                <div class="summary-value">{{ $ausbilderProgress->count() }}</div>
                <div class="summary-label">Ausbilder</div>
            </div>
            <a href="{{ route('ortsverband.invitations.index', $ortsverband) }}" class="btn btn-primary">
                + Mitglieder einladen
            </a>
        </div>

        {{-- Ausbilder-Sektion --}}
        @if($ausbilderProgress->count() > 0)
        <h3 style="color: #00337F; font-weight: 700; margin-bottom: 1rem; font-size: 1.1rem;">👨‍🏫 Ausbilder</h3>
        @foreach($ausbilderProgress as $member)
        <div class="member-card" style="border-left: 4px solid #00337F;">
            <div class="member-header">
                <div>
                    <div class="member-name">
                        {{ $member['user']->name }}
                        <span class="badge badge-primary">👨‍🏫 Ausbilder</span>
                    </div>
                    <div class="member-meta">
                        📧 {{ $member['user']->email }}
                        @if($member['user']->pivot->joined_at)
                            • Beigetreten: {{ \Carbon\Carbon::parse($member['user']->pivot->joined_at)->format('d.m.Y') }}
                        @endif
                    </div>
                </div>

                @if($member['user']->id !== auth()->id())
                <div style="display: flex; gap: 0.5rem;">
                    <form action="{{ route('ortsverband.members.role', [$ortsverband, $member['user']]) }}" 
                          method="POST" 
                          style="display: inline;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="role" value="member">
                        <button type="submit" class="btn btn-secondary" onclick="return confirm('Möchtest du diesen Ausbilder zum Mitglied degradieren?')">
                            👤 Zum Mitglied
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @endforeach
        @endif

        {{-- Mitglieder-Sektion --}}
        <h3 style="color: #00337F; font-weight: 700; margin-bottom: 1rem; margin-top: 2rem; font-size: 1.1rem;">👥 Mitglieder ({{ $memberProgress->count() }})</h3>
        
        @forelse($memberProgress as $member)
        <div class="member-card">
            <div class="member-header">
                <div>
                    <div class="member-name">
                        {{ $member['user']->name }}
                        <span class="badge badge-success">👤 Mitglied</span>
                    </div>
                    <div class="member-meta">
                        📧 {{ $member['user']->email }}
                        @if($member['user']->pivot->joined_at)
                            • Beigetreten: {{ \Carbon\Carbon::parse($member['user']->pivot->joined_at)->format('d.m.Y') }}
                        @endif
                        @if($member['last_activity'])
                            • Zuletzt aktiv: {{ is_string($member['last_activity']) ? \Carbon\Carbon::parse($member['last_activity'])->diffForHumans() : $member['last_activity']->diffForHumans() }}
                        @endif
                    </div>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    {{-- Rolle ändern --}}
                    <form action="{{ route('ortsverband.members.role', [$ortsverband, $member['user']]) }}" 
                          method="POST" 
                          style="display: inline;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="role" value="ausbildungsbeauftragter">
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Möchtest du dieses Mitglied zum Ausbilder befördern?')">
                            👨‍🏫 Zum Ausbilder
                        </button>
                    </form>
                    
                    {{-- Entfernen --}}
                    <form action="{{ route('ortsverband.members.remove', [$ortsverband, $member['user']]) }}" 
                          method="POST" 
                          onsubmit="return confirm('Möchtest du dieses Mitglied wirklich entfernen?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            Entfernen
                        </button>
                    </form>
                </div>
            </div>

            <div class="progress-section">
                <div class="progress-label">
                    Theorie-Fortschritt: <strong>{{ $member['theory_progress_count'] }}/268 Fragen ({{ $member['theory_progress_percent'] }}%)</strong>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $member['theory_progress_percent'] }}%"></div>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-value">{{ $member['exams_passed'] }}/5</div>
                    <div class="stat-label">Prüfungen</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $member['streak'] }}</div>
                    <div class="stat-label">Streak</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $member['level'] }}</div>
                    <div class="stat-label">Level</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($member['points']) }}</div>
                    <div class="stat-label">Punkte</div>
                </div>
            </div>
        </div>
        @empty
        <div class="member-card">
            <div class="empty-state">
                <div class="empty-state-icon">👥</div>
                <h3 style="color: #00337F; margin-bottom: 0.5rem;">Noch keine Mitglieder</h3>
                <p>Lade Mitglieder über Einladungslinks ein.</p>
                <a href="{{ route('ortsverband.invitations.index', $ortsverband) }}" class="btn btn-primary" style="margin-top: 1rem;">
                    Einladung erstellen
                </a>
            </div>
        </div>
        @endforelse

        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{ route('ortsverband.dashboard', $ortsverband) }}" style="color: #6b7280; text-decoration: none; font-size: 0.9rem;">
                ← Zurück zum Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
