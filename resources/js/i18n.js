import { computed, ref } from 'vue';

import translations from './translations/en.js';

const savedLocale = typeof window !== 'undefined'
  ? (document.cookie.match(/(?:^|; )locale=(ar|en)(?:;|$)/)?.[1] || window.localStorage.getItem('locale'))
  : null;

export const locale = ref(savedLocale === 'en' ? 'en' : 'ar');
export const direction = computed(() => locale.value === 'en' ? 'ltr' : 'rtl');

export function setLocale(value) {
  locale.value = value === 'en' ? 'en' : 'ar';
  document.documentElement.lang = locale.value;
  document.documentElement.dir = direction.value;
  document.title = locale.value === 'en' ? 'Support system' : 'نظام الدعم الفني';
  document.cookie = `locale=${locale.value}; path=/; SameSite=Lax; max-age=31536000`;
  window.localStorage.setItem('locale', locale.value);
}

export function t(text) {
  if (locale.value === 'ar' || !text) return text;
  return translations[text] || text;
}

export function localizedName(item) {
  if (!item) return '';
  if (locale.value === 'en') {
    const roleNames = { admin: 'Administrator', support: 'Support', user: 'User' };
    return roleNames[item.name] || item.name || item.name_ar;
  }
  return item.name_ar || item.name;
}
export function localizedCategory(value) {
  const categories = {
    hardware: ['أجهزة ومعدات', 'Hardware and equipment'],
    software: ['برمجيات وأنظمة', 'Software and systems'],
    network: ['شبكات وإنترنت', 'Network and internet'],
    access: ['صلاحيات وحسابات', 'Access and accounts'],
    email: ['البريد الإلكتروني والاتصالات', 'Email and communications'],
    other: ['أخرى', 'Other'],
  };
  return categories[value]?.[locale.value === 'en' ? 1 : 0] || value;
}

export function translateMessage(message) {
  if (locale.value === 'ar' || !message) return message;
  const created = message.match(/^تم إنشاء التذكرة بنجاح برقم: (.+)$/);
  if (created) return `Ticket created successfully: ${created[1]}`;
  const status = message.match(/^تم تغيير حالة التذكرة من \[(.+)\] إلى \[(.+)\]$/);
  if (status) return `Ticket status changed from [${t(status[1])}] to [${t(status[2])}]`;
  const assigned = message.match(/^تم تعيين التذكرة إلى: (.+)$/);
  if (assigned) return `Ticket assigned to: ${assigned[1]}`;
  const error = message.match(/^حدث خطأ أثناء (إنشاء التذكرة|تحديث التذكرة|إضافة الرد): (.+)$/);
  if (error) return `An error occurred while ${t(error[1])}: ${error[2]}`;
  return t(message);
}

if (typeof document !== 'undefined') {
  document.documentElement.lang = locale.value;
  document.documentElement.dir = direction.value;
  if (locale.value === 'en') document.title = 'Support system';
}
