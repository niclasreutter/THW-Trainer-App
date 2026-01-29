@extends('layouts.app')

@section('title', 'Neuer Lehrgang')

@section('content')
<div class="dashboard-container" style="max-width: 800px;">
    <header class="dashboard-header">
        <h1 class="page-title">Neuer <span>Lehrgang</span></h1>
        <p class="page-subtitle">Erstelle einen neuen Lehrgang mit Fragen</p>
    </header>

    <div class="glass hover-lift" style="padding: 1.5rem;">
        <form action="{{ route('admin.lehrgaenge.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 1.5rem;">
                <label for="lehrgang" class="label-glass" style="margin-bottom: 0.5rem; display: block;">
                    Lehrgang Name <span style="color: var(--error);">*</span>
                </label>
                <input
                    type="text"
                    id="lehrgang"
                    name="lehrgang"
                    class="input-glass @error('lehrgang') border-error @enderror"
                    value="{{ old('lehrgang') }}"
                    required
                    placeholder="z.B. Grundlagen der Sicherheit"
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
                    placeholder="Kurze Beschreibung des Lehrgangs..."
                    style="padding: 0.75rem 1rem;"
                >{{ old('beschreibung') }}</textarea>
                @error('beschreibung')
                    <span style="color: var(--error); font-size: 0.85rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 2rem;">
                <button type="submit" class="btn-primary" style="padding: 0.75rem 1.5rem; flex: 1; min-width: 150px;">
                    Erstellen
                </button>
                <a href="{{ route('admin.lehrgaenge.index') }}" class="btn-secondary" style="padding: 0.75rem 1.5rem; flex: 1; min-width: 150px; text-align: center;">
                    Abbrechen
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
