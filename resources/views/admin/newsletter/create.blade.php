@extends('layouts.app')

@section('title', 'Newsletter erstellen - Admin')
@section('description', 'Newsletter an alle User mit E-Mail-Zustimmung senden')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-blue-800">📧 Newsletter erstellen</h1>
        <a href="{{ route('admin.users.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            ← Zurück zur Übersicht
        </a>
    </div>

    <!-- Info-Hinweis -->
    <div class="mb-6 p-4 bg-blue-50 border-2 border-blue-300 rounded-lg">
        <div class="flex items-start gap-2">
            <div class="text-xl">ℹ️</div>
            <div>
                <h3 class="font-bold text-blue-800 mb-1">Verfügbare Platzhalter:</h3>
                <p class="text-sm text-blue-700">
                    <code class="bg-blue-100 px-2 py-1 rounded">@{{name}}</code> - Name des Users |
                    <code class="bg-blue-100 px-2 py-1 rounded">@{{email}}</code> - E-Mail |
                    <code class="bg-blue-100 px-2 py-1 rounded">@{{level}}</code> - Level |
                    <code class="bg-blue-100 px-2 py-1 rounded">@{{points}}</code> - Punkte |
                    <code class="bg-blue-100 px-2 py-1 rounded">@{{streak}}</code> - Streak
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Editor Seite -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-blue-800 mb-4">📝 Newsletter bearbeiten</h2>
            
            <form id="newsletterForm">
                @csrf
                
                <!-- Betreff -->
                <div class="mb-4">
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Betreff</label>
                    <input type="text" id="subject" name="subject" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="z.B. Neue Features im THW-Trainer">
                </div>

                <!-- Rich-Text Editor mit Formatierungs-Toolbar -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Inhalt</label>
                    
                    <!-- Formatierungs-Toolbar -->
                    <div class="mb-2 p-2 bg-gray-100 border rounded-t-lg flex flex-wrap gap-2">
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
                    <div class="mb-2 p-2 bg-blue-50 border-x border-b rounded-b-lg flex flex-wrap gap-2">
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
                         style="min-height: 400px; padding: 16px; background: white; border: 1px solid #d1d5db; border-radius: 8px; outline: none; overflow-y: auto; max-height: 600px;"
                         class="focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p>Hier deinen Newsletter-Inhalt schreiben...</p>
                    </div>
                    <input type="hidden" id="content" name="content">
                </div>

                <!-- Aktionen -->
                <div class="flex gap-3">
                    <button type="button" id="sendTestBtn" 
                            class="flex-1 bg-yellow-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-yellow-600 transition-all duration-300 hover:shadow-lg">
                        🧪 Test-Mail an mich
                    </button>
                    <button type="button" id="sendAllBtn"
                            class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition-all duration-300 hover:shadow-lg">
                        📧 An alle senden
                    </button>
                </div>
            </form>

            <!-- Status-Meldungen -->
            <div id="statusMessage" class="mt-4 hidden"></div>
        </div>

        <!-- Vorschau Seite -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-blue-800 mb-4">👁️ Vorschau</h2>
            <div class="border-2 border-gray-200 rounded-lg p-4 bg-gray-50 overflow-auto" style="max-height: 600px;">
                <div id="preview" class="bg-white p-4 rounded">
                    <p class="text-gray-400 italic">Die Vorschau erscheint hier...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Newsletter-Historie -->
    @if(isset($newsletters) && count($newsletters) > 0)
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-blue-800 mb-4">📜 Zuletzt gesendet</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Betreff</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Empfänger</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Gesendet von</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Datum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($newsletters as $newsletter)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">{{ $newsletter->subject }}</td>
                        <td class="px-4 py-3 text-sm">{{ $newsletter->recipients_count }}</td>
                        <td class="px-4 py-3 text-sm">{{ $newsletter->sender->name ?? 'Unbekannt' }}</td>
                        <td class="px-4 py-3 text-sm">{{ $newsletter->sent_at?->format('d.m.Y H:i') ?? '-' }}</td>
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

// Text-Formatierung (Bold, Italic, etc.)
function formatText(command) {
    document.execCommand(command, false, null);
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
    const text = prompt('Text für die Info-Card:');
    if (text) {
        const html = '<div class="info-card"><p>' + text + '</p></div><p><br></p>';
        insertHTML(html);
    }
}

// Warning-Card einfügen
function insertWarningCard() {
    const text = prompt('Text für die Warning-Card:');
    if (text) {
        const html = '<div class="warning-card"><p>' + text + '</p></div><p><br></p>';
        insertHTML(html);
    }
}

// Success-Card einfügen
function insertSuccessCard() {
    const text = prompt('Text für die Success-Card:');
    if (text) {
        const html = '<div class="success-card"><p>' + text + '</p></div><p><br></p>';
        insertHTML(html);
    }
}

// Error-Card einfügen
function insertErrorCard() {
    const text = prompt('Text für die Error-Card:');
    if (text) {
        const html = '<div class="error-card"><p>' + text + '</p></div><p><br></p>';
        insertHTML(html);
    }
}

// Glow-Button einfügen
function insertGlowButton() {
    const text = prompt('Button-Text:');
    if (!text) return;
    const url = prompt('Link-URL:');
    if (url) {
        const html = '<p style="text-align: center; margin: 20px 0;"><a href="' + url + '" style="display: inline-block; background: linear-gradient(to right, #2563eb, #1d4ed8); color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4), 0 0 20px rgba(37, 99, 235, 0.3), 0 0 40px rgba(37, 99, 235, 0.1);">' + text + '</a></p><p><br></p>';
        insertHTML(html);
    }
}

// Stat-Box einfügen
function insertStatBox() {
    const number = prompt('Zahl:');
    if (!number) return;
    const label = prompt('Beschriftung:');
    if (label) {
        const html = '<div class="stat-box"><div class="stat-number">' + number + '</div><div class="stat-label">' + label + '</div></div><p><br></p>';
        insertHTML(html);
    }
}

// Vorschau aktualisieren
function updatePreview() {
    const subject = document.getElementById('subject').value;
    const content = document.getElementById('editor').innerHTML;
    
    // Hidden field aktualisieren
    document.getElementById('content').value = content;
    
    // Vorschau aktualisieren
    document.getElementById('preview').innerHTML = `
        <div style="border-bottom: 3px solid #2563eb; padding-bottom: 10px; margin-bottom: 20px;">
            <div style="font-size: 24px; font-weight: bold; color: #2563eb; margin-bottom: 10px;">THW-Trainer</div>
            <h2 style="margin: 0;">${subject || 'Kein Betreff'}</h2>
        </div>
        ${content}
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
    btn.textContent = '⏳ Wird gesendet...';
    
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
        btn.textContent = '🧪 Test-Mail an mich';
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
    btn.textContent = '⏳ Wird gesendet...';
    
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
        btn.textContent = '📧 An alle senden';
    });
});

// Status-Nachricht anzeigen
function showMessage(message, type) {
    const statusDiv = document.getElementById('statusMessage');
    statusDiv.className = 'mt-4 p-4 rounded-lg ' + 
        (type === 'success' ? 'bg-green-50 border-2 border-green-500 text-green-700' : 'bg-red-50 border-2 border-red-500 text-red-700');
    statusDiv.textContent = message;
    statusDiv.classList.remove('hidden');
    
    setTimeout(() => {
        statusDiv.classList.add('hidden');
    }, 5000);
}
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

#editor h2 {
    font-size: 24px;
    font-weight: bold;
    margin: 20px 0 10px 0;
    color: #1e40af;
}

#editor h3 {
    font-size: 20px;
    font-weight: bold;
    margin: 16px 0 8px 0;
    color: #1e40af;
}

/* Custom Styles im Editor UND in der Vorschau */
#editor .info-card,
#preview .info-card {
    background-color: #eff6ff;
    border: 2px solid #3b82f6;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.3), 0 0 40px rgba(59, 130, 246, 0.1);
}

#editor .warning-card,
#preview .warning-card {
    background-color: #fef3c7;
    border: 2px solid #f59e0b;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
    box-shadow: 0 0 20px rgba(245, 158, 11, 0.3), 0 0 40px rgba(245, 158, 11, 0.1);
}

#editor .success-card,
#preview .success-card {
    background-color: #f0fdf4;
    border: 2px solid #22c55e;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
    box-shadow: 0 0 20px rgba(34, 197, 94, 0.3), 0 0 40px rgba(34, 197, 94, 0.1);
}

#editor .error-card,
#preview .error-card {
    background-color: #fef2f2;
    border: 2px solid #ef4444;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
    box-shadow: 0 0 20px rgba(239, 68, 68, 0.3), 0 0 40px rgba(239, 68, 68, 0.1);
}

/* Links im Editor anklickbar machen */
#editor a {
    color: #2563eb;
    cursor: pointer;
}

/* Glow-Button wird jetzt via Inline-Styles eingefügt, aber für Legacy-Kompatibilität: */
#editor .glow-button,
#preview .glow-button {
    display: inline-block;
    background: linear-gradient(to right, #2563eb, #1d4ed8);
    color: white;
    padding: 15px 30px;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    margin: 20px 0;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4), 0 0 20px rgba(37, 99, 235, 0.3), 0 0 40px rgba(37, 99, 235, 0.1);
}

#editor .stat-box,
#preview .stat-box {
    background-color: #f3f4f6;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    margin: 10px 0;
}

#editor .stat-number,
#preview .stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #2563eb;
    margin: 10px 0;
}

#editor .stat-label,
#preview .stat-label {
    font-size: 14px;
    color: #6b7280;
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

