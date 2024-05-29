import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from "path"

const isProd = process.env.NODE_ENV === 'production' ? true : false
const dir = isProd ? '../frontend/web/vue' : 'dist'
console.log('mode', process.env.NODE_ENV);

export default defineConfig({
  plugins: [
    vue(),
  ],
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `@import "@/assets/css/part-scss/vars.scss";`,
      }
    }
  },
  resolve: {
    alias: {
      '@/': `${path.resolve(__dirname, 'src')}/`
    }
  },
  build: {
    cssCodeSplit: false,
    rollupOptions: {
      output: {
        dir,
        entryFileNames: 'js/app.js',
        chunkFileNames: "js/chunk.js",
        assetFileNames: 'css/app.css',
        manualChunks: undefined,
      }
    }
  }
})
