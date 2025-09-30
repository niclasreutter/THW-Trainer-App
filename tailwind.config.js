import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js',  // ← NEU: JS-Dateien scannen
        './resources/**/*.vue', // ← NEU: Vue-Dateien scannen
    ],

    // ← NEU: Safelist hinzufügen
    safelist: [
        'bg-gradient-to-b',
        'bg-gradient-to-br',
        'bg-gradient-to-r',
        'from-blue-50',
        'from-blue-100',
        'from-blue-500',
        'via-white',
        'to-blue-100',
        'to-transparent',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
