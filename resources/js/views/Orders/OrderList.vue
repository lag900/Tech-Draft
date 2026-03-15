<template>
  <Layout>
    <div class="orders-view" :class="{ 'rtl': isRtl }">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('Manufacturing Orders', 'طلبات التصنيع') }}</h1>
          <p class="page-subtitle">{{ t('Manage production pipeline and tech packs', 'إدارة خط الإنتاج والملفات الفنية') }}</p>
        </div>
        <div class="header-actions">
          <!-- View Toggle -->
          <div class="view-toggle">
            <button :class="{ active: viewMode === 'table' }" @click="viewMode = 'table'">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
              <span>{{ t('Table', 'جدول') }}</span>
            </button>
            <button :class="{ active: viewMode === 'pipeline' }" @click="viewMode = 'pipeline'">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18M3 12h18"/><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
              <span>{{ t('Pipeline', 'خط الإنتاج') }}</span>
            </button>
          </div>

          <BaseButton v-if="can('orders.create')" @click="$router.push('/orders/create')">
            <template #icon-left>
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
            </template>
            {{ t('New Order', 'إنشاء طلب جديد') }}
          </BaseButton>
        </div>
      </div>

      <!-- Stats Summary -->
      <div class="stats-cards-row">
         <div v-for="(stat, key) in dynamicStats" :key="key" class="mini-stat-card" :class="key">
            <div class="stat-inner">
               <span class="mini-stat-label">{{ t(stat.en, stat.ar) }}</span>
               <span class="mini-stat-value">{{ stat.count }}</span>
            </div>
            <div class="stat-indicator"></div>
         </div>
      </div>

      <!-- Toast Notification -->
      <Transition name="fade">
        <div v-if="toastMessage" class="status-toast" :class="toastType">
           <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
           <span>{{ toastMessage }}</span>
        </div>
      </Transition>

      <div class="filters-row">

        <BaseCard class="filter-card">
          <div class="filter-flex">
            <div class="search-field">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="search-icon"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="M16.5 16.5L21 21" stroke="currentColor" stroke-width="2"/></svg>
              <input v-model="search" type="text" :placeholder="t('Search by order code, brand or product...', 'بحث عن رقم الطلب، البراند أو المنتج...')">
            </div>
            <div class="filter-actions-grid">
              <select v-model="filterStatus" class="filter-select">
                <option value="all">{{ t('All Statuses', 'كل الحالات') }}</option>
                <option v-for="(val, key) in statusMap" :key="key" :value="key">{{ t(val.en, val.ar) }}</option>
              </select>
              <select v-model="filterSeason" class="filter-select">
                <option value="">{{ t('All Seasons', 'كل المواسم') }}</option>
                <option v-for="sea in seasons" :key="sea" :value="sea">{{ sea }}</option>
              </select>
              <select v-model="filterFabric" class="filter-select">
                <option value="">{{ t('All Fabrics', 'كل الأقمشة') }}</option>
                <option v-for="fab in fabrics" :key="fab" :value="fab">{{ fab }}</option>
              </select>
              <BaseButton variant="white" size="sm" @click="exportOrders">
                <template #icon-left>
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                </template>
                {{ t('Export', 'تصدير') }}
              </BaseButton>
            </div>
          </div>
        </BaseCard>
      </div>

      <!-- Table View -->
      <BaseCard v-if="viewMode === 'table'" no-padding class="table-card">
        <div class="table-responsive">
          <table class="modern-table">
            <thead>
              <tr>
                <th>{{ t('Order ID', 'رقم الطلب') }}</th>
                <th>{{ t('Product', 'المنتج') }}</th>
                <th v-if="user?.role !== 'client'">{{ t('Client / Brand', 'العميل / العلامة') }}</th>
                <th>{{ t('Category', 'التصنيف') }}</th>
                <th>{{ t('Status', 'الحالة') }}</th>
                <th class="text-right">{{ t('Actions', 'الإجراءات') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in filteredOrders" :key="order.id" class="order-row">
                <td><span class="code-badge">#{{ order.order_code }}</span></td>
                <td>
                   <div class="product-info">
                      <span class="product-title">{{ order.title || t('Untitled Design', 'تصميم بدون اسم') }}</span>
                      <span class="product-meta">{{ order.production_details?.item_type || '---' }} • {{ order.production_details?.fit || '---' }}</span>
                   </div>
                </td>
                <td v-if="user?.role !== 'client'">
                   <span class="brand-link" @click="$router.push('/clients/' + order.created_by)">
                      {{ order.client?.brand_name || t('Individual', 'عميل مستقل') }}
                   </span>
                </td>
                <td><span class="type-text">{{ order.category?.name || '---' }}</span></td>
                <td class="text-center">
                  <div class="status-wrap">
                    <template v-if="user?.role !== 'client'">
                      <div class="custom-dropdown-container">
                        <button 
                          class="status-pill-btn" 
                          :class="[order.status, { 'loading': updatingStatusId === order.id }]"
                          :disabled="!can('orders.approve') || updatingStatusId === order.id"
                          @click.stop="toggleDropdown(order.id)"
                        >
                          <span v-if="updatingStatusId !== order.id" class="status-content">
                            <span class="status-icon">{{ statusMap[order.status]?.icon }}</span>
                            <span>{{ t(statusMap[order.status]?.en, statusMap[order.status]?.ar) }}</span>
                            <svg class="chevron-icon" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M6 9l6 6 6-6"/></svg>
                          </span>
                          <span v-else class="status-content">
                            <div class="pill-spinner"></div>
                            <span>{{ t('Updating...', 'جاري التحديث...') }}</span>
                          </span>
                        </button>

                        <Transition name="fade-pop">
                          <div v-if="activeDropdownId === order.id" class="status-menu-floating">
                            <div class="menu-header">{{ t('Change Status', 'تغيير الحالة') }}</div>
                            <button 
                              v-for="(val, key) in statusMap" 
                              :key="key" 
                              class="menu-item"
                              :class="{ 'active': order.status === key }"
                              @click.stop="changeStatus(order, key)"
                            >
                              <span class="item-icon">{{ val.icon }}</span>
                              <span class="item-label">{{ t(val.en, val.ar) }}</span>
                              <svg v-if="order.status === key" class="check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>
                            </button>
                          </div>
                        </Transition>
                      </div>
                    </template>
                    <span v-else class="status-pill" :class="order.status">
                      <span class="status-icon">{{ statusMap[order.status]?.icon }}</span>
                      {{ t(statusMap[order.status]?.en, statusMap[order.status]?.ar) }}
                    </span>
                  </div>
                </td>


                <td class="text-right flex-actions-cell">
                   <!-- Quick Status Actions -->
                    <div v-if="can('orders.approve')" class="quick-actions">
                      <button v-if="order.status === 'in_review'" class="quick-btn approve" @click="updateOrderStatus(order, 'approved')" :title="t('Approve', 'موافقة')">
                         <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                      </button>
                      <button v-if="order.status === 'approved'" class="quick-btn production" @click="updateOrderStatus(order, 'production')" :title="t('Move to Production', 'بدء الإنتاج')">
                         <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 10v6M2 10v6M12 4v16M4 10a2 2 0 012-2h12a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6z"/></svg>
                      </button>
                      <button v-if="order.status === 'production'" class="quick-btn complete" @click="updateOrderStatus(order, 'completed')" :title="t('Complete Order', 'إتمام الطلب')">
                         <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                      </button>
                   </div>
                   
                   <button class="view-details-btn" @click="$router.push('/orders/' + order.id)">
                     <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                     <span>{{ t('View', 'عرض') }}</span>
                   </button>

                   <button v-if="user?.role === 'admin'" class="edit-production-btn" @click="$router.push('/orders/' + order.id + '/production')" :title="t('Edit Production', 'تعديل الإنتاج')">
                     <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                     <span>{{ t('Edit Production', 'تعديل الإنتاج') }}</span>
                   </button>
                </td>
              </tr>
              <tr v-if="filteredOrders.length === 0">
                 <td colspan="7" class="empty-state">
                    <div class="empty-v">
                       <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#e2e8f0" stroke-width="1.5"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                       <p>{{ t('No orders found matching your search.', 'لا يوجد طلبات تطابق بحثك.') }}</p>
                    </div>
                 </td>
              </tr>
            </tbody>
          </table>
        </div>
      </BaseCard>

      <!-- Pipeline View (Kanban) -->
      <div v-else class="pipeline-container">
         <div v-for="status in kanbanStatuses" :key="status" class="pipeline-column">
            <div class="column-header">
               <div class="header-content">
                  <span class="column-dot" :class="status"></span>
                  <span class="column-title">{{ t(statusMap[status].en, statusMap[status].ar) }}</span>
                  <span class="column-count">{{ ordersByStatus[status]?.length || 0 }}</span>
               </div>
            </div>
            
            <div class="column-body">
               <draggable 
                  v-model="ordersByStatusComputed[status]" 
                  group="orders" 
                  item-key="id"
                  class="draggable-area"
                  :disabled="!hasPermission(user, 'orders.edit')"
                  ghost-class="ghost-card"
                  drag-class="drag-card"
                  chosen-class="chosen-card"
                  :animation="250"
                  :swap-threshold="0.6"
                  :invert-swap="true"
                  @change="handleDragChange($event, status)"
               >
                  <template #item="{ element: order }">
                     <div class="kanban-card" @click="$router.push('/orders/' + order.id)">
                        <div class="card-top">
                           <span class="card-id">#{{ order.order_code }}</span>
                           <span class="card-time">{{ formatTime(order.created_at) }}</span>
                        </div>
                        <h4 class="card-title">{{ order.title }}</h4>
                        <div class="card-client">
                           <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                           <span>{{ order.client?.brand_name || t('Individual', 'عميل مستقل') }}</span>
                        </div>
                        
                        <!-- Last Updated Info -->
                        <div v-if="order.status_history?.length" class="card-audit">
                           <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                           <span>{{ t('By', 'بواسطة') }} {{ order.status_history[0].changer.name }} • {{ timeAgo(order.status_history[0].created_at) }}</span>
                        </div>

                        <div class="card-footer">
                           <div class="card-meta">
                              <span class="meta-tag">{{ order.category?.name }}</span>
                           </div>
                           <!-- Action buttons inside kanban card -->
                           <button v-if="can('orders.approve') && nextStatusMap[status]" 
                                   class="kanban-next-btn"
                                   @click.stop="updateOrderStatus(order, nextStatusMap[status])">
                              {{ t('Advance', 'تقدم') }}
                           </button>
                        </div>
                     </div>
                  </template>
               </draggable>
            </div>
         </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import Layout from '../../components/Layout.vue';
import BaseCard from '../../components/UI/BaseCard.vue';
import BaseButton from '../../components/UI/BaseButton.vue';
import { hasPermission, getUser } from '../../utils/permissions';
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import draggable from 'vuedraggable';

import { useRouter } from 'vue-router';

const router = useRouter();
const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const can = (perm) => hasPermission(user.value, perm);
const orders = ref([]);
const search = ref('');
const filterStatus = ref('all');
const filterSeason = ref('');
const filterFabric = ref('');
const viewMode = ref('table');

const toastMessage = ref('');
const toastType = ref('success');
const updatingStatusId = ref(null);
const activeDropdownId = ref(null);

const isRtl = computed(() => localStorage.getItem('lang') === 'ar');
const t = (en, ar) => isRtl.value ? ar : en;

const statusMap = {
  submitted: { en: 'Submitted', ar: 'تم الإرسال', icon: '📥' },
  pending: { en: 'Pending', ar: 'قيد الانتظار', icon: '⏳' },
  technical_ready: { en: 'Technical Ready', ar: 'جاهز فنياً', icon: '📐' },
  in_review: { en: 'In Review', ar: 'قيد المراجعة', icon: '🔍' },
  sampling: { en: 'Sampling', ar: 'أخذ عينات', icon: '🧵' },
  approved: { en: 'Approved', ar: 'مقبول', icon: '✅' },
  production: { en: 'Production', ar: 'تصنيع', icon: '🏭' },
  completed: { en: 'Completed', ar: 'مكتمل', icon: '📦' },
  cancelled: { en: 'Cancelled', ar: 'ملغي', icon: '❌' }
};


const kanbanStatuses = ['submitted', 'pending', 'technical_ready', 'in_review', 'sampling', 'production', 'completed'];

const nextStatusMap = {
   submitted: 'pending',
   pending: 'technical_ready',
   technical_ready: 'in_review',
   in_review: 'sampling',
   sampling: 'approved',
   approved: 'production',
   production: 'completed'
};

const statsData = ref({});

const dynamicStats = computed(() => {
   return {
      submitted: { en: 'Submitted', ar: 'مرسل', count: statsData.value.submitted || 0 },
      pending: { en: 'Pending', ar: 'منتظر', count: statsData.value.pending || 0 },
      technical_ready: { en: 'Tech Ready', ar: 'جاهز فنياً', count: statsData.value.technical_ready || 0 },
      in_review: { en: 'In Review', ar: 'مراجعة', count: statsData.value.in_review || 0 },
      sampling: { en: 'Sampling', ar: 'عينات', count: statsData.value.sampling || 0 },
      approved: { en: 'Approved', ar: 'مقبول', count: statsData.value.approved || 0 },
      production: { en: 'Production', ar: 'إنتاج', count: statsData.value.production || 0 },
      completed: { en: 'Completed', ar: 'مكتمل', count: statsData.value.completed || 0 },
      cancelled: { en: 'Cancelled', ar: 'ملغي', count: statsData.value.cancelled || 0 }
   };
});

const loading = ref(false);
const fetchOrders = async () => {
  loading.value = true;
  try {
    const res = await axios.get('/api/orders', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    // Support both paginated and simple array responses
    orders.value = Array.isArray(res.data) ? res.data : (res.data.data || []);
  } catch (e) {
    console.error('Failed to load orders', e);
  } finally {
    loading.value = false;
  }
};

const fetchStats = async () => {
  try {
    const res = await axios.get('/api/orders/stats', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    statsData.value = res.data;
  } catch (e) {
    console.error('Failed to load stats', e);
  }
};

onMounted(() => {
  if (!can('orders.view')) return router.push('/dashboard');
  fetchOrders();
  fetchStats();
  window.addEventListener('click', closeAllDropdowns);
});

const toggleDropdown = (id) => {
   if (activeDropdownId.value === id) {
      activeDropdownId.value = null;
   } else {
      activeDropdownId.value = id;
   }
};

const closeAllDropdowns = () => {
   activeDropdownId.value = null;
};

const changeStatus = (order, newStatus) => {
   activeDropdownId.value = null;
   updateOrderStatus(order, newStatus);
};


const updateOrderStatus = async (order, newStatus) => {
  if (updatingStatusId.value === order.id) return;
  
  const oldStatus = order.status;
  if (oldStatus === newStatus) return;

  updatingStatusId.value = order.id;
  
  try {
    // 1. Optimistic update of the order object itself
    order.status = newStatus;
    
    // 2. Optimistic update of the top stats counters
    if (statsData.value[oldStatus] !== undefined) statsData.value[oldStatus]--;
    if (statsData.value[newStatus] !== undefined) statsData.value[newStatus]++;
    else statsData.value[newStatus] = 1;
    
    // 3. Perform backend update
    await axios.patch(`/api/orders/${order.id}/status`, { status: newStatus }, {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    
    showToast(t('Order status updated successfully', 'تم تحديث حالة الطلب بنجاح'));
    
    // Refresh stats from server periodically to stay in sync
    fetchStats();
  } catch (e) {
    // 4. Revert on failure
    order.status = oldStatus;
    showToast(t('Failed to update status. Reverting...', 'فشل تحديث الحالة. جاري التراجع...'), 'error');
    
    // Refresh all data from server to ensure perfect sync
    await fetchOrders();
    await fetchStats();
  } finally {
    updatingStatusId.value = null;
  }
};

const showToast = (msg, type = 'success') => {
   toastMessage.value = msg;
   toastType.value = type;
   setTimeout(() => {
      toastMessage.value = '';
   }, 3000);
};

const seasons = computed(() => [...new Set(orders.value.map(o => o.season))].filter(Boolean));
const fabrics = computed(() => [...new Set(orders.value.map(o => o.fabric_details?.type))].filter(Boolean));

const filteredOrders = computed(() => {
  return (orders.value || []).filter(o => {
    const matchesSearch = 
      (o.order_code || '').toLowerCase().includes(search.value.toLowerCase()) ||
      (o.client?.brand_name || '').toLowerCase().includes(search.value.toLowerCase()) ||
      (o.title || '').toLowerCase().includes(search.value.toLowerCase());
    
    const matchesStatus = filterStatus.value === 'all' || o.status === filterStatus.value;
    const matchesSeason = !filterSeason.value || o.season === filterSeason.value;
    const matchesFabric = !filterFabric.value || o.fabric_details?.type === filterFabric.value;
    
    return matchesSearch && matchesStatus && matchesSeason && matchesFabric;
  });
});

const ordersByStatusComputed = ref({
   submitted: [],
   pending: [],
   technical_ready: [],
   in_review: [],
   sampling: [],
   production: [],
   completed: []
});

// Deep watch on filteredOrders to ensure status property changes are detected
watch(filteredOrders, (newOrders) => {
   const groups = {};
   kanbanStatuses.forEach(s => groups[s] = []);
   newOrders.forEach(o => {
      if (groups[o.status]) groups[o.status].push(o);
   });
   ordersByStatusComputed.value = groups;
}, { immediate: true, deep: true });

const handleDragChange = (evt, newStatus) => {
   if (evt.added) {
      const order = evt.added.element;
      updateOrderStatus(order, newStatus);
   }
};

const formatTime = (dateStr) => {
   if (!dateStr) return '';
   const date = new Date(dateStr);
   return date.toLocaleDateString(isRtl.value ? 'ar-EG' : 'en-US', { month: 'short', day: 'numeric' });
};

const timeAgo = (dateStr) => {
   if (!dateStr) return '';
   const date = new Date(dateStr);
   const now = new Date();
   const diffInSecs = Math.floor((now - date) / 1000);
   
   if (diffInSecs < 60) return t('Just now', 'الآن');
   if (diffInSecs < 3600) return Math.floor(diffInSecs / 60) + ' ' + t('m ago', 'دقيقة');
   if (diffInSecs < 86400) return Math.floor(diffInSecs / 3600) + ' ' + t('h ago', 'ساعة');
   return Math.floor(diffInSecs / 86400) + ' ' + t('d ago', 'يوم');
};

const ordersByStatus = computed(() => ordersByStatusComputed.value);

const exportOrders = async () => {
   try {
     const res = await axios.get('/api/orders/export', {
       headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` },
       responseType: 'blob'
     });
     
     const url = window.URL.createObjectURL(new Blob([res.data]));
     const link = document.createElement('a');
     link.href = url;
     link.setAttribute('download', `orders_${new Date().toISOString().split('T')[0]}.csv`);
     document.body.appendChild(link);
     link.click();
     link.remove();
   } catch (e) {
     console.error('Export failed', e);
     alert('Export failed.');
   }
};

</script>

<style scoped>
.orders-view { padding-bottom: 4rem; }
.page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; }
.page-title { font-size: 1.75rem; font-weight: 800; color: #1a3a5f; margin: 0; }
.page-subtitle { color: #64748b; font-size: 0.9375rem; margin-top: 0.25rem; }

.header-actions { display: flex; align-items: center; gap: 1.25rem; }
.view-toggle { display: flex; background: #f1f5f9; padding: 4px; border-radius: 10px; border: 1px solid #e2e8eb; }
.view-toggle button { display: flex; align-items: center; gap: 8px; padding: 6px 12px; border: none; border-radius: 8px; font-size: 0.8125rem; font-weight: 700; color: #64748b; cursor: pointer; transition: all 0.2s; background: transparent; }
.view-toggle button.active { background: white; color: #0ea5e9; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

.stats-cards-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
.mini-stat-card { 
   background: white; 
   padding: 1.25rem; 
   border-radius: 14px; 
   border: 1px solid #e5e7eb; 
   display: flex; 
   flex-direction: column; 
   position: relative; 
   overflow: hidden; 
   box-shadow: 0 2px 6px rgba(0,0,0,0.05); 
   transition: transform 0.2s;
}
.mini-stat-card:hover { transform: translateY(-2px); }
.stat-inner { position: relative; z-index: 2; }
.mini-stat-label { font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 4px; }
.mini-stat-value { font-size: 1.5rem; font-weight: 900; color: #111827; }

.stat-indicator { position: absolute; left: 0; top: 0; bottom: 0; width: 4px; }
.rtl .stat-indicator { left: auto; right: 0; }

/* Dashboard Card Indicators */
.submitted .stat-indicator { background: #64748b; }
.pending .stat-indicator { background: #94a3b8; }
.technical_ready .stat-indicator { background: #0ea5e9; }
.in_review .stat-indicator { background: #3b82f6; }
.sampling .stat-indicator { background: #8b5cf6; }
.approved .stat-indicator { background: #22c55e; }
.production .stat-indicator { background: #f59e0b; }
.completed .stat-indicator { background: #16a34a; }
.cancelled .stat-indicator { background: #ef4444; }

.filters-row { margin-bottom: 1.5rem; }
.filter-card { padding: 0.75rem 1.25rem !important; }
.filter-flex { display: flex; flex-direction: column; gap: 1rem; }
.filter-actions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.75rem; width: 100%; }

.search-field { position: relative; width: 100%; max-width: 100%; }
.search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; width: 18px; }
.search-field input { width: 100%; height: 44px; padding-left: 2.75rem; border: 1px solid #e5e7eb; background: #f8fafc; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s; }
.search-field input:focus { background: white; border-color: #0ea5e9; box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.05); }

.filter-select { height: 44px; padding: 0 1rem; border: 1px solid #e5e7eb; background: #f8fafc; border-radius: 12px; font-size: 0.8125rem; font-weight: 700; color: #374151; outline: none; cursor: pointer; }

.modern-table { width: 100%; border-collapse: collapse; }
.modern-table th { text-align: left; padding: 1rem 1.5rem; background: #f1f5f9; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; }
.modern-table td { padding: 1.25rem 1.5rem; border-bottom: 1px solid #e5e7eb; font-size: 0.875rem; color: #1f2937; }
.order-row:hover { background: #f8fafc; }

.text-center { text-align: center; }

.status-wrap { display: flex; justify-content: center; }

/* Modern Status Pill System */
.custom-dropdown-container {
   position: relative;
   display: inline-block;
}

.status-pill-btn {
   display: inline-flex;
   align-items: center;
   gap: 8px;
   padding: 6px 14px;
   border-radius: 999px;
   font-size: 0.75rem;
   font-weight: 700;
   border: 1px solid transparent;
   cursor: pointer;
   transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
   white-space: nowrap;
   min-width: 120px;
   justify-content: center;
}

.status-content {
   display: flex;
   align-items: center;
   gap: 6px;
}

.status-icon { font-size: 0.9rem; }
.chevron-icon { opacity: 0.6; transition: transform 0.2s; }
.status-pill-btn:hover .chevron-icon { transform: translateY(1px); }

/* Specific Status Colors requested by user */
.status-pill-btn.submitted, .status-pill.submitted { background: #f1f5f9; color: #475569; }
.status-pill-btn.pending, .status-pill.pending { background: #f1f5f9; color: #475569; }
.status-pill-btn.technical_ready, .status-pill.technical_ready { background: #f0f9ff; color: #0ea5e9; border-color: #bae6fd; }
.status-pill-btn.in_review, .status-pill.in_review { background: #dbeafe; color: #1d4ed8; }
.status-pill-btn.sampling, .status-pill.sampling { background: #ede9fe; color: #6d28d9; }
.status-pill-btn.approved, .status-pill.approved { background: #dcfce7; color: #166534; }
.status-pill-btn.production, .status-pill.production { background: #fde68a; color: #92400e; }
.status-pill-btn.completed, .status-pill.completed { background: #bbf7d0; color: #065f46; }
.status-pill-btn.cancelled, .status-pill.cancelled { background: #fecaca; color: #991b1b; }

.status-pill-btn:hover { filter: brightness(0.96); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.status-pill-btn:active { transform: scale(0.98); }

/* Floating Menu */
.status-menu-floating {
   position: absolute;
   top: calc(100% + 8px);
   left: 50%;
   transform: translateX(-50%);
   background: white;
   border-radius: 12px;
   box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
   z-index: 50;
   min-width: 180px;
   padding: 6px;
   border: 1px solid #f1f5f9;
}

.menu-header {
   padding: 8px 12px;
   font-size: 0.65rem;
   font-weight: 800;
   color: #94a3b8;
   text-transform: uppercase;
   letter-spacing: 0.05em;
}

.menu-item {
   width: 100%;
   display: flex;
   align-items: center;
   gap: 10px;
   padding: 8px 12px;
   border: none;
   background: transparent;
   border-radius: 8px;
   cursor: pointer;
   transition: all 0.15s;
   color: #475569;
}

.menu-item:hover { background: #f8fafc; color: #0ea5e9; }
.menu-item.active { background: #f0f9ff; color: #0ea5e9; font-weight: 700; }

.item-icon { font-size: 1rem; }
.item-label { font-size: 0.8125rem; flex: 1; text-align: left; }
.rtl .item-label { text-align: right; }
.check-icon { color: #0ea5e9; }

/* Loading & Transitions */
.status-pill-btn.loading { opacity: 0.8; pointer-events: none; }
.pill-spinner {
   width: 14px;
   height: 14px;
   border: 2px solid currentColor;
   border-top-color: transparent;
   border-radius: 50%;
   animation: pill-spin 0.6s linear infinite;
}

@keyframes pill-spin { to { transform: rotate(360deg); } }

/* Fade Pop Animation */
.fade-pop-enter-active, .fade-pop-leave-active { transition: all 0.15s ease; }
.fade-pop-enter-from, .fade-pop-leave-to { opacity: 0; transform: translateX(-50%) translateY(-10px) scale(0.95); }

.flex-actions-cell {
   display: flex;
   align-items: center;
   justify-content: flex-end;
   gap: 0.5rem;
}

.view-details-btn {

   display: inline-flex;
   align-items: center;
   gap: 6px;
   padding: 6px 12px;
   background: #f1f5f9;
   color: #475569;
   border: none;
   border-radius: 8px;
   font-size: 0.8125rem;
   font-weight: 700;
   cursor: pointer;
   transition: all 0.2s;
}

.view-details-btn:hover { background: #e2e8f0; color: #1e293b; transform: translateX(2px); }
.rtl .view-details-btn:hover { transform: translateX(-2px); }

.edit-production-btn {
   display: inline-flex;
   align-items: center;
   gap: 6px;
   padding: 6px 12px;
   background: linear-gradient(135deg, #0ea5e9, #0284c7);
   color: white;
   border: none;
   border-radius: 8px;
   font-size: 0.8125rem;
   font-weight: 700;
   cursor: pointer;
   transition: all 0.2s;
}
.edit-production-btn:hover { background: linear-gradient(135deg, #0284c7, #0369a1); box-shadow: 0 4px 10px rgba(14, 165, 233, 0.25); transform: translateY(-1px); }

.quick-actions { display: flex; gap: 0.5rem; margin-right: 0.75rem; }

.quick-btn { width: 28px; height: 28px; border-radius: 6px; border: 1px solid transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
.quick-btn.approve { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
.quick-btn.production { background: #fef3c7; color: #92400e; border-color: #fde68a; }
.quick-btn.complete { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }
.quick-btn:hover { transform: scale(1.1); }

/* Toast Styles */
.status-toast {
   position: fixed;
   top: 2rem;
   left: 50%;
   transform: translateX(-50%);
   z-index: 9999;
   background: #10b981;
   color: white;
   padding: 0.75rem 1.5rem;
   border-radius: 12px;
   display: flex;
   align-items: center;
   gap: 0.75rem;
   font-weight: 700;
   box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2);
   animation: slideInDown 0.3s ease-out;
}
.status-toast.error {
   background: #ef4444;
   box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.2);
}

@keyframes slideInDown {
   from { transform: translate(-50%, -20px); opacity: 0; }
   to { transform: translate(-50%, 0); opacity: 1; }
}

.action-icon-btn { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid #e5e7eb; background: white; color: #64748b; cursor: pointer; transition: 0.2s; }
.action-icon-btn:hover { border-color: #0ea5e9; color: #0ea5e9; background: #f0f9ff; }

/* Pipeline / Kanban Styles */
.pipeline-container { display: flex; gap: 1.5rem; overflow-x: auto; padding-bottom: 2rem; min-height: 70vh; }
.pipeline-column { flex: 0 0 320px; background: #f1f5f9; border-radius: 16px; display: flex; flex-direction: column; border: 2.5px solid transparent; transition: all 0.2s ease; margin-bottom: 1rem; }
.pipeline-column:focus-within { background: #eef2f6; border-color: #cbd5e1; }
.draggable-area { flex: 1; padding: 1rem; display: flex; flex-direction: column; gap: 1rem; min-height: 400px; transition: background 0.2s; }

.column-header { padding: 1.25rem; }
.header-content { display: flex; align-items: center; gap: 0.75rem; }
.column-dot { width: 10px; height: 10px; border-radius: 50%; }
.column-title { font-weight: 800; color: #1a3a5f; flex: 1; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; }
.column-count { background: white; padding: 2px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 800; color: #64748b; border: 1px solid #e2e8f0; }

.kanban-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1rem; cursor: grab; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
.kanban-card:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.05); border-color: #0ea5e9; }
.kanban-card:active { cursor: grabbing; }

.chosen-card { border-color: #0ea5e9 !important; box-shadow: 0 10px 25px rgba(14,165,233,0.15) !important; }
.ghost-card { opacity: 0.4; border: 2px dashed #0ea5e9 !important; background: #f0f9ff !important; transform: scale(0.95); }
.drag-card { opacity: 0.95; transform: scale(1.04) rotate(1.5deg) !important; box-shadow: 0 25px 50px rgba(0,0,0,0.2) !important; z-index: 1000; }

.card-audit { display: flex; align-items: center; gap: 6px; font-size: 0.65rem; color: #94a3b8; font-weight: 700; margin-bottom: 0.75rem; background: #f8fafc; padding: 4px 8px; border-radius: 6px; }

.card-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
.card-id { font-family: monospace; font-weight: 800; color: #0ea5e9; font-size: 0.75rem; }
.card-time { font-size: 0.7rem; color: #94a3b8; font-weight: 600; }
.card-title { font-size: 0.875rem; font-weight: 800; color: #1f2937; margin: 0 0 0.5rem; }
.card-client { display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #6b7280; margin-bottom: 1rem; font-weight: 600; }

.card-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f8fafc; padding-top: 0.75rem; }
.meta-tag { font-size: 0.65rem; font-weight: 800; color: #94a3b8; background: #f8fafc; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; }
.kanban-next-btn { border: none; background: #f0f9ff; color: #0ea5e9; font-size: 0.7rem; font-weight: 800; padding: 4px 10px; border-radius: 6px; cursor: pointer; transition: 0.2s; }
.kanban-next-btn:hover { background: #0ea5e9; color: white; }

/* Pipeling Column Colors */
.column-dot.submitted { background: #64748b; }
.column-dot.pending { background: #94a3b8; }
.column-dot.technical_ready { background: #0ea5e9; }
.column-dot.in_review { background: #3b82f6; }
.column-dot.sampling { background: #8b5cf6; }
.column-dot.production { background: #f59e0b; }
.column-dot.completed { background: #16a34a; }

.rtl .page-header { text-align: right; }
.rtl .modern-table th { text-align: right; }
.rtl .search-field svg { left: auto; right: 12px; }
.rtl .search-field input { padding-left: 1rem; padding-right: 2.75rem; }
.rtl .text-right { text-align: left; }
.rtl .quick-actions { margin-right: 0; margin-left: 0.75rem; }
</style>
