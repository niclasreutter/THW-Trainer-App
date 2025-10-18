<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Zu viele Anfragen | THW-Trainer</title>
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
                <div class="inline-block bg-yellow-500 text-white rounded-full p-6 shadow-lg">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <h1 class="text-8xl font-bold text-yellow-500 mb-4">429</h1>
            
            <!-- Error Message -->
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Zu viele Anfragen
            </h2>
            
            <p class="text-gray-600 mb-8">
                Du warst etwas zu schnell unterwegs! Bitte warte einen kurzen Moment, 
                bevor du es erneut versuchst.
            </p>

            <!-- Countdown Timer (optional) -->
            <div class="mb-8 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <p class="text-sm text-yellow-800">
                    ⏳ Bitte warte <strong>einen Moment</strong>, dann kannst du fortfahren.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <button onclick="setTimeout(() => window.location.reload(), 3000); this.disabled=true; this.textContent='Lädt in 3 Sekunden...'" 
                        class="block w-full bg-blue-900 hover:bg-blue-800 text-yellow-400 font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    🔄 Automatisch neu laden
                </button>
                
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

