<!-- Offline Form Protection - Prevents page reload when offline -->
<script>
(function() {
    'use strict';
    
    // Immediate form protection (runs before any other scripts)
    const form = document.currentScript?.parentElement?.querySelector('form') || 
                 document.querySelector('form[action*="submit"]');
    
    if (form) {
        // Add onsubmit attribute directly
        form.setAttribute('onsubmit', 'if(!navigator.onLine){event.preventDefault();console.log("⚠️ Form blocked - offline");return false;}');
        
        // Also add event listener with highest priority
        form.addEventListener('submit', function(e) {
            if (!navigator.onLine) {
                console.log('🚫 Blocking form submission - OFFLINE');
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        }, true);
        
        console.log('✅ Offline form protection active');
    }
})();
</script>

