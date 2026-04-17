@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Zusatz-Frage <span>anlegen</span></h1>
        <p class="page-subtitle">Task 3: UI folgt{{ $typ ? ' (vorgewählter Typ: ' . $typ . ')' : '' }}</p>
    </header>
</div>
@endsection
