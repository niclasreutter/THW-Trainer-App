<!-- Achievement Popup (wird über JavaScript eingeblendet) -->
<div id="achievementPopup" class="fixed top-4 right-4 z-50 transform translate-x-full transition-transform duration-500 ease-in-out">
    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded-lg shadow-lg p-4 max-w-sm">
        <div class="flex items-center">
            <div class="text-3xl mr-3" id="achievementIcon">🏆</div>
            <div>
                <div class="font-bold text-sm">Neues Achievement!</div>
                <div class="text-xs" id="achievementTitle">Achievement Title</div>
            </div>
            <button onclick="hideAchievementPopup()" class="ml-2 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
function showAchievementPopup(icon, title) {
    const popup = document.getElementById('achievementPopup');
    const iconEl = document.getElementById('achievementIcon');
    const titleEl = document.getElementById('achievementTitle');
    
    iconEl.textContent = icon;
    titleEl.textContent = title;
    
    popup.classList.remove('translate-x-full');
    popup.classList.add('translate-x-0');
    
    // Auto-hide nach 5 Sekunden
    setTimeout(() => {
        hideAchievementPopup();
    }, 5000);
}

function hideAchievementPopup() {
    const popup = document.getElementById('achievementPopup');
    popup.classList.remove('translate-x-0');
    popup.classList.add('translate-x-full');
}

// Beispiel für automatisches Anzeigen (kann in anderen Views verwendet werden)
// showAchievementPopup('🎯', '🌟 Erste Schritte - Erste Frage beantwortet');
</script>
