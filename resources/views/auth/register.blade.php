

@extends('layouts.app')

@section('title', 'Registrierung - THW Trainer')
@section('description', 'Erstelle deinen kostenlosen THW-Trainer Account und starte sofort mit dem Lernen. Verfolge deinen Fortschritt und bereite dich optimal auf deine THW-Prüfung vor.')

@section('content')
    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-100">
        <div class="w-full max-w-xl mt-10 p-6 bg-white rounded shadow">
            <h2 class="text-2xl font-bold mb-6 text-blue-900 text-center">Registrieren</h2>
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="mb-4 p-4 border border-red-400 text-red-700 rounded" style="background: rgba(255, 0, 0, 0.5);">
                        {{ $error }}
                    </div>
                @endforeach
            @endif
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block mb-2 font-semibold text-yellow-400">Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full px-4 py-2 border border-blue-900 rounded text-blue-900 bg-white focus:border-yellow-400 focus:ring-yellow-400 focus:outline-none">
                </div>
                <div class="mb-4">
                    <label for="email" class="block mb-2 font-semibold text-yellow-400">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border border-blue-900 rounded text-blue-900 bg-white focus:border-yellow-400 focus:ring-yellow-400 focus:outline-none">
                </div>
                <div class="mb-4">
                    <label for="password" class="block mb-2 font-semibold text-yellow-400">Password</label>
                    <input id="password" type="password" name="password" required class="w-full px-4 py-2 border border-blue-900 rounded text-blue-900 bg-white focus:border-yellow-400 focus:ring-yellow-400 focus:outline-none">
                </div>
                <div class="mb-4">
                    <label for="password_confirmation" class="block mb-2 font-semibold text-yellow-400">Password bestätigen</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full px-4 py-2 border border-blue-900 rounded text-blue-900 bg-white focus:border-yellow-400 focus:ring-yellow-400 focus:outline-none">
                </div>
                <button type="submit" class="w-full bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900">Registrieren</button>
            </form>
        </div>
    </div>
@endsection
