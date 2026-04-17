@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Zusatz-Frage <span>bearbeiten</span></h1>
        <p class="page-subtitle">Task 3: UI folgt (ID: {{ $question->id }}, Typ: {{ $question->typ }})</p>
    </header>
</div>
@endsection
