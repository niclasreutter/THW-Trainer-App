<!-- Modal Format (für AJAX) -->
<style>
    .tab-buttons {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .tab-btn {
        padding: 0.75rem 1rem;
        border: none;
        background: none;
        cursor: pointer;
        font-weight: 600;
        color: #6b7280;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        transition: all 0.3s ease;
    }
    
    .tab-btn.active {
        color: #00337F;
        border-bottom-color: #00337F;
    }
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .user-stats-item {
        background: #f9fafb;
        padding: 1rem;
        border-radius: 0.75rem;
        margin-bottom: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
        user-select: none;
    }
    
    .user-stats-item:hover {
        background: #f3f4f6;
        transform: translateY(-2px);
    }
    
    .user-stats-item.expanded {
        background: white;
        border: 1px solid #d1d5db;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .user-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .user-stat-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
    }
    
    .stat-label {
        color: #6b7280;
    }
    
    .stat-value {
        font-weight: 600;
        color: #00337F;
    }
    
    .user-details-content {
        display: none;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .user-details-content.visible {
        display: block;
    }
    
    .detail-section {
        background: #f0f9ff;
        padding: 1rem;
        border-radius: 0.75rem;
        border: 1px solid #bae6fd;
    }
    
    .detail-section h4 {
        font-weight: 600;
        color: #0369a1;
        margin-bottom: 1rem;
        margin-top: 0;
    }
</style>

<div class="modal-header">
    <h2>{{ $lernpool->name }}</h2>
    <button class="modal-close" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">✕</button>
</div>

<div class="modal-body">
    <p class="text-sm text-gray-600 mb-4">{{ $lernpool->description }}</p>

    <!-- Statistiken -->
    <div class="grid grid-cols-2 gap-3 mb-6">
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs font-medium text-gray-600">Gesamt Fragen</p>
            <p class="text-2xl font-bold text-blue-600">{{ $lernpool->getQuestionCount() }}</p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs font-medium text-gray-600">Teilnehmer</p>
            <p class="text-2xl font-bold text-green-600">{{ $lernpool->getEnrollmentCount() }}</p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs font-medium text-gray-600">Ø Fortschritt</p>
            <p class="text-2xl font-bold text-yellow-600">{{ round($lernpool->getAverageProgress()) }}%</p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs font-medium text-gray-600">Status</p>
            <p class="text-lg font-bold {{ $lernpool->is_active ? 'text-green-600' : 'text-gray-600' }}">
                {{ $lernpool->is_active ? '✓ Aktiv' : '✗ Inaktiv' }}
            </p>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="tab-buttons">
        <button class="tab-btn active" onclick="switchTab(event, 'overview')">📚 Übersicht</button>
    </div>

    <!-- Tab: Übersicht -->
    <div id="overview" class="tab-content active">
        <!-- Fragenübersicht -->
        <h3 style="font-weight: 600; color: #1f2937; margin-bottom: 1rem; font-size: 0.95rem;">📚 Lernabschnitte</h3>
        <div class="space-y-3 mb-6">
            @forelse($questionsBySection as $section => $sectionQuestions)
                <div>
                    <h4 class="font-semibold text-sm text-gray-800 mb-1">{{ $section }}</h4>
                    <p class="text-xs text-gray-600">{{ count($sectionQuestions) }} Fragen</p>
                </div>
            @empty
                <p class="text-gray-600 text-sm">Noch keine Fragen hinzugefügt</p>
            @endforelse
        </div>

        <!-- Teilnehmerliste -->
        <h3 style="font-weight: 600; color: #1f2937; margin-bottom: 1rem; font-size: 0.95rem;">👥 Teilnehmer ({{ $lernpool->getEnrollmentCount() }})</h3>
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
                $enrolledDate = $enrollment->created_at->format('d.m.Y');
                $itemId = 'user-' . $user->id;
            @endphp
            <div class="user-stats-item" onclick="toggleUserStats(this, '{{ $itemId }}')">
                <div class="user-name">👤 {{ $user->name }} <span style="color: #9ca3af; font-size: 0.8rem;">▼</span></div>
                <div class="user-stat-row">
                    <span class="stat-label">Gemeisterte Fragen:</span>
                    <span class="stat-value">{{ $solvedCount }}/{{ $questionCount }}</span>
                </div>
                <div class="user-stat-row">
                    <span class="stat-label">Erfolgsquote:</span>
                    <span class="stat-value">{{ $successRate }}%</span>
                </div>
                <div class="user-stat-row">
                    <span class="stat-label">Angemeldet:</span>
                    <span class="stat-value">{{ $enrolledDate }}</span>
                </div>

                <!-- Detaillierte Statistiken (versteckt) -->
                <div class="user-details-content" id="{{ $itemId }}">
                    <div class="detail-section">
                        <h4>📊 Detaillierte Statistiken</h4>
                        
                        <div class="user-stat-row" style="margin-bottom: 0.75rem;">
                            <span class="stat-label">Gesamt Versuche:</span>
                            <span class="stat-value">{{ $totalAttempts }}</span>
                        </div>
                        <div class="user-stat-row" style="margin-bottom: 0.75rem;">
                            <span class="stat-label">Richtige Versuche:</span>
                            <span class="stat-value">{{ $correctAttempts }}</span>
                        </div>
                        <div class="user-stat-row" style="margin-bottom: 0.75rem;">
                            <span class="stat-label">Erfolgsquote:</span>
                            <span class="stat-value">{{ $successRate }}%</span>
                        </div>
                        <div class="user-stat-row" style="margin-bottom: 0.75rem;">
                            <span class="stat-label">Gemeisterte Fragen:</span>
                            <span class="stat-value">{{ $solvedCount }}/{{ $questionCount }}</span>
                        </div>
                        <div class="user-stat-row">
                            <span class="stat-label">Gesamtfortschritt:</span>
                            <span class="stat-value">{{ round(($solvedCount / $questionCount) * 100) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-600 text-sm">Noch keine Teilnehmer</p>
        @endforelse
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-modal-close" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">Zurück</button>
    <a href="{{ route('ortsverband.lernpools.edit', [$ortsverband, $lernpool]) }}" class="btn btn-primary modal-trigger" data-modal-type="edit">✏️ Bearbeiten</a>
</div>

<script>
function toggleUserStats(element, detailId) {
    const detailsContent = document.getElementById(detailId);
    
    if (!detailsContent) return;
    
    // Toggle Sichtbarkeit
    const isVisible = detailsContent.classList.contains('visible');
    
    // Alle anderen Details verstecken
    document.querySelectorAll('.user-details-content').forEach(d => d.classList.remove('visible'));
    document.querySelectorAll('.user-stats-item').forEach(s => s.classList.remove('expanded'));
    
    // Aktuelles Element expandieren
    if (!isVisible) {
        detailsContent.classList.add('visible');
        element.classList.add('expanded');
    }
}
</script>
