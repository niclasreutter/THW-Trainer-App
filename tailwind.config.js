const defaultTheme = require('tailwindcss/defaultTheme');
const forms = require('@tailwindcss/forms');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],

    safelist: [
        'bg-gradient-to-b',
        'bg-gradient-to-br',
        'bg-gradient-to-r',
        'from-blue-50',
        'from-blue-100',
        'from-blue-200',
        'from-blue-300',
        'from-blue-400',
        'from-blue-500',
        'via-white',
        'via-blue-50',
        'via-blue-100',
        'to-blue-50',
        'to-blue-100',
        'to-blue-200',
        'to-blue-300',
        'to-transparent',
        {
            pattern: /bg-gradient-to-(b|br|r|l|t|tl|tr|bl)/,
        },
        {
            pattern: /from-(blue|white|gray)-(50|100|200|300|400|500|600|700|800|900)/,
        },
        {
            pattern: /to-(blue|white|gray|transparent)-(50|100|200|300|400|500|600|700|800|900)?/,
        },
        {
            pattern: /via-(blue|white|gray)-(50|100|200|300|400|500|600|700|800|900)/,
        },
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
