<template>
  <BaseModal :show="show" @close="$emit('close')" :title="title" size="sm">
    <div class="alert-body">
      <div class="alert-icon" :class="type">
        <svg v-if="type === 'success'" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <svg v-else-if="type === 'error'" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <svg v-else width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      </div>
      <p class="alert-message">{{ message }}</p>
    </div>
    <template #footer>
      <BaseButton @click="$emit('close')">{{ t('OK', 'موافق') }}</BaseButton>
    </template>
  </BaseModal>
</template>

<script setup>
import BaseModal from './BaseModal.vue';
import BaseButton from './BaseButton.vue';

const props = defineProps({
  show: Boolean,
  title: String,
  message: String,
  type: {
    type: String,
    default: 'info' // success, error, info
  },
  isRtl: Boolean
});

defineEmits(['close']);

const t = (en, ar) => props.isRtl ? ar : en;
</script>

<style scoped>
.alert-body {
  padding: 1rem 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}
.alert-icon {
  margin-bottom: 1.25rem;
  width: 64px;
  height: 64px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.alert-icon.success { background: #dcfce7; color: #166534; }
.alert-icon.error { background: #fee2e2; color: #991b1b; }
.alert-icon.info { background: #f0f9ff; color: #075985; }

.alert-message {
  color: #1e293b;
  font-weight: 500;
  line-height: 1.5;
  margin: 0;
}
</style>
