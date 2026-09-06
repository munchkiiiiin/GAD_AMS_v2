<template>
  <header class="fixed top-0 left-0 right-0 z-50 w-full max-w-full bg-[#16162a]/95 backdrop-blur-xl border-b border-purple-900/40 shadow-xl text-white transition-all duration-300">
    <div class="w-full px-3 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16 sm:h-20 gap-1.5 sm:gap-4">
        
        <!-- Left: Mobile Menu Toggle & Brand Logo -->
        <div class="flex items-center justify-start gap-1.5 sm:gap-3 min-w-0 flex-1 md:flex-none">
          <!-- Mobile Menu Hamburger Button (screens < md) -->
          <button 
            type="button"
            @click="isMobileDrawerOpen = true" 
            class="md:hidden p-1.5 sm:p-2 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition-colors focus:outline-none flex-shrink-0"
            aria-label="Open Navigation Menu"
          >
            <span class="material-symbols-outlined text-2xl">menu</span>
          </button>

          <!-- Brand Logo & Clean Typography -->
          <router-link to="/" class="flex items-center gap-1.5 sm:gap-3 group text-decoration-none min-w-0">
            <img 
              src="/images/logo.png" 
              alt="BSU GAD Logo" 
              class="h-7 sm:h-10 w-auto object-contain transition-transform duration-300 group-hover:scale-105 flex-shrink-0" 
            />
            <div class="flex flex-col min-w-0">
              <span class="text-[8px] sm:text-[10px] font-bold text-purple-400 uppercase tracking-wider sm:tracking-widest leading-none hidden sm:block truncate">Benguet State University</span>
              <span class="text-sm sm:text-xl font-black text-white tracking-tight leading-tight group-hover:text-purple-200 transition-colors truncate">GAD-AMS</span>
              <span class="text-[8px] sm:text-[9px] uppercase tracking-widest text-slate-400 font-medium leading-none hidden 2xl:block truncate">Gender & Development Office</span>
            </div>
          </router-link>
        </div>

        <!-- Center: Desktop Navigation with Sleek Glowing Bottom Line (Centered) -->
        <nav class="hidden md:flex items-center justify-center gap-1 lg:gap-2 flex-1 mx-2">
          <router-link
            v-for="item in navItems"
            :key="item.href"
            :to="item.href"
            class="relative group flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs lg:text-sm font-semibold transition-all duration-200 whitespace-nowrap"
            :class="isRouteActive(item.href) 
              ? 'text-white font-bold bg-white/10' 
              : 'text-slate-300 hover:text-white hover:bg-white/5'"
          >
            <span 
              class="material-symbols-outlined text-lg transition-colors duration-200" 
              :class="isRouteActive(item.href) ? 'text-purple-300' : 'text-slate-400 group-hover:text-purple-300'"
            >
              {{ item.icon }}
            </span>
            <span>{{ item.label }}</span>

            <!-- Sleek Bottom Glowing Accent Line (Option 4) -->
            <span 
              class="absolute bottom-1 left-3 right-3 h-[2.5px] rounded-full bg-gradient-to-r from-purple-400 via-pink-500 to-purple-400 shadow-[0_0_10px_rgba(192,132,252,0.9)] transition-all duration-300 ease-out origin-center pointer-events-none"
              :class="isRouteActive(item.href) ? 'opacity-100 scale-x-100' : 'opacity-0 scale-x-0 group-hover:opacity-100 group-hover:scale-x-100'"
            ></span>
          </router-link>
        </nav>

        <!-- Right: Actions (Sign Up / Portal Login) -->
        <div class="flex items-center justify-end gap-2 sm:gap-3 flex-shrink-0">
          <template v-if="$route.path === '/login'">
            <router-link 
              to="/register" 
              class="inline-flex items-center gap-1.5 px-3.5 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-purple-600 via-purple-500 to-pink-500 hover:from-purple-500 hover:to-pink-400 shadow-lg shadow-purple-900/40 border border-purple-400/30 transition-all duration-200 hover:scale-105 active:scale-95 whitespace-nowrap"
            >
              <span class="material-symbols-outlined text-base sm:text-lg">person_add</span>
              <span>Sign Up</span>
            </router-link>
          </template>
          <template v-else>
            <span class="hidden lg:block text-xs font-medium text-slate-400 whitespace-nowrap">Already have an account?</span>
            <router-link 
              to="/login" 
              class="inline-flex items-center gap-1.5 px-3.5 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-purple-600 via-purple-500 to-pink-500 hover:from-purple-500 hover:to-pink-400 shadow-lg shadow-purple-900/40 border border-purple-400/30 transition-all duration-200 hover:scale-105 active:scale-95 whitespace-nowrap"
            >
              <span class="material-symbols-outlined text-base sm:text-lg">login</span>
              <span>Portal Login</span>
            </router-link>
          </template>
        </div>

      </div>
    </div>

    <!-- Mobile Slide-Over Backdrop (screens < md) -->
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
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 md:hidden overscroll-none"
      ></div>
    </transition>

    <!-- Mobile Slide-Over Drawer Sheet (screens < md) -->
    <div
      :class="[
        'fixed top-0 left-0 bottom-0 w-80 max-w-[85vw] bg-[#16162a] text-white z-50 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out md:hidden border-r border-purple-900/40 overscroll-contain',
        isMobileDrawerOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
      style="touch-action: pan-y; -webkit-overflow-scrolling: touch;"
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
          type="button"
          @click="isMobileDrawerOpen = false"
          class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors"
          aria-label="Close Navigation Menu"
        >
          <span class="material-symbols-outlined text-xl">close</span>
        </button>
      </div>

      <!-- Drawer Nav Navigation List -->
      <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar">
        <router-link
          v-for="item in navItems"
          :key="item.href"
          :to="item.href"
          @click="isMobileDrawerOpen = false"
          class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-150 whitespace-nowrap"
          :class="isRouteActive(item.href)
            ? 'bg-gradient-to-r from-purple-700/40 to-pink-600/30 text-white font-bold border border-purple-500/40 shadow-sm'
            : 'text-slate-300 hover:text-white hover:bg-white/10'"
        >
          <span class="material-symbols-outlined text-xl text-purple-400">{{ item.icon }}</span>
          <span>{{ item.label }}</span>
        </router-link>
      </div>

      <!-- Drawer Bottom Actions -->
      <div class="p-4 border-t border-white/10 bg-purple-950/20">
        <router-link
          v-if="$route.path === '/login'"
          to="/register"
          @click="isMobileDrawerOpen = false"
          class="flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-purple-600 via-purple-500 to-pink-500 shadow-lg shadow-purple-900/40 border border-purple-400/30 transition-transform active:scale-95"
        >
          <span class="material-symbols-outlined text-lg">person_add</span>
          <span>Sign Up</span>
        </router-link>
        <router-link
          v-else
          to="/login"
          @click="isMobileDrawerOpen = false"
          class="flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-purple-600 via-purple-500 to-pink-500 shadow-lg shadow-purple-900/40 border border-purple-400/30 transition-transform active:scale-95"
        >
          <span class="material-symbols-outlined text-lg">login</span>
          <span>Portal Login</span>
        </router-link>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, watch, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const isMobileDrawerOpen = ref(false);

const navItems = [
  { href: '/', label: 'Home', icon: 'home' },
  { href: '/gad-corner', label: 'GAD Corner', icon: 'campaign' },
  { href: '/about', label: 'About Us', icon: 'info' },
  { href: '/contact', label: 'Contact', icon: 'support_agent' }
];

const isRouteActive = (href) => {
  if (href === '/') {
    return route.path === '/';
  }
  return route.path.startsWith(href);
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

// Close drawer on route change
watch(() => route.path, () => {
  if (isMobileDrawerOpen.value) {
    isMobileDrawerOpen.value = false;
    unlockBodyScroll();
  }
});

onUnmounted(() => {
  unlockBodyScroll();
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
