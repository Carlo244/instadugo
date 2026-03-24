import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const devHost = env.VITE_DEV_SERVER_HOST;
    const devPort = Number(env.VITE_DEV_SERVER_PORT || 5173);

    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                ],
                refresh: true,
            }),
        ],

        server: {
            port: devPort,
            host: true,
            strictPort: false,
            cors: true,
            ...(devHost
                ? {
                      hmr: { host: devHost },
                  }
                : {}),
        },
    };
});
