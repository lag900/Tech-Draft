<template>
  <div class="tab-pane animate-fade-in">
    <!-- 1. Bill of Materials (BOM) -->
    <BaseCard :title="t('Bill of Materials (BOM)', 'جدول المواد (BOM)')" class="mb-1">
      <div class="table-container-compact">
        <table class="tech-table">
          <thead>
            <tr>
              <th>{{ t('Item', 'الصنف') }}</th>
              <th>{{ t('Description', 'الوصف') }}</th>
              <th>{{ t('Color', 'اللون') }}</th>
              <th>{{ t('Unit', 'الوحدة') }}</th>
              <th v-if="canEdit" class="text-right">{{ t('Action', 'إجراء') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, idx) in order.materials" :key="idx">
              <td class="font-bold">{{ item.item_name }}</td>
              <td>{{ item.description || '---' }}</td>
              <td>{{ item.color || '---' }}</td>
              <td>{{ item.unit || '---' }}</td>
              <td v-if="canEdit" class="text-right">
                 <button class="btn-icon-sm danger"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
              </td>
            </tr>
            <tr v-if="!order.materials || order.materials.length === 0">
               <td colspan="5" class="text-center py-4 text-muted">{{ t('No materials records found', 'لا توجد سجلات مواد') }}</td>
            </tr>
            <tr v-if="canEdit" class="add-row">
               <td colspan="5" class="text-center">
                 <button class="btn-text-sm">+ {{ t('Add Item', 'إضافة صنف') }}</button>
               </td>
            </tr>
          </tbody>
        </table>
      </div>
    </BaseCard>

    <div class="compact-grid mt-1">
      <!-- 2. Fabric Specifications -->
      <BaseCard :title="t('Fabric Specifications', 'مواصفات القماش')" class="compact-card">
        <div class="info-item-grid-2">
          <div class="info-field">
            <span class="label">{{ t('Fabric Type', 'نوع القماش') }}</span>
            <span class="value">{{ order.fabric_details?.type || '---' }}</span>
          </div>
          <div class="info-field">
            <span class="label">{{ t('Fabric Weight (GSM)', 'وزن القماش (GSM)') }}</span>
            <span class="value">{{ order.fabric_details?.gsm || order.fabric_details?.weight ? (order.fabric_details.gsm || order.fabric_details.weight) + ' gsm' : '---' }}</span>
          </div>
          <div class="info-field">
            <span class="label">{{ t('Composition', 'التكوين') }}</span>
            <span class="value">{{ order.fabric_details?.composition || '---' }}</span>
          </div>
          <div class="info-field">
            <span class="label">{{ t('Supplier', 'المورد') }}</span>
            <span class="value">{{ order.fabric_details?.supplier || form.fabric_supplier || '---' }}</span>
          </div>
          <div class="info-field">
            <span class="label">{{ t('Cut Width', 'عرض القص') }}</span>
            <span class="value">{{ order.fabric_details?.width || '---' }}</span>
          </div>
          <div class="info-field">
            <span class="label">{{ t('Dye Method', 'طريقة الصباغة') }}</span>
            <span class="value">{{ order.fabric_details?.dye_method || '---' }}</span>
          </div>
          <div class="info-field">
             <span class="label">{{ t('Fabric Finish', 'تشطيب القماش') }}</span>
             <span class="value">{{ order.fabric_details?.finish || '---' }}</span>
          </div>
        </div>
      </BaseCard>

      <!-- 3. Trims & Accessories Summary -->
      <BaseCard :title="t('Trim & Branding Details', 'تفاصيل الإضافات والهوية')" class="compact-card">
        <div class="info-item-grid-1">
          <div class="info-field row">
            <span class="label">{{ t('Zipper', 'سحاب') }}:</span>
            <span class="value">{{ order.zipper_type || '---' }}</span>
          </div>
          <div class="info-field row">
            <span class="label">{{ t('Buttons', 'أزرار') }}:</span>
            <span class="value">{{ order.button_type || '---' }}</span>
          </div>
          <div class="info-field row">
            <span class="label">{{ t('Main Label', 'الملصق الرئيسي') }}:</span>
            <span class="value">{{ order.main_label_type || '---' }}</span>
          </div>
          <div class="info-field row">
             <span class="label">{{ t('Care Label', 'ملصق العناية') }}:</span>
             <span class="value">{{ order.care_label_type || '---' }}</span>
          </div>
          <div class="info-field row">
             <span class="label">{{ t('Size Label', 'ملصق المقاسات') }}:</span>
             <span class="value">{{ order.size_label_type || '---' }}</span>
          </div>
        </div>
      </BaseCard>
    </div>
  </div>
</template>

<script setup>
import BaseCard from '../../../components/UI/BaseCard.vue';
import { useLang } from '../../../composables/useLang';
import BaseInput from '../../../components/UI/BaseInput.vue';

const props = defineProps({
  order: { type: Object, required: true },
  form: { type: Object, required: true },
  canEdit: { type: Boolean, default: false }
});

const { t } = useLang();
</script>

<style scoped>
.tech-table { width: 100%; border-collapse: collapse; margin-top: 0.5rem; }
.tech-table th { text-align: left; font-size: 0.75rem; color: #64748b; text-transform: uppercase; padding: 0.75rem; background: #f8fafc; font-weight: 800; }
.tech-table td { padding: 0.75rem; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; vertical-align: middle; }
.tech-table tr:last-child td { border-bottom: none; }

.rt { text-align: right; }
.btn-icon-sm { width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 6px; border: none; background: #fee2e2; color: #ef4444; cursor: pointer; }
.btn-text-sm { background: none; border: none; color: #3b82f6; font-size: 0.8125rem; font-weight: 800; cursor: pointer; padding: 0.5rem; }

.compact-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 1.5rem;
}

@media (max-width: 900px) {
    .compact-grid { grid-template-columns: 1fr; }
}

.info-item-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
}

.info-item-grid-1 {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.info-field {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.info-field.row {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px dashed #f1f5f9;
    padding-bottom: 8px;
}

.info-field .label {
    font-size: 0.65rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
}

.info-field .value {
    font-size: 0.9375rem;
    font-weight: 700;
    color: #1e293b;
}

.mb-1 { margin-bottom: 1.5rem; }
.mt-1 { margin-top: 1.5rem; }

.text-muted { color: #94a3b8; font-style: italic; }
.text-right { text-align: right; }
.font-bold { font-weight: 700; }
.admin-form-compact {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
</style>
