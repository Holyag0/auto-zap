import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                'cabemce-blue': '#1e40af',
                'cabemce-red': '#dc2626',
                'cabemce-gold': '#d4af37',
            },
        },
    },
}
