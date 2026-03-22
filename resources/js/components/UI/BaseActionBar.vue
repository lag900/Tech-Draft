<template>
  <div
    class="fixed bottom-0 left-0 z-40 w-full transform border-t border-neutral-200 bg-white/80 px-4 py-4 shadow-[0_-4px_12px_-4px_rgba(0,0,0,0.05)] backdrop-blur-md transition-transform duration-300 ease-out md:px-8 print:hidden"
    :class="isVisible ? 'translate-y-0' : 'translate-y-full'"
  >
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4">
      <div class="flex flex-1 items-center gap-4">
        <slot name="left" />
      </div>
      <div class="flex shrink-0 items-center justify-end gap-3">
        <slot name="right" />
      </div>
    </div>
  </div>
</template>

<script setup>
  defineProps({
    isVisible: {
      type: Boolean,
      default: true,
    },
  });
</script>
