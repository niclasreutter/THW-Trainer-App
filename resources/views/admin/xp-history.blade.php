@extends('layouts.app')
@section('title', 'XP-Verlauf - THW Trainer Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title">XP-<span>Verlauf</span></h1>
        <p class="page-subtitle">Punkteverlauf von {{ $user->name }}</p>
    </header>

    <!-- Benutzer Info Card -->
    <div class="glass-gold" style="padding: 1.5rem; margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--gradient-gold); display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-person" style="font-size: 1.75rem; color: var(--thw-blue-dark);"></i>
            </div>
            <div style="flex: 1; min-width: 200px;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">{{ $user->name }}</h2>
                <div style="color: var(--text-secondary); margin-bottom: 0.5rem;">{{ $user->email }}</div>
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <span class="badge-gold">Level {{ $user->level }}</span>
                    <span class="badge-glass">{{ number_format($user->points) }} XP</span>
                    <span class="badge-glass">{{ $entries->total() }} Einträge</span>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn-ghost">
                <i class="bi bi-arrow-left"></i> Zurück zur Nutzerverwaltung
            </a>
        </div>
    </div>

    <!-- XP Verlauf Tabelle -->
    <div class="glass" style="padding: 1.5rem;">
        <div class="section-header" style="padding-left: 1rem; border-left: 3px solid var(--gold-start); margin-bottom: 1.5rem;">
            <h2 class="section-title">Alle XP-Einträge</h2>
        </div>

        @if($entries->isEmpty())
            <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                Noch keine XP-Einträge vorhanden.
            </div>
        @else
            <!-- Desktop Tabelle -->
            <div class="hidden md:block" style="overflow-x: auto;">
                <table class="table-glass">
                    <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Punkte</th>
                            <th>Grund</th>
                            <th>Quelle</th>
                            <th>Gesamt vorher</th>
                            <th>Gesamt nachher</th>
                            <th>Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entries as $entry)
                        <tr>
                            <td style="white-space: nowrap; color: var(--text-secondary);">
                                {{ $entry->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td>
                                <span style="font-weight: 700; color: var(--success);">+{{ $entry->points }}</span>
                            </td>
                            <td style="color: var(--text-primary);">
                                {{ $entry->reason }}
                            </td>
                            <td>
                                @if($entry->source_type === 'question')
                                    <span class="badge-glass">Frage</span>
                                @elseif($entry->source_type === 'exam')
                                    <span class="badge-gold">Prüfung</span>
                                @elseif($entry->source_type === 'lehrgang')
                                    <span class="badge-glass" style="border-color: var(--thw-blue-light);">Lehrgang</span>
                                @elseif($entry->source_type)
                                    <span class="badge-glass">{{ $entry->source_type }}</span>
                                @else
                                    <span style="color: var(--text-muted);">–</span>
                                @endif
                            </td>
                            <td style="color: var(--text-muted);">
                                {{ number_format($entry->total_before) }}
                            </td>
                            <td style="color: var(--text-secondary); font-weight: 600;">
                                {{ number_format($entry->total_after) }}
                            </td>
                            <td>
                                @if($entry->level_after > $entry->level_before)
                                    <span style="color: var(--success); font-weight: 700;">
                                        {{ $entry->level_before }} <i class="bi bi-arrow-right"></i> {{ $entry->level_after }}
                                    </span>
                                @else
                                    <span style="color: var(--text-muted);">{{ $entry->level_after }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Karten -->
            <div class="block md:hidden">
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($entries as $entry)
                    <div class="glass-subtle" style="padding: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $entry->created_at->format('d.m.Y H:i') }}</span>
                            <span style="font-weight: 700; color: var(--success);">+{{ $entry->points }} XP</span>
                        </div>
                        <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">{{ $entry->reason }}</div>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; font-size: 0.85rem;">
                            @if($entry->source_type === 'question')
                                <span class="badge-glass">Frage</span>
                            @elseif($entry->source_type === 'exam')
                                <span class="badge-gold">Prüfung</span>
                            @elseif($entry->source_type === 'lehrgang')
                                <span class="badge-glass" style="border-color: var(--thw-blue-light);">Lehrgang</span>
                            @elseif($entry->source_type)
                                <span class="badge-glass">{{ $entry->source_type }}</span>
                            @endif
                            <span style="color: var(--text-muted);">{{ number_format($entry->total_before) }} <i class="bi bi-arrow-right"></i> {{ number_format($entry->total_after) }} XP</span>
                            @if($entry->level_after > $entry->level_before)
                                <span style="color: var(--success); font-weight: 700;">Level {{ $entry->level_before }} <i class="bi bi-arrow-right"></i> {{ $entry->level_after }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            @if($entries->hasPages())
                <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
                    {{ $entries->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
