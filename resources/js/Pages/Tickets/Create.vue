<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- Page Header -->
      <div class="flex items-center justify-between">
        <div>
          <Link
            :href="route('tickets.index')"
            class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-indigo-400 transition-colors mb-2"
          >
            <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span>العودة إلى قائمة التذاكر</span>
          </Link>
          <h1 class="text-2xl font-extrabold text-white tracking-tight">فتح تذكرة دعم فني جديدة</h1>
          <p class="text-sm text-slate-400 mt-0.5">يرجى تعبئة تفاصيل المشكلة بدقة لمساعدتنا على حلها في أسرع وقت</p>
        </div>
      </div>

      <!-- Form Card -->
      <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl">
        <form @submit.prevent="submit" class="space-y-6">
          
          <!-- Title -->
          <div>
            <label class="block text-sm font-semibold text-slate-200 mb-1.5">
              عنوان التذكرة / المشكلة <span class="text-rose-400">*</span>
            </label>
            <input
              type="text"
              v-model="form.title"
              required
              placeholder="مثال: تعذر الاتصال بالطابعة في الدور الثاني أو عطل بالبريد"
              class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
            />
            <div v-if="form.errors.title" class="text-xs text-rose-400 mt-1.5">{{ form.errors.title }}</div>
          </div>

          <!-- Priority & Category Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <!-- Priority -->
            <div>
              <label class="block text-sm font-semibold text-slate-200 mb-1.5">
                مستوى الأولوية <span class="text-rose-400">*</span>
              </label>
              <select
                v-model="form.priority"
                required
                class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                <option value="low">منخفضة (Low) - استفسارات عامة</option>
                <option value="medium">متوسطة (Medium) - مشكلة لا تعطل العمل كلياً</option>
                <option value="high">عالية (High) - عطل يؤثر على إنجاز العمل</option>
                <option value="urgent">عاجلة جداً (Urgent) - توقف العمل بالكامل</option>
              </select>
              <div v-if="form.errors.priority" class="text-xs text-rose-400 mt-1.5">{{ form.errors.priority }}</div>
            </div>

            <!-- Category -->
            <div>
              <label class="block text-sm font-semibold text-slate-200 mb-1.5">
                تصنيف المشكلة
              </label>
              <select
                v-model="form.category"
                class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                <option value="">اختر التصنيف (اختياري)</option>
                <option value="hardware">أجهزة ومعدات (Hardware)</option>
                <option value="software">برمجيات وأنظمة (Software)</option>
                <option value="network">شبكات وإنترنت (Network)</option>
                <option value="access">صلاحيات وحسابات (Access / Accounts)</option>
                <option value="email">البريد الإلكتروني والاتصالات (Email)</option>
                <option value="other">أخرى (Other)</option>
              </select>
              <div v-if="form.errors.category" class="text-xs text-rose-400 mt-1.5">{{ form.errors.category }}</div>
            </div>

          </div>

          <!-- Department & Equipment Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <!-- Department -->
            <div>
              <label class="block text-sm font-semibold text-slate-200 mb-1.5">
                القسم الموجه له التذكرة
              </label>
              <select
                v-model="form.department_id"
                class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                <option :value="null">القسم الافتراضي</option>
                <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                  {{ dept.name_ar || dept.name }}
                </option>
              </select>
              <div v-if="form.errors.department_id" class="text-xs text-rose-400 mt-1.5">{{ form.errors.department_id }}</div>
            </div>

            <!-- Equipment -->
            <div>
              <label class="block text-sm font-semibold text-slate-200 mb-1.5">
                الجهاز أو المعدة المعنية
              </label>
              <select
                v-model="form.equipment_id"
                class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                <option :value="null">لا يوجد جهاز محدد</option>
                <option v-for="eq in equipment" :key="eq.id" :value="eq.id">
                  {{ eq.brand }} {{ eq.model }} ({{ eq.serial_number }})
                </option>
              </select>
              <div v-if="form.errors.equipment_id" class="text-xs text-rose-400 mt-1.5">{{ form.errors.equipment_id }}</div>
            </div>

          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-semibold text-slate-200 mb-1.5">
              تفاصيل المشكلة والوصف <span class="text-rose-400">*</span>
            </label>
            <textarea
              v-model="form.description"
              required
              rows="6"
              placeholder="اكتب شرحاً مفصلاً للمشكلة والخطوات التي قمت بها ورسائل الخطأ إن وجدت..."
              class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm leading-relaxed"
            ></textarea>
            <div v-if="form.errors.description" class="text-xs text-rose-400 mt-1.5">{{ form.errors.description }}</div>
          </div>

          <!-- Attachments File Upload Area -->
          <div>
            <label class="block text-sm font-semibold text-slate-200 mb-1.5">
              إرفاق ملفات أو لقطات شاشة (اختياري)
            </label>
            
            <div
              @click="$refs.fileInput.click()"
              class="border-2 border-dashed border-slate-700 hover:border-indigo-500/60 bg-slate-800/40 hover:bg-slate-800/80 rounded-2xl p-6 text-center cursor-pointer transition-all duration-150"
            >
              <input
                type="file"
                ref="fileInput"
                @change="handleFileChange"
                multiple
                accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.log,.zip"
                class="hidden"
              />
              <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
              </div>
              <p class="text-sm font-medium text-slate-200">انقر هنا لاختيار الملفات أو الصور المرفقة</p>
              <p class="text-xs text-slate-400 mt-1">الأنواع المسموحة: الصور، PDF، المستندات، ملفات السجل (حتى 5 ملفات بحد أقصى 10MB لكل ملف)</p>
            </div>

            <!-- Selected Files List -->
            <div v-if="form.attachments.length > 0" class="mt-4 space-y-2">
              <div
                v-for="(file, idx) in form.attachments"
                :key="idx"
                class="flex items-center justify-between p-3 bg-slate-800 rounded-xl border border-slate-700/80"
              >
                <div class="flex items-center gap-3">
                  <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                  </svg>
                  <div>
                    <p class="text-xs font-medium text-slate-200">{{ file.name }}</p>
                    <p class="text-[10px] text-slate-400">{{ (file.size / (1024 * 1024)).toFixed(2) }} MB</p>
                  </div>
                </div>
                <button
                  type="button"
                  @click="removeFile(idx)"
                  class="p-1 text-slate-400 hover:text-rose-400 hover:bg-slate-700 rounded-lg transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
            
            <div v-if="form.errors.attachments" class="text-xs text-rose-400 mt-1.5">{{ form.errors.attachments }}</div>
          </div>

          <!-- Action Buttons -->
          <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
            <Link
              :href="route('tickets.index')"
              class="px-5 py-2.5 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-800 text-sm font-medium transition-colors"
            >
              إلغاء
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all duration-150 disabled:opacity-50"
            >
              {{ form.processing ? 'جارٍ إرسال التذكرة...' : 'إرسال التذكرة' }}
            </button>
          </div>

        </form>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';

defineProps({
  departments: {
    type: Array,
    default: () => [],
  },
  equipment: {
    type: Array,
    default: () => [],
  },
});

const form = useForm({
  title: '',
  priority: 'medium',
  category: '',
  department_id: null,
  equipment_id: null,
  description: '',
  attachments: [],
});

const handleFileChange = (e) => {
  const files = Array.from(e.target.files);
  if (form.attachments.length + files.length > 5) {
    alert('الحد الأقصى للمرفقات هو 5 ملفات');
    return;
  }
  form.attachments.push(...files);
  e.target.value = '';
};

const removeFile = (idx) => {
  form.attachments.splice(idx, 1);
};

const submit = () => {
  form.post(route('tickets.store'));
};
</script>
