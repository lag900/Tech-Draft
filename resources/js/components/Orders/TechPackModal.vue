<template>
  <Teleport to="body">
    <Transition name="fade-pop">
      <div v-if="show" class="tp-modal-overlay no-print" @click="close">
        <div class="tp-modal-container" @click.stop>
          <!-- Toolbar -->
          <div class="tp-modal-toolbar">
            <div class="toolbar-left">
              <span class="toolbar-title">{{ t('Tech Pack Preview', 'معاينة الملف الفني') }}</span>
              <span class="toolbar-order-id">#{{ orderId }}</span>
            </div>
            <div class="toolbar-actions">
              <button class="btn-tp-action secondary" @click="printPage">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                <span>{{ t('Print', 'طباعة') }}</span>
              </button>
              <button class="btn-tp-action primary" @click="exportPDF" :disabled="downloading">
                <span v-if="downloading" class="mini-spinner"></span>
                <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                <span>{{ downloading ? t('Downloading...', 'جاري التحميل...') : t('Download PDF', 'تحميل PDF') }}</span>
              </button>
              <button class="btn-close-tp" @click="close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
              </button>
            </div>
          </div>

          <!-- Content Area -->
          <div class="tp-modal-content" ref="contentArea">
            <div v-if="loading" class="tp-loading-state">
              <div class="premium-loader"></div>
              <p>{{ t('Preparing Tech Pack...', 'جاري تحضير الملف الفني...') }}</p>
            </div>
            
            <TechPackLayout v-else-if="unifiedData" :orderData="unifiedData" id="tech-pack-print-target" />
            
            <div v-else class="tp-error-state">
              <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <h3>{{ t('Data Retrieval Failed', 'فشل في استرداد البيانات') }}</h3>
              <p>{{ t('Could not load tech pack information for this order.', 'تعذر تحميل معلومات الملف الفني لهذا الطلب.') }}</p>
              <button class="btn-retry" @click="fetchData">{{ t('Retry', 'إعادة المحاولة') }}</button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import html2pdf from 'html2pdf.js';
import TechPackLayout from './TechPackLayout.vue';
import { useLang } from '../../composables/useLang';

const { t } = useLang();

const props = defineProps({
  show: Boolean,
  orderId: [Number, String]
});

const emit = defineEmits(['close']);

const loading = ref(true);
const downloading = ref(false);
const unifiedData = ref(null);
const contentArea = ref(null);

const authHeaders = () => ({ Authorization: `Bearer ${localStorage.getItem('auth_token')}` });

const fetchData = async () => {
  if (!props.orderId) return;
  loading.value = true;
  try {
    const { data } = await axios.get(`/api/orders/${props.orderId}/tech-pack`, { headers: authHeaders() });
    unifiedData.value = data;
  } catch (err) {
    console.error('Tech Pack Fetch Error:', err);
    unifiedData.value = null;
  } finally {
    loading.value = false;
  }
};

watch(() => props.show, (newVal) => {
  if (newVal) {
    fetchData();
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
});

const close = () => emit('close');

const printPage = () => {
  window.print();
};

const exportPDF = async () => {
  if (downloading.value) return;
  downloading.value = true;
  try {
    const el = document.getElementById('tech-pack-print-target');
    const options = {
      margin: 0,
      filename: `TechPack-ORD-${props.orderId}.pdf`,
      image: { type: 'jpeg', quality: 1.0 },
      html2canvas: { scale: 2, useCORS: true, logging: false, letterRendering: true },
      jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait', compress: true }
    };
    await html2pdf().set(options).from(el).save();
  } catch (err) {
    console.error('PDF Export Error:', err);
  } finally {
    downloading.value = false;
  }
};
</script>

<style scoped>
.tp-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.85);
  backdrop-filter: blur(8px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.tp-modal-container {
  background: #f1f5f9;
  width: 100%;
  max-width: 1100px;
  height: 95vh;
  border-radius: 24px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.tp-modal-toolbar {
  background: white;
  padding: 16px 24px;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-shrink: 0;
}

.toolbar-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.toolbar-title {
  font-size: 1.125rem;
  font-weight: 800;
  color: #0f172a;
}

.toolbar-order-id {
  background: #eff6ff;
  color: #2563eb;
  padding: 4px 10px;
  border-radius: 8px;
  font-family: 'JetBrains Mono', monospace;
  font-size: 0.875rem;
  font-weight: 700;
}

.toolbar-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn-tp-action {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  border: none;
}

.btn-tp-action.primary {
  background: #2563eb;
  color: white;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.btn-tp-action.primary:hover:not(:disabled) {
  background: #1d4ed8;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
}

.btn-tp-action.secondary {
  background: #f8fafc;
  color: #1e293b;
  border: 1.5px solid #e2e8f0;
}

.btn-tp-action.secondary:hover {
  background: #f1f5f9;
  border-color: #cbd5e1;
}

.btn-close-tp {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  background: #f8fafc;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
  margin-left: 8px;
}

.btn-close-tp:hover {
  background: #fee2e2;
  color: #ef4444;
}

.tp-modal-content {
  flex: 1;
  overflow-y: auto;
  padding: 40px 20px;
  scrollbar-width: thin;
}

/* Loading state */
.tp-loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 400px;
  gap: 20px;
  color: #64748b;
  font-weight: 600;
}

.premium-loader {
  width: 48px;
  height: 48px;
  border: 4px solid #e2e8f0;
  border-top-color: #2563eb;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Error state */
.tp-error-state {
  text-align: center;
  padding: 60px 20px;
}

.tp-error-state h3 {
  font-size: 1.25rem;
  font-weight: 800;
  color: #0f172a;
  margin: 20px 0 10px;
}

.tp-error-state p {
  color: #64748b;
  margin-bottom: 24px;
}

.btn-retry {
  padding: 10px 24px;
  background: white;
  border: 1.5px solid #e2e8f0;
  color: #0f172a;
  font-weight: 700;
  border-radius: 12px;
  cursor: pointer;
}

/* Transitions */
.fade-pop-enter-active, .fade-pop-leave-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.fade-pop-enter-from, .fade-pop-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

/* Print Overrides */
@media print {
  .no-print { display: none !important; }
  .tp-modal-overlay { position: static; background: white; padding: 0; visibility: visible; }
  .tp-modal-container { height: auto; max-width: none; box-shadow: none; border: none; background: white; border-radius: 0; }
  .tp-modal-content { padding: 0; overflow: visible; }
}

.mini-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}
</style>
