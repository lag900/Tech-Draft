<template>
  <Layout>
    <div v-if="client" class="client-detail-view" :class="{ rtl: isRtl }">
      <!-- Breadcrumbs & Header -->
      <div class="page-header">
        <div class="header-left">
          <div class="breadcrumb-mini">
            <router-link to="/clients">{{ t('Clients', 'العملاء') }}</router-link>
            <span class="sep">/</span>
            <span class="active">{{ client.client?.brand_name || client.name }}</span>
          </div>
          <div class="title-with-avatar">
            <UserAvatar :user="client" size="xl" />
            <div>
              <h1 class="page-title">{{ client.client?.brand_name || client.name }}</h1>
              <p class="page-subtitle">
                {{ client.client?.company_name }} • {{ client.client?.country }}
              </p>
            </div>
          </div>
        </div>
        <div class="header-actions">
          <BaseButton variant="white" @click="$router.push('/messages')">{{
            t('Send Message', 'إرسال رسالة')
          }}</BaseButton>
        </div>
      </div>

      <!-- Overview Stats -->
      <div class="stats-row mb-3">
        <BaseCard class="stat-box">
          <span class="stat-label">{{ t('Lifetime Orders', 'إجمالي الطلبات') }}</span>
          <span class="stat-value">{{ client.total_orders }}</span>
        </BaseCard>
        <BaseCard class="stat-box">
          <span class="stat-label">{{ t('In Production', 'قيد التصنيع') }}</span>
          <span class="stat-value text-red">{{ client.production_orders }}</span>
        </BaseCard>
        <BaseCard class="stat-box">
          <span class="stat-label">{{ t('Completed', 'مكتمل') }}</span>
          <span class="stat-value text-green">{{ client.completed_orders }}</span>
        </BaseCard>
        <BaseCard class="stat-box">
          <span class="stat-label">{{ t('Member Since', 'عضو منذ') }}</span>
          <span class="stat-value small-text">{{ formatDate(client.created_at) }}</span>
        </BaseCard>
      </div>

      <div class="detail-grid">
        <!-- Left Column: Company Details -->
        <div class="detail-main">
          <BaseCard class="info-section mb-2" :title="t('Company Profile', 'ملف الشركة')">
            <div class="profile-grid">
              <div class="info-item">
                <span class="label">{{ t('Contact Person', 'شخص التواصل') }}</span>
                <span class="value">{{ client.name }}</span>
              </div>
              <div class="info-item">
                <span class="label">{{ t('Email Address', 'البريد الإلكتروني') }}</span>
                <span class="value">{{ client.email }}</span>
              </div>
              <div class="info-item">
                <span class="label">{{ t('Phone Number', 'رقم الهاتف') }}</span>
                <span class="value">{{ client.client?.phone || '---' }}</span>
              </div>
              <div class="info-item">
                <span class="label">{{ t('City / Country', 'المدينة / الدولة') }}</span>
                <span class="value">{{ client.client?.city }}, {{ client.client?.country }}</span>
              </div>
              <div class="info-item">
                <span class="label">{{ t('Business Type', 'نوع النشاط') }}</span>
                <span class="value">{{ client.client?.business_type || '---' }}</span>
              </div>
              <div class="info-item">
                <span class="label">{{ t('Instagram', 'إنستغرام') }}</span>
                <span class="value text-blue">{{ client.client?.instagram || '---' }}</span>
              </div>
            </div>
          </BaseCard>

          <BaseCard class="info-section" :title="t('Recent Activity', 'النشاط الأخير')">
            <div class="table-responsive hidden md:block">
              <table class="modern-table">
                <thead>
                  <tr>
                    <th>{{ t('Order', 'الطلب') }}</th>
                    <th>{{ t('Product', 'المنتج') }}</th>
                    <th>{{ t('Status', 'الحالة') }}</th>
                    <th>{{ t('Date', 'التاريخ') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="order in client.created_orders"
                    :key="order.id"
                    class="clickable-row"
                    @click="$router.push('/orders/' + order.id)"
                  >
                    <td>
                      <span class="code">#{{ order.order_code }}</span>
                    </td>
                    <td>
                      <span class="title">{{ order.title }}</span>
                    </td>
                    <td>
                      <span class="status-pill" :class="order.status">{{ order.status }}</span>
                    </td>
                    <td>{{ formatDate(order.created_at) }}</td>
                  </tr>
                  <tr v-if="!client.created_orders?.length">
                    <td colspan="4" class="empty-text py-6 text-center text-slate-400">
                      {{ t('No recent orders.', 'لا يوجد طلبات حديثة.') }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Mobile Recent Activity -->
            <div class="mt-4 flex flex-col gap-3 md:hidden">
              <div
                v-for="order in client.created_orders"
                :key="'mob-' + order.id"
                class="flex flex-col gap-2 rounded-2xl border border-slate-100 bg-slate-50 p-4 shadow-sm"
                @click="$router.push('/orders/' + order.id)"
              >
                <div class="flex items-start justify-between">
                  <div class="flex flex-col gap-1">
                    <span class="font-mono text-sm font-bold text-sky-500"
                      >#{{ order.order_code }}</span
                    >
                    <span class="font-bold text-slate-800">{{ order.title }}</span>
                  </div>
                  <span class="status-pill shrink-0 text-[0.65rem]" :class="order.status">{{
                    order.status
                  }}</span>
                </div>
                <div class="mt-1 flex justify-end border-t border-slate-200 pt-2">
                  <span class="text-xs font-bold text-slate-500">{{
                    formatDate(order.created_at)
                  }}</span>
                </div>
              </div>
              <div
                v-if="!client.created_orders?.length"
                class="py-6 text-center text-sm font-bold text-slate-400"
              >
                {{ t('No recent orders.', 'لا يوجد طلبات حديثة.') }}
              </div>
            </div>
          </BaseCard>
        </div>

        <!-- Right Column: Business Insights -->
        <aside class="detail-sidebar">
          <BaseCard class="insight-card" :title="t('Business Insights', 'رؤى الأعمال')">
            <div class="insight-item">
              <span class="i-label">{{ t('Avg. Units per Order', 'متوسط القطع لكل طلب') }}</span>
              <span class="i-val"
                >~{{ Math.round(client.total_units / (client.total_orders || 1)) || 0 }}</span
              >
            </div>
            <div class="insight-item">
              <span class="i-label">{{ t('Preferred Category', 'التصنيف المفضل') }}</span>
              <span class="i-val">{{ client.client?.preferred_category || '---' }}</span>
            </div>
            <div class="insight-item">
              <span class="i-label">{{ t('Expected Quantity', 'الكمية المتوقعة') }}</span>
              <span class="i-val">{{ client.client?.expected_quantity || '---' }}</span>
            </div>
          </BaseCard>

          <BaseCard class="help-box mt-2">
            <h4>{{ t('Internal Notes', 'ملاحظات داخلية') }}</h4>
            <p>{{ t('Only visible to factory team members.', 'مرئية فقط لأعضاء فريق المصنع.') }}</p>
            <textarea
              class="internal-notes-area"
              placeholder="Add note about this client..."
            ></textarea>
            <BaseButton variant="white" block size="sm" class="mt-1">{{
              t('Save Note', 'حفظ الملاحظة')
            }}</BaseButton>
          </BaseCard>
        </aside>
      </div>
    </div>
  </Layout>
</template>

<script setup>
  import { useLang } from '../../composables/useLang';
  import Layout from '../../components/Layout.vue';
  import BaseCard from '../../components/UI/BaseCard.vue';
  import BaseButton from '../../components/UI/BaseButton.vue';
  import UserAvatar from '../../components/UI/UserAvatar.vue';
  import { ref, onMounted } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import axios from 'axios';
  import { hasPermission } from '../../utils/permissions';

  const route = useRoute();
  const router = useRouter();
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
  const can = (perm) => hasPermission(user.value, perm);

  const client = ref(null);

  const { isRtl, t } = useLang();

  onMounted(() => {
    if (!can('clients.view')) return router.push('/dashboard');
    fetchClient();
  });

  async function fetchClient() {
    try {
      const res = await axios.get(`/api/clients/${route.params.id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` },
      });
      client.value = res.data;
    } catch (e) {
      console.warn(e);
    }
  }

  const formatDate = (date) => {
    if (!date) return '---';
    return new Date(date).toLocaleDateString(isRtl.value ? 'ar-EG' : 'en-US', {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
    });
  };
</script>

<style scoped>
  .client-detail-view {
    max-width: 1200px;
    margin: 0 auto;
    padding-bottom: 5rem;
  }

  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2.5rem;
  }
  .breadcrumb-mini {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    font-weight: 700;
    color: #94a3b8;
    margin-bottom: 1rem;
  }
  .breadcrumb-mini a {
    color: inherit;
    text-decoration: none;
  }
  .breadcrumb-mini .sep {
    opacity: 0.5;
  }
  .breadcrumb-mini .active {
    color: #0ea5e9;
  }

  .title-with-avatar {
    display: flex;
    align-items: center;
    gap: 1.5rem;
  }
  .client-large-avatar {
    width: 72px;
    height: 72px;
    border-radius: 20px;
    background: #1a3a5f;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 2rem;
  }
  .page-title {
    font-size: 2.25rem;
    font-weight: 800;
    color: #1a3a5f;
    margin: 0;
    letter-spacing: -0.02em;
  }
  .page-subtitle {
    color: #64748b;
    font-size: 1rem;
    margin-top: 4px;
    font-weight: 600;
  }

  .stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
  }
  .stat-box {
    padding: 1.5rem !important;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .stat-label {
    font-size: 0.65rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #1a3a5f;
  }
  .stat-value.text-red {
    color: #ef4444;
  }
  .stat-value.text-green {
    color: #10b981;
  }
  .stat-value.small-text {
    font-size: 1rem;
  }

  .detail-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 2rem;
    align-items: flex-start;
  }
  @media (max-width: 1024px) {
    .detail-grid {
      grid-template-columns: 1fr;
    }
  }

  .profile-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    padding: 1rem 0;
  }
  .info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .info-item .label {
    font-size: 0.7rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
  }
  .info-item .value {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
  }

  .modern-table {
    width: 100%;
    border-collapse: collapse;
  }
  .modern-table th {
    text-align: left;
    padding: 1rem;
    border-bottom: 2px solid #f1f5f9;
    font-size: 0.7rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
  }
  .modern-table td {
    padding: 1rem;
    border-bottom: 1px solid #f8fafc;
    font-size: 0.875rem;
    font-weight: 600;
  }

  .code {
    color: #0ea5e9;
    font-family: monospace;
    font-size: 0.9375rem;
    font-weight: 800;
  }
  .status-pill {
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
  }
  .status-pill.sampling,
  .status-pill.production {
    background: #fef2f2;
    color: #ef4444;
  }
  .status-pill.completed {
    background: #f0fdf4;
    color: #10b981;
  }

  .insight-card {
    padding: 1.25rem !important;
  }
  .insight-item {
    padding: 1rem 0;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .insight-item:last-child {
    border: none;
  }
  .i-label {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 600;
  }
  .i-val {
    font-size: 1rem;
    font-weight: 800;
    color: #1a3a5f;
  }

  .help-box h4 {
    font-weight: 800;
    font-size: 0.9375rem;
    margin: 0 0 0.5rem;
    color: #1a3a5f;
  }
  .help-box p {
    font-size: 0.75rem;
    color: #94a3b8;
    margin-bottom: 1rem;
  }
  .internal-notes-area {
    width: 100%;
    height: 100px;
    padding: 0.75rem;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    font-size: 0.875rem;
    resize: none;
  }

  .mb-3 {
    margin-bottom: 3rem;
  }
  .mb-2 {
    margin-bottom: 2rem;
  }
  .mt-2 {
    margin-top: 2rem;
  }
  .mt-1 {
    margin-top: 1rem;
  }
  .text-blue {
    color: #0ea5e9;
  }

  .rtl .page-header,
  .rtl .modern-table th {
    text-align: right;
  }
  .rtl .breadcrumb-mini {
    direction: rtl;
  }

  @media (max-width: 767px) {
    .page-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 1.5rem;
    }
    .header-actions {
      width: 100%;
    }
    .header-actions button {
      width: 100%;
      justify-content: center;
    }
    .stats-row {
      grid-template-columns: 1fr 1fr;
    }
    .profile-grid {
      grid-template-columns: 1fr;
    }
    .detail-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
