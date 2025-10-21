<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title') - THW-Trainer@else THW-Trainer - Dein digitaler Begleiter für THW Theorie @endif</title>
        
        <!-- SEO Meta Tags -->
        <meta name="description" content="@hasSection('description')@yield('description')@else THW-Trainer: Bereite dich optimal auf deine THW-Prüfung vor. Kostenlose Theoriefragen, Prüfungssimulation und Lernfortschritt. Jetzt anonym oder mit Account üben! @endif">
        <meta name="keywords" content="THW, Technisches Hilfswerk, Theorie, Prüfung, Übung, Lernfortschritt, kostenlos, Simulation">
        <meta name="author" content="Niclas Reutter">
        @if(app()->environment('testing') || str_contains(request()->getHost(), 'test.') || config('app.environment_type') === 'testing')
            <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
            <meta name="googlebot" content="noindex, nofollow">
        @else
            <meta name="robots" content="index, follow">
        @endif
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="@hasSection('title')@yield('title') - THW-Trainer @else THW-Trainer - Dein digitaler Begleiter für THW-Theorie @endif">
        <meta property="og:description" content="@hasSection('description')@yield('description')@else THW-Trainer: Bereite dich optimal auf deine THW-Prüfung vor. Kostenlose Theoriefragen, Prüfungssimulation und Lernfortschritt. @endif">
        <meta property="og:image" content="{{ asset('logo-thwtrainer.png') }}">
        <meta property="og:locale" content="de_DE">
        
        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:title" content="@hasSection('title')@yield('title') - THW-Trainer @else THW-Trainer - Dein digitaler Begleiter für THW-Theorie @endif">
        <meta property="twitter:description" content="@hasSection('description')@yield('description')@else THW-Trainer: Bereite dich optimal auf deine THW-Prüfung vor. Kostenlose Theoriefragen, Prüfungssimulation und Lernfortschritt. @endif">
        <meta property="twitter:image" content="{{ asset('logo-thwtrainer.png') }}">
        
        <!-- Favicons -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        
        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Performance Meta Tags -->
        <meta name="theme-color" content="#00337F">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="format-detection" content="telephone=no">
        <meta name="msapplication-TileColor" content="#00337F">
        <meta name="application-name" content="THW-Trainer">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- PWA Manifest -->
        <link rel="manifest" href="{{ asset('manifest.json') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#FDFDFC]">
    <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

                        <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
        
        <!-- Gamification Notifications -->
        @include('components.gamification-notifications')
        
        <!-- Achievement Popup -->
        @include('components.achievement-popup')
        
        <!-- Cookie Banner -->
        @include('components.cookie-banner')
        
        <footer class="bg-white border-t border-gray-200 py-6 mt-8">
            <div class="max-w-7xl mx-auto px-4">
                <!-- Unterstützung -->
                <div class="text-center mb-4">
                    <p class="text-gray-600 text-sm mb-2">
                        Diese Webseite wird kostenlos zur Verfügung gestellt
                    </p>
                    <a href="https://paypal.me/reuttern" 
                       target="_blank" 
                       rel="noopener"
                       style="display: inline-flex; align-items: center; gap: 0.5rem; background-color: #00337F; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; text-decoration: none; transition: background-color 0.2s;"
                       onmouseover="this.style.backgroundColor='#002a66'"
                       onmouseout="this.style.backgroundColor='#00337F'">
                        <span>☕</span>
                        <span>Unterstütze mich</span>
                    </a>
                </div>
                
                <!-- Links -->
                <div class="text-center text-gray-500 text-sm">
                    &copy; {{ date('Y') }} THW-Trainer &ndash; 
                    <a href="{{ route('impressum') }}" class="text-blue-900 hover:underline">Impressum</a> &middot; 
                    <a href="{{ route('datenschutz') }}" class="text-blue-900 hover:underline">Datenschutz</a>
                </div>
                
                <!-- Creator -->
                <div class="text-center text-gray-400 text-xs mt-2">
                    Mit 💙 erstellt von <a href="https://niclas-reutter.de" target="_blank" rel="noopener" class="text-blue-600 hover:underline">niclas-reutter.de</a>
                </div>
            </div>
        </footer>

        <!-- PWA Install Banner (nur Mobile) -->
        <div id="pwaInstallBanner" class="fixed bottom-0 left-0 right-0 bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-2xl transform translate-y-full transition-transform duration-300 z-50 md:hidden">
            <div class="p-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 flex-1">
                    <img src="{{ asset('logo-thwtrainer_w.png') }}" alt="THW Trainer" class="w-12 h-12 rounded-lg shadow-lg">
                    <div class="flex-1">
                        <h3 class="font-bold text-sm">THW Trainer App</h3>
                        <p class="text-xs opacity-90">Als App installieren für schnelleren Zugriff</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button id="pwaInstallBtn" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-bold text-sm hover:bg-blue-50 transition-colors whitespace-nowrap">
                        Installieren
                    </button>
                    <button id="pwaCloseBanner" class="text-white hover:text-blue-200 px-2 text-lg">
                        ✕
                    </button>
                </div>
            </div>
        </div>

        <!-- Offline Database Manager -->
        <script src="{{ asset('js/offline-db.js') }}"></script>
        
        <!-- Offline Submit Handler -->
        <script src="{{ asset('js/offline-submit.js') }}"></script>

        <!-- Service Worker Registration & PWA Install Logic -->
        <script>
            // Service Worker Registration
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(registration => {
                            console.log('✅ ServiceWorker registered:', registration.scope);
                            
                            // Check for updates periodically
                            setInterval(() => {
                                registration.update();
                            }, 1000 * 60 * 60); // Check every hour
                        })
                        .catch(error => {
                            console.log('❌ ServiceWorker registration failed:', error);
                        });
                });
            }

            // PWA Install Banner Logic
            let deferredPrompt;
            const installBanner = document.getElementById('pwaInstallBanner');
            const installBtn = document.getElementById('pwaInstallBtn');
            const closeBanner = document.getElementById('pwaCloseBanner');

            // Check if already installed or dismissed
            const pwaInstalled = localStorage.getItem('pwa_installed') === 'true';
            const pwaDismissed = localStorage.getItem('pwa_banner_dismissed') === 'true';
            const dismissedTime = parseInt(localStorage.getItem('pwa_banner_dismissed_time') || '0');
            const daysSinceDismissal = (Date.now() - dismissedTime) / (1000 * 60 * 60 * 24);

            // Listen for the beforeinstallprompt event
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                
                // Show banner if not installed and not recently dismissed (7 days)
                if (!pwaInstalled && (!pwaDismissed || daysSinceDismissal > 7)) {
                    setTimeout(() => {
                        if (installBanner) {
                            installBanner.classList.remove('translate-y-full');
                        }
                    }, 2000); // Show after 2 seconds
                }
            });

            // Install button click
            if (installBtn) {
                installBtn.addEventListener('click', async () => {
                    if (!deferredPrompt) return;
                    
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    
                    if (outcome === 'accepted') {
                        console.log('✅ PWA installed');
                        localStorage.setItem('pwa_installed', 'true');
                        if (installBanner) {
                            installBanner.classList.add('translate-y-full');
                        }
                    } else {
                        console.log('❌ PWA installation dismissed');
                    }
                    
                    deferredPrompt = null;
                });
            }

            // Close banner button
            if (closeBanner) {
                closeBanner.addEventListener('click', () => {
                    if (installBanner) {
                        installBanner.classList.add('translate-y-full');
                    }
                    localStorage.setItem('pwa_banner_dismissed', 'true');
                    localStorage.setItem('pwa_banner_dismissed_time', Date.now().toString());
                });
            }

            // Check if app is already installed
            window.addEventListener('appinstalled', () => {
                console.log('✅ PWA installed via event');
                localStorage.setItem('pwa_installed', 'true');
                if (installBanner) {
                    installBanner.classList.add('translate-y-full');
                }
            });

            // Hide banner when running as PWA
            if (window.matchMedia('(display-mode: standalone)').matches || 
                window.navigator.standalone === true) {
                localStorage.setItem('pwa_installed', 'true');
                if (installBanner) {
                    installBanner.remove();
                }
            }

            // Initialize Offline Database & Auto-Sync
            (async function initOfflineQuestions() {
                if (!('indexedDB' in window)) {
                    console.log('❌ IndexedDB not supported');
                    return;
                }

                try {
                    // Wait for OfflineDB to be ready
                    await window.offlineDB.init();
                    
                    // Check if we need to sync
                    const metadata = await window.offlineDB.getMetadata();
                    const now = Date.now();
                    const lastSync = metadata ? new Date(metadata.value).getTime() : 0;
                    const hoursSinceSync = (now - lastSync) / (1000 * 60 * 60);
                    
                    // Sync if: never synced OR more than 24 hours OR less than expected questions
                    const shouldSync = !metadata || 
                                     hoursSinceSync > 24 || 
                                     (metadata.count < 200); // Expected minimum
                    
                    if (shouldSync && navigator.onLine) {
                        console.log('📥 Syncing questions from server...');
                        showSyncStatus('Fragen werden heruntergeladen...', 'info');
                        
                        try {
                            const response = await fetch('/api/questions/all');
                            if (!response.ok) {
                                throw new Error(`HTTP ${response.status}`);
                            }
                            
                            const questions = await response.json();
                            console.log(`📦 Received ${questions.length} questions`);
                            
                            const savedCount = await window.offlineDB.saveQuestions(questions);
                            console.log(`✅ Synced ${savedCount} questions to IndexedDB`);
                            
                            showSyncStatus(`${savedCount} Fragen offline verfügbar!`, 'success');
                        } catch (error) {
                            console.error('❌ Sync failed:', error);
                            showSyncStatus('Sync fehlgeschlagen', 'error');
                        }
                    } else {
                        const count = await window.offlineDB.getQuestionCount();
                        console.log(`📴 Using ${count} cached questions`);
                        
                        if (count > 0 && !navigator.onLine) {
                            showSyncStatus(`${count} Fragen offline verfügbar`, 'offline');
                        }
                    }
                } catch (error) {
                    console.error('❌ OfflineDB initialization error:', error);
                }
            })();

            /**
             * Background Sync für Antworten & Fortschritt
             */
            async function syncPendingData() {
                if (!navigator.onLine) {
                    console.log('⏸️ Offline - sync later');
                    return;
                }

                try {
                    const pendingAnswers = await window.offlineDB.getPendingAnswers();
                    
                    if (pendingAnswers.length === 0) {
                        console.log('✅ No pending answers to sync');
                        return;
                    }

                    console.log(`📤 Syncing ${pendingAnswers.length} pending answers...`);
                    let synced = 0;
                    let failed = 0;

                    for (const answer of pendingAnswers) {
                        try {
                            const response = await fetch(answer.url || '/practice/submit', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': answer.csrf || document.querySelector('meta[name="csrf-token"]')?.content
                                },
                                body: JSON.stringify(answer.data)
                            });

                            if (response.ok) {
                                await window.offlineDB.markAnswerSynced(answer.id);
                                synced++;
                                console.log(`✅ Synced answer ${answer.id}`);
                            } else {
                                failed++;
                                console.log(`❌ Failed to sync answer ${answer.id}: ${response.status}`);
                            }
                        } catch (error) {
                            failed++;
                            console.error(`❌ Error syncing answer ${answer.id}:`, error);
                        }
                    }

                    if (synced > 0) {
                        showSyncStatus(`${synced} Antworten synchronisiert!`, 'success');
                        
                        // Cleanup old synced answers
                        await window.offlineDB.cleanupSyncedAnswers();
                    }

                    if (failed > 0) {
                        console.log(`⚠️ ${failed} answers failed to sync`);
                    }
                } catch (error) {
                    console.error('❌ Background sync error:', error);
                }
            }

            // Sync when coming online
            window.addEventListener('online', () => {
                console.log('🌐 Back online - syncing...');
                setTimeout(syncPendingData, 1000);
            });

            // Periodic sync check (every 5 minutes)
            setInterval(syncPendingData, 5 * 60 * 1000);

            // Initial sync check
            if (navigator.onLine) {
                setTimeout(syncPendingData, 3000);
            }

            /**
             * Show sync status notification
             */
            function showSyncStatus(message, type = 'info') {
                // Remove existing notifications
                const existing = document.querySelectorAll('.sync-notification');
                existing.forEach(el => el.remove());
                
                // Don't show on very small screens or if user dismissed
                if (window.innerWidth < 640 || localStorage.getItem('hide_sync_notifications') === 'true') {
                    return;
                }
                
                const colors = {
                    info: 'bg-blue-500',
                    success: 'bg-green-500',
                    error: 'bg-red-500',
                    offline: 'bg-yellow-500'
                };
                
                const icons = {
                    info: '📥',
                    success: '✅',
                    error: '❌',
                    offline: '📴'
                };
                
                const notification = document.createElement('div');
                notification.className = `sync-notification fixed top-20 right-4 ${colors[type]} text-white px-4 py-3 rounded-lg shadow-2xl z-40 flex items-center gap-2 animate-fade-in`;
                notification.innerHTML = `
                    <span class="text-xl">${icons[type]}</span>
                    <span class="text-sm font-medium">${message}</span>
                    <button onclick="this.parentElement.remove()" class="ml-2 hover:text-gray-200">✕</button>
                `;
                
                document.body.appendChild(notification);
                
                // Auto-remove after 5 seconds (except for errors)
                if (type !== 'error') {
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.style.opacity = '0';
                            notification.style.transform = 'translateX(100%)';
                            setTimeout(() => notification.remove(), 300);
                        }
                    }, 5000);
                }
            }
        </script>
    </body>
</html>
