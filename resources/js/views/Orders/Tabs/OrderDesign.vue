<template>
  <div class="tab-pane animate-fade-in">
    <div class="design-visualization-grid">
      <!-- Front View -->
      <div class="visual-card">
        <div class="visual-label">{{ t('Front View', 'المنظر الأمامي') }}</div>
        <div class="visual-canvas" @click="zoomImage(order.design_front_image)">
          <img v-if="order.design_front_image" :src="order.design_front_image" alt="Front" />
          <div v-else class="empty-canvas">
            <svg
              width="40"
              height="40"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
            >
              <path
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16M14 14l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
              />
            </svg>
            <span>{{ t('No Front Image', 'لا توجد صورة أمامية') }}</span>
          </div>
        </div>
      </div>

      <!-- Back View -->
      <div class="visual-card">
        <div class="visual-label">{{ t('Back View', 'المنظر الخلفي') }}</div>
        <div class="visual-canvas" @click="zoomImage(order.design_back_image)">
          <img v-if="order.design_back_image" :src="order.design_back_image" alt="Back" />
          <div v-else class="empty-canvas">
            <svg
              width="40"
              height="40"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
            >
              <path
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16M14 14l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
              />
            </svg>
            <span>{{ t('No Back Image', 'لا توجد صورة خلفية') }}</span>
          </div>
        </div>
      </div>

      <!-- Technical Sketch -->
      <div class="visual-card technical">
        <div class="visual-label">{{ t('Technical Sketch', 'سكتش فني') }}</div>
        <div class="visual-canvas" @click="zoomImage(order.technical_sketch)">
          <img v-if="order.technical_sketch" :src="order.technical_sketch" alt="Sketch" />
          <div v-else class="empty-canvas">
            <svg
              width="40"
              height="40"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
            >
              <path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
            </svg>
            <span>{{ t('No Sketch', 'لا يوجد سكتش') }}</span>
          </div>
        </div>
      </div>

      <!-- Detail Zoom -->
      <div class="visual-card detail">
        <div class="visual-label">{{ t('Detail Zoom', 'تكبير التفاصيل') }}</div>
        <div class="visual-canvas" @click="zoomImage(order.detail_zoom)">
          <img v-if="order.detail_zoom" :src="order.detail_zoom" alt="Detail" />
          <div v-else class="empty-canvas">
            <svg
              width="40"
              height="40"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
            >
              <circle cx="11" cy="11" r="8" />
              <path d="M21 21l-4.35-4.35M11 8v6M8 11h6" />
            </svg>
            <span>{{ t('No Detail Zoom', 'لا يوجد تكبير') }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Gallery for other images -->
    <BaseCard
      v-if="order.images?.length > 0"
      :title="t('Reference Gallery', 'معرض الصور المرجعية')"
      class="mt-1"
    >
      <div class="gallery-grid-compact">
        <div
          v-for="img in order.images"
          :key="img.id"
          class="gallery-img-box-sm"
          @click="zoomImage(img.file_path)"
        >
          <img :src="img.file_path" alt="reference" />
        </div>
      </div>
    </BaseCard>

    <BaseCard :title="t('Color Palette', 'لوحة الألوان')" class="mt-1">
      <div class="colors-flex-compact">
        <div v-for="(color, idx) in order.colors" :key="idx" class="color-item-sm">
          <div class="color-circle-sm" :style="{ background: color.color_hex || '#ccc' }"></div>
          <div class="color-meta-sm">
            <div class="name">{{ color.color_name || color.color_hex }}</div>
            <div class="code">{{ color.color_code || '---' }}</div>
          </div>
        </div>
      </div>
    </BaseCard>
  </div>
</template>

<script setup>
  import BaseCard from '../../../components/UI/BaseCard.vue';
  import { useLang } from '../../../composables/useLang';

  defineProps({
    order: {
      type: Object,
      required: true,
    },
    isRtl: {
      type: Boolean,
      default: false,
    },
  });

  const { t } = useLang();

  const zoomImage = (path) => window.open(path, '_blank');
</script>

<style scoped>
  .design-visualization-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
  }

  .visual-card {
    background: white;
    border-radius: 16px;
    border: 1px solid #f1f5f9;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  }

  .visual-label {
    font-size: 0.75rem;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding-bottom: 4px;
    border-bottom: 2px solid #f1f5f9;
  }

  .visual-canvas {
    aspect-ratio: 3/4;
    background: #f8fafc;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px dashed #e2e8f0;
    transition: all 0.3s;
  }

  .visual-canvas:hover {
    border-color: #3b82f6;
    background: white;
  }

  .visual-canvas img {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }

  .empty-canvas {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    color: #94a3b8;
    text-align: center;
    padding: 1rem;
  }

  .empty-canvas span {
    font-size: 0.75rem;
    font-weight: 600;
  }

  .gallery-grid-compact {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 1rem;
  }
  .gallery-img-box-sm {
    aspect-ratio: 1;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #f1f5f9;
    cursor: pointer;
  }
  .gallery-img-box-sm img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
  }
  .gallery-img-box-sm:hover img {
    transform: scale(1.05);
  }

  .colors-flex-compact {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
  }
  .color-item-sm {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #fff;
    border-radius: 12px;
    border: 1px solid #f1f5f9;
    min-width: 140px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
  }
  .color-circle-sm {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 1px solid rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
  }
  .color-meta-sm .name {
    font-size: 0.8125rem;
    font-weight: 800;
    color: #1e293b;
  }
  .color-meta-sm .code {
    font-size: 0.65rem;
    color: #94a3b8;
    font-family: monospace;
  }

  .mt-1 {
    margin-top: 1.5rem;
  }
</style>
