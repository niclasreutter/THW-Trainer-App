@extends('layouts.app')

@section('title', 'Mitteilungen')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="dashboard-header mb-8">
            <h1 class="dashboard-greeting">🔔 <span>Mitteilungen</span></h1>
            <p class="dashboard-subtitle">Deine Level-Ups, Achievements und Erfolge</p>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <span class="font-medium text-gray-900">{{ $notifications->total() }}</span> Mitteilungen insgesamt
                    @php
                        $unreadCount = Auth::user()->unreadNotifications()->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="ml-2">•</span>
                        <span class="ml-2"><span class="font-medium text-blue-600">{{ $unreadCount }}</span> ungelesen</span>
                    @endif
                </div>
                <div class="flex gap-2">
                    @if($unreadCount > 0)
                        <form action="{{ route('notifications.read-all') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                Alle als gelesen markieren
                            </button>
                        </form>
                    @endif
                    @if($notifications->where('is_read', true)->count() > 0)
                        <span class="text-gray-300">|</span>
                        <form action="{{ route('notifications.clear-read') }}" method="POST" class="inline" onsubmit="return confirm('Möchtest du wirklich alle gelesenen Mitteilungen löschen?')">
                            @csrf
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                                Gelesene löschen
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            @forelse($notifications as $notification)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden {{ $notification->is_read ? '' : 'border-l-4 border-blue-500' }}">
                    <div class="p-6">
                        <div class="flex items-start">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <span class="text-4xl">{{ $notification->icon ?? '🔔' }}</span>
                            </div>

                            <!-- Content -->
                            <div class="ml-4 flex-1">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $notification->title }}
                                        </h3>
                                        <p class="mt-1 text-base text-gray-700">
                                            {{ $notification->message }}
                                        </p>
                                        @if($notification->data && isset($notification->data['description']))
                                            <p class="mt-1 text-sm text-gray-600">
                                                {{ $notification->data['description'] }}
                                            </p>
                                        @endif
                                        <p class="mt-2 text-sm text-gray-500">
                                            {{ $notification->created_at->diffForHumans() }}
                                            @if(!$notification->is_read)
                                                <span class="ml-2 text-blue-600 font-medium">• Neu</span>
                                            @endif
                                        </p>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="ml-4 flex-shrink-0 flex gap-2">
                                        @if(!$notification->is_read)
                                            <button onclick="markAsRead({{ $notification->id }})" class="text-gray-400 hover:text-blue-600 transition-colors" title="Als gelesen markieren">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        <button onclick="deleteNotification({{ $notification->id }})" class="text-gray-400 hover:text-red-600 transition-colors" title="Löschen">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <span class="text-6xl">📭</span>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Keine Mitteilungen</h3>
                    <p class="mt-2 text-gray-600">Du hast noch keine Mitteilungen erhalten.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    function markAsRead(notificationId) {
        fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function deleteNotification(notificationId) {
        if (!confirm('Möchtest du diese Mitteilung wirklich löschen?')) {
            return;
        }

        fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>

<style>
.dashboard-greeting span {
    background: linear-gradient(90deg, #fbbf24, #f59e0b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
@endsection
