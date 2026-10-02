<script setup lang="ts">
    import { LucideArrowLeft } from '@lucide/vue';
    import { useRouter } from 'vue-router';

    const router = useRouter();
</script>

<template>
    <div class="flex flex-col w-full items-center">
        <h1 class="text-9xl">
            <span class="text-primary">4</span>
            <span class="text-secondary">0</span>
            <span class="text-primary">4</span>
        </h1>
        <p class="my-8 text-2xl">That page is not found !</p>
        <button class="btn btn-neutral" @click="() => { router.back() }">
            <LucideArrowLeft :size=18 class="mr-2"/> Retour
      </button>
    </div>
</template>