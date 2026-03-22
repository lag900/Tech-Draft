<template>
  <Layout>
    <div class="production-queue-view" :class="{ rtl: isRtl }">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('Production Queue', 'طابور الإنتاج') }}</h1>
          <p class="page-subtitle">
            {{
              t(
                'Manage manufacturing priority and live stages',
                'إدارة أولويات التصنيع ومراحل الإنتاج الحالية'
              )
            }}
          </p>
        </div>
        <div class="header-stats">
          <div class="header-stat-item">
            <span class="h-label">{{ t('Total in Queue', 'إجمالي في الطابور') }}</span>
            <span class="h-value">{{ queue.length }}</span>
          </div>
        </div>
      </div>

      <div class="queue-container">
        <!-- Main Queue Table -->
        <BaseCard no-padding class="queue-card">
          <div class="table-responsive">
            <table class="modern-table">
              <thead>
                <tr>
                  <th>{{ t('Pos.', 'المركز') }}</th>
                  <th>{{ t('Order ID', 'رقم الطلب') }}</th>
                  <th>{{ t('Client / Brand', 'العميل / العلامة') }}</th>
                  <th>{{ t('Product', 'المنتج') }}</th>
                  <th>{{ t('Quantity', 'الكمية') }}</th>
                  <th>{{ t('Stage', 'المرحلة') }}</th>
                  <th class="text-right">{{ t('Priority', 'الأولوية') }}</th>
                </tr>
              </thead>
              <tbody>
                <TransitionGroup name="list">
                  <tr
                    v-for="(order, index) in queue"
                    :key="order.id"
                    class="queue-row"
                    :class="{ 'high-priority': order.priority > 0 }"
                  >
                    <td>
                      <div class="pos-badge">#{{ index + 1 }}</div>
                    </td>
                    <td>
                      <span class="code-badge">#{{ order.order_code }}</span>
                    </td>
                    <td>
                      <span class="brand-text">{{
                        order.client?.brand_name || t('Individual', 'عميل مستقل')
                      }}</span>
                    </td>
                    <td>
                      <div class="p-info">
                        <span class="p-title">{{ order.title }}</span>
                        <span class="p-cat">{{ order.category?.name }}</span>
                      </div>
                    </td>
                    <td>
                      <span class="qty-pill"
                        >{{ getTotalQty(order) }} {{ t('Units', 'قطعة') }}</span
                      >
                    </td>
                    <td>
                      <div class="stage-badge" :class="order.production_stage">
                        <span class="stage-dot"></span>
                        {{
                          t(
                            stageMap[order.production_stage]?.en,
                            stageMap[order.production_stage]?.ar
                          )
                        }}
                      </div>
                    </td>
                    <td class="text-right">
                      <div class="priority-actions">
                        <button
                          class="prio-toggle"
                          :title="t('High Priority', 'أولوية عالية')"
                          :class="{ active: order.production_priority > 0 }"
                          @click="togglePriority(order)"
                        >
                          <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                          >
                            <path
                              d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                            />
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </TransitionGroup>

                <tr v-if="loading">
                  <td colspan="7" class="loading-state">
                    <div class="spinner"></div>
                    <p>{{ t('Loading Queue...', 'جاري تحميل الطابور...') }}</p>
                  </td>
                </tr>

                <tr v-if="!loading && queue.length === 0">
                  <td colspan="7" class="empty-state">
                    <div class="empty-v">
                      <svg
                        width="64"
                        height="64"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#e2e8f0"
                        stroke-width="1.5"
                      >
                        <path
                          d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"
                        />
                        <polyline points="14 2 14 8 20 8" />
                      </svg>
                      <p>{{ t('Production queue is empty.', 'طابور الإنتاج فارغ حالياً.') }}</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </BaseCard>
      </div>
    </div>

    <!-- Custom Alert Modal -->
    <AlertModal
      :show="alertState.show"
      :type="alertState.type"
      :title="alertState.title"
      :message="alertState.message"
      :is-rtl="isRtl"
      @close="alertState.show = false"
    />
  </Layout>
</template>

<script setup>
  import Layout from '../../components/Layout.vue';
  import BaseCard from '../../components/UI/BaseCard.vue';
  import AlertModal from '../../components/UI/AlertModal.vue';
  import { ref, reactive, onMounted } from 'vue';
  import axios from 'axios';
  import { useLang } from '../../composables/useLang';

  const queue = ref([]);
  const loading = ref(true);
  const { isRtl, t } = useLang();

  const alertState = reactive({
    show: false,
    type: 'info',
    title: '',
    message: '',
  });

  const showAlert = (message, type = 'info', title = '') => {
    alertState.message = message;
    alertState.type = type;
    alertState.title =
      title ||
      (type === 'success'
        ? t('Success', 'نجاح')
        : type === 'error'
          ? t('Error', 'خطأ')
          : t('Notification', 'تنبيه'));
    alertState.show = true;
  };

  const stageMap = {
    cutting: { en: 'Cutting', ar: 'قص' },
    sewing: { en: 'Sewing', ar: 'خياطة' },
    finishing: { en: 'Finishing', ar: 'تشطيب' },
    packing: { en: 'Packing', ar: 'تعبئة' },
  };

  const fetchQueue = async () => {
    loading.value = true;
    try {
      const res = await axios.get('/api/production/queue', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` },
      });
      queue.value = res.data;
    } catch (e) {
      console.error(e);
    } finally {
      loading.value = false;
    }
  };

  const getTotalQty = (order) => {
    if (!order.measurements || !order.measurements.breakdown) return 0;
    return order.measurements.breakdown.reduce(
      (sum, item) => sum + (parseInt(item.quantity) || 0),
      0
    );
  };

  const togglePriority = async (order) => {
    const newPriority = order.production_priority > 0 ? 0 : 1;
    try {
      // For now we use the PATCH update status endpoint or create a new one.
      // Let's assume OrderController update can handle this.
      await axios.patch(
        `/api/orders/${order.id}`,
        { production_priority: newPriority },
        {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` },
        }
      );
      order.production_priority = newPriority;
      // Re-sort locally
      queue.value.sort(
        (a, b) =>
          b.production_priority - a.production_priority || a.queue_position - b.queue_position
      );
    } catch (e) {
      showAlert(t('Failed to update priority', 'فشل تحديث الأولوية'), 'error');
    }
  };

  onMounted(fetchQueue);
</script>

<style scoped>
  .production-queue-view {
    padding-bottom: 4rem;
  }
  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
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

  .header-stats {
    display: flex;
    gap: 2rem;
  }
  .header-stat-item {
    text-align: right;
  }
  .rtl .header-stat-item {
    text-align: left;
  }
  .h-label {
    display: block;
    font-size: 0.7rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
  }
  .h-value {
    font-size: 1.5rem;
    font-weight: 900;
    color: #0ea5e9;
  }

  .queue-card {
    border: 1px solid #eef2f6;
    overflow: hidden;
  }

  .modern-table {
    width: 100%;
    border-collapse: collapse;
  }
  .modern-table th {
    text-align: left;
    padding: 1.25rem 1.5rem;
    background: #f8fafc;
    font-size: 0.75rem;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid #e5e7eb;
  }
  .rtl .modern-table th {
    text-align: right;
  }
  .modern-table td {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }

  .queue-row {
    transition: all 0.3s ease;
  }
  .queue-row:hover {
    background: #fbfdff;
  }
  .queue-row.high-priority {
    background: #fffcf0;
  }

  .pos-badge {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    color: #475569;
    border-radius: 8px;
    font-weight: 900;
    font-size: 0.8125rem;
  }
  .high-priority .pos-badge {
    background: #f59e0b;
    color: white;
  }

  .brand-text {
    font-weight: 700;
    color: #1e293b;
    font-size: 0.875rem;
  }
  .p-info {
    display: flex;
    flex-direction: column;
  }
  .p-title {
    font-weight: 800;
    color: #1a3a5f;
    font-size: 0.9375rem;
  }
  .p-cat {
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 700;
  }

  .qty-pill {
    padding: 4px 10px;
    background: #f0f9ff;
    color: #0ea5e9;
    border-radius: 99px;
    font-weight: 800;
    font-size: 0.75rem;
    border: 1px solid #bae6fd;
  }

  .stage-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    border-radius: 99px;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
  }
  .stage-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
  }

  .stage-badge.cutting {
    background: #dbeafe;
    color: #1d4ed8;
  }
  .stage-badge.sewing {
    background: #ede9fe;
    color: #6d28d9;
  }
  .stage-badge.finishing {
    background: #fef3c7;
    color: #b45309;
  }
  .stage-badge.packing {
    background: #dcfce7;
    color: #15803d;
  }

  .prio-toggle {
    background: transparent;
    border: none;
    color: #cbd5e1;
    cursor: pointer;
    transition: all 0.2s;
    padding: 4px;
  }
  .prio-toggle:hover {
    color: #f59e0b;
    transform: scale(1.2);
  }
  .prio-toggle.active {
    color: #f59e0b;
    filter: drop-shadow(0 0 4px rgba(245, 158, 11, 0.4));
  }

  .code-badge {
    font-family: monospace;
    padding: 4px 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-weight: 800;
    color: #475569;
    font-size: 0.75rem;
  }

  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #94a3b8;
  }
  .loading-state {
    text-align: center;
    padding: 3rem;
    color: #64748b;
  }
  .spinner {
    width: 32px;
    height: 32px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #0ea5e9;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 1rem;
  }
  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }

  /* List Transitions */
  .list-enter-active,
  .list-leave-active {
    transition: all 0.5s ease;
  }
  .list-enter-from {
    opacity: 0;
    transform: translateX(-30px);
  }
  .list-leave-to {
    opacity: 0;
    transform: translateX(30px);
  }
</style>
