<template>
  <div class="navbar-wrapper w-full max-w-full">
    <!-- Sticky Top Navigation Bar -->
    <header class="sticky top-0 z-40 w-full max-w-full bg-[#16162a]/95 backdrop-blur-xl border-b border-purple-900/40 shadow-xl text-white transition-all duration-300">
      <div class="w-full px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20 gap-1.5 sm:gap-4">
          
          <!-- Left: Mobile Menu Toggle & Brand Logo (Flexible & Non-overlapping) -->
          <div class="flex items-center justify-start gap-1.5 sm:gap-3 min-w-0 flex-1 xl:flex-none">
            <!-- Mobile Menu Hamburger Button (visible on screens < xl) -->
            <button 
              type="button"
              @click="isMobileDrawerOpen = true" 
              class="xl:hidden p-1.5 sm:p-2 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition-colors focus:outline-none flex-shrink-0"
              aria-label="Open Navigation Menu"
            >
              <span class="material-symbols-outlined text-2xl">menu</span>
            </button>

            <!-- Brand Logo & Clean Typography -->
            <router-link :to="dashboardHomePath" class="flex items-center gap-1.5 sm:gap-3 group text-decoration-none min-w-0">
              <img 
                src="/images/logo.png" 
                alt="BSU GAD Logo" 
                class="h-7 sm:h-10 w-auto object-contain transition-transform duration-300 group-hover:scale-105 flex-shrink-0"
                width="666"
                height="353"
                decoding="async"
                fetchpriority="high" 
              />
              <div class="flex flex-col min-w-0">
                <span class="text-[8px] sm:text-[10px] font-bold text-purple-400 uppercase tracking-wider sm:tracking-widest leading-none hidden sm:block truncate">Benguet State University</span>
                <span class="text-sm sm:text-xl font-black text-white tracking-tight leading-tight group-hover:text-purple-200 transition-colors truncate">GAD-AMS</span>
                <span class="text-[8px] sm:text-[9px] uppercase tracking-widest text-slate-400 font-medium leading-none hidden 2xl:block truncate">Gender & Development Office</span>
              </div>
            </router-link>
          </div>

          <!-- Center: Desktop Categorized Navigation with Auto-Hover Dropdowns (Centered) -->
          <nav class="hidden xl:flex items-center justify-center gap-1 2xl:gap-1.5 flex-1 mx-2">
            <template v-for="item in visibleMenuGroups" :key="item.label">
              
              <!-- Direct Single Link with Sleek Bottom Accent Line -->
              <router-link
                v-if="!item.children"
                :to="item.href"
                class="relative group flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs 2xl:text-sm font-semibold transition-all duration-200 whitespace-nowrap"
                :class="isRouteActive(item.href) 
                  ? 'text-white font-bold bg-white/10' 
                  : 'text-slate-300 hover:text-white hover:bg-white/5'"
              >
                <span class="material-symbols-outlined text-lg transition-colors duration-200" :class="isRouteActive(item.href) ? 'text-purple-300' : 'text-slate-400 group-hover:text-purple-300'">{{ item.icon }}</span>
                <span>{{ item.label }}</span>
                <span v-if="item.badge && item.badge > 0" class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ml-1 shadow">
                  {{ item.badge > 99 ? '99+' : item.badge }}
                </span>
                
                <!-- Sleek Bottom Glowing Accent Line -->
                <span 
                  class="absolute bottom-1 left-3 right-3 h-[2.5px] rounded-full bg-gradient-to-r from-purple-400 via-pink-500 to-purple-400 shadow-[0_0_10px_rgba(192,132,252,0.9)] transition-all duration-300 ease-out origin-center pointer-events-none"
                  :class="isRouteActive(item.href) ? 'opacity-100 scale-x-100' : 'opacity-0 scale-x-0 group-hover:opacity-100 group-hover:scale-x-100'"
                ></span>
              </router-link>

              <!-- Dropdown with Auto-Hover Support & Sleek Bottom Accent Line -->
              <div 
                v-else 
                class="relative dropdown-container group" 
                :ref="el => registerDropdownRef(item.label, el)"
                @mouseenter="handleDropdownMouseEnter(item.label)"
                @mouseleave="handleDropdownMouseLeave(item.label)"
              >
                <button
                  @click.stop="toggleDesktopDropdown(item.label)"
                  class="relative flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs 2xl:text-sm font-semibold transition-all duration-200 focus:outline-none whitespace-nowrap cursor-pointer"
                  :class="isGroupActive(item) 
                    ? 'text-white font-bold bg-white/10' 
                    : (activeDropdown === item.label ? 'text-white bg-white/10' : 'text-slate-300 hover:text-white hover:bg-white/5')"
                >
                  <span class="material-symbols-outlined text-lg transition-colors duration-200" :class="(isGroupActive(item) || activeDropdown === item.label) ? 'text-purple-300' : 'text-slate-400 group-hover:text-purple-300'">{{ item.icon }}</span>
                  <span class="whitespace-nowrap">{{ item.label }}</span>
                  <span v-if="getGroupBadgeTotal(item) > 0" class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ml-1 shadow">
                    {{ getGroupBadgeTotal(item) > 99 ? '99+' : getGroupBadgeTotal(item) }}
                  </span>

                  <!-- Sleek Bottom Glowing Accent Line -->
                  <span 
                    class="absolute bottom-1 left-3 right-3 h-[2.5px] rounded-full bg-gradient-to-r from-purple-400 via-pink-500 to-purple-400 shadow-[0_0_10px_rgba(192,132,252,0.9)] transition-all duration-300 ease-out origin-center pointer-events-none"
                    :class="(isGroupActive(item) || activeDropdown === item.label) ? 'opacity-100 scale-x-100' : 'opacity-0 scale-x-0 group-hover:opacity-100 group-hover:scale-x-100'"
                  ></span>
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
                    <div class="px-3 py-1.5 text-[10px] uppercase font-bold tracking-wider text-purple-300/80 border-b border-white/10 mb-1 flex items-center justify-between">
                      <span>{{ item.label }}</span>
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

          <!-- Right: Communications, Notifications & User Profile (Anchored to Right) -->
          <div class="flex items-center justify-end gap-1.5 sm:gap-3 flex-shrink-0">

            <!-- Communication / Messages Icon Button -->
            <router-link
              :to="messagesShortcutPath"
              class="action-btn"
              :class="{ 'active-action-btn': isMessagesActive }"
              title="Communications & Messages"
              aria-label="Communications"
              id="navbar-communications-btn"
            >
              <span class="material-symbols-outlined">forum</span>
              <span v-if="totalCommBadge > 0" class="ml-1 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ totalCommBadge > 99 ? '99+' : totalCommBadge }}</span>
            </router-link>

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
                      <span class="text-sm font-bold text-white truncate">{{ displayName }}</span>
                      <span class="text-[11px] text-slate-400 truncate">{{ displayEmail }}</span>
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

                    <button
                      v-if="canInstall"
                      @click="handleInstallApp"
                      class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-purple-300 hover:text-white hover:bg-purple-600/20 transition-colors w-full text-left cursor-pointer"
                    >
                      <span class="material-symbols-outlined text-lg">download_for_offline</span>
                      <span>Install GAD-AMS App</span>
                    </button>

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
        @touchmove.prevent
        @wheel.prevent
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 xl:hidden overscroll-none"
      ></div>
    </transition>

    <div
      :class="[
        'fixed top-0 left-0 bottom-0 w-80 max-w-[85vw] bg-[#16162a] text-white z-50 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out xl:hidden border-r border-purple-900/40 overscroll-contain',
        isMobileDrawerOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
      style="touch-action: pan-y; -webkit-overflow-scrolling: touch;"
    >
      <!-- Drawer Header -->
      <div class="p-4 border-b border-white/10 flex items-center justify-between bg-purple-950/30">
        <div class="flex items-center gap-3">
          <img src="/images/logo.png" alt="BSU GAD Logo" class="h-9 w-auto" width="666" height="353" loading="lazy" decoding="async" />
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
          <span class="text-xs font-bold text-white truncate">{{ displayName }}</span>
          <span class="text-[10px] text-purple-300 font-semibold uppercase tracking-wider">{{ displayRole }}</span>
        </div>
      </div>

      <!-- Mobile Nav Navigation List -->
      <nav class="flex-grow p-4 space-y-1.5 overflow-y-auto custom-scrollbar overscroll-contain" style="touch-action: pan-y; -webkit-overflow-scrolling: touch;">
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
        <button
          v-if="canInstall"
          @click="handleInstallApp"
          class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-purple-300 hover:text-white hover:bg-white/5 transition-colors w-full text-left cursor-pointer"
        >
          <span class="material-symbols-outlined text-lg">download_for_offline</span>
          <span>Install GAD-AMS App</span>
        </button>

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
        type="button"
        @click="isMobileDrawerOpen = true"
        class="flex flex-col items-center justify-center p-1 rounded-xl text-center min-w-[56px] text-slate-400 hover:text-white transition-colors bg-transparent border-0 cursor-pointer focus:outline-none"
        style="color: #94a3b8;"
      >
        <span class="material-symbols-outlined text-xl" style="color: inherit;">menu</span>
        <span class="text-[10px] mt-0.5" style="color: inherit;">Menu</span>
      </button>

    </div>
  </div>
</template>

<script setup>
import { computed, ref, reactive, watch, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import NotificationDropdown from './NotificationDropdown.vue';
import api from '../api';
import { usePwaInstall } from '../utils/usePwaInstall';

const props = defineProps({
  user: { type: Object, default: () => ({}) },
  roleLabel: { type: String, default: 'User' },
  menuGroups: { type: Array, required: true }
});

const emit = defineEmits(['logout']);

const route = useRoute();
const { canInstall, installApp } = usePwaInstall();

// State
const activeDropdown = ref(null);
const isUserDropdownOpen = ref(false);
const isMobileDrawerOpen = ref(false);
const mobileExpandedSections = reactive({});
const dropdownRefs = reactive({});
const userDropdownRef = ref(null);

const handleInstallApp = async () => {
  isUserDropdownOpen.value = false;
  isMobileDrawerOpen.value = false;
  await installApp();
};

// Body scroll lock management when mobile drawer is open
const lockBodyScroll = () => {
  if (typeof document !== 'undefined') {
    document.documentElement.style.overflow = 'hidden';
    document.body.style.overflow = 'hidden';
    document.body.style.touchAction = 'none';
  }
};

const unlockBodyScroll = () => {
  if (typeof document !== 'undefined') {
    document.documentElement.style.overflow = '';
    document.body.style.overflow = '';
    document.body.style.touchAction = '';
  }
};

watch(isMobileDrawerOpen, (isOpen) => {
  if (isOpen) {
    lockBodyScroll();
  } else {
    unlockBodyScroll();
  }
});

// Close drawer on route navigation and restore body scroll
watch(() => route.path, () => {
  if (isMobileDrawerOpen.value) {
    isMobileDrawerOpen.value = false;
    unlockBodyScroll();
  }
});

const unreadMessagesCount = ref(0);
const unreadInquiriesCount = ref(0);
let commUnreadInterval = null;

let dropdownCloseTimeout = null;
let userDropdownCloseTimeout = null;

const registerDropdownRef = (key, el) => {
  if (el) dropdownRefs[key] = el;
};

// Filter out Communications & Messages from center desktop menu bar
const visibleMenuGroups = computed(() => {
  return props.menuGroups.filter(item => item.label !== 'Communications' && item.label !== 'Messages');
});

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

// Local reactive user state that merges props.user, localStorage, and fresh profile data
const currentUser = ref({});

const syncLocalUser = () => {
  try {
    const local = JSON.parse(localStorage.getItem('user') || '{}');
    currentUser.value = { ...local, ...(props.user || {}) };
  } catch (e) {
    currentUser.value = { ...(props.user || {}) };
  }
};

watch(() => props.user, (newVal) => {
  if (newVal && typeof newVal === 'object') {
    currentUser.value = { ...currentUser.value, ...newVal };
  }
}, { deep: true, immediate: true });

const refreshUserProfile = async () => {
  syncLocalUser();
  const userId = currentUser.value?.id;
  if (!userId) return;

  try {
    const res = await api.get('/users/profile');
    if (res.data && res.data.success && res.data.user) {
      currentUser.value = { ...currentUser.value, ...res.data.user };
      const local = JSON.parse(localStorage.getItem('user') || '{}');
      localStorage.setItem('user', JSON.stringify({ ...local, ...res.data.user }));
    }
  } catch (err) {
    // Non-critical, fallback to props/localStorage
  }
};

// User Details & Initial
const displayName = computed(() => {
  const u = currentUser.value || {};
  if (u.full_name && typeof u.full_name === 'string' && u.full_name.trim()) {
    return u.full_name.trim();
  }
  const combined = [u.first_name, u.last_name].filter(Boolean).join(' ').trim();
  if (combined) return combined;
  if (u.name && typeof u.name === 'string' && u.name.trim()) {
    return u.name.trim();
  }
  if (u.username && typeof u.username === 'string' && u.username.trim()) {
    return u.username.trim();
  }
  if (u.email && typeof u.email === 'string' && u.email.trim()) {
    return u.email.trim();
  }
  return 'User';
});

const displayEmail = computed(() => {
  const u = currentUser.value || {};
  return u.email || '';
});

const userInitial = computed(() => {
  const name = displayName.value || 'U';
  return name.charAt(0).toUpperCase();
});

const displayRole = computed(() => {
  const u = currentUser.value || {};
  return u.user_role || u.profile_role || props.roleLabel || 'User';
});

// Paths
const currentRoleBase = computed(() => {
  const pathPart = route.path.split('/')[1] || '';
  if (['admin', 'staff', 'college'].includes(pathPart)) {
    return pathPart;
  }
  const u = currentUser.value || {};
  const role = (u.role || u.user_role || '').toLowerCase();
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

const isMessagesActive = computed(() => {
  return route.path.includes('/messages') || route.path.includes('/contact-inquiries');
});

const totalCommBadge = computed(() => {
  // Combine directly polled unread messages & inquiries
  const total = (unreadMessagesCount.value || 0) + (unreadInquiriesCount.value || 0);
  if (total > 0) return total;
  
  // Fallback to menu group badges if provided
  let fallback = 0;
  for (const group of props.menuGroups) {
    if (group.label === 'Communications' && group.children) {
      fallback += group.children.reduce((sum, c) => sum + (c.badge || 0), 0);
    } else if (group.label === 'Messages' && group.badge) {
      fallback += group.badge;
    }
  }
  return fallback;
});

const messagesBadgeCount = computed(() => totalCommBadge.value);

// Fetch unread messages and inquiries count
const fetchCommUnreadCounts = async () => {
  const userId = currentUser.value?.id || props.user?.id || JSON.parse(localStorage.getItem('user') || '{}')?.id;
  if (!userId) return;

  try {
    const msgRes = await api.get(`/messages/unread-count/${userId}`);
    if (msgRes.data && msgRes.data.success) {
      unreadMessagesCount.value = msgRes.data.count || 0;
    }

    const u = currentUser.value || {};
    const r = (u.role || u.user_role || '').toLowerCase();
    if (r.includes('admin') || r.includes('director') || r.includes('staff')) {
      const inqRes = await api.get('/contact-inquiries/unread-count');
      if (inqRes.data && inqRes.data.success) {
        unreadInquiriesCount.value = inqRes.data.count || 0;
      }
    }
  } catch (err) {
    // Silently ignore polling errors
  }
};

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
  refreshUserProfile();
  fetchCommUnreadCounts();
  commUnreadInterval = setInterval(fetchCommUnreadCounts, 15000);
});

onUnmounted(() => {
  unlockBodyScroll();
  document.removeEventListener('click', handleOutsideClick);
  if (dropdownCloseTimeout) clearTimeout(dropdownCloseTimeout);
  if (userDropdownCloseTimeout) clearTimeout(userDropdownCloseTimeout);
  if (commUnreadInterval) clearInterval(commUnreadInterval);
});
</script>

<style scoped>
.action-btn {
  padding: 0.5rem;
  background: rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(185, 121, 204, 0.15);
  border-radius: 9999px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
}

.action-btn:hover {
  background: rgba(0, 0, 0, 0.5);
  border-color: rgba(185, 121, 204, 0.3);
  transform: scale(0.95);
}

.action-btn.active-action-btn {
  background: rgba(147, 51, 234, 0.25);
  border-color: rgba(185, 121, 204, 0.4);
}

.action-btn .material-symbols-outlined {
  font-size: 1.25rem;
  color: #cbd5e1;
}

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
