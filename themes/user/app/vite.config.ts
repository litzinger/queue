import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  build: {
    outDir: '../queue',
    rollupOptions: {
      output: {
        entryFileNames: 'assets/[name].js', // Fixed file name for entry points
        chunkFileNames: 'assets/[name].js', // Fixed file name for chunks
        assetFileNames: 'assets/[name][extname]', // Fixed file name for assets (e.g., CSS, images)
      },
    },
  }
});
