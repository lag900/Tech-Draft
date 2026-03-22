<template>
  <div class="tech-pack-viewer">
    <!-- TOOLBAR (Hidden during print) -->
    <div class="viewer-toolbar no-print">
      <div class="toolbar-content">
        <div class="toolbar-left">
          <button class="btn-back" @click="$router.push(`/orders/${route.params.id}`)">
            <svg
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
            >
              <path d="M19 12H5M12 19l-7-7 7-7" />
            </svg>
            Back to Order
          </button>
          <div class="order-slug">
            <span class="badge">READ ONLY</span>
            <h1>Technical Package: #{{ unified?.product?.order_code }}</h1>
          </div>
        </div>
        <div class="toolbar-right">
          <button class="btn btn-primary" :disabled="downloading" @click="exportPDF">
            <span v-if="downloading" class="loader-spinner"></span>
            <svg
              v-else
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" />
            </svg>
            Export PDF
          </button>
        </div>
      </div>
    </div>

    <!-- MAIN CONTENT AREA -->
    <div class="viewer-content">
      <div v-if="loading" class="flex min-h-[50vh] items-center justify-center">
        <div class="loader-spinner !h-8 !w-8 border-4 !border-[#4F46E5]"></div>
      </div>

      <TechPackLayout
        v-else-if="unified.product.id"
        id="tech-pack-content"
        :order-data="unified"
        :class="{ 'is-exporting': downloading }"
      />

      <div v-else class="empty-state py-20 text-center">
        <h2 class="text-xl font-bold text-gray-400">Could not retrieve Tech Pack data.</h2>
        <p class="mt-2 text-gray-400">
          Please ensure the order exists and you have permission to view it.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, onMounted } from 'vue';
  import { useRoute } from 'vue-router';
  import axios from 'axios';
  import html2pdf from 'html2pdf.js';
  import TechPackLayout from '../../components/Orders/TechPackLayout.vue';

  const route = useRoute();

  const authHeaders = () => ({ Authorization: `Bearer ${localStorage.getItem('auth_token')}` });

  const loading = ref(true);
  const downloading = ref(false);
  const unified = ref({
    product: {},
    materials: { fabric: {}, trims: {}, bom: [] },
    measurements: { sizes: [], points: [] },
    construction: {},
    production: { operations: [], anatomy: [], marker: {} },
    packaging: { labels: {} },
  });

  const fetchData = async () => {
    try {
      const { data } = await axios.get(`/api/orders/${route.params.id}/tech-pack`, {
        headers: authHeaders(),
      });
      unified.value = data;
    } catch {
      // error state handled by empty-state template
    } finally {
      loading.value = false;
    }
  };

  onMounted(fetchData);

  const exportPDF = async () => {
    downloading.value = true;
    try {
      // Wait for Vue to apply the 'is-exporting' class which resets paddings
      await new Promise((r) => setTimeout(r, 100));

      const el = document.getElementById('tech-pack-content');
      await html2pdf()
        .set({
          margin: 0,
          filename: `TechPack-Order-${unified.value.product.order_code || 'export'}.pdf`,
          image: { type: 'jpeg', quality: 1.0 },
          pagebreak: { mode: 'css' },
          html2canvas: {
            scale: 2,
            useCORS: true,
            logging: false,
            scrollY: 0,
            backgroundColor: '#ffffff',
          },
          jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
        })
        .from(el)
        .save();
    } catch {
      // PDF export failure is silent — user can retry
    } finally {
      downloading.value = false;
    }
  };
</script>

<style scoped>
  .tech-pack-viewer {
    background: #f1f5f9;
    min-height: 100vh;
    padding-bottom: 50px;
  }

  .viewer-toolbar {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 24px;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  }

  .toolbar-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .toolbar-left {
    display: flex;
    align-items: center;
    gap: 24px;
  }

  .btn-back {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 6px;
    color: #64748b;
    font-weight: 700;
    font-size: 13px;
    transition: all 0.2s;
    background: transparent;
    border: none;
    cursor: pointer;
  }
  .btn-back:hover {
    background: #f8fafc;
    color: #1e293b;
  }

  .order-slug h1 {
    font-size: 16px;
    font-weight: 800;
    color: #1e293b;
    margin: 0;
  }

  .badge {
    font-size: 10px;
    font-weight: 900;
    background: #f1f5f9;
    color: #64748b;
    padding: 2px 6px;
    border-radius: 4px;
    text-transform: uppercase;
    margin-bottom: 4px;
    display: inline-block;
  }

  .toolbar-right {
    display: flex;
    gap: 12px;
  }

  .btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
  }

  .btn-outline {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #1e293b;
  }
  .btn-outline:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
  }

  .btn-primary {
    background: #4f46e5;
    border: 1px solid #4f46e5;
    color: #ffffff;
  }
  .btn-primary:hover {
    background: #4338ca;
  }

  .btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  .viewer-content {
    padding: 40px 0;
    display: flex;
    justify-content: center;
  }

  /* Print Overrides */
  @media print {
    .no-print {
      display: none !important;
    }
    .tech-pack-viewer {
      background: white;
      padding: 0;
    }
    .viewer-content {
      padding: 0;
      display: block;
    }

    /* Hide potential sidebars/navs from parent layouts if they exist */
    :deep(.saas-sidebar),
    :deep(.saas-nav) {
      display: none !important;
    }
  }

  /* Mobile Settings */
  @media (max-width: 768px) {
    .toolbar-content {
      flex-direction: column;
      align-items: flex-start;
      gap: 16px;
    }

    .toolbar-left,
    .toolbar-right {
      width: 100%;
      justify-content: space-between;
    }

    .btn-primary {
      width: 100%;
      justify-content: center;
    }

    .viewer-content {
      padding: 16px 0;
    }

    /* Reset scaling so standard view fits screen on mobile */
    :deep(.tp-page) {
      width: 100%;
      padding: 10px;
      border-radius: 0;
      min-height: auto;
    }
  }
</style>
