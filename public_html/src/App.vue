<template>
  <div id="app" class="min-h-screen flex flex-col w-full max-w-full overflow-x-clip">
    <Navbar v-if="!isDashboard" />
    <router-view class="flex-grow" />
    <Footer v-if="!isDashboard" />
  </div>
</template>

<script setup>
import { computed, onMounted, defineAsyncComponent } from 'vue';
import { useRoute } from 'vue-router';
import { useHolidays } from './utils/useHolidays';

const Navbar = defineAsyncComponent(() => import('./components/Navbar.vue'));
const Footer = defineAsyncComponent(() => import('./components/Footer.vue'));

const route = useRoute();
const { fetchHolidays } = useHolidays();

onMounted(() => {
  fetchHolidays();
});

const isDashboard = computed(() => {
  const path = route.path;
  return path.startsWith('/admin') || path.startsWith('/staff') || path.startsWith('/college');
});
</script>

<style>
/* Global styles can stay here or in style.css */
</style>
