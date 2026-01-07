@extends('layouts.app')

@section('title', 'Ortsverband erstellen')

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
        max-width: 600px;
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
        font-size: 2rem;
        font-weight: 800;
        color: #00337F;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .dashboard-subtitle {
        font-size: 1rem;
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
        font-weight: 700;
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
        font-family: inherit;
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
        width: 100%;
    }

    .btn-primary {
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.2);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 51, 127, 0.3);
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        margin-top: 1rem;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
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

    .alert-error ul {
        margin: 0.5rem 0 0 1.25rem;
        padding: 0;
    }

    @media (max-width: 480px) {
        .dashboard-container { padding: 1rem; }
        .form-card { padding: 1.5rem; }
        .dashboard-greeting { font-size: 1.5rem; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-greeting">🚨 Neuen Ortsverband erstellen</h1>
            <p class="dashboard-subtitle">Als Ausbildungsbeauftragter kannst du Mitglieder einladen und ihren Lernfortschritt verfolgen.</p>
        </div>

        @if ($errors->any())
        <div class="alert alert-error">
            <strong>❌ Fehler bei der Eingabe:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="form-card">
            <form action="{{ route('ortsverband.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Name des Ortsverbands *</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-input" 
                        value="{{ old('name') }}"
                        placeholder="z.B. THW München"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Beschreibung (optional)</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="form-input form-textarea"
                        placeholder="Eine kurze Beschreibung des Ortsverbands..."
                    >{{ old('description') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    ✓ Ortsverband erstellen
                </button>

                <a href="{{ route('ortsverband.index') }}" class="btn btn-secondary">
                    ← Abbrechen
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
