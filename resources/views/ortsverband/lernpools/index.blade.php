@extends('layouts.app')

@section('title', $ortsverband->name . ' · Lernpools')

@section('content')
<div class="dashboard-container" x-data="lernpoolManager()">
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
                <button @click="openModal('create')" class="btn-primary btn-sm">Neuer Lernpool</button>
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
                        <button @click="loadModal('{{ route('ortsverband.lernpools.show', [$ortsverband, $pool]) }}')" class="action-link">
                            Details
                        </button>
                        <button @click="loadModal('{{ route('ortsverband.lernpools.edit', [$ortsverband, $pool]) }}')" class="action-link">
                            Bearbeiten
                        </button>
                        <button @click="loadModal('{{ route('ortsverband.lernpools.questions.index', [$ortsverband, $pool]) }}')" class="action-link">
                            Fragen
                        </button>
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
            <button @click="openModal('create')" class="btn-primary btn-sm">Neuer Lernpool</button>
        </div>
        @endif
    </div>

    <!-- Alpine.js Modal System -->
    <div x-show="isOpen"
         x-cloak
         class="modal-overlay-alpine"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="closeModal()"
         @keydown.escape.window="closeModal()">

        <div class="modal-container-alpine"
             x-show="isOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             @click.stop>

            <!-- Loading State -->
            <template x-if="isLoading">
                <div class="modal-loading-alpine">
                    <div class="spinner-alpine"></div>
                    <div class="modal-loading-text">Lädt...</div>
                </div>
            </template>

            <!-- Create Form (Inline) -->
            <template x-if="modalType === 'create' && !isLoading">
                <div>
                    <div class="modal-header-alpine">
                        <h2>Neuer Lernpool</h2>
                        <button @click="closeModal()" class="modal-close-alpine">&times;</button>
                    </div>
                    <form action="{{ route('ortsverband.lernpools.store', $ortsverband) }}" method="POST">
                        @csrf
                        <div class="modal-body-alpine">
                            <div class="form-group-alpine">
                                <label class="form-label-alpine">
                                    Name <span class="required-alpine">*</span>
                                </label>
                                <input type="text" name="name" class="input-alpine" placeholder="z.B. Grundlagen Erste Hilfe" required>
                            </div>

                            <div class="form-group-alpine">
                                <label class="form-label-alpine">
                                    Beschreibung <span class="required-alpine">*</span>
                                </label>
                                <textarea name="description" rows="4" class="textarea-alpine" placeholder="Beschreibung des Lernpools..." required></textarea>
                            </div>

                            <div class="form-group-alpine">
                                <label class="form-label-alpine">
                                    Tags <span class="optional-alpine">(optional)</span>
                                </label>
                                @if($allTags->isNotEmpty())
                                <div class="tag-suggestions-alpine">
                                    @foreach($allTags as $tag)
                                        <button type="button" @click="addTag('{{ $tag }}')" class="tag-btn-alpine">+ {{ $tag }}</button>
                                    @endforeach
                                </div>
                                @endif
                                <input type="text" name="tags" x-model="tagInput" class="input-alpine" placeholder="z.B. ZTR, B FGr (mit Komma trennen)">
                            </div>

                            <label class="checkbox-alpine">
                                <input type="checkbox" name="is_active" value="1" checked>
                                <span>Sofort aktivieren</span>
                            </label>
                        </div>

                        <div class="modal-footer-alpine">
                            <button type="button" @click="closeModal()" class="btn-ghost">Abbrechen</button>
                            <button type="submit" class="btn-primary">Erstellen</button>
                        </div>
                    </form>
                </div>
            </template>

            <!-- Dynamic Content (from AJAX) -->
            <template x-if="modalType === 'dynamic' && !isLoading">
                <div x-html="modalContent"></div>
            </template>
        </div>
    </div>

    <!-- Toast Container -->
    <div x-show="toast.show"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-full"
         :class="toast.type === 'success' ? 'toast-success-alpine' : 'toast-error-alpine'"
         class="toast-alpine">
        <span x-text="(toast.type === 'success' ? '✓ ' : '✗ ') + toast.message"></span>
    </div>
</div>

@push('styles')
<style>
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .dashboard-header {
        margin-bottom: 2.5rem;
        padding-top: 1rem;
        max-width: 600px;
    }

    [x-cloak] { display: none !important; }

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

    /* Alpine.js Modal Styles */
    .modal-overlay-alpine {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 1rem;
    }

    .modal-container-alpine {
        background: linear-gradient(135deg, rgba(26, 26, 29, 0.98) 0%, rgba(20, 20, 23, 0.99) 100%);
        backdrop-filter: blur(40px);
        -webkit-backdrop-filter: blur(40px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1.25rem;
        box-shadow:
            0 0 0 1px rgba(255, 255, 255, 0.05),
            0 25px 60px rgba(0, 0, 0, 0.5),
            0 0 100px rgba(251, 191, 36, 0.03);
        max-width: 600px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        color: var(--text-primary);
    }

    .modal-container-alpine::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(251, 191, 36, 0.3), transparent);
    }

    .modal-header-alpine {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .modal-header-alpine h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .modal-close-alpine {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: var(--text-secondary);
        width: 36px;
        height: 36px;
        border-radius: 0.625rem;
        cursor: pointer;
        font-size: 1.5rem;
        line-height: 1;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close-alpine:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.2);
        color: var(--text-primary);
        transform: scale(1.05);
    }

    .modal-body-alpine {
        padding: 1.5rem;
    }

    .modal-footer-alpine {
        padding: 1rem 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 0 0 1.25rem 1.25rem;
    }

    /* Form Elements */
    .form-group-alpine {
        margin-bottom: 1.25rem;
    }

    .form-label-alpine {
        display: block;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .required-alpine {
        color: #ef4444;
    }

    .optional-alpine {
        color: var(--text-muted);
        font-weight: normal;
    }

    .input-alpine,
    .textarea-alpine,
    .modal-container-alpine input[type="text"],
    .modal-container-alpine input[type="number"],
    .modal-container-alpine textarea {
        width: 100%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.625rem;
        padding: 0.75rem 1rem;
        color: var(--text-primary);
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .input-alpine:focus,
    .textarea-alpine:focus,
    .modal-container-alpine input:focus,
    .modal-container-alpine textarea:focus {
        outline: none;
        border-color: var(--gold-start);
        box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1);
        background: rgba(255, 255, 255, 0.08);
    }

    .input-alpine::placeholder,
    .textarea-alpine::placeholder,
    .modal-container-alpine input::placeholder,
    .modal-container-alpine textarea::placeholder {
        color: var(--text-muted);
    }

    .tag-suggestions-alpine {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 0.75rem;
    }

    .tag-btn-alpine {
        background: rgba(251, 191, 36, 0.12);
        color: var(--gold-start);
        padding: 0.35rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid rgba(251, 191, 36, 0.25);
        cursor: pointer;
        transition: all 0.2s;
    }

    .tag-btn-alpine:hover {
        background: var(--gradient-gold);
        color: #1e3a5f;
        border-color: transparent;
        transform: translateY(-1px);
    }

    .checkbox-alpine {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        font-weight: 600;
        color: var(--text-primary);
    }

    .checkbox-alpine input {
        width: 1.25rem;
        height: 1.25rem;
        accent-color: var(--gold-start);
    }

    /* Loading State */
    .modal-loading-alpine {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
        text-align: center;
    }

    .spinner-alpine {
        width: 44px;
        height: 44px;
        border: 3px solid rgba(255, 255, 255, 0.1);
        border-top: 3px solid var(--gold-start);
        border-radius: 50%;
        animation: spinAlpine 0.8s linear infinite;
        margin-bottom: 1rem;
    }

    @keyframes spinAlpine {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .modal-loading-text {
        color: var(--text-secondary);
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* Toast */
    .toast-alpine {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 14px 20px;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.9rem;
        z-index: 10001;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        color: white;
    }

    .toast-success-alpine {
        background: linear-gradient(135deg, #22c55e, #16a34a);
    }

    .toast-error-alpine {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    /* Transitions */
    .transition { transition-property: all; }
    .ease-out { transition-timing-function: cubic-bezier(0, 0, 0.2, 1); }
    .ease-in { transition-timing-function: cubic-bezier(0.4, 0, 1, 1); }
    .duration-200 { transition-duration: 200ms; }
    .duration-150 { transition-duration: 150ms; }
    .duration-300 { transition-duration: 300ms; }
    .opacity-0 { opacity: 0; }
    .opacity-100 { opacity: 1; }
    .translate-y-4 { transform: translateY(1rem); }
    .translate-y-0 { transform: translateY(0); }
    .translate-x-full { transform: translateX(100%); }
    .translate-x-0 { transform: translateX(0); }
    .scale-95 { transform: scale(0.95); }
    .scale-100 { transform: scale(1); }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }

        .pool-grid {
            grid-template-columns: 1fr;
        }

        .modal-container-alpine {
            margin: 0.5rem;
            max-height: 95vh;
        }
    }

    /* Modal Content Styling (for AJAX loaded content) */
    .modal-header-glass {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .modal-header-glass h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .modal-body-glass {
        padding: 1.5rem;
    }

    /* ===== LIGHT MODE MODAL STYLES ===== */
    html.light-mode .modal-overlay-alpine {
        background: rgba(0, 0, 0, 0.4);
    }

    html.light-mode .modal-container-alpine {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        box-shadow:
            0 0 0 1px rgba(0, 0, 0, 0.05),
            0 25px 60px rgba(0, 0, 0, 0.15),
            0 0 100px rgba(0, 51, 127, 0.05);
        color: #1a1a1d !important;
    }

    html.light-mode .modal-container-alpine::before {
        background: linear-gradient(90deg, transparent, rgba(0, 51, 127, 0.3), transparent);
    }

    html.light-mode .modal-header-glass {
        border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
    }

    html.light-mode .modal-header-glass h2 {
        color: #1a1a1d !important;
    }

    html.light-mode .modal-body-glass {
        color: #1a1a1d !important;
    }

    html.light-mode .modal-footer-glass {
        background: rgba(0, 0, 0, 0.03) !important;
        border-top: 1px solid rgba(0, 0, 0, 0.08) !important;
    }

    /* Glass-subtle in light mode */
    html.light-mode .modal-container-alpine .glass-subtle {
        background: rgba(0, 0, 0, 0.03) !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        color: #1a1a1d !important;
    }

    /* Buttons in light mode */
    html.light-mode .modal-container-alpine .btn-ghost,
    html.light-mode .modal-footer-glass .btn-ghost {
        background: transparent !important;
        border: 1px solid rgba(0, 51, 127, 0.2) !important;
        color: #00337F !important;
    }

    html.light-mode .modal-container-alpine .btn-ghost:hover,
    html.light-mode .modal-footer-glass .btn-ghost:hover {
        background: rgba(0, 51, 127, 0.06) !important;
        border-color: #00337F !important;
    }

    html.light-mode .modal-container-alpine .btn-primary,
    html.light-mode .modal-footer-glass .btn-primary {
        background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
        color: #1e3a5f !important;
    }

    html.light-mode .modal-container-alpine .btn-secondary,
    html.light-mode .modal-footer-glass .btn-secondary {
        background: linear-gradient(135deg, #00337F, #004AAD) !important;
        color: white !important;
    }

    /* Inputs in light mode */
    html.light-mode .modal-container-alpine input[type="text"],
    html.light-mode .modal-container-alpine input[type="number"],
    html.light-mode .modal-container-alpine textarea,
    html.light-mode .modal-container-alpine .input-alpine,
    html.light-mode .modal-container-alpine .textarea-alpine {
        background: #ffffff !important;
        border: 1px solid rgba(0, 0, 0, 0.15) !important;
        color: #1a1a1d !important;
    }

    html.light-mode .modal-container-alpine input:focus,
    html.light-mode .modal-container-alpine textarea:focus {
        border-color: #00337F !important;
        box-shadow: 0 0 0 3px rgba(0, 51, 127, 0.1) !important;
    }

    html.light-mode .modal-container-alpine input::placeholder,
    html.light-mode .modal-container-alpine textarea::placeholder {
        color: #9ca3af !important;
    }

    /* Close button in light mode */
    html.light-mode .modal-close-btn:not(.btn-ghost) {
        background: rgba(0, 0, 0, 0.05) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        color: #6b7280 !important;
    }

    html.light-mode .modal-close-btn:not(.btn-ghost):hover {
        background: rgba(0, 0, 0, 0.1) !important;
        color: #1a1a1d !important;
    }

    /* Icon buttons in light mode */
    html.light-mode .modal-container-alpine .btn-icon-edit {
        background: rgba(59, 130, 246, 0.1) !important;
        color: #2563eb !important;
    }

    html.light-mode .modal-container-alpine .btn-icon-delete {
        background: rgba(239, 68, 68, 0.1) !important;
        color: #dc2626 !important;
    }

    /* Section headers in light mode */
    html.light-mode .modal-container-alpine .section-header {
        border-left-color: #00337F !important;
    }

    html.light-mode .modal-container-alpine .section-title {
        color: #1a1a1d !important;
    }

    /* Text colors in light mode */
    html.light-mode .modal-container-alpine p,
    html.light-mode .modal-container-alpine span,
    html.light-mode .modal-container-alpine div,
    html.light-mode .modal-container-alpine label {
        color: inherit;
    }

    /* Badge styling in light mode */
    html.light-mode .modal-container-alpine .badge-success {
        background: linear-gradient(135deg, #22c55e, #16a34a) !important;
        color: white !important;
    }

    .modal-footer-glass {
        padding: 1rem 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 0 0 1.25rem 1.25rem;
    }

    /* X close button (top right) */
    .modal-close-btn:not(.btn-ghost) {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: var(--text-secondary);
        width: 36px;
        height: 36px;
        border-radius: 0.625rem;
        cursor: pointer;
        font-size: 1.5rem;
        line-height: 1;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close-btn:not(.btn-ghost):hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.2);
        color: var(--text-primary);
    }

    /* Text close button (btn-ghost in footer) */
    .modal-footer-glass .btn-ghost.modal-close-btn {
        width: auto;
        height: auto;
        font-size: 0.9rem;
        padding: 0.625rem 1.25rem;
    }

    /* Modal buttons - base styles (dark mode) */
    .modal-container-alpine .btn-ghost,
    .modal-footer-glass .btn-ghost {
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: var(--text-secondary);
        padding: 0.625rem 1.25rem;
        border-radius: 0.625rem;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .modal-container-alpine .btn-ghost:hover,
    .modal-footer-glass .btn-ghost:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.25);
        color: var(--text-primary);
    }

    .modal-container-alpine .btn-primary,
    .modal-footer-glass .btn-primary {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        border: none;
        color: #1e3a5f;
        padding: 0.625rem 1.25rem;
        border-radius: 0.625rem;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .modal-container-alpine .btn-primary:hover,
    .modal-footer-glass .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
    }

    .modal-container-alpine .btn-secondary,
    .modal-footer-glass .btn-secondary {
        background: linear-gradient(135deg, #00337F, #004AAD);
        border: none;
        color: white;
        padding: 0.625rem 1.25rem;
        border-radius: 0.625rem;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .modal-container-alpine .btn-secondary:hover,
    .modal-footer-glass .btn-secondary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.3);
    }

    /* Icon Buttons for Questions */
    .btn-icon-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        background: transparent;
    }

    .btn-icon-edit {
        background: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
        text-decoration: none;
    }

    .btn-icon-edit:hover {
        background: rgba(59, 130, 246, 0.25);
        transform: scale(1.05);
    }

    .btn-icon-delete {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    .btn-icon-delete:hover {
        background: rgba(239, 68, 68, 0.25);
        transform: scale(1.05);
    }

    /* Answer Checkbox Styling */
    .answer-checkbox-toggle {
        position: relative;
        cursor: pointer;
        min-width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .answer-checkbox {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .checkbox-custom {
        width: 28px;
        height: 28px;
        border: 2.5px solid rgba(255, 255, 255, 0.2);
        border-radius: 0.5rem;
        background: rgba(255, 255, 255, 0.05);
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .answer-checkbox-toggle:hover .checkbox-custom {
        border-color: var(--gold-start);
        background: rgba(251, 191, 36, 0.1);
    }

    .answer-checkbox:checked + .checkbox-custom {
        background: var(--gradient-gold);
        border-color: transparent;
    }

    .answer-checkbox:checked + .checkbox-custom::after {
        content: '\2713';
        color: #1e3a5f;
        font-size: 1rem;
        font-weight: bold;
        line-height: 1;
    }

    /* Checkbox Light Mode Styles */
    html.light-mode .modal-container-alpine .checkbox-custom {
        border: 2.5px solid rgba(0, 0, 0, 0.2) !important;
        background: rgba(0, 0, 0, 0.03) !important;
    }

    html.light-mode .modal-container-alpine .answer-checkbox-toggle:hover .checkbox-custom {
        border-color: #00337F !important;
        background: rgba(0, 51, 127, 0.1) !important;
    }

    html.light-mode .modal-container-alpine .answer-checkbox:checked + .checkbox-custom {
        background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
        border-color: transparent !important;
    }

    /* "Sofort aktivieren" checkbox in Light Mode */
    html.light-mode .modal-container-alpine .checkbox-alpine span {
        color: #1a1a1d !important;
    }
</style>
@endpush

@push('alpine-components')
<script>
// Lernpool Manager als globale Funktion für Alpine.js
window.lernpoolManager = function() {
    return {
        isOpen: false,
        isLoading: false,
        modalType: null,
        modalContent: '',
        tagInput: '',
        toast: {
            show: false,
            message: '',
            type: 'success'
        },

        openModal(type) {
            this.modalType = type;
            this.isOpen = true;
            this.isLoading = false;
            document.body.style.overflow = 'hidden';
        },

        async loadModal(url) {
            this.modalType = 'dynamic';
            this.isOpen = true;
            this.isLoading = true;
            document.body.style.overflow = 'hidden';

            try {
                const cacheBuster = '_t=' + Date.now();
                const fetchUrl = url + (url.includes('?') ? '&' : '?') + 'ajax=1&' + cacheBuster;

                const response = await fetch(fetchUrl, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                        'Cache-Control': 'no-cache, no-store, must-revalidate'
                    },
                    credentials: 'same-origin',
                    cache: 'no-store'
                });

                if (!response.ok) throw new Error('Fetch failed');

                this.modalContent = await response.text();
                this.isLoading = false;

                // Re-attach event handlers after content loads
                this.$nextTick(() => {
                    this.attachModalEventHandlers();
                });
            } catch (error) {
                console.error('Error loading modal:', error);
                this.modalContent = `
                    <div class="modal-header-glass">
                        <h2>Fehler</h2>
                        <button class="modal-close-btn" onclick="Alpine.$data(document.querySelector('[x-data]')).closeModal()">&times;</button>
                    </div>
                    <div class="modal-body-glass">
                        <p style="color: var(--text-secondary);">Fehler beim Laden des Inhalts.</p>
                    </div>
                `;
                this.isLoading = false;
            }
        },

        closeModal() {
            this.isOpen = false;
            this.modalContent = '';
            this.modalType = null;
            document.body.style.overflow = 'auto';
        },

        addTag(tag) {
            const current = this.tagInput.trim();
            const existingTags = current.split(',').map(t => t.trim()).filter(t => t);

            if (!existingTags.includes(tag)) {
                this.tagInput = existingTags.length ? current + ', ' + tag : tag;
            }
        },

        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => {
                this.toast.show = false;
            }, 3000);
        },

        attachModalEventHandlers() {
            const self = this;

            // Close button handler
            document.querySelectorAll('.modal-close-btn').forEach(btn => {
                btn.onclick = () => self.closeModal();
            });

            // Modal trigger links (for navigation within modals)
            document.querySelectorAll('.modal-trigger').forEach(trigger => {
                trigger.onclick = (e) => {
                    e.preventDefault();
                    self.loadModal(trigger.href);
                };
            });

            // Submit buttons for create question
            const submitFinishBtn = document.getElementById('submitFinishBtn');
            const submitContinueBtn = document.getElementById('submitContinueBtn');

            if (submitFinishBtn) {
                submitFinishBtn.onclick = () => this.handleQuestionSubmit('finish');
            }
            if (submitContinueBtn) {
                submitContinueBtn.onclick = () => this.handleQuestionSubmit('continue');
            }

            // Update button for edit question
            const updateBtn = document.getElementById('updateQuestionBtn');
            if (updateBtn) {
                updateBtn.onclick = () => this.handleQuestionUpdate();
            }

            // Delete buttons
            document.querySelectorAll('.btn-icon-delete').forEach(btn => {
                btn.onclick = () => this.handleQuestionDelete(btn);
            });
        },

        async handleQuestionSubmit(action) {
            const form = document.getElementById('createQuestionForm');
            if (!form) return;

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
            const buttons = form.querySelectorAll('#submitFinishBtn, #submitContinueBtn');

            buttons.forEach(btn => {
                btn.disabled = true;
                btn.style.opacity = '0.6';
            });

            try {
                const response = await fetch(formAction, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (data.success) {
                    this.showToast(data.message, 'success');

                    if (action === 'continue') {
                        let createUrl = formAction.replace(/\/store$/, '/create')
                            .replace(/\/questions.*$/, '/questions/create');
                        this.loadModal(createUrl);
                    } else {
                        this.closeModal();
                        setTimeout(() => location.reload(), 300);
                    }
                } else {
                    this.showToast(data.message || 'Fehler beim Speichern', 'error');
                    buttons.forEach(btn => {
                        btn.disabled = false;
                        btn.style.opacity = '1';
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                this.showToast('Fehler beim Speichern', 'error');
                buttons.forEach(btn => {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                });
            }
        },

        async handleQuestionUpdate() {
            const form = document.getElementById('editQuestionForm');
            if (!form) return;

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

            const updateBtn = document.getElementById('updateQuestionBtn');
            const formData = new FormData(form);
            const formAction = form.getAttribute('action');

            updateBtn.disabled = true;
            updateBtn.style.opacity = '0.6';

            try {
                const response = await fetch(formAction, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (data.success) {
                    this.showToast(data.message, 'success');
                    this.closeModal();
                    setTimeout(() => location.reload(), 300);
                } else {
                    this.showToast(data.message || 'Fehler beim Aktualisieren', 'error');
                    updateBtn.disabled = false;
                    updateBtn.style.opacity = '1';
                }
            } catch (error) {
                console.error('Error:', error);
                this.showToast('Fehler beim Aktualisieren', 'error');
                updateBtn.disabled = false;
                updateBtn.style.opacity = '1';
            }
        },

        async handleQuestionDelete(btn) {
            const deleteUrl = btn.getAttribute('data-delete-url');
            if (!confirm('Möchtest du diese Frage wirklich löschen?')) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            btn.disabled = true;
            btn.style.opacity = '0.5';

            try {
                const response = await fetch(deleteUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ _method: 'DELETE' }),
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    this.showToast('Frage gelöscht', 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    throw new Error('Fehler beim Löschen');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showToast('Fehler beim Löschen', 'error');
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        }
    };
};
</script>
@endpush

@endsection
