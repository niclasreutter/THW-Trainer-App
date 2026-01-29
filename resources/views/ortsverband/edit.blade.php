@extends('layouts.app')

@section('title', $ortsverband->name . ' bearbeiten')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Ortsverband <span>bearbeiten</span></h1>
        <p class="page-subtitle">{{ $ortsverband->name }}</p>
    </header>

    @if($errors->any())
    <div class="alert-compact glass-error" style="margin-bottom: 1.5rem;">
        <i class="bi bi-exclamation-triangle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">Fehler bei der Eingabe</div>
            <ul style="margin: 0.25rem 0 0 1rem; padding: 0; font-size: 0.8rem; color: var(--text-secondary);">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="glass-gold" style="padding: 2rem; border-radius: 1rem; margin-bottom: 1rem;">
        <form action="{{ route('ortsverband.update', $ortsverband) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="name" class="form-label" style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; display: block;">
                    Name des Ortsverbands <span style="color: #ef4444;">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       class="input-glass"
                       value="{{ old('name', $ortsverband->name) }}"
                       placeholder="z.B. OV Musterstadt"
                       required>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Der offizielle Name deines Ortsverbands</p>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="description" class="form-label" style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; display: block;">
                    Beschreibung <span style="color: var(--text-muted); font-weight: normal;">(optional)</span>
                </label>
                <textarea id="description"
                          name="description"
                          class="textarea-glass"
                          rows="4"
                          placeholder="Kurze Beschreibung des Ortsverbands...">{{ old('description', $ortsverband->description) }}</textarea>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Eine optionale Beschreibung für deine Mitglieder</p>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;">
                Änderungen speichern
            </button>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="glass-error" style="padding: 1.5rem; border-radius: 1rem;">
        <div style="display: flex; align-items: start; gap: 1rem;">
            <div style="width: 40px; height: 40px; background: rgba(239, 68, 68, 0.15); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="bi bi-exclamation-triangle" style="font-size: 1.25rem; color: #ef4444;"></i>
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Gefahrenzone</h3>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1rem;">
                    Wenn du den Ortsverband löschst, werden alle Mitgliedschaften und Einladungen entfernt. Diese Aktion kann nicht rückgängig gemacht werden.
                </p>
                <form action="{{ route('ortsverband.destroy', $ortsverband) }}"
                      method="POST"
                      onsubmit="return confirm('Bist du sicher? Alle Mitgliedschaften und Einladungen werden gelöscht!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger btn-sm">
                        Ortsverband löschen
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Back Link -->
    <div style="text-align: center; margin-top: 2rem;">
        <a href="{{ route('ortsverband.dashboard', $ortsverband) }}" class="btn-ghost btn-sm">
            <i class="bi bi-arrow-left"></i> Zurück zum Dashboard
        </a>
    </div>
</div>

@push('styles')
<style>
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .dashboard-header {
        margin-bottom: 2.5rem;
        padding-top: 1rem;
        max-width: 600px;
    }

    .alert-compact {
        padding: 0.875rem 1rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: start;
        gap: 0.75rem;
    }

    .alert-compact-icon { font-size: 1.25rem; margin-top: 0.1rem; }
    .alert-compact-content { flex: 1; }
    .alert-compact-title { font-size: 0.9rem; font-weight: 600; color: var(--text-primary); }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }
    }
</style>
@endpush
@endsection
