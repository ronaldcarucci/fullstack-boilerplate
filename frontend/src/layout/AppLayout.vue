<script setup lang="ts">
  import { RouterView } from 'vue-router'
  import NavBar from '../components/NavBar.vue';
  import DockFooter from '../components/DockFooter.vue';
</script>

<template>
  <NavBar />
  <div class="h-[calc(100vh-128px)] bg-base-200">
    <RouterView />
  </div>
  <DockFooter />
</template>