<template>
  <div class="min-h-screen bg-slate-50 flex flex-col">
    <!-- Modern Top Navigation Header -->
    <DashboardNavbar
      :user="user"
      roleLabel="Administrator"
      :menuGroups="adminMenu"
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

const adminMenu = ref([
  { label: 'Dashboard', icon: 'dashboard', href: '/admin/dashboard' },
  {
    label: 'Documents',
    icon: 'folder_open',
    children: [
      { label: 'Submitted List', icon: 'folder', href: '/admin/submitted-list' },
      { label: 'Activity Design List', icon: 'description', href: '/admin/ad-list' },
      { label: 'Accomplishment Report List', icon: 'assignment', href: '/admin/ar-list' },
      { label: 'Archives', icon: 'archive', href: '/admin/archive' },
      { label: 'Document Trash Bin', icon: 'delete', href: '/admin/trash-bin' }
    ]
  },
  {
    label: 'Plan & Budget',
    icon: 'account_balance',
    children: [
      { label: 'Plan and Budget', icon: 'account_balance', href: '/admin/plan-and-budget' },
      { label: 'Report Monitoring', icon: 'bar_chart', href: '/admin/reports' },
      { label: 'Budget Monitoring', icon: 'account_balance_wallet', href: '/admin/budget' }
    ]
  },
  {
    label: 'Communications',
    icon: 'forum',
    children: [
      { label: 'Messages', icon: 'mail', href: '/admin/messages', badge: 0 },
      { label: 'Inquiries', icon: 'contact_mail', href: '/admin/contact-inquiries', badge: 0 }
    ]
  },
  {
    label: 'System & Controls',
    icon: 'admin_panel_settings',
    children: [
      { label: 'User Management', icon: 'manage_accounts', href: '/admin/user-management' },
      { label: 'Campus Resources', icon: 'business_center', href: '/admin/campus-resources' },
      { label: 'Activity Logs', icon: 'history', href: '/admin/activity-logs' },
      { label: 'User Manual', icon: 'help', href: '/admin/user-manual' },
      { label: 'Data Privacy Policy', icon: 'privacy_tip', href: '/admin/data-privacy-policy' }
    ]
  }
]);

const fetchUnreadCount = async () => {
  if (user.value?.id) {
    try {
      // Fetch Messages unread count
      const msgRes = await api.get(`/messages/unread-count/${user.value.id}`);
      const commItem = adminMenu.value.find(m => m.label === 'Communications');
      
      if (commItem) {
        const msgChild = commItem.children.find(c => c.label === 'Messages');
        if (msgChild && msgRes.data.success) {
          msgChild.badge = msgRes.data.count;
        }

        // Fetch Contact Inquiries unread count
        const inqRes = await api.get(`/contact-inquiries/unread-count`);
        const inqChild = commItem.children.find(c => c.label === 'Inquiries');
        if (inqChild && inqRes.data.success) {
          inqChild.badge = inqRes.data.count;
        }
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
  if (!user.value.id || user.value.role !== 'admin') {
    router.push('/login');
  } else {
    fetchUnreadCount();
    unreadInterval = setInterval(fetchUnreadCount, 10000); // Check every 10 seconds
  }
});

onUnmounted(() => {
  if (unreadInterval) clearInterval(unreadInterval);
});
</script>

<style scoped>
/* Scoped layout styles */
</style>
