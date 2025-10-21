/**
 * THW-Trainer Offline Submit Handler
 * Speichert Antworten offline wenn keine Verbindung besteht
 */

/**
 * Check if current user is guest
 */
function isGuestMode() {
    return window.location.pathname.includes('/guest/') || 
           document.querySelector('meta[name="user-type"]')?.content === 'guest';
}

/**
 * Intercept form submissions für offline handling
 */
function setupOfflineSubmit() {
    // Global form submit handler (capture phase - fires first!)
    document.addEventListener('submit', async function(e) {
        // Check if it's a practice/exam form
        const form = e.target;
        if (!form.action || 
            (!form.action.includes('submit') && 
             !form.action.includes('practice') && 
             !form.action.includes('exam'))) {
            return; // Not a quiz form, allow normal submit
        }
        
        // Nur wenn offline
        if (navigator.onLine) {
            console.log('✅ Online - normal submit');
            return true; // Normal submit
        }
        
        console.log('📴 OFFLINE detected - preventing submit');
        
        // Offline - prevent normal submit
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        // Handle offline submission asynchronously
        (async function() {
            try {
                const formData = new FormData(form);
                const data = {};
                
                // Convert FormData to object
                for (let [key, value] of formData.entries()) {
                    if (key.endsWith('[]')) {
                        // Array field (multiple answers)
                        const arrayKey = key.slice(0, -2);
                        if (!data[arrayKey]) {
                            data[arrayKey] = [];
                        }
                        data[arrayKey].push(value);
                    } else {
                        data[key] = value;
                    }
                }
                
                console.log('📦 Form data:', data);
                
                // Check if Guest mode
                const guestMode = isGuestMode();
                console.log('👤 Guest mode:', guestMode);
                
                // ALLE Nutzer (Guest + Eingeloggt) bekommen sofortige Auswertung!
                console.log('🎯 Evaluating answer offline...');
                await handleOfflineSubmit(data, form, guestMode);
                
            } catch (error) {
                console.error('❌ Failed to save answer offline:', error);
                alert('Fehler beim Offline-Speichern der Antwort: ' + error.message);
            }
        })();
        
        return false;
    }, true); // Use capture phase!
}

/**
 * Handle offline submission (für ALLE Nutzer - Guest + Eingeloggt)
 */
async function handleOfflineSubmit(data, form, isGuest) {
    console.log('🔍 handleOfflineSubmit called - isGuest:', isGuest);
    
    const questionId = data.question_id;
    const userAnswer = data.answer || data.answers;
    
    console.log('📝 Question ID:', questionId);
    console.log('👉 User Answer:', userAnswer);
    
    // Get the correct answer from the cached question
    const question = await window.offlineDB.getQuestionById(questionId);
    
    if (!question) {
        console.error('❌ Question not found in cache:', questionId);
        alert('Frage nicht im Cache gefunden. Bitte lade die Seite neu wenn du online bist.');
        return;
    }
    
    console.log('✅ Question found:', question);
    
    // Check if answer is correct
    const correctAnswer = question.richtige_antwort;
    const userAnswerArray = Array.isArray(userAnswer) ? userAnswer : [userAnswer];
    const correctAnswerArray = Array.isArray(correctAnswer) ? correctAnswer.split(',').map(a => a.trim()) : [correctAnswer];
    
    console.log('🎯 Correct answer:', correctAnswerArray);
    console.log('👤 User answer array:', userAnswerArray);
    
    const isCorrect = arraysEqual(userAnswerArray.sort(), correctAnswerArray.sort());
    
    console.log(isCorrect ? '✅ CORRECT!' : '❌ WRONG!');
    
    // Save locally
    if (isGuest) {
        // Guest: Nur lokal speichern
        await window.offlineDB.saveGuestAnswer(questionId, userAnswerArray, isCorrect);
        console.log('✅ Guest answer saved offline');
    } else {
        // Eingeloggter User: Für Sync speichern UND lokal auswerten
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || 
                         document.querySelector('input[name="_token"]')?.value;
        
        const answerData = {
            url: form.action,
            method: 'POST',
            data: data,
            csrf: csrfToken,
            userAgent: navigator.userAgent,
            evaluated_offline: true,
            is_correct: isCorrect
        };
        
        await window.offlineDB.savePendingAnswer(answerData);
        console.log('✅ User answer saved for sync');
    }
    
    // Show result für ALLE (Guest + User)
    showOfflineResult(question, userAnswerArray, isCorrect, isGuest);
    
    console.log('✅ Answer evaluated offline:', questionId, isCorrect ? 'correct' : 'wrong');
}

/**
 * Handle Guest offline submission (DEPRECATED - use handleOfflineSubmit)
 */
async function handleGuestOfflineSubmit(data, form) {
    console.log('🔍 handleGuestOfflineSubmit called with data:', data);
    
    const questionId = data.question_id;
    const userAnswer = data.answer || data.answers;
    
    console.log('📝 Question ID:', questionId);
    console.log('👉 User Answer:', userAnswer);
    
    // Get the correct answer from the cached question
    const question = await window.offlineDB.getQuestionById(questionId);
    
    if (!question) {
        console.error('❌ Question not found in cache:', questionId);
        alert('Frage nicht im Cache gefunden. Bitte lade die Seite neu wenn du online bist.');
        return;
    }
    
    console.log('✅ Question found:', question);
    
    // Check if answer is correct
    const correctAnswer = question.richtige_antwort;
    const userAnswerArray = Array.isArray(userAnswer) ? userAnswer : [userAnswer];
    const correctAnswerArray = Array.isArray(correctAnswer) ? correctAnswer.split(',').map(a => a.trim()) : [correctAnswer];
    
    console.log('🎯 Correct answer:', correctAnswerArray);
    console.log('👤 User answer array:', userAnswerArray);
    
    const isCorrect = arraysEqual(userAnswerArray.sort(), correctAnswerArray.sort());
    
    console.log(isCorrect ? '✅ CORRECT!' : '❌ WRONG!');
    
    // Save guest answer locally
    await window.offlineDB.saveGuestAnswer(questionId, userAnswerArray, isCorrect);
    
    // Show result locally
    showGuestOfflineResult(question, userAnswerArray, isCorrect);
    
    console.log('✅ Guest answer saved offline:', questionId, isCorrect ? 'correct' : 'wrong');
}

/**
 * Show offline result (für Guest + Eingeloggte User)
 */
function showOfflineResult(question, userAnswer, isCorrect, isGuest) {
    const resultDiv = document.createElement('div');
    resultDiv.className = `fixed top-20 right-4 ${isCorrect ? 'bg-green-500' : 'bg-red-500'} text-white px-6 py-4 rounded-lg shadow-2xl z-50 max-w-md animate-slide-in`;
    
    const syncMessage = isGuest 
        ? 'Offline gespeichert - nur auf diesem Gerät' 
        : 'Offline ausgewertet - Wird synchronisiert wenn du online bist';
    
    resultDiv.innerHTML = `
        <div class="flex items-start gap-3">
            <span class="text-3xl">${isCorrect ? '✅' : '❌'}</span>
            <div class="flex-1">
                <div class="font-bold text-lg mb-1">${isCorrect ? 'Richtig!' : 'Falsch!'}</div>
                <div class="text-sm opacity-90 mb-2">${syncMessage}</div>
                ${!isCorrect ? `<div class="text-sm mt-2 bg-white bg-opacity-20 p-2 rounded">✓ Richtige Antwort: ${question.richtige_antwort}</div>` : ''}
                ${isCorrect && !isGuest ? `<div class="text-xs opacity-75 mt-2">📤 Wird für Punkte synchronisiert</div>` : ''}
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-xl hover:text-gray-200 leading-none">✕</button>
        </div>
    `;
    
    document.body.appendChild(resultDiv);
    
    // Auto-remove
    setTimeout(() => {
        if (resultDiv.parentElement) {
            resultDiv.style.opacity = '0';
            resultDiv.style.transform = 'translateX(100%)';
            resultDiv.style.transition = 'all 0.3s';
            setTimeout(() => resultDiv.remove(), 300);
        }
    }, isCorrect ? 4000 : 6000); // Falsche Antworten länger zeigen
}

/**
 * Show guest offline result (DEPRECATED - use showOfflineResult)
 */
function showGuestOfflineResult(question, userAnswer, isCorrect) {
    showOfflineResult(question, userAnswer, isCorrect, true);
}

/**
 * Check if two arrays are equal
 */
function arraysEqual(a, b) {
    if (a.length !== b.length) return false;
    for (let i = 0; i < a.length; i++) {
        if (a[i] !== b[i]) return false;
    }
    return true;
}

/**
 * Show notification when answer is saved offline
 */
function showOfflineSubmitNotification(type = 'user') {
    const notification = document.createElement('div');
    notification.className = 'fixed top-20 right-4 bg-yellow-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3 animate-fade-in';
    notification.innerHTML = `
        <span class="text-2xl">📴</span>
        <div>
            <div class="font-bold">Offline gespeichert</div>
            <div class="text-sm opacity-90">Wird synchronisiert wenn du online bist</div>
        </div>
        <button onclick="this.parentElement.remove()" class="ml-2 hover:text-gray-200 text-xl">✕</button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            notification.style.transition = 'all 0.3s';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

/**
 * Show pending answers indicator
 */
async function showPendingIndicator() {
    try {
        const count = await window.offlineDB.getPendingAnswersCount();
        
        // Remove existing indicator
        const existing = document.getElementById('pendingAnswersIndicator');
        if (existing) existing.remove();
        
        if (count > 0) {
            const indicator = document.createElement('div');
            indicator.id = 'pendingAnswersIndicator';
            indicator.className = 'fixed bottom-4 left-4 bg-orange-500 text-white px-4 py-2 rounded-full shadow-lg z-40 flex items-center gap-2 text-sm font-medium';
            indicator.innerHTML = `
                <span class="animate-pulse">📤</span>
                <span>${count} Antwort${count > 1 ? 'en' : ''} warten auf Synchronisation</span>
            `;
            
            document.body.appendChild(indicator);
        }
    } catch (error) {
        console.error('❌ Failed to show pending indicator:', error);
    }
}

// Auto-setup when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        setupOfflineSubmit();
        showPendingIndicator();
        
        // Update indicator when coming online
        window.addEventListener('online', () => {
            setTimeout(showPendingIndicator, 2000);
        });
    });
} else {
    setupOfflineSubmit();
    showPendingIndicator();
    
    window.addEventListener('online', () => {
        setTimeout(showPendingIndicator, 2000);
    });
}

// Update indicator every minute
setInterval(showPendingIndicator, 60 * 1000);

/**
 * Show Guest Stats (offline progress)
 */
async function showGuestStatsIndicator() {
    if (!isGuestMode()) return;
    
    try {
        const stats = await window.offlineDB.getGuestStats();
        
        // Remove existing
        const existing = document.getElementById('guestStatsIndicator');
        if (existing) existing.remove();
        
        if (stats.total_answered > 0) {
            const indicator = document.createElement('div');
            indicator.id = 'guestStatsIndicator';
            indicator.className = 'fixed top-4 left-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg z-40 text-sm font-medium';
            indicator.innerHTML = `
                <div class="flex items-center gap-2">
                    <span>📊</span>
                    <div>
                        <div class="font-bold">${stats.total_answered} Fragen beantwortet</div>
                        <div class="text-xs opacity-90">${stats.accuracy}% richtig (nur offline)</div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(indicator);
        }
    } catch (error) {
        console.error('❌ Failed to show guest stats:', error);
    }
}

// Show guest stats on guest pages
if (isGuestMode()) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showGuestStatsIndicator);
    } else {
        showGuestStatsIndicator();
    }
    
    // Update every 30 seconds
    setInterval(showGuestStatsIndicator, 30 * 1000);
}

