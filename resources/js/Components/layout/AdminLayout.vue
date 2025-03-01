<template>
  <!-- <ThemeProvider>
    <SidebarProvider>
      <div class="min-h-screen xl:flex">
        <app-sidebar />
        <Backdrop />
        <div class="flex-1 transition-all duration-300 ease-in-out"
          :class="[isExpanded || isHovered ? 'lg:ml-[290px]' : 'lg:ml-[90px]']">
          <app-header />
          <div class="p-4 mx-auto max-w-screen-2xl md:p-6">
            <slot></slot>
          </div>
        </div>
      </div>
    </SidebarProvider>
  </ThemeProvider> -->


    <div class="min-h-screen xl:flex" id="adminlayout-usesidebar">
        <app-sidebar />
        <Backdrop />
        <div class="flex-1 transition-all duration-300 ease-in-out"
          :class="[isExpanded || isHovered ? 'lg:ml-[290px]' : 'lg:ml-[90px]']">
          <app-header />
          <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
            <slot></slot>
          </div>
        </div>
      </div>


</template>

<script setup>
import AppSidebar from './AppSidebar.vue'
import AppHeader from './AppHeader.vue'

import Backdrop from './Backdrop.vue'



import { useSidebar } from '@/Composables/useSidebar'
const { isExpanded, isHovered } = useSidebar();
</script>
