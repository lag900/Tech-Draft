<template>
  <div class="base-textarea-group" :class="{ 'has-error': error }">
    <label v-if="label" class="input-label"
      >{{ label }} <span v-if="required" class="required-star">*</span></label
    >
    <div class="textarea-wrapper">
      <textarea
        :value="modelValue"
        class="base-textarea"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :rows="rows"
        @input="$emit('update:modelValue', $event.target.value)"
      ></textarea>
    </div>
    <span v-if="error" class="error-text">{{ error }}</span>
    <span v-else-if="helpText" class="help-text">{{ helpText }}</span>
  </div>
</template>

<script setup>
  defineProps({
    modelValue: [String, Number],
    label: String,
    placeholder: String,
    required: Boolean,
    disabled: Boolean,
    error: String,
    helpText: String,
    rows: {
      type: [String, Number],
      default: 4,
    },
  });
  defineEmits(['update:modelValue']);
</script>

<style scoped>
  .base-textarea-group {
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

  .textarea-wrapper {
    position: relative;
  }

  .base-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.9375rem;
    color: #1a3a5f;
    transition: all 0.2s;
    resize: vertical;
    line-height: 1.5;
  }

  .base-textarea:focus {
    outline: none;
    border-color: #0ea5e9;
    box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
  }

  .base-textarea:disabled {
    background: #f8fafc;
    cursor: not-allowed;
    color: #94a3b8;
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

  .has-error .base-textarea {
    border-color: #fca5a5;
    background-color: #fffbfa;
  }
</style>
