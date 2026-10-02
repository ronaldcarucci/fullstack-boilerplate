<script setup lang="ts">
    import { LucideGlobe } from '@lucide/vue';
    import { storeToRefs } from 'pinia';
    import { useLanguageStore } from '../stores/languageStore';
    import { useRouter } from 'vue-router';

  const languageStore = useLanguageStore();

  const { lang } = storeToRefs(languageStore);
  const { updateLang } = languageStore;
  const router = useRouter();

  
  const executeOnChangeLanguage = () => {
    updateLang(lang.value);
    router.go(0);
  }
</script>

<template>
  <label class="select w-fit">
    <span class="label"><LucideGlobe :size=18 /></span>
    <select v-model="lang" v-on:change="() => { 
        executeOnChangeLanguage();
    }">
        <option value="en">English</option>
        <option value="fr">Français</option>
    </select>
    </label>
</template>