<template>
  <Transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="show"
      class="bg-blue-[950]/80 fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-sm sm:p-6"
      @click.self="$emit('close')"
    >
      <Transition
        enter-active-class="transition duration-300 ease-out transform pointer-events-none"
        enter-from-class="opacity-0 translate-y-4 scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100 pointer-events-auto"
        leave-active-class="transition duration-200 ease-in transform pointer-events-none"
        leave-from-class="opacity-100 translate-y-0 scale-100 pointer-events-auto"
        leave-to-class="opacity-0 translate-y-4 scale-95"
      >
        <div
          v-if="show"
          class="pointer-events-auto flex max-h-[90vh] w-full flex-col overflow-hidden rounded-2xl bg-white shadow-[0_16px_40px_-10px_rgba(30,58,138,0.2)]"
          :class="[sizeClass, { 'text-right': isRtl }]"
        >
          <div
            class="flex flex-shrink-0 flex-row items-start justify-between gap-4 border-b border-blue-50 px-6 pt-6 pb-4"
          >
            <div class="flex-1" :class="isRtl ? 'order-last text-right' : ''">
              <h3 class="m-0 text-[17px] font-semibold tracking-tight text-blue-950">
                {{ title }}
              </h3>
              <p v-if="subtitle" class="mt-1 text-[13px] text-blue-500">{{ subtitle }}</p>
            </div>
            <button
              class="rounded-lg bg-blue-50/50 p-1.5 text-blue-400 transition-colors hover:bg-blue-100 hover:text-blue-600 focus:ring-2 focus:ring-blue-200 focus:outline-none"
              @click="$emit('close')"
            >
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M18 6 6 18M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="flex-1 overflow-y-auto p-6">
            <slot />
          </div>

          <div
            v-if="$slots.footer"
            class="flex items-center gap-3 border-t border-blue-50 bg-blue-50/20 px-6 py-5"
            :class="isRtl ? 'flex-row-reverse' : 'justify-end'"
          >
            <slot name="footer" />
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
  import { computed } from 'vue';

  const props = defineProps({
    show: Boolean,
    title: String,
    subtitle: String,
    size: {
      type: String,
      default: 'md',
    },
    isRtl: Boolean,
  });

  defineEmits(['close']);

  const sizeClass = computed(() => {
    const sizes = {
      sm: 'max-w-md',
      md: 'max-w-2xl',
      lg: 'max-w-4xl',
      xl: 'max-w-6xl',
    };
    return sizes[props.size] || sizes.md;
  });
</script>
