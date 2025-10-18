<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Serverfehler | THW-Trainer</title>
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
                <div class="inline-block bg-orange-500 text-white rounded-full p-6 shadow-lg">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <h1 class="text-8xl font-bold text-orange-500 mb-4">500</h1>
            
            <!-- Error Message -->
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Interner Serverfehler
            </h2>
            
            <p class="text-gray-600 mb-8">
                Ups! Etwas ist auf unserer Seite schiefgelaufen. 
                Wir wurden automatisch benachrichtigt und kümmern uns darum.
            </p>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <button onclick="window.location.reload()" 
                        class="block w-full bg-blue-900 hover:bg-blue-800 text-yellow-400 font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                    🔄 Seite neu laden
                </button>
                
                <a href="{{ route('home') }}" 
                   class="block w-full bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md">
                    🏠 Zur Startseite
                </a>
            </div>

            <!-- Additional Help -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-800">
                    <strong>💡 Tipp:</strong> Versuche die Seite neu zu laden. 
                    Sollte das Problem bestehen bleiben, kontaktiere uns bitte.
                </p>
            </div>

            <!-- Footer Info -->
            <div class="mt-8 text-sm text-gray-500">
                <p>THW-Trainer - Dein digitaler Lernbegleiter</p>
            </div>
        </div>
    </div>
</body>
</html>

