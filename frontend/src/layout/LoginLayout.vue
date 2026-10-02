<script setup lang="ts">
  import { RouterView } from 'vue-router'
  import NavBar from '../components/NavBar.vue';
</script>

<template>
  <NavBar />
  <div class="h-[calc(100vh-32px)] bg-base-200">
    <RouterView />
  </div>
</template>