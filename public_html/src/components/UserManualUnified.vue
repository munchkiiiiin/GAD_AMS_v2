<template>
  <div class="page-container">
    <!-- Header Section (Campus Resources Style) -->
    <div class="header-section">
      <h1 class="page-title">User Manual & Guidelines</h1>
      <p class="page-subtitle">Comprehensive system documentation, user guides, and data privacy policies for GAD-AMS.</p>
    </div>

    <!-- Unified Modern Tabs (identical to Campus Resources tabs) -->
    <div class="flex gap-2 sm:gap-4 border-b border-slate-200 mb-6 px-1 sm:px-0 overflow-x-auto">
      <button 
        class="tab-btn" 
        :class="{ 'active-tab': activeTab === 'manual' }"
        @click="switchTab('manual')"
        type="button"
      >
        <span class="material-symbols-outlined">menu_book</span>
        User Manual & Guide
      </button>
      <button 
        class="tab-btn" 
        :class="{ 'active-tab': activeTab === 'privacy' }"
        @click="switchTab('privacy')"
        type="button"
      >
        <span class="material-symbols-outlined">privacy_tip</span>
        Data Privacy Policy
      </button>
    </div>

    <!-- TAB 1: USER MANUAL -->
    <div v-show="activeTab === 'manual'" class="tab-content-panel">
      <div class="content-wrapper">
        <!-- Sticky Table of Contents Sidebar -->
        <aside class="sidebar-container">
          <div class="sticky-toc">
            <h3 class="toc-title">
              <span class="material-symbols-outlined text-purple-600 text-lg">toc</span>
              Table of Contents
            </h3>
            <ul class="toc-list">
              <li v-for="section in manualToc" :key="section.id">
                <button 
                  @click="scrollToManualSection(section.id)"
                  class="sidebar-nav-item"
                  :class="{ 'active-nav-item': activeManualSection === section.id }"
                  type="button"
                >
                  {{ section.label }}
                </button>
              </li>
            </ul>
          </div>
        </aside>

        <!-- Main Manual Content -->
        <div class="content-area">
          <div class="doc-card">
            
            <section id="manual-intro" class="doc-section">
              <div class="doc-header-banner">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-600 to-pink-600 flex items-center justify-center text-white shadow-lg shadow-purple-600/30">
                  <span class="material-symbols-outlined text-2xl">school</span>
                </div>
                <div>
                  <h2 class="section-heading text-2xl font-black text-slate-900">1. Introduction</h2>
                  <p class="section-lead text-slate-600">
                    Welcome to the Benguet State University Gender and Development Activities Management System (GAD-AMS). This guide covers essential workflows, procedures, and system features tailored for your role.
                  </p>
                </div>
              </div>
            </section>

            <section id="manual-getting-started" class="doc-section">
              <h2 class="section-heading">2. Getting Started</h2>
              <div class="space-y-4 mt-3">
                <div class="p-4 rounded-xl bg-purple-50/60 border border-purple-100">
                  <h3 class="font-bold text-slate-900 text-base flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-purple-600 text-lg">login</span>
                    2.1 Logging In
                  </h3>
                  <p class="text-sm text-slate-600">
                    Access the system via your web browser using your institutional GAD credentials. Enter your registered email and password on the portal login screen.
                  </p>
                </div>
                <div class="p-4 rounded-xl bg-purple-50/60 border border-purple-100">
                  <h3 class="font-bold text-slate-900 text-base flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-purple-600 text-lg">dashboard</span>
                    2.2 Navigating the Dashboard
                  </h3>
                  <p class="text-sm text-slate-600">
                    Upon logging in, you will be redirected to your specialized dashboard featuring real-time submission statistics, target deadlines, pending reviews, and quick access to core workflows.
                  </p>
                </div>
              </div>
            </section>

            <section id="manual-dashboard" class="doc-section">
              <h2 class="section-heading">3. Dashboard Overview</h2>
              <p class="text-sm text-slate-600 mb-3">
                Your role dashboard consolidates high-priority metrics and organizational data:
              </p>
              <ul class="styled-list">
                <li><strong>Pending Activities:</strong> Displays submitted Activity Designs and Accomplishment Reports awaiting review or action.</li>
                <li><strong>Activity Designs:</strong> Tracks live progress and workflow status across all proposed activities.</li>
                <li><strong>Accomplishment Reports:</strong> Tracks completed activities with ratings and participant sex-disaggregated data.</li>
                <li><strong>GAD Budget:</strong> Visualizes institutional GAD allocation, current expenditures, and remaining funds.</li>
                <li><strong>Calendar & Deadlines:</strong> Displays critical compliance deadlines and target submission schedules.</li>
              </ul>
            </section>

            <section id="manual-submissions" class="doc-section">
              <h2 class="section-heading">4. Submitting Documents</h2>
              
              <div class="space-y-5 mt-4">
                <div class="subcard">
                  <h3 class="font-bold text-purple-900 text-base mb-2">4.1 Activity Design Submission</h3>
                  <ol class="styled-list numeric text-sm space-y-1.5">
                    <li>Click <strong>New Submission</strong> on the navigation bar.</li>
                    <li>Select <strong>Activity Design</strong>.</li>
                    <li>Fill out activity title, transaction nature, dates, venue, and target attendees.</li>
                    <li>Enter proposed budget allocation and upload your Activity Design PDF.</li>
                    <li>Review proponent and author information, then submit for evaluation.</li>
                  </ol>
                </div>

                <div class="subcard">
                  <h3 class="font-bold text-purple-900 text-base mb-2">4.2 Accomplishment Report Submission</h3>
                  <ol class="styled-list numeric text-sm space-y-1.5">
                    <li>Click <strong>New Submission</strong> &rarr; <strong>Accomplishment Report</strong>.</li>
                    <li>Select the approved Activity Design control number from the reference list.</li>
                    <li>Enter actual attendee count, male/female participant breakdown, and evaluation ratings.</li>
                    <li>Upload supporting files (attendance sheets, photo documentation, financial liquidation).</li>
                    <li>Submit for review and mandate accomplishment tallying.</li>
                  </ol>
                </div>
              </div>
            </section>

            <section id="manual-viewing" class="doc-section">
              <h2 class="section-heading">5. Viewing and Monitoring Documents</h2>
              <p class="text-sm text-slate-600 mb-3">
                Access <strong>Documents &rarr; Submitted List</strong> to view and track all proposals:
              </p>
              <ul class="styled-list">
                <li><strong>Live Statuses:</strong> Monitor whether items are Pending, Under Revision, Approved, or Archived.</li>
                <li><strong>Inline Document Previewer:</strong> Click View on any submission to inspect the PDF, reviews, and feedback directly inside the secure browser viewer.</li>
                <li><strong>Quick Filters:</strong> Filter records by unit, document category, or fiscal year.</li>
              </ul>
            </section>

            <section id="manual-mandates" class="doc-section">
              <h2 class="section-heading">6. Plan, Budget & Mandates</h2>
              <p class="text-sm text-slate-600 mb-2">
                Under <strong>Plan & Budget</strong>, users can inspect GAD mandates, GPB allocations, and expenditure ledgers:
              </p>
              <ul class="styled-list">
                <li>View institutional GAD Mandates categorized by focus area and responsible office.</li>
                <li>Monitor expenditure ledgers comparing proposed vs. liquidated amounts.</li>
                <li>Generate exportable audit-ready annual reports.</li>
              </ul>
            </section>

            <section id="manual-faq" class="doc-section">
              <h2 class="section-heading">7. Frequently Asked Questions</h2>
              <div class="space-y-3 mt-3">
                <div v-for="(faq, idx) in faqs" :key="idx" class="faq-item">
                  <h4 class="font-bold text-slate-900 text-sm mb-1 flex items-start gap-2">
                    <span class="material-symbols-outlined text-purple-600 text-base mt-0.5 flex-shrink-0">help</span>
                    {{ faq.q }}
                  </h4>
                  <p class="text-xs sm:text-sm text-slate-600 pl-6">{{ faq.a }}</p>
                </div>
              </div>
            </section>

            <section id="manual-support" class="doc-section">
              <h2 class="section-heading">8. Technical Support & Helpdesk</h2>
              <div class="p-5 rounded-2xl bg-gradient-to-r from-purple-900/10 via-purple-600/10 to-pink-500/10 border border-purple-200 mt-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                  <h4 class="font-black text-slate-900 text-base">Need Technical Assistance?</h4>
                  <p class="text-xs sm:text-sm text-slate-600 mt-0.5">
                    Contact the Benguet State University GAD Office for technical concerns, account issues, or system onboarding.
                  </p>
                  <p class="text-xs text-purple-700 font-bold mt-2">
                    Email: gad@bsu.edu.ph &bull; Location: Administration Building, BSU Main Campus
                  </p>
                </div>
                <router-link :to="messagesShortcut" class="px-4 py-2.5 rounded-xl bg-purple-600 text-white text-xs font-bold shadow-md hover:bg-purple-700 transition-colors flex-shrink-0 flex items-center gap-1.5 text-decoration-none">
                  <span class="material-symbols-outlined text-sm">forum</span>
                  Message Support
                </router-link>
              </div>
            </section>

          </div>
        </div>
      </div>
    </div>

    <!-- TAB 2: DATA PRIVACY POLICY -->
    <div v-show="activeTab === 'privacy'" class="tab-content-panel">
      <div class="content-wrapper">
        <!-- Sticky Table of Contents Sidebar -->
        <aside class="sidebar-container">
          <div class="sticky-toc">
            <h3 class="toc-title">
              <span class="material-symbols-outlined text-purple-600 text-lg">shield</span>
              Privacy Policy
            </h3>
            <ul class="toc-list">
              <li v-for="section in privacyToc" :key="section.id">
                <button 
                  @click="scrollToPrivacySection(section.id)"
                  class="sidebar-nav-item"
                  :class="{ 'active-nav-item': activePrivacySection === section.id }"
                  type="button"
                >
                  {{ section.label }}
                </button>
              </li>
            </ul>
          </div>
        </aside>

        <!-- Main Privacy Content -->
        <div class="content-area">
          <div class="doc-card">
            
            <section id="privacy-intro" class="doc-section">
              <div class="doc-header-banner">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-700 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-purple-700/30">
                  <span class="material-symbols-outlined text-2xl">verified_user</span>
                </div>
                <div>
                  <h2 class="section-heading text-2xl font-black text-slate-900">1. Data Privacy Statement</h2>
                  <p class="section-lead text-slate-600">
                    Benguet State University (BSU) is strictly committed to safeguarding personal information collected and processed through the Gender and Development Activities Management System (GAD-AMS) in compliance with the Data Privacy Act of 2012 (Republic Act No. 10173).
                  </p>
                </div>
              </div>
            </section>

            <section id="privacy-collection" class="doc-section">
              <h2 class="section-heading">2. Information We Collect</h2>
              <p class="text-sm text-slate-600 mb-3">
                GAD-AMS collects and processes personal information strictly necessary for academic and institutional Gender and Development mandates:
              </p>
              <ul class="styled-list">
                <li><strong>Personal Identity:</strong> Full name, institutional email address, employee/student ID, college, and office unit.</li>
                <li><strong>Activity Documentation:</strong> Proposed activity designs, budgets, proponent details, accomplishment reports, and documentation photographs.</li>
                <li><strong>Demographic Metrics:</strong> Sex-disaggregated participant statistics (male/female attendee counts) for national statutory compliance.</li>
                <li><strong>Security & Access Logs:</strong> System sign-in logs, timestamped activity trails, and audit records.</li>
              </ul>
            </section>

            <section id="privacy-purpose" class="doc-section">
              <h2 class="section-heading">3. Purpose of Processing</h2>
              <p class="text-sm text-slate-600 mb-3">
                Your data is processed exclusively for official university functions:
              </p>
              <ul class="styled-list">
                <li>Review, evaluation, and approval of GAD program designs and accomplishment reports.</li>
                <li>Formulation and monitoring of the institutional GAD Plan and Budget (GPB).</li>
                <li>Fulfillment of statutory reporting to the Philippine Commission on Women (PCW), CHED, and DBM.</li>
                <li>Statistical analytics to enhance gender equality initiatives across BSU campuses.</li>
              </ul>
            </section>

            <section id="privacy-security" class="doc-section">
              <h2 class="section-heading">4. Data Security & Confidentiality</h2>
              <p class="text-sm text-slate-600">
                BSU enforces organizational, physical, and technological security safeguards. Access is restricted using role-based access controls (RBAC), TLS 1.3 encrypted data transmission, automated session timeouts, and continuous audit logging. No personal information is disclosed or sold to unauthorized third parties.
              </p>
            </section>

            <section id="privacy-retention" class="doc-section">
              <h2 class="section-heading">5. Data Retention & Archival</h2>
              <p class="text-sm text-slate-600">
                Personal records and document submissions are retained in accordance with National Archives of the Philippines guidelines and Commission on Audit (COA) statutory retention schedules. Outdated records are securely disposed of in compliance with NPC protocols.
              </p>
            </section>

            <section id="privacy-rights" class="doc-section">
              <h2 class="section-heading">6. Rights of Data Subjects</h2>
              <p class="text-sm text-slate-600 mb-3">
                Under RA 10173, you hold the following rights:
              </p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700">
                  <strong class="text-slate-900 block mb-1">Right to be Informed</strong> Notice of how your data is collected and processed.
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700">
                  <strong class="text-slate-900 block mb-1">Right to Access</strong> Request copies of your processed personal records.
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700">
                  <strong class="text-slate-900 block mb-1">Right to Rectification</strong> Dispute and correct inaccurate information.
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700">
                  <strong class="text-slate-900 block mb-1">Right to Object & Erasure</strong> Request blocking or removal under lawful grounds.
                </div>
              </div>
            </section>

            <section id="privacy-contact" class="doc-section">
              <h2 class="section-heading">7. Contact the Data Protection Officer</h2>
              <div class="p-5 rounded-2xl bg-purple-950 text-white border border-purple-800/40 shadow-xl mt-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                  <div class="flex items-center gap-2 text-purple-300 font-bold text-xs uppercase tracking-wider mb-1">
                    <span class="material-symbols-outlined text-base">policy</span>
                    Official Data Protection Officer
                  </div>
                  <h4 class="font-black text-white text-base">Benguet State University</h4>
                  <p class="text-xs text-slate-300 mt-1">
                    KM 5, La Trinidad, Benguet 2601, Philippines &bull; Tel: (074) 422-2401
                  </p>
                  <p class="text-xs text-purple-300 font-mono mt-1">
                    Email: dpo@bsu.edu.ph
                  </p>
                </div>
                <a href="mailto:dpo@bsu.edu.ph" class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold shadow-md transition-colors flex items-center gap-1.5 text-decoration-none flex-shrink-0">
                  <span class="material-symbols-outlined text-sm">mail</span>
                  Contact DPO
                </a>
              </div>
              <p class="text-[11px] text-slate-400 mt-4">
                Last updated: January 2026 &bull; GAD-AMS Policy Version 2.0
              </p>
            </section>

          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const props = defineProps({
  role: { type: String, default: 'admin' }
});

const route = useRoute();
const router = useRouter();

// Tab state: 'manual' or 'privacy'
const activeTab = ref(route.query.tab === 'privacy' ? 'privacy' : 'manual');
const activeManualSection = ref('manual-intro');
const activePrivacySection = ref('privacy-intro');

watch(() => route.query.tab, (newTab) => {
  if (newTab === 'privacy' || newTab === 'manual') {
    activeTab.value = newTab;
  }
});

const switchTab = (tab) => {
  activeTab.value = tab;
  router.replace({ query: { ...route.query, tab } });
};

const messagesShortcut = computed(() => {
  const r = props.role || 'admin';
  return `/${r}/messages`;
});

const manualToc = [
  { id: 'manual-intro', label: '1. Introduction' },
  { id: 'manual-getting-started', label: '2. Getting Started' },
  { id: 'manual-dashboard', label: '3. Dashboard Overview' },
  { id: 'manual-submissions', label: '4. Submitting Documents' },
  { id: 'manual-viewing', label: '5. Viewing Submissions' },
  { id: 'manual-mandates', label: '6. Plan & Budget' },
  { id: 'manual-faq', label: '7. Frequently Asked Questions' },
  { id: 'manual-support', label: '8. Technical Support' }
];

const privacyToc = [
  { id: 'privacy-intro', label: '1. Statement' },
  { id: 'privacy-collection', label: '2. Information We Collect' },
  { id: 'privacy-purpose', label: '3. Purpose of Processing' },
  { id: 'privacy-security', label: '4. Security & Protection' },
  { id: 'privacy-retention', label: '5. Retention & Archival' },
  { id: 'privacy-rights', label: '6. Rights of Data Subjects' },
  { id: 'privacy-contact', label: '7. Contact the DPO' }
];

const faqs = [
  { 
    q: 'What should I do if I encounter an error while uploading a document?', 
    a: 'Ensure your file format is PDF and within the allowed file size limit (up to 20MB per document). If an upload error persists, check your network or contact the GAD Office.' 
  },
  { 
    q: 'Can I edit an Activity Design after it has been submitted?', 
    a: 'While an Activity Design is under initial review, you can view the progress. If revisions are requested by the Director, the document will unlock in your revision workspace.' 
  },
  { 
    q: 'How do I access Accomplishment Report ratings and participant breakdowns?', 
    a: 'Open any submitted Accomplishment Report and inspect the Sex-Disaggregated Data breakdown and the rating criteria table.' 
  },
  { 
    q: 'Where do I find archived documents from past fiscal years?', 
    a: 'Navigate to Documents &rarr; Archives to filter all approved, completed, or superseded records across all academic years.' 
  }
];

const scrollToManualSection = (id) => {
  activeManualSection.value = id;
  const el = document.getElementById(id);
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
};

const scrollToPrivacySection = (id) => {
  activePrivacySection.value = id;
  const el = document.getElementById(id);
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
};

onMounted(() => {
  if (route.query.tab === 'privacy') {
    activeTab.value = 'privacy';
  }
});
</script>

<style scoped>
/* Campus Resources Unified Page Styling */
.page-container {
  padding: 24px 16px;
  max-width: 1400px;
  margin: 0 auto;
  width: 100%;
}

@media (min-width: 640px) {
  .page-container {
    padding: 32px;
  }
}

.header-section {
  margin-bottom: 24px;
}

.page-title {
  font-size: 28px;
  font-weight: 800;
  color: #1e293b;
  margin: 0 0 8px 0;
  letter-spacing: -0.02em;
}

@media (min-width: 640px) {
  .page-title {
    font-size: 36px;
  }
}

.page-subtitle {
  color: #64748b;
  font-size: 15px;
  margin: 0;
  line-height: 1.5;
}

@media (min-width: 640px) {
  .page-subtitle {
    font-size: 18px;
  }
}

/* Modern Tab Button Styling from Campus Resources */
.tab-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 18px;
  background: transparent;
  border: none;
  border-bottom: 3px solid transparent;
  color: #475569;
  font-weight: 800;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.2s;
  margin-bottom: -1px;
  white-space: nowrap;
}

@media (min-width: 640px) {
  .tab-btn {
    padding: 16px 24px;
    font-size: 18px;
  }
}

.tab-btn:hover {
  color: #0f172a;
  background: rgba(0, 0, 0, 0.04);
  border-radius: 8px 8px 0 0;
}

.tab-btn.active-tab {
  color: #0f172a;
  border-bottom-color: #9333ea;
}

.tab-btn .material-symbols-outlined {
  font-size: 20px;
  color: #9333ea;
}

/* Content Layout with Responsive TOC */
.content-wrapper {
  display: grid;
  grid-template-columns: 1fr;
  gap: 24px;
  align-items: start;
}

@media (min-width: 1024px) {
  .content-wrapper {
    grid-template-columns: 280px minmax(0, 1fr);
  }
}

.sidebar-container {
  width: 100%;
}

.sticky-toc {
  background: #ffffff;
  border: 1px solid rgba(226, 232, 240, 0.9);
  border-radius: 16px;
  padding: 18px;
  box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.04);
  position: static;
}

@media (min-width: 1024px) {
  .sticky-toc {
    position: sticky;
    top: 96px;
    max-height: calc(100vh - 120px);
    overflow-y: auto;
  }
}

.toc-title {
  font-size: 14px;
  font-weight: 800;
  color: #0f172a;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin: 0 0 12px 0;
  padding-bottom: 8px;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  gap: 6px;
}

.toc-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.sidebar-nav-item {
  width: 100%;
  text-align: left;
  background: transparent;
  border: none;
  padding: 8px 12px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.15s ease;
}

.sidebar-nav-item:hover {
  background: #f8fafc;
  color: #9333ea;
}

.sidebar-nav-item.active-nav-item {
  background: rgba(147, 51, 234, 0.1);
  color: #9333ea;
  font-weight: 800;
}

/* Document Cards & Sections */
.doc-card {
  background: #ffffff;
  border: 1px solid rgba(226, 232, 240, 0.9);
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
}

@media (min-width: 640px) {
  .doc-card {
    padding: 36px;
  }
}

.doc-section {
  padding-bottom: 32px;
  margin-bottom: 32px;
  border-bottom: 1px solid #f1f5f9;
}

.doc-section:last-child {
  padding-bottom: 0;
  margin-bottom: 0;
  border-bottom: none;
}

.doc-header-banner {
  display: flex;
  align-items: flex-start;
  gap: 16px;
}

.section-heading {
  font-size: 18px;
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 12px 0;
}

@media (min-width: 640px) {
  .section-heading {
    font-size: 20px;
  }
}

.section-lead {
  font-size: 14px;
  line-height: 1.6;
  margin-top: 6px;
}

.styled-list {
  padding-left: 20px;
  margin: 0;
  font-size: 14px;
  color: #475569;
  line-height: 1.7;
}

.styled-list li {
  margin-bottom: 6px;
}

.subcard {
  padding: 16px;
  border-radius: 14px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}

.faq-item {
  padding: 14px;
  border-radius: 12px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  transition: border-color 0.2s;
}

.faq-item:hover {
  border-color: #cbd5e1;
}
</style>
