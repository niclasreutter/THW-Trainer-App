@extends('layouts.app')

@section('title', $ortsverband->name . ' · Lernpools')

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
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 1.25rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
    }

    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
    }

    .stat-value {
        font-size: 2.25rem;
        font-weight: 800;
        color: #00337F;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #6b7280;
    }

    .info-card {
        background: white;
        padding: 2rem;
        border-radius: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
    }

    .info-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #00337F;
        margin: 0 0 1.5rem 0;
    }

    .button-group {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
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
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #00337F;
        border: 1px solid #e5e7eb;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .pool-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    .pool-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1.25rem;
        padding: 1.5rem;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
    }

    .pool-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 40px rgba(15, 23, 42, 0.12);
        border-color: #cbd5f5;
    }

    .pool-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .pool-card-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: #00337F;
        flex: 1;
    }

    .status-badge {
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
    }

    .status-active {
        background: rgba(34, 197, 94, 0.1);
        color: #15803d;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .status-inactive {
        background: rgba(107, 114, 128, 0.1);
        color: #374151;
        border: 1px solid rgba(107, 114, 128, 0.3);
    }

    .pool-card-desc {
        color: #4b5563;
        font-size: 0.95rem;
        margin: 0 0 1rem 0;
        line-height: 1.5;
    }

    .pool-card-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin: 1.25rem 0;
        padding: 1rem 0;
        border-top: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
    }

    .pool-stat {
        text-align: center;
    }

    .pool-stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #00337F;
        margin-bottom: 0.25rem;
    }

    .pool-stat-label {
        font-size: 0.8rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .pool-card-progress {
        margin: 1rem 0;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }

    .progress-bar-container {
        width: 100%;
        height: 8px;
        background: #e5e7eb;
        border-radius: 999px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #2563eb, #1e40af);
        border-radius: 999px;
        transition: width 0.3s ease;
    }

    .pool-card-actions {
        margin-top: auto;
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }

    .action-link {
        font-size: 0.9rem;
        font-weight: 600;
        color: #2563eb;
        text-decoration: none;
        transition: color 0.2s;
        padding: 0.35rem 0.75rem;
        border-radius: 0.5rem;
    }

    .action-link:hover {
        color: #1e40af;
        background: rgba(37, 99, 235, 0.08);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6b7280;
    }

    .empty-state p {
        font-size: 1.05rem;
        margin-bottom: 1.5rem;
    }

    /* Modal Styles */
    .modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 1000;
        animation: fadeIn 0.3s ease;
    }

    .modal-backdrop.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.15);
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideUp 0.3s ease;
        position: relative;
    }

    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
        padding: 2rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
        color: #00337F;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6b7280;
        transition: color 0.2s;
        padding: 0;
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close:hover {
        color: #1f2937;
    }

    .modal-body {
        padding: 2rem;
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

    .form-checkbox {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .form-checkbox input {
        width: 1.25rem;
        height: 1.25rem;
        cursor: pointer;
    }

    .form-checkbox label {
        font-size: 0.95rem;
        font-weight: 600;
        color: #00337F;
        cursor: pointer;
        margin: 0;
    }

    .form-error {
        color: #dc2626;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }

    .modal-footer {
        padding: 1.5rem 2rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .modal-footer .btn {
        flex: 1;
        min-width: 120px;
        justify-content: center;
    }

    .btn-modal-close {
        background: #f3f4f6;
        color: #00337F;
        border: 1px solid #e5e7eb;
    }

    .btn-modal-close:hover {
        background: #e5e7eb;
    }

    @media (max-width: 480px) {
        .dashboard-container { padding: 1rem; }
        .info-card { padding: 1.25rem; }
        .dashboard-greeting { font-size: 1.75rem; }
        .pool-grid { grid-template-columns: 1fr; }
        .pool-card-stats { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-greeting">📚 <span>Lernpools</span></h1>
            <p class="dashboard-subtitle">Verwalte Lernpools für {{ $ortsverband->name }}</p>
        </div>

        <!-- Schnellstatistiken -->
        @php
            $activePools = $lernpools->where('is_active', true)->count();
            $totalParticipants = $lernpools->sum(fn($pool) => $pool->getEnrollmentCount());
            $avgProgress = $lernpools->count() ? round($lernpools->avg(fn($pool) => $pool->getAverageProgress())) : 0;
        @endphp

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-value">{{ $lernpools->count() }}</div>
                <div class="stat-label">Lernpools</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-value">{{ $activePools }}</div>
                <div class="stat-label">Aktiv</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value">{{ $totalParticipants }}</div>
                <div class="stat-label">Lernende</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">📈</div>
                <div class="stat-value">{{ $avgProgress }}%</div>
                <div class="stat-label">Ø Fortschritt</div>
            </div>
        </div>

        <!-- Lernpools verwaltbar -->
        <div class="info-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 class="info-title" style="margin: 0;">Alle Lernpools</h2>
                <span style="color: #6b7280; font-weight: 600;">{{ $lernpools->count() }} Pools</span>
            </div>

            <div class="button-group">
                <button id="openCreateModal" class="btn btn-primary">
                    ➕ Neuer Lernpool
                </button>
                <a href="{{ route('ortsverband.show', $ortsverband) }}" class="btn btn-secondary">
                    ← Zurück zum Ortsverband
                </a>
            </div>

            @if($lernpools->count() > 0)
                <div class="pool-grid">
                    @foreach($lernpools as $pool)
                        @php
                            $progress = round($pool->getAverageProgress());
                            $enrollments = $pool->getEnrollmentCount();
                            $questions = $pool->getQuestionCount();
                        @endphp
                        <div class="pool-card">
                            <div class="pool-card-header">
                                <h3>{{ $pool->name }}</h3>
                                <span class="status-badge {{ $pool->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $pool->is_active ? '🟢 Aktiv' : '⚫ Inaktiv' }}
                                </span>
                            </div>

                            <p class="pool-card-desc">{{ Str::limit($pool->description, 120) }}</p>

                            <div class="pool-card-stats">
                                <div class="pool-stat">
                                    <div class="pool-stat-value">{{ $questions }}</div>
                                    <div class="pool-stat-label">Fragen</div>
                                </div>
                                <div class="pool-stat">
                                    <div class="pool-stat-value">{{ $enrollments }}</div>
                                    <div class="pool-stat-label">Teilnehmer</div>
                                </div>
                                <div class="pool-stat">
                                    <div class="pool-stat-value">{{ $progress }}%</div>
                                    <div class="pool-stat-label">Fortschritt</div>
                                </div>
                            </div>

                            <div class="pool-card-progress">
                                <div class="progress-label">
                                    <span>Durchschnittlicher Lernfortschritt</span>
                                    <strong>{{ $progress }}%</strong>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            <div class="pool-card-actions">
                                <a href="{{ route('ortsverband.lernpools.show', [$ortsverband, $pool]) }}" class="action-link modal-trigger" data-modal-type="show">
                                    👁️ Details
                                </a>
                                <a href="{{ route('ortsverband.lernpools.edit', [$ortsverband, $pool]) }}" class="action-link modal-trigger" data-modal-type="edit">
                                    ✏️ Bearbeiten
                                </a>
                                <a href="{{ route('ortsverband.lernpools.questions.index', [$ortsverband, $pool]) }}" class="action-link modal-trigger" data-modal-type="questions">
                                    ❓ Fragen
                                </a>
                                <form action="{{ route('ortsverband.lernpools.destroy', [$ortsverband, $pool]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Lernpool wirklich löschen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-link" style="background: none; border: none; padding: 0; cursor: pointer; color: #dc2626;">
                                        🗑️ Löschen
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>📭 Noch keine Lernpools erstellt</p>
                    <a href="{{ route('ortsverband.lernpools.create', $ortsverband) }}" class="btn btn-primary">
                        ➕ Neuen Lernpool erstellen
                    </a>
                </div>
            @endif
        </div>

<!-- Modal für neuen Lernpool -->
<div id="createModal" class="modal-backdrop">
    <div class="modal">
        <div class="modal-header">
            <h2>Neuer Lernpool</h2>
            <button id="closeCreateModal" class="modal-close">✕</button>
        </div>
        <form id="createLernpoolForm" action="{{ route('ortsverband.lernpools.store', $ortsverband) }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="name" class="form-label">
                        Name <span class="required">*</span>
                    </label>
                    <input type="text" name="name" id="name" class="form-input" 
                           placeholder="z.B. Grundlagen Erste Hilfe" required>
                    <p class="form-error" id="nameError" style="display: none;"></p>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">
                        Beschreibung <span class="required">*</span>
                    </label>
                    <textarea name="description" id="description" rows="5" class="form-textarea" 
                              placeholder="Beschreibung des Lernpools..." required></textarea>
                    <p class="form-error" id="descriptionError" style="display: none;"></p>
                </div>

                <div class="form-group">
                    <div class="form-checkbox">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                        <label for="is_active">Sofort aktivieren</label>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" id="cancelCreateModal" class="btn btn-modal-close">
                    Abbrechen
                </button>
                <button type="submit" class="btn btn-primary">
                    ✓ Erstellen
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('createModal');
        const openBtn = document.getElementById('openCreateModal');
        const closeBtn = document.getElementById('closeCreateModal');
        const cancelBtn = document.getElementById('cancelCreateModal');
        const form = document.getElementById('createLernpoolForm');

        // Open Modal
        openBtn.addEventListener('click', function() {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        // Close Modal
        function closeModal() {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
            form.reset();
            document.getElementById('nameError').style.display = 'none';
            document.getElementById('descriptionError').style.display = 'none';
        }

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // Close on backdrop click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                closeModal();
            }
        });

        // Form submission - just submit normally (page will reload on redirect)
        form.addEventListener('submit', function(e) {
            // Allow normal form submission - no preventDefault
            // Laravel will redirect after successful creation
        });
    });

    // Generic Modal Loader für Show/Edit/Fragen
    document.addEventListener('DOMContentLoaded', function() {
        const genericModal = document.getElementById('genericModal');
        const genericModalBackdrop = document.getElementById('genericModalBackdrop');
        const closeGenericModalBtn = document.getElementById('closeGenericModal');
        const cancelGenericModalBtn = document.getElementById('cancelGenericModal');
        const modalTriggers = document.querySelectorAll('.modal-trigger');

        function closeGenericModal() {
            genericModalBackdrop.classList.remove('active');
            document.body.style.overflow = 'auto';
            genericModal.innerHTML = '';
        }

        closeGenericModalBtn.addEventListener('click', closeGenericModal);
        cancelGenericModalBtn.addEventListener('click', closeGenericModal);

        genericModalBackdrop.addEventListener('click', function(e) {
            if (e.target === genericModalBackdrop) {
                closeGenericModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && genericModalBackdrop.classList.contains('active')) {
                closeGenericModal();
            }
        });

        // Use event delegation for modal triggers (für dynamisch geladene Inhalte)
        document.addEventListener('click', function(e) {
            const trigger = e.target.closest('.modal-trigger');
            if (trigger) {
                e.preventDefault();
                const url = trigger.href;
                
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    genericModal.innerHTML = html;
                    genericModalBackdrop.classList.add('active');
                    document.body.style.overflow = 'hidden';
                })
                .catch(error => {
                    console.error('Error loading modal:', error);
                    genericModal.innerHTML = '<div class="modal"><div class="modal-header"><h2>Fehler</h2><button class="modal-close" onclick="this.closest(\'#genericModalBackdrop\').classList.remove(\'active\')">✕</button></div><div class="modal-body"><p>Fehler beim Laden des Inhalts.</p></div></div>';
                    genericModalBackdrop.classList.add('active');
                });
            }
        });
    });
</script>

<!-- Generisches Modal für Show/Edit/Fragen -->
<div id="genericModalBackdrop" class="modal-backdrop">
    <div id="genericModal"></div>
</div>

@endsection
