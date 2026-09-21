<template>
  <div class="manage-allocation-container">
    <!-- Top Header & Navigation -->
    <div class="page-header-card">
      <div class="header-main-row">
        <div>
          <div class="breadcrumb-trail">
            <router-link :to="`/${role}/dashboard`" class="breadcrumb-link">Dashboard</router-link>
            <span class="breadcrumb-separator">/</span>
            <router-link :to="`/${role}/plan-and-budget`" class="breadcrumb-link">Plan & Budget</router-link>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Manage Allocation</span>
          </div>
          <h1 class="page-title">
            <span class="title-icon">account_balance_wallet</span>
            GAD Budget Distribution &amp; Allocation Management
          </h1>
          <p class="page-subtitle">
            Assign Activity Design and Accomplishment Report expenditures to specific mandate budget lines and monitor real-time fund allocations.
          </p>
        </div>

        <div class="header-actions">
          <router-link :to="`/${role}/plan-and-budget`" class="btn-action outline">
            <span class="material-symbols-outlined">edit_note</span>
            Edit GAD Plan
          </router-link>
          <router-link :to="`/${role}/budget`" class="btn-action outline">
            <span class="material-symbols-outlined">monitoring</span>
            Budget Monitoring
          </router-link>
          <button @click="fetchMandateStats" class="btn-action primary" :disabled="loadingStats">
            <span class="material-symbols-outlined" :class="{ 'spin-anim': loadingStats }">refresh</span>
            Refresh
          </button>
        </div>
      </div>

      <!-- Financial Metrics Summary Cards -->
      <div class="metrics-grid">
        <div class="metric-card">
          <div class="metric-icon-box blue">
            <span class="material-symbols-outlined">account_balance</span>
          </div>
          <div class="metric-details">
            <div class="metric-label">Total Allocated Budget</div>
            <div class="metric-value">₱{{ formatCurrency(overallTotals.budget) }}</div>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon-box green">
            <span class="material-symbols-outlined">trending_up</span>
          </div>
          <div class="metric-details">
            <div class="metric-label">Total Utilized (AR)</div>
            <div class="metric-value text-green">₱{{ formatCurrency(overallTotals.utilized) }}</div>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon-box amber">
            <span class="material-symbols-outlined">hourglass_top</span>
          </div>
          <div class="metric-details">
            <div class="metric-label">Total Pending (AD)</div>
            <div class="metric-value text-amber">₱{{ formatCurrency(overallTotals.pending) }}</div>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon-box purple">
            <span class="material-symbols-outlined">savings</span>
          </div>
          <div class="metric-details">
            <div class="metric-label">Available Remaining</div>
            <div class="metric-value" :class="overallTotals.remaining < 0 ? 'text-red' : 'text-cyan'">
              ₱{{ formatCurrency(overallTotals.remaining) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="toolbar-card">
      <div class="toolbar-left">
        <div class="search-input-box">
          <span class="material-symbols-outlined search-icon">search</span>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by mandate, cause, or GAD activity..."
            class="search-input"
          />
          <button v-if="searchQuery" @click="searchQuery = ''" class="clear-search-btn" title="Clear search">
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>

        <div class="filter-group">
          <label class="filter-label">Classification:</label>
          <select v-model="mandateStatsFilter" class="filter-select">
            <option value="all">All Classifications ({{ mandateStats.length }})</option>
            <option value="client">Client-Focused</option>
            <option value="org">Organization-Focused</option>
            <option value="attributed">Attributed Program</option>
          </select>
        </div>
      </div>

      <div class="toolbar-right">
        <div class="mandate-counter-badge">
          Showing <strong>{{ filteredMandateStats.length }}</strong> of <strong>{{ mandateStats.length }}</strong> Mandates
        </div>
      </div>
    </div>

    <!-- Content State: Loading -->
    <div v-if="loadingStats" class="state-card loading-state">
      <div class="spinner-large"></div>
      <h3>Loading Mandate Budget Distributions...</h3>
      <p>Synchronizing active budget lines and approved allocations...</p>
    </div>

    <!-- Content State: Empty GAD Plan -->
    <div v-else-if="mandateStats.length === 0" class="state-card empty-state">
      <span class="empty-icon">📭</span>
      <h3>No Mandate Data Available</h3>
      <p>The statistics are generated from your saved GAD Plan.<br />Please ensure your GAD Plan is saved to generate mandate distribution items.</p>
      <router-link :to="`/${role}/plan-and-budget`" class="btn-action primary" style="margin-top: 16px;">
        Go to Plan &amp; Budget
      </router-link>
    </div>

    <!-- Content State: Filter yields no results -->
    <div v-else-if="filteredMandateStats.length === 0" class="state-card empty-state">
      <span class="empty-icon">🔎</span>
      <h3>No matching mandates found</h3>
      <p>No mandate records match your current search and filter criteria.</p>
      <button @click="resetFilters" class="btn-action outline" style="margin-top: 16px;">
        Reset Filters
      </button>
    </div>

    <!-- Content State: Data Cards Grid -->
    <div v-else class="mandates-grid">
      <div
        v-for="(stat, idx) in filteredMandateStats"
        :key="idx"
        class="mandate-card"
      >
        <!-- Narrative Blocks Section -->
        <div class="narrative-blocks">
          <div class="narrative-block mandate-block">
            <div class="narrative-tag">Gender Issue / Mandate</div>
            <div class="narrative-text primary">{{ stat.mandate || 'N/A' }}</div>
          </div>

          <div class="narrative-block cause-block">
            <div class="narrative-tag">Cause of Gender Issue</div>
            <div class="narrative-text secondary">{{ stat.cause || 'N/A' }}</div>
          </div>

          <div class="narrative-block activity-block">
            <div class="narrative-tag">GAD Activity</div>
            <div class="narrative-text secondary">{{ stat.activity || 'N/A' }}</div>
          </div>
        </div>

        <!-- Stats & Financials Section -->
        <div class="stats-panel">
          <div class="approval-counters">
            <div class="counter-box">
              <div class="counter-label">Approved ADs</div>
              <div class="counter-number">{{ stat.approved_ad_count || 0 }}</div>
            </div>
            <div class="counter-box">
              <div class="counter-label">Approved ARs</div>
              <div class="counter-number">{{ stat.approved_ar_count || 0 }}</div>
            </div>
          </div>

          <div class="financial-lines">
            <div class="financial-line">
              <span class="label">Budget:</span>
              <span class="value budget-val">₱{{ formatCurrency(stat.budget) }}</span>
            </div>
            <div class="financial-line">
              <span class="label">Utilized:</span>
              <span class="value text-green">₱{{ formatCurrency(stat.utilized_budget) }}</span>
            </div>
            <div class="financial-line">
              <span class="label">Pending (ADs):</span>
              <span class="value text-amber">₱{{ formatCurrency(stat.pending_budget) }}</span>
            </div>
            <div class="financial-line remaining-line">
              <span class="label uppercase">Remaining:</span>
              <span
                class="value remaining-val"
                :class="stat.remaining_budget < 0 ? 'text-red' : 'text-blue'"
              >
                ₱{{ formatCurrency(stat.remaining_budget) }}
              </span>
            </div>
          </div>

          <!-- Budget Lines Breakdown -->
          <div v-if="stat.budget_lines && stat.budget_lines.length > 0" class="budget-lines-container">
            <div class="budget-lines-title">
              <span>Budget Lines Breakdown</span>
              <span class="budget-lines-count">({{ stat.budget_lines.length }})</span>
            </div>
            <div class="budget-lines-list">
              <div v-for="bl in stat.budget_lines" :key="bl.id" class="budget-line-item">
                <div class="line-name">{{ bl.label || 'Unnamed Line' }}</div>
                <div class="line-row">
                  <span class="line-sublabel">Original:</span>
                  <span class="line-amount">₱{{ formatCurrency(bl.amount) }}</span>
                </div>
                <div class="line-row text-green">
                  <span class="line-sublabel">Utilized:</span>
                  <span class="line-amount">₱{{ formatCurrency(bl.utilized_budget) }}</span>
                </div>
                <div class="line-row text-amber">
                  <span class="line-sublabel">Pending (AD):</span>
                  <span class="line-amount">₱{{ formatCurrency(bl.pending_budget) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Button -->
        <button
          @click="openAllocationModal(stat)"
          class="btn-manage-allocation"
          title="Manage budget allocations for this mandate"
        >
          <span class="material-symbols-outlined btn-icon">tune</span>
          Manage Allocations
        </button>
      </div>
    </div>

    <!-- ALLOCATION MODAL -->
    <div
      v-if="showAllocationModal"
      class="modal-backdrop"
      @click.self="closeAllocationModal"
    >
      <div class="modal-card">
        <div class="modal-header">
          <div>
            <h2 class="modal-title">
              <span class="material-symbols-outlined">payments</span>
              Budget Allocations
            </h2>
            <p class="modal-subtitle">
              Assign specific Activity Design and Accomplishment Report line items to this mandate's planned budget lines.
            </p>
          </div>
          <button @click="closeAllocationModal" class="btn-close-modal">
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>

        <!-- Mandate Identifier Banner -->
        <div v-if="currentAllocationStat" class="target-mandate-banner">
          <div class="banner-label">Target Mandate / Activity:</div>
          <div class="banner-title">{{ currentAllocationStat.activity || currentAllocationStat.mandate }}</div>
        </div>

        <div class="modal-body">
          <div v-if="loadingAllocations" class="modal-loading-box">
            <div class="spinner-medium"></div>
            <p>Loading allocation details...</p>
          </div>

          <div v-else>
            <!-- Planned Budget Lines Summary -->
            <div
              v-if="currentAllocationStat && currentAllocationStat.budget_lines && currentAllocationStat.budget_lines.length > 0"
              class="modal-section"
            >
              <h3 class="section-title">Planned Budget Lines</h3>
              <div class="table-responsive">
                <table class="modal-table">
                  <thead>
                    <tr>
                      <th>Budget Line</th>
                      <th>Original Amount</th>
                      <th>Pending (AD)</th>
                      <th>Utilized (AR)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="bl in currentAllocationStat.budget_lines" :key="bl.id">
                      <td class="font-medium text-white">{{ bl.label || 'Unnamed Line' }}</td>
                      <td class="font-mono text-white">₱{{ formatCurrency(bl.amount) }}</td>
                      <td class="font-mono text-amber">₱{{ formatCurrency(bl.pending_budget) }}</td>
                      <td class="font-mono text-green">₱{{ formatCurrency(bl.utilized_budget) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Actual Expenditures Breakdown (Verified ARs) -->
            <div v-if="arVerifiedTotals && arVerifiedTotals.length > 0" class="modal-section">
              <h3 class="section-title">Actual Expenditures Breakdown (Verified ARs)</h3>
              <div class="table-responsive">
                <table class="modal-table">
                  <thead>
                    <tr>
                      <th>Expenditure Item</th>
                      <th>Total Cost</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(tv, idx) in arVerifiedTotals" :key="idx">
                      <td class="font-medium text-white">{{ tv.name }}</td>
                      <td class="font-mono text-green font-semibold">₱{{ formatCurrency(tv.amount) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Approved Activity Designs & Accomplishment Reports List -->
            <div class="modal-section">
              <h3 class="section-title">Approved Documents &amp; Expense Items</h3>

              <div v-if="allocationsData.length === 0" class="no-documents-box">
                <span class="material-symbols-outlined text-4xl text-slate-500">folder_off</span>
                <p>No approved Activity Designs or Accomplishment Reports found linked to this mandate.</p>
              </div>

              <div v-else class="documents-accordion">
                <div
                  v-for="doc in allocationsData"
                  :key="doc.type + doc.id"
                  class="document-card"
                >
                  <div class="document-header" @click="doc._expanded = !doc._expanded">
                    <div class="doc-header-info">
                      <span class="doc-type-badge" :class="doc.type">
                        [{{ doc.type }}]
                      </span>
                      <span class="doc-title">{{ doc.title || doc.control_number }}</span>
                      <button
                        v-if="doc.attachment"
                        @click.stop="openDocumentPreview(doc.attachment, doc.type)"
                        class="btn-preview-link"
                        title="Preview uploaded document"
                      >
                        <span class="material-symbols-outlined text-sm">visibility</span>
                        Preview Document
                      </button>
                    </div>
                    <span class="material-symbols-outlined chevron">
                      {{ doc._expanded ? 'expand_less' : 'expand_more' }}
                    </span>
                  </div>

                  <div v-if="doc._expanded" class="document-content">
                    <table class="items-table">
                      <thead>
                        <tr>
                          <th>Item Description</th>
                          <th>Total Cost</th>
                          <th>Allocated To (Budget Line)</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="item in doc.items" :key="item.id">
                          <td class="item-name-cell">
                            <span class="text-white">{{ item.item_name }}</span>
                            <span v-if="item.sub_item" class="sub-item-text">({{ item.sub_item }})</span>
                          </td>
                          <td class="item-cost-cell font-mono">
                            ₱{{ formatCurrency(item.amount) }}
                          </td>
                          <td class="item-allocation-cell">
                            <select
                              v-if="item.amount > 0"
                              v-model="item.gpb_budget_line_id"
                              class="budget-line-select"
                              @change="markAllocationsDirty"
                            >
                              <option :value="null">-- Not Allocated --</option>
                              <option
                                v-for="bl in (currentAllocationStat?.budget_lines || [])"
                                :key="bl.id"
                                :value="bl.id"
                              >
                                {{ bl.label }} (₱{{ formatCurrency(bl.amount) }})
                              </option>
                            </select>
                            <span v-else class="text-muted text-xs">N/A</span>
                          </td>
                          <td class="item-status-cell">
                            <span v-if="item.amount <= 0" class="badge-status muted" title="This item has no cost to allocate.">
                              No Cost
                            </span>
                            <span v-else-if="item.gpb_budget_line_id" class="badge-status assigned">
                              ✓ Assigned
                            </span>
                            <span
                              v-else-if="getAllocatedElsewhere(item) >= item.amount"
                              class="badge-status locked"
                              title="This budget item has been fully assigned to other mandates. It cannot be assigned here unless unlinked from the other mandate first."
                            >
                              🔒 Locked
                            </span>
                            <span v-else class="badge-status unassigned">
                              Unassigned
                            </span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <div class="dirty-status-indicator">
            <span v-if="allocationsDirty" class="dirty-pill">
              <span class="pulse-dot"></span> Unsaved changes
            </span>
          </div>
          <div class="footer-buttons">
            <button @click="closeAllocationModal" class="btn-cancel">
              Cancel
            </button>
            <button
              @click="saveAllocations"
              :disabled="savingAllocations || !allocationsDirty"
              class="btn-save"
              :class="{ disabled: !allocationsDirty }"
            >
              <span v-if="savingAllocations" class="spinner-small"></span>
              {{ savingAllocations ? 'Saving Allocations...' : 'Save Allocations' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- PDF Document Preview Modal -->
    <PdfPreviewModal
      :isOpen="isPdfModalOpen"
      :fileUrl="pdfFileUrl"
      @close="closePdfModal"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import Swal from 'sweetalert2';
import api, { getFileUrl } from '../api';
import PdfPreviewModal from './PdfPreviewModal.vue';

const props = defineProps({
  role: {
    type: String,
    default: 'admin'
  }
});

const mandateStats = ref([]);
const mandateStatsFilter = ref('all');
const searchQuery = ref('');
const loadingStats = ref(true);

const showAllocationModal = ref(false);
const loadingAllocations = ref(false);
const savingAllocations = ref(false);
const allocationsData = ref([]);
const currentAllocationStat = ref(null);
const allocationsDirty = ref(false);

const isPdfModalOpen = ref(false);
const pdfFileUrl = ref('');

// Computed Totals for Top Summary Metrics
const overallTotals = computed(() => {
  let budget = 0;
  let utilized = 0;
  let pending = 0;
  let remaining = 0;

  for (const s of mandateStats.value) {
    budget += parseFloat(s.budget) || 0;
    utilized += parseFloat(s.utilized_budget) || 0;
    pending += parseFloat(s.pending_budget) || 0;
    remaining += parseFloat(s.remaining_budget) || 0;
  }

  return { budget, utilized, pending, remaining };
});

// Filtered Mandate Statistics (handles Classification & Search Query)
const filteredMandateStats = computed(() => {
  let result = mandateStats.value;

  if (mandateStatsFilter.value !== 'all') {
    result = result.filter(s => s.classification === mandateStatsFilter.value);
  }

  if (searchQuery.value && searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim();
    result = result.filter(s =>
      (s.mandate && s.mandate.toLowerCase().includes(q)) ||
      (s.cause && s.cause.toLowerCase().includes(q)) ||
      (s.activity && s.activity.toLowerCase().includes(q))
    );
  }

  return result;
});

// Verified Totals from ARs inside the modal
const arVerifiedTotals = computed(() => {
  const totals = {};
  for (const doc of allocationsData.value) {
    if (doc.type === 'AR' && doc.items) {
      for (const item of doc.items) {
        const name = item.item_name || 'Unspecified Item';
        if (!totals[name]) {
          totals[name] = 0;
        }
        totals[name] += parseFloat(item.amount) || 0;
      }
    }
  }
  return Object.entries(totals).map(([name, amount]) => ({ name, amount }));
});

const formatCurrency = (val) => {
  return Number(val || 0).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
};

const getClassificationLabel = (cls) => {
  switch (cls) {
    case 'client':
      return 'Client-Focused';
    case 'org':
      return 'Organization-Focused';
    case 'attributed':
      return 'Attributed Program';
    default:
      return 'General Mandate';
  }
};

const resetFilters = () => {
  mandateStatsFilter.value = 'all';
  searchQuery.value = '';
};

const fetchMandateStats = async () => {
  loadingStats.value = true;
  try {
    const response = await api.get('/plan/mandate-statistics');
    if (response.data && response.data.success) {
      mandateStats.value = response.data.data || [];
    } else {
      mandateStats.value = [];
    }
  } catch (err) {
    console.error('Failed to fetch mandate stats', err);
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: 'Failed to load mandate statistics',
      showConfirmButton: false,
      timer: 3000
    });
  } finally {
    loadingStats.value = false;
  }
};

const openAllocationModal = async (stat) => {
  currentAllocationStat.value = stat;
  showAllocationModal.value = true;
  loadingAllocations.value = true;
  allocationsDirty.value = false;
  allocationsData.value = [];

  try {
    const res = await api.get(`/plan/mandate-allocations?gpb_ids=${stat.gpb_ids.join(',')}`);
    if (res.data && res.data.success) {
      allocationsData.value = (res.data.data || []).map(d => ({ ...d, _expanded: true }));
    } else {
      Swal.fire('Error', res.data.message || 'Failed to load allocations.', 'error');
    }
  } catch (err) {
    Swal.fire('Error', 'Network error while loading allocations.', 'error');
  } finally {
    loadingAllocations.value = false;
  }
};

const closeAllocationModal = () => {
  if (allocationsDirty.value) {
    Swal.fire({
      title: 'Discard unsaved changes?',
      text: 'You have pending allocation changes that have not been saved.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#475569',
      confirmButtonText: 'Discard Changes',
      cancelButtonText: 'Stay on Modal'
    }).then((result) => {
      if (result.isConfirmed) {
        showAllocationModal.value = false;
        currentAllocationStat.value = null;
        allocationsDirty.value = false;
      }
    });
  } else {
    showAllocationModal.value = false;
    currentAllocationStat.value = null;
  }
};

const markAllocationsDirty = () => {
  allocationsDirty.value = true;
};

const getAllocatedElsewhere = (item) => {
  if (!item.allocations || !currentAllocationStat.value) return 0;
  return item.allocations.reduce((sum, al) => {
    if (!currentAllocationStat.value.gpb_ids.includes(parseInt(al.mandate_id))) {
      return sum + parseFloat(al.allocated_amount);
    }
    return sum;
  }, 0);
};

const saveAllocations = async () => {
  if (!currentAllocationStat.value) return;

  savingAllocations.value = true;

  const flatAllocs = [];
  for (const doc of allocationsData.value) {
    if (doc.items) {
      for (const item of doc.items) {
        let val = 0;
        let gpbLineId = item.gpb_budget_line_id;

        if (gpbLineId) {
          val = parseFloat(item.amount) || 0;
        }

        flatAllocs.push({
          budget_item_id: item.id,
          item_type: doc.type,
          allocated_amount: val,
          gpb_budget_line_id: gpbLineId
        });
      }
    }
  }

  try {
    const res = await api.post('/plan/mandate-allocations', {
      gpb_ids: currentAllocationStat.value.gpb_ids,
      allocations: flatAllocs
    });
    if (res.data && res.data.success) {
      allocationsDirty.value = false;
      showAllocationModal.value = false;
      currentAllocationStat.value = null;
      await fetchMandateStats();
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Allocations saved successfully',
        showConfirmButton: false,
        timer: 3000
      });
    } else {
      Swal.fire('Error', res.data.message || 'Failed to save allocations.', 'error');
    }
  } catch (err) {
    Swal.fire('Error', 'Network error while saving allocations.', 'error');
  } finally {
    savingAllocations.value = false;
  }
};

const openDocumentPreview = (attachment, type) => {
  if (attachment) {
    let fileName = attachment;
    if (typeof attachment === 'string' && attachment.startsWith('[')) {
      try {
        const parsed = JSON.parse(attachment);
        if (parsed.length > 0) fileName = parsed[0];
      } catch (e) {}
    }
    pdfFileUrl.value = getFileUrl('archived', fileName);
    isPdfModalOpen.value = true;
  }
};

const closePdfModal = () => {
  isPdfModalOpen.value = false;
  pdfFileUrl.value = '';
};

onMounted(() => {
  fetchMandateStats();
});
</script>

<style scoped>
.manage-allocation-container {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  width: 100%;
}

/* Page Header Card */
.page-header-card {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
  border: 1px solid rgba(185, 121, 204, 0.15);
  border-radius: 1rem;
  padding: 1.5rem 1.75rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
  backdrop-filter: blur(8px);
}

.header-main-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1.25rem;
  flex-wrap: wrap;
  margin-bottom: 1.5rem;
}

.breadcrumb-trail {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.85rem;
  color: rgba(203, 213, 225, 0.65);
  margin-bottom: 8px;
}

.breadcrumb-link {
  color: rgba(203, 213, 225, 0.65);
  text-decoration: none;
  transition: color 0.2s;
}

.breadcrumb-link:hover {
  color: #b979cc;
}

.breadcrumb-separator {
  color: rgba(185, 121, 204, 0.4);
}

.breadcrumb-current {
  color: #ffffff;
  font-weight: 600;
}

.page-title {
  font-size: 1.5rem;
  font-weight: 800;
  color: #ffffff;
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 0 0 8px 0;
  letter-spacing: -0.02em;
}

.title-icon {
  font-family: 'Material Symbols Outlined';
  font-size: 1.5rem;
  color: #b979cc;
  background: rgba(153, 13, 209, 0.15);
  border: 1px solid rgba(185, 121, 204, 0.25);
  padding: 8px;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.page-subtitle {
  font-size: 0.92rem;
  color: rgba(203, 213, 225, 0.75);
  margin: 0;
  max-width: 800px;
  line-height: 1.5;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.btn-action {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 0.625rem 1.25rem;
  border-radius: 0.75rem;
  font-size: 0.9rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.btn-action.primary {
  background: linear-gradient(135deg, #990dd1 0%, #b979cc 100%);
  color: white;
  border: none;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.15);
}

.btn-action.primary:hover:not(:disabled) {
  opacity: 0.9;
  transform: translateY(-1px);
}

.btn-action.outline {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(185, 121, 204, 0.25);
  color: rgba(203, 213, 225, 0.85);
}

.btn-action.outline:hover {
  background: rgba(153, 13, 209, 0.15);
  border-color: rgba(185, 121, 204, 0.4);
  color: #ffffff;
  transform: translateY(-1px);
}

.btn-action:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.spin-anim {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Metrics Grid - Match BudgetView Stat Cards */
.metrics-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  padding-top: 1.25rem;
  border-top: 1px solid rgba(185, 121, 204, 0.15);
}

.metric-card {
  padding: 1.25rem;
  border-radius: 1rem;
  border: 1px solid rgba(185, 121, 204, 0.15);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(8px);
  transition: all 0.3s;
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.metric-card:hover {
  transform: translateY(-2px);
  border-color: rgba(185, 121, 204, 0.3);
}

.metric-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.metric-icon-box.blue {
  background: rgba(59, 130, 246, 0.1);
  color: #60a5fa;
}

.metric-icon-box.green {
  background: rgba(34, 197, 94, 0.1);
  color: #4ade80;
}

.metric-icon-box.amber {
  background: rgba(245, 158, 11, 0.1);
  color: #fbbf24;
}

.metric-icon-box.purple {
  background: rgba(153, 13, 209, 0.1);
  color: #b979cc;
}

.metric-details {
  display: flex;
  flex-direction: column;
}

.metric-label {
  font-size: 0.82rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: rgba(203, 213, 225, 0.7);
  margin-bottom: 0.25rem;
}

.metric-value {
  font-size: 1.25rem;
  font-weight: 900;
  letter-spacing: -0.025em;
  color: white;
  line-height: 1.25;
  margin: 0;
  font-family: monospace;
}

/* Toolbar Card */
.toolbar-card {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
  border: 1px solid rgba(185, 121, 204, 0.15);
  border-radius: 1rem;
  padding: 1rem 1.25rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.toolbar-left {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-grow: 1;
  flex-wrap: wrap;
}

.search-input-box {
  position: relative;
  min-width: 280px;
  flex: 1;
  max-width: 500px;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: rgba(203, 213, 225, 0.6);
  font-size: 1.2rem;
  pointer-events: none;
}

.search-input {
  width: 100%;
  background: rgba(0, 0, 0, 0.25);
  border: 1px solid rgba(185, 121, 204, 0.2);
  color: #ffffff;
  padding: 9px 36px 9px 38px;
  border-radius: 8px;
  font-size: 0.88rem;
  outline: none;
  transition: all 0.2s;
}

.search-input:focus {
  border-color: #b979cc;
  box-shadow: 0 0 0 2px rgba(185, 121, 204, 0.25);
}

.clear-search-btn {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: transparent;
  border: none;
  color: rgba(203, 213, 225, 0.6);
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
}

.clear-search-btn:hover {
  color: #ffffff;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 8px;
}

.filter-label {
  color: rgba(203, 213, 225, 0.7);
  font-size: 0.85rem;
  white-space: nowrap;
}

.filter-select {
  background: rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(185, 121, 204, 0.2);
  color: #ffffff;
  padding: 8px 14px;
  border-radius: 8px;
  outline: none;
  font-size: 0.88rem;
  cursor: pointer;
}

.filter-select:focus {
  border-color: #b979cc;
}

.filter-select option {
  background: #16213e;
  color: #ffffff;
}

.mandate-counter-badge {
  font-size: 0.85rem;
  color: #b979cc;
  background: rgba(153, 13, 209, 0.1);
  padding: 6px 14px;
  border-radius: 8px;
  border: 1px solid rgba(185, 121, 204, 0.2);
}

/* State Cards */
.state-card {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
  border: 1px solid rgba(185, 121, 204, 0.15);
  border-radius: 1rem;
  padding: 60px 24px;
  text-align: center;
  color: rgba(203, 213, 225, 0.7);
}

.spinner-large {
  width: 48px;
  height: 48px;
  border: 3px solid rgba(185, 121, 204, 0.2);
  border-top-color: #b979cc;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 16px auto;
}

.spinner-medium {
  width: 32px;
  height: 32px;
  border: 3px solid rgba(185, 121, 204, 0.2);
  border-top-color: #b979cc;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 12px auto;
}

.spinner-small {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  display: inline-block;
  margin-right: 6px;
  vertical-align: middle;
}

.state-card h3 {
  color: #ffffff;
  font-size: 1.25rem;
  margin-bottom: 8px;
}

.empty-icon {
  font-size: 3rem;
  display: block;
  margin-bottom: 12px;
}

/* Mandates Grid - Exactly match original previous layout */
.mandates-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 20px;
}

.mandate-card {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
  border-radius: 1rem;
  padding: 1.5rem;
  border: 1px solid rgba(185, 121, 204, 0.15);
  display: flex;
  flex-direction: column;
  gap: 16px;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.mandate-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 25px -4px rgba(0, 0, 0, 0.25);
  border-color: rgba(185, 121, 204, 0.35);
}

/* Narrative Blocks */
.narrative-blocks {
  display: flex;
  flex-direction: column;
  gap: 12px;
  flex: 1;
}

.narrative-block {
  background: rgba(0, 0, 0, 0.25);
  padding: 12px 14px;
  border-radius: 8px;
  border-left-width: 3px;
  border-left-style: solid;
}

.narrative-block.mandate-block {
  border-left-color: #6366f1;
}

.narrative-block.cause-block {
  border-left-color: #8b5cf6;
}

.narrative-block.activity-block {
  border-left-color: #ec4899;
}

.narrative-tag {
  font-size: 0.7rem;
  color: #94a3b8;
  text-transform: uppercase;
  font-weight: 700;
  margin-bottom: 4px;
  letter-spacing: 0.5px;
}

.narrative-text.primary {
  font-size: 0.95rem;
  color: #ffffff;
  font-weight: 600;
  line-height: 1.4;
}

.narrative-text.secondary {
  font-size: 0.85rem;
  color: #cbd5e1;
  line-height: 1.4;
}

/* Stats Panel inside Mandate Card */
.stats-panel {
  background: rgba(0, 0, 0, 0.25);
  border-radius: 8px;
  padding: 16px;
  border: 1px solid rgba(185, 121, 204, 0.1);
}

.approval-counters {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 12px;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(185, 121, 204, 0.1);
}

.counter-box {
  text-align: center;
  padding: 8px;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 6px;
}

.counter-label {
  font-size: 0.7rem;
  color: #94a3b8;
  text-transform: uppercase;
  font-weight: 700;
}

.counter-number {
  font-size: 1.15rem;
  color: #ffffff;
  font-weight: 700;
}

.financial-lines {
  display: flex;
  flex-direction: column;
  gap: 8px;
  font-size: 0.85rem;
  color: #cbd5e1;
}

.financial-line {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.financial-line .label {
  font-weight: 500;
  color: #cbd5e1;
}

.financial-line .value {
  font-family: monospace;
  font-size: 0.95rem;
}

.financial-line.remaining-line {
  font-weight: 700;
  padding-top: 8px;
  border-top: 1px dashed rgba(185, 121, 204, 0.2);
  margin-top: 4px;
}

.financial-line.remaining-line .label {
  text-transform: uppercase;
  font-size: 0.75rem;
}

.financial-line.remaining-line .value {
  font-size: 1.05rem;
}

/* Budget Lines Breakdown */
.budget-lines-container {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid rgba(185, 121, 204, 0.15);
}

.budget-lines-title {
  font-size: 0.75rem;
  color: #94a3b8;
  text-transform: uppercase;
  font-weight: 700;
  margin-bottom: 12px;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.budget-lines-count {
  color: #b979cc;
}

.budget-lines-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  max-height: 180px;
  overflow-y: auto;
}

.budget-line-item {
  background: rgba(0, 0, 0, 0.3);
  border-radius: 6px;
  padding: 10px;
  border: 1px solid rgba(185, 121, 204, 0.1);
  font-size: 0.8rem;
}

.line-name {
  color: #ffffff;
  font-weight: 600;
  margin-bottom: 6px;
}

.line-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 2px;
  color: #cbd5e1;
}

.line-sublabel {
  color: #94a3b8;
}

.line-amount {
  font-family: monospace;
}

/* Manage Allocations Button on Mandate Card */
.btn-manage-allocation {
  width: 100%;
  padding: 11px;
  background: rgba(59, 130, 246, 0.15);
  border: 1px solid rgba(59, 130, 246, 0.4);
  color: #93c5fd;
  font-weight: 600;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-manage-allocation:hover {
  background: rgba(59, 130, 246, 0.25);
  color: #bfdbfe;
  border-color: rgba(59, 130, 246, 0.6);
  transform: translateY(-1px);
}

/* Utility Colors matching site system */
.text-green { color: #10b981 !important; }
.text-amber { color: #f59e0b !important; }
.text-blue { color: #3b82f6 !important; }
.text-cyan { color: #38bdf8 !important; }
.text-red { color: #ef4444 !important; }
.text-white { color: #ffffff !important; }
.text-muted { color: #94a3b8 !important; }
.budget-val { color: #ffffff !important; }

/* Modal Styling matching original site theme */
.modal-backdrop {
  z-index: 1000;
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.modal-card {
  width: 100%;
  max-width: 860px;
  max-height: 92vh;
  display: flex;
  flex-direction: column;
  background: #1e293b;
  border-radius: 14px;
  border: 1px solid rgba(185, 121, 204, 0.2);
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
  overflow: hidden;
}

.modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid rgba(185, 121, 204, 0.15);
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  background: rgba(0, 0, 0, 0.2);
}

.modal-title {
  margin: 0 0 4px 0;
  color: #ffffff;
  font-size: 1.25rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 10px;
}

.modal-title .material-symbols-outlined {
  color: #b979cc;
}

.modal-subtitle {
  color: #94a3b8;
  font-size: 0.85rem;
  margin: 0;
  line-height: 1.4;
}

.btn-close-modal {
  background: transparent;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 4px;
  border-radius: 6px;
  display: flex;
  align-items: center;
}

.btn-close-modal:hover {
  color: white;
  background: rgba(255, 255, 255, 0.1);
}

.target-mandate-banner {
  background: rgba(153, 13, 209, 0.1);
  border-bottom: 1px solid rgba(185, 121, 204, 0.2);
  padding: 12px 24px;
}

.banner-label {
  font-size: 0.72rem;
  color: #b979cc;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.banner-title {
  font-size: 0.92rem;
  color: #ffffff;
  font-weight: 600;
  margin-top: 2px;
}

.modal-body {
  padding: 24px;
  overflow-y: auto;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.modal-loading-box {
  padding: 40px;
  text-align: center;
  color: #94a3b8;
}

.modal-section {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.section-title {
  color: #ffffff;
  font-size: 0.95rem;
  font-weight: 700;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.table-responsive {
  width: 100%;
  overflow-x: auto;
  border: 1px solid rgba(185, 121, 204, 0.15);
  border-radius: 8px;
}

.modal-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.85rem;
}

.modal-table thead tr {
  background: rgba(0, 0, 0, 0.25);
  text-align: left;
  color: #94a3b8;
}

.modal-table th {
  padding: 10px 14px;
  font-weight: 600;
}

.modal-table td {
  padding: 10px 14px;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.no-documents-box {
  padding: 32px;
  text-align: center;
  color: #94a3b8;
  background: rgba(0, 0, 0, 0.2);
  border-radius: 8px;
  border: 1px dashed rgba(185, 121, 204, 0.2);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.documents-accordion {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.document-card {
  border: 1px solid rgba(185, 121, 204, 0.15);
  border-radius: 8px;
  overflow: hidden;
  background: rgba(0, 0, 0, 0.2);
}

.document-header {
  background: rgba(0, 0, 0, 0.25);
  padding: 12px 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;
  user-select: none;
  transition: background 0.2s;
}

.document-header:hover {
  background: rgba(0, 0, 0, 0.35);
}

.doc-header-info {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.doc-type-badge {
  font-weight: 700;
  font-size: 0.8rem;
}

.doc-type-badge.AR {
  color: #10b981;
}

.doc-type-badge.AD {
  color: #f59e0b;
}

.doc-title {
  color: #ffffff;
  font-weight: 600;
  font-size: 0.9rem;
}

.btn-preview-link {
  background: transparent;
  border: none;
  color: #3b82f6;
  cursor: pointer;
  text-decoration: underline;
  font-size: 0.8rem;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.btn-preview-link:hover {
  color: #93c5fd;
}

.chevron {
  color: #94a3b8;
  font-size: 1.2rem;
}

.document-content {
  padding: 14px;
  background: rgba(255, 255, 255, 0.02);
  overflow-x: auto;
}

.items-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.85rem;
}

.items-table th {
  padding: 8px 10px;
  text-align: left;
  color: #94a3b8;
  border-bottom: 1px solid rgba(185, 121, 204, 0.15);
  font-weight: 600;
}

.items-table td {
  padding: 10px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.04);
}

.item-name-cell {
  display: flex;
  flex-direction: column;
}

.sub-item-text {
  font-size: 0.75rem;
  color: #94a3b8;
}

.budget-line-select {
  width: 220px;
  padding: 6px 8px;
  background: #16213e;
  border: 1px solid rgba(185, 121, 204, 0.25);
  color: #ffffff;
  border-radius: 6px;
  outline: none;
  font-size: 0.82rem;
}

.budget-line-select:focus {
  border-color: #b979cc;
}

.badge-status {
  font-size: 0.72rem;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 4px;
  display: inline-block;
}

.badge-status.assigned {
  background: rgba(16, 185, 129, 0.15);
  color: #34d399;
}

.badge-status.locked {
  background: rgba(239, 68, 68, 0.15);
  color: #f87171;
}

.badge-status.unassigned {
  background: rgba(148, 163, 184, 0.15);
  color: #94a3b8;
}

.badge-status.muted {
  background: rgba(255, 255, 255, 0.05);
  color: #64748b;
}

/* Modal Footer */
.modal-footer {
  padding: 16px 24px;
  border-top: 1px solid rgba(185, 121, 204, 0.15);
  background: rgba(0, 0, 0, 0.2);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.dirty-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.78rem;
  color: #fbbf24;
  background: rgba(245, 158, 11, 0.15);
  padding: 4px 10px;
  border-radius: 20px;
  border: 1px solid rgba(245, 158, 11, 0.3);
}

.pulse-dot {
  width: 6px;
  height: 6px;
  background: #fbbf24;
  border-radius: 50%;
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.4; transform: scale(0.85); }
}

.footer-buttons {
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn-cancel {
  padding: 9px 18px;
  background: transparent;
  border: 1px solid rgba(185, 121, 204, 0.2);
  color: #cbd5e1;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.88rem;
  transition: all 0.2s;
}

.btn-cancel:hover {
  background: rgba(255, 255, 255, 0.05);
  color: white;
}

.btn-save {
  padding: 9px 22px;
  background: linear-gradient(135deg, #990dd1 0%, #b979cc 100%);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.88rem;
  font-weight: 600;
  transition: all 0.2s;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.15);
}

.btn-save:hover:not(:disabled) {
  opacity: 0.92;
  transform: translateY(-1px);
}

.btn-save.disabled,
.btn-save:disabled {
  opacity: 0.45;
  cursor: not-allowed;
  transform: none;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
  .metrics-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .header-main-row {
    flex-direction: column;
    align-items: stretch;
  }
  .header-actions {
    width: 100%;
    justify-content: flex-start;
  }
  .metrics-grid {
    grid-template-columns: 1fr;
  }
  .toolbar-card {
    flex-direction: column;
    align-items: stretch;
  }
  .mandates-grid {
    grid-template-columns: 1fr;
  }
}
</style>
