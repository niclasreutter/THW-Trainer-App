<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PWA Push Debug - THW Trainer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #003d7a;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .info-box {
            background: #f0f9ff;
            border: 2px solid #0ea5e9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .info-box.error {
            background: #fef2f2;
            border-color: #ef4444;
        }
        .info-box.success {
            background: #f0fdf4;
            border-color: #22c55e;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #374151;
        }
        .value {
            color: #6b7280;
            text-align: right;
        }
        .value.true {
            color: #22c55e;
        }
        .value.false {
            color: #ef4444;
        }
        button {
            width: 100%;
            background: #003d7a;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            margin-top: 10px;
            cursor: pointer;
        }
        button:active {
            background: #002855;
        }
        .log {
            background: #1e293b;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-height: 400px;
            overflow-y: auto;
            margin-top: 15px;
        }
        .log-entry {
            margin-bottom: 5px;
            word-wrap: break-word;
        }
        .log-time {
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 PWA & Push Debug</h1>
        
        <div class="info-box" id="status-box">
            <div class="info-row">
                <span class="label">User Agent:</span>
                <span class="value" id="user-agent">-</span>
            </div>
            <div class="info-row">
                <span class="label">iOS erkannt:</span>
                <span class="value" id="is-ios">-</span>
            </div>
            <div class="info-row">
                <span class="label">window.navigator.standalone:</span>
                <span class="value" id="standalone">-</span>
            </div>
            <div class="info-row">
                <span class="label">Display Mode (CSS):</span>
                <span class="value" id="display-mode">-</span>
            </div>
            <div class="info-row">
                <span class="label">Is PWA:</span>
                <span class="value" id="is-pwa">-</span>
            </div>
            <div class="info-row">
                <span class="label">Service Worker:</span>
                <span class="value" id="sw-support">-</span>
            </div>
            <div class="info-row">
                <span class="label">Push API:</span>
                <span class="value" id="push-support">-</span>
            </div>
            <div class="info-row">
                <span class="label">Notification API:</span>
                <span class="value" id="notification-support">-</span>
            </div>
            <div class="info-row">
                <span class="label">Permission Status:</span>
                <span class="value" id="permission-status">-</span>
            </div>
        </div>
        
        <button onclick="requestPushPermission()">🔔 Push-Benachrichtigungen aktivieren</button>
        <button onclick="testPush()">🧪 Test-Benachrichtigung senden</button>
        <button onclick="clearLog()">🗑️ Log leeren</button>
        
        <div class="log" id="log">
            <div class="log-entry"><span class="log-time">Initialisiere...</span></div>
        </div>
    </div>

    <script src="{{ asset('js/push-notifications.js') }}"></script>
    <script>
        const logEl = document.getElementById('log');
        
        function log(message, type = 'info') {
            const time = new Date().toLocaleTimeString('de-DE');
            const entry = document.createElement('div');
            entry.className = 'log-entry';
            entry.innerHTML = `<span class="log-time">[${time}]</span> ${message}`;
            logEl.appendChild(entry);
            logEl.scrollTop = logEl.scrollHeight;
            console.log(message);
        }
        
        function clearLog() {
            logEl.innerHTML = '<div class="log-entry"><span class="log-time">Log geleert</span></div>';
        }
        
        function updateStatus() {
            // User Agent
            const userAgent = navigator.userAgent;
            document.getElementById('user-agent').textContent = userAgent.substring(0, 50) + '...';
            log('User Agent: ' + userAgent);
            
            // iOS Detection
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
            document.getElementById('is-ios').textContent = isIOS ? '✅ Ja' : '❌ Nein';
            document.getElementById('is-ios').className = 'value ' + (isIOS ? 'true' : 'false');
            log('iOS erkannt: ' + isIOS);
            
            // Standalone
            const standalone = window.navigator.standalone;
            document.getElementById('standalone').textContent = standalone === true ? '✅ true' : standalone === false ? '❌ false' : '⚠️ undefined';
            document.getElementById('standalone').className = 'value ' + (standalone === true ? 'true' : 'false');
            log('window.navigator.standalone: ' + standalone);
            
            // Display Mode
            const displayMode = window.matchMedia('(display-mode: standalone)').matches;
            document.getElementById('display-mode').textContent = displayMode ? '✅ standalone' : '❌ browser';
            document.getElementById('display-mode').className = 'value ' + (displayMode ? 'true' : 'false');
            log('Display Mode: ' + (displayMode ? 'standalone' : 'browser'));
            
            // Is PWA
            const isPWAMode = window.pushNotifications.isPWA();
            document.getElementById('is-pwa').textContent = isPWAMode ? '✅ Ja' : '❌ Nein';
            document.getElementById('is-pwa').className = 'value ' + (isPWAMode ? 'true' : 'false');
            log('Is PWA: ' + isPWAMode);
            
            // Service Worker Support
            const swSupport = 'serviceWorker' in navigator;
            document.getElementById('sw-support').textContent = swSupport ? '✅ Unterstützt' : '❌ Nicht unterstützt';
            document.getElementById('sw-support').className = 'value ' + (swSupport ? 'true' : 'false');
            log('Service Worker: ' + (swSupport ? 'Unterstützt' : 'Nicht unterstützt'));
            
            // Push API Support
            const pushSupport = 'PushManager' in window;
            document.getElementById('push-support').textContent = pushSupport ? '✅ Unterstützt' : '❌ Nicht unterstützt';
            document.getElementById('push-support').className = 'value ' + (pushSupport ? 'true' : 'false');
            log('Push API: ' + (pushSupport ? 'Unterstützt' : 'Nicht unterstützt'));
            
            // Notification API Support
            const notificationSupport = 'Notification' in window;
            document.getElementById('notification-support').textContent = notificationSupport ? '✅ Unterstützt' : '❌ Nicht unterstützt';
            document.getElementById('notification-support').className = 'value ' + (notificationSupport ? 'true' : 'false');
            log('Notification API: ' + (notificationSupport ? 'Unterstützt' : 'Nicht unterstützt'));
            
            // Permission Status
            if (notificationSupport) {
                const permission = Notification.permission;
                const permissionText = permission === 'granted' ? '✅ Erlaubt' : 
                                     permission === 'denied' ? '❌ Blockiert' : 
                                     '⚠️ Noch nicht abgefragt';
                document.getElementById('permission-status').textContent = permissionText;
                document.getElementById('permission-status').className = 'value ' + 
                    (permission === 'granted' ? 'true' : permission === 'denied' ? 'false' : '');
                log('Permission Status: ' + permission);
            }
            
            // Status Box Color
            const statusBox = document.getElementById('status-box');
            if (isPWAMode && pushSupport) {
                statusBox.className = 'info-box success';
            } else if (!isPWAMode) {
                statusBox.className = 'info-box error';
                log('⚠️ APP LÄUFT NICHT ALS PWA! Bitte als PWA installieren und aus dem Home-Screen öffnen.');
            } else {
                statusBox.className = 'info-box error';
            }
        }
        
        async function requestPushPermission() {
            log('🔔 Push-Berechtigung wird angefragt...');
            
            if (!window.pushNotifications.isPWA()) {
                log('❌ FEHLER: App läuft nicht als PWA!');
                alert('Die App muss als PWA installiert sein und aus dem Home-Screen geöffnet werden.');
                return;
            }
            
            if (!window.pushNotifications.isPushSupported()) {
                log('❌ FEHLER: Push-Benachrichtigungen werden nicht unterstützt!');
                alert('Dein Browser unterstützt keine Push-Benachrichtigungen.');
                return;
            }
            
            const result = await window.pushNotifications.requestPushPermission();
            
            if (result.success) {
                log('✅ Push-Benachrichtigungen erfolgreich aktiviert!');
                alert('✅ Push-Benachrichtigungen aktiviert!');
            } else {
                log('❌ FEHLER: ' + result.message);
                alert('❌ Fehler: ' + result.message);
            }
            
            updateStatus();
        }
        
        async function testPush() {
            log('🧪 Test-Benachrichtigung wird gesendet...');
            
            const result = await window.pushNotifications.sendTestPushNotification();
            
            if (result.success) {
                log('✅ Test-Benachrichtigung erfolgreich gesendet!');
                alert('✅ Benachrichtigung gesendet! Sollte gleich erscheinen.');
            } else {
                log('❌ FEHLER: ' + result.message);
                alert('❌ Fehler: ' + result.message);
            }
        }
        
        // Initial status update
        updateStatus();
        
        // Check every 2 seconds for changes
        setInterval(updateStatus, 2000);
        
        log('=== Debug-Seite geladen ===');
        log('Hinweis: Auf iOS muss die App als PWA installiert sein (Teilen → Zum Home-Bildschirm)');
        log('Dann die App aus dem Home-Screen öffnen, NICHT über Safari!');
    </script>
</body>
</html>
