<template>
  <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans" dir="rtl">
    
    <!-- Background glow effects -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
      <div class="absolute -top-40 right-1/4 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl"></div>
      <div class="absolute bottom-10 left-1/4 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl"></div>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10 text-center">
      <!-- Icon Logo -->
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 via-blue-600 to-cyan-400 p-0.5 shadow-2xl shadow-indigo-500/30 mb-4">
        <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center">
          <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
      </div>

      <h2 class="text-3xl font-extrabold text-white tracking-tight">نظام إدارة التذاكر والدعم الفني</h2>
      <p class="mt-2 text-sm text-slate-400">سجل الدخول بحسابك للمتابعة</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-lg relative z-10 px-4">
      <div class="bg-slate-900/90 border border-slate-800 backdrop-blur-xl py-8 px-6 sm:px-10 rounded-2xl shadow-2xl space-y-6">
        
        <!-- Standard Login Form -->
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">البريد الإلكتروني</label>
            <input
              type="email"
              v-model="form.email"
              required
              placeholder="example@support.local"
              class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
            />
            <div v-if="form.errors.email" class="text-xs text-rose-400 mt-1">{{ form.errors.email }}</div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">كلمة المرور</label>
            <input
              type="password"
              v-model="form.password"
              required
              placeholder="••••••••"
              class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
            />
            <div v-if="form.errors.password" class="text-xs text-rose-400 mt-1">{{ form.errors.password }}</div>
          </div>

          <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 cursor-pointer text-slate-400 hover:text-slate-300">
              <input
                type="checkbox"
                v-model="form.remember"
                class="rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-slate-900"
              />
              <span>تذكرني</span>
            </label>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all duration-200 disabled:opacity-50"
          >
            {{ form.processing ? 'جارٍ تسجيل الدخول...' : 'تسجيل الدخول' }}
          </button>
        </form>

        <section v-if="demoUsers.length" aria-labelledby="demo-accounts-title" class="border-t border-slate-800 pt-6">
          <h3 id="demo-accounts-title" class="text-sm font-semibold text-slate-100">الدخول السريع للتجربة</h3>
          <p class="mt-1 text-xs text-slate-400">اختر أحد الحسابات التجريبية أدناه.</p>

          <div class="mt-4 space-y-2">
            <button
              v-for="demoUser in demoUsers"
              :key="demoUser.id"
              type="button"
              :disabled="switchingUserId !== null"
              class="flex min-h-14 w-full items-center justify-between gap-3 rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-right transition-colors hover:border-indigo-500 hover:bg-slate-700 disabled:cursor-wait disabled:opacity-60"
              @click="quickLogin(demoUser.id)"
            >
              <span class="min-w-0">
                <span class="block truncate text-sm font-medium text-slate-100">{{ demoUser.name }}</span>
                <span class="block truncate text-xs text-slate-400">{{ demoUser.email }}</span>
              </span>
              <span class="shrink-0 text-xs text-slate-300">{{ demoUser.role?.name_ar || demoUser.role?.name }}</span>
            </button>
          </div>
        </section>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

defineProps({
  demoUsers: {
    type: Array,
    default: () => [],
  },
});

const switchingUserId = ref(null);

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'));
};

const quickLogin = (userId) => {
  switchingUserId.value = userId;
  router.post(route('quick-login', { user: userId }), {}, {
    onFinish: () => { switchingUserId.value = null; },
  });
};

</script>
