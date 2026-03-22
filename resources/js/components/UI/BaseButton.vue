<template>
  <button
    :class="[
      'inline-flex items-center justify-center font-medium tracking-tight transition-all duration-200 ease-out focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-50',
      variantClasses,
      sizeClasses,
      block ? 'w-full flex-1' : '',
    ]"
    :disabled="disabled || loading"
    :type="type"
    @click="$emit('click', $event)"
  >
    <svg
      v-if="loading"
      class="mr-2 -ml-1 h-4 w-4 animate-spin text-current"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      ></circle>
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      ></path>
    </svg>

    <div class="flex items-center gap-2">
      <slot name="icon-left" />
      <slot />
      <slot name="icon-right" />
    </div>
  </button>
</template>

<script setup>
  import { computed } from 'vue';

  const props = defineProps({
    variant: {
      type: String,
      default: 'primary',
    },
    size: {
      type: String,
      default: 'md',
    },
    loading: Boolean,
    disabled: Boolean,
    block: Boolean,
    type: {
      type: String,
      default: 'button',
    },
  });

  defineEmits(['click']);

  const variantClasses = computed(() => {
    const map = {
      primary:
        'bg-gradient-to-b from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 shadow-[0_4px_14px_0_rgba(37,99,235,0.39)] hover:shadow-[0_6px_20px_rgba(37,99,235,0.23)] border border-blue-600 focus-visible:ring-blue-500',
      secondary:
        'bg-blue-50 text-blue-900 border border-blue-100 hover:bg-blue-100 hover:text-blue-950 focus-visible:ring-blue-200 shadow-sm',
      danger:
        'bg-red-600 text-white hover:bg-red-700 shadow-[0_2px_8px_-2px_rgba(220,38,38,0.5)] border border-transparent focus-visible:ring-red-600',
      white:
        'bg-white text-blue-800 border border-blue-200 hover:border-blue-300 hover:bg-blue-50/50 shadow-[0_2px_8px_-2px_rgba(37,99,235,0.1)] focus-visible:ring-blue-200',
      ghost:
        'bg-transparent text-blue-600 hover:text-blue-800 hover:bg-blue-50 border border-transparent focus-visible:ring-blue-100',
      flat: 'bg-blue-50/50 text-blue-700 hover:bg-blue-100 border-none shadow-none focus-visible:ring-blue-200',
    };
    return map[props.variant] || map.primary;
  });

  const sizeClasses = computed(() => {
    const map = {
      sm: 'px-3 py-1.5 text-xs rounded-md',
      md: 'px-4 py-2 text-sm rounded-lg',
      lg: 'px-5 py-2.5 text-base rounded-lg',
    };
    return map[props.size] || map.md;
  });
</script>
