<template>
  <div
    class="pointer-events-none fixed right-0 bottom-[80px] left-0 z-[45] w-full px-4 transition-all duration-300 lg:bottom-0 lg:px-0"
  >
    <div
      class="wizard-footer-card pointer-events-auto mx-auto flex w-full max-w-5xl items-center justify-between rounded-2xl border border-slate-200 bg-white/95 px-4 py-3.5 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] backdrop-blur-xl transition-all md:px-8 lg:rounded-none lg:border-x-0 lg:border-t lg:border-b-0"
    >
      <!-- Back Button -->
      <button
        v-if="currentStep > 1"
        type="button"
        class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 transition-all hover:bg-blue-50 hover:text-blue-600 active:scale-95"
        @click="$emit('prev')"
      >
        <svg
          v-if="!isRtl"
          width="18"
          height="18"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2.5"
        >
          <path d="M19 12H5m7 7l-7-7 7-7" />
        </svg>
        <svg
          v-else
          width="18"
          height="18"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2.5"
        >
          <path d="M5 12h14m-7-7l7 7-7 7" />
        </svg>
        <span class="hidden sm:inline">{{ t('Back', 'السابق') }}</span>
      </button>
      <div v-else class="w-10 sm:w-24"></div>

      <!-- Action Buttons -->
      <div class="flex items-center gap-3 sm:gap-4">
        <button
          v-if="currentStep < totalSteps"
          type="button"
          class="flex transform items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-3 text-sm font-bold text-white shadow-[0_4px_14px_0_rgba(37,99,235,0.39)] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(37,99,235,0.23)] active:scale-95 sm:px-8"
          @click="$emit('next')"
        >
          {{ t('Next Step', 'الخطوة التالية') }}
          <svg
            v-if="!isRtl"
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            class="mt-px"
          >
            <path d="M5 12h14m-7-7l7 7-7 7" />
          </svg>
          <svg
            v-else
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            class="mt-px"
          >
            <path d="M19 12H5m7 7l-7-7 7-7" />
          </svg>
        </button>

        <button
          v-else
          type="submit"
          class="flex transform items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-3 text-sm font-bold text-white shadow-[0_4px_14px_0_rgba(37,99,235,0.39)] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(37,99,235,0.23)] active:scale-95 sm:px-8"
          :disabled="loading"
          @click="$emit('submit')"
        >
          <span v-if="!loading">{{ t('Submit Order', 'إرسال الطلب') }}</span>
          <span v-else class="flex items-center gap-2">
            <svg class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24">
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
                fill="none"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            <span class="hidden sm:inline">{{ t('Processing...', 'جاري التنفيذ...') }}</span>
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
  defineProps({
    currentStep: Number,
    totalSteps: Number,
    isRtl: Boolean,
    t: Function,
    loading: Boolean,
  });

  defineEmits(['prev', 'next', 'submit']);
</script>

<style scoped>
  @reference "../../../../../css/app.css";

  .wizard-footer-container {
    box-shadow: 0 -10px 40px -20px rgba(0, 0, 0, 0.1);
  }
</style>
