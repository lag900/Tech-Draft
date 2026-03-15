<template>
  <div class="base-input-group" :class="{ 'has-error': error }">
    <label v-if="label" class="input-label">{{ label }} <span v-if="required" class="required-star">*</span></label>
    <div class="input-wrapper">
      <div v-if="$slots.iconLeft" class="icon-left">
        <slot name="iconLeft" />
      </div>
      <input
        :type="type"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        class="base-input"
        :class="{ 'with-icon-left': $slots.iconLeft, 'with-icon-right': $slots.iconRight }"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
      />
      <div v-if="$slots.iconRight" class="icon-right">
        <slot name="iconRight" />
      </div>
    </div>
    <span v-if="error" class="error-text">{{ error }}</span>
    <span v-else-if="helpText" class="help-text">{{ helpText }}</span>
  </div>
</template>

<script setup>
defineProps({
  modelValue: [String, Number],
  label: String,
  type: {
    type: String,
    default: 'text'
  },
  placeholder: String,
  required: Boolean,
  disabled: Boolean,
  error: String,
  helpText: String
});
defineEmits(['update:modelValue']);
</script>

<style scoped>
.base-input-group {
  margin-bottom: 1.5rem;
  width: 100%;
}

.input-label {
  display: block;
  font-size: 0.8125rem;
  font-weight: 700;
  color: #475569;
  margin-bottom: 0.5rem;
}

.required-star {
  color: #f87171;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.base-input {
  width: 100%;
  height: 44px;
  padding: 0 1rem;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.9375rem;
  color: #1a3a5f;
  transition: all 0.2s;
}

.base-input:focus {
  outline: none;
  border-color: #0ea5e9;
  box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
}

.base-input:disabled {
  background: #f8fafc;
  cursor: not-allowed;
  color: #94a3b8;
}

.with-icon-left { padding-left: 2.75rem; }
.with-icon-right { padding-right: 2.75rem; }

.icon-left {
  position: absolute;
  left: 0.875rem;
  color: #94a3b8;
  display: flex;
}

.icon-right {
  position: absolute;
  right: 0.875rem;
  color: #94a3b8;
  display: flex;
}

.error-text {
  font-size: 0.75rem;
  color: #ef4444;
  font-weight: 600;
  margin-top: 0.375rem;
  display: block;
}

.help-text {
  font-size: 0.75rem;
  color: #94a3b8;
  margin-top: 0.375rem;
  display: block;
}

.has-error .base-input {
  border-color: #fca5a5;
  background-color: #fffbfa;
}
</style>
