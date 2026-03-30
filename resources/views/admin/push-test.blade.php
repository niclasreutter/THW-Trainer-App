@extends('layouts.app')

@section('title', 'Push Debug')

@section('content')
<div class="dashboard-container" x-data="pushDebug()">
    <header class="dashboard-header">
        <h1 class="page-title">Push <span>Debug</span></h1>
        <p class="page-subtitle">Push-Notification Status und Test</p>
    </header>

    {{-- Status Cards --}}
    <div class="bento-grid" style="grid-template-columns: 1fr;">

        {{-- Browser Status --}}
        <div class="glass bento-main">
            <h3 class="text-lg font-bold mb-4" style="color: var(--text-primary)">Browser Status</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2" style="border-bottom: 1px solid var(--glass-border)">
                    <span style="color: var(--text-secondary)">Service Worker</span>
                    <span x-text="swStatus" :style="swStatus === 'Aktiv' ? 'color: var(--success)' : 'color: var(--error)'"></span>
                </div>
                <div class="flex justify-between items-center py-2" style="border-bottom: 1px solid var(--glass-border)">
                    <span style="color: var(--text-secondary)">Notification Permission</span>
                    <span x-text="permissionStatus" :style="permissionStatus === 'granted' ? 'color: var(--success)' : 'color: var(--error)'"></span>
                </div>
                <div class="flex justify-between items-center py-2" style="border-bottom: 1px solid var(--glass-border)">
                    <span style="color: var(--text-secondary)">Push Subscription</span>
                    <span x-text="subscriptionStatus" :style="subscriptionStatus === 'Aktiv' ? 'color: var(--success)' : 'color: var(--error)'"></span>
                </div>
                <div class="flex justify-between items-center py-2" style="border-bottom: 1px solid var(--glass-border)">
                    <span style="color: var(--text-secondary)">Push Service</span>
                    <span x-text="pushService" style="color: var(--text-primary)"></span>
                </div>
                <div class="flex justify-between items-center py-2" style="border-bottom: 1px solid var(--glass-border)">
                    <span style="color: var(--text-secondary)">Content Encoding</span>
                    <span x-text="contentEncoding" style="color: var(--text-primary)"></span>
                </div>
                <div class="flex justify-between items-center py-2" style="border-bottom: 1px solid var(--glass-border)">
                    <span style="color: var(--text-secondary)">Standalone PWA</span>
                    <span x-text="isPwa ? 'Ja' : 'Nein'" style="color: var(--text-primary)"></span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span style="color: var(--text-secondary)">User Agent</span>
                    <span class="text-xs text-right max-w-[60%] break-all" x-text="userAgent" style="color: var(--text-muted)"></span>
                </div>
            </div>
        </div>

        {{-- Server Subscriptions --}}
        <div class="glass">
            <h3 class="text-lg font-bold mb-4" style="color: var(--text-primary)">Server Subscriptions</h3>
            @if($subscriptions->isEmpty())
                <p style="color: var(--text-muted)">Keine Subscriptions in der Datenbank.</p>
            @else
                <div class="space-y-3">
                    @foreach($subscriptions as $sub)
                        <div class="p-3 rounded-lg" style="background: var(--glass-bg); border: 1px solid var(--glass-border)">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium" style="color: var(--text-primary)">
                                    #{{ $sub->id }}
                                    @if(str_contains($sub->endpoint, 'apple') || str_contains($sub->endpoint, 'push.apple'))
                                        — Apple (iOS)
                                    @elseif(str_contains($sub->endpoint, 'fcm') || str_contains($sub->endpoint, 'google'))
                                        — Google (FCM)
                                    @elseif(str_contains($sub->endpoint, 'mozilla') || str_contains($sub->endpoint, 'push.services.mozilla'))
                                        — Mozilla (Firefox)
                                    @else
                                        — Unbekannt
                                    @endif
                                </span>
                                <span class="text-xs px-2 py-1 rounded-full"
                                    style="{{ $sub->is_active
                                        ? 'background: rgba(16,185,129,0.15); color: var(--success)'
                                        : 'background: rgba(239,68,68,0.15); color: var(--error)' }}">
                                    {{ $sub->is_active ? 'Aktiv' : 'Inaktiv' }}
                                </span>
                            </div>
                            <div class="text-xs break-all" style="color: var(--text-muted)">
                                {{ Str::limit($sub->endpoint, 100) }}
                            </div>
                            <div class="text-xs mt-1" style="color: var(--text-muted)">
                                Encoding: {{ $sub->content_encoding }} | Erstellt: {{ $sub->created_at?->format('d.m.Y H:i') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-3 text-sm" style="color: var(--text-secondary)">
                VAPID Key: {{ $vapidConfigured ? 'Konfiguriert' : 'FEHLT!' }}
            </div>
        </div>

        {{-- Test Push --}}
        <div class="glass">
            <h3 class="text-lg font-bold mb-4" style="color: var(--text-primary)">Test Push senden</h3>
            <button @click="sendTestPush()" class="btn-primary w-full py-3" :disabled="sending">
                <span x-show="!sending">Test-Push senden</span>
                <span x-show="sending">Wird gesendet...</span>
            </button>

            <div x-show="result" class="mt-4 p-3 rounded-lg text-sm" :class="resultSuccess ? '' : ''"
                 :style="resultSuccess
                    ? 'background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: var(--success)'
                    : 'background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: var(--error)'">
                <span x-text="result"></span>
            </div>

            <p class="mt-3 text-xs" style="color: var(--text-muted)">
                Sendet einen Test-Push an alle aktiven Subscriptions. Pruefe danach storage/logs/laravel.log fuer Details (Erfolg/Fehler pro Endpoint).
            </p>
        </div>

        {{-- Log Hint --}}
        <div class="glass">
            <h3 class="text-lg font-bold mb-4" style="color: var(--text-primary)">Server Logs pruefen</h3>
            <p class="text-sm mb-2" style="color: var(--text-secondary)">
                Nach dem Test-Push stehen in den Logs Details pro Subscription:
            </p>
            <div class="p-3 rounded-lg text-xs font-mono" style="background: rgba(0,0,0,0.3); color: var(--text-muted)">
                tail -f storage/logs/laravel.log | grep -i push
            </div>
            <p class="text-sm mt-3" style="color: var(--text-secondary)">
                Dort siehst du pro Endpoint: Erfolg/Fehler, HTTP Status, Reason, und ob Apple oder FCM.
            </p>
        </div>
    </div>
</div>

<script>
function pushDebug() {
    return {
        swStatus: 'Pruefe...',
        permissionStatus: 'Pruefe...',
        subscriptionStatus: 'Pruefe...',
        pushService: '-',
        contentEncoding: '-',
        isPwa: false,
        userAgent: navigator.userAgent,
        sending: false,
        result: null,
        resultSuccess: false,

        async init() {
            this.isPwa = window.matchMedia('(display-mode: standalone)').matches
                || window.navigator.standalone === true;

            if (!('serviceWorker' in navigator)) {
                this.swStatus = 'Nicht unterstuetzt';
                this.subscriptionStatus = 'Nicht moeglich';
                return;
            }

            try {
                const reg = await navigator.serviceWorker.getRegistration('/sw.js');
                if (reg) {
                    this.swStatus = reg.active ? 'Aktiv' : (reg.installing ? 'Installiert...' : 'Wartend');
                } else {
                    this.swStatus = 'Nicht registriert';
                }
            } catch (e) {
                this.swStatus = 'Fehler: ' + e.message;
            }

            this.permissionStatus = typeof Notification !== 'undefined'
                ? Notification.permission
                : 'Nicht unterstuetzt';

            try {
                const reg = await navigator.serviceWorker.ready;
                const sub = await reg.pushManager.getSubscription();
                if (sub) {
                    this.subscriptionStatus = 'Aktiv';
                    const endpoint = sub.endpoint;
                    if (endpoint.includes('apple') || endpoint.includes('push.apple')) {
                        this.pushService = 'Apple (iOS Safari)';
                    } else if (endpoint.includes('fcm') || endpoint.includes('google')) {
                        this.pushService = 'Google FCM (Chrome)';
                    } else if (endpoint.includes('mozilla')) {
                        this.pushService = 'Mozilla (Firefox)';
                    } else {
                        this.pushService = 'Unbekannt';
                    }
                    this.contentEncoding = (PushManager.supportedContentEncodings || ['aes128gcm'])[0];
                } else {
                    this.subscriptionStatus = 'Keine Subscription';
                    this.pushService = '-';
                }
            } catch (e) {
                this.subscriptionStatus = 'Fehler: ' + e.message;
            }
        },

        async sendTestPush() {
            this.sending = true;
            this.result = null;
            try {
                const resp = await fetch('{{ route("admin.push-test.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'Accept': 'application/json',
                    },
                });
                const data = await resp.json();
                this.resultSuccess = data.success;
                this.result = data.message;
                if (data.subscriptions) {
                    const services = data.subscriptions.map(s => s.is_apple ? 'Apple' : 'FCM');
                    this.result += ' (' + services.join(', ') + ')';
                }
            } catch (e) {
                this.resultSuccess = false;
                this.result = 'Request fehlgeschlagen: ' + e.message;
            }
            this.sending = false;
        },
    };
}
</script>
@endsection
