<template>
  <nav aria-label="التنقل الرئيسي">
    <p class="px-3 pb-3 text-xs font-medium text-slate-500">القائمة الرئيسية</p>
    <div class="space-y-1">
      <Link
        v-for="item in items"
        :key="item.name"
        :href="route(item.name)"
        :aria-current="item.active ? 'page' : undefined"
        :class="[
          'flex min-h-11 items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors',
          item.active ? 'bg-slate-800 font-semibold text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white',
        ]"
        @click="emit('navigate')"
      >
        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="item.icon" />
        </svg>
        <span>{{ item.label }}</span>
      </Link>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const emit = defineEmits(['navigate']);
const page = usePage();

const items = computed(() => {
  const url = page.url.split('?')[0];
  const links = [
    {
      name: 'tickets.index',
      label: 'التذاكر',
      icon: 'M4 5h16v14H4zM4 10h16M8 15h5',
      active: url === '/tickets' || (/^\/tickets\/[^/]+$/.test(url) && url !== '/tickets/create'),
    },
    {
      name: 'tickets.create',
      label: 'فتح تذكرة جديدة',
      icon: 'M12 5v14M5 12h14',
      active: url === '/tickets/create',
    },
  ];

  if (page.props.auth?.user?.role?.name === 'admin') {
    links.push({
      name: 'settings.index',
      label: 'إعدادات النظام',
      icon: 'M12 8a4 4 0 100 8 4 4 0 000-8zM4 4h16v16H4z',
      active: url.startsWith('/settings'),
    });
  }

  return links;
});
</script>
