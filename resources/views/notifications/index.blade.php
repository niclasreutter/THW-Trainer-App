@extends('layouts.app')

@section('title', 'Mitteilungen')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Deine <span>Mitteilungen</span></h1>
        <p class="page-subtitle">Level-Ups, Achievements und Erfolge</p>
    </header>

    @php
        $unreadCount = Auth::user()->unreadNotifications()->count();
        $readCount = $notifications->where('is_read', true)->count();
    @endphp

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon"><i class="bi bi-bell"></i></span>
            <div>
                <div class="stat-pill-value">{{ $notifications->total() }}</div>
                <div class="stat-pill-label">Gesamt</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon"><i class="bi bi-bell-fill"></i></span>
            <div>
                <div class="stat-pill-value">{{ $unreadCount }}</div>
                <div class="stat-pill-label">Ungelesen</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon"><i class="bi bi-check2-all"></i></span>
            <div>
                <div class="stat-pill-value">{{ $readCount }}</div>
                <div class="stat-pill-label">Gelesen</div>
            </div>
        </div>
    </div>

    <div class="bento-grid">
        <!-- Actions Card -->
        <div class="glass bento-wide p-4">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="text-sm" style="color: var(--text-secondary);">
                    <span class="font-semibold" style="color: var(--text-primary);">{{ $notifications->total() }}</span> Mitteilungen
                    @if($unreadCount > 0)
                        <span class="mx-2" style="color: var(--text-muted);">|</span>
                        <span class="font-semibold text-gradient-gold">{{ $unreadCount }}</span> ungelesen
                    @endif
                </div>
                <div class="flex gap-3">
                    @if($unreadCount > 0)
                        <form action="{{ route('notifications.read-all') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn-ghost btn-sm">
                                Alle als gelesen markieren
                            </button>
                        </form>
                    @endif
                    @if($readCount > 0)
                        <form action="{{ route('notifications.clear-read') }}" method="POST" class="inline" onsubmit="return confirm('Möchtest du wirklich alle gelesenen Mitteilungen löschen?')">
                            @csrf
                            <button type="submit" class="btn-danger btn-sm">
                                Gelesene löschen
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bento-wide space-y-4">
            @forelse($notifications as $notification)
                <div class="{{ $notification->is_read ? 'glass' : 'glass-gold' }} p-5 transition-all duration-200 hover:scale-[1.01]">
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center {{ $notification->is_read ? 'glass-subtle' : 'glass' }}" style="font-size: 1.5rem;">
                            @if($notification->icon)
                                {{ $notification->icon }}
                            @else
                                <i class="bi bi-bell" style="color: var(--gold);"></i>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <h3 class="font-bold" style="color: var(--text-primary);">
                                        {{ $notification->title }}
                                        @if(!$notification->is_read)
                                            <span class="inline-block w-2 h-2 rounded-full ml-2" style="background: var(--gold);"></span>
                                        @endif
                                    </h3>
                                    <p class="mt-1 text-sm" style="color: var(--text-secondary);">
                                        {{ $notification->message }}
                                    </p>
                                    @if($notification->data && isset($notification->data['description']))
                                        <p class="mt-1 text-xs" style="color: var(--text-muted);">
                                            {{ $notification->data['description'] }}
                                        </p>
                                    @endif
                                    <p class="mt-2 text-xs" style="color: var(--text-muted);">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex-shrink-0 flex gap-2">
                                    @if(!$notification->is_read)
                                        <button onclick="markAsRead({{ $notification->id }})"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110"
                                                style="background: var(--glass-white-5); color: var(--text-secondary);"
                                                title="Als gelesen markieren">
                                            <i class="bi bi-check2"></i>
                                        </button>
                                    @endif
                                    <button onclick="deleteNotification({{ $notification->id }})"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110"
                                            style="background: var(--glass-white-5); color: var(--text-secondary);"
                                            title="Löschen">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="glass p-12 text-center">
                    <div class="text-6xl mb-4" style="color: var(--text-muted);">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2" style="color: var(--text-primary);">Keine Mitteilungen</h3>
                    <p style="color: var(--text-secondary);">Du hast noch keine Mitteilungen erhalten.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="bento-wide">
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
@endsection
