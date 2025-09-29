<div id="cookie-banner" style="position: fixed !important; bottom: 0 !important; left: 0 !important; right: 0 !important; width: 100% !important; background-color: white !important; border-top: 1px solid #e5e7eb !important; box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1) !important; z-index: 50 !important; display: block !important;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 1.5rem 1rem;">
        <div style="display: flex; flex-direction: column; gap: 1rem; align-items: flex-start;">
            <!-- Cookie Icon und Text -->
            <div style="flex: 1; width: 100%;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <div style="flex-shrink: 0;">
                        <svg style="width: 1.5rem; height: 1.5rem; color: #2563eb; margin-top: 0.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">🍪 Cookie-Hinweis</h3>
                        <p style="font-size: 0.875rem; color: #4b5563; line-height: 1.625;">
                            Wir verwenden nur technisch notwendige Cookies (Laravel Session, CSRF-Schutz) für die Funktionalität unserer Webseite. 
                            Diese Cookies sind für den Betrieb der Seite erforderlich und werden automatisch gesetzt.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Buttons -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem; flex-shrink: 0; width: 100%;">
                <button onclick="acceptCookies()" 
                        style="padding: 0.5rem 1.5rem; background-color: #2563eb; color: white; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer; transition: background-color 0.2s;">
                    Verstanden
                </button>
                <a href="{{ route('datenschutz') }}" 
                   style="padding: 0.5rem 1.5rem; border: 1px solid #d1d5db; color: #374151; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; text-decoration: none; text-align: center; transition: background-color 0.2s; display: block;">
                    Datenschutz
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Cookie-Banner JavaScript
function showCookieBanner() {
    const banner = document.getElementById('cookie-banner');
    if (!getCookie('cookie_consent')) {
        banner.style.display = 'block';
    } else {
        banner.style.display = 'none';
    }
}

function acceptCookies() {
    setCookie('cookie_consent', 'accepted', 365);
    hideCookieBanner();
}

function hideCookieBanner() {
    const banner = document.getElementById('cookie-banner');
    banner.style.display = 'none';
}

function setCookie(name, value, days) {
    const expires = new Date();
    expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/;SameSite=Lax';
}

function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for(let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

// Banner beim Laden der Seite anzeigen
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cookie Banner Script geladen');
    showCookieBanner();
});

// Fallback falls DOMContentLoaded bereits ausgelöst wurde
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', showCookieBanner);
} else {
    showCookieBanner();
}
</script>
