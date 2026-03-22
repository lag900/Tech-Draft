<template>
  <div
    class="relative my-2 rounded-xl border border-blue-100 bg-white shadow-[0_2px_10px_-2px_rgba(37,99,235,0.05)] transition-all duration-200 ease-out"
    :class="[
      noPadding ? 'overflow-hidden' : '',
      hoverable
        ? 'cursor-pointer hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_8px_30px_-4px_rgba(37,99,235,0.1)]'
        : '',
    ]"
  >
    <div
      v-if="$slots.header || title"
      class="flex items-center justify-between border-b border-blue-50 px-5 py-4"
    >
      <slot name="header">
        <div>
          <h3 v-if="title" class="m-0 text-[15px] font-semibold tracking-tight text-blue-950">
            {{ title }}
          </h3>
          <p v-if="subtitle" class="mt-1 text-[13px] text-blue-500">{{ subtitle }}</p>
        </div>
      </slot>
      <div v-if="$slots.actions" class="flex items-center gap-2">
        <slot name="actions" />
      </div>
    </div>
    <div :class="noPadding ? 'p-0' : 'p-5'">
      <slot />
    </div>
    <div v-if="$slots.footer" class="rounded-b-xl border-t border-blue-50 bg-blue-50/20 px-5 py-4">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
  defineProps({
    title: String,
    subtitle: String,
    noPadding: Boolean,
    hoverable: Boolean,
  });
</script>
