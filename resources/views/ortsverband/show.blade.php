@extends('layouts.app')

@section('title', $ortsverband->name)

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Ortsverband <span>{{ $ortsverband->name }}</span></h1>
        @if($ortsverband->description)
            <p class="page-subtitle">{{ $ortsverband->description }}</p>
        @endif
    </header>

    @if($isAdminViewing)
    <div class="alert-compact glass-thw" style="margin-bottom: 1.5rem;">
        <i class="bi bi-eye alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">Admin-Ansicht</div>
            <div class="alert-compact-desc">Du betrachtest diesen Ortsverband als Admin.</div>
        </div>
        <form method="POST" action="{{ route('admin.ortsverband.exit-view') }}" style="margin: 0;">
            @csrf
            <button type="submit" class="btn-ghost btn-sm">Beenden</button>
        </form>
    </div>
    @endif

    @if(session('success'))
    <div class="alert-compact glass-success" style="margin-bottom: 1.5rem;">
        <i class="bi bi-check-circle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">{{ session('success') }}</div>
        </div>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.25rem;">&times;</button>
    </div>
    @endif

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon"><i class="bi bi-people-fill"></i></span>
            <div>
                <div class="stat-pill-value">{{ $ortsverband->members()->count() }}</div>
                <div class="stat-pill-label">Mitglieder</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-calendar-event"></i></span>
            <div>
                <div class="stat-pill-value">{{ $ortsverband->created_at->format('d.m.Y') }}</div>
                <div class="stat-pill-label">Gegründet</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-info"><i class="bi bi-person-badge"></i></span>
            <div>
                <div class="stat-pill-value">{{ $ortsverband->members()->wherePivot('role', 'ausbildungsbeauftragter')->count() }}</div>
                <div class="stat-pill-label">Ausbilder</div>
            </div>
        </div>
    </div>

    @php
        $userMember = $ortsverband->members()->where('user_id', auth()->id())->first();
        $userIsAusbilder = $userMember && $userMember->pivot->role === 'ausbildungsbeauftragter';
        $ausbilder = $ortsverband->members()->wherePivot('role', 'ausbildungsbeauftragter')->get();
    @endphp

    <!-- Bento Grid -->
    <div class="bento-grid-show">
        <!-- Lernpools Section (Main) -->
        <div class="glass-gold bento-lernpools">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                <div class="section-header" style="margin-bottom: 0; padding-left: 0; border-left: none;">
                    <h2 class="section-title" style="font-size: 1.25rem;">Lernpools</h2>
                </div>
                @if($userIsAusbilder)
                    <a href="{{ route('ortsverband.lernpools.index', $ortsverband) }}" class="btn-primary btn-sm">Verwalten</a>
                @endif
            </div>

            <!-- Tags Filter -->
            @if($allTags->isNotEmpty())
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.25rem;">
                <a href="{{ route('ortsverband.show', $ortsverband) }}"
                   class="{{ !$selectedTag ? 'btn-primary' : 'btn-ghost' }} btn-sm">
                    Alle
                </a>
                @foreach($allTags as $tag)
                    <a href="{{ route('ortsverband.show', ['ortsverband' => $ortsverband, 'tag' => $tag]) }}"
                       class="{{ $selectedTag === $tag ? 'btn-primary' : 'btn-ghost' }} btn-sm">
                        {{ $tag }}
                    </a>
                @endforeach
            </div>
            @endif

            @php
                $activeLernpools = $ortsverband->activeLernpools;
                if ($selectedTag) {
                    $activeLernpools = $activeLernpools->filter(function($pool) use ($selectedTag) {
                        return $pool->tags && in_array($selectedTag, $pool->tags);
                    });
                }
                $userEnrollments = auth()->user()->lernpoolEnrollments->pluck('lernpool_id')->toArray();
            @endphp

            @if($activeLernpools->count() > 0)
            <div class="lernpool-grid">
                @foreach($activeLernpools as $pool)
                    @php
                        $isEnrolled = in_array($pool->id, $userEnrollments);
                        $totalQuestions = $pool->getQuestionCount();
                        $enrollment = auth()->user()->lernpoolEnrollments()->where('lernpool_id', $pool->id)->first();
                        $progress = $enrollment ? $enrollment->getProgress() : 0;
                    @endphp
                    <div class="glass-subtle lernpool-card">
                        @if($isEnrolled)
                            <form action="{{ route('ortsverband.lernpools.unenroll', [$ortsverband, $pool]) }}" method="POST" style="position: absolute; top: 0.5rem; right: 0.5rem;">
                                @csrf
                                <button type="submit" class="btn-icon-leave" title="Verlassen" onclick="return confirm('Möchtest du diesen Lernpool wirklich verlassen?')">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        @endif

                        <h4 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem; padding-right: 2rem;">{{ $pool->name }}</h4>

                        @if($pool->tags && count($pool->tags) > 0)
                        <div style="display: flex; gap: 0.25rem; flex-wrap: wrap; margin-bottom: 0.5rem;">
                            @foreach($pool->tags as $tag)
                                <span class="badge-thw" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">{{ $tag }}</span>
                            @endforeach
                        </div>
                        @endif

                        <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.75rem; flex: 1; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ Str::limit($pool->description, 60) }}
                        </p>

                        <div style="display: flex; gap: 1rem; font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.75rem;">
                            <span>{{ $totalQuestions }} Fragen</span>
                            <span>{{ round($progress) }}%</span>
                        </div>

                        <div style="height: 4px; background: rgba(255,255,255,0.1); border-radius: 2px; margin-bottom: 0.75rem; overflow: hidden;">
                            <div style="height: 100%; background: var(--gradient-gold); width: {{ $progress }}%; border-radius: 2px;"></div>
                        </div>

                        @if($isEnrolled)
                            <a href="{{ route('ortsverband.lernpools.practice', [$ortsverband, $pool]) }}" class="btn-primary btn-sm" style="width: 100%;">
                                Weiter
                            </a>
                        @else
                            <form action="{{ route('ortsverband.lernpools.enroll', [$ortsverband, $pool]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-secondary btn-sm" style="width: 100%;">Beitreten</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
            @else
            <div class="empty-state" style="padding: 2rem;">
                <div class="empty-state-icon"><i class="bi bi-collection"></i></div>
                <h3 class="empty-state-title">Keine Lernpools</h3>
                <p class="empty-state-desc">{{ $selectedTag ? 'Keine Lernpools mit diesem Tag gefunden.' : 'Noch keine Lernpools verfügbar.' }}</p>
                @if($userIsAusbilder && !$selectedTag)
                    <a href="{{ route('ortsverband.lernpools.create', $ortsverband) }}" class="btn-primary btn-sm">Erstellen</a>
                @endif
            </div>
            @endif
        </div>

        <!-- Side Cards -->
        <div class="bento-side-stack">
            <!-- Ausbilder/Mitglieder Card -->
            <div class="glass-tl">
                <div class="section-header" style="margin-bottom: 1rem; padding-left: 0.75rem;">
                    <h3 class="section-title" style="font-size: 1rem;">{{ $userIsAusbilder ? 'Mitglieder' : 'Deine Ausbilder' }}</h3>
                </div>

                <div style="max-height: 200px; overflow-y: auto;">
                    @if($userIsAusbilder)
                        @foreach($ortsverband->members->take(5) as $member)
                        <div class="member-row">
                            <div class="member-avatar-sm">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 600; font-size: 0.85rem; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $member->name }}
                                </div>
                                <div style="font-size: 0.7rem; color: var(--text-muted);">
                                    {{ $member->pivot->role === 'ausbildungsbeauftragter' ? 'Ausbilder' : 'Mitglied' }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @if($ortsverband->members->count() > 5)
                        <a href="{{ route('ortsverband.members', $ortsverband) }}" class="btn-ghost btn-sm" style="width: 100%; margin-top: 0.5rem;">
                            Alle anzeigen
                        </a>
                        @endif
                    @else
                        @foreach($ausbilder as $member)
                        <div class="member-row">
                            <div class="member-avatar-sm">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 600; font-size: 0.85rem; color: var(--text-primary);">{{ $member->name }}</div>
                                <div style="font-size: 0.7rem; color: var(--text-muted);">Ausbilder</div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Rangliste (wenn sichtbar) -->
            @if($ortsverband->ranking_visible && $memberProgress)
            <div class="glass-br">
                <div class="section-header" style="margin-bottom: 1rem; padding-left: 0.75rem;">
                    <h3 class="section-title" style="font-size: 1rem;">Rangliste</h3>
                </div>

                <div style="max-height: 180px; overflow-y: auto;">
                    @foreach($memberProgress->take(5) as $index => $member)
                    <div class="ranking-row">
                        <div style="font-size: 1rem; min-width: 28px; font-weight: 700; text-align: center;
                            {{ $index === 0 ? 'color: #fbbf24;' : ($index === 1 ? 'color: #94a3b8;' : ($index === 2 ? 'color: #cd7f32;' : 'color: var(--text-muted);')) }}">
                            {{ $index + 1 }}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 600; font-size: 0.8rem; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $member['user']->name }}
                            </div>
                            <div style="font-size: 0.7rem; color: var(--text-muted);">{{ $member['theory_progress_percent'] }}%</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Info Card (Wide) -->
        <div class="glass-slash bento-info-wide">
            <div style="display: flex; align-items: start; gap: 1rem;">
                <div style="width: 40px; height: 40px; background: rgba(0, 51, 127, 0.15); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="bi bi-info-circle" style="font-size: 1.25rem; color: var(--thw-blue);"></i>
                </div>
                <div style="flex: 1;">
                    <h4 style="font-size: 0.9rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Was sehen Ausbilder?</h4>
                    <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0; line-height: 1.5;">
                        Theorie-Fortschritt, Prüfungs-Streak, Lern-Streak, Level & Punkte, letzte Aktivität und Schwachstellen.
                    </p>
                </div>
            </div>
        </div>

        <!-- Leave Card -->
        @php
            $currentMember = $ortsverband->members()->where('user_id', auth()->id())->first();
            $isAusbildungsbeauftragter = $currentMember && $currentMember->pivot->role === 'ausbildungsbeauftragter';
            $ausbilderCount = $ortsverband->members()->wherePivot('role', 'ausbildungsbeauftragter')->count();
            $canLeave = !$isAusbildungsbeauftragter || $ausbilderCount > 1;
        @endphp

        <div class="glass-subtle bento-leave" style="text-align: center;">
            @if($canLeave)
            <form action="{{ route('ortsverband.leave', $ortsverband) }}" method="POST"
                  onsubmit="return confirm('Möchtest du diesen Ortsverband wirklich verlassen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger btn-sm">Ortsverband verlassen</button>
            </form>
            @else
            <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0;">
                Du bist der einzige Ausbilder und kannst nicht verlassen.
            </p>
            @endif
        </div>
    </div>

    <!-- Back Link -->
    <div style="text-align: center; margin-top: 2rem;">
        <a href="{{ route('dashboard') }}" class="btn-ghost btn-sm">
            <i class="bi bi-arrow-left"></i> Zurück zum Dashboard
        </a>
    </div>
</div>

@push('styles')
<style>
    .bento-grid-show {
        display: grid;
        grid-template-columns: 2fr 1fr;
        grid-template-rows: auto auto auto;
        gap: 1rem;
    }

    .bento-lernpools {
        grid-row: span 2;
        padding: 1.5rem;
    }

    .bento-side-stack {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .bento-side-stack > div {
        padding: 1.25rem;
    }

    .bento-info-wide {
        grid-column: span 2;
        padding: 1.25rem;
    }

    .bento-leave {
        grid-column: span 2;
        padding: 1rem;
    }

    .lernpool-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.75rem;
    }

    .lernpool-card {
        position: relative;
        padding: 1rem;
        border-radius: 0.75rem;
        display: flex;
        flex-direction: column;
    }

    .btn-icon-leave {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: rgba(239, 68, 68, 0.15);
        border: none;
        color: #ef4444;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        transition: all 0.2s;
    }

    .btn-icon-leave:hover {
        background: rgba(239, 68, 68, 0.25);
    }

    .member-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem;
        border-radius: 0.5rem;
        margin-bottom: 0.25rem;
    }

    .member-row:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .member-avatar-sm {
        width: 32px;
        height: 32px;
        background: var(--gradient-gold);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1e3a5f;
        font-weight: 700;
        font-size: 0.8rem;
        flex-shrink: 0;
    }

    .ranking-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.4rem;
        border-radius: 0.5rem;
        margin-bottom: 0.25rem;
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
    .alert-compact-desc { font-size: 0.75rem; color: var(--text-secondary); }

    .empty-state {
        text-align: center;
    }

    .empty-state-icon {
        font-size: 2rem;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
        opacity: 0.6;
    }

    .empty-state-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .empty-state-desc {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
    }

    @media (max-width: 900px) {
        .bento-grid-show {
            grid-template-columns: 1fr;
        }
        .bento-lernpools { grid-row: span 1; }
        .bento-info-wide, .bento-leave { grid-column: span 1; }
    }
</style>
@endpush
@endsection
