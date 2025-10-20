@extends('layouts.app')

@section('title', 'Nachricht ansehen - Admin')

@section('content')
<style>
    /* CACHE BUST v1.0 - ADMIN CONTACT SHOW - 2025-10-20-22:00 */
    .badge {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
    }
    .badge-feedback { background-color: #dbeafe; color: #1e40af; }
    .badge-feature { background-color: #fef3c7; color: #92400e; }
    .badge-bug { background-color: #fee2e2; color: #991b1b; }
    .badge-other { background-color: #e5e7eb; color: #374151; }
    
    .info-box {
        background: #f9fafb;
        border-left: 4px solid #3b82f6;
        padding: 16px;
        margin-bottom: 16px;
        border-radius: 8px;
    }
    
    .message-box {
        background: white;
        border: 2px solid #e5e7eb;
        padding: 24px;
        border-radius: 12px;
        white-space: pre-wrap;
        word-wrap: break-word;
        line-height: 1.6;
    }
</style>

<div class="max-w-4xl mx-auto p-4 sm:p-6">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.contact-messages.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-bold text-sm hover:bg-gray-300 transition">
            ← Zurück zur Übersicht
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-2 border-green-300 rounded-lg text-green-800 font-bold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-900 to-blue-700 p-6 text-white">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h1 class="text-2xl font-bold mb-2">📬 Kontaktanfrage</h1>
                    <span class="badge badge-{{ $contactMessage->type }}">{{ $contactMessage->type_label }}</span>
                </div>
                <div class="text-right">
                    <div class="text-sm opacity-90">Eingegangen am</div>
                    <div class="font-bold">{{ $contactMessage->created_at->format('d.m.Y H:i') }} Uhr</div>
                    <div class="text-xs opacity-75">{{ $contactMessage->created_at->diffForHumans() }}</div>
                </div>
            </div>
            
            @if(!$contactMessage->is_read)
                <div class="bg-yellow-500 text-yellow-900 px-4 py-2 rounded-lg font-bold text-sm inline-block">
                    🔴 Noch nicht gelesen
                </div>
            @else
                <div class="bg-green-500 text-green-900 px-4 py-2 rounded-lg font-bold text-sm inline-block">
                    ✅ Gelesen am {{ $contactMessage->read_at->format('d.m.Y H:i') }} Uhr
                </div>
            @endif
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Absender Info -->
            <div class="info-box">
                <h2 class="font-bold text-lg text-blue-900 mb-3">👤 Absender</h2>
                <div class="space-y-2">
                    <div>
                        <span class="font-bold text-gray-700">E-Mail:</span>
                        <a href="mailto:{{ $contactMessage->email }}" class="text-blue-600 hover:underline ml-2">
                            {{ $contactMessage->email }}
                        </a>
                    </div>
                    
                    @if($contactMessage->user)
                        <div>
                            <span class="font-bold text-gray-700">Registrierter User:</span>
                            <span class="ml-2">{{ $contactMessage->user->name }} (ID: {{ $contactMessage->user->id }})</span>
                        </div>
                    @else
                        <div class="text-gray-500 text-sm">Nicht registrierter Nutzer</div>
                    @endif
                </div>
            </div>

            <!-- Hermine Kontakt -->
            @if($contactMessage->hermine_contact)
                <div class="info-box" style="border-left-color: #3b82f6;">
                    <h2 class="font-bold text-lg text-blue-900 mb-3">📱 Hermine-Kontakt gewünscht</h2>
                    <div class="space-y-2">
                        <div>
                            <span class="font-bold text-gray-700">Name:</span>
                            <span class="ml-2">{{ $contactMessage->vorname }} {{ $contactMessage->nachname }}</span>
                        </div>
                        <div>
                            <span class="font-bold text-gray-700">Ortsverband:</span>
                            <span class="ml-2">{{ $contactMessage->ortsverband }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Bug Location -->
            @if($contactMessage->type === 'bug' && $contactMessage->error_location)
                <div class="info-box" style="border-left-color: #ef4444;">
                    <h2 class="font-bold text-lg text-red-900 mb-3">🐛 Fehler aufgetreten bei</h2>
                    <div class="text-gray-800">{{ ucfirst($contactMessage->error_location) }}</div>
                </div>
            @endif

            <!-- Message -->
            <div class="mb-6">
                <h2 class="font-bold text-lg text-gray-900 mb-3">💬 Nachricht</h2>
                <div class="message-box">{{ $contactMessage->message }}</div>
            </div>

            <!-- Technische Details (für Admin) -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h2 class="font-bold text-sm text-gray-700 mb-2">🔧 Technische Details</h2>
                <div class="text-xs text-gray-600 space-y-1">
                    <div><strong>ID:</strong> {{ $contactMessage->id }}</div>
                    <div><strong>IP-Adresse:</strong> {{ $contactMessage->ip_address }}</div>
                    <div><strong>User-Agent:</strong> {{ $contactMessage->user_agent }}</div>
                    <div><strong>Erstellt:</strong> {{ $contactMessage->created_at->format('d.m.Y H:i:s') }}</div>
                    @if($contactMessage->is_read)
                        <div><strong>Gelesen:</strong> {{ $contactMessage->read_at->format('d.m.Y H:i:s') }}</div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex gap-3 flex-wrap">
                <a href="mailto:{{ $contactMessage->email }}" 
                   class="px-6 py-3 bg-blue-900 text-yellow-400 rounded-lg font-bold hover:bg-yellow-400 hover:text-blue-900 transition">
                    📧 E-Mail antworten
                </a>
                
                @if(!$contactMessage->is_read)
                    <form method="POST" action="{{ route('admin.contact-messages.mark-read', $contactMessage) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition">
                            ✅ Als gelesen markieren
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.contact-messages.mark-unread', $contactMessage) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-6 py-3 bg-yellow-600 text-white rounded-lg font-bold hover:bg-yellow-700 transition">
                            🔴 Als ungelesen markieren
                        </button>
                    </form>
                @endif
                
                <form method="POST" action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" 
                      onsubmit="return confirm('Wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden!')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition">
                        🗑️ Löschen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
