<template>
  <div class="step-content animate-fade-in mx-auto max-w-5xl">
    <div class="step-header mb-12 text-center">
      <h2 class="mb-2 text-4xl font-bold tracking-tight text-slate-900">
        {{ t('Visual Assets & Blueprints', 'الملفات الفنية والرسومات') }}
      </h2>
      <p class="mx-auto max-w-2xl font-medium text-slate-500">
        {{
          t(
            'Upload high-resolution sketches, technical drawings, and reference photos.',
            'ارفع سكتشات عالية الجودة، رسومات فنية، وصور مرجعية.'
          )
        }}
      </p>
    </div>

    <!-- Core Blueprint Zone -->
    <div class="mb-12 grid grid-cols-1 gap-8 md:grid-cols-3">
      <!-- Front View -->
      <div class="group asset-upload-card" @click="$refs.frontInput.click()">
        <input
          ref="frontInput"
          type="file"
          hidden
          @change="(e) => handleUpload(e, 'design_front_image')"
        />
        <div
          class="asset-preview-box"
          :style="
            previews.design_front_image
              ? { backgroundImage: 'url(' + previews.design_front_image + ')' }
              : {}
          "
        >
          <div v-if="!previews.design_front_image" class="empty-state">
            <svg
              width="32"
              height="32"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.2"
              class="mb-2 text-slate-200"
            >
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12" />
            </svg>
            <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{
              t('Front View', 'الواجهة الأمامية')
            }}</span>
          </div>
          <div v-else class="preview-overlay">
            <div
              class="flex h-8 w-8 items-center justify-center rounded-full border border-white/20 bg-white/20 text-white backdrop-blur-md"
            >
              <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="3"
              >
                <polyline points="20 6 9 17 4 12" />
              </svg>
            </div>
          </div>
        </div>
        <div
          class="group-hover:bg-primary flex h-12 items-center justify-center rounded-b-2xl border-t border-slate-100 bg-slate-50 text-xs font-bold text-slate-500 transition-all group-hover:text-white"
        >
          {{ t('Primary Front Blueprint', 'الرسم الأمامي الأساسي') }}
        </div>
      </div>

      <!-- Back View -->
      <div class="group asset-upload-card" @click="$refs.backInput.click()">
        <input
          ref="backInput"
          type="file"
          hidden
          @change="(e) => handleUpload(e, 'design_back_image')"
        />
        <div
          class="asset-preview-box"
          :style="
            previews.design_back_image
              ? { backgroundImage: 'url(' + previews.design_back_image + ')' }
              : {}
          "
        >
          <div v-if="!previews.design_back_image" class="empty-state">
            <svg
              width="32"
              height="32"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.2"
              class="mb-2 text-slate-200"
            >
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12" />
            </svg>
            <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{
              t('Back View', 'الواجهة الخلفية')
            }}</span>
          </div>
          <div v-else class="preview-overlay">
            <div
              class="flex h-8 w-8 items-center justify-center rounded-full border border-white/20 bg-white/20 text-white backdrop-blur-md"
            >
              <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="3"
              >
                <polyline points="20 6 9 17 4 12" />
              </svg>
            </div>
          </div>
        </div>
        <div
          class="group-hover:bg-primary flex h-12 items-center justify-center rounded-b-2xl border-t border-slate-100 bg-slate-50 text-xs font-bold text-slate-500 transition-all group-hover:text-white"
        >
          {{ t('Primary Back Blueprint', 'الرسم الخلفي الأساسي') }}
        </div>
      </div>

      <!-- Tech Sketch -->
      <div class="group asset-upload-card" @click="$refs.sketchInput.click()">
        <input
          ref="sketchInput"
          type="file"
          hidden
          @change="(e) => handleUpload(e, 'technical_sketch')"
        />
        <div
          class="asset-preview-box"
          :style="
            previews.technical_sketch
              ? { backgroundImage: 'url(' + previews.technical_sketch + ')' }
              : {}
          "
        >
          <div v-if="!previews.technical_sketch" class="empty-state text-amber-500">
            <svg
              width="32"
              height="32"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.2"
              class="mb-2 text-amber-200"
            >
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
            </svg>
            <span class="text-[10px] font-black tracking-widest text-amber-500/50 uppercase">{{
              t('Technical Sketch', 'رسم تقني / سكتش')
            }}</span>
          </div>
          <div v-else class="preview-overlay bg-amber-500/10">
            <div
              class="flex h-8 w-8 items-center justify-center rounded-full border border-amber-500/20 bg-amber-500/20 text-white backdrop-blur-md"
            >
              <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="3"
              >
                <polyline points="20 6 9 17 4 12" />
              </svg>
            </div>
          </div>
        </div>
        <div
          class="flex h-12 items-center justify-center rounded-b-2xl border-t border-amber-100 bg-amber-50 text-xs font-bold text-amber-600 transition-all group-hover:bg-amber-500 group-hover:text-white"
        >
          {{ t('Vector / Detail Blueprint', 'رسم تفصيلي / سكتش') }}
        </div>
      </div>
    </div>

    <!-- Generic Gallery Zone -->
    <div
      class="dropzone-outer card rounded-[2.5rem] border-none bg-slate-100 p-1 shadow-inner"
      @dragover.prevent="dragging = true"
      @dragleave.prevent="dragging = false"
      @drop.prevent="handleDrop"
    >
      <div
        class="relative flex flex-col items-center justify-center overflow-hidden rounded-[2.25rem] border-2 border-dashed bg-white p-10 transition-all duration-500"
        :class="dragging ? 'border-primary ring-primary/5 bg-primary/5 ring-8' : 'border-slate-200'"
      >
        <input ref="galleryInput" type="file" multiple hidden @change="handleGalleryUpload" />

        <div class="relative z-10 flex flex-col items-center">
          <div
            class="bg-primary/10 text-primary shadow-primary/10 mb-6 flex h-20 w-20 items-center justify-center rounded-[2rem] shadow-lg"
          >
            <svg
              width="32"
              height="32"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
            >
              <path
                d="M4 16l4.58-4.59a2 2 0 0 1 2.83 0L16 16m-2-2l1.58-1.59a2 2 0 0 1 2.83 0L20 14m-8-7h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"
              />
            </svg>
          </div>
          <h3 class="mb-2 text-2xl font-bold text-slate-900">
            {{ t('Reference Gallery', 'معرض الصور المرجعية') }}
          </h3>
          <p class="mb-8 max-w-sm text-center text-sm font-bold text-slate-400">
            {{
              t(
                'Drag and drop style references, construction photos, or fabric close-ups here.',
                'اسحب وأفلت صوراً لمرجعيات الستايل، أو صور الخياطة، أو لقطات مقربة للأقمشة هنا.'
              )
            }}
          </p>
          <button
            type="button"
            class="rounded-2xl bg-blue-600 px-8 py-3 text-sm font-bold tracking-widest text-white uppercase shadow-xl shadow-blue-600/20 transition-all hover:bg-blue-700"
            @click="$refs.galleryInput.click()"
          >
            {{ t('Browse your files', 'تصفح ملفاتك') }}
          </button>
          <p class="mt-4 text-[10px] font-black tracking-[0.2em] text-slate-300 uppercase">
            {{ t('Max size: 10MB per file', 'الحد الأقصى ١٠ ميجابايت للملف') }}
          </p>
        </div>

        <!-- Animated BG Lines for dragging -->
        <div v-if="dragging" class="pointer-events-none absolute inset-0 opacity-20">
          <div class="bg-primary absolute inset-0 animate-pulse opacity-5"></div>
        </div>
      </div>
    </div>

    <!-- Gallery Preview Grid -->
    <div
      v-if="galleryPreviews.length"
      class="mt-12 grid grid-cols-2 gap-6 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6"
    >
      <TransitionGroup name="list">
        <div
          v-for="(img, idx) in galleryPreviews"
          :key="idx"
          class="group relative aspect-square overflow-hidden rounded-3xl border-4 border-white shadow-xl transition-all hover:scale-105"
        >
          <img :src="img" class="h-full w-full object-cover" />
          <div
            class="absolute inset-0 flex items-end bg-gradient-to-t from-black/60 to-transparent p-3 opacity-0 transition-opacity group-hover:opacity-100"
          >
            <button
              type="button"
              class="w-full rounded-xl border border-red-500/30 bg-red-500/20 py-2 text-[10px] font-black text-white uppercase backdrop-blur-md transition-colors hover:bg-red-500"
              @click="$emit('removeGalleryImage', idx)"
            >
              {{ t('Delete', 'حذف') }}
            </button>
          </div>
        </div>
      </TransitionGroup>
    </div>
  </div>
</template>

<script setup>
  import { ref, inject } from 'vue';

  const { isRtl, t, previews, galleryPreviews } = inject('orderWizardState');

  const emit = defineEmits(['upload', 'uploadGallery', 'removeGalleryImage']);

  const dragging = ref(false);

  const handleUpload = (event, type) => {
    const file = event.target.files[0];
    if (file) emit('upload', { file, type });
  };

  const handleGalleryUpload = (event) => {
    const files = Array.from(event.target.files);
    if (files.length) emit('uploadGallery', files);
  };

  const handleDrop = (event) => {
    dragging.value = false;
    const files = Array.from(event.dataTransfer.files);
    if (files.length) emit('uploadGallery', files);
  };
</script>

<style scoped>
  @reference "../../../../../css/app.css";

  .asset-upload-card {
    @apply w-full cursor-pointer;
  }
  .asset-preview-box {
    @apply relative h-72 w-full overflow-hidden rounded-t-3xl border-4 border-white bg-slate-50 bg-cover bg-center shadow-lg transition-all group-hover:scale-[1.02] group-hover:shadow-2xl;
  }
  .preview-overlay {
    @apply bg-primary/10 absolute inset-0 flex items-center justify-center backdrop-blur-sm;
  }
  .empty-state {
    @apply absolute inset-0 flex flex-col items-center justify-center p-6 text-center;
  }

  .list-enter-active,
  .list-leave-active {
    transition: all 0.4s cubic-bezier(0.2, 1, 0.2, 1);
  }
  .list-enter-from,
  .list-leave-to {
    opacity: 0;
    scale: 0.8;
  }
</style>
