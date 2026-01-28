@extends('layouts.app')

@section('title', $ortsverband->name . ' · Lernpools')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Lernpools <span>verwalten</span></h1>
        <p class="page-subtitle">{{ $ortsverband->name }}</p>
    </header>

    <!-- Stats Row -->
    @php
        $activePools = $lernpools->where('is_active', true)->count();
        $totalParticipants = $lernpools->sum(fn($pool) => $pool->getEnrollmentCount());
        $avgProgress = $lernpools->count() ? round($lernpools->avg(fn($pool) => $pool->getAverageProgress())) : 0;
    @endphp

    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon"><i class="bi bi-collection"></i></span>
            <div>
                <div class="stat-pill-value">{{ $lernpools->count() }}</div>
                <div class="stat-pill-label">Lernpools</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-success"><i class="bi bi-check-circle"></i></span>
            <div>
                <div class="stat-pill-value">{{ $activePools }}</div>
                <div class="stat-pill-label">Aktiv</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-info"><i class="bi bi-people"></i></span>
            <div>
                <div class="stat-pill-value">{{ $totalParticipants }}</div>
                <div class="stat-pill-label">Lernende</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-graph-up"></i></span>
            <div>
                <div class="stat-pill-value">{{ $avgProgress }}%</div>
                <div class="stat-pill-label">Fortschritt</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="glass-gold" style="padding: 1.5rem; border-radius: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
            <div class="section-header" style="margin-bottom: 0; padding-left: 0; border-left: none;">
                <h2 class="section-title" style="font-size: 1.25rem;">Alle Lernpools</h2>
            </div>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <button id="openCreateModal" class="btn-primary btn-sm">Neuer Lernpool</button>
                <a href="{{ route('ortsverband.show', $ortsverband) }}" class="btn-ghost btn-sm">Zurück</a>
            </div>
        </div>

        <!-- Tags Filter -->
        @if($allTags->isNotEmpty())
        <div class="glass-subtle" style="padding: 0.75rem 1rem; border-radius: 0.75rem; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <span style="font-weight: 600; color: var(--text-secondary); font-size: 0.85rem;">Filter:</span>
                <a href="{{ route('ortsverband.lernpools.index', $ortsverband) }}"
                   class="{{ !$selectedTag ? 'btn-primary' : 'btn-ghost' }} btn-sm">
                    Alle
                </a>
                @foreach($allTags as $tag)
                    <a href="{{ route('ortsverband.lernpools.index', ['ortsverband' => $ortsverband, 'tag' => $tag]) }}"
                       class="{{ $selectedTag === $tag ? 'btn-primary' : 'btn-ghost' }} btn-sm">
                        {{ $tag }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Pool Grid -->
        @if($lernpools->count() > 0)
        <div class="pool-grid">
            @foreach($lernpools as $pool)
                @php
                    $progress = round($pool->getAverageProgress());
                    $enrollments = $pool->getEnrollmentCount();
                    $questions = $pool->getQuestionCount();
                @endphp
                <div class="glass-subtle pool-card">
                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin: 0;">{{ $pool->name }}</h3>
                        @if($pool->is_active)
                            <span class="badge-success" style="font-size: 0.65rem;">Aktiv</span>
                        @else
                            <span class="badge-glass" style="font-size: 0.65rem;">Inaktiv</span>
                        @endif
                    </div>

                    @if($pool->tags && count($pool->tags) > 0)
                    <div style="display: flex; gap: 0.25rem; flex-wrap: wrap; margin-bottom: 0.75rem;">
                        @foreach($pool->tags as $tag)
                            <span class="badge-thw" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">{{ $tag }}</span>
                        @endforeach
                    </div>
                    @endif

                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ Str::limit($pool->description, 100) }}
                    </p>

                    <!-- Stats -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; padding: 0.75rem 0; border-top: 1px solid rgba(255,255,255,0.1); border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 0.75rem;">
                        <div style="text-align: center;">
                            <div style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary);">{{ $questions }}</div>
                            <div style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase;">Fragen</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary);">{{ $enrollments }}</div>
                            <div style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase;">Teilnehmer</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.25rem; font-weight: 800; color: var(--gold-start);">{{ $progress }}%</div>
                            <div style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase;">Fortschritt</div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div style="margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">
                            <span>Lernfortschritt</span>
                            <span>{{ $progress }}%</span>
                        </div>
                        <div style="height: 4px; background: rgba(255,255,255,0.1); border-radius: 2px; overflow: hidden;">
                            <div style="height: 100%; background: var(--gradient-gold); width: {{ $progress }}%; border-radius: 2px;"></div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; padding-top: 0.75rem; border-top: 1px solid rgba(255,255,255,0.1);">
                        <a href="{{ route('ortsverband.lernpools.show', [$ortsverband, $pool]) }}" class="action-link modal-trigger" data-modal-type="show">
                            Details
                        </a>
                        <a href="{{ route('ortsverband.lernpools.edit', [$ortsverband, $pool]) }}" class="action-link modal-trigger" data-modal-type="edit">
                            Bearbeiten
                        </a>
                        <a href="{{ route('ortsverband.lernpools.questions.index', [$ortsverband, $pool]) }}" class="action-link modal-trigger" data-modal-type="questions">
                            Fragen
                        </a>
                        <form action="{{ route('ortsverband.lernpools.destroy', [$ortsverband, $pool]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Lernpool wirklich löschen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-link action-link-danger">
                                Löschen
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        @else
        <div class="empty-state" style="padding: 3rem;">
            <div class="empty-state-icon"><i class="bi bi-collection"></i></div>
            <h3 class="empty-state-title">Keine Lernpools</h3>
            <p class="empty-state-desc">Erstelle deinen ersten Lernpool, um loszulegen.</p>
            <button id="openCreateModalEmpty" class="btn-primary btn-sm">Neuer Lernpool</button>
        </div>
        @endif
    </div>
</div>

<!-- Modal für neuen Lernpool -->
<div id="createModal" class="modal-overlay-glass" style="display: none;">
    <div class="modal-glass" style="max-width: 550px;">
        <div class="modal-header-glass">
            <h2>Neuer Lernpool</h2>
            <button id="closeCreateModal" class="modal-close-btn">&times;</button>
        </div>
        <form id="createLernpoolForm" action="{{ route('ortsverband.lernpools.store', $ortsverband) }}" method="POST">
            @csrf
            <div class="modal-body-glass">
                <div style="margin-bottom: 1.25rem;">
                    <label for="name" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.9rem;">
                        Name <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="name" id="name" class="input-glass"
                           placeholder="z.B. Grundlagen Erste Hilfe" required>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label for="description" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.9rem;">
                        Beschreibung <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea name="description" id="description" rows="4" class="textarea-glass"
                              placeholder="Beschreibung des Lernpools..." required></textarea>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label for="tags" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.9rem;">
                        Tags <span style="color: var(--text-muted); font-weight: normal;">(optional)</span>
                    </label>
                    @if($allTags->isNotEmpty())
                        <div style="margin-bottom: 0.5rem;">
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                @foreach($allTags as $tag)
                                    <button type="button" onclick="addTagToModal('{{ $tag }}')" class="tag-suggestion-btn">
                                        + {{ $tag }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <input type="text" name="tags" id="tags" class="input-glass"
                           placeholder="z.B. ZTR, B FGr (mit Komma trennen)">
                </div>

                <div>
                    <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked style="width: 1.25rem; height: 1.25rem;">
                        <span style="font-weight: 600; color: var(--text-primary);">Sofort aktivieren</span>
                    </label>
                </div>
            </div>

            <div class="modal-footer-glass">
                <button type="button" id="cancelCreateModal" class="btn-ghost">Abbrechen</button>
                <button type="submit" class="btn-primary">Erstellen</button>
            </div>
        </form>
    </div>
</div>

<!-- Generisches Modal für Show/Edit/Fragen -->
<div id="genericModalBackdrop" class="modal-overlay-glass" style="display: none;">
    <div id="genericModal" class="modal-glass" style="max-width: 600px;"></div>
</div>

@push('styles')
<style>
    .pool-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1rem;
    }

    .pool-card {
        padding: 1.25rem;
        border-radius: 0.875rem;
        display: flex;
        flex-direction: column;
    }

    .action-link {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--gold-start);
        text-decoration: none;
        transition: all 0.2s;
        padding: 0.35rem 0.65rem;
        border-radius: 0.4rem;
        background: none;
        border: none;
        cursor: pointer;
    }

    .action-link:hover {
        background: rgba(251, 191, 36, 0.15);
    }

    .action-link-danger {
        color: #ef4444;
    }

    .action-link-danger:hover {
        background: rgba(239, 68, 68, 0.15);
    }

    .tag-suggestion-btn {
        background: rgba(251, 191, 36, 0.15);
        color: var(--gold-start);
        padding: 0.3rem 0.6rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid rgba(251, 191, 36, 0.3);
        cursor: pointer;
        transition: all 0.2s;
    }

    .tag-suggestion-btn:hover {
        background: var(--gradient-gold);
        color: #1e3a5f;
        transform: translateY(-1px);
    }

    .empty-state {
        text-align: center;
    }

    .empty-state-icon {
        font-size: 2.5rem;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
        opacity: 0.6;
    }

    .empty-state-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .empty-state-desc {
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
    }

    .modal-overlay-glass.active {
        display: flex !important;
    }

    .modal-close-btn {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: var(--text-secondary);
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        cursor: pointer;
        font-size: 1.25rem;
        transition: all 0.2s;
    }

    .modal-close-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        color: var(--text-primary);
    }

    /* Loading Animation for Modal */
    .modal-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 2rem;
        text-align: center;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 3px solid rgba(255, 255, 255, 0.1);
        border-top: 3px solid var(--gold-start);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-bottom: 1rem;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .modal-loading-text {
        color: var(--text-secondary);
        font-size: 0.9rem;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .pool-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

<script>
    // Tag-Suggestion Funktionalität
    function addTagToModal(tag) {
        const input = document.getElementById('tags');
        const currentValue = input.value.trim();

        const existingTags = currentValue.split(',').map(t => t.trim());
        if (existingTags.includes(tag)) {
            return;
        }

        if (currentValue === '') {
            input.value = tag;
        } else {
            input.value = currentValue + ', ' + tag;
        }

        input.focus();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('createModal');
        const openBtn = document.getElementById('openCreateModal');
        const openBtnEmpty = document.getElementById('openCreateModalEmpty');
        const closeBtn = document.getElementById('closeCreateModal');
        const cancelBtn = document.getElementById('cancelCreateModal');
        const form = document.getElementById('createLernpoolForm');

        function openModal() {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        if (openBtn) openBtn.addEventListener('click', openModal);
        if (openBtnEmpty) openBtnEmpty.addEventListener('click', openModal);

        function closeModal() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            form.reset();
        }

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.style.display === 'flex') closeModal();
        });
    });

    // Generic Modal Loader
    document.addEventListener('DOMContentLoaded', function() {
        const genericModal = document.getElementById('genericModal');
        const genericModalBackdrop = document.getElementById('genericModalBackdrop');

        function closeGenericModal() {
            genericModalBackdrop.style.display = 'none';
            document.body.style.overflow = 'auto';
            genericModal.innerHTML = '';
        }

        genericModalBackdrop.addEventListener('click', function(e) {
            if (e.target === genericModalBackdrop) closeGenericModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && genericModalBackdrop.style.display === 'flex') closeGenericModal();
        });

        document.addEventListener('click', function(e) {
            const trigger = e.target.closest('.modal-trigger');
            if (trigger && trigger.href) {
                e.preventDefault();
                e.stopPropagation();

                const baseUrl = trigger.href;
                const cacheBuster = '_t=' + Date.now();
                const url = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'ajax=1&' + cacheBuster;

                genericModal.innerHTML = '<div class="modal-loading"><div class="spinner"></div><div class="modal-loading-text">Lädt...</div></div>';
                genericModalBackdrop.style.display = 'flex';

                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                        'Cache-Control': 'no-cache, no-store, must-revalidate',
                        'Pragma': 'no-cache'
                    },
                    credentials: 'same-origin',
                    cache: 'no-store'
                })
                .then(response => response.text())
                .then(html => {
                    genericModal.innerHTML = html;
                    document.body.style.overflow = 'hidden';
                })
                .catch(error => {
                    console.error('Error loading modal:', error);
                    genericModal.innerHTML = '<div class="modal-header-glass"><h2>Fehler</h2><button class="modal-close-btn" onclick="document.getElementById(\'genericModalBackdrop\').style.display=\'none\'">&times;</button></div><div class="modal-body-glass"><p style="color: var(--text-secondary);">Fehler beim Laden des Inhalts.</p></div>';
                });
                return false;
            }
        });

        // Event-Delegation für Submit-Buttons
        document.addEventListener('click', function(e) {
            const submitBtn = e.target.closest('#submitFinishBtn, #submitContinueBtn');
            if (!submitBtn) return;

            e.preventDefault();
            e.stopPropagation();

            const action = submitBtn.id === 'submitContinueBtn' ? 'continue' : 'finish';
            const form = document.getElementById('createQuestionForm');
            if (!form) {
                alert('Fehler: Formular nicht gefunden!');
                return;
            }

            const loesungCheckboxes = form.querySelectorAll('input[name="loesung[]"]:checked');
            if (loesungCheckboxes.length === 0) {
                alert('Bitte wähle mindestens eine richtige Antwort aus!');
                return;
            }

            const requiredInputs = form.querySelectorAll('[required]');
            let allValid = true;
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    allValid = false;
                    input.focus();
                    input.reportValidity();
                }
            });

            if (!allValid) return;

            const formData = new FormData(form);
            const buttons = form.querySelectorAll('#submitFinishBtn, #submitContinueBtn');
            const formAction = form.getAttribute('action');

            buttons.forEach(btn => {
                btn.disabled = true;
                btn.style.opacity = '0.6';
            });

            fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToastNotification(data.message, 'success');

                    if (action === 'continue') {
                        let createUrl;
                        if (formAction.endsWith('/store')) {
                            createUrl = formAction.replace(/\/store$/, '/create');
                        } else if (formAction.includes('/questions')) {
                            createUrl = formAction.replace(/\/questions.*$/, '/questions/create');
                        } else {
                            return;
                        }

                        createUrl += '?ajax=1&_t=' + Date.now();

                        fetch(createUrl, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Cache-Control': 'no-cache'
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            genericModal.innerHTML = html;
                        });
                    } else {
                        genericModalBackdrop.style.display = 'none';
                        setTimeout(() => location.reload(), 300);
                    }
                } else {
                    showToastNotification(data.message || 'Fehler beim Speichern', 'error');
                    buttons.forEach(btn => {
                        btn.disabled = false;
                        btn.style.opacity = '1';
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToastNotification('Fehler beim Speichern', 'error');
                buttons.forEach(btn => {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                });
            });
        });

        // Event-Delegation für Update-Button
        document.addEventListener('click', function(e) {
            const updateBtn = e.target.closest('#updateQuestionBtn');
            if (!updateBtn) return;

            e.preventDefault();
            e.stopPropagation();

            const form = document.getElementById('editQuestionForm');
            if (!form) {
                alert('Fehler: Formular nicht gefunden!');
                return;
            }

            const loesungCheckboxes = form.querySelectorAll('input[name="loesung[]"]:checked');
            if (loesungCheckboxes.length === 0) {
                alert('Bitte wähle mindestens eine richtige Antwort aus!');
                return;
            }

            const requiredInputs = form.querySelectorAll('[required]');
            let allValid = true;
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    allValid = false;
                    input.focus();
                    input.reportValidity();
                }
            });

            if (!allValid) return;

            const formData = new FormData(form);
            const formAction = form.getAttribute('action');

            updateBtn.disabled = true;
            updateBtn.style.opacity = '0.6';

            fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToastNotification(data.message, 'success');
                    genericModalBackdrop.style.display = 'none';
                    setTimeout(() => location.reload(), 300);
                } else {
                    showToastNotification(data.message || 'Fehler beim Aktualisieren', 'error');
                    updateBtn.disabled = false;
                    updateBtn.style.opacity = '1';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToastNotification('Fehler beim Aktualisieren', 'error');
                updateBtn.disabled = false;
                updateBtn.style.opacity = '1';
            });
        });

        // Event-Delegation für Delete-Buttons
        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.btn-icon-delete');
            if (!deleteBtn) return;

            e.preventDefault();
            e.stopPropagation();

            const questionId = deleteBtn.getAttribute('data-question-id');
            const deleteUrl = deleteBtn.getAttribute('data-delete-url');

            if (!confirm('Möchtest du diese Frage wirklich löschen?')) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            deleteBtn.disabled = true;
            deleteBtn.style.opacity = '0.5';

            fetch(deleteUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ _method: 'DELETE' }),
                credentials: 'same-origin'
            })
            .then(response => {
                if (response.ok) {
                    showToastNotification('Frage gelöscht', 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    throw new Error('Fehler beim Löschen');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToastNotification('Fehler beim Löschen', 'error');
                deleteBtn.disabled = false;
                deleteBtn.style.opacity = '1';
            });
        });

        // Toast Notification
        window.showToastNotification = function(message, type) {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 14px 20px;
                background: ${type === 'success' ? 'linear-gradient(135deg, #22c55e, #16a34a)' : 'linear-gradient(135deg, #ef4444, #dc2626)'};
                color: white;
                border-radius: 0.75rem;
                font-weight: 600;
                font-size: 0.9rem;
                z-index: 10000;
                box-shadow: 0 10px 40px rgba(0,0,0,0.3);
                animation: slideInRight 0.3s ease-out;
            `;
            toast.textContent = (type === 'success' ? '✓ ' : '✗ ') + message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        };

        if (!document.getElementById('toast-animations')) {
            const style = document.createElement('style');
            style.id = 'toast-animations';
            style.textContent = `
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOutRight {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
    });
</script>
@endsection
