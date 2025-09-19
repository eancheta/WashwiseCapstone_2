import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';

export default defineConfig({
  // Base URL for production builds
  base: '/build/',

  plugins: [
    // Laravel plugin
    laravel({
      input: [
        'resources/js/app.ts',                // main JS/TS entry
        'resources/js/pages/Welcome.vue',     // adjust path to your actual folder
      ],
      refresh: true,
    }),

    // Tailwind CSS plugin
    tailwindcss(),

    // Vue 3 plugin
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
    manifest: true,         // required by Laravel to read assets
    outDir: 'public/build', // make sure Laravel can find it
    rollupOptions: {
      input: [
        'resources/js/app.ts',
        'resources/js/pages/Welcome.vue',
      ],
    },
  },
});
