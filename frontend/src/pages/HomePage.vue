<script setup lang="ts">
    import { useI18n } from 'vue-i18n' 

    const { t } = useI18n();
</script>

<template>
    <div class="h-full flex items-center justify-center">
        <h1> {{ t('pages.home.title') }} </h1>
    </div>
</template>
