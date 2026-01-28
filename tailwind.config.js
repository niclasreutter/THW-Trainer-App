import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],

    safelist: [
        // Glass effect classes
        'glass', 'glass-subtle', 'glass-accent', 'glass-thw',
        'glass-success', 'glass-error', 'glass-warning',
        // Border radius asymmetric
        'rounded-tl-3xl', 'rounded-br-3xl', 'rounded-br-none', 'rounded-tl-none',
        'rounded-tl-4xl', 'rounded-br-4xl',
        // Gold gradient text
        'text-gradient-gold',
        // Glow effects
        'glow-gold', 'glow-thw', 'glow-success', 'glow-error',
        // Buttons
        'btn-primary', 'btn-secondary', 'btn-ghost', 'btn-danger',
        // Inputs
        'input-glass', 'select-glass', 'checkbox-glass', 'label-glass',
        // Navigation
        'nav-glass', 'nav-link-glass', 'dropdown-glass', 'dropdown-item-glass',
        // Stats
        'stat-card-glass', 'stat-value-glass', 'stat-label-glass',
        // Progress
        'progress-glass', 'progress-fill-gold', 'progress-fill-thw', 'progress-fill-success',
        // Alerts
        'alert-glass',
        // Modals
        'modal-overlay-glass', 'modal-glass',
        // Footer
        'footer-glass',
        // Text utilities
        'text-dark-primary', 'text-dark-secondary', 'text-dark-muted',
        // Border utilities
        'border-gold-accent',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                // Background colors
                'dark': {
                    'base': '#0a0a0b',
                    'elevated': '#121214',
                    'surface': '#1a1a1d',
                    'overlay': '#222226',
                },

                // THW Brand
                'thw': {
                    'DEFAULT': '#00337F',
                    'light': '#004db3',
                    'dark': '#002255',
                },

                // Gold accent
                'gold': {
                    'DEFAULT': '#fbbf24',
                    'dark': '#f59e0b',
                    'light': '#fcd34d',
                },

                // Semantic colors
                'success': '#22c55e',
                'error': '#ef4444',
                'warning': '#f59e0b',
                'info': '#3b82f6',
            },

            backgroundColor: {
                // Glass backgrounds
                'glass': {
                    'white-5': 'rgba(255, 255, 255, 0.05)',
                    'white-10': 'rgba(255, 255, 255, 0.10)',
                    'white-15': 'rgba(255, 255, 255, 0.15)',
                    'white-20': 'rgba(255, 255, 255, 0.20)',
                    'thw-5': 'rgba(0, 51, 127, 0.05)',
                    'thw-10': 'rgba(0, 51, 127, 0.10)',
                    'thw-20': 'rgba(0, 51, 127, 0.20)',
                    'gold-5': 'rgba(251, 191, 36, 0.05)',
                    'gold-10': 'rgba(251, 191, 36, 0.10)',
                },
            },

            borderColor: {
                'glass': {
                    'subtle': 'rgba(255, 255, 255, 0.06)',
                    'default': 'rgba(255, 255, 255, 0.10)',
                    'emphasis': 'rgba(255, 255, 255, 0.20)',
                    'strong': 'rgba(255, 255, 255, 0.40)',
                    'gold': 'rgba(251, 191, 36, 0.30)',
                    'thw': 'rgba(0, 51, 127, 0.40)',
                },
            },

            boxShadow: {
                // Glass shadows
                'glass': '0 8px 32px rgba(0, 0, 0, 0.3)',
                'glass-lg': '0 15px 50px rgba(0, 0, 0, 0.4)',
                'glass-xl': '0 25px 60px rgba(0, 0, 0, 0.5)',

                // Glow effects
                'glow-gold': '0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1)',
                'glow-thw': '0 0 20px rgba(0, 51, 127, 0.4), 0 0 40px rgba(0, 51, 127, 0.2)',
                'glow-success': '0 0 20px rgba(34, 197, 94, 0.3)',
                'glow-error': '0 0 20px rgba(239, 68, 68, 0.3)',

                // Inner glow for inputs
                'inner-glow': 'inset 0 0 20px rgba(255, 255, 255, 0.05)',
            },

            backdropBlur: {
                'xs': '2px',
                'glass': '12px',
                'glass-lg': '20px',
            },

            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
            },
        },
    },

    plugins: [forms],
};
