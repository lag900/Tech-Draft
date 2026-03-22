<template>
  <div class="tab-pane animate-fade-in">
    <!-- 1. Marker Planning Stats -->
    <div class="production-stats-grid">
      <BaseCard :title="t('Marker Planning', 'تخطيط الماركر')" class="stat-card">
        <div class="stat-value-group">
          <div class="stat-main">
            <span class="val">{{ marker?.efficiency || '0' }}%</span>
            <span class="lbl">{{ t('Efficiency', 'الكفاءة') }}</span>
          </div>
          <div class="stat-details">
            <div class="detail-item">
              <span>{{ t('Marker Length', 'طول الماركر') }}</span>
              <strong>{{ marker?.marker_length || '---' }} m</strong>
            </div>
            <div class="detail-item">
              <span>{{ t('Lay Width', 'عرض الفرش') }}</span>
              <strong>{{ marker?.lay_width || '---' }} cm</strong>
            </div>
          </div>
        </div>
      </BaseCard>

      <BaseCard :title="t('Material Consumption', 'استهلاك المواد')" class="stat-card">
        <div class="stat-value-group">
          <div class="stat-main">
            <span class="val">{{ marker?.net_consumption || '0' }}m</span>
            <span class="lbl">{{ t('Net Consumption', 'الاستهلاك الصافي') }}</span>
          </div>
          <div class="stat-details">
            <div class="detail-item">
              <span>{{ t('Number of Parts', 'عدد الأجزاء') }}</span>
              <strong>{{ marker?.count || '---' }} pcs</strong>
            </div>
            <div class="detail-item">
              <span>{{ t('Total Orders', 'إجمالي الطلبات') }}</span>
              <strong>{{ order.production_details?.quantity || '---' }}</strong>
            </div>
          </div>
        </div>
      </BaseCard>
    </div>

    <!-- 2. Production Status Timeline -->
    <BaseCard :title="t('Production Status History', 'سجل حالة الإنتاج')" class="mt-1">
      <div class="production-timeline">
        <div v-for="log in statusHistory" :key="log.id" class="p-log-item">
          <div class="p-log-indicator"></div>
          <div class="p-log-content">
            <div class="p-log-header">
              <span class="p-status-to">{{ t(log.new_status, log.new_status) }}</span>
              <span class="p-log-time">{{ formatDate(log.created_at) }}</span>
            </div>
            <p class="p-log-meta">
              {{ t('Changed by', 'تم التغيير بواسطة') }} {{ log.changer?.name }}
            </p>
          </div>
        </div>
        <div v-if="!statusHistory.length" class="empty-state-placeholder">
          {{
            t('No production status changes documented.', 'لم يتم توثيق تغييرات في حالة الإنتاج.')
          }}
        </div>
      </div>
    </BaseCard>
  </div>
</template>

<script setup>
  import { computed } from 'vue';
  import BaseCard from '../../../components/UI/BaseCard.vue';
  import { useLang } from '../../../composables/useLang';

  const props = defineProps({
    order: { type: Object, required: true },
    form: { type: Object, required: true },
    canEdit: { type: Boolean, default: false },
  });

  const { isRtl, t } = useLang();

  const marker = computed(() => props.order.marker_plans?.[0] || null);
  const statusHistory = computed(() => props.order.status_history || []);

  const formatDate = (date) => {
    return new Date(date).toLocaleString(isRtl.value ? 'ar-EG' : 'en-US', {
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };
</script>

<style scoped>
  .production-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
  }

  .stat-value-group {
    display: flex;
    align-items: center;
    gap: 2rem;
  }

  .stat-main {
    display: flex;
    flex-direction: column;
    align-items: center;
    border-right: 2px solid #f1f5f9;
    padding-right: 2rem;
  }

  .stat-main .val {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1a3a5f;
    line-height: 1;
  }

  .stat-main .lbl {
    font-size: 0.7rem;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    margin-top: 4px;
  }

  .stat-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .detail-item {
    display: flex;
    justify-content: space-between;
    font-size: 0.8125rem;
  }

  .detail-item span {
    color: #64748b;
    font-weight: 500;
  }
  .detail-item strong {
    color: #1e293b;
    font-weight: 700;
  }

  .production-timeline {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    padding-left: 0.5rem;
    margin-top: 1rem;
  }

  .p-log-item {
    display: flex;
    gap: 1.5rem;
    position: relative;
  }

  .p-log-indicator {
    width: 12px;
    height: 12px;
    background: #3b82f6;
    border-radius: 50%;
    margin-top: 6px;
    z-index: 2;
    box-shadow: 0 0 0 4px #dbeafe;
  }

  .p-log-item:not(:last-child):after {
    content: '';
    position: absolute;
    left: 5px;
    top: 18px;
    bottom: -18px;
    width: 2px;
    background: #f1f5f9;
  }

  .p-log-content {
    flex: 1;
  }

  .p-log-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2px;
  }

  .p-status-to {
    font-size: 0.9375rem;
    font-weight: 800;
    color: #1e293b;
    text-transform: capitalize;
  }

  .p-log-time {
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 600;
  }

  .p-log-meta {
    margin: 0;
    font-size: 0.8125rem;
    color: #64748b;
    font-weight: 500;
  }

  .empty-state-placeholder {
    padding: 2rem;
    text-align: center;
    color: #94a3b8;
    font-style: italic;
  }

  .mt-1 {
    margin-top: 1.5rem;
  }
</style>
