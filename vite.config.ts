import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { defineConfig } from 'vite';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        path.resolve(__dirname, 'resources/js/app.ts'),
        path.resolve(__dirname, 'resources/js/Pages/Welcome.vue'),
      ],
      refresh: true,
    }),
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
    rollupOptions: {
      input: {
        app: path.resolve(__dirname, 'resources/js/app.ts'),
        welcome: path.resolve(__dirname, 'resources/js/Pages/Welcome.vue'),
      },
    },
  },
  server: {
    host: '0.0.0.0',
    hmr: {
      host: 'localhost',
      protocol: 'ws',
    },
  },
});
