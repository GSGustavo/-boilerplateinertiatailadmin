import './bootstrap';

import 'primeicons/primeicons.css'

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia'
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import VueApexCharts from "vue3-apexcharts";

import Aura from '@primeuix/themes/aura';
import PrimeVue from 'primevue/config';
import { definePreset } from '@primeuix/themes';
import ToastService from 'primevue/toastservice';
import Tooltip from 'primevue/tooltip';
import ConfirmationService from 'primevue/confirmationservice';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const MyPreset = definePreset(Aura, {

    semantic: {
        focusRing: {
            width: '1px',
            style: 'solid',
            color: '{primary.color}',
            offset: '1px'
        },
        
        colorScheme: {
            light: {
                surface: {
                    0: '#ffffff',
                    50: '{zinc.50}',
                    100: '{zinc.100}',
                    200: '{zinc.200}',
                    300: '{zinc.300}',
                    400: '{zinc.400}',
                    500: '{zinc.500}',
                    600: '{zinc.600}',
                    700: '{zinc.700}',
                    800: '{zinc.800}',
                    900: '{zinc.900}',
                    950: '{zinc.950}'
                },
                primary: {
                    0: '#ffffff',
                    50: '#ecf3ff',
                    100: '#dde9ff',
                    200: '#c2d6ff',
                    300: '#9cb9ff',
                    400: '#7592ff',
                    500: '#465fff',
                    600: '#3641f5',
                    700: '#2a31d8',
                    800: '#252dae',
                    900: '#262e89',
                    950: '#161950',
                    
                    color: '#465fff',
                    inverseColor: '#ffffff',
                    hoverColor: '#262e89',
                    
                    activeColor: '#262e89',
                    border: '#465fff',
                },
                highlight: {
                    background: '#465fff',
                    focusBackground: '#465fff',
                    color: '#ffffff',
                    focusColor: '#ffffff',
                    border: '#465fff',
                },
                
            },
            dark: {
                surface: {
                    0: '#ffffff',
                    50: '{slate.50}',
                    100: '{slate.100}',
                    200: '{slate.200}',
                    300: '{slate.300}',
                    400: '{slate.400}',
                    500: '{slate.500}',
                    600: '{slate.600}',
                    700: '{slate.700}',
                    800: '{slate.800}',
                    900: '{slate.900}',
                    950: '{slate.950}'
                }
            }
        }
    }
})

createInertiaApp({
    title: (title) => `${title ? title + " - " : ""}${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })

            .component('VueDatePicker', VueDatePicker)
            .use(createPinia())
            .use(ZiggyVue)
            // .use(PrimeVue, { unstyled: true })
           
            .use(PrimeVue, {
                theme: {
                    preset: MyPreset,
                    options: {
                        darkModeSelector: '.dark',
                    }
                },
                zIndex: {
                    modal: 99998,        //dialog, drawer
                    overlay: 99999,      //select, popover
                    menu: 1000,         //overlay menus
                    tooltip: 1100,       //tooltip
                
                }
            })
            .use(ToastService)
            .use(ConfirmationService)
            .use(VueApexCharts)

            .use(plugin)
            .directive('tooltip', Tooltip)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
