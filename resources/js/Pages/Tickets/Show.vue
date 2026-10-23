<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- Top Breadcrumb & Actions Bar -->
      <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div class="flex items-center gap-4">
            <Link
              :href="route('tickets.index')"
              class="w-10 h-10 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 flex items-center justify-center text-slate-400 hover:text-white transition-colors shrink-0"
              title="العودة للتذاكر"
            >
              <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </Link>
            <div>
              <div class="flex items-center gap-3">
                <span class="font-mono text-xs font-bold text-indigo-400 bg-indigo-500/15 px-2.5 py-0.5 rounded-md border border-indigo-500/30">
                  {{ ticket.ticket_number }}
                </span>
                <span :class="['px-2.5 py-0.5 rounded-full text-xs font-semibold', priorityBadge.class]">
                  {{ priorityBadge.label }}
                </span>
                <span :class="['px-2.5 py-0.5 rounded-full text-xs font-semibold', statusBadge.class]">
                  {{ statusBadge.label }}
                </span>
              </div>
              <h1 class="text-xl sm:text-2xl font-black text-white mt-1.5">{{ ticket.title }}</h1>
            </div>
          </div>

          <!-- Quick Action Buttons for Status & Assignment -->
          <div class="flex flex-wrap items-center gap-2.5">
            <button
              v-if="canChangeStatus"
              @click="showStatusModal = true"
              class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white rounded-xl text-xs font-semibold border border-slate-700 transition-all flex items-center gap-1.5"
            >
              <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <span>تغيير الحالة</span>
            </button>

            <button
              v-if="canAssign"
              @click="showAssignModal = true"
              class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white rounded-xl text-xs font-semibold border border-slate-700 transition-all flex items-center gap-1.5"
            >
              <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <span>تعيين لموظف</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Main Layout Grid (Content + Sidebar) -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Ticket Details & Replies Thread (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
          
          <!-- Ticket Description Card -->
          <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
              <h2 class="text-base font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                وصف المشكلة
              </h2>
              <span class="text-xs text-slate-400">{{ formatDate(ticket.created_at) }}</span>
            </div>

            <div class="text-slate-300 text-sm leading-relaxed whitespace-pre-wrap" v-html="sanitizedDescription"></div>

            <!-- Attachments -->
            <div v-if="ticket.attachments && ticket.attachments.length > 0" class="pt-4 border-t border-slate-800">
              <h3 class="text-xs font-semibold text-slate-400 mb-3">الملفات والمستندات المرفقة ({{ ticket.attachments.length }})</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a
                  v-for="attachment in ticket.attachments"
                  :key="attachment.id"
                  :href="route('attachments.download', attachment.id)"
                  class="flex items-center gap-3 p-3 bg-slate-800/80 hover:bg-slate-800 rounded-xl border border-slate-700/80 hover:border-indigo-500/50 transition-all group"
                  target="_blank"
                >
                  <div class="w-9 h-9 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-200 truncate group-hover:text-indigo-400">{{ attachment.original_name }}</p>
                    <p class="text-[10px] text-slate-400">{{ (attachment.size / (1024 * 1024)).toFixed(2) }} MB</p>
                  </div>
                  <svg class="w-4 h-4 text-slate-500 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                  </svg>
                </a>
              </div>
            </div>
          </div>

          <!-- Replies Timeline -->
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <h2 class="text-base font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                سجل المتابعة والردود ({{ ticket.replies ? ticket.replies.length : 0 }})
              </h2>
            </div>

            <!-- Empty replies state -->
            <div v-if="!ticket.replies || ticket.replies.length === 0" class="bg-slate-900/60 border border-slate-800 rounded-2xl p-8 text-center text-slate-400 text-sm">
              لا توجد ردود أو تحديثات بعد على هذه التذكرة.
            </div>

            <!-- Reply Items -->
            <div
              v-for="reply in ticket.replies"
              :key="reply.id"
              :class="[
                'rounded-2xl p-5 border shadow-md transition-all space-y-3',
                reply.is_system
                  ? 'bg-blue-950/25 border-blue-500/30'
                  : reply.is_internal
                    ? 'bg-amber-950/25 border-amber-500/30'
                    : 'bg-slate-900/90 border-slate-800'
              ]"
            >
              <!-- Reply Header -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div
                    :class="[
                      'w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs text-white',
                      reply.is_system ? 'bg-blue-600' :
                      reply.is_internal ? 'bg-amber-600' : 'bg-indigo-600'
                    ]"
                  >
                    {{ reply.is_system ? '⚙️' : reply.user?.name?.charAt(0) || 'م' }}
                  </div>
                  <div>
                    <div class="flex items-center gap-2">
                      <span class="text-xs font-bold text-slate-200">
                        {{ reply.is_system ? 'إشعار من النظام' : reply.user?.name }}
                      </span>
                      <span v-if="reply.user?.role" class="text-[10px] px-2 py-0.2 rounded-full bg-slate-800 text-slate-400 border border-slate-700">
                        {{ reply.user.role.name_ar || reply.user.role.name }}
                      </span>
                    </div>
                    <span class="text-[11px] text-slate-500">{{ formatDate(reply.created_at) }}</span>
                  </div>
                </div>

                <!-- Badges -->
                <div class="flex items-center gap-2">
                  <span v-if="reply.is_internal" class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                    🔒 ملاحظة داخلية
                  </span>
                  <span v-if="reply.is_system" class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/40">
                    تحديث تلقائي
                  </span>
                </div>
              </div>

              <!-- Message Body -->
              <div
                :class="[
                  'text-sm leading-relaxed whitespace-pre-wrap',
                  reply.is_system ? 'text-blue-200' : reply.is_internal ? 'text-amber-100' : 'text-slate-300'
                ]"
                v-html="sanitizeHtml(reply.message)"
              ></div>

              <!-- Reply Attachments -->
              <div v-if="reply.attachments && reply.attachments.length > 0" class="pt-2 flex flex-wrap gap-2">
                <a
                  v-for="att in reply.attachments"
                  :key="att.id"
                  :href="route('attachments.download', att.id)"
                  class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg text-xs text-slate-300 hover:text-white border border-slate-700 transition-colors"
                  target="_blank"
                >
                  <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                  </svg>
                  <span>{{ att.original_name }}</span>
                </a>
              </div>
            </div>
          </div>

          <!-- Add Reply Form -->
          <div v-if="canReply" class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              إضافة رد جديد
            </h3>

            <form @submit.prevent="submitReply" class="space-y-4">
              <div>
                <textarea
                  v-model="replyForm.message"
                  required
                  rows="4"
                  placeholder="اكتب ردك أو الإجراء المتخذ هنا..."
                  class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm leading-relaxed"
                ></textarea>
              </div>

              <!-- Attachments Upload -->
              <div class="flex flex-wrap items-center gap-3">
                <input
                  type="file"
                  ref="replyFileInput"
                  @change="handleReplyFiles"
                  multiple
                  accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.log,.zip"
                  class="hidden"
                />
                <button
                  type="button"
                  @click="$refs.replyFileInput.click()"
                  class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-medium border border-slate-700 transition-colors flex items-center gap-1.5"
                >
                  <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                  </svg>
                  <span>إرفاق ملفات</span>
                </button>

                <!-- Internal Note Toggle (if authorized) -->
                <label v-if="canAddInternalNote" class="flex items-center gap-2 text-xs text-amber-300 bg-amber-950/40 border border-amber-500/30 px-3 py-2 rounded-xl cursor-pointer">
                  <input
                    type="checkbox"
                    v-model="replyForm.is_internal"
                    class="rounded bg-slate-800 border-amber-600 text-amber-500 focus:ring-amber-500 focus:ring-offset-slate-900"
                  />
                  <span>ملاحظة داخلية (مرئية لفريق الدعم فقط)</span>
                </label>
              </div>

              <!-- Selected reply files -->
              <div v-if="replyForm.attachments.length > 0" class="flex flex-wrap gap-2">
                <span
                  v-for="(f, i) in replyForm.attachments"
                  :key="i"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-800 text-slate-300 text-xs rounded-lg border border-slate-700"
                >
                  <span>{{ f.name }}</span>
                  <button type="button" @click="replyForm.attachments.splice(i, 1)" class="text-slate-500 hover:text-rose-400">×</button>
                </span>
              </div>

              <div class="flex justify-end pt-2">
                <button
                  type="submit"
                  :disabled="replyForm.processing"
                  class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all disabled:opacity-50"
                >
                  {{ replyForm.processing ? 'جارٍ الإرسال...' : 'إرسال الرد' }}
                </button>
              </div>
            </form>
          </div>

        </div>

        <!-- Right: Sidebar Info Card (1 Col) -->
        <div class="space-y-6">
          <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider border-b border-slate-800 pb-3">
              بيانات ومعلومات التذكرة
            </h3>

            <dl class="space-y-3.5 text-xs">
              
              <!-- Status -->
              <div>
                <dt class="text-slate-400 mb-1">الحالة الحالية</dt>
                <dd>
                  <span :class="['px-2.5 py-1 rounded-full font-semibold inline-block', statusBadge.class]">
                    {{ statusBadge.label }}
                  </span>
                </dd>
              </div>

              <!-- Priority -->
              <div>
                <dt class="text-slate-400 mb-1">الأولوية</dt>
                <dd>
                  <span :class="['px-2.5 py-1 rounded-full font-semibold inline-block', priorityBadge.class]">
                    {{ priorityBadge.label }}
                  </span>
                </dd>
              </div>

              <!-- Creator -->
              <div>
                <dt class="text-slate-400">مقدم الطلب</dt>
                <dd class="text-slate-200 font-semibold mt-0.5">{{ ticket.creator?.name || 'غير محدد' }}</dd>
                <dd class="text-slate-500 text-[11px]">{{ ticket.creator?.email }}</dd>
              </div>

              <!-- Assignee -->
              <div>
                <dt class="text-slate-400">المكلف بالحل (Support)</dt>
                <dd v-if="ticket.assigned_user" class="text-slate-200 font-semibold mt-0.5 flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                  {{ ticket.assigned_user.name }}
                </dd>
                <dd v-else class="text-slate-500 italic mt-0.5">لم يتم التعيين بعد</dd>
              </div>

              <!-- Department -->
              <div v-if="ticket.department">
                <dt class="text-slate-400">القسم المعني</dt>
                <dd class="text-slate-200 font-medium mt-0.5">{{ ticket.department.name_ar || ticket.department.name }}</dd>
              </div>

              <!-- Equipment -->
              <div v-if="ticket.equipment">
                <dt class="text-slate-400">الجهاز / المعدة</dt>
                <dd class="text-slate-200 font-medium mt-0.5">
                  {{ ticket.equipment.brand }} {{ ticket.equipment.model }}
                </dd>
                <dd class="text-slate-500 font-mono text-[11px]">الرقم التسلسلي: {{ ticket.equipment.serial_number }}</dd>
              </div>

              <!-- Category -->
              <div v-if="ticket.category">
                <dt class="text-slate-400">التصنيف</dt>
                <dd class="text-slate-200 font-medium mt-0.5">{{ ticket.category }}</dd>
              </div>

              <!-- Timestamps -->
              <div class="pt-3 border-t border-slate-800 space-y-2 text-[11px] text-slate-400">
                <div class="flex justify-between">
                  <span>تاريخ الإنشاء:</span>
                  <span class="text-slate-300">{{ formatDate(ticket.created_at) }}</span>
                </div>
                <div v-if="ticket.assigned_at" class="flex justify-between">
                  <span>تاريخ التعيين:</span>
                  <span class="text-slate-300">{{ formatDate(ticket.assigned_at) }}</span>
                </div>
                <div v-if="ticket.resolved_at" class="flex justify-between">
                  <span>تاريخ الحل:</span>
                  <span class="text-emerald-400 font-semibold">{{ formatDate(ticket.resolved_at) }}</span>
                </div>
              </div>

            </dl>
          </div>
        </div>

      </div>

    </div>

    <!-- Status Change Modal -->
    <div v-if="showStatusModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4">
        <h3 class="text-lg font-bold text-white">تغيير حالة التذكرة</h3>
        <p class="text-xs text-slate-400">حدد الحالة الجديدة للتذكرة لتحديث مسار العمل</p>

        <div>
          <select
            v-model="statusUpdateForm.status"
            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
          >
            <option value="open">مفتوحة (Open)</option>
            <option value="assigned">معينة (Assigned)</option>
            <option value="in_progress">قيد المعالجة (In Progress)</option>
            <option value="waiting_user">في انتظار المستخدم (Waiting User)</option>
            <option value="resolved">محلولة (Resolved)</option>
            <option value="closed">مغلقة (Closed)</option>
          </select>
        </div>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
          <button
            @click="showStatusModal = false"
            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-medium"
          >
            إلغاء
          </button>
          <button
            @click="updateStatus"
            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow"
          >
            حفظ التغيير
          </button>
        </div>
      </div>
    </div>

    <!-- Assign Modal -->
    <div v-if="showAssignModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4">
        <h3 class="text-lg font-bold text-white">تعيين التذكرة لموظف الدعم</h3>
        <p class="text-xs text-slate-400">اختر موظف الدعم الفني المسؤول عن حل هذه التذكرة</p>

        <div>
          <select
            v-model="assignUpdateForm.assigned_to"
            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
          >
            <option :value="null">إلغاء التعيين (غير معين)</option>
            <option v-for="u in supportUsers" :key="u.id" :value="u.id">
              {{ u.name }}
            </option>
          </select>
        </div>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
          <button
            @click="showAssignModal = false"
            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-medium"
          >
            إلغاء
          </button>
          <button
            @click="updateAssignee"
            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow"
          >
            حفظ التعيين
          </button>
        </div>
      </div>
    </div>

  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useForm, router, Link } from '@inertiajs/vue3';
import DOMPurify from 'dompurify';
import AppLayout from '../../Layouts/AppLayout.vue';

const props = defineProps({
  ticket: Object,
  departments: Array,
  supportUsers: Array,
  canUpdate: Boolean,
  canReply: Boolean,
  canAssign: Boolean,
  canChangeStatus: Boolean,
  canAddInternalNote: Boolean,
  canDelete: Boolean,
});

const showStatusModal = ref(false);
const showAssignModal = ref(false);
const replyFileInput = ref(null);

const replyForm = useForm({
  message: '',
  attachments: [],
  is_internal: false,
});

const statusUpdateForm = useForm({
  status: props.ticket.status,
});

const assignUpdateForm = useForm({
  assigned_to: props.ticket.assigned_to,
});

// Real-time listener
let echoChannel = null;
onMounted(() => {
  if (typeof window !== 'undefined' && window.Echo) {
    try {
      echoChannel = window.Echo.channel(`ticket.${props.ticket.id}`);
      echoChannel.listen('.ticket.updated', () => {
        router.reload({ only: ['ticket'] });
      });
    } catch (e) {
      // safe fallback
    }
  }
});

onUnmounted(() => {
  if (echoChannel && window.Echo) {
    window.Echo.leave(`ticket.${props.ticket.id}`);
  }
});

const sanitizeHtml = (html) => {
  if (!html) return '';
  return DOMPurify.sanitize(html, {
    ALLOWED_TAGS: ['br', 'p', 'ul', 'ol', 'li', 'strong', 'em', 'span', 'b', 'i'],
  });
};

const sanitizedDescription = computed(() => {
  return sanitizeHtml(props.ticket.description);
});

const priorityBadge = computed(() => {
  const map = {
    urgent: { label: 'عاجلة جداً', class: 'bg-rose-500/20 text-rose-300 border border-rose-500/40' },
    high: { label: 'عالية', class: 'bg-orange-500/20 text-orange-300 border border-orange-500/40' },
    medium: { label: 'متوسطة', class: 'bg-blue-500/20 text-blue-300 border border-blue-500/40' },
    low: { label: 'منخفضة', class: 'bg-slate-700 text-slate-300 border border-slate-600' },
  };
  return map[props.ticket.priority] || map.medium;
});

const statusBadge = computed(() => {
  const map = {
    open: { label: 'مفتوحة', class: 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' },
    assigned: { label: 'معينة', class: 'bg-blue-500/20 text-blue-300 border border-blue-500/40' },
    in_progress: { label: 'قيد المعالجة', class: 'bg-purple-500/20 text-purple-300 border border-purple-500/40' },
    waiting_user: { label: 'في انتظار رد المستخدم', class: 'bg-amber-500/20 text-amber-300 border border-amber-500/40' },
    resolved: { label: 'تم حلها', class: 'bg-teal-500/20 text-teal-300 border border-teal-500/40' },
    closed: { label: 'مغلقة', class: 'bg-slate-700 text-slate-400 border border-slate-600' },
  };
  return map[props.ticket.status] || map.open;
});

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleString('ar-SA', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const handleReplyFiles = (e) => {
  const files = Array.from(e.target.files);
  if (replyForm.attachments.length + files.length > 5) {
    alert('الحد الأقصى للمرفقات هو 5 ملفات');
    return;
  }
  replyForm.attachments.push(...files);
  e.target.value = '';
};

const submitReply = () => {
  replyForm.post(route('tickets.reply', props.ticket.id), {
    onSuccess: () => {
      replyForm.reset();
    },
  });
};

const updateStatus = () => {
  statusUpdateForm.put(route('tickets.update', props.ticket.id), {
    onSuccess: () => {
      showStatusModal.value = false;
    },
  });
};

const updateAssignee = () => {
  assignUpdateForm.put(route('tickets.update', props.ticket.id), {
    onSuccess: () => {
      showAssignModal.value = false;
    },
  });
};
</script>
