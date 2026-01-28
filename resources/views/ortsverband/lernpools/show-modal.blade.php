<!-- Show Modal - Glassmorphism Alpine.js -->
<div class="modal-header-glass">
    <h2>{{ $lernpool->name }}</h2>
    <button class="modal-close-btn" type="button">&times;</button>
</div>

<div class="modal-body-glass">
    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.5rem; line-height: 1.6;">{{ $lernpool->description }}</p>

    <!-- Statistiken Grid -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-bottom: 1.5rem;">
        <div class="glass-subtle" style="padding: 1rem; border-radius: 0.75rem; text-align: center;">
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary);">{{ $lernpool->getQuestionCount() }}</div>
            <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Fragen</div>
        </div>
        <div class="glass-subtle" style="padding: 1rem; border-radius: 0.75rem; text-align: center;">
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary);">{{ $lernpool->getEnrollmentCount() }}</div>
            <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Teilnehmer</div>
        </div>
        <div class="glass-subtle" style="padding: 1rem; border-radius: 0.75rem; text-align: center;">
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--gold-start);">{{ round($lernpool->getAverageProgress()) }}%</div>
            <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Fortschritt</div>
        </div>
        <div class="glass-subtle" style="padding: 1rem; border-radius: 0.75rem; text-align: center;">
            @if($lernpool->is_active)
                <span class="badge-success" style="font-size: 0.8rem;">Aktiv</span>
            @else
                <span class="badge-glass" style="font-size: 0.8rem;">Inaktiv</span>
            @endif
            <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; margin-top: 0.5rem; letter-spacing: 0.5px;">Status</div>
        </div>
    </div>

    <!-- Fragen Übersicht -->
    <div class="section-header" style="margin-bottom: 0.75rem; padding-left: 0.75rem;">
        <h3 class="section-title" style="font-size: 0.95rem;">Fragen ({{ count($questions) }})</h3>
    </div>

    @if($questions->isNotEmpty())
    <div class="glass-subtle" style="padding: 0.75rem; border-radius: 0.75rem; margin-bottom: 1.5rem; max-height: 200px; overflow-y: auto;">
        @foreach($questions as $question)
            @php
                $lernpoolId = $lernpool->id;
                $correctCount = \App\Models\OrtsverbandLernpoolProgress::whereHas('question', function($q) use ($lernpoolId) {
                    $q->where('lernpool_id', $lernpoolId);
                })
                ->where('question_id', $question->id)
                ->where('correct_attempts', '>', 0)
                ->distinct('user_id')
                ->count('DISTINCT user_id');

                $totalAttempts = \App\Models\OrtsverbandLernpoolProgress::where('question_id', $question->id)->sum('total_attempts');
                $totalUsers = $enrollments->count();
            @endphp
            <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 0.75rem; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.08); align-items: center;">
                <div style="color: var(--text-primary); font-size: 0.8rem; font-weight: 500;">{{ Str::limit($question->frage, 40) }}</div>
                <div style="text-align: right;">
                    <div style="font-size: 0.65rem; color: var(--text-muted);">Richtig</div>
                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-primary);">{{ $correctCount }}/{{ $totalUsers }}</div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 0.65rem; color: var(--text-muted);">Versuche</div>
                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-primary);">{{ $totalAttempts }}</div>
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="glass-subtle" style="padding: 1.25rem; border-radius: 0.75rem; text-align: center; margin-bottom: 1.5rem;">
        <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0;">Noch keine Fragen hinzugefügt</p>
    </div>
    @endif

    <!-- Teilnehmerliste -->
    <div class="section-header" style="margin-bottom: 0.75rem; padding-left: 0.75rem;">
        <h3 class="section-title" style="font-size: 0.95rem;">Teilnehmer ({{ $lernpool->getEnrollmentCount() }})</h3>
    </div>

    <div style="max-height: 200px; overflow-y: auto;">
        @forelse($enrollments as $enrollment)
            @php
                $user = $enrollment->user;
                $lernpoolId = $lernpool->id ?? null;
                $questionCount = $lernpool->getQuestionCount();

                $solvedCount = $user->lernpoolProgress()
                    ->whereHas('question', function($q) use ($lernpoolId) {
                        $q->where('lernpool_id', $lernpoolId);
                    })
                    ->where('solved', true)
                    ->count();
                $totalAttempts = $user->lernpoolProgress()
                    ->whereHas('question', function($q) use ($lernpoolId) {
                        $q->where('lernpool_id', $lernpoolId);
                    })
                    ->sum('total_attempts');
                $correctAttempts = $user->lernpoolProgress()
                    ->whereHas('question', function($q) use ($lernpoolId) {
                        $q->where('lernpool_id', $lernpoolId);
                    })
                    ->sum('correct_attempts');
                $successRate = $totalAttempts > 0 ? round(($correctAttempts / $totalAttempts) * 100) : 0;
                $progress = $questionCount > 0 ? round(($solvedCount / $questionCount) * 100) : 0;
            @endphp
            <div class="glass-subtle" style="padding: 0.75rem; border-radius: 0.625rem; margin-bottom: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 36px; height: 36px; background: var(--gradient-gold); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #1e3a5f; font-weight: 700; font-size: 0.85rem; flex-shrink: 0;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 600; font-size: 0.85rem; color: var(--text-primary);">{{ $user->name }}</div>
                        <div style="display: flex; gap: 1rem; font-size: 0.7rem; color: var(--text-secondary);">
                            <span>{{ $solvedCount }}/{{ $questionCount }} Fragen</span>
                            <span>{{ $successRate }}% Quote</span>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 1.1rem; font-weight: 700; color: var(--gold-start);">{{ $progress }}%</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="glass-subtle" style="padding: 1.25rem; border-radius: 0.75rem; text-align: center;">
                <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0;">Noch keine Teilnehmer</p>
            </div>
        @endforelse
    </div>
</div>

<div class="modal-footer-glass">
    <button type="button" class="btn-ghost modal-close-btn">Zurück</button>
    <a href="{{ route('ortsverband.lernpools.edit', [$ortsverband, $lernpool]) }}" class="btn-primary modal-trigger">Bearbeiten</a>
</div>
