@extends('layouts.app')

@section('title', 'THW-Trainer - Kostenlos THW Theorie üben')
@section('description', 'THW-Trainer: Dein kostenloser digitaler Begleiter für die THW-Theorieprüfung. Alle aktuellen Fragen, Prüfungssimulation und Lernfortschritt. Jetzt anonym starten oder kostenlos registrieren!')

@section('content')
<div class="min-h-screen bg-[#FDFDFC]">
    
    <!-- Account gelöscht Meldung  -->
    @if (session('status') == 'account-deleted')
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">
                            ✅ Dein Account wurde erfolgreich gelöscht. Alle deine Daten wurden permanent entfernt.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Blauer Block 50% Bildschirmhöhe -->
    <div style="height: 50vh; margin-top: -4px; position: relative; background: linear-gradient(to bottom, #00337F 0%, #00337F 20%, rgba(0, 51, 127, 0.8) 40%, rgba(0, 51, 127, 0.4) 60%, rgba(0, 51, 127, 0.1) 80%, transparent 100%); z-index: 1;">
        <!-- Inhalt -->
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; padding: 0 1rem; width: 100%; max-width: 90%;">
            <!-- Haupttitel -->
            <h1 style="font-size: clamp(2rem, 8vw, 3rem); font-weight: bold; color: white; margin-bottom: 1rem; letter-spacing: -0.025em;">
                THW-Trainer
            </h1>
            
            <!-- Untertitel -->
            <p style="font-size: clamp(1rem, 4vw, 1.5rem); color: #dbeafe; margin-bottom: 2rem; max-width: 48rem; margin-left: auto; margin-right: auto; font-weight: 300; line-height: 1.4;">
                Dein digitaler Begleiter in der Grundausbildung zum Üben der THW-Theorie!
            </p>
            
            <!-- CTA Button -->
            <a href="#" 
               id="cta-button"
               style="display: inline-flex; align-items: center; justify-content: center; padding: clamp(10px, 3vw, 12px) clamp(20px, 5vw, 24px); font-size: clamp(0.9rem, 3vw, 1.125rem); font-weight: bold; color: #1e3a8a; background: #fbbf24; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.25), 0 0 25px rgba(251, 191, 36, 0.8); text-decoration: none; transition: all 0.3s; cursor: pointer;"
               onmouseover="this.style.background='#f59e0b'; this.style.transform='scale(1.05)'"
               onmouseout="this.style.background='#fbbf24'; this.style.transform='scale(1)'"
               onclick="launchRocket(event)">
                <span id="rocket" style="font-size: clamp(1rem, 3vw, 1.25rem); margin-right: 8px; animation: rocket-bounce 1.5s infinite;">🚀</span>
                <span>THW-Theorie kostenlos üben</span>
            </a>
            
            <!-- Anonym Text -->
            <div style="margin-top: 1rem;">
                <a href="{{ route('guest.practice.menu') }}" 
                   style="color: #1e3a8a; font-size: clamp(0.8rem, 2.5vw, 1rem); text-decoration: underline; font-weight: 500; text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8); transition: all 0.3s;"
                   onmouseover="this.style.color='#fbbf24'; this.style.textShadow='0 2px 4px rgba(0, 0, 0, 0.4)'"
                   onmouseout="this.style.color='#1e3a8a'; this.style.textShadow='0 1px 2px rgba(255, 255, 255, 0.8)'">
                    Anonym ohne Login üben
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-16 bg-[#FDFDFC] relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-yellow-50"></div>
        <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-transparent to-white"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-12 lg:mb-24">
                <h2 class="text-2xl lg:text-6xl font-bold text-gray-900 mb-4 lg:mb-8 tracking-tight">
                    🎯 Was bietet der THW-Trainer?
                </h2>
                <p class="text-lg lg:text-5xl text-gray-600 max-w-4xl mx-auto leading-relaxed font-light px-4">
                    Alles, was du brauchst, um dich optimal auf deine Grundausbildung Theorie-Prüfung im THW vorzubereiten
                </p>
            </div>
            <br>
            
            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-blue-800 mb-4">📚 Alle Theoriefragen</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Umfassende Sammlung aller THW-Theoriefragen zum Üben und Lernen. 
                        Von Grundlagen bis zu spezialisierten Bereichen.
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-blue-800 mb-4">🎓 Prüfungssimulation</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Realistische Prüfungssimulation verfügbar, sobald alle Fragen bearbeitet wurden. 
                        Teste dich unter echten Bedingungen.
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-blue-800 mb-4">📊 Lernfortschritt</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Dein Fortschritt wird gespeichert und im persönlichen Dashboard angezeigt. 
                        Verfolge deine Erfolge und Schwächen.
                    </p>
                </div>
            </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div style="background-color: #00337F; padding: 2rem 0; lg:padding: 4rem 0;">
        <div class="max-w-6xl mx-auto text-center" style="padding: 0 1rem; lg:padding: 0 3rem;">
            <h2 class="text-xl lg:text-6xl font-bold text-white mb-4 lg:mb-6 tracking-tight">
                📑 Bereit zum Lernen?
            </h2>
            <p class="text-base lg:text-5xl text-white mb-8 lg:mb-16 max-w-4xl mx-auto leading-relaxed font-light px-4">
                Starte jetzt mit dem THW-Trainer und bereite dich optimal auf deine Grundausbildung Theorie-Prüfung im THW vor. 
            </p>
            <p class="text-base lg:text-5xl text-white max-w-4xl mx-auto leading-relaxed font-light px-4" style="margin-bottom: 2rem;">
            Registriere dich kostenlos und beginne sofort mit dem Lernen, egal ob Handy, Laptop oder Tablet!
            Ein Account, ein Lernstand!
            </p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 max-w-2xl mx-auto" style="gap: 1rem;">
                <a href="{{ route('register') }}" 
                   class="bg-yellow-400 text-blue-900 px-8 py-3 font-bold hover:bg-yellow-300 transition-all duration-300 text-center" style="border-radius: 12px;">
                    Jetzt kostenlos anmelden
                </a>
                
                <a href="{{ route('login') }}" 
                   class="px-8 py-3 font-bold border-2 border-white hover:bg-white hover:text-blue-900 transition-all duration-300 text-center" style="background-color: #FDFDFC; color: #00337F; border-radius: 12px;">
                    Login
                </a>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="py-12 lg:py-16 bg-[#FDFDFC]">
        <div class="max-w-2xl lg:max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 lg:mb-12">
                <h2 class="text-2xl lg:text-4xl font-bold text-gray-900 mb-4 tracking-tight">
                    ❓ Häufig gestellte Fragen
                </h2>
                <p class="text-base lg:text-xl text-gray-600 max-w-xl mx-auto leading-relaxed">
                    Alles was du über THW Grundausbildung und den THW-Trainer wissen musst
                </p>
            </div>
            
            <div class="space-y-3">
                <!-- FAQ Item 1 -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <button class="faq-toggle w-full text-left p-4 lg:p-5 flex justify-between items-center hover:bg-blue-50 transition-colors rounded-lg" onclick="toggleFAQ('faq1')">
                        <span class="text-base lg:text-lg font-medium text-gray-900 pr-4">Was ist die THW Grundausbildung?</span>
                        <span class="faq-icon text-xl text-blue-600 font-bold flex-shrink-0">+</span>
                    </button>
                    <div id="faq1" class="faq-content hidden px-4 lg:px-5 pb-4 lg:pb-5">
                        <p class="text-gray-700 leading-relaxed text-sm lg:text-base">
                            Die THW Grundausbildung ist die erste Ausbildungsstufe im Technischen Hilfswerk. 
                            Sie vermittelt die grundlegenden Kenntnisse und Fähigkeiten für alle THW-Helfer. 
                            Die Theorie-Prüfung ist ein wichtiger Bestandteil dieser Ausbildung und umfasst 
                            Themen wie Rechtsgrundlagen, Organisation des THW, Einsatzgrundlagen und technisches Wissen.
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <button class="faq-toggle w-full text-left p-4 lg:p-5 flex justify-between items-center hover:bg-blue-50 transition-colors rounded-lg" onclick="toggleFAQ('faq2')">
                        <span class="text-base lg:text-lg font-medium text-gray-900 pr-4">Wie bereite ich mich auf die THW Grundausbildung Theorie vor?</span>
                        <span class="faq-icon text-xl text-blue-600 font-bold flex-shrink-0">+</span>
                    </button>
                    <div id="faq2" class="faq-content hidden px-4 lg:px-5 pb-4 lg:pb-5">
                        <p class="text-gray-700 leading-relaxed text-sm lg:text-base">
                            Der THW-Trainer bietet dir alle aktuelle THW-Theoriefragen zur optimalen Vorbereitung. 
                            Übe systematisch alle Themenbereiche, nutze die Prüfungssimulation und verfolge deinen Lernfortschritt. 
                            Die App funktioniert auf allen Geräten, sodass du auch unterwegs lernen kannst.
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <button class="faq-toggle w-full text-left p-4 lg:p-5 flex justify-between items-center hover:bg-blue-50 transition-colors rounded-lg" onclick="toggleFAQ('faq3')">
                        <span class="text-base lg:text-lg font-medium text-gray-900 pr-4">Ist der THW-Trainer kostenlos?</span>
                        <span class="faq-icon text-xl text-blue-600 font-bold flex-shrink-0">+</span>
                    </button>
                    <div id="faq3" class="faq-content hidden px-4 lg:px-5 pb-4 lg:pb-5">
                        <p class="text-gray-700 leading-relaxed text-sm lg:text-base">
                            Ja, der THW-Trainer ist komplett kostenlos! Du kannst sofort mit dem Lernen beginnen, 
                            ohne jegliche Kosten. Auch eine Anmeldung ist nicht zwingend erforderlich - 
                            du kannst anonym üben oder dich kostenlos registrieren, um deinen Lernfortschritt zu speichern.
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <button class="faq-toggle w-full text-left p-4 lg:p-5 flex justify-between items-center hover:bg-blue-50 transition-colors rounded-lg" onclick="toggleFAQ('faq4')">
                        <span class="text-base lg:text-lg font-medium text-gray-900 pr-4">Wie viele Fragen gibt es im THW-Trainer?</span>
                        <span class="faq-icon text-xl text-blue-600 font-bold flex-shrink-0">+</span>
                    </button>
                    <div id="faq4" class="faq-content hidden px-4 lg:px-5 pb-4 lg:pb-5">
                        <p class="text-gray-700 leading-relaxed text-sm lg:text-base">
                            Der THW-Trainer enthält alle aktuelle THW-Theoriefragen aus allen relevanten Bereichen 
                            der Grundausbildung. Die Fragen werden regelmäßig aktualisiert und spiegeln den 
                            aktuellen Stand der THW-Ausbildung wider.
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <button class="faq-toggle w-full text-left p-4 lg:p-5 flex justify-between items-center hover:bg-blue-50 transition-colors rounded-lg" onclick="toggleFAQ('faq5')">
                        <span class="text-base lg:text-lg font-medium text-gray-900 pr-4">Welche Themen werden in der THW Grundausbildung abgefragt?</span>
                        <span class="faq-icon text-xl text-blue-600 font-bold flex-shrink-0">+</span>
                    </button>
                    <div id="faq5" class="faq-content hidden px-4 lg:px-5 pb-4 lg:pb-5">
                        <p class="text-gray-700 leading-relaxed text-sm lg:text-base">
                            Die THW Grundausbildung umfasst Themen wie: Rechtsgrundlagen, Organisation des THW, 
                            Einsatzgrundlagen, Gefahren der Einsatzstelle, Technische Hilfe, Einsatzablauf, 
                            Führung und Kommunikation.
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 6 -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <button class="faq-toggle w-full text-left p-4 lg:p-5 flex justify-between items-center hover:bg-blue-50 transition-colors rounded-lg" onclick="toggleFAQ('faq6')">
                        <span class="text-base lg:text-lg font-medium text-gray-900 pr-4">Funktioniert der THW-Trainer auf dem Handy?</span>
                        <span class="faq-icon text-xl text-blue-600 font-bold flex-shrink-0">+</span>
                    </button>
                    <div id="faq6" class="faq-content hidden px-4 lg:px-5 pb-4 lg:pb-5">
                        <p class="text-gray-700 leading-relaxed text-sm lg:text-base">
                            Ja, der THW-Trainer ist vollständig responsive und funktioniert optimal auf Smartphones, 
                            Tablets und Desktop-Computern. Du kannst überall und jederzeit lernen - 
                            egal ob zu Hause, unterwegs oder in der Pause.
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 7 -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <button class="faq-toggle w-full text-left p-4 lg:p-5 flex justify-between items-center hover:bg-blue-50 transition-colors rounded-lg" onclick="toggleFAQ('faq7')">
                        <span class="text-base lg:text-lg font-medium text-gray-900 pr-4">Wie schwer ist die THW Grundausbildung Theorie-Prüfung?</span>
                        <span class="faq-icon text-xl text-blue-600 font-bold flex-shrink-0">+</span>
                    </button>
                    <div id="faq7" class="faq-content hidden px-4 lg:px-5 pb-4 lg:pb-5">
                        <p class="text-gray-700 leading-relaxed text-sm lg:text-base">
                            Mit der richtigen Vorbereitung ist die THW Grundausbildung Theorie-Prüfung gut zu schaffen. 
                            Der THW-Trainer hilft dir dabei, alle wichtigen Themen zu verstehen und zu üben. 
                            Nutze die Prüfungssimulation, um dich unter realistischen Bedingungen zu testen.
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 8 -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <button class="faq-toggle w-full text-left p-4 lg:p-5 flex justify-between items-center hover:bg-blue-50 transition-colors rounded-lg" onclick="toggleFAQ('faq8')">
                        <span class="text-base lg:text-lg font-medium text-gray-900 pr-4">Ist der THW-Trainer offiziell vom THW?</span>
                        <span class="faq-icon text-xl text-blue-600 font-bold flex-shrink-0">+</span>
                    </button>
                    <div id="faq8" class="faq-content hidden px-4 lg:px-5 pb-4 lg:pb-5">
                        <p class="text-gray-700 leading-relaxed text-sm lg:text-base">
                            Der THW-Trainer ist eine private Initiative eines aktiven THW-Mitglieds und nicht offiziell 
                            vom THW herausgegeben. Die Fragen basieren jedoch auf den offiziellen Ausbildungsunterlagen 
                            und werden regelmäßig aktualisiert, um den aktuellen Stand der THW-Ausbildung zu reflektieren.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Der Kopf Dahinter Section -->
    <div class="py-12 lg:py-16 bg-[#FDFDFC]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6 lg:gap-12 items-center">
                <!-- Container links - Bild -->
                <div class="flex-shrink-0 flex justify-center lg:justify-start">
                    <div class="relative">
                        <picture>
                            <source srcset="{{ asset('niclas_compressed.webp') }}" type="image/webp">
                            <img src="{{ asset('niclas_compressed.png') }}" 
                                 alt="Niclas Reutter - Entwickler des THW-Trainers" 
                                 class="h-auto object-contain block"
                                 style="max-height: 300px; max-width: 250px; opacity: 1; visibility: visible;"
                                 loading="lazy"
                                 width="250"
                                 height="300">
                        </picture>
                    </div>
                </div>
                
                <!-- Container rechts - Text -->
                <div class="flex-1 space-y-4 lg:space-y-6">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-bold text-gray-900 tracking-tight">
                        Der Kopf Dahinter
                    </h2>
                    <div class="space-y-4 text-base sm:text-lg text-gray-700 leading-relaxed">
                        <p>
                            Hallo! Ich bin Niclas, der Entwickler hinter dem THW-Trainer. Als aktives THW-Mitglied 
                            kenne ich die Herausforderungen bei der Vorbereitung auf die Theoriefragen nur zu gut.
                        </p>
                        <p>
                            Mit dieser App möchte ich dir eine moderne, intuitive und effektive Möglichkeit bieten, 
                            dich optimal auf deine THW-Prüfung vorzubereiten. Alle Fragen sind sorgfältig ausgewählt 
                            und spiegeln den aktuellen Stand der THW-Ausbildung wider.
                        </p>
                        <p>
                            Viel Erfolg bei deiner Prüfung! 🚀
                        </p>
                        <p>
                            Diese Webseite stelle ich kostenlos zur Verfügung und finanziere alle Kosten für Webseite, Domain und Server selbst. 
                            Finanziere mich mit einem Kaffee!
                        </p>
                    </div>
                    <div class="pt-4">
                        <a href="https://paypal.me/reuttern" 
                           target="_blank"
                           class="inline-flex items-center px-6 py-3 text-white font-semibold rounded-lg transition-colors duration-300 hover:shadow-lg" 
                           style="background-color: #00337F;"
                           onmouseover="this.style.backgroundColor='#002a66'"
                           onmouseout="this.style.backgroundColor='#00337F'">
                            <span class="mr-2">☕</span>
                            Finanziere meinen Kaffee
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
 
</div>

<style>
@keyframes rocket-bounce {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
    }
    25% {
        transform: translateY(-2px) rotate(-2deg);
    }
    50% {
        transform: translateY(-4px) rotate(0deg);
    }
    75% {
        transform: translateY(-2px) rotate(2deg);
    }
}

@keyframes rocket-launch {
    0% {
        transform: translateY(0px) rotate(0deg) scale(1);
        opacity: 1;
    }
    50% {
        transform: translateY(-20px) rotate(-10deg) scale(1.2);
        opacity: 0.8;
    }
    100% {
        transform: translateY(-100px) rotate(-20deg) scale(0.5);
        opacity: 0;
    }
}

.rocket-launching {
    animation: rocket-launch 1s ease-out forwards !important;
}
</style>

<script>
function launchRocket(event) {
    event.preventDefault();
    
    const rocket = document.getElementById('rocket');
    const button = document.getElementById('cta-button');
    
    // Rakete starten
    rocket.classList.add('rocket-launching');
    
    // Button deaktivieren während der Animation
    button.style.pointerEvents = 'none';
    button.style.opacity = '0.7';
    
    // Nach 1 Sekunde zur Dashboard-Seite weiterleiten
    setTimeout(() => {
        window.location.href = '{{ route("dashboard") }}';
    }, 1000);
}

// FAQ Toggle Funktion
function toggleFAQ(faqId) {
    const content = document.getElementById(faqId);
    const icon = content.previousElementSibling.querySelector('.faq-icon');
    
    if (content.classList.contains('hidden')) {
        // FAQ öffnen
        content.classList.remove('hidden');
        icon.textContent = '−';
        icon.style.transform = 'rotate(0deg)';
    } else {
        // FAQ schließen
        content.classList.add('hidden');
        icon.textContent = '+';
        icon.style.transform = 'rotate(0deg)';
    }
}

// Smooth scroll für bessere UX
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Icons mit Rotation
    const faqIcons = document.querySelectorAll('.faq-icon');
    faqIcons.forEach(icon => {
        icon.style.transition = 'transform 0.3s ease';
    });
});
</script>

@endsection
