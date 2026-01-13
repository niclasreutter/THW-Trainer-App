@extends('layouts.app')

@section('title', $ortsverband->name . ' · Neuer Lernpool')

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
        margin-bottom: 2rem;
        padding-top: 1rem;
    }

    .dashboard-greeting {
        font-size: 2.25rem;
        font-weight: 800;
        color: #00337F;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .dashboard-subtitle {
        font-size: 1.1rem;
        color: #4b5563;
        margin-bottom: 0;
    }

    .info-card {
        background: white;
        padding: 2rem;
        border-radius: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #00337F;
        border: 1px solid #e5e7eb;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-size: 0.95rem;
        font-weight: 600;
        color: #00337F;
        margin-bottom: 0.5rem;
    }

    .form-label .required {
        color: #dc2626;
    }

    .form-input, .form-textarea {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        font-size: 0.95rem;
        transition: border-color 0.2s;
    }

    .form-input:focus, .form-textarea:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-input.error, .form-textarea.error {
        border-color: #dc2626;
    }

    .form-textarea {
        resize: vertical;
    }

    .form-checkbox {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .form-checkbox input {
        width: 1.25rem;
        height: 1.25rem;
        cursor: pointer;
    }

    .form-checkbox label {
        font-size: 0.95rem;
        font-weight: 600;
        color: #00337F;
        cursor: pointer;
        margin: 0;
    }

    .form-error {
        color: #dc2626;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 2rem;
    }

    .back-link {
        color: #2563eb;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: #1e40af;
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <a href="{{ route('ortsverband.lernpools.index', $ortsverband) }}" class="back-link">
            ← Zurück zu Lernpools
        </a>

        <div class="dashboard-header">
            <h1 class="dashboard-greeting">Neuer Lernpool</h1>
            <p class="dashboard-subtitle">{{ $ortsverband->name }}</p>
        </div>

        <div class="info-card">
            <form action="{{ route('ortsverband.lernpools.store', $ortsverband) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">
                        Name <span class="required">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                           class="form-input @error('name') error @enderror" 
                           placeholder="z.B. Grundlagen Erste Hilfe" required>
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">
                        Beschreibung <span class="required">*</span>
                    </label>
                    <textarea name="description" id="description" rows="5"
                              class="form-textarea @error('description') error @enderror"
                              placeholder="Beschreibung des Lernpools..." required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tags" class="form-label">
                        Schlagwörter / Tags
                    </label>
                    @if($existingTags->isNotEmpty())
                        <div style="margin-bottom: 0.75rem;">
                            <p style="font-size: 0.85rem; color: #6b7280; margin-bottom: 0.5rem;">
                                Vorhandene Tags (zum Hinzufügen anklicken):
                            </p>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                @foreach($existingTags as $tag)
                                    <button type="button" onclick="addTag('{{ $tag }}')" class="tag-suggestion" style="background: #e0f2fe; color: #0c4a6e; padding: 0.35rem 0.75rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600; border: 1px solid #7dd3fc; cursor: pointer; transition: all 0.2s;">
                                        + {{ $tag }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <input type="text" name="tags" id="tags" value="{{ old('tags') }}"
                           class="form-input @error('tags') error @enderror"
                           placeholder="z.B. ZTR, B FGr, N FGr (mit Komma trennen)">
                    <p style="font-size: 0.85rem; color: #6b7280; margin-top: 0.25rem;">
                        Mehrere Tags mit Komma trennen (z.B. "ZTR, B FGr, N FGr")
                    </p>
                    @error('tags')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <style>
                    .tag-suggestion:hover {
                        background: #0ea5e9 !important;
                        color: white !important;
                        transform: translateY(-2px);
                        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
                    }
                </style>

                <script>
                    function addTag(tag) {
                        const input = document.getElementById('tags');
                        const currentValue = input.value.trim();

                        // Prüfe ob Tag bereits vorhanden ist
                        const existingTags = currentValue.split(',').map(t => t.trim());
                        if (existingTags.includes(tag)) {
                            return; // Tag bereits vorhanden
                        }

                        // Füge Tag hinzu
                        if (currentValue === '') {
                            input.value = tag;
                        } else {
                            input.value = currentValue + ', ' + tag;
                        }

                        // Fokussiere Input
                        input.focus();
                    }
                </script>

                <div class="form-group">
                    <div class="form-checkbox">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active">Sofort aktivieren</label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        ✓ Lernpool erstellen
                    </button>
                    <a href="{{ route('ortsverband.lernpools.index', $ortsverband) }}" 
                       class="btn btn-secondary">
                        Abbrechen
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
