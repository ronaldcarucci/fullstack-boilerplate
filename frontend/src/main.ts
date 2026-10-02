import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from './router'
import { pinia } from './stores.ts'
import { i18n } from './i18n.ts'

const app = createApp(App)

app
.use(pinia)
.use(router)
.use(i18n) 
.mount('#app')
