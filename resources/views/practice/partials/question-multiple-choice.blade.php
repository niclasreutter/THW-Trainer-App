{{-- Practice Partial: Multiple Choice (nur in practice.blade.php; Kopf, Ergebnis und Lösungsweg rendert die Seite). --}}
@php
    $answersOriginal = [
        ['letter' => 'A', 'text' => $question->antwort_a],
        ['letter' => 'B', 'text' => $question->antwort_b],
        ['letter' => 'C', 'text' => $question->antwort_c],
    ];

    if (isset($isCorrect) && isset($answerResult['answer_mapping'])) {
        $mappingArray = $answerResult['answer_mapping'];
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
        $answers = $answersOriginal;
        shuffle($answers);
        $mappingArray = [];
        foreach ($answers as $index => $answer) {
            $mappingArray[$index] = $answer['letter'];
        }
    }

    $mappingJson = json_encode($mappingArray);
    $solution = collect(explode(',', $question->loesung))->map(fn($s) => strtoupper(trim($s)));
@endphp

<input type="hidden" name="answer_mapping" value="{{ $mappingJson }}">

<div class="l-opts">
    @foreach($answers as $index => $answer)
        @if(isset($isCorrect))
            @php
                $isCorrectAnswer = $solution->contains($answer['letter']);
                $isUserAnswer = isset($userAnswer) && $userAnswer->contains($answer['letter']);
                $state = $isCorrectAnswer ? ($isUserAnswer ? 'correct' : 'missed') : ($isUserAnswer ? 'wrong' : 'neutral');
            @endphp
            <div class="l-opt is-{{ $state }}">
                <span class="t">{{ $answer['text'] }}@if($state === 'missed')<em class="l-tag">Richtig</em>@endif</span>
                <span class="l-check"><i class="bi {{ $state === 'wrong' ? 'bi-x-lg' : 'bi-check-lg' }}" aria-hidden="true"></i></span>
            </div>
        @else
            <label class="l-opt">
                <input type="checkbox" name="answer[]" value="{{ $index }}" class="l-hide" onchange="updateSubmitButton()">
                <span class="t">{{ $answer['text'] }}</span>
                <span class="l-check"><i class="bi bi-check-lg" aria-hidden="true"></i></span>
            </label>
        @endif
    @endforeach
</div>
