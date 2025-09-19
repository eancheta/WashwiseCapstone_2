import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { defineConfig } from 'vite';

export default defineConfig({
  // IMPORTANT: make asset URLs absolute and point at /build/
  base: '/build/',

  plugins: [
    // Use the real entry points (css + single app entry)
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.ts', // <- keep this in sync with your real entry file
      ],
      refresh: true,
    }),

    // Vue plugin for .vue files
    vue({
      template: {
        // leave default behavior so Vite rewrites asset URLs correctly
        transformAssetUrls: {
          // DON'T set base: null here — let Vite handle it
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
    outDir: 'public/build',
    manifest: true,
    assetsDir: 'assets', // default, ensures files go to public/build/assets
    rollupOptions: {
      // optional — keep a single entry for app (Vite + laravel plugin handle the rest)
      input: {
        app: 'resources/js/app.ts', // make sure this file exists
      },
      output: {
        manualChunks(id) {
          if (id.includes('node_modules')) {
            return 'vendor';
          }
        },
      },
    },
  },
});
