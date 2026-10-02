<script setup lang="ts">
  import { ref } from 'vue';

  const PossibleThemes = {
    abyss : 'abyss',
    acid : 'acid',
    aqua : 'aqua',
    automn : 'automn',
    black : 'black',
    bumblebee : 'bumblebee',
    business : 'business',
    caramellatte : 'caramellatte',
    cmyk : 'cmyk',
    coffee : 'coffee',
    corporate : 'corporate',
    cupcake : 'cupcake',
    cyberpunk : 'cyberpunk',
    dark : 'dark',
    dim : 'dim',
    dracula : 'dracula',
    emerald : 'emerald',
    fantasy : 'fantasy',
    forest : 'forest',
    garden : 'garden',
    halloween : 'halloween',
    lemonade : 'lemonade',
    luxury : 'luxury',
    light : 'light',
    lofi : 'lofi',
    night : 'night',
    nord : 'nord',
    pastel : 'pastel',
    retro : 'retro',
    silk : 'silk',
    sunset : 'sunset',
    synthwave : 'synthwave',
    valentine : 'valentine',
    wireframe : 'wireframe',
    winter : 'winter',
  } as const

  const theme = ref(PossibleThemes.corporate);
</script>

<template>
  <div :data-theme="theme">
    <component :is="$route.meta.layout || 'div'">
    </component>
  </div>
</template>
