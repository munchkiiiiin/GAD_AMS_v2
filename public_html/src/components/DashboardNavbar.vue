<template>
  <div class="navbar-wrapper">
    <!-- Sticky Top Navigation Bar -->
    <header class="sticky top-0 z-40 w-full bg-[#16162a]/95 backdrop-blur-xl border-b border-purple-900/40 shadow-xl text-white transition-all duration-300">
      <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20 gap-2 sm:gap-4">
          
          <!-- Left: Mobile Menu Toggle & Brand Logo -->
          <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
            <!-- Mobile Menu Hamburger Button (visible on screens < xl) -->
            <button 
              @click="isMobileDrawerOpen = true" 
              class="xl:hidden p-2 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500/50"
              aria-label="Open Navigation Menu"
            >
              <span class="material-symbols-outlined text-2xl">menu</span>
            </button>

            <!-- Brand Logo & Clean Typography -->
            <router-link :to="dashboardHomePath" class="flex items-center gap-2.5 sm:gap-3 group text-decoration-none flex-shrink-0">
              <img 
                src="/images/logo.png" 
                alt="BSU GAD Logo" 
                class="h-9 sm:h-11 w-auto object-contain transition-transform duration-300 group-hover:scale-105 flex-shrink-0" 
              />
              <div class="flex flex-col whitespace-nowrap min-w-0">
                <span class="text-[9px] sm:text-[10px] font-bold text-purple-400 uppercase tracking-widest leading-none">Benguet State University</span>
                <span class="text-base sm:text-xl font-black text-white tracking-tight leading-tight group-hover:text-purple-200 transition-colors">GAD-AMS</span>
                <span class="text-[8px] sm:text-[9px] uppercase tracking-widest text-slate-400 font-medium leading-none hidden 2xl:block">Gender & Development Office</span>
              </div>
            </router-link>
          </div>

          <!-- Center: Desktop Categorized Navigation with Auto-Hover Dropdowns (xl and above) -->
          <nav class="hidden xl:flex items-center gap-1 2xl:gap-1.5 flex-shrink-0">
            <template v-for="item in menuGroups" :key="item.label">
              
              <!-- Direct Single Link -->
              <router-link
                v-if="!item.children"
                :to="item.href"
                class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs 2xl:text-sm font-semibold transition-all duration-200 whitespace-nowrap"
                :class="isRouteActive(item.href) 
                  ? 'bg-gradient-to-r from-purple-700/40 to-pink-600/30 text-white shadow-sm border border-purple-500/40 font-bold' 
                  : 'text-slate-300 hover:text-white hover:bg-white/10'"
              >
                <span class="material-symbols-outlined text-lg text-purple-300">{{ item.icon }}</span>
                <span>{{ item.label }}</span>
                <span v-if="item.badge && item.badge > 0" class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ml-1 shadow">
                  {{ item.badge > 99 ? '99+' : item.badge }}
                </span>
              </router-link>

              <!-- Dropdown with Auto-Hover Support -->
              <div 
                v-else 
                class="relative dropdown-container" 
                :ref="el => registerDropdownRef(item.label, el)"
                @mouseenter="handleDropdownMouseEnter(item.label)"
                @mouseleave="handleDropdownMouseLeave(item.label)"
              >
                <button
                  @click.stop="toggleDesktopDropdown(item.label)"
                  class="flex items-center gap-1 px-3 py-2 rounded-xl text-xs 2xl:text-sm font-semibold transition-all duration-200 focus:outline-none whitespace-nowrap"
                  :class="isGroupActive(item) 
                    ? 'bg-gradient-to-r from-purple-700/40 to-pink-600/30 text-white border border-purple-500/40 font-bold' 
                    : (activeDropdown === item.label ? 'bg-white/15 text-white' : 'text-slate-300 hover:text-white hover:bg-white/10')"
                >
                  <span class="material-symbols-outlined text-lg text-purple-300">{{ item.icon }}</span>
                  <span class="whitespace-nowrap">{{ item.label }}</span>
                  <span v-if="getGroupBadgeTotal(item) > 0" class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ml-1 shadow">
                    {{ getGroupBadgeTotal(item) > 99 ? '99+' : getGroupBadgeTotal(item) }}
                  </span>
                  <span 
                    class="material-symbols-outlined text-base transition-transform duration-200 ml-0.5 text-purple-400" 
                    :class="{ 'rotate-180': activeDropdown === item.label }"
                  >
                    expand_more
                  </span>
                </button>

                <!-- Invisible bridge to prevent mouse leave gap -->
                <div v-if="activeDropdown === item.label" class="absolute top-full left-0 right-0 h-2"></div>

                <!-- Dropdown Flyout Panel -->
                <transition
                  enter-active-class="transition duration-200 ease-out"
                  enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                  enter-to-class="transform scale-100 opacity-100 translate-y-0"
                  leave-active-class="transition duration-150 ease-in"
                  leave-from-class="transform scale-100 opacity-100 translate-y-0"
                  leave-to-class="transform scale-95 opacity-0 -translate-y-2"
                >
                  <div
                    v-if="activeDropdown === item.label"
                    class="absolute top-[calc(100%+6px)] left-0 min-w-[240px] bg-[#121226]/98 backdrop-blur-2xl border border-purple-500/30 shadow-2xl rounded-2xl p-2 z-50 ring-1 ring-black/60"
                  >
                    <div class="px-3 py-1.5 text-[10px] uppercase font-bold tracking-wider text-purple-400 border-b border-white/10 mb-1 flex items-center justify-between">
                      <span>{{ item.label }}</span>
                      <span class="material-symbols-outlined text-xs text-purple-400">arrow_downward</span>
                    </div>
                    
                    <div class="flex flex-col gap-1">
                      <router-link
                        v-for="child in item.children"
                        :key="child.label"
                        :to="child.href"
                        @click="activeDropdown = null"
                        class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 group/item whitespace-nowrap"
                        :class="isRouteActive(child.href) 
                          ? 'bg-purple-600/30 text-white font-bold border border-purple-500/40' 
                          : 'text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-purple-600/20 hover:to-pink-600/15'"
                      >
                        <div class="flex items-center gap-2.5 min-w-0">
                          <span class="material-symbols-outlined text-base text-purple-400 group-hover/item:scale-110 transition-transform">{{ child.icon }}</span>
                          <span class="truncate">{{ child.label }}</span>
                        </div>
                        <span v-if="child.badge && child.badge > 0" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0 shadow">
                          {{ child.badge > 99 ? '99+' : child.badge }}
                        </span>
                      </router-link>
                    </div>
                  </div>
                </transition>
              </div>

            </template>
          </nav>

          <!-- Right: Notifications & User Profile -->
          <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">

            <!-- Notifications Dropdown Component -->
            <NotificationDropdown />

            <!-- User Profile Avatar & Dropdown -->
            <div 
              class="relative user-dropdown-container" 
              ref="userDropdownRef"
              @mouseenter="handleUserDropdownMouseEnter"
              @mouseleave="handleUserDropdownMouseLeave"
            >
              <button
                @click.stop="toggleUserDropdown"
                class="flex items-center gap-2 p-1 rounded-full hover:ring-2 hover:ring-purple-400/60 transition-all duration-200 focus:outline-none cursor-pointer"
                :aria-expanded="isUserDropdownOpen"
                aria-label="User Profile Menu"
              >
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-gradient-to-tr from-purple-700 via-purple-600 to-pink-500 flex items-center justify-center font-bold text-white text-sm shadow-md border border-purple-300/40 hover:scale-105 transition-transform">
                  {{ userInitial }}
                </div>
              </button>

              <!-- Invisible bridge for smooth hover to profile dropdown -->
              <div v-if="isUserDropdownOpen" class="absolute top-full right-0 w-32 h-2"></div>

              <!-- Profile Dropdown Popover -->
              <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                enter-to-class="transform scale-100 opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="transform scale-100 opacity-100 translate-y-0"
                leave-to-class="transform scale-95 opacity-0 -translate-y-2"
              >
                <div
                  v-if="isUserDropdownOpen"
                  class="absolute right-0 top-[calc(100%+6px)] min-w-[260px] bg-[#121226]/98 backdrop-blur-2xl border border-purple-500/30 shadow-2xl rounded-2xl p-2 z-50 ring-1 ring-black/60"
                >
                  <!-- User Header with Role Badge inside -->
                  <div class="p-3 border-b border-white/10 flex items-center gap-3 bg-purple-950/30 rounded-xl mb-1">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-purple-700 to-pink-500 flex items-center justify-center font-bold text-white text-base shadow flex-shrink-0 border border-purple-400/40">
                      {{ userInitial }}
                    </div>
                    <div class="flex flex-col min-w-0">
                      <span class="text-sm font-bold text-white truncate">{{ user.name || user.email || 'User' }}</span>
                      <span class="text-[11px] text-slate-400 truncate">{{ user.email || '' }}</span>
                      <div class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 bg-purple-900/60 border border-purple-500/30 rounded-full w-fit">
                        <span class="material-symbols-outlined text-purple-300 text-[11px]">shield_person</span>
                        <span class="text-[10px] text-purple-200 font-bold uppercase tracking-wider">{{ displayRole }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Actions List -->
                  <div class="py-1 flex flex-col gap-1">
                    <router-link
                      :to="settingsPath"
                      @click="isUserDropdownOpen = false"
                      class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-purple-600/20 transition-colors"
                    >
                      <span class="material-symbols-outlined text-lg text-purple-300">settings</span>
                      <span>Account Settings</span>
                    </router-link>

                    <router-link
                      :to="userManualPath"
                      @click="isUserDropdownOpen = false"
                      class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-purple-600/20 transition-colors"
                    >
                      <span class="material-symbols-outlined text-lg text-purple-300">help</span>
                      <span>User Manual & Guide</span>
                    </router-link>

                    <div class="my-1 border-t border-white/10"></div>

                    <button
                      @click="handleLogoutClick"
                      class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-red-400 hover:text-red-300 hover:bg-red-500/15 transition-colors w-full text-left cursor-pointer"
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

    <!-- Mobile Slide-Over Drawer Sheet (screens < xl) -->
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
        'fixed top-0 left-0 bottom-0 w-80 max-w-[85vw] bg-[#16162a] text-white z-50 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out xl:hidden border-r border-purple-900/40',
        isMobileDrawerOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <!-- Drawer Header -->
      <div class="p-4 border-b border-white/10 flex items-center justify-between bg-purple-950/30">
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
      <div class="p-3.5 mx-4 mt-4 bg-purple-950/40 border border-purple-500/20 rounded-2xl flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-purple-700 to-pink-500 flex items-center justify-center font-bold text-white text-base shadow flex-shrink-0 border border-purple-400/40">
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
              <span class="text-sm font-semibold">{{ item.label }}</span>
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
                <span class="text-sm font-semibold">{{ item.label }}</span>
                <span v-if="getGroupBadgeTotal(item) > 0" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full ml-1">
                  {{ getGroupBadgeTotal(item) > 99 ? '99+' : getGroupBadgeTotal(item) }}
                </span>
              </div>
              <span class="material-symbols-outlined text-lg transition-transform duration-200 text-slate-400" :class="{ 'rotate-180': mobileExpandedSections[item.label] }">
                expand_more
              </span>
            </button>

            <!-- Collapsible Child Links -->
            <div v-show="mobileExpandedSections[item.label]" class="pl-4 pr-1 py-1 flex flex-col gap-1 border-l-2 border-purple-500/30 ml-5 my-1">
              <router-link
                v-for="child in item.children"
                :key="'mob-child-' + child.label"
                :to="child.href"
                @click="isMobileDrawerOpen = false"
                class="flex items-center justify-between px-3 py-2 rounded-xl text-xs transition-all duration-200"
                :class="isRouteActive(child.href) ? 'bg-purple-600/30 text-white font-bold border border-purple-500/40' : 'text-slate-400 hover:bg-white/5 hover:text-white'"
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
      <div class="p-4 border-t border-white/10 flex flex-col gap-1 bg-[#121226]">
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
    <div class="md:hidden fixed bottom-0 left-0 right-0 z-30 bg-[#16162a]/98 backdrop-blur-lg border-t border-purple-900/40 px-2 py-1.5 flex items-center justify-around shadow-2xl">
      
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

let dropdownCloseTimeout = null;
let userDropdownCloseTimeout = null;

const registerDropdownRef = (key, el) => {
  if (el) dropdownRefs[key] = el;
};

// Auto Dropdown on Hover handlers
const handleDropdownMouseEnter = (label) => {
  if (dropdownCloseTimeout) {
    clearTimeout(dropdownCloseTimeout);
    dropdownCloseTimeout = null;
  }
  activeDropdown.value = label;
  isUserDropdownOpen.value = false;
};

const handleDropdownMouseLeave = (label) => {
  dropdownCloseTimeout = setTimeout(() => {
    if (activeDropdown.value === label) {
      activeDropdown.value = null;
    }
  }, 180);
};

const handleUserDropdownMouseEnter = () => {
  if (userDropdownCloseTimeout) {
    clearTimeout(userDropdownCloseTimeout);
    userDropdownCloseTimeout = null;
  }
  isUserDropdownOpen.value = true;
  activeDropdown.value = null;
};

const handleUserDropdownMouseLeave = () => {
  userDropdownCloseTimeout = setTimeout(() => {
    isUserDropdownOpen.value = false;
  }, 180);
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

// Dropdown Toggles for Touch devices
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
  if (dropdownCloseTimeout) clearTimeout(dropdownCloseTimeout);
  if (userDropdownCloseTimeout) clearTimeout(userDropdownCloseTimeout);
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
