<!DOCTYPE html>
<html lang="de" class="{{ session('theme', 'dark') === 'light' ? 'light-mode' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | THW-Trainer</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
</head>
<body>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-lg w-full">
            @yield('content')

            <!-- Footer -->
            <div class="text-center mt-8">
                <p class="text-sm" style="color: var(--text-muted);">
                    THW-Trainer - Dein digitaler Lernbegleiter
                </p>
            </div>
        </div>
    </div>

    <script>
        // Theme aus localStorage laden (mit Auto-Modus)
        (function() {
            var theme = localStorage.getItem('theme');
            var isLight = theme === 'light' || (!theme && window.matchMedia('(prefers-color-scheme: light)').matches);
            if (isLight) document.documentElement.classList.add('light-mode');
        })();
    </script>
</body>
</html>
