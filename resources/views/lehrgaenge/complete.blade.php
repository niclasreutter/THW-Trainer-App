@extends('layouts.app')

@section('title', $lehrgang->lehrgang . ' - Abgeschlossen')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto text-center">
        <!-- Celebration Animation -->
        <div class="mb-8">
            <div class="text-6xl mb-4">🎉</div>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ __('Herzlichen Glückwunsch!') }}</h1>
            <p class="text-xl text-gray-600">{{ __('Du hast den Lehrgang abgeschlossen!') }}</p>
        </div>

        <!-- Statistik -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Lehrgang Info -->
                <div class="border-l-4 border-blue-600 pl-6">
                    <p class="text-gray-600 text-sm">{{ __('Lehrgang') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $lehrgang->lehrgang }}</p>
                </div>

                <!-- Punkte -->
                <div class="border-l-4 border-green-600 pl-6">
                    <p class="text-gray-600 text-sm">{{ __('Erreichte Punkte') }}</p>
                    <p class="text-2xl font-bold text-green-600">{{ $points }}</p>
                </div>

                <!-- Status -->
                <div class="border-l-4 border-yellow-600 pl-6">
                    <p class="text-gray-600 text-sm">{{ __('Status') }}</p>
                    <p class="text-2xl font-bold text-yellow-600">✓ {{ __('Lehrgang abgeschlossen') }}</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col gap-3 mb-8">
            <a href="{{ route('lehrgaenge.show', $lehrgang->slug) }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                {{ __('Zur Kursseite') }}
            </a>
            <a href="{{ route('lehrgaenge.index') }}" 
               class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition font-semibold">
                {{ __('Weitere Kurse erkunden') }}
            </a>
        </div>

        <!-- Motivational Message -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <p class="text-gray-700">
                🌟 {{ __('Du hast den Lehrgang abgeschlossen! Großartig gemacht!') }}
            </p>
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
