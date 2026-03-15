<template>
  <div class="searchable-select-container" :class="{ 'is-open': isOpen, 'is-rtl': isRtl }" ref="containerRef">
    <div class="select-label" v-if="label">{{ label }}</div>
    
    <div 
      class="select-trigger" 
      @click="toggleDropdown" 
      :class="{ 'has-value': modelValue, 'has-error': error }"
    >
      <div class="selected-content">
        <span v-if="selectedLabel" class="selected-text">{{ selectedLabel }}</span>
        <span v-else-if="modelValue && allowCustom" class="selected-text">{{ modelValue }}</span>
        <span v-else class="placeholder-text">{{ placeholder }}</span>
      </div>
      
    <div class="trigger-actions">
        <button 
          v-if="clearable && modelValue" 
          type="button" 
          class="clear-btn" 
          @click.stop="clearSelection"
        >
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
        <div class="chevron-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
        </div>
      </div>
    </div>
    <div v-if="error && typeof error === 'string'" class="error-msg-text">{{ error }}</div>
    <div class="help-text-slot" v-if="$slots.help">
      <slot name="help" />
    </div>

    <Teleport to="body">
      <Transition name="dropdown-fade">
        <div 
          v-if="isOpen" 
          class="dropdown-panel-portal" 
          :class="{ 'is-rtl': isRtl }"
          :style="dropdownStyle"
        >
          <div class="search-wrapper">
            <div class="search-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            </div>
            <input 
              ref="searchInput"
              v-model="searchQuery" 
              type="text" 
              class="search-input" 
              :placeholder="t('Search...', 'بحث...')"
              @keydown.down.prevent="navigateOptions(1)"
              @keydown.up.prevent="navigateOptions(-1)"
              @keydown.enter.prevent="selectHighlighted"
              @keydown.esc="closeDropdown"
            >
          </div>

          <div class="options-list" ref="optionsListRef">
            <template v-if="filteredOptions.length">
              <div 
                v-for="(option, index) in filteredOptions" 
                :key="getOptionValue(option)" 
                class="option-item"
                :class="{ 
                  'is-selected': modelValue === getOptionValue(option),
                  'is-highlighted': highlightedIndex === index
                }"
                @click="selectOption(option)"
                @mouseenter="highlightedIndex = index"
              >
                <span class="option-label">{{ getOptionLabel(option) }}</span>
                <div v-if="modelValue === getOptionValue(option)" class="check-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                </div>
              </div>
            </template>
            <div v-else-if="allowCustom && searchQuery" class="option-item custom-entry" @click="selectCustom">
              <span class="option-label">{{ t('Use custom:', 'استخدم مخصص:') }} "{{ searchQuery }}"</span>
            </div>
            <div v-else class="no-results">
              {{ searchQuery ? t('No results found', 'لم يتم العثور على نتائج') : (noResultsMessage || t('No options available', 'لا توجد خيارات متاحة')) }}
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';

const props = defineProps({
  modelValue: [String, Number],
  options: {
    type: Array,
    default: () => []
  },
  label: String,
  placeholder: {
    type: String,
    default: 'Select option'
  },
  clearable: {
    type: Boolean,
    default: true
  },
  error: [String, Boolean],
  isRtl: {
    type: Boolean,
    default: false
  },
  allowCustom: {
    type: Boolean,
    default: false
  },
  noResultsMessage: String
});

const emit = defineEmits(['update:modelValue', 'change']);

const isOpen = ref(false);
const searchQuery = ref('');
const highlightedIndex = ref(-1);
const containerRef = ref(null);
const searchInput = ref(null);
const optionsListRef = ref(null);
const dropdownStyle = ref({ top: '0px', left: '0px', width: '0px' });

const updateDropdownPosition = () => {
  if (!containerRef.value) return;
  const rect = containerRef.value.getBoundingClientRect();
  dropdownStyle.value = {
    top: `${rect.bottom + window.scrollY + 8}px`,
    left: `${rect.left + window.scrollX}px`,
    width: `${rect.width}px`
  };
};

const t = (en, ar) => props.isRtl ? ar : en;

const getOptionValue = (option) => {
  if (typeof option === 'object') {
    return option.id !== undefined ? option.id : option.value;
  }
  return option;
};

const getOptionLabel = (option) => {
  if (typeof option === 'object') {
    return option.name || option.label || option.text;
  }
  return option;
};

const selectedLabel = computed(() => {
  const options = props.options || [];
  const option = options.find(opt => getOptionValue(opt) === props.modelValue);
  return option ? getOptionLabel(option) : '';
});

const filteredOptions = computed(() => {
  const options = props.options || [];
  if (!searchQuery.value) return options;
  const q = searchQuery.value.toLowerCase();
  return options.filter(opt => 
    getOptionLabel(opt).toLowerCase().includes(q)
  );
});

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    updateDropdownPosition();
    searchQuery.value = '';
    highlightedIndex.value = filteredOptions.value.findIndex(opt => getOptionValue(opt) === props.modelValue);
    nextTick(() => {
      searchInput.value?.focus();
    });
  }
};

const closeDropdown = () => {
  isOpen.value = false;
};

const selectOption = (option) => {
  const value = getOptionValue(option);
  emit('update:modelValue', value);
  emit('change', value);
  closeDropdown();
};

const selectCustom = () => {
  emit('update:modelValue', searchQuery.value);
  emit('change', searchQuery.value);
  closeDropdown();
};

const clearSelection = () => {
  emit('update:modelValue', '');
  emit('change', '');
};

const navigateOptions = (direction) => {
  if (!isOpen.value) {
    toggleDropdown();
    return;
  }
  
  const count = filteredOptions.value.length;
  if (count === 0) return;

  highlightedIndex.value = (highlightedIndex.value + direction + count) % count;
  
  nextTick(() => {
    const el = optionsListRef.value?.children[highlightedIndex.value];
    if (el) {
      el.scrollIntoView({ block: 'nearest' });
    }
  });
};

const selectHighlighted = () => {
  if (highlightedIndex.value >= 0 && highlightedIndex.value < filteredOptions.value.length) {
    selectOption(filteredOptions.value[highlightedIndex.value]);
  } else if (props.allowCustom && searchQuery.value) {
    selectCustom();
  }
};

const handleClickOutside = (event) => {
  if (containerRef.value && !containerRef.value.contains(event.target)) {
    closeDropdown();
  }
};

onMounted(() => {
  document.addEventListener('mousedown', handleClickOutside);
  window.addEventListener('resize', updateDropdownPosition);
  window.addEventListener('scroll', updateDropdownPosition, true);
});

onUnmounted(() => {
  document.removeEventListener('mousedown', handleClickOutside);
  window.removeEventListener('resize', updateDropdownPosition);
  window.removeEventListener('scroll', updateDropdownPosition, true);
});

watch(isOpen, (val) => {
  if (!val) {
    searchQuery.value = '';
    highlightedIndex.value = -1;
  }
});
</script>

<style scoped>
.searchable-select-container {
  position: relative;
  width: 100%;
}

.select-label {
  display: block;
  font-size: 0.8125rem;
  font-weight: 700;
  color: #475569;
  margin-bottom: 0.5rem;
}
.help-text-slot {
  margin-top: 0.4rem;
}

.select-trigger {
  width: 100%;
  padding: 0.75rem 1rem;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  min-height: 46px;
}

.select-trigger:hover {
  border-color: #cbd5e1;
  background: #f8fafc;
}

.is-open .select-trigger {
  border-color: #0ea5e9;
  box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
  background: white;
}

.selected-text {
  font-size: 0.9375rem;
  color: #1a3a5f;
  font-weight: 500;
}

.placeholder-text {
  font-size: 0.9375rem;
  color: #94a3b8;
}

.trigger-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #94a3b8;
}

.clear-btn {
  background: none;
  border: none;
  padding: 4px;
  cursor: pointer;
  color: #94a3b8;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.2s;
}

.clear-btn:hover {
  background: #f1f5f9;
  color: #ef4444;
}

.chevron-icon {
  transition: transform 0.3s ease;
}

.is-open .chevron-icon {
  transform: rotate(180deg);
}

.dropdown-panel-portal {
  position: absolute;
  background: white;
  border: 1px solid #f1f5f9;
  border-radius: 16px;
  box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
  z-index: 9999;
  overflow: hidden;
  transform-origin: top;
}

.search-wrapper {
  padding: 12px;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  gap: 10px;
  background: #f8fafc;
}

.search-icon {
  color: #94a3b8;
  flex-shrink: 0;
}

.search-input {
  width: 100%;
  border: none;
  background: transparent;
  padding: 4px 0;
  font-size: 0.875rem;
  color: #1a3a5f;
  outline: none;
}

.options-list {
  max-height: 240px;
  overflow-y: auto;
  padding: 6px;
}

.option-item {
  padding: 10px 12px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: all 0.15s;
  font-size: 0.9375rem;
  color: #475569;
}

.option-item:hover, .is-highlighted {
  background: #f1f5f9;
  color: #1e293b;
}

.is-selected {
  background: #f0f9ff !important;
  color: #0ea5e9 !important;
  font-weight: 600;
}

.check-icon {
  color: #0ea5e9;
}

.no-results {
  padding: 24px;
  text-align: center;
  color: #94a3b8;
  font-size: 0.875rem;
}

/* Animations */
.dropdown-fade-enter-active {
  animation: dropdown-in 0.25s cubic-bezier(0.2, 1, 0.2, 1);
}
.dropdown-fade-leave-active {
  animation: dropdown-in 0.2s cubic-bezier(0.2, 1, 0.2, 1) reverse;
}

@keyframes dropdown-in {
  from {
    opacity: 0;
    transform: translateY(-8px) scaleY(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scaleY(1);
  }
}

/* RTL Support */
.is-rtl .selected-content {
  text-align: right;
}
.is-rtl .search-wrapper {
  flex-direction: row-reverse;
}
.is-rtl .search-input {
  text-align: right;
}
.is-rtl .option-item {
  flex-direction: row-reverse;
  text-align: right;
}

/* Scrollbar styling */
.options-list::-webkit-scrollbar {
  width: 6px;
}
.options-list::-webkit-scrollbar-track {
  background: transparent;
}
.options-list::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.options-list::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}

.error-msg-text {
  font-size: 0.75rem;
  color: #ef4444;
  font-weight: 600;
  margin-top: 0.375rem;
  display: block;
}

.has-error {
  border-color: #fca5a5 !important;
  background-color: #fffbfa !important;
}
</style>
