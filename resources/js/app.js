import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { locale } from './i18n';

const appName = import.meta.env.VITE_APP_NAME || 'نظام دعم فني';

createInertiaApp({
    title: (title) => {
        const name = locale.value === 'en' ? 'Support system' : appName;
        return title ? `${title} - ${name}` : name;
    },
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4F46E5',
    },
});
