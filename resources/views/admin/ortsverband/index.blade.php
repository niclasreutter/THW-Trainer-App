@extends('layouts.app')

@section('title', 'Admin - Ortsverbände')
@section('description', 'Admin-Panel: Verwalte alle Ortsverbände')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Ortsverbände <span>Verwaltung</span></h1>
        <p class="page-subtitle">Admin-Übersicht aller THW-Ortsverbände</p>
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

    @if($ortsverbande->count() > 0)
        <div class="glass hover-lift" style="padding: 1.5rem;">
            <h2 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-building text-gold"></i>
                Alle Ortsverbände ({{ $ortsverbande->total() }})
            </h2>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($ortsverbande as $ov)
                    <div class="glass" style="padding: 1.25rem;">
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <!-- Header Section -->
                            <div>
                                <h3 style="margin: 0 0 0.5rem 0; font-size: 1.1rem; font-weight: 700;">{{ $ov->name }}</h3>
                                @if($ov->description)
                                    <p style="margin: 0 0 0.75rem 0; color: var(--text-secondary); font-size: 0.9rem;">{{ $ov->description }}</p>
                                @endif

                                <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.9rem; color: var(--text-secondary);">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="bi bi-people"></i>
                                        <strong style="color: var(--text-primary);">{{ $ov->members_count ?? $ov->members()->count() }}</strong>
                                        Mitglied{{ ($ov->members_count ?? $ov->members()->count()) === 1 ? '' : 'er' }}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="bi bi-calendar"></i>
                                        <strong style="color: var(--text-primary);">{{ $ov->created_at->format('d.m.Y') }}</strong>
                                    </div>
                                </div>

                                @if($ov->creator)
                                    <p style="margin: 0.5rem 0 0 0; font-size: 0.85rem; color: var(--text-muted);">
                                        Gründer: <strong style="color: var(--text-secondary);">{{ $ov->creator->name }}</strong>
                                    </p>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <form method="POST" action="{{ route('admin.ortsverband.view-as', $ov) }}" style="flex: 1 1 auto; min-width: 120px;">
                                    @csrf
                                    <button type="submit" class="btn-secondary" style="width: 100%; justify-content: center;">
                                        <i class="bi bi-eye"></i> Ansehen
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.ortsverband.destroy', $ov) }}" style="flex: 1 1 auto; min-width: 120px;" onsubmit="return confirm('Wirklich löschen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost" style="width: 100%; justify-content: center; color: var(--error);">
                                        <i class="bi bi-trash"></i> Löschen
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($ortsverbande->hasPages())
                <div style="margin-top: 2rem;">
                    {{ $ortsverbande->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="glass hover-lift" style="padding: 3rem 1rem; text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 1rem; color: var(--text-muted);"><i class="bi bi-building"></i></div>
            <p style="font-size: 1.05rem; color: var(--text-muted); margin-bottom: 1.5rem;">Keine Ortsverbände vorhanden</p>
            <p style="color: var(--text-secondary); margin: 0;">Es wurden noch keine Ortsverbände erstellt.</p>
        </div>
    @endif
</div>
@endsection


