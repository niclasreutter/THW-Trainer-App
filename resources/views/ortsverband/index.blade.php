@extends('layouts.app')

@section('title', 'Meine Ortsverbände')

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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 900px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
        .dashboard-greeting { font-size: 1.75rem; }
    }

    .stat-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.25rem;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .stat-icon { font-size: 2rem; margin-bottom: 0.5rem; }
    .stat-value { font-size: 1.75rem; font-weight: 800; color: #00337F; line-height: 1; margin-bottom: 0.25rem; }
    .stat-label { font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }

    .main-actions {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    @media (max-width: 700px) {
        .main-actions { grid-template-columns: 1fr; }
    }

    .action-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .action-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .action-card-icon {
        width: 56px;
        height: 56px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
    }

    .action-card-icon.blue { background: linear-gradient(135deg, #00337F 0%, #0047b3 100%); }
    .action-card-icon.yellow { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); }

    .action-card-badge {
        background: rgba(0, 51, 127, 0.1);
        color: #00337F;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .action-card-title { font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem; }
    .action-card-description { font-size: 0.95rem; color: #6b7280; line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1; }

    .action-card-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .action-card-btn.primary {
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.2);
    }

    .action-card-btn.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 51, 127, 0.3);
    }

    .action-card-btn.secondary {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #1e40af;
        box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
    }

    .action-card-btn svg { width: 20px; height: 20px; }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
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

    .ortsverband-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .ortsverband-card:hover {
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .ortsverband-card-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1.5rem;
    }

    .ortsverband-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: #00337F;
        margin-bottom: 0.5rem;
    }

    .badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-primary {
        background: rgba(0, 51, 127, 0.1);
        color: #00337F;
    }

    .badge-success {
        background: rgba(34, 197, 94, 0.1);
        color: #16a34a;
    }

    .ortsverband-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
        margin: 1.5rem 0;
    }

    .ortsverband-stat {
        text-align: center;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 0.75rem;
    }

    .ortsverband-stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: #00337F;
    }

    .ortsverband-stat-label {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
        margin-top: 0.25rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
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
    }

    .btn-secondary:hover {
        background: #e5e7eb;
        transform: translateY(-2px);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }

    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #00337F;
        margin-bottom: 0.5rem;
    }

    .empty-state-description {
        color: #6b7280;
        margin-bottom: 2rem;
    }

    @media (max-width: 480px) {
        .dashboard-container { padding: 1rem; }
        .ortsverband-card { padding: 1.5rem; }
        .action-buttons { flex-direction: column; }
        .btn { width: 100%; justify-content: center; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-greeting">🚨 <span>Ortsverband</span></h1>
            <p class="dashboard-subtitle">Tritt einem Ortsverband bei oder erstelle deinen eigenen</p>
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

        {{-- Kein Ortsverband - Erstellen oder Beitreten --}}
        <div class="ortsverband-card">
            <div class="empty-state">
                <div class="empty-state-icon">🚨</div>
                <h3 class="empty-state-title">Noch kein Ortsverband</h3>
                <p class="empty-state-description">
                    Erstelle einen eigenen Ortsverband als Ausbildungsbeauftragter oder tritt einem über einen Einladungscode bei.
                </p>
                
                {{-- Einladungscode eingeben --}}
                <form action="{{ route('ortsverband.join.code') }}" method="POST" style="margin-bottom: 1.5rem; max-width: 400px; margin-left: auto; margin-right: auto;">
                    @csrf
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" 
                               name="code" 
                               placeholder="Einladungscode (z.B. THW-XXXXXXXX)"
                               required
                               style="flex: 1; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; font-size: 1rem;">
                        <button type="submit" class="btn btn-primary" style="white-space: nowrap;">
                            Beitreten
                        </button>
                    </div>
                </form>

                <p style="color: #9ca3af; margin-bottom: 1.5rem;">— oder —</p>
                
                <a href="{{ route('ortsverband.create') }}" class="btn btn-secondary" style="background: #f3f4f6; color: #374151; border: 2px solid #e5e7eb;">
                    + Ortsverband erstellen
                </a>
            </div>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{ route('dashboard') }}" style="color: #6b7280; text-decoration: none; font-size: 0.9rem;">
                ← Zurück zum Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
