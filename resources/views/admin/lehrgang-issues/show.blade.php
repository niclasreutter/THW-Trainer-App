@extends('layouts.app')
@section('title', 'Fehlermeldung - Admin')

@section('content')
<div class="min-h-screen bg-gray-100 py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">🐛 Fehlermeldung</h1>
                @if($issue->lehrgangQuestion)
                    <p class="text-lg text-gray-600">{{ $issue->lehrgangQuestion->lehrgang->lehrgang }}</p>
                @else
                    <p class="text-lg text-red-600 font-semibold">⚠️ Frage wurde gelöscht</p>
                @endif
            </div>
            <a href="{{ route('admin.lehrgang-issues.index') }}" class="text-blue-600 hover:text-blue-800 font-bold text-lg">← Zurück</a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-8 text-green-800 rounded-r-lg">
                ✓ {{ session('success') }}
            </div>
        @endif

        <!-- Full Width Question Card at Top -->
        @if($issue->lehrgangQuestion)
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">📋 Frage</h2>
                
                <div class="mb-6 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                    <p class="text-lg font-semibold text-gray-800 leading-relaxed">
                        {{ $issue->lehrgangQuestion->frage }}
                    </p>
                </div>

                <!-- Answer Options -->
                <div class="space-y-4 mb-8">
                    <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-blue-400">
                        <div class="flex gap-4">
                            <span class="inline-flex items-center justify-center w-10 h-10 bg-blue-500 text-white font-bold rounded-full flex-shrink-0">A</span>
                            <p class="text-gray-700 leading-relaxed pt-1">{{ $issue->lehrgangQuestion->antwort_a }}</p>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-blue-400">
                        <div class="flex gap-4">
                            <span class="inline-flex items-center justify-center w-10 h-10 bg-blue-500 text-white font-bold rounded-full flex-shrink-0">B</span>
                            <p class="text-gray-700 leading-relaxed pt-1">{{ $issue->lehrgangQuestion->antwort_b }}</p>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-blue-400">
                        <div class="flex gap-4">
                            <span class="inline-flex items-center justify-center w-10 h-10 bg-blue-500 text-white font-bold rounded-full flex-shrink-0">C</span>
                            <p class="text-gray-700 leading-relaxed pt-1">{{ $issue->lehrgangQuestion->antwort_c }}</p>
                        </div>
                    </div>
                </div>

                <!-- Solution -->
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                    <p class="text-sm font-semibold text-gray-600 mb-2">✓ Richtige Antwort(en):</p>
                    <p class="text-xl font-bold text-green-600">{{ $issue->lehrgangQuestion->loesung }}</p>
                </div>

                <p class="text-xs text-gray-500 mt-4">Frage-ID: {{ $issue->lehrgangQuestion->id }}</p>
            </div>
        @else
            <div class="bg-red-50 border-l-4 border-red-400 p-8 rounded-r-lg mb-8">
                <p class="text-red-800 font-semibold text-lg">⚠️ Diese Frage wurde gelöscht oder existiert nicht mehr.</p>
                <p class="text-red-700 text-sm mt-2">Frage-ID war: {{ $issue->lehrgang_question_id }}</p>
            </div>
        @endif

        <!-- 2/3 Left and 1/3 Right Layout -->
        <div class="flex flex-col lg:flex-row gap-8 lg:items-stretch">
            
            <!-- Left Column: Report Details (2/3 width) -->
            <div class="w-full flex" style="flex: 0 0 calc(66.666% - 1.5rem);">
                <div class="bg-white rounded-lg shadow-lg p-8 flex flex-col flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">📊 Meldungsdetails</h2>
                    
                    <!-- Overview Section -->
                    <div class="space-y-5 mb-6">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <span class="text-gray-700 font-semibold">Gesamtmeldungen:</span>
                            <span class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full font-bold text-lg">{{ $issue->report_count }}×</span>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <span class="text-gray-700 font-semibold">Zuletzt gemeldet von:</span>
                            <span class="text-gray-800 font-medium">{{ $issue->reportedByUser?->name ?? 'Anonym' }}</span>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <span class="text-gray-700 font-semibold">Zuletzt aktualisiert:</span>
                            <span class="text-gray-800 font-medium">{{ $issue->updated_at ? $issue->updated_at->format('d.m.Y H:i') : 'Nicht verfügbar' }}</span>
                        </div>
                    </div>

                    <!-- Chat-like Message Display -->
                    <div class="flex-1 flex flex-col min-h-0">
                        <div class="border-t border-gray-200 pt-4 mb-3">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">💬 Meldungen</h3>
                        </div>
                        
                        <!-- Scrollable chat area -->
                        <div class="flex-1 overflow-y-auto bg-gray-50 rounded-lg p-4 border border-gray-200 space-y-4">
                            @forelse($issue->reports as $report)
                                <!-- User Message Bubble -->
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold text-sm">
                                        {{ substr($report->user?->name ?? 'A', 0, 1) }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-baseline justify-between mb-2">
                                            <span class="font-semibold text-gray-900 text-sm">{{ $report->user?->name ?? 'Anonym' }}</span>
                                            <span class="text-xs text-gray-500">{{ $report->created_at->format('d.m.Y H:i') }}</span>
                                        </div>
                                        @if($report->message)
                                            <div class="bg-white rounded-lg rounded-tl-none p-4 shadow-sm border border-gray-200">
                                                <p class="text-gray-800 text-base leading-relaxed">{{ $report->message }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <!-- Fallback: Show old latest_message if no reports exist yet -->
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold text-sm">
                                        {{ substr($issue->reportedByUser?->name ?? 'A', 0, 1) }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-baseline justify-between mb-2">
                                            <span class="font-semibold text-gray-900 text-sm">{{ $issue->reportedByUser?->name ?? 'Anonym' }}</span>
                                            <span class="text-xs text-gray-500">{{ $issue->updated_at ? $issue->updated_at->format('d.m.Y H:i') : '' }}</span>
                                        </div>
                                        @if($issue->latest_message)
                                            <div class="bg-white rounded-lg rounded-tl-none p-4 shadow-sm border border-gray-200">
                                                <p class="text-gray-800 text-base leading-relaxed">{{ $issue->latest_message }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Info and Admin (1/3 width) -->
            <div class="w-full flex flex-col gap-8" style="flex: 0 0 calc(33.333% - 1.5rem);">
                <!-- Info Card -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">ℹ️ Informationen</h3>
                    
                    <div class="space-y-4 text-sm">
                        @if($issue->lehrgangQuestion)
                            <div class="pb-3 border-b">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Lehrgang</p>
                                <p class="text-gray-800 font-semibold">{{ $issue->lehrgangQuestion->lehrgang->lehrgang }}</p>
                            </div>

                            <div class="pb-3 border-b">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Lernabschnitt</p>
                                <p class="text-gray-800 font-semibold">{{ $issue->lehrgangQuestion->lernabschnitt }}</p>
                            </div>

                            <div class="pb-3 border-b">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Frage-Nr.</p>
                                <p class="text-gray-800 font-semibold">{{ $issue->lehrgangQuestion->nummer }}</p>
                            </div>
                        @else
                            <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                                <p class="text-red-700 text-xs font-semibold">Frage wurde gelöscht</p>
                            </div>
                        @endif

                        <div class="pt-2">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Status</p>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                                @if($issue->status === 'open') bg-red-100 text-red-800
                                @elseif($issue->status === 'in_review') bg-yellow-100 text-yellow-800
                                @elseif($issue->status === 'resolved') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($issue->status === 'open') 🔴 Offen
                                @elseif($issue->status === 'in_review') 🟡 In Bearbeitung
                                @elseif($issue->status === 'resolved') 🟢 Gelöst
                                @else ⚫ Abgelehnt
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Admin Panel -->
                <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">⚙️ Bearbeitung</h2>
                    
                    <form method="POST" action="{{ route('admin.lehrgang-issues.update', ['lehrgang_issue' => $issue->id]) }}" class="space-y-4 flex flex-col flex-1">
                        @csrf
                        @method('PUT')
                        
                        <!-- Status Select -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2 uppercase">Status</label>
                            <select name="status" class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                                <option value="open" {{ $issue->status === 'open' ? 'selected' : '' }}>🔴 Offen</option>
                                <option value="in_review" {{ $issue->status === 'in_review' ? 'selected' : '' }}>🟡 In Bearbeitung</option>
                                <option value="resolved" {{ $issue->status === 'resolved' ? 'selected' : '' }}>🟢 Gelöst</option>
                                <option value="rejected" {{ $issue->status === 'rejected' ? 'selected' : '' }}>⚫ Abgelehnt</option>
                            </select>
                        </div>

                        <!-- Admin Notes -->
                        <div class="flex-1 flex flex-col min-h-0">
                            <label class="block text-xs font-bold text-gray-700 mb-2 uppercase">Notizen</label>
                            <textarea name="admin_notes" 
                                      class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition resize-none flex-1"
                                      maxlength="1000"
                                      placeholder="Notizen...">{{ $issue->admin_notes ?? '' }}</textarea>
                            <div class="text-xs text-gray-500 mt-1">
                                <span id="noteCount">{{ strlen($issue->admin_notes ?? '') }}</span>/1000
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-2 pt-2 border-t mt-auto">
                            <button type="submit" class="flex-1 px-3 py-2 bg-blue-600 text-white font-bold text-sm rounded-lg hover:bg-blue-700 transition">
                                ✓ Speichern
                            </button>
                            
                            <button type="button" onclick="confirmDelete()" class="flex-1 px-3 py-2 bg-red-600 text-white font-bold text-sm rounded-lg hover:bg-red-700 transition">
                                🗑️ Löschen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteForm" method="POST" action="{{ route('admin.lehrgang-issues.destroy', ['lehrgang_issue' => $issue->id]) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function confirmDelete() {
        if (confirm('Willst du diese Fehlermeldung wirklich löschen?')) {
            document.getElementById('deleteForm').submit();
        }
    }
    
    // Character Counter
    document.querySelector('textarea[name="admin_notes"]').addEventListener('input', function() {
        document.getElementById('noteCount').textContent = this.value.length;
    });
</script>
@endsection
