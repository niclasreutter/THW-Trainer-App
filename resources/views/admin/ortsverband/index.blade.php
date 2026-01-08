@extends('layouts.app')

@section('title', 'Admin - Ortsverbände')
@section('description', 'Admin-Panel: Verwalte alle Ortsverbände')

@push('styles')
<style>
    * { box-sizing: border-box; }

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

    .ortsverband-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .ortsverband-card {
        background: white;
        border-radius: 1.5rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
    }

    .ortsverband-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .ortsverband-card-content {
        flex: 1;
    }

    .ortsverband-card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 0.5rem 0;
    }

    .ortsverband-card-description {
        font-size: 0.9rem;
        color: #6b7280;
        margin: 0 0 1rem 0;
    }

    .ortsverband-card-meta {
        display: flex;
        gap: 2rem;
        font-size: 0.9rem;
        color: #6b7280;
        margin-bottom: 0.75rem;
    }

    .ortsverband-card-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .ortsverband-card-meta-item strong {
        color: #1f2937;
    }

    .ortsverband-card-creator {
        font-size: 0.85rem;
        color: #6b7280;
        margin: 0;
    }

    .ortsverband-card-creator strong {
        color: #1f2937;
    }

    .ortsverband-card-actions {
        display: flex;
        gap: 0.75rem;
        flex-shrink: 0;
    }

    .btn-small {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        padding: 0.75rem 1.25rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .btn-primary-small {
        background: linear-gradient(135deg, #00337F, #002a66);
        color: white;
    }

    .btn-primary-small:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 51, 127, 0.3);
    }

    .btn-danger-small {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-danger-small:hover {
        background: #fecaca;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        background: white;
        border-radius: 1.5rem;
        color: #6b7280;
        margin-top: 2rem;
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .empty-state-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 2rem;
    }

    .pagination a, .pagination span {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        text-decoration: none;
        color: #00337F;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .pagination a:hover {
        background: #f3f4f6;
    }

    .pagination .active span {
        background: #00337F;
        color: white;
        border-color: #00337F;
    }

    .alert-banner {
        background: #dcfce7;
        color: #166534;
        padding: 1rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #22c55e;
    }

    @media (max-width: 768px) {
        .dashboard-container { padding: 1rem; }
        .dashboard-greeting { font-size: 1.75rem; }
        .ortsverband-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        .ortsverband-card-actions {
            width: 100%;
        }
        .btn-small {
            flex: 1;
        }
        .ortsverband-card-meta {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1 class="dashboard-greeting">🏢 <span>Ortsverbände</span></h1>
            <p class="dashboard-subtitle">Admin-Übersicht aller Ortsverbände</p>
        </header>

        @if(session('success'))
        <div class="alert-banner">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if($ortsverbande->count() > 0)
            <div class="ortsverband-list">
                @foreach($ortsverbande as $ov)
                <div class="ortsverband-card">
                    <div class="ortsverband-card-content">
                        <h3 class="ortsverband-card-title">{{ $ov->name }}</h3>
                        @if($ov->description)
                        <p class="ortsverband-card-description">{{ $ov->description }}</p>
                        @endif
                        
                        <div class="ortsverband-card-meta">
                            <div class="ortsverband-card-meta-item">
                                <span>👥</span>
                                <strong>{{ $ov->members_count ?? $ov->members()->count() }}</strong>
                                <span>Mitglied{{ ($ov->members_count ?? $ov->members()->count()) === 1 ? '' : 'er' }}</span>
                            </div>
                            <div class="ortsverband-card-meta-item">
                                <span>📅</span>
                                <strong>{{ $ov->created_at->format('d.m.Y') }}</strong>
                            </div>
                        </div>

                        @if($ov->creator)
                        <p class="ortsverband-card-creator">👤 Gründer: <strong>{{ $ov->creator->name }}</strong></p>
                        @endif
                    </div>

                    <div class="ortsverband-card-actions">
                        <form method="POST" action="{{ route('admin.ortsverband.view-as', $ov) }}" style="width: 100%;">
                            @csrf
                            <button type="submit" class="btn-small btn-primary-small" style="width: 100%;">
                                👁️ Ansehen
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.ortsverband.destroy', $ov) }}" style="width: 100%;" onsubmit="return confirm('Wirklich löschen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-small btn-danger-small" style="width: 100%;">
                                🗑️ Löschen
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            @if($ortsverbande->hasPages())
            <div class="pagination">
                {{ $ortsverbande->links('pagination::simple-bootstrap-4') }}
            </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-state-icon">🏢</div>
                <div class="empty-state-title">Keine Ortsverbände vorhanden</div>
                <p>Es wurden noch keine Ortsverbände erstellt.</p>
            </div>
        @endif
    </div>
</div>
@endsection


