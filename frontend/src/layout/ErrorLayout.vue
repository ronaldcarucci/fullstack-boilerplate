<script setup lang="ts">
  import { RouterView } from 'vue-router'
</script>

<template>
  <div class="h-[calc(100vh-0px)] bg-base-200">
    <div class="h-full flex items-center justify-center">
      <RouterView />
    </div>
  </div>
</template>