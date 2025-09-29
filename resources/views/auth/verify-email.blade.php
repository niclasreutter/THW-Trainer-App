
@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded shadow">
            <div class="text-gray-900">
                <div class="flex items-center mb-6">
                    <img src="{{ asset('logo-thwtrainer.png') }}" alt="THW-Trainer Logo" class="max-h-6 w-full mx-auto" style="max-width:50%;height:auto;display:block;" />
                </div>
                <h2 class="text-xl font-bold text-blue-900 mb-4 text-center">E-Mail Bestätigung</h2>
                <p class="mb-4 text-lg text-center">Vielen Dank für deine Registrierung!<br>Bitte bestätige deine E-Mail-Adresse, indem du auf den Link in der E-Mail klickst, die wir dir gerade gesendet haben.</p>
                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 font-medium text-sm text-green-600 text-center">
                        Ein neuer Bestätigungslink wurde an deine E-Mail-Adresse gesendet.
                    </div>
                @endif
                <div class="flex flex-col gap-4 mt-4 w-full">
                    <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                        @csrf
                        <button type="submit" class="bg-yellow-400 text-blue-900 font-bold px-6 py-2 rounded hover:bg-white hover:text-blue-900 w-full">E-Mail erneut senden</button>
                    </form>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="bg-blue-900 text-yellow-400 font-bold px-6 py-2 rounded hover:bg-yellow-400 hover:text-blue-900 w-full">Abmelden</button>
                    </form>
                </div>
            </div>
        </div>
        <footer class="mt-8 text-sm text-gray-400 text-center">Nicht offiziell vom THW. Nur zu Trainingszwecken.</footer>
    </div>
@endsection
