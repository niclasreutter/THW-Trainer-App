@extends('layouts.app')
@section('title', 'THW Theorie üben - Interaktive Fragen mit Lernfortschritt')
@section('description', 'Übe THW Theoriefragen mit deinem persönlichen Lernfortschritt. Markiere schwierige Fragen, filtere nach Lernabschnitten und verfolge deinen Erfolg. Kostenlos und effektiv!')
@section('content')
<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
    @if(session('gamification_result'))
        @php $result = session('gamification_result'); @endphp
        <div class="mb-6 p-4 bg-green-100 border border-green-300 rounded-lg shadow-lg animate-pulse">
            <div class="flex items-center">
                <div class="text-2xl mr-3">🎉</div>
                <div>
                    <div class="font-medium text-green-800">
                        +{{ $result['points_awarded'] }} Punkte! 
                        @if($result['level_up'])
                            🎊 Level UP! Neues Level: {{ $result['new_level'] }}
                        @endif
                    </div>
                    <div class="text-sm text-green-600">{{ $result['reason'] }}</div>
                </div>
            </div>
        </div>
        @php session()->forget('gamification_result'); @endphp
    @endif

    @if($question)
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-2xl font-bold">
                @if(isset($mode))
                    @switch($mode)
                        @case('unsolved')
                            🎯 Ungelöste Fragen
                            @break
                        @case('section')
                            📖 Lernabschnitt {{ session('practice_parameter') }}
                            @break
                        @case('search')
                            🔍 Suche: "{{ session('practice_parameter') }}"
                            @break
                        @case('bookmarked')
                            🔖 Gespeicherte Fragen
                            @break
                        @default
                            📚 Alle Fragen
                    @endswitch
                @else
                    Theorie üben
                @endif
            </h2>
            <a href="{{ route('practice.menu') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Zurück zum Menü</a>
        </div>
        
        <div class="mb-4 text-sm text-gray-600">
            @php
                // Nur bei 100% wirklich 100% anzeigen, sonst aufrunden vermeiden
                $progressPercent = $total > 0 ? ($progress == $total ? 100 : floor($progress / $total * 100)) : 0;
            @endphp
            Fortschritt: {{ $progress }}/{{ $total }}
            <div class="w-full bg-gray-200 rounded-full h-4 mt-1 mb-2">
                <div class="bg-yellow-400 h-4 rounded-full transition-all duration-300 shadow-lg" 
                     style="width: {{ $progressPercent }}%; box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 0 0 20px rgba(251, 191, 36, 0.4), 0 0 30px rgba(251, 191, 36, 0.2);"></div>
            </div>
            <span class="text-xs text-gray-500">{{ $progressPercent }}% abgeschlossen</span>
        </div>
        
        <!-- Bookmark Button außerhalb des Forms -->
        <div class="mb-4 flex justify-end">
            @php
                $user = Auth::user();
                $bookmarked = is_array($user->bookmarked_questions ?? null) 
                    ? $user->bookmarked_questions 
                    : json_decode($user->bookmarked_questions ?? '[]', true);
                $isBookmarked = in_array($question->id, $bookmarked);
            @endphp
            
            <button type="button" 
                    class="flex items-center gap-2 px-3 py-1 hover:bg-gray-100 hover:shadow-md hover:scale-105 rounded-lg transition-all duration-300 cursor-pointer"
                    title="{{ $isBookmarked ? 'Aus Lesezeichen entfernen' : 'Zu Lesezeichen hinzufügen' }}"
                    id="bookmarkBtn"
                    onclick="toggleBookmark({{ $question->id }}, {{ $isBookmarked ? 'true' : 'false' }})">
                <svg class="w-5 h-5 {{ $isBookmarked ? 'text-yellow-500 fill-current' : 'text-gray-400' }}" 
                     viewBox="0 0 20 20" stroke="currentColor" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" id="bookmarkIcon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M5 5a2 2 0 012-2h6a2 2 0 012 2v10l-5-3-5 3V5z"></path>
                </svg>
                <span class="text-sm text-gray-600" id="bookmarkText">
                    {{ $isBookmarked ? 'Gespeichert' : 'Speichern' }}
                </span>
            </button>
        </div>
        
        <form method="POST" action="{{ route('practice.submit') }}">
            @csrf
            <input type="hidden" name="question_id" value="{{ $question->id }}">
            <div class="mb-6 p-6 border rounded-lg bg-gray-50 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="mb-2 text-xs text-gray-500 flex items-center gap-2">
                    <span>ID: {{ $question->id }}</span>
                    <span class="mx-2">&middot;</span>
                    <span>Lernabschnitt: {{ $question->lernabschnitt ?? '-' }}.{{ $question->nummer ?? '-' }}</span>
                </div>
                <div class="mb-2 font-bold">Frage:</div>
                <div class="mb-4">{{ $question->frage }}</div>
                <div class="mb-4">
                    <label class="block mb-2 font-semibold">Antwortmöglichkeiten:</label>
                    <div class="flex flex-col gap-3">
                        @foreach(['A','B','C'] as $option)
                            @php
                                $solution = collect(explode(',', $question->loesung))->map(fn($s) => trim($s));
                                $isCorrectAnswer = $solution->contains($option);
                                $isUserAnswer = isset($userAnswer) && $userAnswer->contains($option);
                                $isChecked = isset($isCorrect) && $isUserAnswer;
                            @endphp
                            <label class="inline-flex items-center p-2 rounded-lg hover:bg-gray-50 transition-all duration-200 cursor-pointer">
                                @if(isset($isCorrect))
                                    @if($isCorrectAnswer)
                                        <span class="mr-2 text-green-600 text-lg">✅</span>
                                    @elseif($isUserAnswer)
                                        <span class="mr-2 text-red-600 text-lg">❌</span>
                                    @else
                                        <span class="mr-2 text-gray-400 text-lg">⚪</span>
                                    @endif
                                @endif
                                <input type="checkbox" name="answer[]" value="{{ $option }}"
                                    @if($isChecked) checked @endif
                                    @if(isset($isCorrect)) disabled @endif
                                    class="mr-2 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                <span class="ml-2 {{ isset($isCorrect) && $isChecked ? ($isCorrectAnswer ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold') : '' }}">
                                    {{ $option }}: {{ $question['antwort_'.strtolower($option)] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @if(!isset($isCorrect))
                <button type="submit" id="submitBtn" class="bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900 hover:shadow-lg hover:scale-105 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:hover:shadow-none" disabled>Antwort absenden</button>
            @elseif(isset($isCorrect) && $isCorrect)
                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 font-bold animate-pulse">✅ Richtig! Weiter zur nächsten Frage...</div>
                <a href="{{ route('practice.index') }}" class="mt-4 inline-block bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900 hover:shadow-lg hover:scale-105 transition-all duration-300">Nächste Frage</a>
            @elseif(isset($isCorrect) && !$isCorrect)
                <div class="mt-4 p-4 rounded-lg font-bold shadow-lg" style="background-color: rgba(239, 68, 68, 0.1); border: 2px solid rgba(239, 68, 68, 0.3); color: #dc2626; box-shadow: 0 0 15px rgba(239, 68, 68, 0.3), 0 0 30px rgba(239, 68, 68, 0.1);">
                    <div class="flex items-center">
                        <div class="text-2xl mr-3">❌</div>
                        <span>Leider falsch. Die richtigen Antworten sind markiert.</span>
                    </div>
                </div>
                <a href="{{ route('practice.index', ['skip_id' => $question->id]) }}" class="mt-4 inline-block bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900 hover:shadow-lg hover:scale-105 transition-all duration-300">Weiter zur nächsten Frage</a>
            @endif
        </form>
        
        <script>
            // Bookmark AJAX Function
            function toggleBookmark(questionId, currentlyBookmarked) {
                const btn = document.getElementById('bookmarkBtn');
                const text = document.getElementById('bookmarkText');
                const icon = document.getElementById('bookmarkIcon');
                
                // Loading State
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                text.textContent = 'Speichere...';
                icon.classList.add('animate-spin');
                
                const formData = new FormData();
                formData.append('question_id', questionId);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                fetch('{{ route("bookmarks.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.is_bookmarked) {
                            icon.classList.add('text-yellow-500', 'fill-current');
                            icon.classList.remove('text-gray-400');
                            icon.setAttribute('fill', 'currentColor');
                            text.textContent = 'Gespeichert';
                            btn.setAttribute('title', 'Aus Lesezeichen entfernen');
                            btn.setAttribute('onclick', `toggleBookmark(${questionId}, true)`);
                        } else {
                            icon.classList.remove('text-yellow-500', 'fill-current');
                            icon.classList.add('text-gray-400');
                            icon.setAttribute('fill', 'none');
                            text.textContent = 'Speichern';
                            btn.setAttribute('title', 'Zu Lesezeichen hinzufügen');
                            btn.setAttribute('onclick', `toggleBookmark(${questionId}, false)`);
                        }
                        
                        // Kurzes Feedback mit Animation
                        const originalText = text.textContent;
                        text.textContent = data.is_bookmarked ? 'Gespeichert!' : 'Entfernt!';
                        btn.classList.add('animate-pulse');
                        setTimeout(() => {
                            text.textContent = originalText;
                            btn.classList.remove('animate-pulse');
                        }, 1500);
                    }
                })
                .catch(error => {
                    console.error('Bookmark error:', error);
                    alert('Fehler beim Speichern der Markierung');
                })
                .finally(() => {
                    // Reset Loading State
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    icon.classList.remove('animate-spin');
                });
            }
            
            @if(!isset($isCorrect))
            // Original checkbox logic - nur wenn Frage noch nicht beantwortet
            const checkboxes = document.querySelectorAll('input[type=checkbox][name="answer[]"]');
            const submitBtn = document.getElementById('submitBtn');
            function updateBtn() {
                let checked = 0;
                checkboxes.forEach(cb => { if(cb.checked) checked++; });
                submitBtn.disabled = checked === 0;
            }
            checkboxes.forEach(cb => cb.addEventListener('change', updateBtn));
            updateBtn();
            @endif
        </script>
    @else
        <div class="text-center text-lg mb-4">Du hast alle Fragen in diesem Modus bearbeitet! 🎉</div>
        <div class="text-center">
            <a href="{{ route('practice.menu') }}" class="inline-block bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900 hover:shadow-lg hover:scale-105 transition-all duration-300 mr-4">Zurück zum Übungsmenü</a>
            <a href="{{ route('dashboard') }}" class="inline-block bg-gray-600 text-white px-6 py-2 rounded font-bold hover:bg-gray-700 hover:shadow-lg hover:scale-105 transition-all duration-300">Dashboard</a>
        </div>
    @endif
</div>
@endsection

