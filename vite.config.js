import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],

    // allow Vite dev server to be accessed from network IPs (for mobile testing, LAN access, etc.)
    server: {
        host: true,            // listen on all addresses (0.0.0.0 / ::)
        strictPort: false,
        hmr: {
            host: '192.168.1.21',   // HMR client should connect to this IP
        },
        cors: true,           // automatically send Access-Control-Allow-Origin
        // force client scripts to reference the real network address instead of 0.0.0.0
        origin: 'http://192.168.1.21:5173',
    },
});
