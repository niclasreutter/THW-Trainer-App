@extends('layouts.app')

@section('title', 'Meine Ortsverbände')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Mein <span>Ortsverband</span></h1>
        <p class="page-subtitle">Tritt einem Ortsverband bei oder erstelle deinen eigenen</p>
    </header>

    @if(session('success'))
    <div class="alert-compact glass-success" style="margin-bottom: 1.5rem;">
        <i class="bi bi-check-circle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">{{ session('success') }}</div>
        </div>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.25rem;">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert-compact glass-error" style="margin-bottom: 1.5rem;">
        <i class="bi bi-exclamation-triangle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">{{ session('error') }}</div>
        </div>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.25rem;">&times;</button>
    </div>
    @endif

    <!-- Bento Grid Layout -->
    <div class="bento-grid-ov">
        <!-- Beitreten Card (Main) -->
        <div class="glass-gold bento-join">
            <div class="section-header" style="margin-bottom: 1.5rem; padding-left: 0; border-left: none;">
                <h2 class="section-title" style="font-size: 1.5rem;">Beitreten</h2>
            </div>

            <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem;">
                Dein Ausbildungsbeauftragter hat einen Einladungscode für dich? Gib ihn hier ein, um deinem Ortsverband beizutreten.
            </p>

            <form action="{{ route('ortsverband.join.code') }}" method="POST">
                @csrf
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <input type="text"
                           name="code"
                           placeholder="Einladungscode (z.B. THW-XXXXXXXX)"
                           required
                           class="input-glass"
                           style="flex: 1; min-width: 200px;">
                    <button type="submit" class="btn-primary">
                        Beitreten
                    </button>
                </div>
            </form>
        </div>

        <!-- Erstellen Card (Side) -->
        <div class="glass-tl bento-create">
            <div style="margin-bottom: 1rem;">
                <span class="badge-thw">Ausbilder</span>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.75rem;">Ortsverband erstellen</h3>
            <p style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.5; margin-bottom: 1.25rem;">
                Erstelle deinen eigenen Ortsverband als Ausbildungsbeauftragter und lade Mitglieder ein.
            </p>
            <a href="{{ route('ortsverband.create') }}" class="btn-secondary btn-sm" style="align-self: flex-start;">
                Erstellen
            </a>
        </div>

        <!-- Info Card -->
        <div class="glass-slash bento-info">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: rgba(251, 191, 36, 0.15); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-info-circle" style="font-size: 1.5rem; color: var(--gold-start);"></i>
                </div>
                <div style="flex: 1;">
                    <h4 style="font-size: 0.95rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;">Was ist ein Ortsverband?</h4>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0;">
                        Ortsverbände ermöglichen Ausbildern, den Lernfortschritt ihrer Mitglieder zu verfolgen und eigene Lernpools zu erstellen.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Zurück Link -->
    <div style="text-align: center; margin-top: 2rem;">
        <a href="{{ route('dashboard') }}" class="btn-ghost btn-sm">
            <i class="bi bi-arrow-left"></i> Zurück zum Dashboard
        </a>
    </div>
</div>

@push('styles')
<style>
    .bento-grid-ov {
        display: grid;
        grid-template-columns: 2fr 1fr;
        grid-template-rows: auto auto;
        gap: 1rem;
    }

    .bento-join {
        padding: 2rem;
        display: flex;
        flex-direction: column;
    }

    .bento-create {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .bento-info {
        grid-column: span 2;
        padding: 1.25rem;
    }

    .alert-compact {
        padding: 0.875rem 1rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-compact-icon { font-size: 1.25rem; }
    .alert-compact-content { flex: 1; }
    .alert-compact-title { font-size: 0.9rem; font-weight: 600; color: var(--text-primary); }

    @media (max-width: 768px) {
        .bento-grid-ov {
            grid-template-columns: 1fr;
        }
        .bento-info { grid-column: span 1; }
    }
</style>
@endpush
@endsection
