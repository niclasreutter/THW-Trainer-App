@extends('layouts.app')
@section('title', 'Fortschritt bearbeiten - THW Trainer Admin')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-blue-800">📊 Fortschritt bearbeiten</h1>
            <a href="{{ route('admin.users.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                ← Zurück zur Nutzerverwaltung
            </a>
        </div>
        
        <!-- Benutzer Info -->
        <div class="mb-8 bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl">👤</div>
                <div class="ml-4">
                    <h2 class="text-xl font-semibold text-blue-800">{{ $user->name }}</h2>
                    <div class="text-sm text-gray-600">{{ $user->email }}</div>
                </div>
            </div>
        </div>
        
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('admin.users.progress.update', $user->id) }}" class="space-y-8">
            @csrf
            @method('PUT')
            
            <!-- Gelöste Fragen -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-blue-800 mb-2">✅ Erfolgreich beantwortete Fragen</h2>
                        <p class="text-sm text-gray-600">Markiere Fragen als bereits erfolgreich beantwortet</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="selectAll('solved_questions', true)" 
                                style="padding: 8px 16px; background-color: #059669; color: white; border-radius: 8px; border: none; cursor: pointer; font-weight: 500; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" 
                                onmouseover="this.style.backgroundColor='#047857'; this.style.transform='scale(1.05)'" 
                                onmouseout="this.style.backgroundColor='#059669'; this.style.transform='scale(1)'">
                            ✅ Alle auswählen
                        </button>
                        <button type="button" onclick="selectAll('solved_questions', false)" 
                                style="padding: 8px 16px; background-color: #9CA3AF; color: white; border-radius: 8px; border: none; cursor: pointer; font-weight: 500; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" 
                                onmouseover="this.style.backgroundColor='#6B7280'; this.style.transform='scale(1.05)'" 
                                onmouseout="this.style.backgroundColor='#9CA3AF'; this.style.transform='scale(1)'">
                            ❌ Alle abwählen
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                    @foreach($questions as $question)
                        <label class="flex items-start bg-gray-50 rounded-lg p-3 hover:bg-green-50 transition-colors cursor-pointer border">
                            <input type="checkbox" name="solved_questions[]" value="{{ $question->id }}" 
                                   @if(in_array($question->id, $solved)) checked @endif 
                                   class="mt-1 mr-3 accent-green-600 w-4 h-4">
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-blue-800">Frage {{ $question->id }}</div>
                                <div class="text-xs text-gray-600 truncate">{{ Str::limit($question->frage, 50) }}</div>
                                <div class="text-xs text-gray-500 mt-1">LA: {{ $question->lernabschnitt }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            
            <!-- Wiederholungsfragen -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-blue-800 mb-2">🔄 Wiederholungsfragen</h2>
                        <p class="text-sm text-gray-600">Markiere Fragen, die in der Prüfung falsch beantwortet wurden</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="selectAll('exam_failed_questions', true)" 
                                style="padding: 8px 16px; background-color: #DC2626; color: white; border-radius: 8px; border: none; cursor: pointer; font-weight: 500; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" 
                                onmouseover="this.style.backgroundColor='#B91C1C'; this.style.transform='scale(1.05)'" 
                                onmouseout="this.style.backgroundColor='#DC2626'; this.style.transform='scale(1)'">
                            ✅ Alle auswählen
                        </button>
                        <button type="button" onclick="selectAll('exam_failed_questions', false)" 
                                style="padding: 8px 16px; background-color: #9CA3AF; color: white; border-radius: 8px; border: none; cursor: pointer; font-weight: 500; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" 
                                onmouseover="this.style.backgroundColor='#6B7280'; this.style.transform='scale(1.05)'" 
                                onmouseout="this.style.backgroundColor='#9CA3AF'; this.style.transform='scale(1)'">
                            ❌ Alle abwählen
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                    @foreach($questions as $question)
                        <label class="flex items-start bg-gray-50 rounded-lg p-3 hover:bg-red-50 transition-colors cursor-pointer border">
                            <input type="checkbox" name="exam_failed_questions[]" value="{{ $question->id }}" 
                                   @if(isset($failed) && in_array($question->id, $failed)) checked @endif 
                                   class="mt-1 mr-3 accent-red-600 w-4 h-4">
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-blue-800">Frage {{ $question->id }}</div>
                                <div class="text-xs text-gray-600 truncate">{{ Str::limit($question->frage, 50) }}</div>
                                <div class="text-xs text-gray-500 mt-1">LA: {{ $question->lernabschnitt }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            
            <!-- Statistiken -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="text-3xl">✅</div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-blue-800" id="solvedCount">{{ count($solved) }}</div>
                            <div class="text-sm text-gray-600">Gelöste Fragen</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="text-3xl">🔄</div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-blue-800" id="failedCount">{{ isset($failed) ? count($failed) : 0 }}</div>
                            <div class="text-sm text-gray-600">Wiederholungsfragen</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="text-3xl">📊</div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-blue-800">{{ round((count($solved) / $questions->count()) * 100) }}%</div>
                            <div class="text-sm text-gray-600">Fortschritt</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Aktionen -->
            <div class="bg-white rounded-lg shadow-md p-6 border-2 border-blue-200">
                <div class="text-center mb-4">
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">📋 Aktionen</h3>
                    <p class="text-sm text-gray-600">Vergiss nicht, deine Änderungen zu speichern!</p>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <button type="submit" 
                            style="width: 100%; max-width: 400px; display: inline-flex; align-items: center; justify-content: center; padding: 16px 32px; background-color: #059669; color: white; font-size: 18px; font-weight: bold; border-radius: 12px; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 8px 16px rgba(5, 150, 105, 0.3); text-decoration: none;"
                            onmouseover="this.style.backgroundColor='#047857'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 12px 24px rgba(4, 120, 87, 0.4)'" 
                            onmouseout="this.style.backgroundColor='#059669'; this.style.transform='scale(1)'; this.style.boxShadow='0 8px 16px rgba(5, 150, 105, 0.3)'">
                        <svg style="width: 24px; height: 24px; margin-right: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        💾 ÄNDERUNGEN SPEICHERN
                    </button>
                </div>
                
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="{{ route('admin.users.edit', $user->id) }}" 
                           class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            👤 Benutzer bearbeiten
                        </a>
                        <a href="{{ route('admin.users.index') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            📋 Zur Übersicht
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function selectAll(name, checked) {
            const checkboxes = document.querySelectorAll('input[name="'+name+'[]"]');
            checkboxes.forEach(cb => cb.checked = checked);
            
            // Update counter
            updateCounters();
        }
        
        function updateCounters() {
            const solvedCount = document.querySelectorAll('input[name="solved_questions[]"]:checked').length;
            const failedCount = document.querySelectorAll('input[name="exam_failed_questions[]"]:checked').length;
            
            document.getElementById('solvedCount').textContent = solvedCount;
            document.getElementById('failedCount').textContent = failedCount;
        }
        
        // Event listener for checkbox changes
        document.addEventListener('change', function(e) {
            if (e.target.type === 'checkbox') {
                updateCounters();
            }
        });
    </script>
@endsection
