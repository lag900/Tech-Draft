<template>
  <div class="tab-pane animate-fade-in">
    <div class="tech-spec-grid">
      <!-- 1. Labeling & Branding -->
      <BaseCard :title="t('Labeling & Placement', 'بطاقات البيانات والوضع المرجعي')" class="spec-card">
        <div class="info-grid-2">
           <div class="info-field">
             <span class="label">{{ t('Main Label Type', 'نوع الملصق الرئيسي') }}</span>
             <span class="value">{{ order.main_label_type || 'Woven - 100% Cotton' }}</span>
           </div>
           <div class="info-field">
             <span class="label">{{ t('Size Label Type', 'نوع ملصق المقاس') }}</span>
             <span class="value">{{ order.size_label_type || 'Satin Loop' }}</span>
           </div>
           <div class="info-field">
             <span class="label">{{ t('Care & Composition', 'ملصق العناية والتركيب') }}</span>
             <span class="value">{{ order.care_label_type || 'Heat Transfer' }}</span>
           </div>
           <div class="info-field">
             <span class="label">{{ t('Label Position', 'موضع الملصق') }}</span>
             <span class="value">{{ order.production_details?.label_position || 'Center Back Neck' }}</span>
           </div>
        </div>
      </BaseCard>

      <!-- 2. Packaging & Shipping -->
      <BaseCard :title="t('Packaging & Shipping', 'التعبئة والشحن')" class="spec-card">
        <div class="info-grid-2">
           <div class="info-field">
             <span class="label">{{ t('Pack Type', 'نوع التعبئة') }}</span>
             <span class="value">{{ order.packaging_type || 'Single Polybag' }}</span>
           </div>
           <div class="info-field">
             <span class="label">{{ t('Folding Method', 'طريقة الطي') }}</span>
             <span class="value">{{ order.folding_method || 'Standard Flat Fold' }}</span>
           </div>
           <div class="info-field">
             <span class="label">{{ t('Carton Quantity', 'كمية الكرتون الموحدة') }}</span>
             <span class="value">{{ order.production_details?.carton_qty || '24 Units/CTN' }}</span>
           </div>
           <div class="info-field">
             <span class="label">{{ t('Barcode Required', 'متطلبات الباركود') }}</span>
             <div class="boolean-badge" :class="{ 'active': order.barcode_required }">
                {{ order.barcode_required ? t('Yes', 'نعم') : t('No', 'لا') }}
             </div>
           </div>
        </div>
      </BaseCard>
    </div>

    <!-- 3. Shipping Instruction Mockup -->
    <BaseCard :title="t('Packaging Instruction Details', 'تفاصيل تعليمات التغليف')" class="mt-1">
        <div class="packaging-instruction-box">
           <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 8V5a2 2 0 00-2-2H5a2 2 0 00-2 2v3m18 0l-9 6-9-6m18 0v11a2 2 0 01-2 2H5a2 2 0 01-2-2V8"/></svg>
           <p>{{ order.packaging_notes || t('Standard export packaging. Corrugated cartons. No moisture allowed.', 'تغليف قياسي للتصدير. كراتين مضلعة. غير مسموح بالرطوبة.') }}</p>
        </div>
    </BaseCard>
  </div>
</template>

<script setup>
import BaseCard from '../../../components/UI/BaseCard.vue';
import { useLang } from '../../../composables/useLang';

const props = defineProps({
  order: { type: Object, required: true },
  form: { type: Object, required: true },
  canEdit: { type: Boolean, default: false }
});

const { t } = useLang();
</script>

<style scoped>
.tech-spec-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 1.5rem;
}

.info-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.info-field { display: flex; flex-direction: column; gap: 6px; }
.info-field .label { font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
.info-field .value { font-size: 0.9375rem; font-weight: 700; color: #1e293b; }

.boolean-badge {
    display: inline-flex;
    padding: 2px 10px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 800;
    background: #f1f5f9;
    color: #64748b;
    width: fit-content;
}

.boolean-badge.active {
    background: #dcfce7;
    color: #166534;
}

.packaging-instruction-box {
    display: flex;
    gap: 1rem;
    align-items: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px dashed #e2e8f0;
    color: #475569;
}

.packaging-instruction-box p { margin: 0; font-size: 0.875rem; font-weight: 500; }

.mt-1 { margin-top: 1.5rem; }
</style>
