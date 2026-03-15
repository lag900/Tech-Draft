<template>
  <button 
    :class="['base-btn', `btn-${variant}`, `btn-${size}`, { 'btn-loading': loading, 'btn-block': block }]"
    :disabled="disabled || loading"
    :type="type"
    @click="$emit('click', $event)"
  >
    <div v-if="loading" class="spinner"></div>
    <span v-else class="btn-content">
      <slot name="icon-left" />
      <slot />
      <slot name="icon-right" />
    </span>
  </button>
</template>

<script setup>
defineProps({
  variant: {
    type: String,
    default: 'primary' // primary, secondary, danger, white, ghost
  },
  size: {
    type: String,
    default: 'md' // sm, md, lg
  },
  loading: Boolean,
  disabled: Boolean,
  block: Boolean,
  type: {
    type: String,
    default: 'button'
  }
});
defineEmits(['click']);
</script>

<style scoped>
.base-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  white-space: nowrap;
  vertical-align: middle;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  gap: 0.5rem;
  border-radius: 10px;
  cursor: pointer;
  border: 1px solid transparent;
}

.btn-content {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* Variants */
.btn-primary {
  background: var(--primary-color);
  color: white;
}
.btn-primary:hover:not(:disabled) {
  background: var(--primary-dark);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(15, 91, 211, 0.2);
}

.btn-secondary {
  background: #f1f5f9;
  color: var(--text-primary);
}
.btn-secondary:hover:not(:disabled) {
  background: #e2e8f0;
}

.btn-white {
  background: white;
  border-color: var(--border-color);
  color: var(--text-primary);
  box-shadow: var(--shadow-sm);
}
.btn-white:hover:not(:disabled) {
  border-color: #cbd5e1;
  background: #f8fafc;
}

.btn-danger {
  background: #ef4444;
  color: white;
}

.btn-ghost {
  background: transparent;
  color: var(--text-muted);
}
.btn-ghost:hover:not(:disabled) {
  background: #f8fafc;
  color: var(--text-primary);
}

/* Sizes */
.btn-sm { padding: 0.5rem 0.875rem; font-size: 0.8125rem; }
.btn-md { padding: 0.625rem 1.25rem; font-size: 0.875rem; }
.btn-lg { padding: 0.875rem 1.75rem; font-size: 1rem; }

.btn-block { width: 100%; }

.btn-loading {
  position: relative;
  cursor: wait;
}

.spinner {
  width: 1.25rem;
  height: 1.25rem;
  border: 2px solid rgba(255,255,255,0.3);
  border-radius: 50%;
  border-top-color: currentColor;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
