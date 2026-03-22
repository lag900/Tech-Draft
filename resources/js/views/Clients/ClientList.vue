<template>
  <Layout>
    <div class="client-management-view" :class="{ rtl: isRtl }">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('Clients & Brands', 'العملاء والعلامات التجارية') }}</h1>
          <p class="page-subtitle">
            {{
              t(
                'Manage external manufacturing partners and brand accounts',
                'إدارة شركاء التصنيع الخارجيين وحسابات العلامات التجارية'
              )
            }}
          </p>
        </div>
      </div>

      <!-- Analytics Cards -->
      <div class="analytics-grid mb-2">
        <div class="stat-card">
          <div class="stat-icon clients">
            <svg
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
              <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
          </div>
          <div class="stat-info">
            <span class="stat-label">{{ t('Total Clients', 'إجمالي العملاء') }}</span>
            <span class="stat-value">{{ stats.total_clients || 0 }}</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon production">
            <svg
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
            </svg>
          </div>
          <div class="stat-info">
            <span class="stat-label">{{ t('In Production', 'قيد الإنتاج') }}</span>
            <span class="stat-value">{{ stats.production_orders || 0 }}</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon completed">
            <svg
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <polyline points="20 6 9 17 4 12" />
            </svg>
          </div>
          <div class="stat-info">
            <span class="stat-label">{{ t('Total Orders', 'إجمالي الطلبات') }}</span>
            <span class="stat-value">{{ stats.total_orders || 0 }}</span>
          </div>
        </div>
      </div>

      <BaseCard class="mb-2" no-padding>
        <div class="filter-bar">
          <div class="search-box">
            <svg
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="#94a3b8"
              stroke-width="2"
            >
              <circle cx="11" cy="11" r="8" />
              <path d="m21 21-4.35-4.35" />
            </svg>
            <input
              v-model="search"
              type="text"
              :placeholder="t('Search name, company, email...', 'بحث بالاسم، الشركة، البريد...')"
            />
          </div>
        </div>
      </BaseCard>

      <BaseCard no-padding class="table-card hidden md:block">
        <div class="table-responsive">
          <table class="modern-table">
            <thead>
              <tr>
                <th>{{ t('Client / Brand', 'العميل / العلامة') }}</th>
                <th>{{ t('Contact Info', 'معلومات التواصل') }}</th>
                <th>{{ t('Location', 'الموقع') }}</th>
                <th>{{ t('Order Stats', 'إحصائيات الطلبات') }}</th>
                <th class="text-right">{{ t('Actions', 'الإجراءات') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="client in filteredClients"
                :key="client.id"
                class="clickable-row"
                @click="goToDetail(client.id)"
              >
                <td>
                  <div class="client-info-cell">
                    <UserAvatar :user="client" size="md" />
                    <div class="client-text">
                      <span class="c-brand">{{ client.client?.brand_name || 'Individual' }}</span>
                      <span class="c-company">{{ client.client?.company_name || '---' }}</span>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="contact-info">
                    <span class="c-email">{{ client.email }}</span>
                    <span class="c-phone">{{ client.client?.phone || '---' }}</span>
                  </div>
                </td>
                <td>
                  <span class="location-text">{{ client.client?.country || '---' }}</span>
                </td>
                <td>
                  <div class="order-stats-cell">
                    <div class="stat-mini">
                      <span class="mini-label">{{ t('Total', 'الكل') }}</span>
                      <span class="mini-val">{{ client.total_orders }}</span>
                    </div>
                    <div class="stat-mini">
                      <span class="mini-label">{{ t('Live', 'نشط') }}</span>
                      <span class="mini-val production">{{ client.production_orders }}</span>
                    </div>
                    <div class="stat-mini">
                      <span class="mini-label">{{ t('Done', 'تم') }}</span>
                      <span class="mini-val completed">{{ client.completed_orders }}</span>
                    </div>
                  </div>
                </td>
                <td class="text-right">
                  <BaseButton variant="ghost" size="sm" @click.stop="goToDetail(client.id)">
                    {{ t('View Activity', 'عرض النشاط') }}
                  </BaseButton>
                </td>
              </tr>
              <tr v-if="filteredClients.length === 0">
                <td colspan="5" class="empty-state">
                  {{ t('No clients found.', 'لم يتم العثور على عملاء.') }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </BaseCard>

      <!-- Mobile View: Card-based Layout -->
      <div class="flex flex-col gap-4 md:hidden">
        <div
          v-for="client in filteredClients"
          :key="'mob-' + client.id"
          class="mobile-client-card"
          @click="goToDetail(client.id)"
        >
          <!-- Header: Avatar + Info -->
          <div class="mcc-header">
            <UserAvatar :user="client" size="md" class="mcc-avatar" />
            <div class="mcc-title-area">
              <h3 class="mcc-brand">{{ client.client?.brand_name || 'Individual' }}</h3>
              <p class="mcc-company">{{ client.client?.company_name || '---' }}</p>
            </div>
          </div>

          <!-- Contact & Location Info -->
          <div class="mcc-contact-grid mt-4">
            <div class="mcc-info-item">
              <svg
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <rect x="2" y="4" width="20" height="16" rx="2" />
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
              </svg>
              <span>{{ client.email }}</span>
            </div>
            <div class="mcc-info-item">
              <svg
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"
                />
              </svg>
              <span>{{ client.client?.phone || '---' }}</span>
            </div>
            <div class="mcc-info-item">
              <svg
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                <circle cx="12" cy="10" r="3" />
              </svg>
              <span>{{ client.client?.country || '---' }}</span>
            </div>
          </div>

          <!-- Order Stats -->
          <div class="mcc-stats-grid mt-4 border-t border-slate-100 pt-4">
            <div class="mcc-stat-box">
              <span class="mcc-stat-lbl">{{ t('Total', 'الكل') }}</span>
              <span class="mcc-stat-val">{{ client.total_orders }}</span>
            </div>
            <div class="mcc-stat-box">
              <span class="mcc-stat-lbl">{{ t('Live', 'نشط') }}</span>
              <span class="mcc-stat-val text-red-500">{{ client.production_orders }}</span>
            </div>
            <div class="mcc-stat-box">
              <span class="mcc-stat-lbl">{{ t('Done', 'تم') }}</span>
              <span class="mcc-stat-val text-emerald-500">{{ client.completed_orders }}</span>
            </div>
          </div>

          <!-- Action -->
          <div class="mt-4 border-t border-slate-100 pt-4">
            <button class="mcc-action-btn" @click.stop="goToDetail(client.id)">
              {{ t('View Activity', 'عرض النشاط') }}
              <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="m9 18 6-6-6-6" />
              </svg>
            </button>
          </div>
        </div>

        <div
          v-if="filteredClients.length === 0"
          class="rounded-2xl border border-slate-100 bg-white py-10 text-center text-sm font-bold text-slate-500 shadow-sm"
        >
          {{ t('No clients found.', 'لم يتم العثور على عملاء.') }}
        </div>
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
  import { hasPermission } from '../../utils/permissions';
  import { ref, onMounted, computed } from 'vue';
  import { useRouter } from 'vue-router';
  import axios from 'axios';

  const router = useRouter();
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
  const can = (perm) => hasPermission(user.value, perm);

  const clients = ref([]);
  const stats = ref({});
  const search = ref('');

  const { isRtl, t } = useLang();

  onMounted(() => {
    if (!can('clients.view')) return router.push('/dashboard');
    fetchClients();
    fetchStats();
  });

  async function fetchClients() {
    try {
      const res = await axios.get('/api/clients', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` },
      });
      clients.value = res.data;
    } catch (e) {
      console.warn(e);
    }
  }

  async function fetchStats() {
    try {
      const res = await axios.get('/api/clients/stats', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` },
      });
      stats.value = res.data;
    } catch (e) {
      console.warn(e);
    }
  }

  const filteredClients = computed(() => {
    return (clients.value || []).filter((c) => {
      const brand = (c.client?.brand_name || '').toLowerCase();
      const company = (c.client?.company_name || '').toLowerCase();
      const name = (c.name || '').toLowerCase();
      const email = (c.email || '').toLowerCase();
      const s = (search.value || '').toLowerCase();
      return brand.includes(s) || company.includes(s) || name.includes(s) || email.includes(s);
    });
  });

  const goToDetail = (id) => router.push(`/clients/${id}`);
</script>

<style scoped>
  .client-management-view {
    padding-bottom: 4rem;
  }
  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 2rem;
  }
  .page-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1a3a5f;
    margin: 0;
  }
  .page-subtitle {
    color: #64748b;
    font-size: 0.9375rem;
    margin-top: 0.25rem;
  }

  .analytics-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
  }
  .stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 20px;
    border: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
  }
  .stat-icon {
    width: 54px;
    height: 54px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .stat-icon.clients {
    background: #f0f9ff;
    color: #0ea5e9;
  }
  .stat-icon.production {
    background: #fef2f2;
    color: #ef4444;
  }
  .stat-icon.completed {
    background: #f0fdf4;
    color: #10b981;
  }

  .stat-info {
    display: flex;
    flex-direction: column;
  }
  .stat-label {
    font-size: 0.75rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #1a3a5f;
    margin-top: 2px;
  }

  .filter-bar {
    padding: 1.25rem 1.5rem;
  }
  .search-box {
    position: relative;
    width: 100%;
    max-width: 500px;
  }
  .search-box svg {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
  }
  .search-box input {
    width: 100%;
    height: 44px;
    padding-left: 2.75rem;
    border: 1px solid #f1f5f9;
    background: #fafbfc;
    border-radius: 12px;
    font-size: 0.875rem;
  }
  .search-box input:focus {
    background: white;
    border-color: #0ea5e9;
    outline: none;
  }

  .modern-table {
    width: 100%;
    border-collapse: collapse;
  }
  .modern-table th {
    text-align: left;
    padding: 1.25rem 1.5rem;
    background: #fafbfc;
    font-size: 0.75rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    border-bottom: 1px solid #f1f5f9;
  }
  .modern-table td {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f8fafc;
    font-size: 0.875rem;
  }

  .clickable-row {
    cursor: pointer;
    transition: background 0.2s;
  }
  .clickable-row:hover {
    background: #fcfdfe;
  }

  .client-info-cell {
    display: flex;
    align-items: center;
    gap: 1.25rem;
  }
  .client-avatar {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #1a3a5f;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.25rem;
  }
  .client-text {
    display: flex;
    flex-direction: column;
  }
  .c-brand {
    font-weight: 800;
    color: #1a3a5f;
    font-size: 0.9375rem;
  }
  .c-company {
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 600;
  }

  .contact-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .c-email {
    font-weight: 700;
    color: #475569;
  }
  .c-phone {
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 600;
  }

  .location-text {
    font-weight: 700;
    color: #64748b;
    font-size: 0.8125rem;
  }

  .order-stats-cell {
    display: flex;
    gap: 1.5rem;
  }
  .stat-mini {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .mini-label {
    font-size: 0.65rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
  }
  .mini-val {
    font-size: 1.125rem;
    font-weight: 800;
    color: #1a3a5f;
  }
  .mini-val.production {
    color: #ef4444;
  }
  .mini-val.completed {
    color: #10b981;
  }

  .mb-2 {
    margin-bottom: 2rem;
  }
  .text-right {
    text-align: right;
  }
  .empty-state {
    padding: 4rem;
    text-align: center;
    color: #94a3b8;
    font-style: italic;
  }

  .rtl .page-header,
  .rtl .filter-bar {
    text-align: right;
  }
  .rtl .search-box svg {
    left: auto;
    right: 12px;
  }
  .rtl .search-box input {
    padding-left: 1rem;
    padding-right: 2.75rem;
  }
  .rtl .modern-table th {
    text-align: right;
  }
  .rtl .text-right {
    text-align: left;
  }

  /* Mobile Client Card Layout */
  .mobile-client-card {
    background: white;
    border-radius: 1.25rem;
    padding: 1.25rem;
    border: 1px solid #f1f5f9;
    box-shadow:
      0 4px 6px -1px rgba(0, 0, 0, 0.05),
      0 2px 4px -2px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .mobile-client-card:active {
    transform: scale(0.98);
    background-color: #f8fafc;
  }

  .mcc-header {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .mcc-title-area {
    display: flex;
    flex-direction: column;
  }

  .mcc-brand {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 800;
    color: #0f172a;
  }

  .mcc-company {
    margin: 0;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #64748b;
  }

  .mcc-contact-grid {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .mcc-info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #475569;
  }
  .mcc-info-item svg {
    color: #94a3b8;
  }

  .mcc-stats-grid {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
  }

  .mcc-stat-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    flex: 1;
    background: #f8fafc;
    padding: 0.75rem 0.5rem;
    border-radius: 0.75rem;
  }

  .mcc-stat-lbl {
    font-size: 0.65rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .mcc-stat-val {
    font-size: 1.125rem;
    font-weight: 900;
    color: #1e293b;
  }

  .mcc-action-btn {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    background: white;
    border: 1.5px solid #e2e8f0;
    color: #334155;
    padding: 0.875rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 800;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .mcc-action-btn:hover,
  .mcc-action-btn:active {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a;
  }

  .rtl .mcc-action-btn svg {
    transform: scaleX(-1);
  }

  @media (max-width: 767px) {
    .page-header {
      flex-direction: column;
      align-items: stretch;
      gap: 1rem;
      margin-bottom: 1rem;
    }
  }
</style>
