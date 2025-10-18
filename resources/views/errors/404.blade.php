<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Seite nicht gefunden | THW-Trainer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full text-center">
            <!-- Logo -->
            <div class="mb-8">
                <div class="inline-block bg-blue-900 text-yellow-400 rounded-full p-6 shadow-lg">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <h1 class="text-8xl font-bold text-blue-900 mb-4">404</h1>
            
            <!-- Error Message -->
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Seite nicht gefunden
            </h2>
            
            <p class="text-gray-600 mb-8">
                Die von dir gesuchte Seite existiert leider nicht. 
                Vielleicht wurde sie verschoben oder gelöscht.
            </p>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="{{ route('home') }}" 
                   class="block w-full bg-blue-900 hover:bg-blue-800 text-yellow-400 font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                    🏠 Zur Startseite
                </a>
                
                @auth
                    <a href="{{ route('dashboard') }}" 
                       class="block w-full bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md">
                        📊 Zum Dashboard
                    </a>
                @else
                    <a href="{{ route('guest.practice.menu') }}" 
                       class="block w-full bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md">
                        🎯 Anonym üben
                    </a>
                @endauth
            </div>

            <!-- Footer Info -->
            <div class="mt-8 text-sm text-gray-500">
                <p>THW-Trainer - Dein digitaler Lernbegleiter</p>
            </div>
        </div>
    </div>
</body>
</html>

