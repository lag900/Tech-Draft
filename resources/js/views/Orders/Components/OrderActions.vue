<template>
  <BaseCard
    v-if="canExport || canApproveSampling || isAdmin"
    :title="t('Order Actions', 'إجراءات الطلب')"
    class="admin-actions-card no-print"
  >
    <div class="admin-actions flex-col gap-0.75">
      <!-- Prominent Actions -->
      <div v-if="canExport" class="prominent-actions gap-0-75 mb-2 w-full flex-col">
        <BaseButton variant="outline" block size="sm" @click="scrollToAdminSection">
          <template #icon-left>
            <svg
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.2"
            >
              <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
            </svg>
          </template>
          {{ t('Fill Production Specs', 'تعبئة مواصفات الإنتاج') }}
        </BaseButton>
        <div class="divider mt-1"></div>
      </div>

      <!-- Status Actions based on Policy check passing via UI state -->
      <BaseButton
        v-if="order.status === 'pending' && isAdmin"
        variant="secondary"
        block
        size="sm"
        :loading="updating && statusTarget === 'in_review'"
        @click="updateStatus('in_review')"
      >
        {{ t('Move to Review', 'نقل للمراجعة') }}
      </BaseButton>
      <BaseButton
        v-if="order.status === 'in_review' && canApproveSampling"
        variant="secondary"
        block
        size="sm"
        :loading="updating && statusTarget === 'sampling'"
        @click="updateStatus('sampling')"
      >
        {{ t('Approve for Sampling', 'الموافقة على العينات') }}
      </BaseButton>
      <BaseButton
        v-if="order.status === 'sampling' && canApproveSampling"
        variant="secondary"
        block
        size="sm"
        :loading="updating && statusTarget === 'approved'"
        @click="updateStatus('approved')"
      >
        {{ t('Approve Sample', 'الموافقة على العينة') }}
      </BaseButton>
      <BaseButton
        v-if="order.status === 'approved' && isAdmin"
        variant="secondary"
        block
        size="sm"
        :loading="updating && statusTarget === 'production'"
        @click="updateStatus('production')"
      >
        {{ t('Start Production', 'بدء التصنيع') }}
      </BaseButton>
      <BaseButton
        v-if="order.status === 'production' && isAdmin"
        variant="secondary"
        block
        size="sm"
        :loading="updating && statusTarget === 'completed'"
        @click="updateStatus('completed')"
      >
        {{ t('Mark as Completed', 'تم الإكمال') }}
      </BaseButton>

      <BaseButton
        v-if="!['completed', 'cancelled'].includes(order.status) && isAdmin"
        variant="ghost"
        block
        size="sm"
        class="cancel-btn mt-1"
        :loading="updating && statusTarget === 'cancelled'"
        @click="updateStatus('cancelled')"
      >
        {{ t('Cancel Order', 'إلغاء الطلب') }}
      </BaseButton>
    </div>
  </BaseCard>
</template>

<script setup>
  import { computed } from 'vue';
  import { useRouter } from 'vue-router';
  import BaseCard from '../../../components/UI/BaseCard.vue';
  import BaseButton from '../../../components/UI/BaseButton.vue';
  import { useLang } from '../../../composables/useLang';

  const props = defineProps({
    order: { type: Object, required: true },
    updating: { type: Boolean, default: false },
    statusTarget: { type: String, default: '' },
    isAdmin: { type: Boolean, default: false },
  });

  const emit = defineEmits(['updateStatus', 'scrollToAdmin', 'viewTechPack', 'print']);
  const router = useRouter();
  const { t } = useLang();

  const canApproveSampling = computed(() => props.order?.can?.approve || false);
  const canExport = computed(() => props.order?.can?.export || false);

  const updateStatus = (status) => {
    emit('updateStatus', status);
  };

  const scrollToAdminSection = () => {
    emit('scrollToAdmin');
  };
</script>

<style scoped>
  .admin-actions-card {
    position: sticky;
    top: 1.5rem;
  }
  .flex-col {
    display: flex;
    flex-direction: column;
  }
  .gap-0\.75 {
    gap: 0.75rem;
  }
  .mt-1 {
    margin-top: 1rem;
  }
  .mt-0-75 {
    margin-top: 0.75rem;
  }
  .cancel-btn {
    color: #ef4444;
  }
  .cancel-btn:hover {
    background: #fef2f2;
    color: #b91c1c;
  }
  .divider {
    height: 1px;
    background: #e2e8f0;
    margin: 1rem 0;
  }
  .tech-pack-btn {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: white;
    border: none;
    font-weight: 600;
    box-shadow:
      0 4px 6px -1px rgba(37, 99, 235, 0.2),
      0 2px 4px -1px rgba(37, 99, 235, 0.1);
    transition: all 0.2s;
  }
  .tech-pack-btn:hover {
    transform: translateY(-2px);
    box-shadow:
      0 10px 15px -3px rgba(0, 0, 0, 0.1),
      0 4px 6px -2px rgba(0, 0, 0, 0.05);
  }

  @media (max-width: 768px) {
    .admin-actions-card {
      position: static;
      margin-bottom: 2rem;
      box-shadow: none;
      border: 1px solid #e2e8f0;
    }
    .prominent-actions .tech-pack-btn {
      padding: 1rem;
      font-size: 1.1rem;
    }
  }
</style>
