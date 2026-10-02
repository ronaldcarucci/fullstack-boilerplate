import { defineStore } from 'pinia'
import { ref } from 'vue'

const languageLS = ref(localStorage.getItem('lang') ?? 'en');

export const useLanguageStore = defineStore('language', () => { 
    const lang = ref(languageLS.value);

    const updateLang = (newLang: string) => {
        lang.value = newLang;
        languageLS.value = newLang
        localStorage.setItem('lang', lang.value);
    }

    return { lang, updateLang }
})