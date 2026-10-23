<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- Top header with actions -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-extrabold text-white tracking-tight">لوحة إدارة تذاكر الدعم الفني</h1>
          <p class="text-sm text-slate-400 mt-1">تتبع وإدارة جميع طلبات وبلاغات الدعم الفني وتحديث حالتها</p>
        </div>
        <div>
          <Link
            :href="route('tickets.create')"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-medium text-sm rounded-xl shadow-lg shadow-indigo-500/20 transition-all duration-150 transform hover:-translate-y-0.5"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>فتح تذكرة جديدة</span>
          </Link>
        </div>
      </div>

      <!-- KPI Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total -->
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 shadow-lg relative overflow-hidden group hover:border-slate-700 transition-all">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-semibold text-slate-400">إجمالي التذاكر</p>
              <h3 class="text-3xl font-black text-white mt-1">{{ stats?.total || 0 }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">سجل التذاكر المتاحة لحسابك</div>
        </div>

        <!-- Open -->
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 shadow-lg relative overflow-hidden group hover:border-emerald-500/30 transition-all">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-semibold text-emerald-400">تذاكر جديدة / مفتوحة</p>
              <h3 class="text-3xl font-black text-emerald-300 mt-1">{{ stats?.open || 0 }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">بانتظار المعالجة أو التعيين</div>
        </div>

        <!-- In Progress -->
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 shadow-lg relative overflow-hidden group hover:border-amber-500/30 transition-all">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-semibold text-amber-400">قيد المعالجة</p>
              <h3 class="text-3xl font-black text-amber-300 mt-1">{{ stats?.in_progress || 0 }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">جارٍ العمل عليها من قبل الفريق</div>
        </div>

        <!-- Resolved -->
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 shadow-lg relative overflow-hidden group hover:border-cyan-500/30 transition-all">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-semibold text-cyan-400">تم حلها / مغلقة</p>
              <h3 class="text-3xl font-black text-cyan-300 mt-1">{{ stats?.resolved || 0 }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center border border-cyan-500/20">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">تم الانتهاء منها وإغلاقها</div>
        </div>

      </div>

      <!-- Filters & Search Bar -->
      <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 shadow-lg space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
          
          <!-- Search Input -->
          <div class="md:col-span-6 relative">
            <input
              type="text"
              v-model="searchQuery"
              @keyup.enter="applyFilters"
              placeholder="ابحث برقم التذكرة، العنوان، أو الوصف..."
              class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 pr-10 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>

          <!-- Status Dropdown -->
          <div class="md:col-span-3">
            <select
              v-model="selectedStatus"
              @change="applyFilters"
              class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="all">جميع الحالات</option>
              <option value="open">مفتوحة (Open)</option>
              <option value="assigned">معينة (Assigned)</option>
              <option value="in_progress">قيد المعالجة (In Progress)</option>
              <option value="waiting_user">في انتظار المستخدم (Waiting User)</option>
              <option value="resolved">محلولة (Resolved)</option>
              <option value="closed">مغلقة (Closed)</option>
            </select>
          </div>

          <!-- Priority Dropdown -->
          <div class="md:col-span-3">
            <select
              v-model="selectedPriority"
              @change="applyFilters"
              class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="all">جميع الأولويات</option>
              <option value="urgent">عاجلة جداً (Urgent)</option>
              <option value="high">عالية (High)</option>
              <option value="medium">متوسطة (Medium)</option>
              <option value="low">منخفضة (Low)</option>
            </select>
          </div>

        </div>
      </div>

      <!-- Tickets List Table -->
      <div class="bg-slate-900/90 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        
        <div v-if="tickets.data && tickets.data.length > 0" class="overflow-x-auto">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="bg-slate-800/60 border-b border-slate-800 text-xs font-semibold text-slate-400">
                <th class="py-3.5 px-4">رقم التذكرة</th>
                <th class="py-3.5 px-4">العنوان والتفاصيل</th>
                <th class="py-3.5 px-4">الأولوية</th>
                <th class="py-3.5 px-4">الحالة</th>
                <th class="py-3.5 px-4">مقدم الطلب</th>
                <th class="py-3.5 px-4">المكلف بالحل</th>
                <th class="py-3.5 px-4">التاريخ</th>
                <th class="py-3.5 px-4 text-center">إجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 text-sm">
              <tr
                v-for="ticket in tickets.data"
                :key="ticket.id"
                class="hover:bg-slate-800/40 transition-colors group"
              >
                <!-- Number & Category -->
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-mono text-xs font-bold text-indigo-400 bg-indigo-500/10 px-2.5 py-1 rounded-md inline-block border border-indigo-500/20">
                    {{ ticket.ticket_number }}
                  </div>
                  <div v-if="ticket.category" class="text-[11px] text-slate-500 mt-1">
                    {{ ticket.category }}
                  </div>
                </td>

                <!-- Title & Description Snippet -->
                <td class="py-4 px-4 max-w-xs md:max-w-md">
                  <Link :href="route('tickets.show', ticket.id)" class="font-semibold text-slate-200 group-hover:text-indigo-400 transition-colors line-clamp-1">
                    {{ ticket.title }}
                  </Link>
                  <p class="text-xs text-slate-400 line-clamp-1 mt-0.5">
                    {{ ticket.description }}
                  </p>
                  <div class="flex items-center gap-3 mt-1 text-[11px] text-slate-500">
                    <span v-if="ticket.department">{{ ticket.department.name_ar || ticket.department.name }}</span>
                    <span v-if="ticket.equipment">• {{ ticket.equipment.brand }} {{ ticket.equipment.model }}</span>
                    <span v-if="ticket.replies_count > 0" class="flex items-center gap-1 text-slate-400">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                      </svg>
                      {{ ticket.replies_count }} ردود
                    </span>
                  </div>
                </td>

                <!-- Priority Badge -->
                <td class="py-4 px-4 whitespace-nowrap">
                  <span
                    :class="[
                      'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold',
                      getPriorityBadge(ticket.priority).class
                    ]"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :class="getPriorityBadge(ticket.priority).dot"></span>
                    {{ getPriorityBadge(ticket.priority).label }}
                  </span>
                </td>

                <!-- Status Badge -->
                <td class="py-4 px-4 whitespace-nowrap">
                  <span
                    :class="[
                      'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold',
                      getStatusBadge(ticket.status).class
                    ]"
                  >
                    {{ getStatusBadge(ticket.status).label }}
                  </span>
                </td>

                <!-- Creator -->
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-xs font-medium text-slate-200">{{ ticket.creator?.name || 'غير معروف' }}</div>
                  <div class="text-[11px] text-slate-500">{{ ticket.creator?.email }}</div>
                </td>

                <!-- Assigned To -->
                <td class="py-4 px-4 whitespace-nowrap">
                  <div v-if="ticket.assigned_user" class="text-xs font-medium text-slate-200">
                    {{ ticket.assigned_user.name }}
                  </div>
                  <div v-else class="text-xs text-slate-500 italic">
                    لم يُعين بعد
                  </div>
                </td>

                <!-- Date -->
                <td class="py-4 px-4 whitespace-nowrap text-xs text-slate-400">
                  {{ formatDate(ticket.created_at) }}
                </td>

                <!-- Actions -->
                <td class="py-4 px-4 whitespace-nowrap text-center">
                  <Link
                    :href="route('tickets.show', ticket.id)"
                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-indigo-600/30 text-indigo-400 hover:text-indigo-300 text-xs font-medium border border-slate-700/80 hover:border-indigo-500/40 transition-all"
                  >
                    <span>عرض</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-else class="py-16 text-center px-4">
          <div class="w-16 h-16 rounded-2xl bg-slate-800 flex items-center justify-center mx-auto text-slate-500 mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-200">لا توجد تذاكر مطابقة</h3>
          <p class="text-sm text-slate-400 mt-1 max-w-sm mx-auto">
            لم يتم العثور على أي تذاكر بالمعايير المحددة، يمكنك تجربة البحث بكلمات أخرى أو إنشاء تذكرة جديدة.
          </p>
          <div class="mt-6">
            <Link
              :href="route('tickets.create')"
              class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl shadow transition-colors"
            >
              فتح تذكرة جديدة الآن
            </Link>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="tickets.links && tickets.links.length > 3" class="bg-slate-800/40 border-t border-slate-800 px-4 py-3 flex items-center justify-between">
          <div class="text-xs text-slate-400">
            عرض {{ tickets.from || 0 }} إلى {{ tickets.to || 0 }} من أصل {{ tickets.total || 0 }} تذكرة
          </div>
          <div class="flex items-center gap-1">
            <Link
              v-for="(link, i) in tickets.links"
              :key="i"
              :href="link.url || '#'"
              :class="[
                'px-3 py-1.5 rounded-lg text-xs font-medium transition-colors',
                link.active
                  ? 'bg-indigo-600 text-white'
                  : link.url
                    ? 'text-slate-300 hover:bg-slate-800'
                    : 'text-slate-600 pointer-events-none'
              ]"
              v-html="link.label"
            />
          </div>
        </div>

      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';

const props = defineProps({
  tickets: Object,
  stats: Object,
  filters: Object,
});

const searchQuery = ref(props.filters?.search || '');
const selectedStatus = ref(props.filters?.status || 'all');
const selectedPriority = ref(props.filters?.priority || 'all');

const applyFilters = () => {
  router.get(
    route('tickets.index'),
    {
      search: searchQuery.value,
      status: selectedStatus.value,
      priority: selectedPriority.value,
    },
    {
      preserveState: true,
      preserveScroll: true,
    }
  );
};

const getPriorityBadge = (priority) => {
  const map = {
    urgent: {
      label: 'عاجلة جداً',
      class: 'bg-rose-500/15 text-rose-400 border border-rose-500/30',
      dot: 'bg-rose-400 animate-pulse',
    },
    high: {
      label: 'عالية',
      class: 'bg-orange-500/15 text-orange-400 border border-orange-500/30',
      dot: 'bg-orange-400',
    },
    medium: {
      label: 'متوسطة',
      class: 'bg-blue-500/15 text-blue-400 border border-blue-500/30',
      dot: 'bg-blue-400',
    },
    low: {
      label: 'منخفضة',
      class: 'bg-slate-500/15 text-slate-300 border border-slate-600/30',
      dot: 'bg-slate-400',
    },
  };
  return map[priority] || map.medium;
};

const getStatusBadge = (status) => {
  const map = {
    open: {
      label: 'مفتوحة',
      class: 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30',
    },
    assigned: {
      label: 'معينة',
      class: 'bg-blue-500/15 text-blue-400 border border-blue-500/30',
    },
    in_progress: {
      label: 'قيد المعالجة',
      class: 'bg-purple-500/15 text-purple-400 border border-purple-500/30',
    },
    waiting_user: {
      label: 'في انتظار المستخدم',
      class: 'bg-amber-500/15 text-amber-400 border border-amber-500/30',
    },
    resolved: {
      label: 'محلولة',
      class: 'bg-teal-500/15 text-teal-400 border border-teal-500/30',
    },
    closed: {
      label: 'مغلقة',
      class: 'bg-slate-700/60 text-slate-400 border border-slate-700',
    },
  };
  return map[status] || map.open;
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('ar-SA', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>
