@extends('layouts.app')

@section('title', 'Kontaktanfragen - Admin')

@push('styles')
<style>
    .badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    .badge-feedback { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .badge-feature { background: rgba(251, 191, 36, 0.2); color: #fbbf24; }
    .badge-bug { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
    .badge-other { background: rgba(255, 255, 255, 0.1); color: var(--text-secondary); }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Kontaktanfragen <span>Übersicht</span></h1>
        <p class="page-subtitle">Verwalte eingehende Feedback- und Kontaktanfragen</p>
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

    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-thw-blue">
                <i class="bi bi-chat-left-text"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $stats['total'] }}</div>
                <div class="stat-pill-label">Gesamt</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-gold">
                <i class="bi bi-exclamation-circle"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $stats['unread'] }}</div>
                <div class="stat-pill-label">Ungelesen</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-success">
                <i class="bi bi-calendar-today"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $stats['today'] }}</div>
                <div class="stat-pill-label">Heute</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-gold">
                <i class="bi bi-calendar-week"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $stats['this_week'] }}</div>
                <div class="stat-pill-label">Diese Woche</div>
            </div>
        </div>
    </div>

    <div class="glass hover-lift" style="padding: 1.5rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 1rem 0;">Filter & Suche</h3>
        <form id="filterForm" method="GET" action="{{ route('admin.contact-messages.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Suche</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="E-Mail, Name, Nachricht..."
                       style="width: 100%; padding: 0.625rem 1rem; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.5rem; color: var(--text-primary); outline: none;">
            </div>

            <div>
                <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Kategorie</label>
                <select name="type" style="width: 100%; padding: 0.625rem 1rem; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.5rem; color: var(--text-primary); outline: none;">
                    <option value="">Alle</option>
                    <option value="feedback" {{ request('type') == 'feedback' ? 'selected' : '' }}>Feedback</option>
                    <option value="feature" {{ request('type') == 'feature' ? 'selected' : '' }}>Feature</option>
                    <option value="bug" {{ request('type') == 'bug' ? 'selected' : '' }}>Bug</option>
                    <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Sonstiges</option>
                </select>
            </div>

            <div>
                <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Status</label>
                <select name="status" style="width: 100%; padding: 0.625rem 1rem; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.5rem; color: var(--text-primary); outline: none;">
                    <option value="">Alle</option>
                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Ungelesen</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Gelesen</option>
                </select>
            </div>

            <div>
                <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Hermine</label>
                <select name="hermine" style="width: 100%; padding: 0.625rem 1rem; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.5rem; color: var(--text-primary); outline: none;">
                    <option value="">Alle</option>
                    <option value="1" {{ request('hermine') == '1' ? 'selected' : '' }}>Ja</option>
                </select>
            </div>
        </form>

        <div style="display: flex; gap: 0.75rem;">
            <button type="submit" form="filterForm" class="btn-primary" style="padding: 0.5rem 1.5rem;">
                <i class="bi bi-search"></i> Filtern
            </button>
            <a href="{{ route('admin.contact-messages.index') }}" class="btn-secondary" style="padding: 0.5rem 1.5rem;">
                <i class="bi bi-arrow-clockwise"></i> Zurücksetzen
            </a>
        </div>
    </div>

    <div class="glass hover-lift" style="padding: 1.5rem;">
        @if($messages->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($messages as $message)
                    <div style="padding: 1rem; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.5rem; background: {{ !$message->is_read ? 'rgba(255, 255, 255, 0.03)' : 'transparent' }}; transition: all 0.2s;"
                         onmouseover="this.style.background='rgba(255, 255, 255, 0.05)'; this.style.transform='scale(1.01)'"
                         onmouseout="this.style.background='{{ !$message->is_read ? 'rgba(255, 255, 255, 0.03)' : 'transparent' }}'; this.style.transform='scale(1)'">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                <span class="badge badge-{{ $message->type }}">{{ $message->type_label }}</span>
                                @if(!$message->is_read)
                                    <span style="display: inline-block; background: rgba(251, 191, 36, 0.2); color: #fbbf24; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                                        <i class="bi bi-dot"></i> NEU
                                    </span>
                                @endif
                                @if($message->hermine_contact)
                                    <span style="display: inline-block; background: rgba(59, 130, 246, 0.2); color: #3b82f6; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                                        <i class="bi bi-phone"></i> Hermine
                                    </span>
                                @endif
                            </div>
                            <div style="font-size: 0.875rem; color: var(--text-muted); white-space: nowrap;">
                                {{ $message->created_at->diffForHumans() }}
                            </div>
                        </div>

                        <div style="margin-bottom: 0.75rem;">
                            <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-primary);">{{ $message->email }}</div>
                            @if($message->user)
                                <div style="font-size: 0.85rem; color: var(--text-muted);">User: {{ $message->user->name }} (ID: {{ $message->user->id }})</div>
                            @endif
                            @if($message->hermine_contact)
                                <div style="font-size: 0.85rem; color: var(--text-secondary);">
                                    {{ $message->vorname }} {{ $message->nachname }} ({{ $message->ortsverband }})
                                </div>
                            @endif
                        </div>

                        <p style="margin: 0.75rem 0; color: var(--text-secondary); font-size: 0.95rem;">
                            {{ Str::limit($message->message, 150) }}
                        </p>

                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <a href="{{ route('admin.contact-messages.show', $message) }}"
                               class="btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                <i class="bi bi-eye"></i> Ansehen
                            </a>

                            @if(!$message->is_read)
                                <form method="POST" action="{{ route('admin.contact-messages.mark-read', $message) }}" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                        <i class="bi bi-check-circle"></i> Als gelesen
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.contact-messages.mark-unread', $message) }}" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                        <i class="bi bi-circle"></i> Als ungelesen
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}"
                                  onsubmit="return confirm('Wirklich löschen?')" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-ghost" style="padding: 0.5rem 1rem; font-size: 0.9rem; color: var(--error);">
                                    <i class="bi bi-trash"></i> Löschen
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 2rem;">
                {{ $messages->links() }}
            </div>
        @else
            <div style="padding: 3rem 1rem; text-align: center; color: var(--text-muted);">
                <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="bi bi-inbox"></i></div>
                <p style="margin: 0 0 0.5rem 0; font-weight: 700;">Keine Nachrichten gefunden</p>
                <p style="margin: 0; font-size: 0.95rem;">{{ request()->hasAny(['search', 'type', 'status', 'hermine']) ? 'Versuche einen anderen Filter' : 'Es sind noch keine Kontaktanfragen eingegangen' }}</p>
            </div>
        @endif
    </div>
</div>

<script>
    function toggleAllCheckboxes() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.message-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
    }
    
    function bulkDelete() {
        const checkboxes = document.querySelectorAll('.message-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Bitte wähle mindestens eine Nachricht aus.');
            return;
        }
        
        if (confirm(`Wirklich ${checkboxes.length} Nachricht(en) löschen?`)) {
            document.getElementById('bulkDeleteForm').submit();
        }
    }
</script>
@endsection
