<template>
  <div class="tab-pane animate-fade-in">
    <div class="tech-spec-grid">
      <!-- primary details card -->
      <BaseCard :title="t('Product Specifications', 'مواصفات المنتج')" class="spec-card">
        <div class="info-grid-3">
          <div class="info-field">
            <span class="label">{{ t('Style Name', 'اسم الموديل') }}</span>
            <span class="value">{{ order.title }}</span>
          </div>
          <div class="info-field">
            <span class="label">{{ t('Client / Brand', 'العميل / العلامة التجارية') }}</span>
            <span class="value">{{ order.brand_name || order.client?.brand_name || '---' }}</span>
          </div>
          <div class="info-field">
            <span class="label">{{ t('Product Category', 'تصنيف المنتج') }}</span>
            <span class="value">{{ order.category?.name || '---' }}</span>
          </div>
          <div class="info-field">
            <span class="label">{{ t('Season / Cycle', 'الموسم / الدورة') }}</span>
            <span class="value"
              >{{ t(order.season, seasonsAr[order.season]) }} {{ order.year }}</span
            >
          </div>
          <div class="info-field">
            <span class="label">{{ t('Target Marketplace', 'السوق المستهدف') }}</span>
            <span class="value">{{ order.target_marketplace || '---' }}</span>
          </div>
          <div class="info-field">
            <span class="label">{{ t('Total Quantity', 'إجمالي الكمية') }}</span>
            <span class="value highlight">{{
              order.production_details?.quantity || order.quantity || '---'
            }}</span>
          </div>
        </div>
      </BaseCard>

      <!-- system status card -->
      <BaseCard :title="t('Lifecycle & Status', 'حالة الدورة')" class="spec-card">
        <div class="info-grid-2">
          <div class="info-field">
            <span class="label">{{ t('Production Status', 'حالة الإنتاج') }}</span>
            <span class="status-badge" :class="order.status">{{ order.status }}</span>
          </div>
          <div class="info-field">
            <span class="label">{{ t('Created Date', 'تاريخ الإنشاء') }}</span>
            <span class="value small">{{ formatDate(order.created_at) }}</span>
          </div>
          <div class="info-field">
            <span class="label">{{ t('Last Updated', 'آخر تحديث') }}</span>
            <span class="value small">{{
              formatDate(order.updated_at || order.last_update_date)
            }}</span>
          </div>
          <div class="info-field">
            <span class="label">{{ t('Version', 'الإصدار') }}</span>
            <span class="value"
              >v{{ order.version_number || 1 }}.{{ order.revision_number || 0 }}</span
            >
          </div>
        </div>
      </BaseCard>
    </div>

    <BaseCard
      v-if="order.notes"
      :title="t('Internal Factory Notes', 'ملاحظات المصنع الداخلية')"
      class="mt-1"
    >
      <p class="notes-text">{{ order.notes }}</p>
    </BaseCard>
  </div>
</template>

<script setup>
  import BaseCard from '../../../components/UI/BaseCard.vue';
  import { useLang } from '../../../composables/useLang';

  defineProps({
    order: { type: Object, required: true },
  });

  const { isRtl, t } = useLang();

  const seasonsAr = {
    Summer: 'صيف',
    Winter: 'شتاء',
    Spring: 'ربيع',
    Autumn: 'خريف',
  };

  const formatDate = (date) => {
    if (!date) return '---';
    return new Date(date).toLocaleDateString(isRtl.value ? 'ar-EG' : 'en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    });
  };
</script>

<style scoped>
  .tech-spec-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 1.5rem;
  }

  @media (max-width: 900px) {
    .tech-spec-grid {
      grid-template-columns: 1fr;
    }
  }

  .info-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
  }

  .info-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
  }

  .info-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .info-field .label {
    font-size: 0.7rem;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .info-field .value {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
  }

  .info-field .value.highlight {
    color: #2563eb;
    font-size: 1.25rem;
  }

  .info-field .value.small {
    font-size: 0.875rem;
    font-weight: 600;
  }

  .status-badge {
    display: inline-flex;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    width: fit-content;
    background: #f1f5f9;
    color: #64748b;
  }

  .status-badge.approved {
    background: #dcfce7;
    color: #166534;
  }
  .status-badge.production {
    background: #dbeafe;
    color: #1e40af;
  }
  .status-badge.pending {
    background: #fef9c3;
    color: #854d0e;
  }

  .notes-text {
    font-size: 0.9375rem;
    color: #334155;
    line-height: 1.7;
    margin: 0;
    white-space: pre-wrap;
  }

  .mt-1 {
    margin-top: 1.5rem;
  }
</style>

<style scoped>
  .compact-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
  }
  .info-item-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }
  .info-field {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .info-field .label {
    font-size: 0.65rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .info-field .value {
    font-size: 0.9375rem;
    font-weight: 700;
    color: #1e293b;
  }
  .notes-text-compact {
    font-size: 0.875rem;
    color: #475569;
    line-height: 1.6;
    margin: 0;
  }
  .mt-1 {
    margin-top: 1rem;
  }
</style>
