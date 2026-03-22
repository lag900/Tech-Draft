<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm print:static print:block print:bg-white print:p-0"
        @click="close"
      >
        <div
          class="flex h-[95vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl border border-white/10 bg-neutral-50 shadow-2xl print:h-auto print:max-w-none print:rounded-none print:border-none print:bg-white print:shadow-none"
          @click.stop
        >
          <!-- Toolbar -->
          <div
            class="flex shrink-0 items-center justify-between border-b border-neutral-200 bg-white px-6 py-4 print:hidden"
          >
            <div class="flex items-center gap-3">
              <span class="text-lg font-bold tracking-tight text-neutral-900">{{
                t('Tech Pack Preview', 'معاينة الملف الفني')
              }}</span>
              <span
                class="rounded-lg bg-blue-50 px-2.5 py-1 font-mono text-sm font-bold tracking-tight text-blue-700"
                >#{{ orderId }}</span
              >
            </div>
            <div class="flex items-center gap-3">
              <button
                class="flex items-center gap-2 rounded-xl border border-neutral-200 bg-neutral-50 px-5 py-2.5 text-sm font-bold text-neutral-900 transition-all hover:bg-neutral-100 focus:ring-2 focus:ring-neutral-200 focus:outline-none"
                @click="printPage"
              >
                <svg
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path
                    d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"
                  />
                  <rect x="6" y="14" width="12" height="8" />
                </svg>
                <span>{{ t('Print', 'طباعة') }}</span>
              </button>
              <button
                class="flex items-center gap-2 rounded-xl bg-neutral-900 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-neutral-800 focus:ring-2 focus:ring-neutral-900 focus:outline-none disabled:opacity-70 disabled:hover:translate-y-0"
                :disabled="downloading"
                @click="exportPDF"
              >
                <svg
                  v-if="downloading"
                  class="h-4 w-4 animate-spin text-white"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                <svg
                  v-else
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" />
                </svg>
                <span>{{
                  downloading
                    ? t('Downloading...', 'جاري التحميل...')
                    : t('Download PDF', 'تحميل PDF')
                }}</span>
              </button>
              <button
                class="ml-2 flex h-11 w-11 items-center justify-center rounded-xl bg-neutral-50 text-neutral-400 transition-colors hover:bg-red-50 hover:text-red-500 focus:ring-2 focus:ring-red-200 focus:outline-none"
                @click="close"
              >
                <svg
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.5"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M18 6 6 18M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Content Area -->
          <div
            ref="contentArea"
            class="custom-scrollbar flex-1 overflow-y-auto p-6 md:p-10 print:overflow-visible print:p-0"
          >
            <div
              v-if="loading"
              class="flex h-[400px] flex-col items-center justify-center gap-5 font-semibold text-neutral-500"
            >
              <svg
                class="h-12 w-12 animate-spin rounded-full border-4 border-neutral-200 border-t-blue-600"
                viewBox="0 0 24 24"
              ></svg>
              <p>{{ t('Preparing Tech Pack...', 'جاري تحضير الملف الفني...') }}</p>
            </div>

            <TechPackLayout
              v-else-if="unifiedData"
              id="tech-pack-print-target"
              :order-data="unifiedData"
            />

            <div v-else class="mx-auto max-w-md px-5 py-16 text-center">
              <div
                class="mb-5 inline-flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-500"
              >
                <svg
                  width="32"
                  height="32"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <circle cx="12" cy="12" r="10" />
                  <line x1="12" y1="8" x2="12" y2="16" />
                  <line x1="12" y1="20" x2="12.01" y2="20" />
                </svg>
              </div>
              <h3 class="mb-2 text-xl font-bold text-neutral-900">
                {{ t('Data Retrieval Failed', 'فشل في استرداد البيانات') }}
              </h3>
              <p class="mb-6 text-neutral-500">
                {{
                  t(
                    'Could not load tech pack information for this order.',
                    'تعذر تحميل معلومات الملف الفني لهذا الطلب.'
                  )
                }}
              </p>
              <button
                class="rounded-xl border border-neutral-200 bg-white px-6 py-2.5 font-bold text-neutral-900 shadow-sm transition-colors hover:bg-neutral-50"
                @click="fetchData"
              >
                {{ t('Retry', 'إعادة المحاولة') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
  import { ref, watch } from 'vue';
  import axios from 'axios';
  import html2pdf from 'html2pdf.js';
  import TechPackLayout from './TechPackLayout.vue';
  import { useLang } from '../../composables/useLang';

  const { t } = useLang();

  const props = defineProps({
    show: Boolean,
    orderId: [Number, String],
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
      const { data } = await axios.get(`/api/orders/${props.orderId}/tech-pack`, {
        headers: authHeaders(),
      });
      unifiedData.value = data;
    } catch (err) {
      console.error('Tech Pack Fetch Error:', err);
      unifiedData.value = null;
    } finally {
      loading.value = false;
    }
  };

  watch(
    () => props.show,
    (newVal) => {
      if (newVal) {
        fetchData();
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    }
  );

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
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait', compress: true },
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
  .custom-scrollbar::-webkit-scrollbar {
    width: 6px;
  }
  .custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
  }
  .custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #d4d4d8;
    border-radius: 9999px;
  }
</style>
