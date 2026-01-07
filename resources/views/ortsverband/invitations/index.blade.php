@extends('layouts.app')

@section('title', $ortsverband->name . ' - Einladungen')

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
        color: #00337F;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-input {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
    }

    .form-input:focus {
        outline: none;
        border-color: #00337F;
        box-shadow: 0 0 0 3px rgba(0, 51, 127, 0.1);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    @media (max-width: 600px) {
        .form-row { grid-template-columns: 1fr; }
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 1rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
        width: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 40px rgba(0, 51, 127, 0.3);
    }

    .btn-small {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        width: auto;
    }

    .btn-success {
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        color: white;
    }

    .btn-danger {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-danger:hover {
        background: #fca5a5;
    }

    .btn-outline {
        background: white;
        border: 2px solid #e5e7eb;
        color: #374151;
    }

    .btn-outline:hover {
        border-color: #00337F;
        color: #00337F;
    }

    .invitation-card {
        background: #f9fafb;
        border-radius: 1rem;
        padding: 1.25rem;
        margin-bottom: 1rem;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .invitation-card:hover {
        border-color: #00337F;
        background: white;
    }

    .invitation-card.active {
        border-color: #22c55e;
    }

    .invitation-card.inactive {
        border-color: #fbbf24;
        opacity: 0.7;
    }

    .invitation-card.expired {
        border-color: #ef4444;
        opacity: 0.6;
    }

    .invitation-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 0.75rem;
    }

    .invitation-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #00337F;
    }

    .invitation-meta {
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

    .badge-active {
        background: rgba(34, 197, 94, 0.1);
        color: #16a34a;
    }

    .badge-inactive {
        background: rgba(251, 191, 36, 0.1);
        color: #b45309;
    }

    .badge-expired {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .copy-box {
        display: flex;
        gap: 0.5rem;
        margin: 0.75rem 0;
    }

    .copy-input {
        flex: 1;
        padding: 0.5rem 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 0.85rem;
        font-family: monospace;
        background: white;
    }

    .copy-btn {
        padding: 0.5rem 0.75rem;
        background: #00337F;
        color: white;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }

    .copy-btn:hover {
        background: #002a66;
    }

    .copy-btn.copied {
        background: #16a34a;
    }

    .stats-row {
        display: flex;
        gap: 1.5rem;
        margin-top: 0.75rem;
        font-size: 0.85rem;
        color: #6b7280;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
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
        padding: 2rem;
        color: #6b7280;
    }

    @media (max-width: 480px) {
        .dashboard-container { padding: 1rem; }
        .section-card { padding: 1.25rem; }
        .dashboard-greeting { font-size: 1.75rem; }
        .invitation-header { flex-direction: column; gap: 0.5rem; }
        .copy-box { flex-direction: column; }
        .stats-row { flex-direction: column; gap: 0.5rem; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-greeting">🔗 <span>Einladungen verwalten</span></h1>
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

        <!-- Neue Einladung erstellen -->
        <div class="section-card">
            <h2 class="section-title">➕ Neue Einladung erstellen</h2>

            <form action="{{ route('ortsverband.invitations.store', $ortsverband) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Bezeichnung</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-input" 
                           placeholder="z.B. Grundausbildung 2024, Neue Helfer..."
                           required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="max_uses" class="form-label">Maximale Nutzungen (optional)</label>
                        <input type="number" 
                               id="max_uses" 
                               name="max_uses" 
                               class="form-input" 
                               placeholder="Unbegrenzt"
                               min="1">
                    </div>

                    <div class="form-group">
                        <label for="expires_at" class="form-label">Gültig bis (optional)</label>
                        <input type="date" 
                               id="expires_at" 
                               name="expires_at" 
                               class="form-input"
                               min="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    🔗 Einladung erstellen
                </button>
            </form>
        </div>

        <!-- Bestehende Einladungen -->
        <div class="section-card">
            <h2 class="section-title">📋 Bestehende Einladungen</h2>

            @forelse($invitations as $invitation)
            @php
                $statusClass = $invitation->is_expired ? 'expired' : ($invitation->is_active ? 'active' : 'inactive');
                $badgeClass = $invitation->is_expired ? 'badge-expired' : ($invitation->is_active ? 'badge-active' : 'badge-inactive');
                $statusText = $invitation->is_expired ? 'Abgelaufen' : ($invitation->is_active ? 'Aktiv' : 'Pausiert');
            @endphp

            <div class="invitation-card {{ $statusClass }}">
                <div class="invitation-header">
                    <div>
                        <div class="invitation-name">{{ $invitation->name }}</div>
                        <div class="invitation-meta">
                            Erstellt: {{ $invitation->created_at->format('d.m.Y H:i') }}
                            @if($invitation->expires_at)
                                • Gültig bis: {{ $invitation->expires_at->format('d.m.Y') }}
                            @endif
                        </div>
                    </div>
                    <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                </div>

                <div class="copy-box">
                    <input type="text" 
                           class="copy-input" 
                           value="{{ route('register', ['code' => $invitation->code]) }}" 
                           readonly 
                           id="link-{{ $invitation->id }}">
                    <button type="button" class="copy-btn" onclick="copyToClipboard('link-{{ $invitation->id }}', this)">
                        📋 Kopieren
                    </button>
                </div>

                <div class="copy-box">
                    <input type="text" 
                           class="copy-input" 
                           value="{{ $invitation->code }}" 
                           readonly 
                           id="code-{{ $invitation->id }}"
                           style="font-weight: bold;">
                    <button type="button" class="copy-btn" onclick="copyToClipboard('code-{{ $invitation->id }}', this)">
                        📋 Code kopieren
                    </button>
                </div>

                <div class="stats-row">
                    <span>👥 {{ $invitation->current_uses }} Nutzungen</span>
                    @if($invitation->max_uses)
                        <span>📊 Max: {{ $invitation->max_uses }}</span>
                    @else
                        <span>📊 Unbegrenzt</span>
                    @endif
                </div>

                @if(!$invitation->is_expired)
                <div class="action-buttons" style="margin-top: 1rem;">
                    <form action="{{ route('ortsverband.invitations.toggle', [$ortsverband, $invitation]) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-small btn-outline">
                            @if($invitation->is_active)
                                ⏸️ Pausieren
                            @else
                                ▶️ Aktivieren
                            @endif
                        </button>
                    </form>

                    <form action="{{ route('ortsverband.invitations.destroy', [$ortsverband, $invitation]) }}" 
                          method="POST" 
                          style="display: inline;"
                          onsubmit="return confirm('Möchtest du diese Einladung wirklich löschen?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-small btn-danger">
                            🗑️ Löschen
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @empty
            <div class="empty-state">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🔗</div>
                <h3 style="color: #00337F; margin-bottom: 0.5rem;">Keine Einladungen vorhanden</h3>
                <p>Erstelle deine erste Einladung, um Mitglieder einzuladen.</p>
            </div>
            @endforelse
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{ route('ortsverband.dashboard', $ortsverband) }}" style="color: #6b7280; text-decoration: none; font-size: 0.9rem;">
                ← Zurück zum Dashboard
            </a>
        </div>
    </div>
</div>

<script>
function copyToClipboard(inputId, button) {
    const input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999);
    
    navigator.clipboard.writeText(input.value).then(() => {
        const originalText = button.innerHTML;
        button.innerHTML = '✓ Kopiert!';
        button.classList.add('copied');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('copied');
        }, 2000);
    });
}
</script>
@endsection
