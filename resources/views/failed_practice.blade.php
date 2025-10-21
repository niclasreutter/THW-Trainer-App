@extends('layouts.app')
@section('title', 'THW Fehlerwiederholung - Falsche Fragen üben')
@section('description', 'Wiederhole deine falschen THW Theoriefragen. Übe gezielt die Fragen, die du in Prüfungen falsch beantwortet hast.')

@section('content')
@php
    // Hole Antwort-Details aus Session (falls vorhanden)
    $answerResult = session('answer_result');
    $hasAnswerResult = $answerResult && isset($answerResult['question_id']) && $answerResult['question_id'] == $question->id;
    
    // Hole Gamification Result aus Session
    $gamificationResult = session('gamification_result');
    
    if ($hasAnswerResult) {
        $isCorrect = $answerResult['is_correct'];
        $userAnswer = collect($answerResult['user_answer']);
        $questionProgress = (object)['consecutive_correct' => $answerResult['question_progress']];
        
        // Lösche BEIDE Sessions nach dem Auslesen (sie gehören zusammen)
        session()->forget(['answer_result', 'gamification_result']);
    } else {
        $isCorrect = null;
        $userAnswer = null;
        $questionProgress = null;
    }
@endphp
<style>
    /* CACHE BUST v3.0 - FAILED PRACTICE REBUILD - 2025-10-21 */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }
    
    /* ===== MOBILE OPTIMIERUNG (nur unter 640px) ===== */
    @media (max-width: 640px) {
        /* Footer und Navigation ausblenden */
        footer {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
        }
        
        nav {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
        }
        
        /* Body auf Vollbild */
        body {
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
            height: 100vh !important;
        }
        
        /* Main Container auf volle Höhe mit Scroll */
        main {
            height: 100vh !important;
            max-height: 100vh !important;
            padding: 0 !important;
            margin: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            -webkit-overflow-scrolling: touch !important;
            position: relative !important;
        }
        
        /* Practice Container ohne Margins */
        #practiceContainer {
            margin: 0 !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            padding: 0.75rem !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            max-width: 100% !important;
            width: 100% !important;
            min-height: 100vh !important;
        }
        
        /* Alle responsive Klassen überschreiben */
        .sm\:mt-4, .sm\:mt-8, .sm\:p-4, .sm\:p-6, 
        .sm\:rounded-lg, .sm\:shadow-lg {
            margin-top: 0 !important;
            padding: 0.75rem !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }
        
        /* Mobile Header */
        .mobile-header {
            position: sticky !important;
            top: 0 !important;
            z-index: 50 !important;
            background: white !important;
            border-bottom: 1px solid #e5e7eb !important;
            padding: 0.5rem 0.75rem !important;
            margin: 0 !important;
        }
        
        /* Mobile Back Button */
        .mobile-back-btn {
            background: #1e3a8a !important;
            color: #fbbf24 !important;
            border: none !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.375rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            text-decoration: none !important;
        }
        
        .mobile-back-btn:hover {
            background: #fbbf24 !important;
            color: #1e3a8a !important;
        }
        
        /* Mobile Progress Bar */
        .mobile-progress {
            background: #e5e7eb !important;
            height: 0.5rem !important;
            border-radius: 0.25rem !important;
            overflow: hidden !important;
            margin: 0.5rem 0 !important;
        }
        
        .mobile-progress-fill {
            background: linear-gradient(90deg, #10b981, #059669) !important;
            height: 100% !important;
            transition: width 0.3s ease !important;
        }
        
        /* Mobile Bookmark Button */
        .mobile-bookmark-btn {
            background: #f3f4f6 !important;
            border: 1px solid #d1d5db !important;
            padding: 0.5rem !important;
            border-radius: 0.375rem !important;
            color: #374151 !important;
            font-size: 0.75rem !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.25rem !important;
        }
        
        .mobile-bookmark-btn.bookmarked {
            background: #fbbf24 !important;
            color: #1e3a8a !important;
            border-color: #f59e0b !important;
        }
    }
    
    /* ===== DESKTOP STYLES ===== */
    @media (min-width: 641px) {
        .mobile-header {
            display: none !important;
        }
        
        .desktop-progress {
            display: block !important;
        }
    }
    
    @media (max-width: 640px) {
        .desktop-progress {
            display: none !important;
        }
    }
</style>

<div id="practiceContainer" class="min-h-[calc(100vh-6rem)] sm:min-h-[calc(100vh-8rem)] p-2 sm:p-4 mx-auto max-w-4xl">
    <!-- Mobile Header (nur auf Mobile sichtbar) -->
    <div class="mobile-header sm:hidden">
        <div class="flex items-center justify-between mb-2">
            <a href="{{ route('practice.menu') }}" class="mobile-back-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Zurück
            </a>
            <button onclick="toggleBookmark({{ $question->id }})" class="mobile-bookmark-btn" id="mobileBookmarkBtn">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                </svg>
                <span id="mobileBookmarkText">Speichern</span>
            </button>
        </div>
        <div class="mobile-progress">
            <div class="mobile-progress-fill" style="width: {{ $progressPercent }}%"></div>
        </div>
        <div class="text-xs text-gray-600 text-center">
            {{ $progress }} von {{ $total }} Fragen bearbeitet ({{ $progressPercent }}%)
        </div>
    </div>

    <!-- Desktop Progress Bar (nur auf Desktop sichtbar) -->
    <div class="desktop-progress hidden sm:block mb-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Fehlerwiederholung</span>
            <span class="text-sm text-gray-600">{{ $progress }} von {{ $total }} Fragen bearbeitet ({{ $progressPercent }}%)</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
        </div>
    </div>

    <!-- Desktop Bookmark Button (nur auf Desktop sichtbar) -->
    <div class="hidden sm:flex justify-end mb-4">
        <button onclick="toggleBookmark({{ $question->id }})" class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-yellow-100 border border-gray-300 rounded-lg transition-colors" id="desktopBookmarkBtn">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
            </svg>
            <span id="desktopBookmarkText">Speichern</span>
        </button>
    </div>
    
    <form method="POST" action="{{ route('failed.submit') }}" id="failedPracticeForm">
        @csrf
        <input type="hidden" name="question_id" value="{{ $question->id }}">
        
        @php
            // Erstelle ein Array mit den Antworten
            $answersOriginal = [
                ['letter' => 'A', 'text' => $question->antwort_a],
                ['letter' => 'B', 'text' => $question->antwort_b],
                ['letter' => 'C', 'text' => $question->antwort_c],
            ];
            
            // Wenn eine Antwort angezeigt wird (isCorrect gesetzt), nutze das gespeicherte Mapping
            if (isset($isCorrect) && isset($answerResult['answer_mapping'])) {
                $mappingArray = $answerResult['answer_mapping'];
                
                // Sortiere $answers entsprechend dem Mapping
                $answers = [];
                foreach ($mappingArray as $position => $letter) {
                    foreach ($answersOriginal as $ans) {
                        if ($ans['letter'] === $letter) {
                            $answers[$position] = $ans;
                            break;
                        }
                    }
                }
                ksort($answers);
            } else {
                // Neue Frage: shuffle
                $answers = $answersOriginal;
                shuffle($answers);
                
                // Erstelle Mapping: Position -> Buchstabe
                $mappingArray = [];
                foreach ($answers as $index => $answer) {
                    $mappingArray[$index] = $answer['letter'];
                }
            }
            
            $mappingJson = json_encode($mappingArray);
            $solution = collect(explode(',', $question->loesung))->map(fn($s) => trim($s));
        @endphp
        
        <input type="hidden" name="answer_mapping" value="{{ $mappingJson }}">
        
        <div class="mb-2 sm:mb-3 p-2 sm:p-3 border rounded-lg bg-gray-50 shadow-sm sm:hover:shadow-md sm:transition-shadow sm:duration-300">
            <div class="mb-1 text-[9px] sm:text-[10px] text-gray-500 flex items-center gap-1">
                <span>ID: {{ $question->id }}</span>
                <span class="mx-0.5 sm:mx-1">&middot;</span>
                <span>Lernabschnitt: {{ $question->lernabschnitt ?? '-' }}.{{ $question->nummer ?? '-' }}</span>
            </div>
            <div class="mb-1 font-bold text-xs sm:text-sm">Frage:</div>
            <div class="mb-2 text-xs sm:text-sm">{{ $question->frage }}</div>
            <div class="mb-2">
                <label class="block mb-1 font-semibold text-xs sm:text-sm">Antwortmöglichkeiten:</label>
                <div class="flex flex-col gap-1.5 sm:gap-1.5">
                    @foreach($answers as $index => $answer)
                        @php
                            $isCorrectAnswer = $solution->contains($answer['letter']);
                            $isUserAnswer = isset($userAnswer) && $userAnswer->contains($answer['letter']);
                            $isChecked = isset($isCorrect) && $isUserAnswer;
                        @endphp
                        <label class="inline-flex items-start p-1.5 sm:p-1.5 rounded-lg hover:bg-gray-50 transition-all duration-200 cursor-pointer">
                            @if(isset($isCorrect))
                                @if($isCorrectAnswer)
                                    <span class="mr-1.5 sm:mr-1.5 text-green-600 text-base sm:text-base">✅</span>
                                @elseif($isUserAnswer)
                                    <span class="mr-1.5 sm:mr-1.5 text-red-600 text-base sm:text-base">❌</span>
                                @else
                                    <span class="mr-1.5 sm:mr-1.5 text-gray-400 text-base sm:text-base">⚪</span>
                                @endif
                            @endif
                            <input type="checkbox" name="answer[]" value="{{ $index }}"
                                @if($isChecked) checked @endif
                                @if(isset($isCorrect)) disabled @endif
                                class="mr-1.5 sm:mr-1.5 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 mt-0.5">
                            <span class="ml-1 sm:ml-1 text-xs sm:text-sm {{ isset($isCorrect) && $isChecked ? ($isCorrectAnswer ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold') : '' }}">
                                {{ $answer['text'] }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        @if(!isset($isCorrect))
            <button type="submit" id="submitBtn" class="w-full text-center font-bold text-xs sm:text-base py-2.5 sm:py-2.5 px-4 rounded-lg border-none cursor-pointer transition-all duration-300" 
                    style="background-color: #1e3a8a; color: #fbbf24; box-shadow: 0 0 15px rgba(30, 58, 138, 0.3);" 
                    onmouseover="if(!this.disabled) { this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 20px rgba(251, 191, 36, 0.4)'; }" 
                    onmouseout="if(!this.disabled) { this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 15px rgba(30, 58, 138, 0.3)'; }"
                    onclick="this.disabled=true; this.innerHTML='<span class=\'inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-yellow-400 mr-2\'></span>Wird ausgewertet...'; setTimeout(() => this.form.submit(), 100);">
                Antwort absenden
            </button>
        @else
            <div class="text-center">
                <a href="{{ route('failed.index') }}" class="inline-block text-center font-bold text-xs sm:text-base py-2.5 sm:py-2.5 px-4 rounded-lg border-none cursor-pointer transition-all duration-300" 
                   style="background-color: #1e3a8a; color: #fbbf24; box-shadow: 0 0 15px rgba(30, 58, 138, 0.3);" 
                   onmouseover="this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 0 20px rgba(251, 191, 36, 0.4)';" 
                   onmouseout="this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 0 15px rgba(30, 58, 138, 0.3)';">
                    Nächste Frage
                </a>
            </div>
        @endif
    </form>
</div>

<!-- Popup Container -->
<div id="popupContainer" class="fixed top-4 right-4 z-50"></div>

<script>
// Cache Bust v3.0 - Failed Practice Rebuild
console.log('Failed Practice v3.0 loaded');

// Bookmark Toggle Function
function toggleBookmark(questionId) {
    fetch('{{ route("bookmarks.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ question_id: questionId })
    })
    .then(response => response.json())
    .then(data => {
        updateBookmarkIconState(data.bookmarked);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Update Bookmark Icon State
function updateBookmarkIconState(isBookmarked) {
    const mobileBtn = document.getElementById('mobileBookmarkBtn');
    const desktopBtn = document.getElementById('desktopBookmarkBtn');
    const mobileText = document.getElementById('mobileBookmarkText');
    const desktopText = document.getElementById('desktopBookmarkText');
    
    if (isBookmarked) {
        if (mobileBtn) {
            mobileBtn.classList.add('bookmarked');
            mobileText.textContent = 'Gespeichert';
        }
        if (desktopBtn) {
            desktopBtn.classList.add('bookmarked');
            desktopText.textContent = 'Gespeichert';
        }
    } else {
        if (mobileBtn) {
            mobileBtn.classList.remove('bookmarked');
            mobileText.textContent = 'Speichern';
        }
        if (desktopBtn) {
            desktopBtn.classList.remove('bookmarked');
            desktopText.textContent = 'Speichern';
        }
    }
}

// Setup Mobile Layout
function setupMobileLayout() {
    if (window.innerWidth <= 640) {
        document.body.style.overflow = 'hidden';
        document.body.style.margin = '0';
        document.body.style.padding = '0';
        document.body.style.height = '100vh';
    }
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    setupMobileLayout();
    
    // Check if question is bookmarked
    fetch('{{ route("bookmarks.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ question_id: {{ $question->id }}, check_only: true })
    })
    .then(response => response.json())
    .then(data => {
        if (data.bookmarked !== undefined) {
            updateBookmarkIconState(data.bookmarked);
        }
    })
    .catch(error => {
        console.error('Bookmark check error:', error);
    });
});

// Show Popup Function
function showPopup(message, type = 'info', duration = 3000) {
    const popupContainer = document.getElementById('popupContainer');
    
    // Remove existing popups
    popupContainer.innerHTML = '';
    
    const popup = document.createElement('div');
    popup.className = `animate-fade-in bg-white border-l-4 p-4 rounded-lg shadow-lg max-w-sm ${
        type === 'success' ? 'border-green-500' : 
        type === 'error' ? 'border-red-500' : 
        'border-blue-500'
    }`;
    
    popup.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                ${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">${message}</p>
            </div>
        </div>
    `;
    
    popupContainer.appendChild(popup);
    
    // Auto remove after duration
    setTimeout(() => {
        if (popup.parentNode) {
            popup.parentNode.removeChild(popup);
        }
    }, duration);
}

// Show Answer Result Popup
@if($hasAnswerResult)
    @if($isCorrect)
        showPopup('🎉 Richtig! +{{ $gamificationResult['points'] ?? 10 }} Punkte', 'success');
    @else
        showPopup('❌ Falsch. Richtige Antworten sind markiert.', 'error');
    @endif
@endif

// Show Gamification Popup
@if($gamificationResult)
    @if($gamificationResult['achievement'])
        setTimeout(() => {
            showPopup('🏆 Erfolg freigeschaltet: {{ $gamificationResult['achievement']['name'] }}', 'success', 5000);
        }, 1000);
    @endif
@endif
</script>
@endsection