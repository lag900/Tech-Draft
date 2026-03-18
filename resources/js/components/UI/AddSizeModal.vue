<template>
  <BaseModal :show="show" @close="$emit('close')" :title="t('Add New Size', 'إضافة مقاس جديد')" size="sm">
    <div class="add-size-form">
      <BaseInput 
        v-model="form.name" 
        :label="t('Size Name', 'اسم المقاس')" 
        placeholder="e.g. XL, 42, Large"
        autofocus
      />
      <BaseInput 
        v-model.number="form.quantity" 
        type="number" 
        :label="t('Quantity', 'الكمية')" 
        class="mt-4"
      />
    </div>
    <template #footer>
      <BaseButton variant="white" @click="$emit('close')">{{ t('Cancel', 'إلغاء') }}</BaseButton>
      <BaseButton @click="submit" :disabled="!form.name">{{ t('Add Size', 'إضافة المقاس') }}</BaseButton>
    </template>
  </BaseModal>
</template>

<script setup>
import { reactive, watch } from 'vue';
import BaseModal from '../UI/BaseModal.vue';
import BaseInput from '../UI/BaseInput.vue';
import BaseButton from '../UI/BaseButton.vue';

const props = defineProps({
  show: Boolean,
  isRtl: Boolean
});

const emit = defineEmits(['close', 'add']);

const t = (en, ar) => props.isRtl ? ar : en;

const form = reactive({
  name: '',
  quantity: 0
});

watch(() => props.show, (newVal) => {
  if (newVal) {
    form.name = '';
    form.quantity = 0;
  }
});

const submit = () => {
  if (form.name) {
    emit('add', { ...form });
    emit('close');
  }
};
</script>

<style scoped>
.add-size-form {
  padding: 0.5rem 0;
}
.mt-4 {
  margin-top: 1rem;
}
</style>
