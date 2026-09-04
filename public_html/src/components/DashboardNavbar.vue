<template>
  <div>
    <!-- Sticky Top Navigation Bar -->
    <header class="sticky top-0 z-40 w-full bg-[#1a1a2e]/95 backdrop-blur-md border-b border-purple-900/30 shadow-md text-white transition-all duration-300">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20 gap-2">
          
          <!-- Left: Mobile Menu Toggle & Brand Logo -->
          <div class="flex items-center gap-3">
            <!-- Mobile Menu Button (below xl) -->
            <button 
              @click="isMobileDrawerOpen = true" 
              class="xl:hidden p-2 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition-colors focus:outline-none focus:ring-2 focus:ring-primary/50"
              aria-label="Open Navigation Menu"
            >
              <span class="material-symbols-outlined text-2xl">menu</span>
            </button>

            <!-- Brand Logo -->
            <router-link :to="dashboardHomePath" class="flex items-center gap-3 group text-decoration-none">
              <img src="/images/logo.png" alt="BSU GAD Logo" class="h-10 sm:h-12 w-auto object-contain transition-transform duration-300 group-hover:scale-105" />
              <div class="flex flex-col">
                <span class="text-[10px] font-bold text-purple-300 uppercase tracking-wider leading-none">Benguet State University</span>
                <span class="text-lg sm:text-xl font-extrabold text-white tracking-tight leading-tight group-hover:text-purple-200 transition-colors">GAD-AMS</span>
                <span class="text-[8px] sm:text-[9px] uppercase tracking-widest text-slate-400 font-medium leading-none hidden sm:block">Gender & Development</span>
              </div>
            </router-link>
          </div>

          <!-- Center: Desktop Categorized Navigation (xl and above) -->
          <nav class="hidden xl:flex items-center gap-1.5 2xl:gap-2">
            <template v-for="item in menuGroups" :key="item.label">
              
              <!-- Direct Link without Dropdown -->
              <router-link
                v-if="!item.children"
                :to="item.href"
                class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs 2xl:text-sm font-semibold transition-all duration-200"
                :class="isRouteActive(item.href) ? 'bg-primary/20 text-white shadow-sm border border-primary/30 font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5'"
              >
                <span class="material-symbols-outlined text-lg">{{ item.icon }}</span>
                <span>{{ item.label }}</span>
                <span v-if="item.badge && item.badge > 0" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full ml-0.5">
                  {{ item.badge > 99 ? '99+' : item.badge }}
                </span>
              </router-link>

              <!-- Dropdown Menu Item -->
              <div v-else class="relative dropdown-container" :ref="el => registerDropdownRef(item.label, el)">
                <button
                  @click.stop="toggleDesktopDropdown(item.label)"
                  class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs 2xl:text-sm font-semibold transition-all duration-200 focus:outline-none"
                  :class="isGroupActive(item) ? 'bg-primary/20 text-white border border-primary/30 font-bold' : 'text-slate-300 hover:text-white hover:bg-white/5'"
                >
                  <span class="material-symbols-outlined text-lg">{{ item.icon }}</span>
                  <span>{{ item.label }}</span>
                  <span v-if="getGroupBadgeTotal(item) > 0" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full ml-0.5">
                    {{ getGroupBadgeTotal(item) > 99 ? '99+' : getGroupBadgeTotal(item) }}
                  </span>
                  <span class="material-symbols-outlined text-base transition-transform duration-200 ml-0.5 opacity-70" :class="{ 'rotate-180': activeDropdown === item.label }">
                    expand_more
                  </span>
                </button>

                <!-- Dropdown Flyout Panel -->
                <transition
                  enter-active-class="transition duration-150 ease-out"
                  enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                  enter-to-class="transform scale-100 opacity-100 translate-y-0"
                  leave-active-class="transition duration-100 ease-in"
                  leave-from-class="transform scale-100 opacity-100 translate-y-0"
                  leave-to-class="transform scale-95 opacity-0 -translate-y-2"
                >
                  <div
                    v-if="activeDropdown === item.label"
                    class="absolute top-full left-0 mt-2 w-64 bg-[#16162a]/95 backdrop-blur-xl border border-purple-500/20 shadow-2xl rounded-2xl p-2 z-50 ring-1 ring-black/50"
                  >
                    <div class="px-3 py-1.5 text-[10px] uppercase font-bold tracking-wider text-purple-400 border-b border-white/5 mb-1">
                      {{ item.label }}
                    </div>
                    <div class="flex flex-col gap-1">
                      <router-link
                        v-for="child in item.children"
                        :key="child.label"
                        :to="child.href"
                        @click="activeDropdown = null"
                        class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150"
                        :class="isRouteActive(child.href) ? 'bg-primary/20 text-white font-bold' : 'text-slate-300 hover:text-white hover:bg-white/10'"
                      >
                        <div class="flex items-center gap-2.5 min-w-0">
                          <span class="material-symbols-outlined text-base text-purple-300">{{ child.icon }}</span>
                          <span class="truncate">{{ child.label }}</span>
                        </div>
                        <span v-if="child.badge && child.badge > 0" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0">
                          {{ child.badge > 99 ? '99+' : child.badge }}
                        </span>
                      </router-link>
                    </div>
                  </div>
                </transition>
              </div>

            </template>
          </nav>

          <!-- Right: Notifications, Role Badge & User Profile -->
          <div class="flex items-center gap-2 sm:gap-3">
            
            <!-- Role Pill Badge (hidden on extra small) -->
            <div v-if="displayRole" class="hidden sm:flex items-center gap-1.5 px-3 py-1 bg-purple-950/60 border border-purple-500/40 rounded-full shadow-inner backdrop-blur-md">
              <span class="material-symbols-outlined text-purple-300 text-sm">shield_person</span>
              <span class="text-white text-[11px] font-bold uppercase tracking-wider">{{ displayRole }}</span>
            </div>

            <!-- Notifications Dropdown Component -->
            <NotificationDropdown />

            <!-- User Profile Avatar & Dropdown -->
            <div class="relative user-dropdown-container" ref="userDropdownRef">
              <button
                @click.stop="toggleUserDropdown"
                class="flex items-center gap-2 p-1 rounded-full hover:ring-2 hover:ring-purple-400/50 transition-all duration-200 focus:outline-none"
                :aria-expanded="isUserDropdownOpen"
                aria-label="User Profile Menu"
              >
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-gradient-to-tr from-purple-700 to-pink-500 flex items-center justify-center font-bold text-white text-sm shadow-md border border-purple-400/40">
                  {{ userInitial }}
                </div>
              </button>

              <!-- Profile Dropdown Popover -->
              <transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                enter-to-class="transform scale-100 opacity-100 translate-y-0"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="transform scale-100 opacity-100 translate-y-0"
                leave-to-class="transform scale-95 opacity-0 -translate-y-2"
              >
                <div
                  v-if="isUserDropdownOpen"
                  class="absolute right-0 top-full mt-2 w-64 bg-[#16162a]/95 backdrop-blur-xl border border-purple-500/20 shadow-2xl rounded-2xl p-2 z-50 ring-1 ring-black/50"
                >
                  <!-- User Header -->
                  <div class="p-3 border-b border-white/10 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-purple-700 to-pink-500 flex items-center justify-center font-bold text-white text-base shadow flex-shrink-0">
                      {{ userInitial }}
                    </div>
                    <div class="flex flex-col min-w-0">
                      <span class="text-sm font-bold text-white truncate">{{ user.name || user.email || 'User' }}</span>
                      <span class="text-[11px] text-slate-400 truncate">{{ user.email || '' }}</span>
                      <span class="text-[10px] text-purple-300 font-semibold uppercase tracking-wider mt-0.5">{{ displayRole }}</span>
                    </div>
                  </div>

                  <!-- Actions List -->
                  <div class="py-1.5 flex flex-col gap-0.5">
                    <router-link
                      :to="settingsPath"
                      @click="isUserDropdownOpen = false"
                      class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/10 transition-colors"
                    >
                      <span class="material-symbols-outlined text-lg text-purple-300">settings</span>
                      <span>Account Settings</span>
                    </router-link>

                    <router-link
                      :to="userManualPath"
                      @click="isUserDropdownOpen = false"
                      class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/10 transition-colors"
                    >
                      <span class="material-symbols-outlined text-lg text-purple-300">help</span>
                      <span>User Manual & Guide</span>
                    </router-link>

                    <div class="my-1 border-t border-white/10"></div>

                    <button
                      @click="handleLogoutClick"
                      class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-colors w-full text-left"
                    >
                      <span class="material-symbols-outlined text-lg">logout</span>
                      <span>Sign Out</span>
                    </button>
                  </div>
                </div>
              </transition>
            </div>

          </div>

        </div>
      </div>
    </header>

    <!-- Mobile Slide-Over Drawer -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isMobileDrawerOpen"
        @click="isMobileDrawerOpen = false"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 xl:hidden"
      ></div>
    </transition>

    <div
      :class="[
        'fixed top-0 left-0 bottom-0 w-80 max-w-[85vw] bg-[#1a1a2e] text-white z-50 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out xl:hidden',
        isMobileDrawerOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <!-- Drawer Header -->
      <div class="p-5 border-b border-white/10 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <img src="/images/logo.png" alt="BSU GAD Logo" class="h-9 w-auto" />
          <div class="flex flex-col">
            <span class="text-[9px] font-bold text-purple-300 uppercase tracking-wider">BSU</span>
            <span class="text-base font-extrabold text-white tracking-tight">GAD-AMS</span>
          </div>
        </div>
        <button
          @click="isMobileDrawerOpen = false"
          class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors"
        >
          <span class="material-symbols-outlined text-xl">close</span>
        </button>
      </div>

      <!-- User Role Card inside Mobile Drawer -->
      <div class="p-4 mx-4 mt-4 bg-purple-950/40 border border-purple-500/20 rounded-2xl flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-purple-700 to-pink-500 flex items-center justify-center font-bold text-white text-base shadow flex-shrink-0">
          {{ userInitial }}
        </div>
        <div class="flex flex-col min-w-0">
          <span class="text-xs font-bold text-white truncate">{{ user.name || user.email || 'User' }}</span>
          <span class="text-[10px] text-purple-300 font-semibold uppercase tracking-wider">{{ displayRole }}</span>
        </div>
      </div>

      <!-- Mobile Nav Navigation List -->
      <nav class="flex-grow p-4 space-y-1.5 overflow-y-auto custom-scrollbar">
        <template v-for="item in menuGroups" :key="'mob-' + item.label">
          
          <!-- Single Link Item -->
          <router-link
            v-if="!item.children"
            :to="item.href"
            @click="isMobileDrawerOpen = false"
            class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all duration-200"
            :class="isRouteActive(item.href) ? 'bg-primary/20 text-white font-bold border border-primary/30' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
          >
            <div class="flex items-center gap-3">
              <span class="material-symbols-outlined text-xl text-purple-400">{{ item.icon }}</span>
              <span class="text-sm">{{ item.label }}</span>
            </div>
            <span v-if="item.badge && item.badge > 0" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
              {{ item.badge > 99 ? '99+' : item.badge }}
            </span>
          </router-link>

          <!-- Accordion Group Item -->
          <div v-else class="flex flex-col">
            <button
              @click="toggleMobileSection(item.label)"
              class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all duration-200 w-full text-left"
              :class="isGroupActive(item) ? 'bg-primary/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
            >
              <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-xl text-purple-400">{{ item.icon }}</span>
                <span class="text-sm">{{ item.label }}</span>
                <span v-if="getGroupBadgeTotal(item) > 0" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full ml-1">
                  {{ getGroupBadgeTotal(item) > 99 ? '99+' : getGroupBadgeTotal(item) }}
                </span>
              </div>
              <span class="material-symbols-outlined text-lg transition-transform duration-200 text-slate-400" :class="{ 'rotate-180': mobileExpandedSections[item.label] }">
                expand_more
              </span>
            </button>

            <!-- Collapsible Child Links -->
            <div v-show="mobileExpandedSections[item.label]" class="pl-4 pr-1 py-1 flex flex-col gap-1 border-l-2 border-purple-500/20 ml-5 my-1">
              <router-link
                v-for="child in item.children"
                :key="'mob-child-' + child.label"
                :to="child.href"
                @click="isMobileDrawerOpen = false"
                class="flex items-center justify-between px-3 py-2 rounded-xl text-xs transition-all duration-200"
                :class="isRouteActive(child.href) ? 'bg-primary/20 text-white font-bold' : 'text-slate-400 hover:bg-white/5 hover:text-white'"
              >
                <div class="flex items-center gap-2.5">
                  <span class="material-symbols-outlined text-base text-purple-300">{{ child.icon }}</span>
                  <span>{{ child.label }}</span>
                </div>
                <span v-if="child.badge && child.badge > 0" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                  {{ child.badge > 99 ? '99+' : child.badge }}
                </span>
              </router-link>
            </div>
          </div>

        </template>
      </nav>

      <!-- Drawer Footer -->
      <div class="p-4 border-t border-white/10 flex flex-col gap-1 bg-[#16162a]/50">
        <router-link
          :to="settingsPath"
          @click="isMobileDrawerOpen = false"
          class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-300 hover:text-white hover:bg-white/5 transition-colors"
          :class="isRouteActive(settingsPath) ? 'bg-primary/20 text-white' : ''"
        >
          <span class="material-symbols-outlined text-lg text-purple-300">settings</span>
          <span>Settings</span>
        </router-link>

        <button
          @click="handleLogoutClick"
          class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-colors w-full text-left"
        >
          <span class="material-symbols-outlined text-lg">logout</span>
          <span>Sign Out</span>
        </button>
      </div>
    </div>

    <!-- Mobile Quick Bottom Navigation Bar (Screens < md) -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 z-30 bg-[#16162a]/95 backdrop-blur-lg border-t border-purple-900/30 px-2 py-1.5 flex items-center justify-around shadow-2xl">
      
      <!-- 1. Home Dashboard -->
      <router-link
        :to="dashboardHomePath"
        class="flex flex-col items-center justify-center p-1 rounded-xl text-center min-w-[56px] transition-colors"
        :class="isRouteActive(dashboardHomePath) ? 'text-purple-300 font-bold' : 'text-slate-400 hover:text-white'"
      >
        <span class="material-symbols-outlined text-xl">dashboard</span>
        <span class="text-[10px] mt-0.5">Home</span>
      </router-link>

      <!-- 2. Documents / Submissions -->
      <router-link
        :to="documentsShortcutPath"
        class="flex flex-col items-center justify-center p-1 rounded-xl text-center min-w-[56px] transition-colors"
        :class="isRouteActive(documentsShortcutPath) ? 'text-purple-300 font-bold' : 'text-slate-400 hover:text-white'"
      >
        <span class="material-symbols-outlined text-xl">folder_open</span>
        <span class="text-[10px] mt-0.5">Docs</span>
      </router-link>

      <!-- 3. Messages -->
      <router-link
        :to="messagesShortcutPath"
        class="flex flex-col items-center justify-center p-1 rounded-xl text-center min-w-[56px] transition-colors relative"
        :class="isRouteActive(messagesShortcutPath) ? 'text-purple-300 font-bold' : 'text-slate-400 hover:text-white'"
      >
        <div class="relative flex items-center justify-center">
          <span class="material-symbols-outlined text-xl">forum</span>
          <span v-if="messagesBadgeCount > 0" class="absolute -top-1 -right-2 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.2 rounded-full">
            {{ messagesBadgeCount > 99 ? '99+' : messagesBadgeCount }}
          </span>
        </div>
        <span class="text-[10px] mt-0.5">Messages</span>
      </router-link>

      <!-- 4. Menu Drawer Trigger -->
      <button
        @click="isMobileDrawerOpen = true"
        class="flex flex-col items-center justify-center p-1 rounded-xl text-center min-w-[56px] text-slate-400 hover:text-white transition-colors"
      >
        <span class="material-symbols-outlined text-xl">menu</span>
        <span class="text-[10px] mt-0.5">Menu</span>
      </button>

    </div>
  </div>
</template>

<script setup>
import { computed, ref, reactive, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import NotificationDropdown from './NotificationDropdown.vue';

const props = defineProps({
  user: { type: Object, default: () => ({}) },
  roleLabel: { type: String, default: 'User' },
  menuGroups: { type: Array, required: true }
});

const emit = defineEmits(['logout']);

const route = useRoute();

// State
const activeDropdown = ref(null);
const isUserDropdownOpen = ref(false);
const isMobileDrawerOpen = ref(false);
const mobileExpandedSections = reactive({});
const dropdownRefs = reactive({});
const userDropdownRef = ref(null);

const registerDropdownRef = (key, el) => {
  if (el) dropdownRefs[key] = el;
};

// User Details & Initial
const userInitial = computed(() => {
  const name = props.user?.name || props.user?.email || 'U';
  return name.charAt(0).toUpperCase();
});

const displayRole = computed(() => {
  return props.user?.user_role || props.roleLabel || 'User';
});

// Paths
const currentRoleBase = computed(() => {
  const pathPart = route.path.split('/')[1] || '';
  if (['admin', 'staff', 'college'].includes(pathPart)) {
    return pathPart;
  }
  const role = (props.user?.role || props.user?.user_role || '').toLowerCase();
  if (role.includes('admin') || role.includes('director')) return 'admin';
  if (role.includes('staff')) return 'staff';
  return 'college';
});

const dashboardHomePath = computed(() => `/${currentRoleBase.value}/dashboard`);
const settingsPath = computed(() => `/${currentRoleBase.value}/settings`);
const userManualPath = computed(() => `/${currentRoleBase.value}/user-manual`);

const documentsShortcutPath = computed(() => {
  if (currentRoleBase.value === 'college') return '/college/submitted-list';
  if (currentRoleBase.value === 'staff') return '/staff/submitted-list';
  return '/admin/submitted-list';
});

const messagesShortcutPath = computed(() => `/${currentRoleBase.value}/messages`);

const messagesBadgeCount = computed(() => {
  for (const group of props.menuGroups) {
    if (group.label === 'Communications' && group.children) {
      const msgItem = group.children.find(c => c.label === 'Messages');
      if (msgItem && msgItem.badge) return msgItem.badge;
    }
    if (group.label === 'Messages' && group.badge) {
      return group.badge;
    }
  }
  return 0;
});

// Navigation helpers
const isRouteActive = (path) => {
  if (!path) return false;
  return route.path === path;
};

const isGroupActive = (group) => {
  if (!group.children) return false;
  return group.children.some(child => route.path === child.href);
};

const getGroupBadgeTotal = (group) => {
  if (!group.children) return group.badge || 0;
  return group.children.reduce((sum, child) => sum + (child.badge || 0), 0);
};

// Dropdown Toggles
const toggleDesktopDropdown = (label) => {
  activeDropdown.value = activeDropdown.value === label ? null : label;
  isUserDropdownOpen.value = false;
};

const toggleUserDropdown = () => {
  isUserDropdownOpen.value = !isUserDropdownOpen.value;
  activeDropdown.value = null;
};

const toggleMobileSection = (label) => {
  mobileExpandedSections[label] = !mobileExpandedSections[label];
};

const handleLogoutClick = () => {
  isUserDropdownOpen.value = false;
  isMobileDrawerOpen.value = false;
  emit('logout');
};

// Global click-outside handler
const handleOutsideClick = (e) => {
  // Check if click was outside active desktop dropdown
  if (activeDropdown.value) {
    const currentEl = dropdownRefs[activeDropdown.value];
    if (currentEl && !currentEl.contains(e.target)) {
      activeDropdown.value = null;
    }
  }

  // Check if click was outside user dropdown
  if (isUserDropdownOpen.value && userDropdownRef.value) {
    if (!userDropdownRef.value.contains(e.target)) {
      isUserDropdownOpen.value = false;
    }
  }
};

onMounted(() => {
  document.addEventListener('click', handleOutsideClick);
});

onUnmounted(() => {
  document.removeEventListener('click', handleOutsideClick);
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #b979cc;
  border-radius: 10px;
}
</style>
