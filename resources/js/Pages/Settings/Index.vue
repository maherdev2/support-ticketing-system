<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-violet-500/15 border border-violet-500/30 flex items-center justify-center">
              <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            {{ t('إعدادات النظام') }}
          </h1>
          <p class="text-sm text-slate-400 mt-1 ms-12">{{ t('إدارة المستخدمين والصلاحيات والأقسام') }}</p>
        </div>
        <button
          @click="openCreateModal"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white font-medium text-sm rounded-xl shadow-lg shadow-violet-500/20 transition-all duration-150 transform hover:-translate-y-0.5"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
          </svg>
          {{ t('إضافة موظف جديد') }}
        </button>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 text-center">
          <div class="text-3xl font-black text-white">{{ stats.total }}</div>
          <div class="text-xs text-slate-400 mt-1">{{ t('إجمالي الموظفين') }}</div>
        </div>
        <div class="bg-slate-900/80 border border-violet-500/20 rounded-2xl p-4 text-center">
          <div class="text-3xl font-black text-violet-400">{{ stats.admins }}</div>
          <div class="text-xs text-slate-400 mt-1">{{ t('مديرو النظام') }}</div>
        </div>
        <div class="bg-slate-900/80 border border-cyan-500/20 rounded-2xl p-4 text-center">
          <div class="text-3xl font-black text-cyan-400">{{ stats.support }}</div>
          <div class="text-xs text-slate-400 mt-1">{{ t('موظفو الدعم') }}</div>
        </div>
        <div class="bg-slate-900/80 border border-slate-700 rounded-2xl p-4 text-center">
          <div class="text-3xl font-black text-slate-300">{{ stats.users }}</div>
          <div class="text-xs text-slate-400 mt-1">{{ t('المستخدمون') }}</div>
        </div>
        <div class="bg-slate-900/80 border border-emerald-500/20 rounded-2xl p-4 text-center">
          <div class="text-3xl font-black text-emerald-400">{{ stats.active }}</div>
          <div class="text-xs text-slate-400 mt-1">{{ t('حسابات نشطة') }}</div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 shadow-lg">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
          <!-- Search -->
          <div class="md:col-span-5 relative">
            <input
              type="text"
              v-model="searchQuery"
              @keyup.enter="applyFilters"
              :placeholder="t('ابحث بالاسم، البريد الإلكتروني، أو رقم الموظف...')"
              class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 pr-10 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500"
            />
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>
          <!-- Role Filter -->
          <div class="md:col-span-3">
            <select v-model="selectedRole" @change="applyFilters" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-500">
              <option value="">{{ t('جميع الأدوار') }}</option>
              <option v-for="role in roles" :key="role.id" :value="role.id">{{ localizedName(role) }}</option>
            </select>
          </div>
          <!-- Department Filter -->
          <div class="md:col-span-3">
            <select v-model="selectedDept" @change="applyFilters" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-500">
              <option value="">{{ t('جميع الأقسام') }}</option>
              <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ localizedName(dept) }}</option>
            </select>
          </div>
          <!-- Reset -->
          <div class="md:col-span-1 flex items-center">
            <button @click="resetFilters" class="w-full h-10 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white border border-slate-700 text-xs transition-colors" :title="t('إعادة ضبط')">
              <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Users Table -->
      <div class="bg-slate-900/90 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-start border-collapse">
            <thead>
              <tr class="bg-slate-800/60 border-b border-slate-800 text-xs font-semibold text-slate-400">
                <th class="py-3.5 px-4">{{ t('الموظف') }}</th>
                <th class="py-3.5 px-4">{{ t('رقم الموظف') }}</th>
                <th class="py-3.5 px-4">{{ t('الدور / الصلاحية') }}</th>
                <th class="py-3.5 px-4">{{ t('القسم') }}</th>
                <th class="py-3.5 px-4">{{ t('الهاتف') }}</th>
                <th class="py-3.5 px-4 text-center">{{ t('الحالة') }}</th>
                <th class="py-3.5 px-4 text-center">{{ t('إجراءات') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 text-sm">
              <tr
                v-for="user in users.data"
                :key="user.id"
                class="hover:bg-slate-800/40 transition-colors group"
                :class="{ 'opacity-60': !user.is_active }"
              >
                <!-- Name & Email -->
                <td class="py-4 px-4">
                  <div class="flex items-center gap-3">
                    <div
                      class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm text-white shrink-0 shadow"
                      :class="getRoleColor(user.role?.name)"
                    >
                      {{ user.name?.charAt(0) || '?' }}
                    </div>
                    <div>
                      <div class="font-semibold text-slate-200 text-sm">{{ user.name }}</div>
                      <div class="text-xs text-slate-500">{{ user.email }}</div>
                    </div>
                  </div>
                </td>
                <!-- Employee ID -->
                <td class="py-4 px-4">
                  <span v-if="user.employee_id" class="font-mono text-xs text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20">
                    {{ user.employee_id }}
                  </span>
                  <span v-else class="text-xs text-slate-600 italic">—</span>
                </td>
                <!-- Role -->
                <td class="py-4 px-4">
                  <span :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold', getRoleBadge(user.role?.name)]">
                    {{ localizedName(user.role) || t('غير محدد') }}
                  </span>
                </td>
                <!-- Department -->
                <td class="py-4 px-4 text-xs text-slate-300">
                  {{ localizedName(user.department) || '—' }}
                </td>
                <!-- Phone -->
                <td class="py-4 px-4 text-xs text-slate-400 font-mono">
                  {{ user.phone || '—' }}
                </td>
                <!-- Status -->
                <td class="py-4 px-4 text-center">
                  <button
                    @click="toggleActive(user)"
                    :class="[
                      'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border transition-all',
                      user.is_active
                        ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 hover:bg-emerald-500/20'
                        : 'bg-rose-500/10 text-rose-400 border-rose-500/20 hover:bg-rose-500/20'
                    ]"
                    :title="user.is_active ? t('انقر لتعطيل الحساب') : t('انقر لتفعيل الحساب')"
                  >
                    <span :class="['w-1.5 h-1.5 rounded-full', user.is_active ? 'bg-emerald-400' : 'bg-rose-400']"></span>
                    {{ user.is_active ? t('نشط') : t('معطل') }}
                  </button>
                </td>
                <!-- Actions -->
                <td class="py-4 px-4">
                  <div class="flex items-center justify-center gap-2">
                    <button
                      @click="openEditModal(user)"
                      class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-indigo-600/20 text-slate-400 hover:text-indigo-400 border border-slate-700 hover:border-indigo-500/40 flex items-center justify-center transition-all"
                      :title="t('تعديل')"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button
                      @click="confirmDelete(user)"
                      class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-rose-600/20 text-slate-400 hover:text-rose-400 border border-slate-700 hover:border-rose-500/40 flex items-center justify-center transition-all"
                      :title="t('حذف')"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="!users.data || users.data.length === 0" class="py-16 text-center">
          <div class="w-16 h-16 rounded-2xl bg-slate-800 flex items-center justify-center mx-auto text-slate-500 mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-200">{{ t('لا يوجد موظفون مطابقون') }}</h3>
          <p class="text-sm text-slate-400 mt-1">{{ t('جرب تغيير معايير البحث') }}</p>
        </div>

        <!-- Pagination -->
        <div v-if="users.links && users.links.length > 3" class="bg-slate-800/40 border-t border-slate-800 px-4 py-3 flex items-center justify-between">
          <div class="text-xs text-slate-400">
            {{ t('عرض') }} {{ users.from || 0 }} {{ t('إلى') }} {{ users.to || 0 }} {{ t('من أصل') }} {{ users.total || 0 }} {{ t('موظف') }}
          </div>
          <div class="flex items-center gap-1">
            <Link
              v-for="(link, i) in users.links"
              :key="i"
              :href="link.url || '#'"
              :class="[
                'px-3 py-1.5 rounded-lg text-xs font-medium transition-colors',
                link.active ? 'bg-violet-600 text-white' : link.url ? 'text-slate-300 hover:bg-slate-800' : 'text-slate-600 pointer-events-none'
              ]"
              v-html="link.label"
            />
          </div>
        </div>
      </div>

    </div>

    <!-- ====== ADD / EDIT USER MODAL ====== -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="closeModal"></div>
        <div class="relative bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

          <!-- Modal Header -->
          <div class="flex items-center justify-between p-6 border-b border-slate-800">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
              <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="editingUser ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'" />
              </svg>
              {{ editingUser ? t('تعديل بيانات الموظف') : t('إضافة موظف جديد') }}
            </h2>
            <button @click="closeModal" class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Modal Body -->
          <form @submit.prevent="submitForm" class="p-6 space-y-4">

            <!-- Name + Employee ID -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5">{{ t('الاسم الكامل *') }}</label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  :placeholder="t('محمد أحمد العمري')"
                  class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500"
                />
                <p v-if="errors.name" class="text-rose-400 text-xs mt-1">{{ translateMessage(errors.name) }}</p>
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5">{{ t('رقم الموظف') }}</label>
                <input
                  v-model="form.employee_id"
                  type="text"
                  placeholder="EMP-001"
                  class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500 font-mono"
                />
                <p v-if="errors.employee_id" class="text-rose-400 text-xs mt-1">{{ translateMessage(errors.employee_id) }}</p>
              </div>
            </div>

            <!-- Email -->
            <div>
              <label class="block text-xs font-semibold text-slate-400 mb-1.5">{{ t('البريد الإلكتروني *') }}</label>
              <input
                v-model="form.email"
                type="email"
                required
                placeholder="user@company.com"
                class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500 ltr"
                dir="ltr"
              />
              <p v-if="errors.email" class="text-rose-400 text-xs mt-1">{{ translateMessage(errors.email) }}</p>
            </div>

            <!-- Password -->
            <div>
              <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                كلمة المرور {{ editingUser ? t('(اتركها فارغة إذا لم تريد تغييرها)') : '*' }}
              </label>
              <input
                v-model="form.password"
                type="password"
                :required="!editingUser"
                :placeholder="t('8 أحرف على الأقل')"
                class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500"
              />
              <p v-if="errors.password" class="text-rose-400 text-xs mt-1">{{ translateMessage(errors.password) }}</p>
            </div>

            <!-- Role + Department -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5">{{ t('الدور / الصلاحية *') }}</label>
                <select
                  v-model="form.role_id"
                  required
                  class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-500"
                >
                  <option value="">{{ t('-- اختر الدور --') }}</option>
                  <option v-for="role in roles" :key="role.id" :value="role.id">
                    {{ localizedName(role) }}
                  </option>
                </select>
                <p v-if="errors.role_id" class="text-rose-400 text-xs mt-1">{{ translateMessage(errors.role_id) }}</p>
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5">{{ t('القسم') }}</label>
                <select
                  v-model="form.department_id"
                  class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-500"
                >
                  <option :value="null">{{ t('-- بدون قسم --') }}</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                    {{ localizedName(dept) }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Phone -->
            <div>
              <label class="block text-xs font-semibold text-slate-400 mb-1.5">{{ t('رقم الهاتف') }}</label>
              <input
                v-model="form.phone"
                type="text"
                placeholder="05xxxxxxxx"
                dir="ltr"
                class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500 font-mono"
              />
            </div>

            <!-- Active Status -->
            <div class="flex items-center justify-between bg-slate-800/60 rounded-xl px-4 py-3 border border-slate-700">
              <div>
                <div class="text-sm font-semibold text-slate-200">{{ t('تفعيل الحساب') }}</div>
                <div class="text-xs text-slate-400">{{ t('المستخدم المعطل لا يمكنه تسجيل الدخول') }}</div>
              </div>
              <button
                type="button"
                @click="form.is_active = !form.is_active"
                :class="['relative w-11 h-6 rounded-full transition-colors duration-200', form.is_active ? 'bg-emerald-500' : 'bg-slate-600']"
              >
                <span :class="['absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-all duration-200', form.is_active ? 'right-0.5' : 'left-0.5']"></span>
              </button>
            </div>

            <!-- Roles info box -->
            <div class="bg-slate-800/40 rounded-xl p-3 border border-slate-700/50 text-xs text-slate-400 space-y-1">
              <p class="font-semibold text-slate-300 mb-1">{{ t('🔑 شرح الأدوار:') }}</p>
              <p>• <span class="text-violet-400 font-medium">{{ t('مدير النظام (Admin)') }}</span> {{ t('— وصول كامل، إدارة المستخدمين والتذاكر') }}</p>
              <p>• <span class="text-cyan-400 font-medium">{{ t('دعم فني (Support)') }}</span> {{ t('— يستقبل ويعالج التذاكر المعينة له') }}</p>
              <p>• <span class="text-slate-300 font-medium">{{ t('مستخدم (User)') }}</span> {{ t('— يفتح تذاكر ويتابع حالتها فقط') }}</p>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center gap-3 pt-2">
              <button
                type="submit"
                :disabled="submitting"
                class="flex-1 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white font-semibold text-sm rounded-xl transition-all disabled:opacity-60 flex items-center justify-center gap-2"
              >
                <svg v-if="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                {{ editingUser ? t('حفظ التعديلات') : t('إضافة الموظف') }}
              </button>
              <button type="button" @click="closeModal" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium text-sm rounded-xl border border-slate-700 transition-colors">
                {{ t('إلغاء') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- ====== DELETE CONFIRM MODAL ====== -->
    <Teleport to="body">
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="showDeleteModal = false"></div>
        <div class="relative bg-slate-900 border border-rose-500/30 rounded-2xl shadow-2xl w-full max-w-md p-6">
          <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center shrink-0">
              <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-white">{{ t('تأكيد الحذف') }}</h3>
              <p class="text-sm text-slate-400 mt-0.5">
                {{ t('هل أنت متأكد من حذف حساب') }} <span class="text-rose-400 font-semibold">{{ deletingUser?.name }}</span>{{ t('؟') }}
              </p>
            </div>
          </div>
          <p class="text-xs text-slate-500 bg-slate-800/60 rounded-xl px-4 py-3 border border-slate-700 mb-4">
            {{ t('⚠️ إذا كان للمستخدم تذاكر مرتبطة، سيتم تعطيل الحساب بدلاً من حذفه نهائياً.') }}
          </p>
          <div class="flex items-center gap-3">
            <button @click="deleteUser" :disabled="submitting" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-semibold text-sm rounded-xl transition-colors disabled:opacity-60">
              {{ t('نعم، احذف الحساب') }}
            </button>
            <button @click="showDeleteModal = false" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium text-sm rounded-xl border border-slate-700 transition-colors">
              {{ t('إلغاء') }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import { localizedName, t, translateMessage } from '../../i18n';

const props = defineProps({
  users: Object,
  roles: Array,
  departments: Array,
  filters: Object,
  stats: Object,
});

// Filters
const searchQuery = ref(props.filters?.search || '');
const selectedRole = ref(props.filters?.role_id || '');
const selectedDept = ref(props.filters?.department_id || '');

const applyFilters = () => {
  router.get(route('settings.index'), {
    search: searchQuery.value,
    role_id: selectedRole.value,
    department_id: selectedDept.value,
  }, { preserveState: true, preserveScroll: true });
};

const resetFilters = () => {
  searchQuery.value = '';
  selectedRole.value = '';
  selectedDept.value = '';
  applyFilters();
};

// Modal state
const showModal = ref(false);
const editingUser = ref(null);
const submitting = ref(false);
const errors = ref({});

const form = reactive({
  name: '',
  email: '',
  password: '',
  employee_id: '',
  role_id: '',
  department_id: null,
  phone: '',
  is_active: true,
});

const resetForm = () => {
  form.name = '';
  form.email = '';
  form.password = '';
  form.employee_id = '';
  form.role_id = '';
  form.department_id = null;
  form.phone = '';
  form.is_active = true;
  errors.value = {};
};

const openCreateModal = () => {
  editingUser.value = null;
  resetForm();
  showModal.value = true;
};

const openEditModal = (user) => {
  editingUser.value = user;
  form.name = user.name;
  form.email = user.email;
  form.password = '';
  form.employee_id = user.employee_id || '';
  form.role_id = user.role_id;
  form.department_id = user.department_id;
  form.phone = user.phone || '';
  form.is_active = user.is_active;
  errors.value = {};
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingUser.value = null;
};

const submitForm = () => {
  submitting.value = true;
  errors.value = {};

  const data = { ...form };
  if (!data.password) delete data.password;

  if (editingUser.value) {
    router.put(route('settings.users.update', editingUser.value.id), data, {
      onSuccess: () => { submitting.value = false; closeModal(); },
      onError: (e) => { submitting.value = false; errors.value = e; },
    });
  } else {
    router.post(route('settings.users.store'), data, {
      onSuccess: () => { submitting.value = false; closeModal(); },
      onError: (e) => { submitting.value = false; errors.value = e; },
    });
  }
};

// Delete
const showDeleteModal = ref(false);
const deletingUser = ref(null);

const confirmDelete = (user) => {
  deletingUser.value = user;
  showDeleteModal.value = true;
};

const deleteUser = () => {
  submitting.value = true;
  router.delete(route('settings.users.destroy', deletingUser.value.id), {
    onSuccess: () => {
      submitting.value = false;
      showDeleteModal.value = false;
      deletingUser.value = null;
    },
    onError: () => { submitting.value = false; },
  });
};

// Toggle active
const toggleActive = (user) => {
  router.patch(route('settings.users.toggle-active', user.id), {}, {
    preserveScroll: true,
  });
};

// Style helpers
const getRoleColor = (roleName) => {
  const map = {
    admin: 'bg-gradient-to-tr from-violet-600 to-purple-500',
    support: 'bg-gradient-to-tr from-cyan-600 to-blue-500',
    user: 'bg-gradient-to-tr from-slate-600 to-slate-500',
  };
  return map[roleName] || 'bg-slate-600';
};

const getRoleBadge = (roleName) => {
  const map = {
    admin: 'bg-violet-500/15 text-violet-400 border border-violet-500/30',
    support: 'bg-cyan-500/15 text-cyan-400 border border-cyan-500/30',
    user: 'bg-slate-700/60 text-slate-300 border border-slate-600',
  };
  return map[roleName] || 'bg-slate-700/60 text-slate-300 border border-slate-600';
};
</script>
