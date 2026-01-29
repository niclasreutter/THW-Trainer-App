@extends('layouts.app')

@section('title', 'Ortsverband erstellen')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Neuen <span>Ortsverband</span> erstellen</h1>
        <p class="page-subtitle">Als Ausbildungsbeauftragter kannst du Mitglieder einladen und ihren Lernfortschritt verfolgen.</p>
    </header>

    @if ($errors->any())
    <div class="alert-compact glass-error" style="margin-bottom: 1.5rem;">
        <i class="bi bi-exclamation-triangle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">Fehler bei der Eingabe</div>
            <ul style="margin: 0.25rem 0 0 1rem; padding: 0; font-size: 0.8rem; color: var(--text-secondary);">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="form-wrapper">
    <div class="glass-gold" style="padding: 2rem; border-radius: 1rem;">
        <form action="{{ route('ortsverband.store') }}" method="POST">
            @csrf

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="name" class="form-label" style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; display: block;">
                    Name des Ortsverbands <span style="color: #ef4444;">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       class="input-glass"
                       value="{{ old('name') }}"
                       placeholder="z.B. THW München"
                       required>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="description" class="form-label" style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; display: block;">
                    Beschreibung <span style="color: var(--text-muted); font-weight: normal;">(optional)</span>
                </label>
                <textarea id="description"
                          name="description"
                          class="textarea-glass"
                          rows="4"
                          placeholder="Eine kurze Beschreibung des Ortsverbands...">{{ old('description') }}</textarea>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <button type="submit" class="btn-primary" style="width: 100%;">
                    Ortsverband erstellen
                </button>
                <a href="{{ route('ortsverband.index') }}" class="btn-ghost" style="width: 100%; text-align: center;">
                    Abbrechen
                </a>
            </div>
        </form>
    </div>
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

    .form-wrapper {
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
