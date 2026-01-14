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

        .error-gradient {
            background: linear-gradient(90deg, #fbbf24, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-primary {
            background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 51, 127, 0.3);
        }

        .error-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .error-card:hover {
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.2);
        }

        .info-box {
            background: rgba(251, 191, 36, 0.1);
            border: 1px solid rgba(251, 191, 36, 0.3);
            border-radius: 0.75rem;
            padding: 1rem;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(0.95); }
        }

        .pulse-icon {
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes bounce-delayed {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            animation: bounce-delayed 1.4s ease-in-out infinite;
        }

        .dot:nth-child(1) {
            animation-delay: 0s;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
        }

        .dot:nth-child(2) {
            animation-delay: 0.2s;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
        }

        .dot:nth-child(3) {
            animation-delay: 0.4s;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
        }
    </style>
</head>
<body style="background: #f3f4f6;">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-2xl w-full">
            <div class="error-card" style="padding: 3rem;">
                <!-- Error Code & Icon -->
                <div class="text-center mb-6">
                    <div class="mb-4">
                        <div class="pulse-icon" style="display: inline-block; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(251, 191, 36, 0.15) 0%, rgba(245, 158, 11, 0.1) 100%); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 2.5rem;">
                            🔧
                        </div>
                    </div>
                    <h1 style="font-size: 5rem; font-weight: 800; line-height: 1; margin-bottom: 1rem;">
                        <span class="error-gradient">503</span>
                    </h1>
                    <h2 style="font-size: 1.75rem; font-weight: 700; color: #00337F; margin-bottom: 1rem;">
                        Wartungsmodus
                    </h2>
                    <p style="color: #6b7280; font-size: 1rem; line-height: 1.6; max-width: 500px; margin: 0 auto;">
                        Wir führen gerade Wartungsarbeiten durch, um den THW-Trainer
                        für dich noch besser zu machen. Bitte habe einen Moment Geduld.
                    </p>
                </div>

                <!-- Loading Indicator -->
                <div style="display: flex; justify-content: center; gap: 0.75rem; margin-bottom: 2rem;">
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>

                <!-- Info Box -->
                <div class="info-box" style="margin-bottom: 2rem;">
                    <p style="color: #92400e; font-size: 0.875rem; margin: 0;">
                        <strong>⏱️ Info:</strong> Die Wartungsarbeiten dauern in der Regel
                        nur wenige Minuten. Danke für deine Geduld!
                    </p>
                </div>

                <!-- Action Button -->
                <div style="display: grid; gap: 1rem;">
                    <button onclick="window.location.reload()"
                            class="btn-primary"
                            style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 1rem 1.5rem; border-radius: 0.75rem; font-weight: 700; color: white; border: none; cursor: pointer;">
                        <span>🔄</span>
                        <span>Erneut versuchen</span>
                    </button>
                </div>

                <!-- Footer -->
                <div class="text-center mt-8" style="padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                    <p style="color: #9ca3af; font-size: 0.875rem;">
                        THW-Trainer - Dein digitaler Lernbegleiter
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

