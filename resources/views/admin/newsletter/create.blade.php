@extends('layouts.app')

@section('title', 'Newsletter erstellen - Admin')
@section('description', 'Newsletter an alle User mit E-Mail-Zustimmung senden')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title">Newsletter <span>erstellen</span></h1>
        <p class="page-subtitle">Newsletter an alle User mit E-Mail-Zustimmung senden</p>
    </header>

    <!-- Info-Hinweis -->
    <div class="glass mb-6" style="padding: 1.25rem;">
        <div style="display: flex; align-items: start; gap: 0.75rem;">
            <i class="bi bi-info-circle text-gold" style="font-size: 1.25rem;"></i>
            <div>
                <h3 style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Verfügbare Platzhalter:</h3>
                <p style="font-size: 0.875rem; color: var(--text-secondary);">
                    <code style="background: rgba(255, 255, 255, 0.1); padding: 0.25rem 0.5rem; border-radius: 0.25rem;">@{{name}}</code> - Name des Users |
                    <code style="background: rgba(255, 255, 255, 0.1); padding: 0.25rem 0.5rem; border-radius: 0.25rem;">@{{email}}</code> - E-Mail |
                    <code style="background: rgba(255, 255, 255, 0.1); padding: 0.25rem 0.5rem; border-radius: 0.25rem;">@{{level}}</code> - Level |
                    <code style="background: rgba(255, 255, 255, 0.1); padding: 0.25rem 0.5rem; border-radius: 0.25rem;">@{{points}}</code> - Punkte |
                    <code style="background: rgba(255, 255, 255, 0.1); padding: 0.25rem 0.5rem; border-radius: 0.25rem;">@{{streak}}</code> - Streak
                </p>
            </div>
        </div>
    </div>

    <div class="bento-grid">
        <!-- Editor Seite -->
        <div class="glass-gold hover-lift bento-half" style="padding: 1.5rem;">
            <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-pencil text-gold"></i>
                Newsletter bearbeiten
            </h2>
            
            <form id="newsletterForm">
                @csrf
                
                <!-- Betreff -->
                <div class="mb-4">
                    <label for="subject" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Betreff</label>
                    <input type="text" id="subject" name="subject" required placeholder="z.B. Neue Features im THW-Trainer">
                </div>

                <!-- Rich-Text Editor mit Formatierungs-Toolbar -->
                <div class="mb-4">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Inhalt</label>
                    
                    <!-- Formatierungs-Toolbar -->
                    <div class="mb-2 p-2 border rounded-t-lg flex flex-wrap gap-2" style="background: rgba(255, 255, 255, 0.05); border-color: rgba(255, 255, 255, 0.1);">
                        <button type="button" onclick="formatText('bold')" 
                                style="padding: 6px 12px; background-color: #374151; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: bold;">
                            B
                        </button>
                        <button type="button" onclick="formatText('italic')" 
                                style="padding: 6px 12px; background-color: #374151; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-style: italic;">
                            I
                        </button>
                        <button type="button" onclick="formatText('underline')" 
                                style="padding: 6px 12px; background-color: #374151; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; text-decoration: underline;">
                            U
                        </button>
                        <div style="width: 1px; background-color: #d1d5db;"></div>
                        <button type="button" onclick="alignText('left')" title="Linksbündig"
                                style="padding: 6px 12px; background-color: #374151; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer;">
                            ◀
                        </button>
                        <button type="button" onclick="alignText('center')" title="Zentriert"
                                style="padding: 6px 12px; background-color: #374151; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer;">
                            ▬
                        </button>
                        <button type="button" onclick="alignText('right')" title="Rechtsbündig"
                                style="padding: 6px 12px; background-color: #374151; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer;">
                            ▶
                        </button>
                        <div style="width: 1px; background-color: #d1d5db;"></div>
                        <button type="button" onclick="formatText('insertOrderedList')" 
                                style="padding: 6px 12px; background-color: #374151; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer;">
                            1. Liste
                        </button>
                        <button type="button" onclick="formatText('insertUnorderedList')" 
                                style="padding: 6px 12px; background-color: #374151; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer;">
                            • Liste
                        </button>
                        <div style="width: 1px; background-color: #d1d5db;"></div>
                        <button type="button" onclick="formatHeading('h2')" 
                                style="padding: 6px 12px; background-color: #374151; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: bold;">
                            H2
                        </button>
                        <button type="button" onclick="formatHeading('h3')" 
                                style="padding: 6px 12px; background-color: #374151; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: bold;">
                            H3
                        </button>
                    </div>
                    
                    <!-- Komponenten-Toolbar -->
                    <div class="mb-2 p-2 border-x border-b rounded-b-lg flex flex-wrap gap-2" style="background: rgba(251, 191, 36, 0.05); border-color: rgba(255, 255, 255, 0.1);">
                        <button type="button" onclick="insertPlaceholder()" 
                                style="padding: 6px 12px; background-color: #3b82f6; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; transition: all 0.2s;" 
                                onmouseover="this.style.backgroundColor='#2563eb'" 
                                onmouseout="this.style.backgroundColor='#3b82f6'">
                            @{{...}} Platzhalter
                        </button>
                        <button type="button" onclick="insertInfoCard()" 
                                style="padding: 6px 12px; background-color: #3b82f6; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; transition: all 0.2s;" 
                                onmouseover="this.style.backgroundColor='#2563eb'" 
                                onmouseout="this.style.backgroundColor='#3b82f6'">
                            ℹ️ Info-Card
                        </button>
                        <button type="button" onclick="insertWarningCard()" 
                                style="padding: 6px 12px; background-color: #f59e0b; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; transition: all 0.2s;" 
                                onmouseover="this.style.backgroundColor='#d97706'" 
                                onmouseout="this.style.backgroundColor='#f59e0b'">
                            ⚠️ Warning-Card
                        </button>
                        <button type="button" onclick="insertSuccessCard()" 
                                style="padding: 6px 12px; background-color: #22c55e; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; transition: all 0.2s;" 
                                onmouseover="this.style.backgroundColor='#16a34a'" 
                                onmouseout="this.style.backgroundColor='#22c55e'">
                            ✅ Success-Card
                        </button>
                        <button type="button" onclick="insertErrorCard()" 
                                style="padding: 6px 12px; background-color: #ef4444; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; transition: all 0.2s;" 
                                onmouseover="this.style.backgroundColor='#dc2626'" 
                                onmouseout="this.style.backgroundColor='#ef4444'">
                            ❌ Error-Card
                        </button>
                        <button type="button" onclick="insertGlowButton()" 
                                style="padding: 6px 12px; background-color: #a855f7; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; transition: all 0.2s;" 
                                onmouseover="this.style.backgroundColor='#9333ea'" 
                                onmouseout="this.style.backgroundColor='#a855f7'">
                            🔘 Glow-Button
                        </button>
                        <button type="button" onclick="insertStatBox()" 
                                style="padding: 6px 12px; background-color: #6366f1; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; transition: all 0.2s;" 
                                onmouseover="this.style.backgroundColor='#4f46e5'" 
                                onmouseout="this.style.backgroundColor='#6366f1'">
                            📊 Stat-Box
                        </button>
                    </div>
                    
                    <!-- ContentEditable Editor -->
                    <div id="editor" contenteditable="true"
                         style="min-height: 400px; padding: 16px; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; outline: none; overflow-y: auto; max-height: 600px; color: var(--text-primary);"
                         onfocus="this.style.borderColor='var(--gold-start)'; this.style.background='rgba(255, 255, 255, 0.05)';"
                         onblur="this.style.borderColor='rgba(255, 255, 255, 0.1)'; this.style.background='rgba(255, 255, 255, 0.03)';">
                        <p>Hier deinen Newsletter-Inhalt schreiben...</p>
                    </div>
                    <input type="hidden" id="content" name="content">
                </div>

                <!-- Aktionen -->
                <div style="display: flex; gap: 12px;">
                    <button type="button" id="sendTestBtn" class="btn-secondary" style="flex: 1;">
                        Test-Mail an mich
                    </button>
                    <button type="button" id="sendAllBtn" class="btn-primary" style="flex: 1;">
                        An alle senden
                    </button>
                </div>
            </form>

            <!-- Status-Meldungen -->
            <div id="statusMessage" class="mt-4 hidden"></div>
        </div>

        <!-- Vorschau Seite -->
        <div class="glass-tl hover-lift bento-half" style="padding: 1.5rem;">
            <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-eye text-gold"></i>
                Vorschau
            </h2>
            <div style="border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.5rem; padding: 1rem; background: rgba(255, 255, 255, 0.03); overflow: auto; max-height: 600px;">
                <div id="preview" style="background: rgba(255, 255, 255, 0.05); padding: 1rem; border-radius: 0.5rem;">
                    <p style="color: var(--text-muted); font-style: italic;">Die Vorschau erscheint hier...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Newsletter-Historie -->
    @if(isset($newsletters) && count($newsletters) > 0)
    <div class="glass mt-8" style="padding: 1.5rem;">
        <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-clock-history text-gold"></i>
            Zuletzt gesendet
        </h2>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid rgba(255, 255, 255, 0.1);">
                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-secondary);">Betreff</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-secondary);">Empfänger</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-secondary);">Gesendet von</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-secondary);">Datum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($newsletters as $newsletter)
                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.06); transition: all 0.2s;"
                        onmouseover="this.style.background='rgba(255, 255, 255, 0.03)'"
                        onmouseout="this.style.background='transparent'">
                        <td style="padding: 0.875rem 1rem; font-size: 0.875rem; color: var(--text-primary);">{{ $newsletter->subject }}</td>
                        <td style="padding: 0.875rem 1rem; font-size: 0.875rem; color: var(--text-secondary);">{{ $newsletter->recipients_count }}</td>
                        <td style="padding: 0.875rem 1rem; font-size: 0.875rem; color: var(--text-secondary);">{{ $newsletter->sender->name ?? 'Unbekannt' }}</td>
                        <td style="padding: 0.875rem 1rem; font-size: 0.875rem; color: var(--text-muted);">{{ $newsletter->sent_at?->format('d.m.Y H:i') ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<script>
// Routes für AJAX
const testRoute = '{{ route("admin.newsletter.test") }}';
const sendRoute = '{{ route("admin.newsletter.send") }}';

@verbatim
// Text-Formatierung (Bold, Italic, etc.)
function formatText(command) {
    document.execCommand(command, false, null);
    document.getElementById('editor').focus();
    updatePreview();
}

// Textausrichtung
function alignText(alignment) {
    const alignmentMap = {
        'left': 'justifyLeft',
        'center': 'justifyCenter',
        'right': 'justifyRight'
    };
    document.execCommand(alignmentMap[alignment], false, null);
    document.getElementById('editor').focus();
    updatePreview();
}

// Überschrift formatieren
function formatHeading(tag) {
    document.execCommand('formatBlock', false, tag);
    document.getElementById('editor').focus();
    updatePreview();
}

// HTML an Cursor-Position einfügen
function insertHTML(html) {
    const editor = document.getElementById('editor');
    editor.focus();
    document.execCommand('insertHTML', false, html);
    updatePreview();
}

// Platzhalter einfügen
function insertPlaceholder() {
    const placeholder = prompt('Welchen Platzhalter möchtest du einfügen?\n\n1. name\n2. email\n3. level\n4. points\n5. streak\n\nGib den Namen ein:');
    if (placeholder) {
        insertHTML('{{' + placeholder + '}}');
    }
}

// Info-Card einfügen
function insertInfoCard() {
    const html = '<div class="info-card"><p>Dein Info-Text hier...</p></div><p><br></p>';
    insertHTML(html);
}

// Warning-Card einfügen
function insertWarningCard() {
    const html = '<div class="warning-card"><p>Dein Warning-Text hier...</p></div><p><br></p>';
    insertHTML(html);
}

// Success-Card einfügen
function insertSuccessCard() {
    const html = '<div class="success-card"><p>Dein Success-Text hier...</p></div><p><br></p>';
    insertHTML(html);
}

// Error-Card einfügen
function insertErrorCard() {
    const html = '<div class="error-card"><p>Dein Error-Text hier...</p></div><p><br></p>';
    insertHTML(html);
}

// Glow-Button einfügen (Table + Inline-Styles für maximale Kompatibilität)
function insertGlowButton() {
    const text = prompt('Button-Text:');
    if (!text) return;
    const url = prompt('Link-URL (z.B. https://thw-trainer.de/dashboard):');
    if (url) {
        const html = `
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 16px 0;">
    <tr>
        <td align="center">
            <a href="${url}" target="_blank" class="glow-button" style="display: inline-block; background: linear-gradient(135deg, #00337F, #0055cc); background-color: #00337F; color: #ffffff !important; padding: 12px 32px; text-decoration: none !important; border-radius: 0.5rem; font-weight: 700; font-size: 13px; box-shadow: 0 4px 15px rgba(0, 51, 127, 0.3);">
                <span style="color: #ffffff !important;">${text}</span>
            </a>
        </td>
    </tr>
</table>
<p><br></p>`;
        insertHTML(html);
    }
}

// Stat-Box einfügen
function insertStatBox() {
    const number = prompt('Zahl:');
    if (!number) return;
    const label = prompt('Beschriftung:');
    if (!label) return;
    const html = '<div class="stat-box"><div class="stat-number">' + number + '</div><div class="stat-label">' + label + '</div></div><p><br></p>';
    insertHTML(html);
}

// Vorschau aktualisieren - spiegelt das Mail-Layout aus emails/newsletter.blade.php wider
function updatePreview() {
    const subject = document.getElementById('subject').value;
    const content = document.getElementById('editor').innerHTML;

    // Hidden field aktualisieren
    document.getElementById('content').value = content;

    // Vorschau aktualisieren
    document.getElementById('preview').innerHTML = `
        <div style="background:#f0f2f5;padding:16px;border-radius:0.5rem;">
            <div style="max-width:560px;margin:0 auto;">
                <div style="background:linear-gradient(135deg,#00337F,#0055cc);padding:20px 24px 16px;border-radius:1.5rem 0.5rem 0 0;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:32px;height:32px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:11px;">THW</div>
                        <div style="color:#fff;font-weight:700;font-size:14px;letter-spacing:0.5px;">THW-TRAINER</div>
                    </div>
                </div>
                <div style="background:#ffffff;padding:24px;border-left:3px solid #00337F;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                    <div style="font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;margin-bottom:6px;">Newsletter</div>
                    <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:16px;">${subject || 'Kein Betreff'}</div>
                    <div class="newsletter-content" style="color:#475569;font-size:13px;line-height:1.6;">${content}</div>
                </div>
                <div style="background:#f8fafc;padding:16px 24px;border-radius:0 0 0.5rem 1.5rem;border-top:1px solid #e2e8f0;text-align:center;">
                    <p style="margin:0;font-size:11px;color:#94a3b8;line-height:1.6;"><strong style="color:#64748b;">THW-Trainer</strong> &middot; Dein Lernbegleiter für die THW-Grundausbildung</p>
                </div>
            </div>
        </div>
    `;
}

// Betreff-Änderungen überwachen
document.getElementById('subject').addEventListener('input', updatePreview);

// Content-Änderungen überwachen (MutationObserver für contenteditable)
const editor = document.getElementById('editor');
const observer = new MutationObserver(updatePreview);
observer.observe(editor, { 
    childList: true, 
    subtree: true, 
    characterData: true,
    attributes: true 
});

// Auch bei direktem Tippen
editor.addEventListener('input', updatePreview);
editor.addEventListener('paste', () => setTimeout(updatePreview, 100));

// Initiale Vorschau generieren
updatePreview();

// Platzhalter-Text beim ersten Fokus entfernen
editor.addEventListener('focus', function() {
    if (this.innerHTML === '<p>Hier deinen Newsletter-Inhalt schreiben...</p>') {
        this.innerHTML = '<p><br></p>';
    }
}, { once: true });

// Test-Mail senden
document.getElementById('sendTestBtn').addEventListener('click', function() {
    const btn = this;
    const subject = document.getElementById('subject').value;
    const content = document.getElementById('content').value;

    if (!subject || !content) {
        showMessage('Bitte fülle Betreff und Inhalt aus!', 'error');
        return;
    }

    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = 'Wird gesendet...';
    
    fetch(testRoute, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ subject, content })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        showMessage('Fehler beim Senden: ' + error.message, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Test-Mail an mich';
    });
});

// An alle senden
document.getElementById('sendAllBtn').addEventListener('click', function() {
    const btn = this;
    const subject = document.getElementById('subject').value;
    const content = document.getElementById('content').value;

    if (!subject || !content) {
        showMessage('Bitte fülle Betreff und Inhalt aus!', 'error');
        return;
    }

    if (!confirm('Newsletter wirklich an alle User mit E-Mail-Zustimmung senden?')) {
        return;
    }

    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = 'Wird gesendet...';
    
    fetch(sendRoute, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ subject, content })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            // Formular zurücksetzen
            document.getElementById('subject').value = '';
            document.getElementById('editor').innerHTML = '<p>Hier deinen Newsletter-Inhalt schreiben...</p>';
            updatePreview();
            // Seite nach 2 Sekunden neu laden um Historie zu aktualisieren
            setTimeout(() => location.reload(), 2000);
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        showMessage('Fehler beim Senden: ' + error.message, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'An alle senden';
    });
});

// Status-Nachricht anzeigen
function showMessage(message, type) {
    const statusDiv = document.getElementById('statusMessage');
    if (type === 'success') {
        statusDiv.className = 'glass-success';
        statusDiv.style.cssText = 'margin-top: 1rem; padding: 1rem; border-radius: 0.5rem;';
    } else {
        statusDiv.className = 'glass-error';
        statusDiv.style.cssText = 'margin-top: 1rem; padding: 1rem; border-radius: 0.5rem;';
    }
    statusDiv.textContent = message;
    statusDiv.classList.remove('hidden');

    setTimeout(() => {
        statusDiv.classList.add('hidden');
    }, 5000);
}
@endverbatim
</script>

<style>
/* Editor Styles */
#editor {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    color: #333;
}

#editor p {
    margin: 10px 0;
}

#editor h2,
.newsletter-content h2 {
    font-size: 18px;
    font-weight: 800;
    margin: 20px 0 10px 0;
    color: #0f172a;
    line-height: 1.3;
}

#editor h3,
.newsletter-content h3 {
    font-size: 15px;
    font-weight: 700;
    margin: 16px 0 8px 0;
    color: #0f172a;
    line-height: 1.3;
}

/* Custom Styles im Editor UND in der Vorschau - synchron mit emails/newsletter.blade.php */
#editor .info-card,
#preview .info-card {
    background: #f0f4ff;
    border-left: 3px solid #3b82f6;
    border-radius: 8px;
    padding: 14px 16px;
    margin: 16px 0;
    color: #1e3a5f;
    font-size: 13px;
    line-height: 1.6;
}

#editor .warning-card,
#preview .warning-card {
    background: #fffbeb;
    border-left: 3px solid #f59e0b;
    border-radius: 8px;
    padding: 14px 16px;
    margin: 16px 0;
    color: #92400e;
    font-size: 13px;
    line-height: 1.6;
}

#editor .success-card,
#preview .success-card {
    background: #f0fdf4;
    border-left: 3px solid #22c55e;
    border-radius: 8px;
    padding: 14px 16px;
    margin: 16px 0;
    color: #166534;
    font-size: 13px;
    line-height: 1.6;
}

#editor .error-card,
#preview .error-card {
    background: #fef2f2;
    border-left: 3px solid #ef4444;
    border-radius: 8px;
    padding: 14px 16px;
    margin: 16px 0;
    color: #991b1b;
    font-size: 13px;
    line-height: 1.6;
}

/* Links im Editor anklickbar machen */
#editor a {
    color: #2563eb;
    cursor: pointer;
}

/* Table-basierte Buttons im Editor - synchron mit Standard-Mail-CTA */
#editor table a,
#preview table a {
    display: inline-block;
    background: linear-gradient(135deg, #00337F, #0055cc);
    color: #ffffff !important;
    padding: 12px 32px;
    text-decoration: none;
    border-radius: 0.5rem;
    font-weight: 700;
    font-size: 13px;
    box-shadow: 0 4px 15px rgba(0, 51, 127, 0.3);
}

/* Glow-Button: gleicher Look wie Standard-CTA der anderen Mails */
#editor .glow-button,
#preview .glow-button {
    display: inline-block;
    background: linear-gradient(135deg, #00337F, #0055cc);
    color: #ffffff;
    padding: 12px 32px;
    text-decoration: none;
    border-radius: 0.5rem;
    font-weight: 700;
    font-size: 13px;
    margin: 16px 0;
    box-shadow: 0 4px 15px rgba(0, 51, 127, 0.3);
}

#editor .stat-box,
#preview .stat-box {
    background: #f0f4ff;
    border: 1px solid rgba(0, 51, 127, 0.12);
    border-radius: 2rem;
    padding: 14px 18px;
    margin: 12px 0;
    text-align: center;
}

#editor .stat-number,
#preview .stat-number {
    font-size: 24px;
    font-weight: 800;
    color: #00337F;
    margin: 4px 0;
}

#editor .stat-label,
#preview .stat-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #64748b;
}

/* Preview Styles */
#preview {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    color: #333;
}

#preview p {
    margin: 10px 0;
}

#preview h2 {
    font-size: 24px;
    font-weight: bold;
    margin: 20px 0 10px 0;
    color: #1e40af;
}

#preview h3 {
    font-size: 20px;
    font-weight: bold;
    margin: 16px 0 8px 0;
    color: #1e40af;
}
</style>
@endsection

