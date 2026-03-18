<template>
  <BaseModal :show="show" @close="$emit('cancel')" :title="title" size="sm">
    <div class="confirm-body">
      <p>{{ message }}</p>
    </div>
    <template #footer>
      <BaseButton variant="white" @click="$emit('cancel')">{{ cancelText || t('Cancel', 'إلغاء') }}</BaseButton>
      <BaseButton :variant="confirmVariant" @click="$emit('confirm')">{{ confirmText || t('Confirm', 'تأكيد') }}</BaseButton>
    </template>
  </BaseModal>
</template>

<script setup>
import BaseModal from './BaseModal.vue';
import BaseButton from './BaseButton.vue';

const props = defineProps({
  show: Boolean,
  title: {
    type: String,
    default: 'Confirmation'
  },
  message: String,
  confirmText: String,
  cancelText: String,
  confirmVariant: {
    type: String,
    default: 'primary'
  },
  isRtl: Boolean
});

defineEmits(['confirm', 'cancel']);

const t = (en, ar) => props.isRtl ? ar : en;
</script>

<style scoped>
.confirm-body {
  padding: 0.5rem 0;
  color: #475569;
  line-height: 1.5;
}
</style>
