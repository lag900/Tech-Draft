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

        <div v-if="viewMode === 'table'">
          <!-- Table Card Wrapper -->
          <div class="table-card-wrapper max-w-[1400px] mx-auto overflow-visible">
            
            <!-- Desktop Table View -->
            <div v-if="!isMobile">
              <div class="overflow-x-auto">
                <table class="w-full border-separate border-spacing-0">
                  <thead>
                    <tr class="table-header-row border-b border-gray-100">
                      <th class="w-[120px] px-6 py-4 text-start align-middle">{{ t('ID', 'رقم') }}</th>
                      <th class="w-[240px] px-6 py-4 text-start align-middle">{{ t('Product / Brand', 'المنتج / البراند') }}</th>
                      <th v-if="user?.role !== 'client'" class="w-[200px] px-6 py-4 text-start align-middle">{{ t('Client', 'العميل') }}</th>
                      <th class="w-[160px] px-6 py-4 text-start align-middle">{{ t('Category', 'التصنيف') }}</th>
                      <th class="w-[180px] px-6 py-4 text-start align-middle">{{ t('Material', 'الخامة') }}</th>
                      <th class="w-[180px] px-6 py-4 text-center align-middle">{{ t('Status', 'الحالة') }}</th>
                      <th class="w-[180px] px-6 py-4 text-end align-middle">{{ t('Actions', 'الإجراءات') }}</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-50">
                    <tr v-for="order in filteredOrders" :key="order.id" 
                        class="table-row-modern group"
                        @click="$router.push('/orders/' + order.id)">
                      
                      <!-- ID Column -->
                      <td class="px-6 py-5 align-middle text-start">
                        <span class="id-badge">#{{ order.order_code }}</span>
                      </td>

                      <!-- Product Column -->
                      <td class="px-6 py-5 align-middle text-start">
                        <div class="flex flex-col min-w-0">
                          <span class="product-title group-hover:text-blue-600 transition-colors">
                            {{ order.title || t('Untitled Design', 'تصميم بدون اسم') }}
                          </span>
                          <span class="product-meta mt-1">
                            {{ order.production_details?.item_type || '---' }} • {{ order.production_details?.fit || '---' }}
                          </span>
                        </div>
                      </td>

                      <!-- Client Column -->
                      <td v-if="user?.role !== 'client'" class="px-6 py-5 align-middle text-start">
                        <div class="flex items-center gap-3">
                          <div class="brand-avatar">
                            {{ (order.client?.brand_name || 'I').charAt(0) }}
                          </div>
                          <div class="flex flex-col min-w-0">
                            <span class="client-name">
                              {{ order.client?.brand_name || t('Individual', 'عميل مستقل') }}
                            </span>
                            <span class="client-status">
                              <span class="status-dot"></span>
                              {{ t('Verified Client', 'عميل موثق') }}
                            </span>
                          </div>
                        </div>
                      </td>

                      <!-- Category Column -->
                      <td class="px-6 py-5 align-middle text-start">
                        <span class="category-tag">
                          {{ order.category?.name || '---' }}
                        </span>
                      </td>

                      <!-- Material Column -->
                      <td class="px-6 py-5 align-middle text-start">
                        <div class="flex flex-col min-w-0">
                          <span class="text-sm font-bold text-slate-700 truncate max-w-[150px]" :title="order.fabric_details?.type || order.fabric_type">
                            {{ order.fabric_details?.type || order.fabric_type || '---' }}
                          </span>
                          <span class="text-xs text-slate-400 font-medium mt-1 truncate max-w-[150px]" :title="order.notes">
                            {{ order.notes || '---' }}
                          </span>
                        </div>
                      </td>

                      <!-- Status Column -->
                      <td class="px-6 py-5 align-middle text-center" @click.stop>
                        <div v-if="user?.role !== 'client'" class="status-cell-relative">
                          <button 
                            class="status-pill-v2 mx-auto" 
                            :class="[order.status, { 'loading': updatingStatusId === order.id }]"
                            @click.stop="toggleDropdown(order.id)"
                          >
                            <span v-if="updatingStatusId !== order.id" class="flex items-center gap-2">
                              {{ t(statusMap[order.status]?.en, statusMap[order.status]?.ar) }}
                              <svg class="opacity-50" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M6 9l6 6 6-6"/></svg>
                            </span>
                            <div v-else class="status-loading-spinner"></div>
                          </button>

                          <Transition name="fade-pop">
                            <div v-if="activeDropdownId === order.id" class="status-dropdown-menu">
                              <div class="dropdown-header">
                                {{ t('Change Order Status', 'تغيير حالة الطلب') }}
                              </div>
                              <button v-for="(val, key) in statusMap" :key="key" 
                                      class="dropdown-item"
                                      :class="{ 'is-active': order.status === key }"
                                      @click.stop="changeStatus(order, key)">
                                <span class="item-dot" :class="key"></span>
                                <span class="flex-1">{{ t(val.en, val.ar) }}</span>
                                <svg v-if="order.status === key" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="text-blue-500"><polyline points="20 6 9 17 4 12"/></svg>
                              </button>
                            </div>
                          </Transition>
                        </div>
                        <span v-else class="status-pill-v2 readonly inline-flex mx-auto" :class="order.status">
                          {{ t(statusMap[order.status]?.en, statusMap[order.status]?.ar) }}
                        </span>
                      </td>

                      <!-- Actions Column -->
                      <td class="px-6 py-5 align-middle text-end" @click.stop>
                        <div class="actions-group-wrapper">
                          <button @click="$router.push('/orders/' + order.id)" class="action-circle-btn" :title="t('View Details', 'عرض التفاصيل')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z"/><circle cx="12" cy="12" r="3"/></svg>
                          </button>

                          <button v-if="can('orders.delete')" @click="confirmDelete(order)" class="action-circle-btn danger" :title="t('Delete', 'حذف')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Premium Mobile-First Orders View -->
            <div v-else class="space-y-6 p-4">
              <div v-for="order in filteredOrders" :key="order.id" 
                   class="order-mobile-card relative overflow-hidden group"
                   :class="order.status"
                   @click="$router.push('/orders/' + order.id)">
                
                <!-- Status Sidebar Accent -->
                <div class="card-status-accent"></div>

                <!-- 1. Header: Primary Data (Title + Status Row) -->
                <div class="flex items-start justify-between gap-4 mb-1 relative z-10">
                  <h4 class="text-[17px] font-black text-slate-900 leading-tight">
                    {{ order.title || t('Untitled Design', 'تصميم بدون اسم') }}
                  </h4>
                  <div class="shrink-0 pt-0.5">
                    <span :class="['status-pill-v2 sm !min-w-0 !px-3 font-bold text-[11px] uppercase tracking-tighter', order.status]">
                      {{ t(statusMap[order.status]?.en, statusMap[order.status]?.ar) }}
                    </span>
                  </div>
                </div>

                <!-- 2. Unique ID (Sub-header) -->
                <div class="mb-5 relative z-10">
                  <span class="id-badge !text-[10px] !px-2.5 !py-1 !rounded-md">#{{ order.order_code }}</span>
                </div>
                
                <!-- 3. Dynamic Metadata Grid -->
                <div class="grid grid-cols-2 gap-4 mb-7 relative z-10 p-4 bg-slate-50/50 rounded-2xl border border-slate-100/50">
                  <div class="info-item">
                    <span class="label !text-[9px] !mb-1 text-slate-400 font-black uppercase">{{ t('Client', 'العميل') }}</span>
                    <span class="value text-[13px] font-black text-slate-700 truncate block">{{ order.client?.brand_name || t('Individual', 'مستقل') }}</span>
                  </div>
                  <div class="info-item">
                    <span class="label !text-[9px] !mb-1 text-slate-400 font-black uppercase">{{ t('Established', 'تاريخ الإنشاء') }}</span>
                    <span class="value text-[13px] font-black text-slate-700 block">{{ formatTime(order.created_at) }}</span>
                  </div>
                  <div class="info-item col-span-2 pt-2 border-t border-slate-100 mt-1 flex justify-between gap-4">
                    <div>
                        <span class="label !text-[9px] !mb-1 text-slate-400 font-black uppercase">{{ t('Category', 'التصنيف') }}</span>
                        <span class="category-tag-v3">{{ order.category?.name || '---' }}</span>
                    </div>
                    <div>
                        <span class="label !text-[9px] !mb-1 text-slate-400 font-black uppercase">{{ t('Material', 'الخامة') }}</span>
                        <span class="text-[12px] font-bold text-slate-700 truncate block max-w-[120px]">{{ order.fabric_details?.type || order.fabric_type || '---' }}</span>
                    </div>
                  </div>
                </div>

                <!-- 4. Touch-Optimized Primary Actions (Bottom Row) -->
                <div class="flex relative z-10 mt-auto">
                    <!-- Promoted Primary Action -->
                    <button @click.stop="$router.push('/orders/' + order.id)" 
                            class="w-full mobile-btn-primary !bg-blue-600 !text-white !shadow-blue-200">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z"/><circle cx="12" cy="12" r="3"/></svg>
                      <span class="text-sm">{{ t('View Details', 'عرض التفاصيل') }}</span>
                    </button>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="filteredOrders.length === 0" class="py-20 text-center border-t border-gray-50">
              <div class="empty-state-icon mb-4">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
              </div>
              <h3 class="text-gray-900 font-extrabold mb-1">{{ t('No orders discovered', 'لم يتم العثور على طلبات') }}</h3>
              <p class="text-gray-400 text-sm">{{ t('Try adjusting your search criteria or filters.', 'حاول تعديل معايير البحث أو خيارات التصفية.') }}</p>
            </div>
          </div>

          <!-- Professional Pagination Footer -->
          <div class="pagination-footer-standalone">
            <div class="pagination-flex-container">
              
              <!-- Left: Results Counter -->
              <div class="pagination-results-side">
                <p class="results-text">
                  {{ isRtl ? 'عرض' : 'Showing' }} 
                  <span class="count-highlight">{{ paginationRange.from }}</span> 
                  {{ isRtl ? 'إلى' : 'to' }} 
                  <span class="count-highlight">{{ paginationRange.to }}</span> 
                  {{ isRtl ? 'من' : 'of' }} 
                  <span class="count-total">{{ paginationRange.total }}</span> 
                </p>
              </div>

              <!-- Center: Per Page Selector -->
              <div class="pagination-center-side">
                <div class="per-page-wrapper">
                  <span class="per-page-label">{{ t('Per Page', 'نتائج الصفحة') }}</span>
                  <select v-model.number="perPage" class="select-modern-v3">
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                    <option :value="100">100</option>
                  </select>
                </div>
              </div>
              
              <!-- Right: Navigation -->
              <div class="pagination-nav-side">
                <button class="btn-pag-standard" :disabled="currentPage === 1" @click="goToPage(currentPage - 1)">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" :class="{'rotate-180': isRtl}"><path d="M15 18l-6-6 6-6"/></svg>
                  <span>{{ t('Prev', 'السابق') }}</span>
                </button>
                
                <div class="page-indicator-glow">
                   <span class="current">{{ currentPage }}</span>
                   <span class="separator">/</span>
                   <span class="total">{{ lastPage }}</span>
                </div>

                <button class="btn-pag-standard" :disabled="currentPage === lastPage" @click="goToPage(currentPage + 1)">
                  <span>{{ t('Next', 'التالي') }}</span>
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" :class="{'rotate-180': isRtl}"><path d="M9 18l6-6-6-6"/></svg>
                </button>
              </div>
            </div>
          </div>
        </div>

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

        <!-- Delete Confirmation Modal -->
        <Teleport to="body">
          <Transition name="fade">
            <div v-if="showDeleteModal" class="modal-overlay" @click="showDeleteModal = false">
              <div class="delete-modal-card" @click.stop>
                <div class="modal-icon-wrap">
                  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                </div>
                <h3>{{ t('Delete Order', 'حذف الطلب') }}</h3>
                <p>{{ t('Are you sure you want to delete order', 'هل أنت متأكد من حذف الطلب') }} <span class="font-bold">#{{ orderToDelete?.order_code }}</span>? {{ t('This action cannot be undone.', 'لا يمكن التراجع عن هذا الإجراء.') }}</p>
                
                <div class="modal-actions">
                  <button class="btn-cancel" @click="showDeleteModal = false">{{ t('Cancel', 'إلغاء') }}</button>
                  <button class="btn-delete-confirm" :disabled="deleting" @click="deleteOrder">
                    <span v-if="deleting" class="mini-spinner"></span>
                    {{ deleting ? t('Deleting...', 'جاري الحذف...') : t('Yes, Delete', 'نعم، حذف') }}
                  </button>
                </div>
              </div>
            </div>
          </Transition>
        </Teleport>
      </div>
    </Layout>
  </template>

  <script setup>
  import Layout from '../../components/Layout.vue';
  import BaseCard from '../../components/UI/BaseCard.vue';
  import BaseButton from '../../components/UI/BaseButton.vue';
  import { hasPermission } from '../../utils/permissions';
  import { ref, onMounted, computed, watch, onUnmounted, watchEffect } from 'vue';
  import axios from 'axios';
  import draggable from 'vuedraggable';
  import { useRouter } from 'vue-router';
  import { useLang } from '../../composables/useLang';

  const { t, isRtl } = useLang();
  const router = useRouter();

  // Responsive state
  const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1200);
  const isMobile = computed(() => windowWidth.value < 768);

  const updateWidth = () => {
    windowWidth.value = window.innerWidth;
  };

  onMounted(() => {
    window.addEventListener('resize', updateWidth);
  });

  onUnmounted(() => {
    window.removeEventListener('resize', updateWidth);
  });
  // Auth helper — reads token once per call rather than per inline expression
  const authHeaders = () => ({ Authorization: `Bearer ${localStorage.getItem('auth_token')}` });

  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
  const can = (perm) => hasPermission(user.value, perm);
  const orders = ref([]);
  const search = ref('');
  const filterStatus = ref('all');
  const filterSeason = ref('');
  const filterFabric = ref('');
  const viewMode = ref('table');

  // Pagination
  const perPage = ref(10);
  const currentPage = ref(1);
  const lastPage = ref(1);
  const totalOrders = ref(0);

  const toastMessage = ref('');
  const toastType = ref('success');
  const updatingStatusId = ref(null);
  const activeDropdownId = ref(null);

  // Delete Modal State
  const showDeleteModal = ref(false);
  const orderToDelete = ref(null);
  const deleting = ref(false);

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
    if (loading.value) return; // Prevent concurrent requests
    loading.value = true;
    
    try {
      const queryParams = {
        per_page: perPage.value,
        page: currentPage.value,
      };
      
      // Clean params: don't send "all" or empty values
      if (search.value?.trim()) queryParams.search = search.value.trim();
      if (filterStatus.value && filterStatus.value !== 'all') queryParams.status = filterStatus.value;
      if (filterSeason.value) queryParams.season = filterSeason.value;
      if (filterFabric.value) queryParams.fabric_type = filterFabric.value;

      console.log('[OrderList] Fetching with params:', queryParams);
      
      const { data } = await axios.get('/api/orders', { 
        headers: authHeaders(),
        params: queryParams
      });
      
      console.log('[OrderList] API Raw Response:', data);
      
      // Flexible Data Mapping: Auto-discover where the records are
      let finalItems = [];
      let sourceInfo = data;

      if (Array.isArray(data)) {
        finalItems = data;
      } else if (data?.data && Array.isArray(data.data)) {
        finalItems = data.data;
        sourceInfo = data; // Root is pagination object
      } else if (data?.data?.data && Array.isArray(data.data.data)) {
        finalItems = data.data.data;
        sourceInfo = data.data; // Nested pagination object
      } else if (data?.orders && Array.isArray(data.orders)) {
        finalItems = data.orders;
        sourceInfo = data;
      } else if (data?.data?.orders && Array.isArray(data.data.orders)) {
        finalItems = data.data.orders;
        sourceInfo = data.data;
      }
      
      orders.value = finalItems;
      
      // Dynamic Pagination Metadata Extraction
      currentPage.value = sourceInfo.current_page || sourceInfo.currentPage || currentPage.value || 1;
      lastPage.value = sourceInfo.last_page || sourceInfo.lastPage || 1;
      totalOrders.value = sourceInfo.total ?? finalItems.length;
      
      console.log(`[OrderList] Mapping Success: ${finalItems.length} records, Page: ${currentPage.value}/${lastPage.value}, Total: ${totalOrders.value}`);
    } catch (err) {
      console.error('[OrderList] Error details:', err);
      showToast(t('Failed to load orders.', 'فشل تحميل الطلبات.'), 'error');
    } finally {
      loading.value = false;
    }
  };

  const fetchStats = async () => {
    try {
      const { data } = await axios.get('/api/orders/stats', { headers: authHeaders() });
      statsData.value = data;
    } catch { /* stats are secondary */ }
  };

  onMounted(() => {
    if (!can('orders.view')) return router.push('/dashboard');
    fetchOrders();
    fetchStats();
    window.addEventListener('click', closeAllDropdowns);
  });

  onUnmounted(() => {
    window.removeEventListener('click', closeAllDropdowns);
  });

  // Watch filters and search: trigger reset then fetch
  watch([search, filterStatus, filterSeason, filterFabric], () => {
    if (currentPage.value !== 1) {
      currentPage.value = 1;
    } else {
      fetchOrders();
    }
  });

  // Watch page size
  watch(perPage, () => {
    currentPage.value = 1;
    fetchOrders();
  });

  // Watch current page change
  watch(currentPage, (newVal, oldVal) => {
    if (newVal !== oldVal) fetchOrders();
  });

  const goToPage = (page) => {
    if (page < 1 || page > lastPage.value) return;
    currentPage.value = page;
  };

  const toggleDropdown = (id) => {
    activeDropdownId.value = activeDropdownId.value === id ? null : id;
  };

  const closeAllDropdowns = () => {
    activeDropdownId.value = null;
  };

  const confirmDelete = (order) => {
    orderToDelete.value = order;
    showDeleteModal.value = true;
  };

  const deleteOrder = async () => {
    if (!orderToDelete.value || deleting.value) return;
    deleting.value = true;
    try {
      await axios.delete(`/api/orders/${orderToDelete.value.id}`, { headers: authHeaders() });
      orders.value = orders.value.filter(o => o.id !== orderToDelete.value.id);
      totalOrders.value = Math.max(0, totalOrders.value - 1);
      showToast(t('Order deleted successfully', 'تم حذف الطلب بنجاح'));
      showDeleteModal.value = false;
      orderToDelete.value = null;
      fetchStats();
      if (orders.value.length === 0 && currentPage.value > 1) {
        currentPage.value--;
      } else {
        fetchOrders();
      }
    } catch {
      showToast(t('Failed to delete order', 'فشل حذف الطلب'), 'error');
    } finally {
      deleting.value = false;
    }
  };

  // changeStatus closes the dropdown then delegates to updateOrderStatus
  const changeStatus = (order, newStatus) => {
    activeDropdownId.value = null;
    updateOrderStatus(order, newStatus);
  };

  const updateOrderStatus = async (order, newStatus) => {
    if (updatingStatusId.value === order.id || order.status === newStatus) return;

    const oldStatus = order.status;
    updatingStatusId.value = order.id;

    // Optimistic updates
    order.status = newStatus;
    if (statsData.value[oldStatus] !== undefined) statsData.value[oldStatus]--;
    statsData.value[newStatus] = (statsData.value[newStatus] ?? 0) + 1;

    try {
      await axios.patch(`/api/orders/${order.id}/status`, { status: newStatus }, { headers: authHeaders() });
      showToast(t('Order status updated successfully', 'تم تحديث حالة الطلب بنجاح'));
      fetchStats();
    } catch {
      order.status = oldStatus;
      showToast(t('Failed to update status. Reverting...', 'فشل تحديث الحالة. جاري التراجع...'), 'error');
      await fetchOrders();
      await fetchStats();
    } finally {
      updatingStatusId.value = null;
    }
  };

  const showToast = (msg, type = 'success') => {
    toastMessage.value = msg;
    toastType.value = type;
    setTimeout(() => { toastMessage.value = ''; }, 3000);
  };

  const seasons = computed(() => [...new Set(orders.value.map(o => o.season))].filter(Boolean));
  const fabrics = computed(() => [...new Set(orders.value.map(o => o.fabric_details?.type))].filter(Boolean));

  // filteredOrders === orders (server-side filtered); alias kept for template compatibility
  const filteredOrders = computed(() => orders.value);

  // Kanban grouping: must be a writable ref because vuedraggable uses v-model on sub-arrays.
  // watchEffect keeps it in sync with filteredOrders reactively without a deep watcher.
  const ordersByStatusComputed = ref(Object.fromEntries(kanbanStatuses.map(s => [s, []])));
  watchEffect(() => {
    const groups = Object.fromEntries(kanbanStatuses.map(s => [s, []]));
    for (const o of filteredOrders.value) {
      if (groups[o.status]) groups[o.status].push(o);
    }
    ordersByStatusComputed.value = groups;
  });

  // Alias used in pipeline template
  const ordersByStatus = ordersByStatusComputed;

  const handleDragChange = (evt, newStatus) => {
    if (evt.added) updateOrderStatus(evt.added.element, newStatus);
  };

  const formatTime = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString(isRtl.value ? 'ar-EG' : 'en-US', { month: 'short', day: 'numeric' });
  };

  const timeAgo = (dateStr) => {
    if (!dateStr) return '';
    const diffInSecs = Math.floor((Date.now() - new Date(dateStr)) / 1000);
    if (diffInSecs < 60) return t('Just now', 'الآن');
    if (diffInSecs < 3600) return Math.floor(diffInSecs / 60) + ' ' + t('m ago', 'دقيقة');
    if (diffInSecs < 86400) return Math.floor(diffInSecs / 3600) + ' ' + t('h ago', 'ساعة');
    return Math.floor(diffInSecs / 86400) + ' ' + t('d ago', 'يوم');
  };

  const exportOrders = async () => {
    try {
      const res = await axios.get('/api/orders/export', {
        headers: authHeaders(),
        responseType: 'blob'
      });
      const url = URL.createObjectURL(new Blob([res.data]));
      const link = Object.assign(document.createElement('a'), {
        href: url,
        download: `orders_${new Date().toISOString().split('T')[0]}.csv`
      });
      document.body.appendChild(link);
      link.click();
      link.remove();
      URL.revokeObjectURL(url);
    } catch {
      showToast(t('Export failed.', 'فشل التصدير.'), 'error');
    }
  };

  // Pagination Range helper
  const paginationRange = computed(() => {
    const from = totalOrders.value === 0 ? 0 : (currentPage.value - 1) * perPage.value + 1;
    const to = Math.min(currentPage.value * perPage.value, totalOrders.value);
    return { from, to, total: totalOrders.value };
  });

  </script>

  <style scoped>
  .orders-view { padding-bottom: 4rem; }
  .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; }
  .page-title { font-size: 2rem; font-weight: 800; color: #1e3a8a; margin: 0; letter-spacing: -0.02em; }
  .page-subtitle { color: #64748b; font-size: 0.9375rem; margin-top: 0.25rem; font-weight: 500; }

  .header-actions { display: flex; align-items: center; gap: 1.5rem; }

  .view-toggle { 
    display: flex; 
    background: #f1f5f9; 
    padding: 6px; 
    border-radius: 14px; 
    border: 1px solid #e2e8f0; 
  }
  .view-toggle button { 
    display: flex; 
    align-items: center; 
    gap: 8px; 
    padding: 8px 16px; 
    border: none; 
    border-radius: 10px; 
    font-size: 0.8125rem; 
    font-weight: 700; 
    color: #64748b; 
    cursor: pointer; 
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); 
    background: transparent; 
  }
  .view-toggle button.active { 
    background: white; 
    color: #3b82f6; 
    box-shadow: 0 4px 10px rgba(0,0,0,0.05); 
  }

  /* Stats Cards Grid */
  .stats-cards-row { 
    display: grid; 
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); 
    gap: 1.25rem; 
    margin-bottom: 2rem; 
  }
  .mini-stat-card { 
    background: white; 
    padding: 1.5rem; 
    border-radius: 24px; 
    border: 1px solid #f1f5f9; 
    display: flex; 
    flex-direction: column; 
    position: relative; 
    overflow: hidden; 
    box-shadow: 0 10px 15px -10px rgba(0, 0, 0, 0.04); 
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  }
  .mini-stat-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05); border-color: #e2e8f0; }
  
  .stat-indicator { 
    position: absolute; 
    left: 0; 
    top: 0; 
    bottom: 0; 
    width: 6px; 
    border-radius: 0 4px 4px 0;
  }
  .rtl .stat-indicator { left: auto; right: 0; border-radius: 4px 0 0 4px; }

  /* Stat Indicator Colors */
  .stat-submitted .stat-indicator { background: #94a3b8; }
  .stat-pending .stat-indicator { background: #f59e0b; }
  .stat-technical_ready .stat-indicator { background: #3b82f6; }
  .stat-in_review .stat-indicator { background: #0ea5e9; }
  .stat-sampling .stat-indicator { background: #8b5cf6; }
  .stat-approved .stat-indicator { background: #10b981; }
  .stat-production .stat-indicator { background: #f97316; }
  .stat-completed .stat-indicator { background: #22c55e; }
  .stat-cancelled .stat-indicator { background: #ef4444; }

  .stat-inner { position: relative; z-index: 2; }
  .mini-stat-label { font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px; }
  .mini-stat-value { font-size: 1.75rem; font-weight: 900; color: #1e293b; }

  /* Filters */
  .filters-row { margin-bottom: 2.5rem; }
  .filter-card { 
    background: white;
    padding: 1.5rem !important; 
    border-radius: 24px !important; 
    border: 1px solid #f1f5f9 !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02) !important;
  }
  .filter-flex { display: flex; flex-direction: column; gap: 1.5rem; }
  .filter-actions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; width: 100%; }

  .search-field { position: relative; width: 100%; }
  .search-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; width: 18px; }
  .rtl .search-icon { left: auto; right: 16px; }
  .search-field input { 
    width: 100%; 
    height: 48px; 
    padding-left: 3rem; 
    border: 1px solid #f1f5f9; 
    background: #f8fafc; 
    border-radius: 14px; 
    font-size: 14px; 
    font-weight: 600; 
    transition: all 0.2s; 
    color: #1e293b; 
  }
  .search-field input:focus { 
    background: white; 
    border-color: #3b82f6; 
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); 
    outline: none; 
  }

  .filter-select { 
    height: 48px; 
    padding: 0 1.25rem; 
    border: 1px solid #f1f5f9; 
    background: #f8fafc; 
    border-radius: 14px; 
    font-size: 13px; 
    font-weight: 700; 
    color: #475569; 
    outline: none; 
    cursor: pointer; 
    transition: all 0.2s; 
  }
  .filter-select:hover { border-color: #e2e8f0; background: #f1f5f9; }
  .filter-select:focus { border-color: #3b82f6; background: white; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
  /* Modern Table Layout */
  .table-card-wrapper {
    background: white;
    border-radius: 24px;
    border: 1px solid #eef2f6;
    box-shadow: 0 12px 30px -10px rgba(0, 0, 0, 0.04);
    overflow: visible;
    margin-top: 1.5rem;
  }

  table {
    display: table !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    width: 100% !important;
  }

  thead { display: table-header-group !important; }
  tbody { display: table-row-group !important; }
  tr { display: table-row !important; }
  th, td { display: table-cell !important; }

  .table-header-row th {
    background: #f8fafc;
    padding: 1.1rem 1.5rem;
    font-size: 0.75rem;
    font-weight: 850;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    border-bottom: 2px solid #f1f5f9;
    white-space: nowrap;
    text-align: start;
  }

  .rtl .table-header-row th { text-align: right; }

  .table-row-modern {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    background: white;
  }

  .table-row-modern:hover {
    background-color: #f1f7ff !important;
    box-shadow: inset 4px 0 0 #3b82f6;
  }

  /* Zebra Effect */
  .divide-y > tr:nth-child(even) {
    background-color: #f8fafc;
  }

  .table-row-modern td {
    padding: 1.25rem 1.5rem;
    vertical-align: middle;
  }



  .product-meta {
    font-size: 0.8125rem;
    color: #475569;
    font-weight: 600;
  }

  /* Brand Avatar */
  .brand-avatar {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    background: #eff6ff;
    color: #3b82f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.1rem;
    border: 2px solid #fff;
    box-shadow: 0 4px 10px rgba(59, 130, 246, 0.1);
    flex-shrink: 0;
  }

  .brand-avatar.sm {
    width: 32px;
    height: 32px;
    font-size: 0.9rem;
    border-radius: 10px;
  }

  .client-name {
    font-size: 0.875rem;
    font-weight: 800;
    color: #0f172a;
  }

  .client-status {
    font-size: 0.7rem;
    color: #3b82f6;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 2px;
  }

  .status-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #3b82f6;
  }

  .category-tag {
    font-size: 0.7rem;
    font-weight: 850;
    color: #334155;
    background: #f1f5f9;
    padding: 6px 14px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  /* Enhanced Status Pills */
  .status-pill-v2 {
    padding: 8px 16px;
    border-radius: 14px;
    font-size: 0.75rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: 1px solid transparent;
    transition: all 0.2s;
    cursor: pointer;
    min-width: 125px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  .status-pill-v2.sm {
    padding: 4px 12px;
    font-size: 0.65rem;
    min-width: auto;
    border-radius: 10px;
  }

  .status-pill-v2.submitted { background: #f1f5f9; color: #475569; border-color: #cbd5e1; }
  .status-pill-v2.pending { background: #fffbeb; color: #92400e; border-color: #fcd34d; }
  .status-pill-v2.technical_ready { background: #eff6ff; color: #1e40af; border-color: #93c5fd; }
  .status-pill-v2.in_review { background: #f0f9ff; color: #075985; border-color: #7dd3fc; }
  .status-pill-v2.sampling { background: #f5f3ff; color: #5b21b6; border-color: #c4b5fd; }
  .status-pill-v2.approved { background: #f0fdf4; color: #166534; border-color: #86efac; }
  .status-pill-v2.production { background: #fff7ed; color: #9a3412; border-color: #fdba74; }
  .status-pill-v2.completed { background: #ecfdf5; color: #065f46; border-color: #6ee7b7; }
  .status-pill-v2.cancelled { background: #fef2f2; color: #991b1b; border-color: #fca5a5; }



  .status-pill-v2.readonly { cursor: default; }

  .status-cell-relative { position: relative; }

  .actions-group-wrapper {
      display: flex !important;
      flex-direction: row !important;
      align-items: center !important;
      justify-content: flex-end !important;
      gap: 12px;
      opacity: 1; /* Always visible for maximum accessibility */
      transition: all 0.3s;
      width: 100%;
      white-space: nowrap !important;
  }

  /* Action Buttons refinement */
  .action-circle-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    color: #475569;
    border: 1.5px solid #e2e8f0;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    flex-shrink: 0;
  }

  .action-circle-btn svg {
    stroke: currentColor;
    stroke-width: 2.2;
    overflow: visible;
    transition: stroke 0.2s;
  }

  .action-circle-btn:hover {
    transform: translateY(-2px);
    color: white !important;
    border-color: #2563eb;
    background: #2563eb;
    box-shadow: 0 8px 15px -3px rgba(37, 99, 235, 0.25);
  }

  /* Specific roles with high-contrast states */
  .action-circle-btn.primary { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
  .action-circle-btn.primary:hover {
    background: #2563eb;
    color: white !important;
    border-color: #2563eb;
  }

  .action-circle-btn.danger { background: #fff1f2; color: #e11d48; border-color: #fecdd3; }
  .action-circle-btn.danger:hover {
    background: #e11d48;
    color: white !important;
    border-color: #e11d48;
    box-shadow: 0 8px 15px -3px rgba(225, 29, 72, 0.3);
  }

  .action-circle-btn:hover svg { stroke: white !important; }

  /* Status Dropdown Refinement */
  .status-dropdown-menu {
    position: absolute;
    top: calc(100% + 12px);
    left: 50%;
    transform: translateX(-50%);
    width: 250px;
    background: white;
    border-radius: 18px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2);
    border: 1px solid #e2e8f0;
    z-index: 1000;
    padding: 10px;
    overflow: visible;
    animation: dropdownPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  }

  @keyframes dropdownPop {
    from { opacity: 0; transform: translateX(-50%) translateY(-10px) scale(0.95); }
    to { opacity: 1; transform: translateX(-50%) translateY(0) scale(1); }
  }

  .dropdown-header {
    padding: 10px 14px;
    font-size: 0.7rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 8px;
    text-align: center;
  }

  .dropdown-item {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 700;
    color: #475569;
    transition: all 0.2s;
    background: transparent;
    border: none;
    cursor: pointer;
    text-align: start;
  }

  .dropdown-item:hover {
    background: #f0f7ff;
    color: #2563eb;
    transform: translateX(4px);
  }

  .rtl .dropdown-item:hover { transform: translateX(-4px); }

  .dropdown-item.is-active {
    background: #eff6ff;
    color: #2563eb;
    filter: none;
  }

  .item-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
    box-shadow: 0 0 0 3px rgba(0,0,0,0.03);
  }

  .item-dot.submitted, .item-dot.pending { background: #94a3b8; }
  .item-dot.technical_ready { background: #3b82f6; }
  .item-dot.in_review { background: #0ea5e9; }
  .item-dot.sampling { background: #8b5cf6; }
  .item-dot.approved { background: #10b981; }
  .item-dot.production { background: #f59e0b; }
  .item-dot.completed { background: #16a34a; }
  .item-dot.cancelled { background: #ef4444; }

  /* Mobile-First Refined Cards */
  .order-mobile-card {
    background: white;
    border-radius: 28px;
    padding: 1.75rem 1.5rem;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 12px 35px -12px rgba(0, 0, 0, 0.1);
    border: 1px solid #f1f5f9;
    margin-bottom: 0.5rem;
  }

  .order-mobile-card:active { 
    transform: scale(0.96) translateY(2px);
    background-color: #fafbfc;
  }

  /* Custom Mobile Utility Buttons */
  .mobile-btn-primary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #2563eb;
    color: white !important;
    border-radius: 18px;
    padding: 14px 10px;
    font-size: 13px;
    font-weight: 900;
    box-shadow: 0 8px 20px -6px rgba(37, 99, 235, 0.35);
    border: none;
    transition: all 0.2s;
  }

  .mobile-btn-primary svg {
    stroke: white !important;
    stroke-width: 2.2;
    overflow: visible;
  }

  .mobile-btn-secondary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: #f8fafc;
    color: #475569 !important;
    border-radius: 18px;
    padding: 14px 10px;
    font-size: 13px;
    font-weight: 900;
    border: 1.5px solid #e2e8f0;
    transition: all 0.2s;
  }

  .mobile-btn-secondary svg {
    stroke: currentColor;
    stroke-width: 2.5;
    overflow: visible;
  }

  .category-tag-v3 {
    font-size: 11px;
    font-weight: 900;
    color: #475569;
    background: #f1f5f9;
    padding: 6px 14px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
  }

  .card-status-accent {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 5px;
    background: #e2e8f0;
    z-index: 1;
  }
  .rtl .card-status-accent { left: auto; right: 0; }

  .order-mobile-card.submitted .card-status-accent,
  .order-mobile-card.pending .card-status-accent { background: #94a3b8; }
  .order-mobile-card.technical_ready .card-status-accent { background: #3b82f6; }
  .order-mobile-card.in_review .card-status-accent { background: #0ea5e9; }
  .order-mobile-card.sampling .card-status-accent { background: #8b5cf6; }
  .order-mobile-card.approved .card-status-accent { background: #10b981; }
  .order-mobile-card.production .card-status-accent { background: #f59e0b; }
  .order-mobile-card.completed .card-status-accent { background: #16a34a; }
  .order-mobile-card.cancelled .card-status-accent { background: #ef4444; }

  .info-item .label {
    display: block;
    font-size: 0.65rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    margin-bottom: 4px;
    letter-spacing: 0.05em;
  }

  .info-item .value {
    font-size: 0.875rem;
    font-weight: 800;
    color: #1e293b;
  }

  .status-loading-spinner {
    width: 16px;
    height: 16px;
    border: 2.5px solid currentColor;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }

  .empty-state-icon { color: #cbd5e1; display: flex; justify-content: center; }

  /* Transitions */
  .fade-pop-enter-active, .fade-pop-leave-active { transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
  .fade-pop-enter-from, .fade-pop-leave-to { opacity: 0; transform: translateY(-10px) scale(0.95); }

  /* Pagination Footer Context */
  .pagination-footer-standalone {
    background: white;
    border-radius: 24px;
    margin-top: 2rem;
    padding: 1.25rem 2rem;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.05);
  }

  .pagination-flex-container {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
  }

  /* Left Side */
  .results-text { font-size: 0.875rem; color: #64748b; font-weight: 600; }
  .count-highlight { color: #2563eb; font-weight: 800; margin: 0 4px; }
  .count-total { color: #0f172a; font-weight: 900; margin: 0 4px; }

  /* Center Side */
  .per-page-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    padding: 6px 16px;
    border-radius: 14px;
    border: 1px solid #eff2f5;
  }
  .per-page-label { font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
  .select-modern-v3 {
    background: transparent;
    border: none;
    font-size: 0.875rem;
    font-weight: 800;
    color: #1e293b;
    cursor: pointer;
    outline: none;
    padding: 2px 4px;
  }

  /* Right Side */
  .pagination-nav-side { display: flex; align-items: center !important; gap: 12px; }
  .btn-pag-standard {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    background: white;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.8125rem;
    font-weight: 700;
    color: #475569;
    transition: all 0.2s;
    cursor: pointer;
  }
  .btn-pag-standard:hover:not(:disabled) {
    border-color: #3b82f6;
    color: #2563eb;
    background: #f0f7ff;
    transform: translateY(-1px);
  }
  .btn-pag-standard:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    background: #f8fafc;
  }

  .page-indicator-glow {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 0 16px;
    background: #eff6ff;
    border-radius: 12px;
    border: 1px solid #dbeafe;
    min-height: 42px;
  }
  .page-indicator-glow .current { color: #2563eb; font-weight: 900; font-size: 0.9375rem; }
  .page-indicator-glow .separator { color: #bfdbfe; font-weight: 400; }
  .page-indicator-glow .total { color: #64748b; font-weight: 700; font-size: 0.8125rem; }

  @media (max-width: 768px) {
    .pagination-flex-container { flex-direction: column; gap: 1.5rem; text-align: center; }
    .pagination-nav-side { width: 100%; justify-content: center; }
  }

  /* Shared Hierarchy */
  .product-title {
    font-size: 1rem;
    font-weight: 900;
    color: #0f172a;
    display: block;
  }

  .id-badge {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.65rem;
    font-weight: 800;
    color: #2563eb;
    background: #eff6ff;
    padding: 4px 10px;
    border-radius: 8px;
    border: 1px solid #dbeafe;
    display: inline-block;
    letter-spacing: 0.02em;
  }

  /* Professional Kanban / Pipeline View Styles */
  .pipeline-container {
    display: flex;
    gap: 1.5rem;
    overflow-x: auto;
    padding: 0.5rem 0.5rem 2rem 0.5rem;
    min-height: calc(100vh - 350px);
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
  }

  /* Custom scrollbar for Kanban */
  .pipeline-container::-webkit-scrollbar { height: 8px; }
  .pipeline-container::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
  .pipeline-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
  .pipeline-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

  .pipeline-column {
    flex: 0 0 320px;
    background: #f8fafc;
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    max-height: 100%;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  }

  .column-header {
    padding: 1.25rem;
    border-bottom: 2px solid #f1f5f9;
    background: white;
    border-radius: 20px 20px 0 0;
  }

  .header-content {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .column-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(0,0,0,0.05);
  }
  .column-dot.submitted, .column-dot.pending { background: #94a3b8; }
  .column-dot.technical_ready { background: #3b82f6; }
  .column-dot.in_review { background: #0ea5e9; }
  .column-dot.sampling { background: #8b5cf6; }
  .column-dot.approved { background: #10b981; }
  .column-dot.production { background: #f59e0b; }
  .column-dot.completed { background: #16a34a; }

  .column-title {
    font-size: 0.9375rem;
    font-weight: 800;
    color: #1e293b;
    flex: 1;
  }

  .column-count {
    background: #eff6ff;
    color: #2563eb;
    font-size: 0.75rem;
    font-weight: 800;
    padding: 4px 10px;
    border-radius: 9999px;
  }

  .column-body {
    padding: 1rem;
    overflow-y: auto;
    flex: 1;
  }

  .draggable-area {
    min-height: 100px;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .kanban-card {
    background: white;
    border-radius: 16px;
    padding: 1.25rem;
    border: 1px solid #edf2f7;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }

  .kanban-card:hover {
    transform: translateY(-4px);
    border-color: #3b82f6;
    box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.15);
  }

  .card-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
  }

  .card-id {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.75rem;
    font-weight: 800;
    color: #2563eb;
    background: #eff6ff;
    padding: 4px 8px;
    border-radius: 6px;
  }

  .card-time {
    font-size: 0.7rem;
    font-weight: 600;
    color: #94a3b8;
  }

  .card-title {
    font-size: 0.9375rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 12px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .card-client {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8125rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 16px;
  }

  .card-audit {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.7rem;
    font-weight: 600;
    color: #94a3b8;
    margin-bottom: 16px;
    padding-top: 12px;
    border-top: 1px dashed #f1f5f9;
  }

  .card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
  }

  .meta-tag {
    font-size: 0.7rem;
    font-weight: 800;
    color: #475569;
    background: #f1f5f9;
    padding: 4px 10px;
    border-radius: 6px;
  }

  .kanban-next-btn {
    background: #2563eb;
    color: white;
    font-size: 0.75rem;
    font-weight: 800;
    padding: 6px 14px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
  }

  .kanban-next-btn:hover {
    background: #1d4ed8;
    transform: scale(1.05);
  }

  .ghost-card {
    opacity: 0.4;
    background: #e2e8f0;
    border: 2px dashed #94a3b8;
  }

  .drag-card {
    transform: rotate(2deg) scale(1.05);
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2) !important;
    z-index: 1001;
  }
  </style>
