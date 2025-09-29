
@extends('layouts.app')

@section('title', 'Login - THW Trainer')
@section('description', 'Melde dich bei THW-Trainer an und greife auf deinen persönlichen Lernfortschritt zu. Übe THW-Theoriefragen mit gespeichertem Fortschritt.')

@section('content')
    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-100">
        <div class="w-full max-w-xl mt-10 p-6 bg-white rounded shadow">
            <h2 class="text-2xl font-bold mb-6 text-blue-900 text-center">Login</h2>
            <x-auth-session-status class="mb-4" :status="session('status')" />
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="mb-4 p-4 border border-red-400 text-red-700 rounded" style="background: rgba(255, 0, 0, 0.5);">
                        {{ $error }}
                    </div>
                @endforeach
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block mb-2 font-semibold text-yellow-400">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-2 border border-blue-900 rounded text-blue-900 bg-white focus:border-yellow-400 focus:ring-yellow-400 focus:outline-none">
                </div>
                <div class="mb-4">
                    <label for="password" class="block mb-2 font-semibold text-yellow-400">Password</label>
                    <input id="password" type="password" name="password" required class="w-full px-4 py-2 border border-blue-900 rounded text-blue-900 bg-white focus:border-yellow-400 focus:ring-yellow-400 focus:outline-none">
                </div>
                <div class="mb-6 flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="mr-2 accent-yellow-400 border-blue-900 rounded focus:ring-yellow-400 focus:border-yellow-400">
                    <label for="remember_me" class="text-yellow-400 font-semibold cursor-pointer">Angemeldet bleiben</label>
                </div>
                <button type="submit" class="w-full bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900">Login</button>
            </form>
            
            <div class="mt-4 text-center">
                <a href="{{ route('password.request') }}" class="text-blue-900 hover:text-yellow-600 underline text-sm font-medium">
                    🔑 Passwort vergessen?
                </a>
            </div>
        </div>
    </div>
@endsection
