@extends('layouts.app')
@section('title', 'Fortschritt bearbeiten - THW Trainer Admin')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-7xl mx-auto p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center">
                    <div class="text-4xl mr-4">📊</div>
                    <div>
                        <h1 class="text-3xl font-bold text-blue-800">Fortschritt bearbeiten</h1>
                        <p class="text-gray-600">Verwalte den Lernfortschritt des Benutzers</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.index') }}" 
                   style="display: inline-flex; align-items: center; padding: 12px 24px; background-color: #6b7280; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.2s; box-shadow: 0 4px 15px rgba(107, 114, 128, 0.4), 0 0 20px rgba(107, 114, 128, 0.3), 0 0 40px rgba(107, 114, 128, 0.1);"
                   onmouseover="this.style.backgroundColor='#4b5563'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 6px 20px rgba(75, 85, 99, 0.5), 0 0 30px rgba(75, 85, 99, 0.4), 0 0 50px rgba(75, 85, 99, 0.2)'"
                   onmouseout="this.style.backgroundColor='#6b7280'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(107, 114, 128, 0.4), 0 0 20px rgba(107, 114, 128, 0.3), 0 0 40px rgba(107, 114, 128, 0.1)'">
                    <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Zurück zur Nutzerverwaltung
                </a>
            </div>
            
            <!-- Benutzer Info -->
            <div class="mb-8 bg-white rounded-2xl shadow-xl p-8 border border-blue-100">
                <div class="flex items-center">
                    <div class="text-4xl mr-6">👤</div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-blue-800">{{ $user->name }}</h2>
                        <div class="text-gray-600 mb-2">{{ $user->email }}</div>
                        <div class="flex items-center gap-4 text-sm">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✅ E-Mail bestätigt
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    ❌ E-Mail nicht bestätigt
                                </span>
                            @endif
                            <span class="text-gray-500">
                                Registriert: {{ $user->created_at->format('d.m.Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        
            @if(session('success'))
                <div class="mb-6 p-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 rounded-xl shadow-lg">
                    <div class="flex items-center">
                        <div class="text-2xl mr-3">✅</div>
                        <div class="text-green-800 font-medium">{{ session('success') }}</div>
                    </div>
                </div>
            @endif
            
            <!-- Lehrgänge Sektion -->
            @if($lehrgangProgress && !$lehrgangProgress->isEmpty())
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-blue-800 mb-4 flex items-center">
                        <span class="mr-3">📚</span>
                        Lehrgänge - Fortschrittsübersicht
                    </h2>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @foreach($lehrgangProgress as $lehrgangId => $data)
                            <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                                <!-- Header -->
                                <div class="mb-4 pb-4 border-b border-gray-200">
                                    <h3 class="text-xl font-bold text-blue-800 mb-2">{{ $data['lehrgang']->lehrgang }}</h3>
                                    <p class="text-sm text-gray-600">{{ $data['lehrgang']->beschreibung }}</p>
                                </div>
                                
                                <!-- Gesamt-Statistik -->
                                <div class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold text-gray-700">Gesamtfortschritt</span>
                                        <span class="text-sm font-bold text-blue-700">{{ $data['totalSolved'] }}/{{ $data['totalQuestions'] }} ({{ $data['totalPercent'] }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-300 rounded-full h-2">
                                        <div class="bg-yellow-400 h-2 rounded-full transition-all duration-500" 
                                             style="width: {{ $data['totalPercent'] }}%; box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 0 0 20px rgba(251, 191, 36, 0.4), 0 0 30px rgba(251, 191, 36, 0.2);"></div>
                                    </div>
                                </div>
                                
                                <!-- Lernabschnitte -->
                                @if($data['sectionProgress'])
                                    <div class="mb-3">
                                        <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Lernabschnitte:</p>
                                        <div class="space-y-2 max-h-48 overflow-y-auto">
                                            @foreach($data['sectionProgress'] as $section => $progress)
                                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-semibold text-gray-700">
                                            Abschnitt {{ $section }}
                                            <span class="text-xs text-gray-500">({{ $progress['solved'] }}/{{ $progress['total'] }})</span>
                                        </span>
                                        <span class="text-xs font-bold text-gray-600">{{ $progress['percentage'] }}%</span>
                                                    </div>
                                                    <div class="w-full bg-gray-300 rounded-full h-1.5">
                                                        <div class="bg-green-500 h-1.5 rounded-full transition-all duration-300" 
                                                             style="width: {{ $progress['percentage'] }}%;"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Info Link -->
                                <a href="{{ route('lehrgaenge.show', $data['lehrgang']->slug) }}" 
                                   class="inline-block mt-4 text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                    🔍 Details anzeigen →
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.users.progress.update', $user->id) }}" class="space-y-8">
                @csrf
                @method('PUT')
                
                <!-- Statistiken Dashboard -->
                @php
                    // Berechne echten Fortschritt inkl. 1x richtige Antworten
                    $progressData = \App\Models\UserQuestionProgress::where('user_id', $user->id)->get();
                    $totalProgressPoints = 0;
                    foreach ($progressData as $prog) {
                        $totalProgressPoints += min($prog->consecutive_correct, 2);
                    }
                    $maxProgressPoints = $questions->count() * 2;
                    $trueProgressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                        <div class="flex items-center">
                            <div class="text-4xl mr-4">✅</div>
                            <div>
                                <div class="text-3xl font-bold text-green-800" id="solvedCount">{{ count($solved) }}</div>
                                <div class="text-sm text-green-600 font-medium">Gemeisterte Fragen</div>
                                <div class="text-xs text-gray-500 mt-1">2x richtig in Folge</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                        <div class="flex items-center">
                            <div class="text-4xl mr-4">🔄</div>
                            <div>
                                <div class="text-3xl font-bold text-red-800" id="failedCount">{{ isset($failed) ? count($failed) : 0 }}</div>
                                <div class="text-sm text-red-600 font-medium">Wiederholungsfragen</div>
                                <div class="text-xs text-gray-500 mt-1">Aus Prüfungen</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                        <div class="flex items-center">
                            <div class="text-4xl mr-4">📊</div>
                            <div>
                                <div class="text-3xl font-bold text-blue-800">{{ $trueProgressPercent }}%</div>
                                <div class="text-sm text-blue-600 font-medium">Gesamt-Fortschritt</div>
                                <div class="text-xs text-gray-500 mt-1">Inkl. 1x richtige</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Info-Box: Neue 2x-richtig Logik -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6 rounded-lg">
                    <div class="flex items-start">
                        <div class="text-2xl mr-3">ℹ️</div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-900 mb-2">Wichtig: "2x richtig in Folge" Logik</h3>
                            <p class="text-sm text-blue-800 mb-2">
                                Seit dem Update müssen User jede Frage <strong>2x hintereinander richtig</strong> beantworten, um sie zu meistern.
                            </p>
                            <ul class="text-sm text-blue-700 list-disc list-inside space-y-1">
                                <li><strong>Gelöste Fragen:</strong> Wurden mindestens 2x richtig in Folge beantwortet</li>
                                <li><strong>Wiederholungsfragen:</strong> Nur aus Prüfungen (nicht aus Übungen)</li>
                                <li>Beim Speichern wird automatisch die <code class="bg-blue-100 px-1 rounded">user_question_progress</code> Tabelle aktualisiert</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Gelöste Fragen -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <div class="text-4xl mr-4">✅</div>
                                <div>
                                    <h2 class="text-2xl font-bold text-green-800 mb-2">Gemeisterte Fragen (2x richtig)</h2>
                                    <p class="text-gray-600">Fragen die mindestens 2x in Folge richtig beantwortet wurden</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" onclick="toggleSection('solved-questions')" 
                                        style="display: inline-flex; align-items: center; padding: 8px 16px; background-color: #6b7280; color: white; border-radius: 8px; border: none; cursor: pointer; font-weight: 500; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                        onmouseover="this.style.backgroundColor='#4b5563'; this.style.transform='scale(1.05)'"
                                        onmouseout="this.style.backgroundColor='#6b7280'; this.style.transform='scale(1)'">
                                    <svg id="solved-arrow" style="width: 16px; height: 16px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                    <span id="solved-toggle-text">Aufklappen</span>
                                </button>
                                <button type="button" onclick="selectAll('solved_questions', true)" 
                                        style="display: inline-flex; align-items: center; padding: 8px 16px; background-color: #2563eb; color: white; border-radius: 8px; border: none; cursor: pointer; font-weight: 500; transition: all 0.2s; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4), 0 0 20px rgba(37, 99, 235, 0.3), 0 0 40px rgba(37, 99, 235, 0.1);"
                                        onmouseover="this.style.backgroundColor='#1d4ed8'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 6px 20px rgba(29, 78, 216, 0.5), 0 0 30px rgba(29, 78, 216, 0.4), 0 0 50px rgba(29, 78, 216, 0.2)'"
                                        onmouseout="this.style.backgroundColor='#2563eb'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(37, 99, 235, 0.4), 0 0 20px rgba(37, 99, 235, 0.3), 0 0 40px rgba(37, 99, 235, 0.1)'">
                                    <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Alle auswählen
                                </button>
                                <button type="button" onclick="selectAll('solved_questions', false)" 
                                        style="display: inline-flex; align-items: center; padding: 8px 16px; background-color: #fbbf24; color: #1e3a8a; border-radius: 8px; border: none; cursor: pointer; font-weight: 500; transition: all 0.2s; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1);"
                                        onmouseover="this.style.backgroundColor='#f59e0b'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 6px 20px rgba(245, 158, 11, 0.5), 0 0 30px rgba(245, 158, 11, 0.4), 0 0 50px rgba(245, 158, 11, 0.2)'"
                                        onmouseout="this.style.backgroundColor='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1)'">
                                    <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Alle abwählen
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="solved-questions-content" class="hidden px-6 pb-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                            @foreach($questions as $question)
                                <label class="flex items-start bg-gray-50 rounded-lg p-3 hover:bg-green-50 transition-colors cursor-pointer border border-gray-200">
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
                </div>
            
                <!-- Wiederholungsfragen -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <div class="text-4xl mr-4">🔄</div>
                                <div>
                                    <h2 class="text-2xl font-bold text-red-800 mb-2">Wiederholungsfragen</h2>
                                    <p class="text-gray-600">Markiere Fragen, die in der Prüfung falsch beantwortet wurden</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" onclick="toggleSection('failed-questions')" 
                                        style="display: inline-flex; align-items: center; padding: 8px 16px; background-color: #6b7280; color: white; border-radius: 8px; border: none; cursor: pointer; font-weight: 500; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                        onmouseover="this.style.backgroundColor='#4b5563'; this.style.transform='scale(1.05)'"
                                        onmouseout="this.style.backgroundColor='#6b7280'; this.style.transform='scale(1)'">
                                    <svg id="failed-arrow" style="width: 16px; height: 16px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                    <span id="failed-toggle-text">Aufklappen</span>
                                </button>
                                <button type="button" onclick="selectAll('exam_failed_questions', true)" 
                                        style="display: inline-flex; align-items: center; padding: 8px 16px; background-color: #ef4444; color: white; border-radius: 8px; border: none; cursor: pointer; font-weight: 500; transition: all 0.2s; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4), 0 0 20px rgba(239, 68, 68, 0.3), 0 0 40px rgba(239, 68, 68, 0.1);"
                                        onmouseover="this.style.backgroundColor='#dc2626'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 6px 20px rgba(220, 38, 38, 0.5), 0 0 30px rgba(220, 38, 38, 0.4), 0 0 50px rgba(220, 38, 38, 0.2)'"
                                        onmouseout="this.style.backgroundColor='#ef4444'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(239, 68, 68, 0.4), 0 0 20px rgba(239, 68, 68, 0.3), 0 0 40px rgba(239, 68, 68, 0.1)'">
                                    <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Alle auswählen
                                </button>
                                <button type="button" onclick="selectAll('exam_failed_questions', false)" 
                                        style="display: inline-flex; align-items: center; padding: 8px 16px; background-color: #fbbf24; color: #1e3a8a; border-radius: 8px; border: none; cursor: pointer; font-weight: 500; transition: all 0.2s; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1);"
                                        onmouseover="this.style.backgroundColor='#f59e0b'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 6px 20px rgba(245, 158, 11, 0.5), 0 0 30px rgba(245, 158, 11, 0.4), 0 0 50px rgba(245, 158, 11, 0.2)'"
                                        onmouseout="this.style.backgroundColor='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1)'">
                                    <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Alle abwählen
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="failed-questions-content" class="hidden px-6 pb-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                            @foreach($questions as $question)
                                <label class="flex items-start bg-gray-50 rounded-lg p-3 hover:bg-red-50 transition-colors cursor-pointer border border-gray-200">
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
                </div>
            
                <!-- Aktionen -->
                <div class="bg-white rounded-lg shadow-md p-6 border-2 border-blue-200">
                    <div class="text-center mb-6">
                        <div class="text-4xl mb-4">💾</div>
                        <h3 class="text-xl font-semibold text-blue-800 mb-2">Änderungen speichern</h3>
                        <p class="text-sm text-gray-600">Vergiss nicht, deine Änderungen zu speichern!</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <button type="submit" 
                                style="display: inline-flex; align-items: center; justify-content: center; padding: 16px 32px; background-color: #059669; color: white; font-size: 18px; font-weight: bold; border-radius: 12px; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 8px 16px rgba(5, 150, 105, 0.3);"
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
                               style="display: inline-flex; align-items: center; justify-content: center; padding: 8px 16px; background-color: #8b5cf6; color: white; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.2s; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4), 0 0 20px rgba(139, 92, 246, 0.3), 0 0 40px rgba(139, 92, 246, 0.1);"
                               onmouseover="this.style.backgroundColor='#7c3aed'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 6px 20px rgba(124, 58, 237, 0.5), 0 0 30px rgba(124, 58, 237, 0.4), 0 0 50px rgba(124, 58, 237, 0.2)'"
                               onmouseout="this.style.backgroundColor='#8b5cf6'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(139, 92, 246, 0.4), 0 0 20px rgba(139, 92, 246, 0.3), 0 0 40px rgba(139, 92, 246, 0.1)'">
                                <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                👤 Benutzer bearbeiten
                            </a>
                            <a href="{{ route('admin.users.index') }}" 
                               style="display: inline-flex; align-items: center; justify-content: center; padding: 8px 16px; background-color: #6b7280; color: white; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.2s; box-shadow: 0 4px 15px rgba(107, 114, 128, 0.4), 0 0 20px rgba(107, 114, 128, 0.3), 0 0 40px rgba(107, 114, 128, 0.1);"
                               onmouseover="this.style.backgroundColor='#4b5563'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 6px 20px rgba(75, 85, 99, 0.5), 0 0 30px rgba(75, 85, 99, 0.4), 0 0 50px rgba(75, 85, 99, 0.2)'"
                               onmouseout="this.style.backgroundColor='#6b7280'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(107, 114, 128, 0.4), 0 0 20px rgba(107, 114, 128, 0.3), 0 0 40px rgba(107, 114, 128, 0.1)'">
                                <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                📋 Zur Übersicht
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Lehrgänge Bearbeitungssektion -->
                @if($lehrgangProgress && !$lehrgangProgress->isEmpty())
                    <hr class="my-8 border-gray-300">
                    <h2 class="text-2xl font-bold text-blue-800 mb-4 flex items-center">
                        <span class="mr-3">✏️</span>
                        Lehrgänge - Fortschritt bearbeiten
                    </h2>
                    
                    @foreach($lehrgangProgress as $lehrgangId => $data)
                        <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-6">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h3 class="text-xl font-bold text-blue-800">{{ $data['lehrgang']->lehrgang }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">Lernabschnitte: {{ $data['questions_grouped']->count() }}</p>
                                    </div>
                                    <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-semibold">{{ $data['totalQuestions'] }} Fragen</span>
                                </div>
                            </div>
                            
                            <!-- Lernabschnitte mit Fragen -->
                            <div class="border-t border-gray-200 divide-y divide-gray-200">
                                @foreach($data['questions_grouped'] as $section => $sectionQuestions)
                                    <div class="p-6">
                                        <button type="button" 
                                                onclick="toggleLehrgangSection('lehrgang-{{ $lehrgangId }}-section-{{ $section }}')"
                                                class="w-full flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200 hover:shadow-lg transition-all">
                                            <div class="text-left flex-1">
                                                <p class="font-bold text-blue-800">Lernabschnitt {{ $section }}</p>
                                                <p class="text-sm text-blue-600 mt-1">{{ $sectionQuestions->count() }} Fragen</p>
                                            </div>
                                            <svg id="lehrgang-{{ $lehrgangId }}-section-{{ $section }}-arrow" class="w-5 h-5 text-blue-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        
                                        <div id="lehrgang-{{ $lehrgangId }}-section-{{ $section }}" class="hidden mt-4">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                                @foreach($sectionQuestions as $question)
                                                    @php
                                                        $questionProgress = \App\Models\UserLehrgangProgress::where('user_id', $user->id)
                                                            ->where('lehrgang_question_id', $question->id)
                                                            ->first();
                                                        $isSolved = $questionProgress && $questionProgress->solved;
                                                    @endphp
                                                    <label class="flex items-start bg-gray-50 rounded-lg p-3 hover:bg-blue-50 transition-colors cursor-pointer border {{ $isSolved ? 'border-green-400 bg-green-50' : 'border-gray-200' }}">
                                                        <input type="checkbox" 
                                                               name="lehrgang_{{ $lehrgangId }}_solved[]" 
                                                               value="{{ $question->id }}"
                                                               @if($isSolved) checked @endif
                                                               class="mt-1 mr-3 accent-green-600 w-4 h-4">
                                                        <div class="flex-1 min-w-0">
                                                            <div class="text-sm font-medium text-blue-800">F. {{ $question->nummer }}</div>
                                                            <div class="text-xs text-gray-600 truncate">{{ Str::limit($question->frage, 40) }}</div>
                                                            @if($questionProgress)
                                                                <div class="text-xs text-gray-500 mt-1">✓ {{ $questionProgress->consecutive_correct }}x richtig</div>
                                                            @endif
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
        </form>
    </div>

    <script>
        function toggleSection(sectionId) {
            const content = document.getElementById(sectionId + '-content');
            const arrow = document.getElementById(sectionId + '-arrow');
            const text = document.getElementById(sectionId + '-toggle-text');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                arrow.setAttribute('d', 'M5 15l7-7 7 7'); // Arrow up
                text.textContent = 'Einklappen';
            } else {
                content.classList.add('hidden');
                arrow.setAttribute('d', 'M19 9l-7 7-7-7'); // Arrow down
                text.textContent = 'Aufklappen';
            }
        }
        
        function toggleLehrgangSection(sectionId) {
            const content = document.getElementById(sectionId);
            const arrow = document.getElementById(sectionId + '-arrow');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                arrow.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                arrow.style.transform = 'rotate(0deg)';
            }
        }
        
        function selectAll(name, checked) {
            const checkboxes = document.querySelectorAll('input[name="'+name+'[]"]');
            checkboxes.forEach(cb => {
                cb.checked = checked;
                // Add visual feedback
                const label = cb.closest('label');
                if (checked) {
                    label.classList.add('ring-2', 'ring-green-500', 'ring-opacity-50');
                } else {
                    label.classList.remove('ring-2', 'ring-green-500', 'ring-opacity-50');
                }
            });
            
            // Update counter with animation
            updateCounters();
            
            // Show feedback
            showFeedback(checked ? 'Alle ausgewählt' : 'Alle abgewählt');
        }
        
        function updateCounters() {
            const solvedCount = document.querySelectorAll('input[name="solved_questions[]"]:checked').length;
            const failedCount = document.querySelectorAll('input[name="exam_failed_questions[]"]:checked').length;
            
            // Animate counter changes
            animateCounter('solvedCount', solvedCount);
            animateCounter('failedCount', failedCount);
        }
        
        function animateCounter(elementId, newValue) {
            const element = document.getElementById(elementId);
            const currentValue = parseInt(element.textContent);
            
            if (currentValue !== newValue) {
                element.style.transform = 'scale(1.2)';
                element.style.color = '#059669';
                
                setTimeout(() => {
                    element.textContent = newValue;
                    element.style.transform = 'scale(1)';
                    element.style.color = '';
                }, 150);
            }
        }
        
        function showFeedback(message) {
            // Create temporary feedback element
            const feedback = document.createElement('div');
            feedback.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-50 transform translate-x-full transition-transform duration-300';
            feedback.textContent = message;
            document.body.appendChild(feedback);
            
            // Show feedback
            setTimeout(() => {
                feedback.style.transform = 'translateX(0)';
            }, 100);
            
            // Hide feedback
            setTimeout(() => {
                feedback.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    document.body.removeChild(feedback);
                }, 300);
            }, 2000);
        }
        
        // Event listener for checkbox changes
        document.addEventListener('change', function(e) {
            if (e.target.type === 'checkbox') {
                updateCounters();
                
                // Add visual feedback to individual checkboxes
                const label = e.target.closest('label');
                if (e.target.checked) {
                    label.classList.add('ring-2', 'ring-green-500', 'ring-opacity-50');
                } else {
                    label.classList.remove('ring-2', 'ring-green-500', 'ring-opacity-50');
                }
            }
        });
        
        // Add hover effects to question cards
        document.addEventListener('DOMContentLoaded', function() {
            const labels = document.querySelectorAll('label[class*="group"]');
            labels.forEach(label => {
                label.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                
                label.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
@endsection
