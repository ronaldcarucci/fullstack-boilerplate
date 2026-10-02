<script setup lang="ts">
    import { useI18n } from 'vue-i18n' 

    const { t } = useI18n();
</script>

<template>
    <div class="h-full flex justify-center">
        <h1> {{ t('pages.about.title') }} </h1>
    </div>
</template>
