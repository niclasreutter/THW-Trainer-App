@extends('layouts.app')
@section('title', 'Frage erstellen')
@section('content')
<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Neue Frage erstellen</h2>
    <form method="POST" action="{{ route('admin.questions.store') }}">
        @csrf
        <div class="mb-4"><label>Lernabschnitt</label><input type="text" name="lernabschnitt" class="w-full border p-2" required></div>
        <div class="mb-4"><label>Nummer</label><input type="number" name="nummer" class="w-full border p-2" required></div>
        <div class="mb-4"><label>Frage</label><textarea name="frage" class="w-full border p-2" required></textarea></div>
        <div class="mb-4 flex flex-col gap-2">
            <label class="inline-flex items-center"><span class="w-32">Antwort A</span><input type="text" name="antwort_a" class="w-full border p-2 ml-2" required></label>
            <label class="inline-flex items-center"><span class="w-32">Antwort B</span><input type="text" name="antwort_b" class="w-full border p-2 ml-2" required></label>
            <label class="inline-flex items-center"><span class="w-32">Antwort C</span><input type="text" name="antwort_c" class="w-full border p-2 ml-2" required></label>
        </div>
        <div class="mb-4"><label>Lösung (z.B. A,C)</label><input type="text" name="loesung" class="w-full border p-2" required></div>
        <button type="submit" class="bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900">Speichern</button>
    </form>
</div>
@endsection
