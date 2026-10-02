import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useNavigatorPointStore = defineStore('navigator-point', () => {
    const currentPage = ref('home');

    const updateCurrentPage = (newCurrentPage: string) => {
        currentPage.value = newCurrentPage;
    }

    return { currentPage, updateCurrentPage }
})