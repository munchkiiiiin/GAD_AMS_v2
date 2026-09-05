<template>
  <div class="min-h-screen bg-slate-50 flex flex-col">
    <!-- Modern Top Navigation Header -->
    <DashboardNavbar
      :user="user"
      roleLabel="College / Unit"
      :menuGroups="collegeMenu"
      @logout="handleLogout"
    />

    <!-- Full-width Responsive Main Content Area -->
    <main 
      :class="[
        'flex-grow w-full transition-all duration-300 pb-20 md:pb-10',
        $route.path.includes('/plan-and-budget') ? 'p-0 max-w-full' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6'
      ]"
    >
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';
import DashboardNavbar from '../components/DashboardNavbar.vue';

const router = useRouter();
const user = ref({});

const collegeMenu = ref([
  { label: 'Dashboard', icon: 'dashboard', href: '/college/dashboard' },
  { label: 'New Submission', icon: 'add_circle', href: '/college/submit' },
  {
    label: 'Documents',
    icon: 'folder_open',
    children: [
      { label: 'Submitted List', icon: 'folder', href: '/college/submitted-list' },
      { label: 'Archives', icon: 'archive', href: '/college/archive' },
      { label: 'Document Trash Bin', icon: 'delete', href: '/college/trashbin' }
    ]
  },
  { label: 'Plan and Budget', icon: 'account_balance', href: '/college/plan-and-budget' },
  {
    label: 'Guides & Activity',
    icon: 'policy',
    children: [
      { label: 'Activity Logs', icon: 'history', href: '/college/activity-logs' },
      { label: 'User Manual', icon: 'help', href: '/college/user-manual' },
      { label: 'Data Privacy Policy', icon: 'privacy_tip', href: '/college/data-privacy-policy' }
    ]
  }
]);

const fetchUnreadCount = async () => {
  if (user.value?.id) {
    try {
      const res = await api.get(`/messages/unread-count/${user.value.id}`);
      if (res.data.success) {
        const msgItem = collegeMenu.value.find(m => m.label === 'Messages');
        if (msgItem) msgItem.badge = res.data.count;
      }
    } catch (err) {
      console.error('Failed to fetch unread count:', err);
    }
  }
};

let unreadInterval;

const handleLogout = async () => {
  try {
    await api.get('logout');
  } catch (err) {
    console.error('Logout failed:', err);
  } finally {
    localStorage.removeItem('user');
    localStorage.removeItem('authToken');
    router.push('/login');
  }
};

onMounted(() => {
  user.value = JSON.parse(localStorage.getItem('user') || '{}');
  
  const role = (user.value.role || user.value.user_role || '').toLowerCase();
  
  if (!user.value.id || !['twg', 'non-twg'].includes(role)) {
    router.push('/login');
  } else {
    fetchUnreadCount();
    unreadInterval = setInterval(fetchUnreadCount, 30000); // Check every 30 seconds
  }
});

onUnmounted(() => {
  if (unreadInterval) clearInterval(unreadInterval);
});
</script>

<style scoped>
/* Scoped layout styles */
</style>
