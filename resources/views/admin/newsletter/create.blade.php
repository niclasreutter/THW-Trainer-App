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

                <!-- TinyMCE Editor -->
                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Inhalt</label>
                    <textarea id="content" name="content"></textarea>
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

<!-- TinyMCE einbinden -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
// TinyMCE initialisieren
tinymce.init({
    selector: '#content',
    height: 400,
    menubar: false,
    plugins: 'code link lists',
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link | code | infocard warningcard successcard errorcard glowbutton statbox | placeholders',
    
    // Custom Buttons für Komponenten
    setup: function(editor) {
        // Platzhalter Button
        editor.ui.registry.addMenuButton('placeholders', {
            text: '{{...}} Platzhalter',
            fetch: function(callback) {
                var items = [
                    {
                        type: 'menuitem',
                        text: 'Name ({{name}})',
                        onAction: function() {
                            editor.insertContent('{{name}}');
                        }
                    },
                    {
                        type: 'menuitem',
                        text: 'E-Mail ({{email}})',
                        onAction: function() {
                            editor.insertContent('{{email}}');
                        }
                    },
                    {
                        type: 'menuitem',
                        text: 'Level ({{level}})',
                        onAction: function() {
                            editor.insertContent('{{level}}');
                        }
                    },
                    {
                        type: 'menuitem',
                        text: 'Punkte ({{points}})',
                        onAction: function() {
                            editor.insertContent('{{points}}');
                        }
                    },
                    {
                        type: 'menuitem',
                        text: 'Streak ({{streak}})',
                        onAction: function() {
                            editor.insertContent('{{streak}}');
                        }
                    }
                ];
                callback(items);
            }
        });

        // Info-Card Button
        editor.ui.registry.addButton('infocard', {
            text: 'ℹ️ Info-Card',
            onAction: function() {
                editor.windowManager.open({
                    title: 'Info-Card einfügen',
                    body: {
                        type: 'panel',
                        items: [
                            {
                                type: 'textarea',
                                name: 'text',
                                label: 'Text'
                            }
                        ]
                    },
                    buttons: [
                        {
                            type: 'cancel',
                            text: 'Abbrechen'
                        },
                        {
                            type: 'submit',
                            text: 'Einfügen',
                            primary: true
                        }
                    ],
                    onSubmit: function(api) {
                        var data = api.getData();
                        var html = '<div class="info-card"><p>' + data.text + '</p></div>';
                        editor.insertContent(html);
                        api.close();
                    }
                });
            }
        });

        // Warning-Card Button
        editor.ui.registry.addButton('warningcard', {
            text: '⚠️ Warning-Card',
            onAction: function() {
                editor.windowManager.open({
                    title: 'Warning-Card einfügen',
                    body: {
                        type: 'panel',
                        items: [
                            {
                                type: 'textarea',
                                name: 'text',
                                label: 'Text'
                            }
                        ]
                    },
                    buttons: [
                        {
                            type: 'cancel',
                            text: 'Abbrechen'
                        },
                        {
                            type: 'submit',
                            text: 'Einfügen',
                            primary: true
                        }
                    ],
                    onSubmit: function(api) {
                        var data = api.getData();
                        var html = '<div class="warning-card"><p>' + data.text + '</p></div>';
                        editor.insertContent(html);
                        api.close();
                    }
                });
            }
        });

        // Success-Card Button
        editor.ui.registry.addButton('successcard', {
            text: '✅ Success-Card',
            onAction: function() {
                editor.windowManager.open({
                    title: 'Success-Card einfügen',
                    body: {
                        type: 'panel',
                        items: [
                            {
                                type: 'textarea',
                                name: 'text',
                                label: 'Text'
                            }
                        ]
                    },
                    buttons: [
                        {
                            type: 'cancel',
                            text: 'Abbrechen'
                        },
                        {
                            type: 'submit',
                            text: 'Einfügen',
                            primary: true
                        }
                    ],
                    onSubmit: function(api) {
                        var data = api.getData();
                        var html = '<div class="success-card"><p>' + data.text + '</p></div>';
                        editor.insertContent(html);
                        api.close();
                    }
                });
            }
        });

        // Error-Card Button
        editor.ui.registry.addButton('errorcard', {
            text: '❌ Error-Card',
            onAction: function() {
                editor.windowManager.open({
                    title: 'Error-Card einfügen',
                    body: {
                        type: 'panel',
                        items: [
                            {
                                type: 'textarea',
                                name: 'text',
                                label: 'Text'
                            }
                        ]
                    },
                    buttons: [
                        {
                            type: 'cancel',
                            text: 'Abbrechen'
                        },
                        {
                            type: 'submit',
                            text: 'Einfügen',
                            primary: true
                        }
                    ],
                    onSubmit: function(api) {
                        var data = api.getData();
                        var html = '<div class="error-card"><p>' + data.text + '</p></div>';
                        editor.insertContent(html);
                        api.close();
                    }
                });
            }
        });

        // Glow Button Button
        editor.ui.registry.addButton('glowbutton', {
            text: '🔘 Glow-Button',
            onAction: function() {
                editor.windowManager.open({
                    title: 'Glow-Button einfügen',
                    body: {
                        type: 'panel',
                        items: [
                            {
                                type: 'input',
                                name: 'text',
                                label: 'Button-Text'
                            },
                            {
                                type: 'input',
                                name: 'url',
                                label: 'Link-URL'
                            }
                        ]
                    },
                    buttons: [
                        {
                            type: 'cancel',
                            text: 'Abbrechen'
                        },
                        {
                            type: 'submit',
                            text: 'Einfügen',
                            primary: true
                        }
                    ],
                    onSubmit: function(api) {
                        var data = api.getData();
                        var html = '<p style="text-align: center;"><a href="' + data.url + '" class="glow-button">' + data.text + '</a></p>';
                        editor.insertContent(html);
                        api.close();
                    }
                });
            }
        });

        // Statistik-Box Button
        editor.ui.registry.addButton('statbox', {
            text: '📊 Stat-Box',
            onAction: function() {
                editor.windowManager.open({
                    title: 'Statistik-Box einfügen',
                    body: {
                        type: 'panel',
                        items: [
                            {
                                type: 'input',
                                name: 'number',
                                label: 'Zahl'
                            },
                            {
                                type: 'input',
                                name: 'label',
                                label: 'Beschriftung'
                            }
                        ]
                    },
                    buttons: [
                        {
                            type: 'cancel',
                            text: 'Abbrechen'
                        },
                        {
                            type: 'submit',
                            text: 'Einfügen',
                            primary: true
                        }
                    ],
                    onSubmit: function(api) {
                        var data = api.getData();
                        var html = '<div class="stat-box"><div class="stat-number">' + data.number + '</div><div class="stat-label">' + data.label + '</div></div>';
                        editor.insertContent(html);
                        api.close();
                    }
                });
            }
        });

        // Update Vorschau bei Änderungen
        editor.on('change keyup', function() {
            updatePreview();
        });
    },
    
    // Erlaube unsichere Content-Klassen (für unsere Custom-Styles)
    extended_valid_elements: 'div[class|style],a[class|href|style]',
    valid_children: '+body[style]'
});

// Vorschau aktualisieren
function updatePreview() {
    const subject = document.getElementById('subject').value;
    const content = tinymce.get('content').getContent();
    
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

// Test-Mail senden
document.getElementById('sendTestBtn').addEventListener('click', function() {
    const btn = this;
    const subject = document.getElementById('subject').value;
    const content = tinymce.get('content').getContent();
    
    if (!subject || !content) {
        showMessage('Bitte fülle Betreff und Inhalt aus!', 'error');
        return;
    }
    
    btn.disabled = true;
    btn.textContent = '⏳ Wird gesendet...';
    
    fetch('{{ route("admin.newsletter.test") }}', {
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
    const content = tinymce.get('content').getContent();
    
    if (!subject || !content) {
        showMessage('Bitte fülle Betreff und Inhalt aus!', 'error');
        return;
    }
    
    if (!confirm('Newsletter wirklich an alle User mit E-Mail-Zustimmung senden?')) {
        return;
    }
    
    btn.disabled = true;
    btn.textContent = '⏳ Wird gesendet...';
    
    fetch('{{ route("admin.newsletter.send") }}', {
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
            tinymce.get('content').setContent('');
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
/* TinyMCE Custom Styles in der Vorschau */
#preview .info-card {
    background-color: #eff6ff;
    border: 2px solid #3b82f6;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.3), 0 0 40px rgba(59, 130, 246, 0.1);
}

#preview .warning-card {
    background-color: #fef3c7;
    border: 2px solid #f59e0b;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
    box-shadow: 0 0 20px rgba(245, 158, 11, 0.3), 0 0 40px rgba(245, 158, 11, 0.1);
}

#preview .success-card {
    background-color: #f0fdf4;
    border: 2px solid #22c55e;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
    box-shadow: 0 0 20px rgba(34, 197, 94, 0.3), 0 0 40px rgba(34, 197, 94, 0.1);
}

#preview .error-card {
    background-color: #fef2f2;
    border: 2px solid #ef4444;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
    box-shadow: 0 0 20px rgba(239, 68, 68, 0.3), 0 0 40px rgba(239, 68, 68, 0.1);
}

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

#preview .stat-box {
    background-color: #f3f4f6;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    margin: 10px 0;
}

#preview .stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #2563eb;
    margin: 10px 0;
}

#preview .stat-label {
    font-size: 14px;
    color: #6b7280;
}
</style>
@endsection

