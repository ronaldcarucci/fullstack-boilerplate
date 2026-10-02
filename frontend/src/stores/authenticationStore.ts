import { defineStore } from 'pinia'
import { ref } from 'vue'

const authenticatedLS = ref(localStorage.getItem('isAuthenticated') ?? '0');

export const useAuthenticationStore = defineStore('authentication', () => { 
    const authenticated = ref(authenticatedLS.value === '1');
    const toRedirect = ref('/');

    const updateAuthenficated = (newAuthenticated: boolean) => {
        authenticated.value = newAuthenticated;
        authenticatedLS.value = newAuthenticated ? '1' : '0'
        localStorage.setItem('isAuthenticated', authenticatedLS.value);
    }
    
    const updateToRedirect = (newToRedirect: string) => {
        toRedirect.value = newToRedirect;
    }

    return { authenticated, toRedirect, updateAuthenficated, updateToRedirect }
})