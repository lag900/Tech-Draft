<template>
  <div class="wizard-progress-container">
    <!-- Desktop Horizontal Steps -->
    <div class="relative hidden items-center justify-between px-4 py-4 md:flex">
      <!-- Background Connecting Line -->
      <div
        class="absolute top-[40px] left-0 h-[3px] w-full overflow-hidden rounded-full bg-blue-50"
        style="z-index: 0"
      >
        <div
          class="h-full bg-blue-600 shadow-[0_0_10px_rgba(37,99,235,0.3)] transition-all duration-1000 ease-in-out"
          :style="{ width: `${((currentStep - 1) / (steps.length - 1)) * 100}%` }"
        ></div>
      </div>

      <!-- Step items -->
      <div
        v-for="(step, index) in steps"
        :key="index"
        class="group relative flex cursor-pointer flex-col items-center"
        style="z-index: 1"
        @click="$emit('goToStep', index + 1)"
      >
        <!-- Icon/Number -->
        <div
          class="flex h-12 w-12 items-center justify-center rounded-2xl border-4 shadow-xl transition-all duration-500"
          :class="[
            currentStep === index + 1
              ? 'scale-110 border-white bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-[0_0_20px_rgba(37,99,235,0.4)]'
              : currentStep > index + 1
                ? 'border-blue-600 bg-blue-50 text-blue-600'
                : 'border-blue-100 bg-white text-blue-300',
          ]"
        >
          <Transition name="scale" mode="out-in">
            <span v-if="currentStep > index + 1" :key="'check'" class="text-xl font-bold">
              <svg
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="3"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <polyline points="20 6 9 17 4 12" />
              </svg>
            </span>
            <span v-else :key="'num'" class="text-lg font-black">{{ index + 1 }}</span>
          </Transition>
        </div>

        <!-- Label -->
        <div
          class="mt-6 flex flex-col items-center"
          :class="{
            'opacity-100': currentStep === index + 1,
            'opacity-60': currentStep !== index + 1,
          }"
        >
          <span
            class="text-[10px] font-black tracking-[0.2em] whitespace-nowrap uppercase transition-all duration-300"
            :class="currentStep === index + 1 ? 'text-blue-600' : 'text-blue-400'"
          >
            {{ isRtl ? step.labelAr : step.labelEn }}
          </span>
          <div
            v-show="currentStep === index + 1"
            class="mt-2 h-1.5 w-1.5 animate-pulse rounded-full bg-blue-600 shadow-[0_0_8px_rgba(37,99,235,0.6)]"
          ></div>
        </div>
      </div>
    </div>

    <!-- Mobile Header -->
    <div class="md:hidden">
      <div class="mb-4 flex items-center justify-between">
        <div>
          <span class="mb-1 block text-[10px] font-black tracking-widest text-blue-400 uppercase">
            {{ isRtl ? 'المرحلة' : 'Step' }} {{ currentStep }} / {{ steps.length }}
          </span>
          <h3 class="text-lg leading-tight font-black text-blue-950">
            {{ isRtl ? steps[currentStep - 1].labelAr : steps[currentStep - 1].labelEn }}
          </h3>
        </div>
        <div
          class="flex h-12 w-12 items-center justify-center rounded-2xl border border-blue-100 bg-blue-50 text-lg font-black text-blue-600 shadow-sm"
        >
          {{ Math.round((currentStep / steps.length) * 100) }}%
        </div>
      </div>

      <!-- Tiny Progress Dots for mobile -->
      <div class="mb-6 flex gap-1.5">
        <div
          v-for="n in steps.length"
          :key="n"
          class="h-1.5 flex-1 rounded-full transition-all duration-500"
          :class="
            n <= currentStep ? 'bg-blue-600 shadow-[0_0_8px_rgba(37,99,235,0.4)]' : 'bg-blue-50'
          "
        ></div>
      </div>
    </div>
  </div>
</template>

<script setup>
  defineProps({
    steps: {
      type: Array, // [{ labelEn, labelAr }]
      required: true,
    },
    currentStep: {
      type: Number,
      required: true,
    },
    isRtl: Boolean,
  });

  defineEmits(['goToStep']);
</script>

<style scoped>
  @reference "../../../../../css/app.css";

  .wizard-progress-container {
    width: 100%;
  }

  .scale-enter-active,
  .scale-leave-active {
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  }

  .scale-enter-from,
  .scale-leave-to {
    opacity: 0;
    transform: scale(0.5);
  }
</style>
