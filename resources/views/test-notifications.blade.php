@extends('layouts.app')

@section('title', 'Notification Tests')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="dashboard-header mb-8">
            <h1 class="dashboard-greeting">🧪 <span>Notification Tests</span></h1>
            <p class="dashboard-subtitle">Teste Gamification Notifications</p>
        </div>

        <!-- Status Messages -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded">
                {{ session('info') }}
            </div>
        @endif

        <!-- Current Stats -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Deine aktuellen Stats</h2>
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-600">{{ auth()->user()->level }}</div>
                    <div class="text-sm text-gray-600">Level</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ number_format(auth()->user()->points) }}</div>
                    <div class="text-sm text-gray-600">Punkte</div>
                </div>
                <div class="text-center">
                    @php
                        $userAchievements = is_array(auth()->user()->achievements) ? auth()->user()->achievements : json_decode(auth()->user()->achievements, true) ?? [];
                    @endphp
                    <div class="text-3xl font-bold text-purple-600">{{ count($userAchievements) }}</div>
                    <div class="text-sm text-gray-600">Achievements</div>
                </div>
            </div>
        </div>

        <!-- Test Buttons -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Test Notifications</h2>
            <div class="space-y-3">
                <a href="{{ route('test.notification', ['type' => 'achievement']) }}" class="block w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg text-center transition-colors">
                    🏆 Zufälliges Achievement freischalten
                </a>
                <a href="{{ route('test.notification', ['type' => 'levelup']) }}" class="block w-full bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-3 px-6 rounded-lg text-center transition-colors">
                    🎉 Level Up Test (500 Punkte)
                </a>
            </div>
            <p class="mt-4 text-sm text-gray-500">
                Diese Buttons lösen echte Gamification-Events aus. Du solltest sehen:
            </p>
            <ul class="mt-2 text-sm text-gray-600 space-y-1 ml-4">
                <li>✅ Popup-Notification (rechts oben)</li>
                <li>✅ Canvas Confetti Animation</li>
                <li>✅ Neue Mitteilung in der Mitteilungszentrale</li>
                <li>✅ Badge-Counter erhöht sich</li>
            </ul>
        </div>

        <!-- Recent Notifications -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Letzte Mitteilungen (DB)</h2>
                <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Alle anzeigen →
                </a>
            </div>
            @php
                $recentNotifications = auth()->user()->notifications()->limit(5)->get();
            @endphp
            @forelse($recentNotifications as $notification)
                <div class="border-b border-gray-100 py-3 last:border-b-0">
                    <div class="flex items-start space-x-3">
                        <span class="text-2xl">{{ $notification->icon ?? '🔔' }}</span>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                            <p class="text-sm text-gray-600">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @if(!$notification->is_read)
                            <span class="px-2 py-1 text-xs font-semibold text-blue-600 bg-blue-100 rounded">Neu</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-8">
                    📭 Noch keine Mitteilungen vorhanden
                </p>
            @endforelse
        </div>

        <!-- Debug Info -->
        <div class="bg-gray-100 rounded-lg p-4 mt-6">
            <details>
                <summary class="cursor-pointer font-semibold text-gray-700">🔍 Debug Info</summary>
                <div class="mt-3 space-y-2 text-sm">
                    <div><strong>User ID:</strong> {{ auth()->user()->id }}</div>
                    <div><strong>Session ID:</strong> {{ session()->getId() }}</div>
                    <div><strong>Notifications in DB:</strong> {{ auth()->user()->notifications()->count() }}</div>
                    <div><strong>Unread:</strong> {{ auth()->user()->unreadNotifications()->count() }}</div>
                    <div><strong>Achievements:</strong> {{ count($userAchievements) }}/{{ count(\App\Services\GamificationService::ACHIEVEMENTS) }}</div>
                </div>
            </details>
        </div>
    </div>
</div>

<style>
.dashboard-greeting span {
    background: linear-gradient(90deg, #fbbf24, #f59e0b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
@endsection
