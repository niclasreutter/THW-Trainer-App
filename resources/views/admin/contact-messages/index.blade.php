@extends('layouts.app')

@section('title', 'Kontaktanfragen - Admin')

@section('content')
<style>
    /* CACHE BUST v1.0 - ADMIN CONTACTS - 2025-10-20-22:00 */
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-feedback { background-color: #dbeafe; color: #1e40af; }
    .badge-feature { background-color: #fef3c7; color: #92400e; }
    .badge-bug { background-color: #fee2e2; color: #991b1b; }
    .badge-other { background-color: #e5e7eb; color: #374151; }
    
    .message-row {
        transition: all 0.2s ease;
    }
    .message-row:hover {
        background-color: #f9fafb !important;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .message-row.unread {
        background-color: #eff6ff;
        font-weight: 600;
    }
</style>

<div class="max-w-7xl mx-auto p-4 sm:p-6">
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-blue-900 mb-2">📬 Kontaktanfragen</h1>
        <p class="text-gray-600">Verwalte eingehende Feedback- und Kontaktanfragen</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-2 border-green-300 rounded-lg text-green-800 font-bold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="text-2xl font-bold text-blue-900">{{ $stats['total'] }}</div>
            <div class="text-xs sm:text-sm text-gray-600">Gesamt</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="text-2xl font-bold text-yellow-900">{{ $stats['unread'] }}</div>
            <div class="text-xs sm:text-sm text-gray-600">Ungelesen</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="text-2xl font-bold text-green-900">{{ $stats['today'] }}</div>
            <div class="text-xs sm:text-sm text-gray-600">Heute</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="text-2xl font-bold text-purple-900">{{ $stats['this_week'] }}</div>
            <div class="text-xs sm:text-sm text-gray-600">Diese Woche</div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search -->
                <div>
                    <label class="block text-xs font-bold mb-1 text-gray-700">Suche</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="E-Mail, Name, Nachricht..."
                           class="w-full p-2 text-sm border rounded-lg focus:border-blue-500">
                </div>
                
                <!-- Type Filter -->
                <div>
                    <label class="block text-xs font-bold mb-1 text-gray-700">Kategorie</label>
                    <select name="type" class="w-full p-2 text-sm border rounded-lg focus:border-blue-500">
                        <option value="">Alle</option>
                        <option value="feedback" {{ request('type') == 'feedback' ? 'selected' : '' }}>💭 Feedback</option>
                        <option value="feature" {{ request('type') == 'feature' ? 'selected' : '' }}>✨ Feature</option>
                        <option value="bug" {{ request('type') == 'bug' ? 'selected' : '' }}>🐛 Bug</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>📧 Sonstiges</option>
                    </select>
                </div>
                
                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-bold mb-1 text-gray-700">Status</label>
                    <select name="status" class="w-full p-2 text-sm border rounded-lg focus:border-blue-500">
                        <option value="">Alle</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>🔴 Ungelesen</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>✅ Gelesen</option>
                    </select>
                </div>
                
                <!-- Hermine Filter -->
                <div>
                    <label class="block text-xs font-bold mb-1 text-gray-700">Hermine</label>
                    <select name="hermine" class="w-full p-2 text-sm border rounded-lg focus:border-blue-500">
                        <option value="">Alle</option>
                        <option value="1" {{ request('hermine') == '1' ? 'selected' : '' }}>📱 Ja</option>
                    </select>
                </div>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-900 text-yellow-400 rounded-lg font-bold text-sm hover:bg-yellow-400 hover:text-blue-900 transition">
                    🔍 Filtern
                </button>
                <a href="{{ route('admin.contact-messages.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-bold text-sm hover:bg-gray-300 transition">
                    ↻ Zurücksetzen
                </a>
            </div>
        </form>
    </div>

    <!-- Messages List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($messages->count() > 0)
            <!-- Bulk Actions -->
            <div class="p-4 bg-gray-50 border-b flex items-center gap-3">
                <input type="checkbox" id="selectAll" class="w-4 h-4" onclick="toggleAllCheckboxes()">
                <label for="selectAll" class="text-sm font-bold">Alle auswählen</label>
                <button onclick="bulkDelete()" class="ml-auto px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-red-700 transition">
                    🗑️ Ausgewählte löschen
                </button>
            </div>
            
            <form id="bulkDeleteForm" method="POST" action="{{ route('admin.contact-messages.bulk-delete') }}">
                @csrf
                @method('DELETE')
                
                <div class="divide-y">
                    @foreach($messages as $message)
                        <div class="message-row p-4 {{ !$message->is_read ? 'unread' : '' }}">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" name="ids[]" value="{{ $message->id }}" class="message-checkbox w-4 h-4 mt-1">
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="badge badge-{{ $message->type }}">{{ $message->type_label }}</span>
                                            @if(!$message->is_read)
                                                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded font-bold">🔴 NEU</span>
                                            @endif
                                            @if($message->hermine_contact)
                                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded font-bold">📱 Hermine</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 whitespace-nowrap">
                                            {{ $message->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <div class="font-bold text-sm text-gray-900">{{ $message->email }}</div>
                                        @if($message->user)
                                            <div class="text-xs text-gray-600">User: {{ $message->user->name }} (ID: {{ $message->user->id }})</div>
                                        @endif
                                        @if($message->hermine_contact)
                                            <div class="text-xs text-blue-700">
                                                {{ $message->vorname }} {{ $message->nachname }} ({{ $message->ortsverband }})
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="text-sm text-gray-700 mb-3 line-clamp-2">
                                        {{ Str::limit($message->message, 150) }}
                                    </div>
                                    
                                    <div class="flex gap-2 flex-wrap">
                                        <a href="{{ route('admin.contact-messages.show', $message) }}" 
                                           class="px-3 py-1 bg-blue-100 text-blue-800 rounded text-xs font-bold hover:bg-blue-200 transition">
                                            👁️ Ansehen
                                        </a>
                                        
                                        @if(!$message->is_read)
                                            <form method="POST" action="{{ route('admin.contact-messages.mark-read', $message) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1 bg-green-100 text-green-800 rounded text-xs font-bold hover:bg-green-200 transition">
                                                    ✅ Als gelesen markieren
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.contact-messages.mark-unread', $message) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-bold hover:bg-yellow-200 transition">
                                                    🔴 Als ungelesen markieren
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}" 
                                              onsubmit="return confirm('Wirklich löschen?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-100 text-red-800 rounded text-xs font-bold hover:bg-red-200 transition">
                                                🗑️ Löschen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </form>
            
            <!-- Pagination -->
            <div class="p-4 bg-gray-50 border-t">
                {{ $messages->links() }}
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                <div class="text-4xl mb-3">📭</div>
                <div class="font-bold">Keine Nachrichten gefunden</div>
                <div class="text-sm">{{ request()->hasAny(['search', 'type', 'status', 'hermine']) ? 'Versuche einen anderen Filter' : 'Es sind noch keine Kontaktanfragen eingegangen' }}</div>
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
