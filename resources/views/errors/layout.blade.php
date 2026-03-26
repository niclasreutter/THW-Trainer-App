<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | THW-Trainer</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        html, body { margin: 0; padding: 0; height: 100dvh; overflow: hidden; }
        .error-page {
            height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .error-logo { height: 4rem; width: auto; margin-bottom: 2rem; }
        .error-code {
            font-size: clamp(4rem, 15vw, 8rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.04em;
            background: linear-gradient(135deg, var(--thw-blue), var(--thw-blue-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .error-title {
            font-size: clamp(1.1rem, 3vw, 1.5rem);
            font-weight: 700;
            color: var(--text-primary);
            margin-top: 0.5rem;
        }
        .error-desc {
            color: var(--text-muted);
            font-size: 0.9rem;
            max-width: 360px;
            text-align: center;
            line-height: 1.5;
            margin-top: 0.75rem;
        }
        .error-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .error-footer {
            position: absolute;
            bottom: 1.5rem;
            font-size: 0.75rem;
            color: var(--text-muted);
            opacity: 0.6;
        }
        .error-logo-light { display: none; }
        .error-logo-dark { display: block; }
        html.light-mode .error-logo-light { display: block; }
        html.light-mode .error-logo-dark { display: none; }
    </style>
</head>
<body>
    <div class="error-page">
        <img src="{{ asset('logo-thwtrainer.png') . '?v=' . filemtime(public_path('logo-thwtrainer.png')) }}" alt="THW-Trainer" class="error-logo error-logo-light">
        <img src="{{ asset('logo-thwtrainer_w.png') . '?v=' . filemtime(public_path('logo-thwtrainer_w.png')) }}" alt="THW-Trainer" class="error-logo error-logo-dark">

        @yield('content')

        <div class="error-footer">THW-Trainer</div>
    </div>

    <script>
        (function() {
            var theme = localStorage.getItem('theme');
            var isLight = theme === 'light' || (!theme && window.matchMedia('(prefers-color-scheme: light)').matches);
            if (isLight) document.documentElement.classList.add('light-mode');
        })();
    </script>
</body>
</html>
