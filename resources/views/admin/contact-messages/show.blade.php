@extends('layouts.app')

@section('title', 'Nachricht ansehen - Admin')

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

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 0.5rem;
        margin-bottom: 0.75rem;
    }
    .info-row:last-child {
        margin-bottom: 0;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container" style="max-width: 900px;">
    <header class="dashboard-header">
        <h1 class="page-title">Kontaktanfrage <span>Details</span></h1>
        <p class="page-subtitle">{{ $contactMessage->email }}</p>
    </header>

    <div style="margin-bottom: 2rem;">
        <a href="{{ route('admin.contact-messages.index') }}" class="btn-secondary" style="padding: 0.625rem 1.25rem;">
            Zuruck zur Ubersicht
        </a>
    </div>

    @if(session('success'))
        <div class="glass-success" style="padding: 1.25rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-start;">
            <i class="bi bi-check-circle" style="font-size: 1.25rem; flex-shrink: 0;"></i>
            <div>
                <strong>Erfolg!</strong>
                <p style="margin: 0.25rem 0 0 0;">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="glass-thw hover-lift" style="padding: 1.5rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
            <div>
                <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; margin-bottom: 0.5rem;">
                    <span class="badge badge-{{ $contactMessage->type }}">{{ $contactMessage->type_label }}</span>
                    @if(!$contactMessage->is_read)
                        <span style="display: inline-block; background: rgba(251, 191, 36, 0.2); color: #fbbf24; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                            Ungelesen
                        </span>
                    @else
                        <span style="display: inline-block; background: rgba(34, 197, 94, 0.2); color: #22c55e; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                            Gelesen
                        </span>
                    @endif
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.85rem; color: var(--text-muted);">Eingegangen am</div>
                <div style="font-weight: 700; color: var(--text-primary);">{{ $contactMessage->created_at->format('d.m.Y H:i') }} Uhr</div>
                <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $contactMessage->created_at->diffForHumans() }}</div>
            </div>
        </div>

        @if($contactMessage->is_read)
            <div style="font-size: 0.85rem; color: var(--text-muted);">
                Gelesen am {{ $contactMessage->read_at->format('d.m.Y H:i') }} Uhr
            </div>
        @endif
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="glass hover-lift" style="padding: 1.5rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 1rem 0;">Absender</h3>

                <div class="info-row">
                    <span style="color: var(--text-secondary); font-weight: 600;">E-Mail:</span>
                    <a href="mailto:{{ $contactMessage->email }}" style="color: var(--gold-start); font-weight: 500;">
                        {{ $contactMessage->email }}
                    </a>
                </div>

                @if($contactMessage->user)
                    <div class="info-row">
                        <span style="color: var(--text-secondary); font-weight: 600;">Registrierter User:</span>
                        <span style="color: var(--text-primary); font-weight: 500;">{{ $contactMessage->user->name }} (ID: {{ $contactMessage->user->id }})</span>
                    </div>
                @else
                    <div class="info-row">
                        <span style="color: var(--text-muted); font-size: 0.9rem;">Nicht registrierter Nutzer</span>
                    </div>
                @endif
            </div>

            @if($contactMessage->hermine_contact)
                <div class="glass hover-lift" style="padding: 1.5rem; border-left: 3px solid var(--thw-blue);">
                    <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 1rem 0;">Hermine-Kontakt gewunscht</h3>

                    <div class="info-row">
                        <span style="color: var(--text-secondary); font-weight: 600;">Name:</span>
                        <span style="color: var(--text-primary); font-weight: 500;">{{ $contactMessage->vorname }} {{ $contactMessage->nachname }}</span>
                    </div>

                    <div class="info-row">
                        <span style="color: var(--text-secondary); font-weight: 600;">Ortsverband:</span>
                        <span style="color: var(--text-primary); font-weight: 500;">{{ $contactMessage->ortsverband }}</span>
                    </div>
                </div>
            @endif

            @if($contactMessage->type === 'bug' && $contactMessage->error_location)
                <div class="glass-error hover-lift" style="padding: 1.5rem;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 1rem 0;">Fehler aufgetreten bei</h3>
                    <p style="color: var(--text-primary); font-weight: 500; margin: 0;">{{ ucfirst($contactMessage->error_location) }}</p>
                </div>
            @endif

            <div class="glass hover-lift" style="padding: 1.5rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 1rem 0;">Nachricht</h3>
                <div style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); padding: 1.25rem; border-radius: 0.75rem; white-space: pre-wrap; word-wrap: break-word; line-height: 1.6; color: var(--text-secondary);">{{ $contactMessage->message }}</div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="glass hover-lift" style="padding: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 1rem 0;">Aktionen</h3>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <a href="mailto:{{ $contactMessage->email }}" class="btn-primary" style="padding: 0.75rem 1rem; text-align: center;">
                        E-Mail antworten
                    </a>

                    @if(!$contactMessage->is_read)
                        <form method="POST" action="{{ route('admin.contact-messages.mark-read', $contactMessage) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-secondary" style="width: 100%; padding: 0.75rem 1rem;">
                                Als gelesen markieren
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.contact-messages.mark-unread', $contactMessage) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-secondary" style="width: 100%; padding: 0.75rem 1rem;">
                                Als ungelesen markieren
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.contact-messages.destroy', $contactMessage) }}"
                          onsubmit="return confirm('Wirklich loschen? Diese Aktion kann nicht ruckgangig gemacht werden!')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger" style="width: 100%; padding: 0.75rem 1rem;">
                            Loschen
                        </button>
                    </form>
                </div>
            </div>

            <div class="glass hover-lift" style="padding: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 1rem 0;">Technische Details</h3>

                <div style="font-size: 0.85rem; color: var(--text-muted);">
                    <div style="padding-bottom: 0.5rem; margin-bottom: 0.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.06);">
                        <span style="font-weight: 600;">ID:</span> {{ $contactMessage->id }}
                    </div>
                    <div style="padding-bottom: 0.5rem; margin-bottom: 0.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.06);">
                        <span style="font-weight: 600;">IP-Adresse:</span> {{ $contactMessage->ip_address }}
                    </div>
                    <div style="padding-bottom: 0.5rem; margin-bottom: 0.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.06); word-break: break-all;">
                        <span style="font-weight: 600;">User-Agent:</span><br>
                        <span style="font-size: 0.8rem;">{{ $contactMessage->user_agent }}</span>
                    </div>
                    <div style="padding-bottom: 0.5rem; margin-bottom: 0.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.06);">
                        <span style="font-weight: 600;">Erstellt:</span> {{ $contactMessage->created_at->format('d.m.Y H:i:s') }}
                    </div>
                    @if($contactMessage->is_read)
                        <div>
                            <span style="font-weight: 600;">Gelesen:</span> {{ $contactMessage->read_at->format('d.m.Y H:i:s') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
