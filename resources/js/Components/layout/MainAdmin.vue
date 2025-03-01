<template>
    <ThemeProvider>
        <SidebarProvider>
            <AdminLayout>
                <slot></slot>
            </AdminLayout>
        </SidebarProvider>
    </ThemeProvider>

</template>
<script setup>
import ThemeProvider from './ThemeProvider.vue'
import SidebarProvider from './SidebarProvider.vue'
import AdminLayout from './AdminLayout.vue';

</script>
