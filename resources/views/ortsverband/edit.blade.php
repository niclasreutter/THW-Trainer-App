@extends('layouts.app')

@section('title', $ortsverband->name . ' bearbeiten')

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
        max-width: 800px;
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

    .form-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
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

    .form-textarea {
        min-height: 120px;
        resize: vertical;
    }

    .form-hint {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 0.5rem;
    }

    .form-error {
        font-size: 0.8rem;
        color: #dc2626;
        margin-top: 0.5rem;
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

    .btn-danger {
        background: #fee2e2;
        color: #dc2626;
        width: 100%;
        margin-top: 1rem;
    }

    .btn-danger:hover {
        background: #fca5a5;
    }

    .danger-zone {
        margin-top: 2rem;
        padding: 1.5rem;
        background: #fef2f2;
        border-radius: 1rem;
        border: 1px solid #fecaca;
    }

    .danger-title {
        color: #991b1b;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .danger-text {
        color: #991b1b;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 1rem;
        margin-bottom: 1.5rem;
    }

    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #991b1b;
    }

    @media (max-width: 480px) {
        .dashboard-container { padding: 1rem; }
        .form-card { padding: 1.5rem; }
        .dashboard-greeting { font-size: 1.75rem; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-greeting">✏️ <span>OV bearbeiten</span></h1>
            <p class="dashboard-subtitle">{{ $ortsverband->name }}</p>
        </div>

        @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="form-card">
            <form action="{{ route('ortsverband.update', $ortsverband) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name" class="form-label">Name des Ortsverbands *</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-input" 
                           value="{{ old('name', $ortsverband->name) }}"
                           placeholder="z.B. OV Musterstadt"
                           required>
                    <p class="form-hint">Der offizielle Name deines Ortsverbands</p>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Beschreibung (optional)</label>
                    <textarea id="description" 
                              name="description" 
                              class="form-input form-textarea"
                              placeholder="Kurze Beschreibung des Ortsverbands...">{{ old('description', $ortsverband->description) }}</textarea>
                    <p class="form-hint">Eine optionale Beschreibung für deine Mitglieder</p>
                </div>

                <button type="submit" class="btn btn-primary">
                    💾 Änderungen speichern
                </button>
            </form>

            <div class="danger-zone">
                <h3 class="danger-title">⚠️ Gefahrenzone</h3>
                <p class="danger-text">
                    Wenn du den Ortsverband löschst, werden alle Mitgliedschaften und Einladungen entfernt. 
                    Diese Aktion kann nicht rückgängig gemacht werden.
                </p>
                <form action="{{ route('ortsverband.destroy', $ortsverband) }}" 
                      method="POST"
                      onsubmit="return confirm('Bist du sicher? Alle Mitgliedschaften und Einladungen werden gelöscht!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        🗑️ Ortsverband löschen
                    </button>
                </form>
            </div>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{ route('ortsverband.dashboard', $ortsverband) }}" style="color: #6b7280; text-decoration: none; font-size: 0.9rem;">
                ← Zurück zum Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
