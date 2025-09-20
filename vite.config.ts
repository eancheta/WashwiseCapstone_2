import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';

export default defineConfig(({ mode }) => {
  return {
    // production assets should be served from /build/ so runtime asks for /build/assets/...
    base: mode === 'production' ? '/build/' : '/',
    plugins: [
      laravel({
        input: [
          'resources/js/app.ts', // main entry
          'resources/css/app.css', // optional CSS
        ],
        refresh: true,
      }),
      tailwindcss(),
      vue({
        template: {
          transformAssetUrls: {
            base: null,
            includeAbsolute: false,
          },
        },
      }),
    ],
    resolve: {
      alias: {
        '@': path.resolve(__dirname, './resources/js'),
      },
    },
    build: {
      manifest: true,
      outDir: 'public/build',
      // make assets folder explicit (Vite default is 'assets' anyway)
      assetsDir: 'assets',
    },
  };
});
