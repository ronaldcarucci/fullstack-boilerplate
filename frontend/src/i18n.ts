import en from "./locales/en.json" 
import fr from "./locales/fr.json" 
import { createI18n } from 'vue-i18n'
import { useLanguageStore } from './stores/languageStore';
import { storeToRefs } from "pinia";

const languageStore = useLanguageStore();

const { lang } = storeToRefs(languageStore);

export const i18n = createI18n({ 
  locale: lang.value, 
  fallbackLocale: "en", 
  messages: { en, fr }, 
  legacy: false 
})