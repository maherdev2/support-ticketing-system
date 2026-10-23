<template>
  <button
    type="button"
    class="rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-200 hover:border-slate-500 hover:bg-slate-800"
    :aria-label="locale === 'ar' ? 'Switch to English' : 'التحويل إلى العربية'"
    @click="setLocale(locale === 'ar' ? 'en' : 'ar')"
  >
    {{ locale === 'ar' ? 'English' : 'العربية' }}
  </button>
</template>

<script setup>
import { locale, setLocale } from '../i18n';
</script>
