@extends('layouts.app')

@section('title', $ortsverband->name . ' - Einladungen')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Einladungen <span>verwalten</span></h1>
        <p class="page-subtitle">{{ $ortsverband->name }}</p>
    </header>

    @if(session('success'))
    <div class="alert-compact glass-success" style="margin-bottom: 1.5rem;">
        <i class="bi bi-check-circle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">{{ session('success') }}</div>
        </div>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.25rem;">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert-compact glass-error" style="margin-bottom: 1.5rem;">
        <i class="bi bi-exclamation-triangle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">{{ session('error') }}</div>
        </div>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.25rem;">&times;</button>
    </div>
    @endif

    <!-- Bento Grid -->
    <div class="bento-grid-inv">
        <!-- Neue Einladung erstellen -->
        <div class="glass-gold bento-create-inv">
            <div class="section-header" style="margin-bottom: 1.25rem; padding-left: 0; border-left: none;">
                <h2 class="section-title" style="font-size: 1.25rem;">Neue Einladung erstellen</h2>
            </div>

            <form action="{{ route('ortsverband.invitations.store', $ortsverband) }}" method="POST">
                @csrf

                <div style="margin-bottom: 1.25rem;">
                    <label for="name" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.9rem;">
                        Bezeichnung <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           class="input-glass"
                           placeholder="z.B. Grundausbildung 2024, Neue Helfer..."
                           required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <label for="max_uses" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.9rem;">
                            Max. Nutzungen <span style="color: var(--text-muted); font-weight: normal;">(optional)</span>
                        </label>
                        <input type="number"
                               id="max_uses"
                               name="max_uses"
                               class="input-glass"
                               placeholder="Unbegrenzt"
                               min="1">
                    </div>

                    <div>
                        <label for="expires_at" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.9rem;">
                            Gültig bis <span style="color: var(--text-muted); font-weight: normal;">(optional)</span>
                        </label>
                        <input type="date"
                               id="expires_at"
                               name="expires_at"
                               class="input-glass"
                               min="{{ date('Y-m-d') }}"
                               max="{{ now()->addYears(10)->format('Y-m-d') }}">
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">
                    Einladung erstellen
                </button>
            </form>
        </div>

        <!-- Bestehende Einladungen -->
        <div class="glass-tl bento-list-inv">
            <div class="section-header" style="margin-bottom: 1rem; padding-left: 0.75rem;">
                <h2 class="section-title" style="font-size: 1.1rem;">Bestehende Einladungen</h2>
            </div>

            <div class="invitations-list">
                @forelse($invitations as $invitation)
                @php
                    $statusClass = $invitation->is_expired ? 'glass-error' : ($invitation->is_active ? 'glass-success' : 'glass-warning');
                    $statusText = $invitation->is_expired ? 'Abgelaufen' : ($invitation->is_active ? 'Aktiv' : 'Pausiert');
                    $badgeClass = $invitation->is_expired ? 'badge-error' : ($invitation->is_active ? 'badge-success' : 'badge-gold');
                @endphp

                <div class="{{ $statusClass }} invitation-card-item">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                        <div>
                            <div style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">{{ $invitation->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">
                                Erstellt: {{ $invitation->created_at->format('d.m.Y H:i') }}
                                @if($invitation->expires_at)
                                    | Gültig bis: {{ $invitation->expires_at->format('d.m.Y') }}
                                @endif
                            </div>
                        </div>
                        <span class="{{ $badgeClass }}" style="font-size: 0.65rem;">{{ $statusText }}</span>
                    </div>

                    <!-- Link Copy -->
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <input type="text"
                               class="input-glass"
                               value="{{ route('register', ['code' => $invitation->code]) }}"
                               readonly
                               id="link-{{ $invitation->id }}"
                               style="flex: 1; font-size: 0.8rem; padding: 0.5rem 0.75rem; font-family: monospace;">
                        <button type="button" class="btn-secondary btn-sm" onclick="copyToClipboard('link-{{ $invitation->id }}', this)">
                            Kopieren
                        </button>
                    </div>

                    <!-- Code Copy & QR -->
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <input type="text"
                               class="input-glass"
                               value="{{ $invitation->code }}"
                               readonly
                               id="code-{{ $invitation->id }}"
                               style="flex: 1; font-size: 0.85rem; padding: 0.5rem 0.75rem; font-weight: 700;">
                        <button type="button" class="btn-secondary btn-sm" onclick="copyToClipboard('code-{{ $invitation->id }}', this)">
                            Code
                        </button>
                        <button type="button" class="btn-primary btn-sm" onclick="showQRCode({{ $invitation->id }}, '{{ route('ortsverband.invitations.qrcode', [$ortsverband, $invitation]) }}')">
                            QR
                        </button>
                    </div>

                    <!-- Stats -->
                    <div style="display: flex; gap: 1rem; font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.75rem;">
                        <span><i class="bi bi-people"></i> {{ $invitation->current_uses }} Nutzungen</span>
                        <span><i class="bi bi-graph-up"></i> {{ $invitation->max_uses ? 'Max: ' . $invitation->max_uses : 'Unbegrenzt' }}</span>
                    </div>

                    @if(!$invitation->is_expired)
                    <div style="display: flex; gap: 0.5rem;">
                        <form action="{{ route('ortsverband.invitations.toggle', [$ortsverband, $invitation]) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn-ghost btn-sm">
                                {{ $invitation->is_active ? 'Pausieren' : 'Aktivieren' }}
                            </button>
                        </form>

                        <form action="{{ route('ortsverband.invitations.destroy', [$ortsverband, $invitation]) }}"
                              method="POST"
                              style="display: inline;"
                              onsubmit="return confirm('Möchtest du diese Einladung wirklich löschen?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger btn-sm">Löschen</button>
                        </form>
                    </div>
                    @endif
                </div>
                @empty
                <div class="empty-state" style="padding: 2rem;">
                    <div class="empty-state-icon"><i class="bi bi-link-45deg"></i></div>
                    <h3 class="empty-state-title">Keine Einladungen</h3>
                    <p class="empty-state-desc">Erstelle deine erste Einladung, um Mitglieder einzuladen.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Back Link -->
    <div style="text-align: center; margin-top: 2rem;">
        <a href="{{ route('ortsverband.dashboard', $ortsverband) }}" class="btn-ghost btn-sm">
            <i class="bi bi-arrow-left"></i> Zurück zum Dashboard
        </a>
    </div>
</div>

<!-- QR-Code Modal -->
<div id="qr-modal" class="modal-overlay-glass" style="display: none;">
    <div class="modal-glass" style="max-width: 400px; text-align: center;">
        <div class="modal-header-glass">
            <h2 style="font-size: 1.25rem;">QR-Code Einladung</h2>
            <button onclick="closeQRModal()" class="modal-close-btn">&times;</button>
        </div>
        <div class="modal-body-glass">
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.85rem;">
                Scannen Sie diesen QR-Code, um direkt zur Registrierungsseite zu gelangen.
            </p>
            <div id="qr-code-container" style="display: flex; justify-content: center; margin-bottom: 1.5rem;">
                <img id="qr-code-image" src="" alt="QR Code" style="max-width: 100%; height: auto; border-radius: 0.75rem;">
            </div>
            <div style="display: flex; gap: 0.75rem; justify-content: center;">
                <button onclick="downloadQRCode()" class="btn-primary btn-sm">Herunterladen</button>
                <button onclick="printQRCode()" class="btn-secondary btn-sm">Drucken</button>
            </div>
        </div>
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

    .bento-grid-inv {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 1rem;
    }

    .bento-create-inv {
        padding: 1.5rem;
        height: fit-content;
    }

    .bento-list-inv {
        padding: 1.5rem;
    }

    .invitations-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        max-height: 500px;
        overflow-y: auto;
    }

    .invitation-card-item {
        padding: 1rem;
        border-radius: 0.75rem;
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
    }

    @media (max-width: 900px) {
        .bento-grid-inv {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }
    }

    @media (max-width: 480px) {
        .invitation-card-item > div:first-child {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>
@endpush

<script>
let currentQRCodeUrl = '';

function copyToClipboard(inputId, button) {
    const input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999);

    navigator.clipboard.writeText(input.value).then(() => {
        const originalText = button.innerHTML;
        button.innerHTML = 'Kopiert!';

        setTimeout(() => {
            button.innerHTML = originalText;
        }, 2000);
    });
}

function showQRCode(invitationId, qrCodeUrl) {
    currentQRCodeUrl = qrCodeUrl;
    const modal = document.getElementById('qr-modal');
    const img = document.getElementById('qr-code-image');

    img.src = qrCodeUrl;
    modal.style.display = 'flex';

    modal.onclick = function(e) {
        if (e.target === modal) {
            closeQRModal();
        }
    };
}

function closeQRModal() {
    const modal = document.getElementById('qr-modal');
    modal.style.display = 'none';
}

function downloadQRCode() {
    const link = document.createElement('a');
    link.href = currentQRCodeUrl;
    link.download = 'thw-trainer-einladung-qr-code.png';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function printQRCode() {
    const img = document.getElementById('qr-code-image');
    const printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>THW-Trainer QR-Code</title>');
    printWindow.document.write('<style>body { display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; } img { max-width: 80%; height: auto; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<img src="' + img.src + '" />');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
    }, 250);
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeQRModal();
    }
});
</script>
@endsection
