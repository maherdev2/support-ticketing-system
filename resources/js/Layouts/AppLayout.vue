<template>
  <div class="min-h-screen bg-slate-950 text-slate-100" dir="rtl">
    <header class="sticky top-0 z-30 border-b border-slate-800 bg-slate-900">
      <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6">
        <div class="flex min-w-0 items-center gap-3">
          <button
            type="button"
            class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-slate-200 hover:bg-slate-800 lg:hidden"
            aria-label="فتح القائمة الجانبية"
            :aria-expanded="menuOpen"
            aria-controls="mobile-sidebar"
            @click="menuOpen = true"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <Link :href="route('tickets.index')" class="flex min-w-0 items-center gap-3 font-semibold text-white">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-lg" aria-hidden="true">ت</span>
            <span class="truncate">نظام الدعم الفني</span>
          </Link>
        </div>

        <div class="flex items-center gap-3">
          <div class="min-w-0 text-left sm:text-right">
            <div class="max-w-32 truncate text-sm font-medium text-slate-100 sm:max-w-56">{{ currentUser?.name }}</div>
            <div class="text-xs text-slate-400">{{ currentUser?.role?.name_ar || currentUser?.role?.name }}</div>
          </div>
          <button type="button" class="rounded-lg px-3 py-2 text-sm text-slate-300 hover:bg-slate-800 hover:text-white" @click="logout">
            خروج
          </button>
        </div>
      </div>
    </header>

    <div class="flex min-h-[calc(100vh-4rem)]">
      <aside class="hidden w-60 shrink-0 border-l border-slate-800 bg-slate-900 lg:block" aria-label="القائمة الجانبية">
        <div class="sticky top-16 px-3 py-6">
          <SidebarNav />
        </div>
      </aside>

      <div v-if="menuOpen" class="fixed inset-0 z-50 lg:hidden" @keydown.esc="menuOpen = false">
        <button type="button" class="absolute inset-0 bg-black/70" aria-label="إغلاق القائمة الجانبية" @click="menuOpen = false" />
        <aside id="mobile-sidebar" class="relative h-full w-72 max-w-[85vw] border-l border-slate-800 bg-slate-900 p-3 shadow-2xl" aria-label="القائمة الجانبية">
          <div class="mb-5 flex items-center justify-between px-2 py-2">
            <span class="font-semibold">القائمة الرئيسية</span>
            <button type="button" class="rounded-lg px-3 py-2 text-sm text-slate-300 hover:bg-slate-800" @click="menuOpen = false">إغلاق</button>
          </div>
          <SidebarNav @navigate="menuOpen = false" />
        </aside>
      </div>

      <div class="min-w-0 flex-1">
        <div v-if="$page.props.flash?.success" role="status" class="mx-4 mt-4 rounded-lg border border-emerald-700 bg-emerald-950 px-4 py-3 text-sm text-emerald-200 sm:mx-6">
          {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash?.error" role="alert" class="mx-4 mt-4 rounded-lg border border-rose-700 bg-rose-950 px-4 py-3 text-sm text-rose-200 sm:mx-6">
          {{ $page.props.flash.error }}
        </div>
        <main class="min-w-0 py-6">
          <slot />
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import SidebarNav from './SidebarNav.vue';

const page = usePage();
const currentUser = computed(() => page.props.auth?.user);
const menuOpen = ref(false);

const logout = () => router.post(route('logout'));
</script>
