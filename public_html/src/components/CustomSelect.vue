<template>
  <div class="custom-select-wrapper" ref="wrapperRef">
    <!-- Trigger Button -->
    <div
      class="custom-select-trigger"
      :class="{ 
        'is-open': isOpen, 
        'is-disabled': disabled,
        'has-value': hasSelectedValue
      }"
      tabindex="0"
      @click="toggleDropdown"
      @keydown.down.prevent="openDropdown"
      @keydown.up.prevent="openDropdown"
      @keydown.enter.prevent="toggleDropdown"
      @keydown.esc="closeDropdown"
      :title="selectedLabel"
    >
      <span class="custom-select-label" :class="{ 'placeholder-text': !hasSelectedValue }">
        {{ selectedLabel || placeholder }}
      </span>
      <span class="custom-select-arrow" :class="{ 'rotate-180': isOpen }">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="arrow-svg">
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </span>
    </div>

    <!-- Dropdown Menu -->
    <transition name="dropdown-fade">
      <div v-if="isOpen" class="custom-select-menu">
        <!-- Search input -->
        <div v-if="searchable && normalizedOptions.length > 5" class="custom-select-search-box">
          <span class="material-symbols-outlined search-icon">search</span>
          <input
            ref="searchInputRef"
            v-model="searchQuery"
            type="text"
            class="custom-select-search-input"
            :placeholder="searchPlaceholder"
            @click.stop
            @keydown.esc="closeDropdown"
          />
          <button
            v-if="searchQuery"
            type="button"
            class="clear-search-btn"
            @click.stop="searchQuery = ''"
          >
            ×
          </button>
        </div>

        <!-- Options list -->
        <ul class="custom-select-options-list" role="listbox">
          <li
            v-for="(opt, idx) in filteredOptions"
            :key="opt.value !== undefined ? opt.value : idx"
            class="custom-select-option"
            :class="{ 'is-selected': isOptionSelected(opt.value) }"
            role="option"
            :aria-selected="isOptionSelected(opt.value)"
            @click="selectOption(opt)"
          >
            <span class="option-text">{{ opt.label }}</span>
            <span v-if="isOptionSelected(opt.value)" class="material-symbols-outlined check-icon">check</span>
          </li>
          <li v-if="filteredOptions.length === 0" class="no-options-found">
            {{ noResultsText }}
          </li>
        </ul>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number, Array],
    default: ''
  },
  options: {
    type: Array,
    default: () => []
  },
  placeholder: {
    type: String,
    default: 'Select...'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  searchable: {
    type: Boolean,
    default: true
  },
  searchPlaceholder: {
    type: String,
    default: 'Search...'
  },
  noResultsText: {
    type: String,
    default: 'No matching options'
  },
  required: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const isOpen = ref(false);
const searchQuery = ref('');
const wrapperRef = ref(null);
const searchInputRef = ref(null);

const normalizedOptions = computed(() => {
  if (!Array.isArray(props.options)) return [];
  return props.options.map(opt => {
    if (typeof opt === 'object' && opt !== null) {
      const val = opt.value !== undefined ? opt.value : opt.id;
      const text = opt.label || (opt.code ? `${opt.code} - ${opt.title}` : opt.title) || opt.name || String(val);
      return { value: val, label: text };
    }
    return { value: opt, label: String(opt) };
  });
});

const filteredOptions = computed(() => {
  if (!searchQuery.value.trim()) return normalizedOptions.value;
  const q = searchQuery.value.toLowerCase().trim();
  return normalizedOptions.value.filter(opt =>
    String(opt.label).toLowerCase().includes(q)
  );
});

const isOptionSelected = (val) => {
  if (props.modelValue === null || props.modelValue === undefined || props.modelValue === '') {
    return false;
  }
  return String(props.modelValue) === String(val);
};

const hasSelectedValue = computed(() => {
  return props.modelValue !== null && props.modelValue !== undefined && props.modelValue !== '';
});

const selectedLabel = computed(() => {
  if (!hasSelectedValue.value) return '';
  const found = normalizedOptions.value.find(opt => isOptionSelected(opt.value));
  return found ? found.label : (props.modelValue === 'Other' ? 'Other' : String(props.modelValue));
});

const toggleDropdown = () => {
  if (props.disabled) return;
  if (isOpen.value) {
    closeDropdown();
  } else {
    openDropdown();
  }
};

const openDropdown = () => {
  if (props.disabled) return;
  isOpen.value = true;
  searchQuery.value = '';
  nextTick(() => {
    if (searchInputRef.value) {
      searchInputRef.value.focus();
    }
  });
};

const closeDropdown = () => {
  isOpen.value = false;
  searchQuery.value = '';
};

const selectOption = (opt) => {
  emit('update:modelValue', opt.value);
  emit('change', opt.value);
  closeDropdown();
};

const handleClickOutside = (e) => {
  if (isOpen.value && wrapperRef.value && !wrapperRef.value.contains(e.target)) {
    closeDropdown();
  }
};

onMounted(() => {
  document.addEventListener('pointerdown', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('pointerdown', handleClickOutside);
});

watch(() => props.disabled, (newVal) => {
  if (newVal) closeDropdown();
});
</script>

<style scoped>
.custom-select-wrapper {
  position: relative;
  width: 100%;
  min-width: 0;
}

.custom-select-trigger {
  width: 100%;
  min-width: 0;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  padding: 12px 44px 12px 18px;
  font-size: 14px;
  color: #ffffff;
  cursor: pointer;
  display: flex;
  align-items: center;
  position: relative;
  transition: all 0.2s ease;
  user-select: none;
  min-height: 48px;
  box-sizing: border-box;
}

.custom-select-trigger:focus-visible,
.custom-select-trigger.is-open {
  background: rgba(255, 255, 255, 0.05);
  border-color: #b979cc;
  outline: none;
  box-shadow: 0 0 0 2px rgba(153, 13, 209, 0.2);
}

.custom-select-trigger.is-disabled {
  opacity: 0.6;
  cursor: not-allowed;
  background: rgba(255, 255, 255, 0.02);
  pointer-events: none;
}

.custom-select-label {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  width: 100%;
  min-width: 0;
  line-height: 1.4;
  word-break: break-word;
  color: #ffffff;
  font-size: 13px;
}

.custom-select-label.placeholder-text {
  color: #94a3b8;
  display: block;
  white-space: nowrap;
}

.custom-select-arrow {
  position: absolute;
  right: 18px;
  top: 50%;
  transform: translateY(-50%);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.25s ease;
  color: #b979cc;
  pointer-events: none;
}

.custom-select-arrow.rotate-180 {
  transform: translateY(-50%) rotate(180deg);
}

.arrow-svg {
  width: 16px;
  height: 16px;
  stroke: #b979cc;
}

.custom-select-menu {
  position: absolute;
  top: calc(100% + 6px);
  left: 0;
  right: 0;
  width: 100%;
  max-width: 100%;
  background: #16213e;
  border: 1px solid rgba(185, 121, 204, 0.35);
  border-radius: 12px;
  box-shadow: 0 16px 36px rgba(5, 5, 15, 0.65), 0 0 0 1px rgba(185, 121, 204, 0.15);
  z-index: 999;
  overflow: hidden;
  box-sizing: border-box;
}

.custom-select-search-box {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(0, 0, 0, 0.25);
  gap: 8px;
}

.search-icon {
  font-size: 18px;
  color: #b979cc;
  flex-shrink: 0;
}

.custom-select-search-input {
  width: 100%;
  background: transparent;
  border: none;
  outline: none;
  font-size: 13px;
  color: #ffffff;
  padding: 4px 0;
}

.custom-select-search-input::placeholder {
  color: #64748b;
}

.clear-search-btn {
  background: transparent;
  border: none;
  color: #94a3b8;
  font-size: 16px;
  cursor: pointer;
  line-height: 1;
  padding: 2px 6px;
  border-radius: 4px;
}

.clear-search-btn:hover {
  color: #ffffff;
  background: rgba(255, 255, 255, 0.1);
}

.custom-select-options-list {
  list-style: none;
  margin: 0;
  padding: 6px 0;
  max-height: 240px;
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: rgba(185, 121, 204, 0.5) rgba(0, 0, 0, 0.2);
}

.custom-select-options-list::-webkit-scrollbar {
  width: 6px;
}

.custom-select-options-list::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.2);
}

.custom-select-options-list::-webkit-scrollbar-thumb {
  background: rgba(185, 121, 204, 0.5);
  border-radius: 3px;
}

.custom-select-option {
  padding: 10px 16px;
  font-size: 13px;
  line-height: 1.45;
  color: #cbd5e1;
  cursor: pointer;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  transition: background 0.15s ease, color 0.15s ease;
  user-select: none;
  border-bottom: 1px solid rgba(255, 255, 255, 0.03);
}

.custom-select-option:last-child {
  border-bottom: none;
}

.custom-select-option:hover {
  background: rgba(185, 121, 204, 0.15);
  color: #ffffff;
}

.custom-select-option.is-selected {
  background: rgba(185, 121, 204, 0.25);
  color: #f3e8ff;
  font-weight: 600;
}

.option-text {
  flex: 1;
  white-space: normal;
  word-break: break-word;
}

.check-icon {
  font-size: 16px;
  color: #b979cc;
  flex-shrink: 0;
  margin-top: 2px;
}

.no-options-found {
  padding: 16px;
  text-align: center;
  color: #94a3b8;
  font-size: 13px;
  font-style: italic;
}

/* Animations */
.dropdown-fade-enter-active,
.dropdown-fade-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}

.dropdown-fade-enter-from,
.dropdown-fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
