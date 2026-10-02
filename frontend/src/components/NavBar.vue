<script setup lang="ts">
  import { LucideLogOut } from '@lucide/vue';
  import { useNavigatorPointStore } from '../stores/navigatorPointStore';
  import { useRouter } from 'vue-router';
  import { useAuthenticationStore } from '../stores/authenticationStore';
  import { storeToRefs } from 'pinia';
  import LanguageSwitcher from './LanguageSwitcher.vue';

  const router = useRouter();

  const navigatorPointStore = useNavigatorPointStore();
  const authenticationStore = useAuthenticationStore();

  const { authenticated } = storeToRefs(authenticationStore);

  const { updateCurrentPage } = navigatorPointStore;
  const { updateAuthenficated, updateToRedirect } = authenticationStore;

  const updateRoute = (newRoute: string, newCurrentPage: string) => {
      updateAuthenficated(false);
      updateToRedirect(newRoute);
      router.push('/login');
      updateCurrentPage(newCurrentPage);
  }

  const logout = () => {
    updateRoute('/', 'home');
  }


</script>

<template>
  <nav class="navbar bg-base-100 shadow-sm">
    <div class="navbar-start">
      <LanguageSwitcher />
    </div>
    <div class="navbar-center">
      <span class="text-xl font-medium">daisyUI</span>
    </div>
    <div class="navbar-end">
      <button class="btn btn-ghost btn-circle" @click="logout" v-if="authenticated">
        <LucideLogOut />
      </button>
    </div>
  </nav>
</template>
