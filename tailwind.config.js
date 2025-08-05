export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: [
                    'Instrument Sans',
                    'ui-sans-serif',
                    'system-ui',
                    'sans-serif',
                    'Apple Color Emoji',
                    'Segoe UI Emoji',
                    'Segoe UI Symbol',
                    'Noto Color Emoji',
                ],
            },
            colors: {
                primary: {
                    100: '#e0f0ff',
                    200: '#b3d9ff',
                    300: '#80c1ff',
                    400: '#4da9ff',
                    500: '#1a91ff',  // base
                    600: '#007ae6',
                    700: '#0062bf',
                    800: '#004b99',
                    900: '#003366',
                },
                secondary: {
                    100: '#f0fbff',
                    200: '#d9f3fc',
                    300: '#c2eafb',
                    400: '#a3d8f8', // base
                    500: '#7fc4f2',
                    600: '#5ab0e7',
                    700: '#3a9bd6',
                    800: '#1e86c2',
                    900: '#0e6ea9',
                },
            },
        },
    },
    plugins: [],
}
