<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - THW Trainer</title>
    @vite(['resources/css/app.css'])
    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        .pulse-animation {
            animation: pulse 2s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 min-h-screen flex items-center justify-center p-6">
    <div class="text-center text-white max-w-md">
        <!-- Icon -->
        <div class="text-8xl mb-6 pulse-animation">📴</div>
        
        <!-- Heading -->
        <h1 class="text-4xl font-bold mb-4">Du bist offline</h1>
        
        <!-- Description -->
        <p class="text-xl mb-6 opacity-90">
            Diese Seite ist nicht im Cache verfügbar. Bitte stelle eine Internetverbindung her, um fortzufahren.
        </p>
        
        <!-- Status Box -->
        <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4 mb-6">
            <div class="flex items-center justify-center gap-3">
                <div class="w-3 h-3 bg-yellow-400 rounded-full animate-pulse"></div>
                <span class="text-sm font-medium" id="connectionStatus">Verbindung wird geprüft...</span>
            </div>
        </div>
        
        <!-- Retry Button -->
        <button onclick="location.reload()" 
                class="bg-white text-blue-900 px-8 py-3 rounded-lg font-bold hover:bg-blue-50 transition-all transform hover:scale-105 active:scale-95 shadow-lg">
            🔄 Erneut versuchen
        </button>
        
        <!-- Tips -->
        <div class="mt-10 bg-white bg-opacity-5 rounded-lg p-6 text-left">
            <h3 class="font-bold mb-3 text-lg">💡 Tipps für Offline-Nutzung:</h3>
            <ul class="space-y-2 text-sm opacity-90">
                <li class="flex items-start gap-2">
                    <span class="mt-1">•</span>
                    <span>Besuche Seiten online, damit sie offline verfügbar werden</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1">•</span>
                    <span>Bereits besuchte Übungen und Prüfungen sind offline nutzbar</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1">•</span>
                    <span>Deine Antworten werden synchronisiert, sobald du wieder online bist</span>
                </li>
            </ul>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-sm opacity-75">
            <a href="/" class="underline hover:opacity-100">Zurück zur Startseite</a>
        </div>
    </div>

    <script>
        // Check connection status
        function updateConnectionStatus() {
            const statusEl = document.getElementById('connectionStatus');
            
            if (navigator.onLine) {
                statusEl.innerHTML = '<span class="text-green-300">✓ Online - Seite wird geladen...</span>';
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                statusEl.innerHTML = '<span class="text-yellow-300">⚠ Offline - Warte auf Verbindung...</span>';
            }
        }
        
        // Initial check
        updateConnectionStatus();
        
        // Listen for connection changes
        window.addEventListener('online', updateConnectionStatus);
        window.addEventListener('offline', updateConnectionStatus);
        
        // Periodic check (every 5 seconds)
        setInterval(updateConnectionStatus, 5000);
    </script>
</body>
</html>

