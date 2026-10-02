<script setup lang="ts">
    import { useRouter } from 'vue-router'
    import { LucideContact, LucideHouse } from '@lucide/vue';
    import { useNavigatorPointStore } from '../stores/navigatorPointStore';
    import { storeToRefs } from 'pinia';
    import { useI18n } from 'vue-i18n' 

    const { t } = useI18n();
    
    const router = useRouter();

    const navigatorPointStore = useNavigatorPointStore();

    const { currentPage } = storeToRefs(navigatorPointStore)

    const { updateCurrentPage } = navigatorPointStore;

    const updateRoute = (newRoute: string, newCurrentPage: string) => {
        router.push(newRoute);
        updateCurrentPage(newCurrentPage);
    }
</script>

<template>
    <div class="dock">
        <button 
            @click="() => { updateRoute('/', 'home'); }"
            :class="{ 'dock-active': currentPage === 'home' }"
        >
            <LucideHouse class="size-[1.2em]" />
            <span class="dock-label">{{ t('dockFooter.home') }}</span>
        </button>

        <button 
            @click="() => { updateRoute('/about', 'about'); }"
            :class="{ 'dock-active': currentPage === 'about' }"
        >
            <LucideContact class="size-[1.2em]" />
            <span class="dock-label">{{ t('dockFooter.contact') }}</span>
        </button>
    </div>
</template>
