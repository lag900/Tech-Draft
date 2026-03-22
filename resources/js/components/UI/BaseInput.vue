<template>
  <div class="mb-6 w-full">
    <label v-if="label" class="mb-1.5 block text-[13px] font-medium text-neutral-900">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="relative flex items-center">
      <div
        v-if="$slots.iconLeft"
        class="pointer-events-none absolute left-3 flex items-center text-neutral-400"
      >
        <slot name="iconLeft" />
      </div>
      <input
        :type="type"
        :value="modelValue"
        class="h-[38px] w-full rounded-lg border border-neutral-200 bg-white px-3 text-sm text-neutral-900 shadow-sm transition-all duration-200 ease-out hover:border-neutral-300 focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 focus:outline-none disabled:cursor-not-allowed disabled:border-neutral-200 disabled:bg-neutral-50 disabled:text-neutral-500 disabled:shadow-none"
        :class="[
          { 'pl-9': $slots.iconLeft, 'pr-9': $slots.iconRight },
          error
            ? 'border-red-300 bg-red-50 hover:border-red-400 focus:border-red-500 focus:ring-red-500'
            : '',
        ]"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        @input="$emit('update:modelValue', $event.target.value)"
      />
      <div
        v-if="$slots.iconRight"
        class="pointer-events-none absolute right-3 flex items-center text-neutral-400"
      >
        <slot name="iconRight" />
      </div>
    </div>
    <span v-if="error" class="mt-1.5 block text-xs font-semibold text-red-500">{{ error }}</span>
    <span v-else-if="helpText" class="mt-1.5 block text-xs text-neutral-500">{{ helpText }}</span>
  </div>
</template>

<script setup>
  defineProps({
    modelValue: [String, Number],
    label: String,
    type: {
      type: String,
      default: 'text',
    },
    placeholder: String,
    required: Boolean,
    disabled: Boolean,
    error: String,
    helpText: String,
  });
  defineEmits(['update:modelValue']);
</script>
