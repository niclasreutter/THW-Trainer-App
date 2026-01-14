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

        .btn-secondary {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(251, 191, 36, 0.4);
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
    </style>
</head>
<body style="background: #f3f4f6;">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-2xl w-full">
            <div class="error-card" style="padding: 3rem;">
                <!-- Error Code & Icon -->
                <div class="text-center mb-6">
                    <div class="mb-4">
                        <div style="display: inline-block; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(251, 191, 36, 0.15) 0%, rgba(245, 158, 11, 0.1) 100%); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 2.5rem;">
                            🔒
                        </div>
                    </div>
                    <h1 style="font-size: 5rem; font-weight: 800; line-height: 1; margin-bottom: 1rem;">
                        <span class="error-gradient">403</span>
                    </h1>
                    <h2 style="font-size: 1.75rem; font-weight: 700; color: #00337F; margin-bottom: 1rem;">
                        Zugriff verweigert
                    </h2>
                    <p style="color: #6b7280; font-size: 1rem; line-height: 1.6; max-width: 500px; margin: 0 auto;">
                        Du hast keine Berechtigung, auf diese Seite zuzugreifen.
                        @guest
                            Möglicherweise musst du dich anmelden.
                        @else
                            Kontaktiere einen Administrator, falls du Zugriff benötigst.
                        @endguest
                    </p>
                </div>

                <!-- Action Buttons -->
                <div style="display: grid; gap: 1rem; margin-top: 2rem;">
                    @guest
                        <a href="{{ route('login') }}"
                           class="btn-primary"
                           style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 1rem 1.5rem; border-radius: 0.75rem; font-weight: 700; text-decoration: none; color: white;">
                            <span>🔐</span>
                            <span>Anmelden</span>
                        </a>
                    @endguest

                    <a href="{{ route('home') }}"
                       class="btn-secondary"
                       style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 1rem 1.5rem; border-radius: 0.75rem; font-weight: 700; text-decoration: none; color: #1e40af;">
                        <span>🏠</span>
                        <span>Zur Startseite</span>
                    </a>
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

