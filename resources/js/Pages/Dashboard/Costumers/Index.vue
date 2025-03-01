<script>

import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'


const currentPageTitle = ref('Calendar')
import { ref, reactive, onMounted } from 'vue'
import MainAdmin from '@/Components/layout/MainAdmin.vue';
import ComponentCard from "@/Components/common/ComponentCard.vue";
import BasicTableOne from "@/Components/tables/basic-tables/BasicTableOne.vue";

export default {
    components: {PageBreadcrumb, ComponentCard, BasicTableOne},
    layout: MainAdmin
}

</script>

<template>
    <PageBreadcrumb pageTitle="Costumers" />
    
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard title="Basic Table 1">
        <BasicTableOne />
      </ComponentCard>
    </div>
</template>