<template>
  <div class="tab-pane animate-fade-in">
    <div class="tech-spec-grid">
      <!-- 1. Construction Engineering -->
      <BaseCard
        :title="t('Construction Specifications', 'مواصفات الإنشاء والتجميع')"
        class="spec-card"
      >
        <div class="info-grid-2">
          <div class="info-field">
            <span class="label">{{ t('Stitch Type', 'نوع الغرزة') }}</span>
            <span class="value">{{ order.stitching_type || form.stitch_type || '---' }}</span>
          </div>
          <div class="info-field">
            <span class="label">{{ t('Seam Type', 'نوع الدرزة') }}</span>
            <span class="value">{{
              order.production_details?.seam_type || form.seam_type || '---'
            }}</span>
          </div>
          <div class="info-field">
            <span class="label">{{
              t('SPI Target (Stitches Per Inch)', 'عدد الغرز في البوصة (SPI)')
            }}</span>
            <span class="value">{{ order.production_details?.spi || '10-12 SPI' }}</span>
          </div>
          <div class="info-field">
            <span class="label">{{ t('Reinforcement', 'التقوية / التثبيت') }}</span>
            <span class="value">{{
              order.production_details?.reinforcement || 'Back-tack all corners'
            }}</span>
          </div>
        </div>
      </BaseCard>

      <!-- 2. Sewing Sequence -->
      <BaseCard :title="t('Sewing Sequence', 'تسلسل عمليات الخياطة')" class="spec-card">
        <div class="sewing-timeline">
          <div v-for="(step, idx) in sewingSteps" :key="idx" class="timeline-item">
            <div class="step-badge">{{ idx + 1 }}</div>
            <div class="step-content">
              <p class="step-text">{{ step }}</p>
            </div>
          </div>
          <div v-if="!sewingSteps.length" class="empty-state-sm py-4">
            {{ t('No sewing sequence defined.', 'لم يتم تحديد تسلسل عمليات.') }}
          </div>
        </div>
      </BaseCard>
    </div>

    <!-- 3. Model Anatomy / Parts List -->
    <BaseCard
      :title="t('Model Anatomy (Parts List)', 'تشريح الموديل (قائمة الأجزاء)')"
      class="mt-1"
    >
      <div class="anatomy-professional-grid">
        <div v-for="(part, idx) in anatomies" :key="idx" class="anatomy-card-sm">
          <div class="part-image-box">
            <img v-if="part.image_path" :src="part.image_path" alt="part" />
            <div v-else class="part-img-placeholder">
              <svg
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
              >
                <path
                  d="M20 21v-2a4 4 0 00-3-3.87m-7 3.87v-2a4 4 0 013-3.87M12 3.13a4 4 0 010 7.75"
                />
              </svg>
            </div>
          </div>
          <div class="anatomy-details-sm">
            <div class="p-title-group">
              <span class="p-name">{{ part.piece_name }}</span>
              <span class="p-id">#{{ idx + 1 }}</span>
            </div>
            <div class="p-meta">
              <span class="p-qty">QTY: {{ part.count }}</span>
              <span class="p-note">{{
                part.part_description || t('Body Item', 'قطعة أساسية')
              }}</span>
            </div>
          </div>
        </div>
        <div v-if="!anatomies.length" class="empty-state-sm w-full py-8 text-center">
          {{
            t('No parts list mapped to this tech pack.', 'لا توجد قائمة أجزاء مرتبطة بهذا الملف.')
          }}
        </div>
      </div>
    </BaseCard>
  </div>
</template>

<script setup>
  import BaseCard from '../../../components/UI/BaseCard.vue';
  import { useLang } from '../../../composables/useLang';

  defineProps({
    order: { type: Object, required: true },
    form: { type: Object, required: true },
    canEdit: { type: Boolean, default: false },
  });

  const { t } = useLang();
</script>

<style scoped>
  .compact-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
  }
  .sewing-sequence-compact {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }
  .seq-row-sm {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
  }
  .seq-num {
    background: #1e293b;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 800;
  }
  .seq-text {
    flex: 1;
    font-size: 0.875rem;
    color: #1e293b;
    font-weight: 500;
  }
  .admin-form-compact {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }
  .anatomy-grid-compact {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 1rem;
  }
  .anatomy-box-sm {
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }
  .anatomy-box-sm img {
    width: 100%;
    height: 100px;
    object-fit: cover;
  }
  .anatomy-info-sm {
    padding: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .p-name {
    font-size: 0.75rem;
    font-weight: 700;
    color: #1e293b;
  }
  .p-qty {
    font-size: 0.7rem;
    font-weight: 800;
    color: #0ea5e9;
    background: #e0f2fe;
    padding: 2px 6px;
    border-radius: 4px;
  }
  .mt-1 {
    margin-top: 1rem;
  }
  .empty-state-sm {
    font-size: 0.875rem;
    color: #94a3b8;
    font-style: italic;
  }
</style>
