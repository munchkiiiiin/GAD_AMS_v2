<template>
  <footer class="w-full bg-[#0d0d1b] border-t border-purple-900/40 text-slate-300 text-sm relative overflow-hidden transition-colors">
    <!-- Subtle top ambient glow -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-3/4 max-w-4xl h-px bg-gradient-to-r from-transparent via-purple-500/50 to-transparent"></div>
    <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-32 bg-purple-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Main Footer Content Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-12 mb-10">
        
        <!-- Column 1: Institutional Branding & Overview (lg:col-span-5) -->
        <div class="lg:col-span-5 flex flex-col space-y-4">
          <!-- Official GAD-AMS Logo & University Title -->
          <div class="flex items-center gap-3">
            <img 
              src="/images/logo.png" 
              alt="BSU GAD Logo" 
              class="h-10 sm:h-12 w-auto object-contain flex-shrink-0"
              loading="lazy"
            />
            <div class="flex flex-col">
              <span class="text-[10px] font-extrabold uppercase tracking-widest text-purple-400 leading-none">
                Benguet State University
              </span>
              <span class="text-base sm:text-lg font-black text-white tracking-tight leading-snug">
                Gender and Development Office
              </span>
              <span class="text-[11px] text-slate-400 font-medium leading-none">
                Activity Monitoring & Management System (GAD-AMS v2)
              </span>
            </div>
          </div>

          <!-- Mission / Mandate Snippet -->
          <p class="text-xs sm:text-sm text-slate-400 leading-relaxed max-w-md">
            Dedicated to gender-responsive governance, inclusive higher education, and the mainstreaming of gender equity and sensitivity initiatives across all BSU colleges, institutes, and satellite campuses.
          </p>

          <!-- System Status Badge -->
          <div class="pt-1 flex items-center gap-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/25 text-emerald-300 text-xs font-medium shadow-sm">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
              <span>GAD-AMS Portal Operational</span>
            </div>
            <span class="text-xs text-slate-500">•</span>
            <span class="text-xs text-slate-400 font-medium">Academic Year 2025–2026</span>
          </div>
        </div>

        <!-- Column 2: Quick Portal Links (lg:col-span-2) -->
        <div class="lg:col-span-2 flex flex-col space-y-3">
          <div class="flex items-center gap-2">
            <div class="w-1 h-3.5 rounded bg-purple-500"></div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-white">Portal Access</h4>
          </div>
          <ul class="space-y-2 text-xs sm:text-sm">
            <li>
              <router-link to="/" class="text-slate-400 hover:text-purple-300 transition-colors flex items-center gap-1.5">
                <span class="material-symbols-outlined text-xs text-purple-400/80">chevron_right</span>
                <span>Home Portal</span>
              </router-link>
            </li>
            <li>
              <router-link to="/gad-corner" class="text-slate-400 hover:text-purple-300 transition-colors flex items-center gap-1.5">
                <span class="material-symbols-outlined text-xs text-purple-400/80">chevron_right</span>
                <span>GAD Corner & News</span>
              </router-link>
            </li>
            <li>
              <router-link to="/contact" class="text-slate-400 hover:text-purple-300 transition-colors flex items-center gap-1.5">
                <span class="material-symbols-outlined text-xs text-purple-400/80">chevron_right</span>
                <span>Inquiries & Helpdesk</span>
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Column 3: Compliance & Legal (lg:col-span-2) -->
        <div class="lg:col-span-2 flex flex-col space-y-3">
          <div class="flex items-center gap-2">
            <div class="w-1 h-3.5 rounded bg-purple-500"></div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-white">Governance</h4>
          </div>
          <ul class="space-y-2 text-xs sm:text-sm">
            <li>
              <button 
                type="button" 
                @click="showPrivacyModal = true" 
                class="text-left text-slate-400 hover:text-purple-300 transition-colors flex items-center gap-1.5 cursor-pointer"
              >
                <span class="material-symbols-outlined text-xs text-purple-400/80">privacy_tip</span>
                <span>Privacy Policy</span>
              </button>
            </li>
            <li>
              <button 
                type="button" 
                @click="showTermsModal = true" 
                class="text-left text-slate-400 hover:text-purple-300 transition-colors flex items-center gap-1.5 cursor-pointer"
              >
                <span class="material-symbols-outlined text-xs text-purple-400/80">gavel</span>
                <span>Terms of Service</span>
              </button>
            </li>
            <li>
              <a 
                href="https://privacy.gov.ph/data-privacy-act/" 
                target="_blank" 
                rel="noopener noreferrer" 
                class="text-slate-400 hover:text-purple-300 transition-colors flex items-center gap-1.5"
              >
                <span class="material-symbols-outlined text-xs text-purple-400/80">verified_user</span>
                <span>RA 10173 (DPA 2012)</span>
              </a>
            </li>
            <li>
              <a 
                href="https://pcw.gov.ph/" 
                target="_blank" 
                rel="noopener noreferrer" 
                class="text-slate-400 hover:text-purple-300 transition-colors flex items-center gap-1.5"
              >
                <span class="material-symbols-outlined text-xs text-purple-400/80">open_in_new</span>
                <span>PCW GAD Guidelines</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Column 4: Contact & Office Location (lg:col-span-3) -->
        <div class="lg:col-span-3 flex flex-col space-y-3">
          <div class="flex items-center gap-2">
            <div class="w-1 h-3.5 rounded bg-purple-500"></div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-white">Office Location</h4>
          </div>
          <ul class="space-y-2.5 text-xs text-slate-400">
            <li class="flex items-start gap-2.5">
              <span class="material-symbols-outlined text-sm text-purple-400 shrink-0 mt-0.5">location_on</span>
              <span class="leading-snug text-slate-300">
                Behind LANDBANK, BSU Compound, Km. 5, La Trinidad, 2601 Benguet, Philippines
              </span>
            </li>
            <li class="flex items-center gap-2.5">
              <span class="material-symbols-outlined text-sm text-purple-400 shrink-0">phone_in_talk</span>
              <span class="text-slate-300">(074) 422-2127</span>
            </li>
            <li class="flex items-center gap-2.5">
              <span class="material-symbols-outlined text-sm text-purple-400 shrink-0">mail</span>
              <a href="mailto:gad.office@bsu.edu.ph" class="text-slate-300 hover:text-purple-300 transition-colors underline-offset-2 hover:underline">
                gad.office@bsu.edu.ph
              </a>
            </li>
            <li class="flex items-center gap-2.5">
              <span class="material-symbols-outlined text-sm text-purple-400 shrink-0">schedule</span>
              <span class="text-slate-300">Mon – Fri: 8:00 AM – 5:00 PM</span>
            </li>
          </ul>
        </div>

      </div>

      <!-- Horizontal Divider -->
      <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent my-6"></div>

      <!-- Bottom Bar: Copyright, Compliance, and Back-to-Top -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400 pb-20 md:pb-2">
        <div class="flex flex-col sm:flex-row items-center gap-1.5 sm:gap-3 text-center sm:text-left">
          <span>&copy; {{ currentYear }} Benguet State University Gender and Development Office.</span>
          <span class="hidden sm:inline text-slate-600">•</span>
          <span class="text-[11px] text-slate-500">All rights reserved.</span>
        </div>

        <div class="flex items-center gap-4">
          <span class="text-[11px] text-slate-500 hidden md:inline">
            Republic of the Philippines • SUC GFPS
          </span>
          <button 
            type="button"
            @click="scrollToTop"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/5 hover:bg-purple-600/20 border border-white/10 hover:border-purple-500/40 text-slate-300 hover:text-white transition-all text-xs cursor-pointer active:scale-95"
            aria-label="Back to Top"
          >
            <span>Back to Top</span>
            <span class="material-symbols-outlined text-xs">arrow_upward</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Privacy Policy Modal -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div 
        v-if="showPrivacyModal" 
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/75 backdrop-blur-md p-4"
        @keydown.esc="showPrivacyModal = false"
        tabindex="0"
      >
        <div class="bg-[#15152a] rounded-2xl w-full max-w-2xl shadow-2xl flex flex-col max-h-[88vh] border border-purple-500/30 overflow-hidden ring-1 ring-black/80">
          <div class="p-5 sm:p-6 border-b border-white/10 flex justify-between items-center bg-purple-950/40">
            <div class="flex items-center gap-2.5">
              <span class="material-symbols-outlined text-purple-400 text-2xl">privacy_tip</span>
              <div>
                <h2 class="text-lg sm:text-xl font-bold text-white">Data Privacy Policy</h2>
                <p class="text-[11px] text-purple-300/80">Republic Act No. 10173 (Data Privacy Act of 2012)</p>
              </div>
            </div>
            <button 
              type="button" 
              @click="showPrivacyModal = false" 
              class="p-1.5 rounded-full text-slate-400 hover:text-white hover:bg-white/10 transition-colors"
              aria-label="Close modal"
            >
              <span class="material-symbols-outlined text-xl">close</span>
            </button>
          </div>
          
          <div class="p-5 sm:p-6 overflow-y-auto text-slate-300 text-xs sm:text-sm space-y-4 text-left custom-scrollbar leading-relaxed">
            <div class="p-3.5 rounded-xl bg-purple-900/20 border border-purple-500/20 text-purple-200 text-xs">
              <strong class="font-bold text-white">Notice:</strong> The Benguet State University Gender and Development Office values your privacy and is committed to protecting your personal information pursuant to RA 10173 and University data governance regulations.
            </div>

            <p>
              By accessing and using the BSU Gender and Development Activity Monitoring System (GAD-AMS v2), you acknowledge and consent to the following provisions regarding personal information collection and processing:
            </p>
            
            <div class="space-y-3">
              <div class="p-3 rounded-lg bg-white/5 border border-white/5">
                <h3 class="font-bold text-white text-xs uppercase tracking-wider mb-1 flex items-center gap-1.5">
                  <span class="material-symbols-outlined text-sm text-purple-400">inventory_2</span>
                  1. Purpose of Data Collection
                </h3>
                <p class="text-slate-300 text-xs leading-relaxed">
                  Your identity details (full name, institutional email, designated office/college, account credentials, and system role) are collected solely for user authentication, activity design submissions, accomplishment report tracking, official communications, and statutory gender audit reporting.
                </p>
              </div>

              <div class="p-3 rounded-lg bg-white/5 border border-white/5">
                <h3 class="font-bold text-white text-xs uppercase tracking-wider mb-1 flex items-center gap-1.5">
                  <span class="material-symbols-outlined text-sm text-purple-400">security</span>
                  2. Security Measures & Retention
                </h3>
                <p class="text-slate-300 text-xs leading-relaxed">
                  The system employs encryption, password hashing, automated data retention safeguards, and role-based access control to prevent unauthorized modification, disclosure, or access. Inactive audit trails and historical documents follow University data retention schedules.
                </p>
              </div>

              <div class="p-3 rounded-lg bg-white/5 border border-white/5">
                <h3 class="font-bold text-white text-xs uppercase tracking-wider mb-1 flex items-center gap-1.5">
                  <span class="material-symbols-outlined text-sm text-purple-400">share</span>
                  3. Non-Disclosure & Third Parties
                </h3>
                <p class="text-slate-300 text-xs leading-relaxed">
                  Information entered into GAD-AMS is confidential and accessible solely to authorized BSU GAD staff, designated college focal persons, and university administrators. Data is never shared with unauthorized external entities except where mandated by regulatory authorities (CHED, PCW, COA).
                </p>
              </div>

              <div class="p-3 rounded-lg bg-white/5 border border-white/5">
                <h3 class="font-bold text-white text-xs uppercase tracking-wider mb-1 flex items-center gap-1.5">
                  <span class="material-symbols-outlined text-sm text-purple-400">manage_accounts</span>
                  4. Your Rights as a Data Subject
                </h3>
                <p class="text-slate-300 text-xs leading-relaxed">
                  Under the Data Privacy Act, you have the right to request access to your records, rectify inaccurate profiles, or file inquiries regarding your stored information by contacting the BSU GAD Office Data Protection Officer at <a href="mailto:gad.office@bsu.edu.ph" class="text-purple-300 underline">gad.office@bsu.edu.ph</a>.
                </p>
              </div>
            </div>
          </div>

          <div class="p-4 border-t border-white/10 bg-black/30 flex justify-end">
            <button 
              type="button"
              @click="showPrivacyModal = false" 
              class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs transition-colors cursor-pointer"
            >
              Close Policy
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Terms of Service Modal -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div 
        v-if="showTermsModal" 
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/75 backdrop-blur-md p-4"
        @keydown.esc="showTermsModal = false"
        tabindex="0"
      >
        <div class="bg-[#15152a] rounded-2xl w-full max-w-2xl shadow-2xl flex flex-col max-h-[88vh] border border-purple-500/30 overflow-hidden ring-1 ring-black/80">
          <div class="p-5 sm:p-6 border-b border-white/10 flex justify-between items-center bg-purple-950/40">
            <div class="flex items-center gap-2.5">
              <span class="material-symbols-outlined text-purple-400 text-2xl">gavel</span>
              <div>
                <h2 class="text-lg sm:text-xl font-bold text-white">Terms of Service</h2>
                <p class="text-[11px] text-purple-300/80">BSU GAD-AMS v2 Portal Acceptable Use Policy</p>
              </div>
            </div>
            <button 
              type="button" 
              @click="showTermsModal = false" 
              class="p-1.5 rounded-full text-slate-400 hover:text-white hover:bg-white/10 transition-colors"
              aria-label="Close modal"
            >
              <span class="material-symbols-outlined text-xl">close</span>
            </button>
          </div>
          
          <div class="p-5 sm:p-6 overflow-y-auto text-slate-300 text-xs sm:text-sm space-y-4 text-left custom-scrollbar leading-relaxed">
            <p>
              Welcome to the official Benguet State University Gender and Development Activity Monitoring & Management System. By logging in or interacting with this platform, you agree to comply with the terms below:
            </p>

            <div class="space-y-3">
              <div class="p-3 rounded-lg bg-white/5 border border-white/5">
                <h3 class="font-bold text-white text-xs uppercase tracking-wider mb-1">
                  1. Authorized Institutional Use Only
                </h3>
                <p class="text-slate-300 text-xs leading-relaxed">
                  This system is provided strictly for authenticated BSU administrators, GAD staff, college/unit coordinators, and authorized evaluators to manage gender-related plans, activities, budgets, and accomplishment reports.
                </p>
              </div>

              <div class="p-3 rounded-lg bg-white/5 border border-white/5">
                <h3 class="font-bold text-white text-xs uppercase tracking-wider mb-1">
                  2. Accuracy of Submissions
                </h3>
                <p class="text-slate-300 text-xs leading-relaxed">
                  Users submitting Activity Designs (AD) and Accomplishment Reports (AR) are responsible for verifying the accuracy of all submitted budgetary items, participant counts, gender mandates, and supporting attachments.
                </p>
              </div>

              <div class="p-3 rounded-lg bg-white/5 border border-white/5">
                <h3 class="font-bold text-white text-xs uppercase tracking-wider mb-1">
                  3. Credential Security & Prohibited Acts
                </h3>
                <p class="text-slate-300 text-xs leading-relaxed">
                  Users must keep login credentials strictly confidential. Tampering with system workflows, bypassing authorization checks, sharing account credentials, or uploading malicious files is grounds for account suspension and administrative disciplinary action.
                </p>
              </div>

              <div class="p-3 rounded-lg bg-white/5 border border-white/5">
                <h3 class="font-bold text-white text-xs uppercase tracking-wider mb-1">
                  4. System Modifications
                </h3>
                <p class="text-slate-300 text-xs leading-relaxed">
                  The BSU GAD Office reserves the right to enhance, upgrade, or revise portal functionalities, guidelines, and terms to remain aligned with Commission on Higher Education (CHED) and Philippine Commission on Women (PCW) standards.
                </p>
              </div>
            </div>
          </div>

          <div class="p-4 border-t border-white/10 bg-black/30 flex justify-end">
            <button 
              type="button" 
              @click="showTermsModal = false" 
              class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs transition-colors cursor-pointer"
            >
              I Understand & Agree
            </button>
          </div>
        </div>
      </div>
    </transition>

  </footer>
</template>

<script setup>
import { ref } from 'vue';

const showPrivacyModal = ref(false);
const showTermsModal = ref(false);

const currentYear = new Date().getFullYear();

const scrollToTop = () => {
  if (typeof window !== 'undefined') {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(168, 85, 247, 0.4);
  border-radius: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(168, 85, 247, 0.7);
}
</style>
