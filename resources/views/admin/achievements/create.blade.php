@extends('layouts.app')
@section('title', 'Neues Achievement erstellen - THW Trainer Admin')

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

    .form-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #00337F;
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 1rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
    }

    .form-text {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #6b7280;
    }

    .button-group {
        display: flex;
        gap: 0.75rem;
        margin-top: 2rem;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-greeting">✨ <span>Neues Achievement</span></h1>
            <p class="dashboard-subtitle">Erstelle ein neues Achievement</p>
        </div>

        @if ($errors->any())
            <div class="alert-error">
                <strong>Fehler beim Speichern:</strong>
                <ul style="margin: 0.5rem 0 0 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-card">
            <form action="{{ route('admin.achievements.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="key">Key (eindeutig, z.B. "first_question")</label>
                    <input type="text"
                           class="form-control"
                           id="key"
                           name="key"
                           value="{{ old('key') }}"
                           required>
                    <small class="form-text">Eindeutiger Bezeichner (nur Kleinbuchstaben, Zahlen, Unterstriche)</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="title">Titel (z.B. "🌟 Erste Schritte")</label>
                    <input type="text"
                           class="form-control"
                           id="title"
                           name="title"
                           value="{{ old('title') }}"
                           required>
                    <small class="form-text">Der Titel kann ein Emoji enthalten</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Beschreibung</label>
                    <textarea class="form-control"
                              id="description"
                              name="description"
                              rows="3"
                              required>{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="icon">Icon (Emoji)</label>
                    <input type="text"
                           class="form-control"
                           id="icon"
                           name="icon"
                           value="{{ old('icon') }}"
                           maxlength="10"
                           placeholder="🎯">
                    <small class="form-text">Optional: Ein einzelnes Emoji</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="category">Kategorie</label>
                    <select class="form-control" id="category" name="category" required>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="requirement_value">Anforderungswert</label>
                    <input type="number"
                           class="form-control"
                           id="requirement_value"
                           name="requirement_value"
                           value="{{ old('requirement_value') }}"
                           min="0">
                    <small class="form-text">z.B. 50 für "50 Fragen beantwortet" (optional)</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="sort_order">Sortierung</label>
                    <input type="number"
                           class="form-control"
                           id="sort_order"
                           name="sort_order"
                           value="{{ old('sort_order', 0) }}"
                           min="0">
                    <small class="form-text">Niedrigere Werte werden zuerst angezeigt</small>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox"
                               class="form-check-input"
                               id="is_active"
                               name="is_active"
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-label" for="is_active" style="margin: 0;">
                            Achievement ist aktiv
                        </label>
                    </div>
                </div>

                <div class="button-group">
                    <a href="{{ route('admin.achievements.index') }}" class="btn btn-secondary">
                        Abbrechen
                    </a>
                    <button type="submit" class="btn btn-primary">
                        ✨ Achievement erstellen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
