import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { directives } from './directives'

// Global styles: the original project stylesheets, unchanged and in the same
// order.
//
// They MUST be imported here (globally) instead of inside a component with
// <style scoped>, because they contain rules scoped styles cannot reach:
//   :root { --color-primary: ... }        <- design tokens
//   * { box-sizing: border-box }          <- reset
//   body { ... }  body::before { ... }    <- base + ambient background orbs
//   html[data-theme="dark"] { ... }       <- dark mode tokens + ~60 overrides
import './assets/css/style.css'
import './assets/css/animations.css'
import './assets/css/responsive.css'

const app = createApp(App)

app.use(router)

// Registers v-reveal globally so every component can use it without importing.
Object.entries(directives).forEach(([name, directive]) => {
  app.directive(name, directive)
})

app.mount('#app')

