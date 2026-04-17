@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Zusatz-<span>Fragen</span></h1>
        <p class="page-subtitle">Task 3: UI folgt</p>
    </header>

    <p>Anzahl Fragen: {{ $questions->count() }}</p>
    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif
</div>
@endsection
