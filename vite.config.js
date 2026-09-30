import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      // Lets you write `import X from '@/components/NavBar.vue'` instead of
      // long relative paths like `../../components/NavBar.vue`
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
})
