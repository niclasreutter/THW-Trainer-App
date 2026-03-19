@extends('layouts.app')
@section('title', 'Statistiken')

@section('content')
<div class="dash-container">

    {{-- Header --}}
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Fortschritt</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Statistiken</h1>
        <p class="text-sm" style="color: var(--text-muted);">Dein detaillierter Lernfortschritt</p>
    </div>

    {{-- Overview Stats --}}
    <div class="flex gap-3 mb-6">
        <div class="gami-pill">
            <div class="gami-pill__value gami-pill__value--blue">{{ $progressPercent }}%</div>
            <div class="gami-pill__label">Fortschritt</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value">{{ $solvedTotal }}/{{ $totalQuestions }}</div>
            <div class="gami-pill__label">Gelöst</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value gami-pill__value--blue">{{ $hitRate }}%</div>
            <div class="gami-pill__label">Trefferquote</div>
        </div>
    </div>

    {{-- Desktop 2-column --}}
    <div class="dash-grid">
        {{-- Main: Section Analysis --}}
        <div class="space-y-4">
            <div class="glass p-4" style="border-radius: 0.75rem;">
                <div class="text-xs uppercase tracking-wider mb-3" style="color: var(--text-muted);">
                    Lernabschnitte
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($sectionStats as $section)
                        <a href="{{ route('practice.section', $section['section']) }}"
                           class="stat-section-card block" style="text-decoration: none;">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-semibold" style="color: var(--text-primary);">
                                    Abschnitt {{ $section['section'] }}
                                </span>
                                <span class="text-xs font-bold" style="color: {{ $section['percent'] > 80 ? 'var(--success)' : ($section['percent'] >= 50 ? '#5b9aff' : 'var(--error)') }};">
                                    {{ $section['percent'] }}%
                                </span>
                            </div>
                            <div class="stat-section-card__bar">
                                <div class="h-full rounded-sm transition-all {{ $section['percent'] > 80 ? 'stat-section-card__fill--green' : ($section['percent'] >= 50 ? 'stat-section-card__fill--blue' : 'stat-section-card__fill--red') }}"
                                     style="width: {{ $section['percent'] }}%;"></div>
                            </div>
                            <div class="flex justify-between mt-2">
                                <span class="text-xs" style="color: var(--text-muted);">
                                    {{ $section['mastered'] }}/{{ $section['total'] }} gemeistert
                                </span>
                                <span class="text-xs" style="color: var(--text-muted);">
                                    {{ $section['hit_rate'] }}% Trefferquote
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            {{-- Activity Chart --}}
            <div class="glass p-4" style="border-radius: 0.75rem;" x-data="{ view: 'week' }">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-xs uppercase tracking-wider" style="color: var(--text-muted);">Aktivität</span>
                    <div class="flex gap-1">
                        <button @click="view = 'week'"
                                :class="view === 'week' ? 'font-semibold' : ''"
                                class="text-xs px-2 py-1 rounded"
                                :style="view === 'week' ? 'color: #5b9aff; background: rgba(0,85,204,0.15);' : 'color: var(--text-muted);'">
                            Woche
                        </button>
                        <button @click="view = 'month'"
                                :class="view === 'month' ? 'font-semibold' : ''"
                                class="text-xs px-2 py-1 rounded"
                                :style="view === 'month' ? 'color: #5b9aff; background: rgba(0,85,204,0.15);' : 'color: var(--text-muted);'">
                            Monat
                        </button>
                    </div>
                </div>

                {{-- Weekly Barchart --}}
                <div x-show="view === 'week'">
                    @php
                        $days = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
                        $maxWeekCount = $weeklyActivity->max('count') ?: 1;
                    @endphp
                    <div class="activity-bar" style="height: 120px;">
                        @for($d = 0; $d < 7; $d++)
                            @php
                                $date = now()->startOfWeek()->addDays($d)->format('Y-m-d');
                                $dayData = $weeklyActivity->firstWhere('date', $date);
                                $count = $dayData->count ?? 0;
                                $heightPct = $maxWeekCount > 0 ? max(($count / $maxWeekCount) * 100, 3) : 3;
                                $isToday = $date === now()->format('Y-m-d');
                            @endphp
                            <div class="activity-bar__col">
                                <span class="activity-bar__count">{{ $count ?: '' }}</span>
                                <div class="activity-bar__fill {{ $isToday ? 'activity-bar__fill--today' : ($count === 0 ? 'activity-bar__fill--empty' : '') }}"
                                     style="height: {{ $count > 0 ? $heightPct : 8 }}%;"></div>
                                <span class="activity-bar__day {{ $isToday ? 'activity-bar__day--today' : '' }}">
                                    {{ $days[$d] }}
                                </span>
                            </div>
                        @endfor
                    </div>
                </div>

                {{-- Monthly Heatmap --}}
                <div x-show="view === 'month'" x-cloak>
                    @php
                        $maxDayCount = $activity->max('count') ?: 1;
                        $hasAnyActivity = $activity->sum('count') > 0;
                    @endphp
                    @if(!$hasAnyActivity)
                        <p class="text-sm text-center py-6" style="color: var(--text-muted);">
                            Starte mit deiner ersten Frage
                        </p>
                    @else
                        <div class="heatmap-grid">
                            @for($d = 29; $d >= 0; $d--)
                                @php
                                    $date = now()->subDays($d)->format('Y-m-d');
                                    $dayData = $activity->get($date);
                                    $count = $dayData->count ?? 0;
                                    $level = $count === 0 ? '' : ($count <= $maxDayCount * 0.25 ? 'heatmap-cell--l1' : ($count <= $maxDayCount * 0.5 ? 'heatmap-cell--l2' : ($count <= $maxDayCount * 0.75 ? 'heatmap-cell--l3' : 'heatmap-cell--l4')));
                                @endphp
                                <div class="heatmap-cell {{ $level }}" title="{{ $date }}: {{ $count }} Fragen"></div>
                            @endfor
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="text-xs" style="color: var(--text-muted);">{{ now()->subDays(29)->format('d.m.') }}</span>
                            <span class="text-xs" style="color: var(--text-muted);">{{ now()->format('d.m.') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Exam History --}}
            <div class="glass p-4" style="border-radius: 0.75rem;">
                <div class="text-xs uppercase tracking-wider mb-3" style="color: var(--text-muted);">
                    Prüfungshistorie
                </div>
                @if($examHistory->isEmpty())
                    <p class="text-sm text-center py-4" style="color: var(--text-muted);">
                        Noch keine Prüfungen abgelegt
                    </p>
                @else
                    <div class="space-y-2">
                        @foreach($examHistory->take(10) as $index => $exam)
                            @php
                                $pct = round(($exam->correct_answers / 40) * 100);
                                $prevExam = $examHistory->get($index + 1);
                                $prevPct = $prevExam ? round(($prevExam->correct_answers / 40) * 100) : null;
                            @endphp
                            <div class="flex items-center gap-3 p-2 rounded-lg" style="background: var(--glass-bg);">
                                <span class="text-xs" style="color: var(--text-muted); min-width: 60px;">
                                    {{ $exam->created_at->format('d.m.Y') }}
                                </span>
                                <div class="flex-1 h-1 rounded-full" style="background: rgba(255,255,255,0.1);">
                                    <div class="h-full rounded-full" style="background: {{ $exam->is_passed ? 'var(--success)' : 'var(--error)' }}; width: {{ $pct }}%;"></div>
                                </div>
                                <span class="text-sm font-bold" style="color: {{ $exam->is_passed ? 'var(--success)' : 'var(--error)' }}; min-width: 35px;">
                                    {{ $pct }}%
                                </span>
                                @if($prevPct !== null)
                                    <span class="text-xs" style="color: {{ $pct > $prevPct ? 'var(--success)' : ($pct < $prevPct ? 'var(--error)' : 'var(--text-muted)') }};">
                                        @if($pct > $prevPct) ↑ @elseif($pct < $prevPct) ↓ @else → @endif
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Spaced Repetition --}}
            <div class="glass p-4" style="border-radius: 0.75rem;">
                <div class="text-xs uppercase tracking-wider mb-3" style="color: var(--text-muted);">
                    Spaced Repetition
                </div>
                <div class="flex gap-3 mb-3">
                    <div class="text-center flex-1">
                        <div class="text-xl font-bold" style="color: #5b9aff;">{{ $srStats['due_now'] }}</div>
                        <div class="text-xs" style="color: var(--text-muted);">Heute fällig</div>
                    </div>
                    <div class="text-center flex-1">
                        <div class="text-xl font-bold" style="color: var(--text-primary);">{{ $srStats['due_this_week'] }}</div>
                        <div class="text-xs" style="color: var(--text-muted);">Diese Woche</div>
                    </div>
                    <div class="text-center flex-1">
                        <div class="text-xl font-bold" style="color: var(--text-primary);">{{ $masteryPercent }}%</div>
                        <div class="text-xs" style="color: var(--text-muted);">Gemeistert</div>
                    </div>
                </div>
                @if($intervalDistribution->isNotEmpty())
                    <div class="text-xs uppercase tracking-wider mb-2 mt-4" style="color: var(--text-muted);">
                        Review-Intervalle
                    </div>
                    <div class="space-y-1">
                        @foreach($intervalDistribution->take(5) as $interval)
                            <div class="flex justify-between text-xs">
                                <span style="color: var(--text-secondary);">{{ $interval->review_interval }} Tage</span>
                                <span style="color: var(--text-muted);">{{ $interval->count }} Fragen</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
