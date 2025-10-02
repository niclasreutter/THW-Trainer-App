

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
                
                <!-- E-Mail-Zustimmung -->
                <div class="mb-6 p-4 rounded-lg" style="background-color: #f0f9ff; border: 2px solid #0ea5e9; box-shadow: 0 0 20px rgba(14, 165, 233, 0.3), 0 0 40px rgba(14, 165, 233, 0.1);">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 mt-1" style="color: #0284c7;" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium" style="color: #0284c7; margin-bottom: 6px;">📧 E-Mail-Benachrichtigungen</h3>
                            <p class="text-xs" style="color: #0369a1; margin-bottom: 8px;">
                                Erhalte E-Mails zu deinem Lernfortschritt, neuen Features und wichtigen Systeminformationen.
                            </p>
                            <div class="flex items-center">
                                <input type="checkbox" name="email_consent" id="email_consent" value="1" 
                                       {{ old('email_consent') ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="email_consent" class="ml-2 text-xs font-medium" style="color: #0369a1;">
                                    Ich möchte E-Mail-Benachrichtigungen erhalten
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900">Registrieren</button>
            </form>
        </div>
    </div>
@endsection
