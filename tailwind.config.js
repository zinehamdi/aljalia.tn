import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Cairo', ...defaultTheme.fontFamily.sans],
                arabic: ['Tajawal', 'Cairo', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'aljalia': {
                    red: '#C8102E', // Tunisian Red
                    dark: '#1e293b',
                    light: '#f8fafc',
                }
            }
        },
    },

    plugins: [forms],
};
