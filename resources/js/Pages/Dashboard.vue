<script>

import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'


const currentPageTitle = ref('Calendar')
import { ref, reactive, onMounted } from 'vue'
import MainAdmin from '@/Components/layout/MainAdmin.vue';

export default {
    components: {PageBreadcrumb},
    layout: MainAdmin
}

</script>

<template>
    <PageBreadcrumb pageTitle="Dashboard" />
    <div
      class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
    >Bem Vindos</div>
</template>

<!-- <script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Welcome />
                </div>
            </div>
        </div>
    </AppLayout>
</template> -->