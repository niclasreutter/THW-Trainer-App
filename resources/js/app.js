import './bootstrap';

import Alpine from 'alpinejs';
import confetti from 'canvas-confetti';

window.Alpine = Alpine;

// Confetti Funktionen für Gamification
window.triggerLevelUpConfetti = function() {
    const duration = 3000;
    const animationEnd = Date.now() + duration;
    const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 9999 };

    function randomInRange(min, max) {
        return Math.random() * (max - min) + min;
    }

    const interval = setInterval(function() {
        const timeLeft = animationEnd - Date.now();

        if (timeLeft <= 0) {
            return clearInterval(interval);
        }

        const particleCount = 50 * (timeLeft / duration);

        // Level-Up: Gold und Orange
        confetti(Object.assign({}, defaults, {
            particleCount,
            origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 },
            colors: ['#FFD700', '#FFA500', '#FFDF00', '#FF8C00']
        }));
        confetti(Object.assign({}, defaults, {
            particleCount,
            origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 },
            colors: ['#FFD700', '#FFA500', '#FFDF00', '#FF8C00']
        }));
    }, 250);
};

window.triggerAchievementConfetti = function() {
    const duration = 2500;
    const animationEnd = Date.now() + duration;
    const defaults = { startVelocity: 25, spread: 360, ticks: 50, zIndex: 9999 };

    function randomInRange(min, max) {
        return Math.random() * (max - min) + min;
    }

    const interval = setInterval(function() {
        const timeLeft = animationEnd - Date.now();

        if (timeLeft <= 0) {
            return clearInterval(interval);
        }

        const particleCount = 40 * (timeLeft / duration);

        // Achievement: Lila und Pink
        confetti(Object.assign({}, defaults, {
            particleCount,
            origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 },
            colors: ['#9333EA', '#EC4899', '#A855F7', '#F472B6']
        }));
        confetti(Object.assign({}, defaults, {
            particleCount,
            origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 },
            colors: ['#9333EA', '#EC4899', '#A855F7', '#F472B6']
        }));
    }, 250);
};

// Floating Points Animation
window.showFloatingPoints = function(x, y, points) {
    const el = document.createElement('div');
    el.textContent = `+${points}`;
    el.className = 'floating-points';
    el.style.left = x + 'px';
    el.style.top = y + 'px';
    document.body.appendChild(el);

    // Remove element after animation completes
    setTimeout(() => {
        el.remove();
    }, 1500);
};

// Number Counter Animation
window.animateCounter = function(element, from, to, duration = 800) {
    const start = performance.now();
    const diff = to - from;

    function update(currentTime) {
        const elapsed = currentTime - start;
        const progress = Math.min(elapsed / duration, 1);

        // Ease-out cubic for smooth deceleration
        const easeOut = 1 - Math.pow(1 - progress, 3);

        const currentValue = Math.floor(from + diff * easeOut);
        element.textContent = currentValue;

        if (progress < 1) {
            requestAnimationFrame(update);
        } else {
            // Ensure final value is exact
            element.textContent = to;
        }
    }

    requestAnimationFrame(update);
};

Alpine.start();
