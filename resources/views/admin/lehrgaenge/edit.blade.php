@extends('layouts.app')

@section('title', 'Lehrgang bearbeiten')

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

    .dashboard-greeting span {
        display: inline-block;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dashboard-subtitle {
        font-size: 1rem;
        color: #4b5563;
        margin-bottom: 0;
    }

    .form-card {
        background: white;
        padding: 2rem;
        border-radius: 1.25rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
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
        font-family: inherit;
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

    .form-error {
        color: #dc2626;
        font-size: 0.85rem;
        margin-top: 0.25rem;
        display: block;
    }

    .button-group {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 2rem;
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
        justify-content: center;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        color: white;
        flex: 1;
        min-width: 150px;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #00337F;
        border: 1px solid #e5e7eb;
        flex: 1;
        min-width: 150px;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    @media (max-width: 480px) {
        .dashboard-container { padding: 1rem; }
        .form-card { padding: 1.25rem; }
        .dashboard-greeting { font-size: 1.5rem; }
        .button-group { flex-direction: column; }
        .btn { width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-greeting">✏️ <span>Lehrgang bearbeiten</span></h1>
            <p class="dashboard-subtitle">{{ $lehrgang->lehrgang }}</p>
        </div>

        <!-- Alerts -->
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

        <div class="form-card">
            <form action="{{ route('admin.lehrgaenge.update', $lehrgang->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="lehrgang" class="form-label">
                        Lehrgang Name <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="lehrgang"
                        name="lehrgang"
                        class="form-input @error('lehrgang') error @enderror"
                        value="{{ old('lehrgang', $lehrgang->lehrgang) }}"
                        required
                        placeholder="z.B. Grundausbildung"
                    >
                    @error('lehrgang')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="beschreibung" class="form-label">
                        Beschreibung <span class="required">*</span>
                    </label>
                    <textarea
                        id="beschreibung"
                        name="beschreibung"
                        rows="6"
                        class="form-textarea @error('beschreibung') error @enderror"
                        required
                        placeholder="Beschreibung des Lehrgangs..."
                    >{{ old('beschreibung', $lehrgang->beschreibung) }}</textarea>
                    @error('beschreibung')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        💾 Speichern
                    </button>
                    <a href="{{ route('admin.lehrgaenge.show', $lehrgang->id) }}" class="btn btn-secondary">
                        ← Zurück
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
