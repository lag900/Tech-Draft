<template>
  <Layout>
    <div class="dashboard-view" :class="{ rtl: isRtl }">
      <!-- Top Section: Welcome & Quick Actions -->
      <div class="top-section">
        <div v-if="user?.role === 'client'" class="welcome-card client-header animate-fade-in">
          <div class="welcome-content">
            <h1 class="portal-greeting">
              {{
                t(
                  'Ahlan, ' + (user?.name || 'Partner') + '!',
                  'أهلاً، ' + (user?.name || 'شريكنا') + '!'
                )
              }}
            </h1>
            <p class="portal-description">
              {{
                t(
                  'Your production pipeline is moving smoothly today.',
                  'مسار الإنتاج الخاص بك يسير بسلاسة اليوم.'
                )
              }}
            </p>
            <div class="dashboard-chips">
              <span class="chip-item"
                ><span class="chip-dot active"></span>
                {{ t('Factory Online', 'المصنع متصل') }}</span
              >
              <span class="chip-item"
                ><span class="chip-dot"></span> {{ statsData.production || 0 }}
                {{ t('Orders in Production', 'طلبات في الإنتاج') }}</span
              >
            </div>
          </div>
          <div class="quick-actions">
            <BaseButton size="lg" class="premium-btn" @click="$router.push('/orders/create')">
              <template #icon-left>
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.5"
                >
                  <path d="M12 5v14M5 12h14" />
                </svg>
              </template>
              {{ t('Create New Order', 'إنشاء طلب جديد') }}
            </BaseButton>
          </div>
        </div>

        <div v-else class="welcome-card">
          <div class="welcome-content">
            <h1>{{ t('Dashboard', 'لوحة التحكم') }}</h1>
            <p>
              {{
                t(
                  'Welcome back, ' + (user?.name || 'Friend') + "! Here is what's happening today.",
                  'مرحباً بعودتك، ' + (user?.name || 'صديقنا') + '! إليك ما يحدث اليوم.'
                )
              }}
            </p>
          </div>
          <div v-if="user?.role === 'admin'" class="quick-actions">
            <BaseButton @click="$router.push('/orders/create')">
              <template #icon-left>
                <svg
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M12 5v14M5 12h14" />
                </svg>
              </template>
              {{ t('New Manufacturing Order', 'طلب تصنيع جديد') }}
            </BaseButton>
          </div>
        </div>
      </div>

      <!-- KPI Grid -->
      <div class="kpi-grid" :class="{ 'client-kpis': user?.role === 'client' }">
        <BaseCard v-for="kpi in filteredKpis" :key="kpi.labelEn" hoverable class="kpi-card-portal">
          <div class="kpi-item">
            <div class="kpi-icon" :style="{ backgroundColor: kpi.bg, color: kpi.color }">
              <component :is="kpi.icon" />
            </div>
            <div class="kpi-data">
              <span class="kpi-label">{{ t(kpi.labelEn, kpi.labelAr) }}</span>
              <div class="kpi-value-row">
                <span class="kpi-value">{{ kpi.value }}</span>
                <span
                  v-if="kpi.trend && user?.role !== 'client'"
                  class="kpi-trend"
                  :class="kpi.trend > 0 ? 'up' : 'down'"
                >
                  {{ kpi.trend > 0 ? '+' : '' }}{{ kpi.trend }}%
                </span>
              </div>
            </div>
          </div>
        </BaseCard>
      </div>

      <!-- Production Pipeline for Clients -->
      <div v-if="user?.role === 'client'" class="pipeline-section">
        <BaseCard
          :title="t('Production Pipeline', 'مسار الإنتاج')"
          :subtitle="
            t(
              'Current status of your orders moving through factory',
              'حالة طلباتك الحالية أثناء انتقالها عبر المصنع'
            )
          "
        >
          <div class="pipeline-container">
            <div
              v-for="step in pipelineSteps"
              :key="step.key"
              class="pipeline-step"
              :class="{ active: statsData[step.key] > 0 }"
            >
              <div class="step-icon" :class="step.key">
                <component :is="step.icon" />
                <span v-if="statsData[step.key] > 0" class="step-count">{{
                  statsData[step.key]
                }}</span>
              </div>
              <span class="step-label">{{ t(step.labelEn, step.labelAr) }}</span>
            </div>
          </div>
        </BaseCard>
      </div>

      <!-- Main Content Grid -->
      <div class="main-grid">
        <BaseCard
          :title="t('Recent Orders', 'الطلبات الأخيرة')"
          :subtitle="t('Last 5 active manufacturing orders', 'آخر ٥ طلبات تصنيع نشطة')"
          no-padding
        >
          <template #actions>
            <BaseButton variant="ghost" size="sm" @click="$router.push('/orders')">
              {{ t('View All', 'عرض الكل') }}
            </BaseButton>
          </template>

          <div class="table-responsive">
            <table class="modern-table">
              <thead>
                <tr>
                  <th>{{ t('Order Code', 'كود الطلب') }}</th>
                  <th v-if="user?.role !== 'client'">{{ t('Client', 'العميل') }}</th>
                  <th>{{ t('Category', 'التصنيف') }}</th>
                  <th>{{ t('Status', 'الحالة') }}</th>
                  <th class="text-right">{{ t('Date', 'التاريخ') }}</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="order in recentOrders"
                  :key="order.id"
                  class="clickable-row"
                  @click="$router.push('/orders/' + order.id)"
                >
                  <td>
                    <span class="order-code">#{{ order.order_code }}</span>
                  </td>
                  <td v-if="user?.role !== 'client'">
                    {{ order.client?.brand_name || 'Individual' }}
                  </td>
                  <td>{{ order.category?.name || '---' }}</td>
                  <td>
                    <span class="status-pill" :class="order.status">
                      {{ t(statusMap[order.status]?.en, statusMap[order.status]?.ar) }}
                    </span>
                  </td>
                  <td class="date-cell text-right">{{ formatDate(order.created_at) }}</td>
                </tr>
                <tr v-if="recentOrders.length === 0">
                  <td colspan="5" class="empty-state">
                    {{ t('No recent orders found.', 'لا توجد طلبات حديثة.') }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </BaseCard>

        <BaseCard
          :title="t('Quick Stats', 'إحصائيات سريعة')"
          :subtitle="t('Order status distribution', 'توزيع حالات الطلبات')"
          no-padding
          class="quick-stats-card"
        >
          <div class="stats-list custom-scrollbar">
            <div v-for="key in Object.keys(statusMap)" :key="key" class="stat-row">
              <div class="stat-info">
                <span class="stat-dot" :class="key"></span>
                <span class="stat-name">{{ t(statusMap[key].en, statusMap[key].ar) }}</span>
              </div>
              <span class="stat-count">{{ statsByStatus[key] || 0 }}</span>
            </div>
          </div>
          <template #footer>
            <div class="footer-note">
              {{ t('Syncing with factory...', 'جاري المزامنة مع المصنع...') }}
            </div>
          </template>
        </BaseCard>
      </div>
    </div>
  </Layout>
</template>

<script setup>
  import { useLang } from '../composables/useLang';
  import { ref, onMounted, computed, h } from 'vue';
  import Layout from '../components/Layout.vue';
  import BaseCard from '../components/UI/BaseCard.vue';
  import BaseButton from '../components/UI/BaseButton.vue';
  import axios from 'axios';

  const getUser = () => {
    try {
      return JSON.parse(localStorage.getItem('user') || 'null');
    } catch (e) {
      return null;
    }
  };
  const user = ref(getUser());
  const { isRtl, t } = useLang();

  const recentOrders = ref([]);
  const stats = ref({
    total: 0,
    pending: 0,
    production: 0,
    completed: 0,
    totalDesigns: 0,
    totalSeasons: 0,
  });
  const statsByStatus = ref({});

  const statusMap = {
    pending: { en: 'Pending', ar: 'قيد الانتظار' },
    in_review: { en: 'In Review', ar: 'قيد المراجعة' },
    sampling: { en: 'Sampling', ar: 'عينات' },
    approved: { en: 'Approved', ar: 'مقبول' },
    production: { en: 'Production', ar: 'تصنيع' },
    completed: { en: 'Completed', ar: 'مكتمل' },
    cancelled: { en: 'Cancelled', ar: 'ملغي' },
  };

  const formatDate = (dateStr) => {
    if (!dateStr) return '---';
    const d = new Date(dateStr);
    return d.toLocaleDateString(isRtl.value ? 'ar-EG' : 'en-US', {
      day: 'numeric',
      month: 'short',
    });
  };

  // KPI Icons
  const IconBox = () =>
    h(
      'svg',
      {
        width: 22,
        height: 22,
        viewBox: '0 0 24 24',
        fill: 'none',
        stroke: 'currentColor',
        'stroke-width': 2,
      },
      [
        h('path', {
          d: 'M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z',
        }),
        h('path', { d: 'm3.3 7 8.7 5 8.7-5' }),
        h('path', { d: 'M12 22V12' }),
      ]
    );
  const IconUsers = () =>
    h(
      'svg',
      {
        width: 22,
        height: 22,
        viewBox: '0 0 24 24',
        fill: 'none',
        stroke: 'currentColor',
        'stroke-width': 2,
      },
      [
        h('path', { d: 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2' }),
        h('circle', { cx: 9, cy: 7, r: 4 }),
        h('path', { d: 'M22 21v-2a4 4 0 0 0-3-3.87' }),
        h('path', { d: 'M16 3.13a4 4 0 0 1 0 7.75' }),
      ]
    );
  const IconLayers = () =>
    h(
      'svg',
      {
        width: 22,
        height: 22,
        viewBox: '0 0 24 24',
        fill: 'none',
        stroke: 'currentColor',
        'stroke-width': 2,
      },
      [
        h('polygon', { points: '12 2 2 7 12 12 22 7 12 2' }),
        h('polyline', { points: '2 17 12 22 22 17' }),
        h('polyline', { points: '2 12 12 17 22 12' }),
      ]
    );

  const kpis = computed(() => [
    {
      labelEn: 'Active Orders',
      labelAr: 'الطلبات النشطة',
      value:
        (statsData.value.pending || 0) +
        (statsData.value.in_review || 0) +
        (statsData.value.sampling || 0) +
        (statsData.value.approved || 0) +
        (statsData.value.production || 0),
      trend: 12,
      bg: '#eff6ff',
      color: '#3b82f6',
      icon: IconBox,
      roles: ['superadmin', 'admin', 'client', 'manager', 'dataentry'],
    },
    {
      labelEn: 'Total Products',
      labelAr: 'إجمالي المنتجات',
      value: stats.value.totalDesigns,
      trend: 5,
      bg: '#f5f3ff',
      color: '#8b5cf6',
      icon: IconLayers,
      roles: ['superadmin', 'admin', 'manager', 'client'],
    },
    {
      labelEn: 'Total Seasons',
      labelAr: 'إجمالي المواسم',
      value: stats.value.totalSeasons,
      trend: 0,
      bg: '#f0fdf4',
      color: '#22c55e',
      icon: IconBox,
      roles: ['superadmin', 'admin', 'manager', 'dataentry'],
    },
    {
      labelEn: 'Completed',
      labelAr: 'طلبات مكتملة',
      value: statsData.value.completed || 0,
      trend: 8,
      bg: '#fffaf0',
      color: '#f59e0b',
      icon: IconBox,
      roles: ['superadmin', 'admin', 'client', 'manager'],
    },
  ]);

  const pipelineSteps = [
    { key: 'pending', labelEn: 'Pending', labelAr: 'انتظار', icon: IconBox },
    { key: 'in_review', labelEn: 'Review', labelAr: 'مراجعة', icon: IconLayers },
    { key: 'sampling', labelEn: 'Sampling', labelAr: 'عينات', icon: IconBox },
    { key: 'production', labelEn: 'Production', labelAr: 'إنتاج', icon: IconLayers },
    { key: 'completed', labelEn: 'Completed', labelAr: 'مكتمل', icon: IconBox },
  ];

  const filteredKpis = computed(() => {
    const userRole = user.value?.role;
    if (!userRole) return [];
    return kpis.value.filter((k) => k.roles.includes(userRole));
  });

  const statsData = ref({});

  onMounted(async () => {
    try {
      const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };
      const [orderRes, designRes, statRes] = await Promise.all([
        axios.get('/api/orders', { headers }),
        axios.get('/api/designs', { headers }),
        axios.get('/api/orders/stats', { headers }),
      ]);

      const ordersRaw = orderRes.data || [];
      const orders = Array.isArray(ordersRaw) ? ordersRaw : ordersRaw.data || [];
      const designsRaw = designRes.data || [];
      const designs = Array.isArray(designsRaw) ? designsRaw : designsRaw.data || [];
      const counts = statRes.data || {};

      statsData.value = counts;
      recentOrders.value = orders.slice(0, 5);
      stats.value.total = orders.length;
      stats.value.totalDesigns = designs.length;

      // Unique seasons count
      const seasons = new Set(
        [...orders.map((o) => o.season), ...designs.map((d) => d.season)].filter(Boolean)
      );
      stats.value.totalSeasons = seasons.size;

      statsByStatus.value = counts;

      stats.value.pending =
        (counts.pending || 0) +
        (counts.in_review || 0) +
        (counts.sampling || 0) +
        (counts.approved || 0);
      stats.value.production = counts.production || 0;
      stats.value.completed = counts.completed || 0;
    } catch (e) {
      console.error('Error loading dashboard stats:', e);
    }
  });
</script>

<style scoped>
  /* === Dashboard Layout === */
  .dashboard-view {
    max-width: 1280px;
    margin: 0 auto;
  }

  /* === Top Section: Welcome + Actions === */
  .top-section {
    margin-bottom: 1.75rem;
  }

  .welcome-card {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 1.5rem;
  }

  .welcome-content h1 {
    font-size: 1.875rem;
    font-weight: 800;
    color: #1a3a5f;
    margin: 0;
    letter-spacing: -0.02em;
  }

  .welcome-content p {
    color: #64748b;
    font-size: 1rem;
    margin-top: 0.375rem;
  }

  /* Client Premium Header */
  .client-header {
    background: linear-gradient(135deg, #1a3a5f 0%, #2a5298 100%);
    border-radius: 20px;
    padding: 2.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 40px -15px rgba(26, 58, 95, 0.3);
    border: none;
  }

  .client-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
  }

  .portal-greeting {
    font-size: 2.25rem;
    font-weight: 900;
    color: white !important;
    margin: 0;
    letter-spacing: -0.03em;
  }

  .portal-description {
    color: rgba(255, 255, 255, 0.8) !important;
    font-size: 1.0625rem !important;
    margin-top: 0.75rem;
    max-width: 500px;
  }

  .dashboard-chips {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.5rem;
  }

  .chip-item {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(12px);
    padding: 0.5rem 1rem;
    border-radius: 99px;
    font-size: 0.8125rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
  }

  .chip-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
  }

  .chip-dot.active {
    background: #34d399;
    box-shadow: 0 0 10px #34d399;
  }

  .premium-btn {
    background: white !important;
    color: #1a3a5f !important;
    border-radius: 12px !important;
    font-weight: 800 !important;
    box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.2) !important;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
  }

  .premium-btn:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.3) !important;
  }

  /* === KPI Statistics Grid === */
  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.75rem;
  }

  .kpi-card-portal {
    background: white;
    border-radius: 14px;
    border: 1px solid #e8edf2;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;
  }

  .kpi-card-portal:hover {
    border-color: #0ea5e9;
    box-shadow: 0 8px 24px -8px rgba(14, 165, 233, 0.15);
    transform: translateY(-2px);
  }

  .kpi-item {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .kpi-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .kpi-data {
    flex: 1;
    min-width: 0;
  }

  .kpi-label {
    font-size: 0.6875rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
    display: block;
  }

  .kpi-value-row {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
  }

  .kpi-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1a3a5f;
    line-height: 1;
  }

  .kpi-trend {
    font-size: 0.6875rem;
    font-weight: 800;
    padding: 0.15rem 0.5rem;
    border-radius: 6px;
  }

  .kpi-trend.up {
    background: #dcfce7;
    color: #166534;
  }
  .kpi-trend.down {
    background: #fee2e2;
    color: #b91c1c;
  }

  /* === Pipeline Section (Client Only) === */
  .pipeline-section {
    margin-bottom: 1.75rem;
  }

  .pipeline-container {
    display: flex;
    justify-content: space-between;
    padding: 2rem 1rem;
    position: relative;
  }

  .pipeline-container::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 5%;
    right: 5%;
    height: 2px;
    background: #f1f5f9;
    z-index: 1;
    transform: translateY(-24px);
  }

  .pipeline-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    position: relative;
    z-index: 2;
    flex: 1;
  }

  .step-icon {
    width: 52px;
    height: 52px;
    background: white;
    border: 2px solid #f1f5f9;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    position: relative;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  }

  .pipeline-step.active .step-icon {
    border-color: #0ea5e9;
    color: #0ea5e9;
    transform: scale(1.1);
    box-shadow: 0 10px 20px -5px rgba(14, 165, 233, 0.2);
  }

  .step-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ef4444;
    color: white;
    font-size: 0.65rem;
    font-weight: 800;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
  }

  .step-label {
    font-size: 0.8125rem;
    font-weight: 700;
    color: #64748b;
  }

  .pipeline-step.active .step-label {
    color: #1a3a5f;
    font-weight: 800;
  }

  /* === Main Content Grid (Recent Orders + Quick Stats) === */
  .main-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
    align-items: start;
  }

  /* === Table Styles === */
  .table-responsive {
    overflow-x: auto;
  }

  .modern-table {
    width: 100%;
    border-collapse: collapse;
  }

  .modern-table th {
    text-align: left;
    padding: 0.875rem 1.25rem;
    background: #fafbfc;
    font-size: 0.6875rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid #f1f5f9;
  }

  .modern-table td {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f8fafc;
    font-size: 0.875rem;
    color: #475569;
    font-weight: 600;
  }

  .clickable-row {
    cursor: pointer;
    transition: background 0.2s;
  }

  .clickable-row:hover {
    background: #f8fafc;
  }

  .order-code {
    font-weight: 800;
    color: #0ea5e9;
    font-family: 'JetBrains Mono', monospace;
  }

  /* Status Pills */
  .status-pill {
    padding: 0.3rem 0.75rem;
    border-radius: 99px;
    font-size: 0.6875rem;
    font-weight: 800;
    display: inline-block;
    white-space: nowrap;
  }

  .status-pill.pending {
    background: #fef3c7;
    color: #92400e;
  }
  .status-pill.submitted {
    background: #f1f5f9;
    color: #475569;
  }
  .status-pill.in_review {
    background: #fff7ed;
    color: #9a3412;
  }
  .status-pill.sampling {
    background: #eff6ff;
    color: #1e40af;
  }
  .status-pill.approved {
    background: #f0fdf4;
    color: #166534;
  }
  .status-pill.production {
    background: #fdf2f8;
    color: #9d174d;
  }
  .status-pill.completed {
    background: #f0fdf4;
    color: #166534;
  }
  .status-pill.cancelled {
    background: #fef2f2;
    color: #991b1b;
  }

  .date-cell {
    color: #94a3b8;
    font-weight: 600;
    text-align: right;
    white-space: nowrap;
  }

  /* === Quick Stats Panel === */
  .quick-stats-card {
    display: flex;
    flex-direction: column;
  }
  .stats-list {
    padding: 1.25rem;
    max-height: 380px;
    overflow-y: auto;
  }

  /* Custom Scrollbar */
  .custom-scrollbar::-webkit-scrollbar {
    width: 5px;
  }
  .custom-scrollbar::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 8px;
  }
  .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 8px;
  }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }

  .stat-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.625rem 0;
  }

  .stat-row + .stat-row {
    border-top: 1px solid #f8fafc;
  }

  .stat-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .stat-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
  }

  .stat-name {
    font-size: 0.8125rem;
    font-weight: 700;
    color: #475569;
  }

  .stat-count {
    font-size: 1rem;
    font-weight: 800;
    color: #1a3a5f;
    background: #f8fafc;
    padding: 0.25rem 0.75rem;
    border-radius: 8px;
    min-width: 36px;
    text-align: center;
  }

  .stat-dot.pending {
    background: #f59e0b;
  }
  .stat-dot.submitted {
    background: #64748b;
  }
  .stat-dot.in_review {
    background: #f97316;
  }
  .stat-dot.sampling {
    background: #3b82f6;
  }
  .stat-dot.approved {
    background: #10b981;
  }
  .stat-dot.production {
    background: #ec4899;
  }
  .stat-dot.completed {
    background: #10b981;
  }
  .stat-dot.cancelled {
    background: #ef4444;
  }

  .footer-note {
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 600;
    padding: 0.875rem 1.25rem;
    border-top: 1px solid #f1f5f9;
    text-align: center;
  }

  /* === Utils === */
  .text-right {
    text-align: right;
  }
  .empty-state {
    padding: 4rem 2rem !important;
    text-align: center;
    color: #94a3b8;
    font-style: italic;
    font-weight: 500;
  }

  .animate-fade-in {
    animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(12px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* === RTL Fixes === */
  .rtl .welcome-card,
  .rtl .welcome-content {
    text-align: right;
  }
  .rtl .modern-table th {
    text-align: right;
  }
  .rtl .text-right,
  .rtl .date-cell {
    text-align: left;
  }
  .rtl .pipeline-container::after {
    transform: translateY(-24px) scaleX(-1);
  }

  /* === Responsive: Tablet (1024px) === */
  @media (max-width: 1024px) {
    .kpi-grid {
      grid-template-columns: repeat(2, 1fr);
    }

    .main-grid {
      grid-template-columns: 1fr;
    }
  }

  /* === Responsive: Mobile (768px) === */
  @media (max-width: 768px) {
    .dashboard-view {
      padding: 0 0.25rem;
    }

    .kpi-grid {
      grid-template-columns: 1fr;
      gap: 0.75rem;
    }

    .welcome-card {
      flex-direction: column;
      align-items: flex-start;
      gap: 1rem;
    }

    .welcome-content h1 {
      font-size: 1.5rem;
    }

    .client-header {
      padding: 1.75rem 1.25rem;
      border-radius: 16px;
    }

    .portal-greeting {
      font-size: 1.75rem;
    }

    .portal-description {
      font-size: 0.9375rem !important;
    }

    .dashboard-chips {
      flex-wrap: wrap;
      gap: 0.5rem;
    }

    .pipeline-container {
      overflow-x: auto;
      justify-content: flex-start;
      gap: 1.5rem;
      padding-bottom: 2rem;
    }

    .pipeline-container::after {
      display: none;
    }

    .pipeline-step {
      flex: none;
      min-width: 80px;
    }

    .main-grid {
      grid-template-columns: 1fr;
      gap: 1rem;
    }

    .modern-table th {
      padding: 0.75rem 1rem;
    }

    .modern-table td {
      padding: 0.75rem 1rem;
      font-size: 0.8125rem;
    }

    .stats-list {
      max-height: none;
      overflow-y: visible;
    }

    .kpi-value {
      font-size: 1.5rem;
    }

    .kpi-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
    }
  }
</style>
