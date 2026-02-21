import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { createPinia } from 'pinia';
import { createHead } from '@vueuse/head';
import { PerfectScrollbarPlugin } from 'vue3-perfect-scrollbar';
import Popper from 'vue3-popper';
import i18n from '@/i18n';
import appSetting from '@/app-setting';
import { ZiggyVue } from 'ziggy-js';
import NotificationsPlugin from '@/plugins/notifications';
import Alert from '@/components/Alert.vue';

// Main app CSS
import '@/assets/css/app.css';

createInertiaApp({
    resolve: (name) => {
        // Prefer the conventional Inertia Pages/ directory, but keep support for existing src/views/ pages
        const pagesFromPages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        const pagesFromViews = import.meta.glob('./src/views/**/*.vue', { eager: true });

        return (
            (pagesFromPages as Record<string, any>)[`./Pages/${name}.vue`]
        );
    },

    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });

        // Register Inertia plugin
        vueApp.use(plugin);

        // Register Ziggy
        vueApp.use(ZiggyVue);

        // Initialize Pinia
        const pinia = createPinia();
        vueApp.use(pinia);

        // Initialize app settings (loads from localStorage)
        appSetting.init();

        // Vue plugins
        vueApp.use(PerfectScrollbarPlugin);
        vueApp.use(createHead());
        vueApp.use(i18n);

        // Register Popper component
        vueApp.component('Popper', Popper);

        // Register global components
        vueApp.component('Alert', Alert);

        // Register notifications plugin (provides useNotifications() globally via inject)
        vueApp.use(NotificationsPlugin);

        vueApp.mount(el);
    },

    title: (title) => title ? `${title} ` : '',

    progress: {
        color: '#4361ee',
        showSpinner: false,
    },
});
