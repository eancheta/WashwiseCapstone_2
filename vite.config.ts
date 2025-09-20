import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';

export default defineConfig(({ mode }) => {
  return {
    base: mode === 'production'
      ? 'https://washwisecapstone2-production.up.railway.app/'
      : '/',
    plugins: [
      laravel({
        input: [
          'resources/js/app.ts', // only main entry point
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
    },
  };
});
