import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createVuetify } from 'vuetify';
import { createPinia } from 'pinia';

// Vuetify
import 'vuetify/styles';
import '@mdi/font/css/materialdesignicons.css';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Create Vuetify instance with all components
const vuetify = createVuetify({
    components,
    directives,
    theme: {
        defaultTheme: 'light',
        themes: {
            light: {
                colors: {
                    primary: '#1976D2',
                    secondary: '#424242',
                    accent: '#82B1FF',
                    error: '#FF5252',
                    info: '#2196F3',
                    success: '#4CAF50',
                    warning: '#FFC107',
                },
            },
            dark: {
                colors: {
                    primary: '#2196F3',
                    secondary: '#424242',
                    accent: '#FF4081',
                    error: '#FF5252',
                    info: '#2196F3',
                    success: '#4CAF50',
                    warning: '#FFC107',
                },
            },
        },
    },
    icons: {
        defaultSet: 'mdi',
    },
    defaults: {
        VBtn: {
            style: 'text-transform: none;',
        },
        VCard: {
            elevation: 2,
        },
        VTextField: {
            variant: 'outlined',
        },
        VSelect: {
            variant: 'outlined',
        },
        VTextarea: {
            variant: 'outlined',
        },
    },
});

// Create Pinia instance
const pinia = createPinia();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(vuetify)
            .use(pinia)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// Configure Inertia.js to include CSRF token
import { router } from '@inertiajs/vue3';

// Get CSRF token from meta tag and configure it globally
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    // Set default headers for all Inertia requests using the 'before' hook
    router.on('before', (event) => {
        event.detail.visit.headers = {
            ...event.detail.visit.headers,
            'X-CSRF-TOKEN': token.content,
        };
    });
}

