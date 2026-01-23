/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/css/**/*.css',
        './resources/views/filament/pages/year-filter.blade.php',
    ],

    // --- TAMBAHKAN BAGIAN SAFELIST INI ---
    safelist: [
        // Ukuran Baru (w-4 h-4 = 16px)
        'w-4', 'h-4',
        'w-3.5', 'h-3.5', // Untuk legend
        'rounded-[3px]',
        'gap-1', // Jarak baru (4px)
        'border',

        // Warna
        'bg-transparent', 'border-transparent',
        'bg-gray-200', 'border-gray-300',
        'bg-green-300', 'border-green-400',
        'bg-green-400', 'border-green-500',
        'bg-green-500', 'border-green-600',
        'bg-green-700', 'border-green-800',
    ],
    // -------------------------------------

    theme: {
        extend: {},
    },
    plugins: [],
};