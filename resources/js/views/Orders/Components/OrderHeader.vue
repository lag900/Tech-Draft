<template>
  <BaseCard class="order-header-card mb-2 no-print">
    <div class="header-flex">
      <div class="header-img">
        <img :src="order.product_image_url || '/images/product-placeholder.png'" 
             alt="product"
             @error="(e) => e.target.src = '/images/product-placeholder.png'">
      </div>
      <div class="header-info">
        <h1 class="header-title">{{ order.title || order.category?.name || t('Untitled Product', 'منتج بدون اسم') }}</h1>
        <div class="header-meta-grid">
          <div class="h-meta">
            <span class="h-label">{{ t('Order Code', 'كود الطلب') }}</span>
            <span class="h-val">#{{ order.order_code }}</span>
          </div>
          <div class="h-meta">
            <span class="h-label">{{ t('Client', 'العميل') }}</span>
            <span class="h-val">{{ order.client?.brand_name || order.creator?.name || '---' }}</span>
          </div>
          <div class="h-meta">
            <span class="h-label">{{ t('Quantity', 'الكمية') }}</span>
            <span class="h-val">{{ order.production_details?.quantity || '---' }}</span>
          </div>
          <div class="h-meta">
            <span class="h-label">{{ t('Season', 'الموسم') }}</span>
            <span class="h-val">{{ t(order.season, seasonsAr[order.season] || order.season) }} {{ order.year }}</span>
          </div>
        </div>
        <div class="header-status-row">
          <span class="status-pill-large" :class="order.status">
            {{ t(statusMap[order.status]?.en || order.status, statusMap[order.status]?.ar || order.status) }}
          </span>
          <span class="h-dates">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            {{ t('Created:', 'تاريخ الإنشاء:') }} {{ formatDate(order.created_at) }} 
            <span class="dot"></span> 
            {{ t('Updated:', 'آخر تحديث:') }} {{ formatTimeAgo(order.updated_at) }}
          </span>
        </div>
      </div>
    </div>
  </BaseCard>
</template>

<script setup>
import BaseCard from '../../../components/UI/BaseCard.vue';
import { useLang } from '../../../composables/useLang';

const props = defineProps({
  order: { type: Object, required: true },
  statusMap: { type: Object, required: true }
});

const { isRtl, t } = useLang();

const seasonsAr = {
  Summer: 'صيف',
  Winter: 'شتاء',
  Spring: 'ربيع',
  Autumn: 'خريف'
};

const formatDate = (date) => new Date(date).toLocaleDateString(props.isRtl ? 'ar-EG' : 'en-US', { day: 'numeric', month: 'short', year: 'numeric' });

const formatTimeAgo = (date) => {
    const diff = Math.floor((new Date() - new Date(date)) / 1000);
    if (diff < 60) return t('Just now', 'الآن');
    if (diff < 3600) return Math.floor(diff / 60) + ' ' + t('m ago', 'دقيقة');
    if (diff < 86400) return Math.floor(diff / 3600) + ' ' + t('h ago', 'ساعة');
    return Math.floor(diff / 86400) + ' ' + t('d ago', 'يوم');
};
</script>

<style scoped>
.order-header-card {
  border: 1px solid #eef2f6;
  background: white;
  padding: 1.5rem;
}
.header-flex {
  display: flex;
  gap: 1.5rem;
  align-items: center;
}
.header-img {
  width: 120px;
  height: 120px;
  border-radius: 12px;
  overflow: hidden;
  border: 4px solid #f8fafc;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
  flex-shrink: 0;
}
.header-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.header-info {
  flex: 1;
}
.header-title {
  font-size: 1.5rem;
  font-weight: 800;
  color: #1a3a5f;
  margin: 0 0 0.75rem 0;
  letter-spacing: -0.01em;
}
.header-meta-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
  margin-bottom: 1rem;
}
.h-meta {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.h-label {
  font-size: 0.65rem;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.h-val {
  font-size: 0.875rem;
  font-weight: 700;
  color: #1e293b;
}
.header-status-row {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  flex-wrap: wrap;
}
.status-pill-large {
  padding: 0.4rem 1rem;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border: 1px solid transparent;
}

/* Status variants matching default Tailwind/custom colors */
.status-pill-large.submitted { background: #f8fafc; color: #475569; border-color: #e2e8f0; }
.status-pill-large.pending { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }
.status-pill-large.technical_ready { background: #f0f9ff; color: #0ea5e9; border-color: #bae6fd; }
.status-pill-large.in_review { background: #eff6ff; color: #1e40af; border-color: #dbeafe; }
.status-pill-large.sampling { background: #f5f3ff; color: #6d28d9; border-color: #ede9fe; }
.status-pill-large.approved { background: #f0fdf4; color: #15803d; border-color: #dcfce7; }
.status-pill-large.production { background: #fff7ed; color: #c2410c; border-color: #ffedd5; }
.status-pill-large.completed { background: #ecfdf5; color: #064e3b; border-color: #d1fae5; }
.status-pill-large.cancelled { background: #fef2f2; color: #991b1b; border-color: #fee2e2; }

.h-dates {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.75rem;
  color: #64748b;
  font-weight: 600;
}
.h-dates svg { color: #94a3b8; }
.dot {
  width: 4px;
  height: 4px;
  border-radius: 50%;
  background: #cbd5e1;
  margin: 0 4px;
}

@media (max-width: 768px) {
  .header-flex { flex-direction: column; text-align: center; gap: 1rem; }
  .header-img { margin: 0 auto; width: 100px; height: 100px; }
  .header-meta-grid { justify-content: center; gap: 1rem; }
  .header-status-row { justify-content: center; }
}
</style>
