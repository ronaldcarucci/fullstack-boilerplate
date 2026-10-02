import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import path from 'path'

export default defineConfig({
  plugins: [vue(), tailwindcss()],
  base: '/app',
  build: {
    outDir: path.resolve(__dirname, '../backend/webroot/app'),
    emptyOutDir: true,
  }
})