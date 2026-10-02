<script setup lang="ts">
    import { storeToRefs } from 'pinia';
    import { useAuthenticationStore } from '../stores/authenticationStore';
    import { useRouter } from 'vue-router';

    import { useI18n } from 'vue-i18n' 

    const { t } = useI18n();
    const router = useRouter();
    const authenticationStore = useAuthenticationStore();

    const { toRedirect } = storeToRefs(authenticationStore);
    const { updateAuthenficated } = authenticationStore;

    const submitClickHandler = () => {
        updateAuthenficated(true);
        router.push(toRedirect.value);
    }
</script>

<template>
    <div class="h-full flex items-center justify-center -mt-8">
        <form class="fieldset bg-base-100 border-base-300 rounded-box w-xs border-4 p-4" @submit.prevent="submitClickHandler">
            <fieldset class="fieldset">
                <label class="label">{{ t('pages.login.email') }}</label>
                <input type="email" class="input validator" :placeholder="t('pages.login.email')" required />
                <p class="validator-hint hidden">{{ t('pages.login.required') }}</p>
            </fieldset>

            <label class="fieldset">
                <span class="label">{{ t('pages.login.password') }}</span>
                <input type="password" class="input validator" :placeholder="t('pages.login.password')" required />
                <span class="validator-hint hidden">{{ t('pages.login.required') }}</span>
            </label>
            <div class="divider"></div>
            <button class="btn btn-neutral" type="submit">{{ t('pages.login.login') }}</button>
            <button class="btn btn-ghost mt-1" type="reset">{{ t('pages.login.reset') }}</button>
        </form>
    </div>
</template>