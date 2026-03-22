<template>
  <div class="mb-6 w-full">
    <div class="mb-1.5 flex items-center justify-between">
      <label class="block text-[13px] font-medium text-neutral-900">
        {{ label }} <span v-if="required" class="text-red-500">*</span>
      </label>
    </div>

    <!-- Primary Approach: Segmented Buttons -->
    <div
      class="custom-scrollbar -mx-1 flex items-center gap-2 overflow-x-auto px-1 pb-1"
      :class="{ '-m-1 rounded-xl p-1 ring-1 ring-red-500': error }"
    >
      <button
        v-for="year in visibleYears"
        :key="year"
        type="button"
        class="relative flex h-10 min-w-[72px] shrink-0 items-center justify-center rounded-xl border px-3 text-sm font-bold transition-all duration-200 outline-none select-none"
        :class="[
          modelValue === year
            ? 'border-transparent bg-blue-600 text-white shadow-[0_2px_10px_rgba(37,99,235,0.25)] ring-2 ring-blue-600/20'
            : 'border-neutral-200 bg-white text-neutral-600 hover:border-blue-300 hover:bg-blue-50 focus:border-blue-400 focus:bg-blue-50',
          error && modelValue !== year ? 'border-red-200' : '',
        ]"
        @click="selectYear(year)"
      >
        <span class="relative z-10 flex items-center gap-1.5">
          {{ year }}
          <svg
            v-if="modelValue === year"
            class="animate-zoom-in h-3.5 w-3.5"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="3"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </span>
      </button>

      <!-- Advanced / More Modal Trigger -->
      <button
        v-if="hasMoreYears"
        type="button"
        class="flex h-10 min-w-[72px] shrink-0 items-center justify-center rounded-xl border border-dashed border-neutral-300 bg-neutral-50 px-3 text-sm font-bold text-neutral-500 transition-all hover:border-neutral-400 hover:bg-neutral-100 hover:text-neutral-700"
        :class="{ 'pointer-events-none hidden scale-95 opacity-0': showModal }"
        @click="openModal"
      >
        {{ t('+ More', '+ المزيد') }}
      </button>
    </div>

    <!-- Feedback text -->
    <span v-if="error" class="mt-1.5 block text-xs font-semibold text-red-500">{{ error }}</span>
    <span v-else-if="helpText" class="mt-1.5 block text-xs text-neutral-500">{{ helpText }}</span>

    <!-- Mobile Bottom Sheet / Modal Fallback -->
    <Teleport to="body">
      <Transition name="sheet-fade">
        <div
          v-if="showModal"
          class="animate-fade-in fixed inset-0 z-50 flex items-end justify-center sm:items-center"
          @click.self="closeModal"
        >
          <div class="absolute inset-0 bg-slate-900/30 backdrop-blur-sm" @click="closeModal"></div>
          <div
            class="animate-slide-up relative w-full transform rounded-t-3xl bg-white p-6 shadow-2xl transition-transform duration-300 sm:w-[360px] sm:rounded-2xl"
            :class="{ rtl: isRtl }"
          >
            <!-- Drag Handle (Mobile) -->
            <div class="mx-auto mb-6 h-1.5 w-12 rounded-full bg-neutral-200 sm:hidden"></div>

            <div class="mb-6 flex items-center justify-between">
              <h3 class="text-lg font-bold text-slate-800">{{ t('Select Year', 'اختر السنة') }}</h3>
              <button
                class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition-colors hover:bg-slate-200 hover:text-slate-800"
                @click="closeModal"
              >
                <svg
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.5"
                  stroke-linecap="round"
                >
                  <path d="M18 6L6 18M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div
              class="custom-scrollbar grid max-h-[300px] grid-cols-3 gap-3 overflow-y-auto pr-1 pb-4"
            >
              <button
                v-for="year in years"
                :key="year"
                type="button"
                class="flex h-12 items-center justify-center rounded-xl border text-sm font-bold transition-all"
                :class="
                  modelValue === year
                    ? 'border-blue-600 bg-blue-50 text-blue-700 shadow-sm'
                    : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50'
                "
                @click="selectYearAndClose(year)"
              >
                {{ year }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
  import { ref, computed } from 'vue';

  const props = defineProps({
    modelValue: {
      type: [Number, String],
      required: true,
    },
    years: {
      type: Array,
      required: true,
    },
    label: {
      type: String,
      default: 'Year',
    },
    required: {
      type: Boolean,
      default: false,
    },
    error: {
      type: String,
      default: '',
    },
    helpText: {
      type: String,
      default: '',
    },
    isRtl: {
      type: Boolean,
      default: false,
    },
  });

  const emit = defineEmits(['update:modelValue']);

  // Local helper
  const t = (en, ar) => (props.isRtl ? ar : en);

  const showModal = ref(false);

  const visibleYears = computed(() => {
    // Show max 4 elements in the horizontal row to avoid clutter
    // Ensure the selected year is always in the visible list if possible
    const arr = [...props.years];
    const maxTabs = 4;

    if (arr.length <= maxTabs) return arr;

    // Dynamic slice
    let subset = arr.slice(0, maxTabs);
    if (props.modelValue && !subset.includes(props.modelValue)) {
      // Inject the selected year dynamically into the visible slice
      subset = arr.slice(0, maxTabs - 1);
      subset.push(props.modelValue);
      subset.sort((a, b) => Number(a) - Number(b));
    }

    return subset;
  });

  const hasMoreYears = computed(() => props.years.length > visibleYears.value.length);

  const selectYear = (y) => {
    emit('update:modelValue', y);
  };

  const selectYearAndClose = (y) => {
    selectYear(y);
    setTimeout(() => {
      closeModal();
    }, 150); // slight delay for visual check confirmation
  };

  const openModal = () => {
    showModal.value = true;
  };

  const closeModal = () => {
    showModal.value = false;
  };
</script>

<style scoped>
  @reference "../../../css/app.css";

  .custom-scrollbar::-webkit-scrollbar {
    height: 4px;
    width: 4px;
  }
  .custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
  }
  .custom-scrollbar::-webkit-scrollbar-thumb {
    @apply rounded-full bg-slate-200;
  }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    @apply bg-slate-300;
  }

  .animate-zoom-in {
    animation: zoomIn 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
  }

  @keyframes zoomIn {
    from {
      opacity: 0;
      transform: scale(0.5);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  .sheet-fade-enter-active,
  .sheet-fade-leave-active {
    transition: opacity 0.3s ease;
  }
  .sheet-fade-enter-from,
  .sheet-fade-leave-to {
    opacity: 0;
  }
  .sheet-fade-leave-active .animate-slide-up {
    transform: translateY(100%);
    transition: transform 0.3s ease;
  }

  .animate-slide-up {
    animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }

  @keyframes slideUp {
    from {
      transform: translateY(100%);
    }
    to {
      transform: translateY(0);
    }
  }
</style>
