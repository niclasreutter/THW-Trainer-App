@extends('layouts.app')

@section('title', $ortsverband->name . ' - Einladungen')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
@keyframes dash-rise {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
.dash-container > .space-y-4 > * {
    animation: dash-rise 0.45s cubic-bezier(0.22,1,0.36,1) both;
}
.dash-container > .space-y-4 > *:nth-child(1) { animation-delay: 0.03s; }
.dash-container > .space-y-4 > *:nth-child(2) { animation-delay: 0.07s; }
.dash-container > .space-y-4 > *:nth-child(3) { animation-delay: 0.11s; }
.dash-container > .space-y-4 > *:nth-child(4) { animation-delay: 0.15s; }
.dash-container > .space-y-4 > *:nth-child(5) { animation-delay: 0.19s; }
@media (prefers-reduced-motion: reduce) {
    .dash-container > .space-y-4 > * { animation: none; }
}

.ov-item {
    display: flex; align-items: center; gap: 0.625rem;
    padding: 0.5rem 0.25rem; transition: background 150ms ease; border-radius: 0.375rem;
}
.ov-item:hover { background: rgba(255,255,255,0.03); }
html.light-mode .ov-item:hover { background: rgba(0,0,0,0.03); }
.ov-item + .ov-item { border-top: 1px solid rgba(255,255,255,0.04); }
html.light-mode .ov-item + .ov-item { border-top-color: rgba(0,0,0,0.06); }

.inv-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    max-height: 500px;
    overflow-y: auto;
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
html.light-mode .modal-close-btn {
    background: rgba(0, 0, 0, 0.06);
}
html.light-mode .modal-close-btn:hover {
    background: rgba(0, 0, 0, 0.12);
}

@media (max-width: 480px) {
    .inv-header-row {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>
@endpush

@section('content')
<div class="dash-container">

    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Verwaltung</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Einladungen verwalten</h1>
        <p class="text-sm" style="color: var(--text-muted);">{{ $ortsverband->name }}</p>
    </div>

    @if(session('success'))
    <div class="glass p-4" style="border-radius:0.75rem;margin-bottom:1rem;display:flex;align-items:center;gap:0.75rem;border-left:3px solid #22c55e;">
        <i class="bi bi-check-circle" style="color:#22c55e;font-size:1.1rem;flex-shrink:0;"></i>
        <span style="font-size:0.875rem;color:var(--text-primary);flex:1;">{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1.25rem;">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="glass p-4" style="border-radius:0.75rem;margin-bottom:1rem;display:flex;align-items:center;gap:0.75rem;border-left:3px solid #ef4444;">
        <i class="bi bi-exclamation-triangle" style="color:#ef4444;font-size:1.1rem;flex-shrink:0;"></i>
        <span style="font-size:0.875rem;color:var(--text-primary);flex:1;">{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1.25rem;">&times;</button>
    </div>
    @endif

    <div class="flex gap-3 mb-6" style="flex-wrap: wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#5b9aff;-webkit-text-fill-color:#5b9aff;">{{ $invitations->count() }}</div>
            <div class="gami-pill__label">Gesamt</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#5b9aff;-webkit-text-fill-color:#5b9aff;">{{ $invitations->where('is_active', true)->where('is_expired', false)->count() }}</div>
            <div class="gami-pill__label">Aktiv</div>
        </div>
    </div>

    <div class="space-y-4">

        {{-- Create new invitation --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">NEUE EINLADUNG ERSTELLEN</span>

            <form action="{{ route('ortsverband.invitations.store', $ortsverband) }}" method="POST" style="margin-top: 1rem;">
                @csrf

                <div style="margin-bottom: 1.25rem;">
                    <label for="name" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.875rem;">
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
                        <label for="max_uses" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.875rem;">
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
                        <label for="expires_at" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.875rem;">
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

        {{-- Existing invitations --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">BESTEHENDE EINLADUNGEN</span>

            <div class="inv-list" style="margin-top: 0.75rem;">
                @forelse($invitations as $invitation)
                @php
                    $isExpired = $invitation->is_expired;
                    $isActive = $invitation->is_active;
                    $statusText = $isExpired ? 'Abgelaufen' : ($isActive ? 'Aktiv' : 'Pausiert');
                    $statusColor = $isExpired ? '#ef4444' : ($isActive ? '#22c55e' : '#eab308');
                    $statusBg = $isExpired ? 'rgba(239,68,68,0.12)' : ($isActive ? 'rgba(34,197,94,0.12)' : 'rgba(234,179,8,0.12)');
                @endphp

                <div class="glass p-4" style="border-radius:0.75rem;">
                    <div class="inv-header-row" style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                        <div>
                            <div style="font-weight: 700; color: var(--text-primary); font-size: 0.875rem; margin-bottom: 0.25rem;">{{ $invitation->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">
                                Erstellt: {{ $invitation->created_at->format('d.m.Y H:i') }}
                                @if($invitation->expires_at)
                                    &middot; Gültig bis: {{ $invitation->expires_at->format('d.m.Y') }}
                                @endif
                            </div>
                        </div>
                        <span style="font-size:0.6rem;padding:0.125rem 0.4rem;border-radius:9999px;background:{{ $statusBg }};color:{{ $statusColor }};font-weight:700;text-transform:uppercase;letter-spacing:0.04em;flex-shrink:0;">{{ $statusText }}</span>
                    </div>

                    {{-- Link Copy --}}
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

                    {{-- Code Copy & QR --}}
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

                    {{-- Stats --}}
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
                <div style="text-align: center; padding: 2rem;">
                    <p style="font-size: 0.875rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">Keine Einladungen</p>
                    <p style="font-size: 0.8rem; color: var(--text-secondary);">Erstelle deine erste Einladung, um Mitglieder einzuladen.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Back Link --}}
        <div style="text-align: center; padding-top: 0.5rem;">
            <a href="{{ route('ortsverband.dashboard', $ortsverband) }}" class="btn-ghost btn-sm">
                Zurück zum Dashboard
            </a>
        </div>

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
