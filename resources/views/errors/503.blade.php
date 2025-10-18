<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Wartungsmodus | THW-Trainer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full text-center">
            <!-- Logo -->
            <div class="mb-8">
                <div class="inline-block bg-blue-600 text-yellow-400 rounded-full p-6 shadow-lg pulse-glow">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <h1 class="text-8xl font-bold text-blue-600 mb-4">503</h1>
            
            <!-- Error Message -->
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Wartungsmodus
            </h2>
            
            <p class="text-gray-600 mb-8">
                Wir führen gerade Wartungsarbeiten durch, um den THW-Trainer 
                für dich noch besser zu machen. Bitte habe einen Moment Geduld.
            </p>

            <!-- Progress Indicator -->
            <div class="mb-8">
                <div class="flex justify-center space-x-2">
                    <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
                    <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                    <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.4s;"></div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="space-y-3">
                <button onclick="window.location.reload()" 
                        class="block w-full bg-blue-900 hover:bg-blue-800 text-yellow-400 font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                    🔄 Erneut versuchen
                </button>
            </div>

            <!-- Additional Info -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-800">
                    <strong>⏱️ Info:</strong> Die Wartungsarbeiten dauern in der Regel 
                    nur wenige Minuten. Danke für deine Geduld!
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

