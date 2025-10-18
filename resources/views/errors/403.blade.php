<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Zugriff verweigert | THW-Trainer</title>
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
                <div class="inline-block bg-red-600 text-white rounded-full p-6 shadow-lg">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <h1 class="text-8xl font-bold text-red-600 mb-4">403</h1>
            
            <!-- Error Message -->
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Zugriff verweigert
            </h2>
            
            <p class="text-gray-600 mb-8">
                Du hast keine Berechtigung, auf diese Seite zuzugreifen. 
                @guest
                    Möglicherweise musst du dich anmelden.
                @else
                    Kontaktiere einen Administrator, falls du Zugriff benötigst.
                @endguest
            </p>

            <!-- Action Buttons -->
            <div class="space-y-3">
                @guest
                    <a href="{{ route('login') }}" 
                       class="block w-full bg-blue-900 hover:bg-blue-800 text-yellow-400 font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                        🔐 Anmelden
                    </a>
                @endguest
                
                <a href="{{ route('home') }}" 
                   class="block w-full bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md">
                    🏠 Zur Startseite
                </a>
            </div>

            <!-- Footer Info -->
            <div class="mt-8 text-sm text-gray-500">
                <p>THW-Trainer - Dein digitaler Lernbegleiter</p>
            </div>
        </div>
    </div>
</body>
</html>

