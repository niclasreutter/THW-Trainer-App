@extends('layouts.app')

@section('title', 'Lehrgang bearbeiten')

@section('content')
<div class="dashboard-container" style="max-width: 800px;">
    <header class="dashboard-header">
        <h1 class="page-title">Lehrgang <span>bearbeiten</span></h1>
        <p class="page-subtitle">{{ $lehrgang->lehrgang }}</p>
    </header>

    @if(session('success'))
        <div class="glass-success" style="padding: 1.25rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-start;">
            <i class="bi bi-check-circle" style="font-size: 1.25rem; flex-shrink: 0;"></i>
            <div>
                <strong>Erfolg!</strong>
                <p style="margin: 0.25rem 0 0 0;">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="glass-error" style="padding: 1.25rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-start;">
            <i class="bi bi-exclamation-circle" style="font-size: 1.25rem; flex-shrink: 0;"></i>
            <div>
                <strong>Fehler!</strong>
                <p style="margin: 0.25rem 0 0 0;">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="glass hover-lift" style="padding: 1.5rem;">
        <form action="{{ route('admin.lehrgaenge.update', $lehrgang->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 1.5rem;">
                <label for="lehrgang" class="label-glass" style="margin-bottom: 0.5rem; display: block;">
                    Lehrgang Name <span style="color: var(--error);">*</span>
                </label>
                <input
                    type="text"
                    id="lehrgang"
                    name="lehrgang"
                    class="input-glass @error('lehrgang') border-error @enderror"
                    value="{{ old('lehrgang', $lehrgang->lehrgang) }}"
                    required
                    placeholder="z.B. Grundausbildung"
                    style="padding: 0.75rem 1rem;"
                >
                @error('lehrgang')
                    <span style="color: var(--error); font-size: 0.85rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="beschreibung" class="label-glass" style="margin-bottom: 0.5rem; display: block;">
                    Beschreibung <span style="color: var(--error);">*</span>
                </label>
                <textarea
                    id="beschreibung"
                    name="beschreibung"
                    rows="6"
                    class="textarea-glass @error('beschreibung') border-error @enderror"
                    required
                    placeholder="Beschreibung des Lehrgangs..."
                    style="padding: 0.75rem 1rem;"
                >{{ old('beschreibung', $lehrgang->beschreibung) }}</textarea>
                @error('beschreibung')
                    <span style="color: var(--error); font-size: 0.85rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 2rem;">
                <button type="submit" class="btn-primary" style="padding: 0.75rem 1.5rem; flex: 1; min-width: 150px;">
                    Speichern
                </button>
                <a href="{{ route('admin.lehrgaenge.show', $lehrgang->id) }}" class="btn-secondary" style="padding: 0.75rem 1.5rem; flex: 1; min-width: 150px; text-align: center;">
                    Zurück
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
