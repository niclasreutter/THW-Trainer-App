<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Pages Test | THW-Trainer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12 px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-blue-900 mb-2">🧪 Error Pages Testen</h1>
                <p class="text-gray-600">Klicke auf einen Fehlercode, um die entsprechende Error Page zu sehen</p>
                <p class="text-sm text-red-500 mt-2">⚠️ Diese Seite ist nur im Debug-Modus verfügbar</p>
            </div>

            <!-- Error Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- 404 -->
                <a href="/test-errors/404" 
                   class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 border-l-4 border-blue-500">
                    <div class="text-5xl font-bold text-blue-500 mb-2">404</div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Seite nicht gefunden</h3>
                    <p class="text-sm text-gray-600">Wenn eine Seite nicht existiert</p>
                </a>

                <!-- 403 -->
                <a href="/test-errors/403" 
                   class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 border-l-4 border-red-500">
                    <div class="text-5xl font-bold text-red-500 mb-2">403</div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Zugriff verweigert</h3>
                    <p class="text-sm text-gray-600">Keine Berechtigung für diese Seite</p>
                </a>

                <!-- 500 -->
                <a href="/test-errors/500" 
                   class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 border-l-4 border-orange-500">
                    <div class="text-5xl font-bold text-orange-500 mb-2">500</div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Interner Serverfehler</h3>
                    <p class="text-sm text-gray-600">Unerwarteter Fehler auf dem Server</p>
                </a>

                <!-- 503 -->
                <a href="/test-errors/503" 
                   class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 border-l-4 border-blue-600">
                    <div class="text-5xl font-bold text-blue-600 mb-2">503</div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Wartungsmodus</h3>
                    <p class="text-sm text-gray-600">Service vorübergehend nicht verfügbar</p>
                </a>

                <!-- 419 -->
                <a href="/test-errors/419" 
                   class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 border-l-4 border-purple-500">
                    <div class="text-5xl font-bold text-purple-500 mb-2">419</div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Sitzung abgelaufen</h3>
                    <p class="text-sm text-gray-600">CSRF Token expired</p>
                </a>

                <!-- 429 -->
                <a href="/test-errors/429" 
                   class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 border-l-4 border-yellow-500">
                    <div class="text-5xl font-bold text-yellow-500 mb-2">429</div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Zu viele Anfragen</h3>
                    <p class="text-sm text-gray-600">Rate Limiting aktiv</p>
                </a>
            </div>

            <!-- Back to Home -->
            <div class="mt-12 text-center">
                <a href="{{ route('landing.home') }}" 
                   class="inline-block bg-blue-900 hover:bg-blue-800 text-yellow-400 font-semibold py-3 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                    ← Zurück zur Startseite
                </a>
            </div>

            <!-- Info Box -->
            <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg p-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-blue-800 mb-2">ℹ️ Über diese Test-Seite</h3>
                        <p class="text-sm text-blue-700">
                            Diese Seite ist nur verfügbar, wenn <code class="bg-blue-100 px-2 py-1 rounded">APP_DEBUG=true</code> 
                            in deiner .env Datei steht. In der Produktionsumgebung ist sie nicht erreichbar.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

