@extends('layouts.app')

@section('title', 'Passwort vergessen - THW Trainer')
@section('description', 'Passwort vergessen? Setze dein THW-Trainer Passwort zurück und erhalte einen sicheren Reset-Link per E-Mail.')

@section('content')
    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-100">
        <div class="w-full max-w-xl mt-10 p-6 bg-white rounded shadow">
            <h2 class="text-2xl font-bold mb-6 text-blue-900 text-center">Passwort zurücksetzen</h2>
            <div class="mb-4 text-sm text-gray-600 text-center">
                Teile uns deine E-Mail-Adresse mit. Sollte ein Account existieren, senden wir dir einen Link zum Zurücksetzen des Passworts!
            </div>
            @if (session('status'))
                <div class="mb-4 p-4 border border-green-400 text-green-700 rounded bg-green-100">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="mb-4 p-4 border border-red-400 text-red-700 rounded" style="background: rgba(255, 0, 0, 0.5);">
                        {{ $error }}
                    </div>
                @endforeach
            @endif
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block mb-2 font-semibold text-yellow-400">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-2 border border-blue-900 rounded text-blue-900 bg-white focus:border-yellow-400 focus:ring-yellow-400 focus:outline-none">
                </div>
                <button type="submit" class="w-full bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900">Passwort zurücksetzen</button>
            </form>
            
            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-blue-900 hover:text-yellow-600 underline text-sm font-medium">
                    ← Zurück zum Login
                </a>
            </div>
        </div>
    </div>
@endsection
