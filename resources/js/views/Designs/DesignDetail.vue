<template>
  <Layout>
    <div class="design-detail-view dashboard-mode" :class="{ 'rtl': isRtl }">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('Product Dashboard', 'لوحة تحكم المنتج') }}</h1>
          <p class="page-subtitle" v-if="design">{{ design.name }} - {{ design.product_code || '---' }}</p>
        </div>
        <div class="header-actions">
           <BaseButton variant="white" @click="$router.push('/designs')">{{ t('Back to History', 'العودة للتصاميم') }}</BaseButton>
           <BaseButton v-if="can('designs.edit')">{{ t('Edit Product', 'تعديل المنتج') }}</BaseButton>
        </div>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
      </div>

      <div v-else-if="!design" class="empty-state">
        {{ t('Product not found.', 'المنتج غير موجود.') }}
      </div>
      
      <div v-else class="dashboard-grid">
        <!-- Stats Row -->
        <div class="stats-row column-span-full">
           <div class="stat-card">
              <span class="stat-label">{{ t('Total Orders', 'إجمالي الطلبات') }}</span>
              <span class="stat-value">{{ orders.length }}</span>
           </div>
           <div class="stat-card">
              <span class="stat-label">{{ t('Current Version', 'الإصدار الحالي') }}</span>
              <span class="stat-value">v{{ design.version || 1 }}</span>
           </div>
           <div class="stat-card">
              <span class="stat-label">{{ t('Season', 'الموسم') }}</span>
              <span class="stat-value">{{ design.season || '---' }}</span>
           </div>
           <div class="stat-card">
              <span class="stat-label">{{ t('Fabric', 'القماش') }}</span>
              <span class="stat-value">{{ design.fabric_type || '---' }}</span>
           </div>
        </div>

        <!-- Left Column: Visuals & Info -->
        <div class="left-col">
          <BaseCard no-padding class="visual-card">
            <div class="image-container">
              <img v-if="design.image_path" :src="`/storage/${design.image_path}`" class="main-visual">
              <div v-else class="visual-placeholder">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
              </div>
            </div>
            <div class="visual-info p-1-5">
               <h3 class="info-title">{{ t('Technical Specification', 'المواصفات الفنية') }}</h3>
               <p class="info-desc">{{ design.description || t('No description provided.', 'لا يوجد وصف.') }}</p>
            </div>
          </BaseCard>

          <!-- Change Log Section (Point 5) -->
          <BaseCard class="history-card mt-1-5" :title="t('Modification History', 'سجل التغييرات')">
             <div class="audit-trail">
                <div v-for="log in auditLogs" :key="log.id" class="trail-item">
                   <div class="trail-header">
                      <span class="trail-user">{{ log.causer?.name || 'System' }}</span>
                      <span class="trail-date">{{ formatDate(log.created_at) }}</span>
                   </div>
                   <div class="trail-desc">{{ log.description }}</div>
                   <div v-if="log.properties && log.properties.length" class="trail-changes">
                      <div v-for="(change, idx) in log.properties" :key="idx" class="change-line">
                         <span class="field-name">{{ change.field }}:</span>
                         <span class="old-val">{{ change.old }}</span>
                         <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                         <span class="new-val">{{ change.new }}</span>
                      </div>
                   </div>
                </div>
                <div v-if="!auditLogs.length" class="empty-audit">
                   {{ t('No modifications found.', 'لا توجد تغييرات مسجلة.') }}
                </div>
             </div>
          </BaseCard>
        </div>

        <!-- Right Column: Orders & Files -->
        <div class="right-col">
           <BaseCard class="orders-card" :title="t('Production Orders', 'طلبات الإنتاج')">
              <div class="mini-order-list">
                 <div v-for="order in orders" :key="order.id" class="mini-order-item" @click="$router.push(`/orders/${order.id}`)">
                    <div class="mo-info">
                       <span class="mo-code">{{ order.order_code }}</span>
                       <span class="mo-status" :class="order.status">{{ order.status }}</span>
                    </div>
                    <div class="mo-meta">
                       {{ formatDate(order.created_at) }} • {{ order.title }}
                    </div>
                 </div>
                 <div v-if="!orders.length" class="empty-list">
                    {{ t('No production orders yet.', 'لا توجد طلبات إنتاج بعد.') }}
                 </div>
              </div>
           </BaseCard>

           <BaseCard class="files-card mt-1-5" :title="t('Attachments', 'المرفقات')">
              <div class="file-list">
                 <!-- File Item Example -->
                 <div class="file-item">
                    <div class="file-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-7-7z"/><polyline points="13 2 13 9 20 9"/></svg></div>
                    <span class="file-name">Technical_Drawing_V1.pdf</span>
                    <BaseButton size="xs" variant="white">{{ t('Download', 'تحميل') }}</BaseButton>
                 </div>
              </div>
              <BaseButton block variant="white" class="mt-1">{{ t('Upload New File', 'رفع ملف جديد') }}</BaseButton>
           </BaseCard>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { useLang } from '../../composables/useLang';
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { hasPermission } from '../../utils/permissions';
import Layout from '../../components/Layout.vue';
import BaseCard from '../../components/UI/BaseCard.vue';
import BaseButton from '../../components/UI/BaseButton.vue';

const route = useRoute();
const router = useRouter();
const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const can = (perm) => hasPermission(user.value, perm);
const { isRtl, t } = useLang();

const loading = ref(true);
const design = ref(null);
const orders = ref([]);
const auditLogs = ref([]);

const fetchData = async () => {
   if (!can('designs.view')) return router.push('/dashboard');
   loading.value = true;
   try {
      const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };
      const [designRes, logRes] = await Promise.all([
         axios.get(`/api/designs/${route.params.id}`, { headers }),
         // We'll need a way to fetch logs for this specific design
         axios.get(`/api/activity-logs?subject_type=App\\Models\\Design&subject_id=${route.params.id}`, { headers })
      ]);
      design.value = designRes.data;
      auditLogs.value = logRes.data;

      // Also get associated orders (based on title/code match for now or category)
      const ordersRes = await axios.get(`/api/orders?search=${design.value.name}`, { headers });
      orders.value = ordersRes.data;

   } catch(e) { 
      console.error(e);
   } finally { 
      loading.value = false; 
   }
};

const formatDate = (date) => {
   return new Date(date).toLocaleDateString(isRtl.value ? 'ar-EG' : 'en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
   });
};

onMounted(fetchData);
</script>

<style scoped>
.design-detail-view { padding-bottom: 5rem; }
.page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; }
.page-title { font-size: 1.75rem; font-weight: 800; color: #1a3a5f; margin: 0; }
.page-subtitle { color: #64748b; font-size: 0.9375rem; margin-top: 0.25rem; font-weight: 600; }
.header-actions { display: flex; gap: 0.75rem; }

.dashboard-grid { display: grid; grid-template-columns: repeat(12, 1fr); gap: 1.5rem; }
.column-span-full { grid-column: span 12; }
.left-col { grid-column: span 8; }
.right-col { grid-column: span 4; }

.stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 1rem; }
.stat-card { background: white; padding: 1.25rem; border-radius: 16px; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
.stat-label { display: block; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.25rem; }
.stat-value { font-size: 1.5rem; font-weight: 800; color: #1a3a5f; }

.visual-card .image-container { height: 320px; background: #fafbfc; display: flex; align-items: center; justify-content: center; overflow: hidden; }
.main-visual { width: 100%; height: 100%; object-fit: contain; }
.visual-placeholder { color: #cbd5e1; }
.info-title { font-size: 1rem; color: #1e293b; font-weight: 800; margin-bottom: 0.75rem; }
.info-desc { color: #64748b; line-height: 1.6; font-size: 0.9375rem; }

/* Audit Trail */
.audit-trail { display: flex; flex-direction: column; gap: 1rem; }
.trail-item { padding: 1rem; background: #fafbfc; border-radius: 12px; border-left: 4px solid #0ea5e9; }
.rtl .trail-item { border-left: none; border-right: 4px solid #0ea5e9; }
.trail-header { display: flex; justify-content: space-between; margin-bottom: 0.25rem; }
.trail-user { font-weight: 700; color: #1a3a5f; font-size: 0.875rem; }
.trail-date { font-size: 0.75rem; color: #94a3b8; font-weight: 600; }
.trail-desc { font-size: 0.8125rem; color: #475569; margin-bottom: 0.5rem; font-weight: 600; }
.trail-changes { background: white; padding: 0.5rem; border-radius: 8px; border: 1px solid #f1f5f9; }
.change-line { display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; padding: 0.2rem 0; }
.field-name { font-weight: 800; color: #64748b; min-width: 60px; }
.old-val { color: #ef4444; font-weight: 600; text-decoration: line-through; }
.new-val { color: #10b981; font-weight: 700; }

/* Orders List */
.mini-order-list { display: flex; flex-direction: column; gap: 0.75rem; }
.mini-order-item { padding: 0.875rem; border: 1px solid #f1f5f9; border-radius: 10px; cursor: pointer; transition: 0.2s; }
.mini-order-item:hover { border-color: #0ea5e9; background: #f0f9ff; }
.mo-info { display: flex; justify-content: space-between; font-weight: 800; font-size: 0.875rem; margin-bottom: 0.25rem; }
.mo-code { color: #1a3a5f; }
.mo-status { font-size: 0.65rem; text-transform: uppercase; padding: 0.15rem 0.4rem; border-radius: 4px; }
.mo-status.pending { background: #fffbeb; color: #d97706; }
.mo-meta { font-size: 0.75rem; color: #94a3b8; font-weight: 500; }

/* File List */
.file-list { display: flex; flex-direction: column; gap: 0.5rem; }
.file-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem; background: #fafbfc; border-radius: 8px; }
.file-name { flex: 1; font-size: 0.8125rem; font-weight: 600; color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.p-1-5 { padding: 1.5rem; }
.mt-1-5 { margin-top: 1.5rem; }
.mt-1 { margin-top: 1rem; }

@media (max-width: 1024px) {
  .left-col, .right-col { grid-column: span 12; }
  .stats-row { grid-template-columns: repeat(2, 1fr); }
}
</style>
