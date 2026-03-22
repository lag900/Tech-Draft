<template>
  <div class="step-content animate-fade-in mx-auto max-w-5xl">
    <div class="step-header mb-10 text-center">
      <h2 class="mb-2 text-4xl font-bold tracking-tight text-slate-900">
        {{ t('Review & Finalize', 'المراجعة النهائية') }}
      </h2>
      <p class="font-medium text-slate-500">
        {{
          t(
            'Please review your entire technical specification before submitting for production.',
            'يرجى مراجعة كافة المواصفات الفنية قبل إرسال الطلب للإنتاج.'
          )
        }}
      </p>
    </div>

    <div class="space-y-8">
      <!-- 1. Information -->
      <div
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)]"
      >
        <div
          class="flex items-center justify-between border-b border-blue-100 bg-blue-50/50 px-6 py-4"
        >
          <div class="flex items-center gap-3">
            <div
              class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 font-black text-blue-600"
            >
              1
            </div>
            <h3 class="text-lg font-bold text-slate-800">
              {{ t('General Information', 'المعلومات العامة') }}
            </h3>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-6 p-6 md:grid-cols-3">
          <!-- Title -->
          <div
            class="-m-2 flex flex-col gap-1.5 rounded-lg p-2 transition-colors focus-within:bg-slate-50"
            :class="{ 'bg-red-50 ring-1 ring-red-500': !form.title }"
          >
            <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{
              t('Style Name', 'اسم الموديل')
            }}</span>
            <span
              class="text-sm font-bold"
              :class="form.title ? 'text-slate-900' : 'text-red-500 italic'"
              >{{ form.title || t('Not provided', 'لم يتم التحديد') }}</span
            >
          </div>
          <!-- Category -->
          <div
            class="-m-2 flex flex-col gap-1.5 rounded-lg p-2 transition-colors focus-within:bg-slate-50"
            :class="{ 'bg-red-50 ring-1 ring-red-500': !form.category_id }"
          >
            <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{
              t('Category', 'التصنيف')
            }}</span>
            <span
              class="text-sm font-bold"
              :class="form.category_id ? 'text-slate-900' : 'text-red-500 italic'"
              >{{ categoryName || t('Not provided', 'لم يتم التحديد') }}</span
            >
          </div>
          <!-- Item Type -->
          <div
            class="-m-2 flex flex-col gap-1.5 rounded-lg p-2 transition-colors focus-within:bg-slate-50"
            :class="{ 'bg-red-50 ring-1 ring-red-500': !form.item_type_id }"
          >
            <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{
              t('Item Type', 'نوع القطعة')
            }}</span>
            <span
              class="text-sm font-bold"
              :class="form.item_type_id ? 'text-slate-900' : 'text-red-500 italic'"
              >{{ itemTypeName || t('Not provided', 'لم يتم التحديد') }}</span
            >
          </div>
          <!-- Fit Profile -->
          <div
            class="-m-2 flex flex-col gap-1.5 rounded-lg p-2 transition-colors focus-within:bg-slate-50"
            :class="{ 'bg-red-50 ring-1 ring-red-500': !form.fit_id }"
          >
            <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{
              t('Fit Target Profile', 'هيكل القصة المستهدفة')
            }}</span>
            <span
              class="text-sm font-bold"
              :class="form.fit_id ? 'text-slate-900' : 'text-red-500 italic'"
              >{{ fitName || t('Not provided', 'لم يتم التحديد') }}</span
            >
          </div>
          <!-- Product Code -->
          <div
            class="-m-2 flex flex-col gap-1.5 rounded-lg p-2 transition-colors focus-within:bg-slate-50"
          >
            <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{
              t('Product Code', 'كود التخزين')
            }}</span>
            <span class="text-sm font-bold text-slate-900">{{
              form.product_code || t('Auto-generated', 'مولد تلقائياً')
            }}</span>
          </div>
          <!-- Seasons -->
          <div
            class="-m-2 flex flex-col gap-1.5 rounded-lg p-2 transition-colors focus-within:bg-slate-50"
            :class="{ 'bg-red-50 ring-1 ring-red-500': !form.seasons.length }"
          >
            <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{
              t('Target Seasons', 'المواسم المستهدفة')
            }}</span>
            <span
              class="text-sm font-bold"
              :class="form.seasons.length ? 'text-slate-900' : 'text-red-500 italic'"
              >{{ formattedSeasons || t('Not provided', 'لم يتم التحديد') }}</span
            >
          </div>
        </div>
      </div>

      <!-- 2. Materials -->
      <div
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)]"
      >
        <div
          class="flex items-center justify-between border-b border-teal-100 bg-teal-50/50 px-6 py-4"
        >
          <div class="flex items-center gap-3">
            <div
              class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-100 font-black text-teal-600"
            >
              2
            </div>
            <h3 class="text-lg font-bold text-slate-800">
              {{ t('Materials & Visuals', 'الخامات والألوان') }}
            </h3>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-6 p-6 pb-2 lg:grid-cols-3">
          <!-- Fabric Type -->
          <div
            class="-m-2 flex flex-col gap-1.5 rounded-lg p-2 transition-colors focus-within:bg-slate-50"
            :class="{ 'bg-red-50 ring-1 ring-red-500': !form.fabric_type }"
          >
            <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{
              t('Fabric Type', 'نوع القماش')
            }}</span>
            <span
              class="text-sm font-bold"
              :class="form.fabric_type ? 'text-slate-900' : 'text-red-500 italic'"
              >{{ fabricTypeName || t('Not provided', 'لم يتم التحديد') }}</span
            >
          </div>
          <!-- Fabric Weight -->
          <div
            class="-m-2 flex flex-col gap-1.5 rounded-lg p-2 transition-colors focus-within:bg-slate-50"
            :class="{ 'bg-red-50 ring-1 ring-red-500': !form.fabric_weight }"
          >
            <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{
              t('Fabric Weight', 'وزن القماش')
            }}</span>
            <span
              class="text-sm font-bold"
              :class="form.fabric_weight ? 'text-slate-900' : 'text-red-500 italic'"
              >{{
                form.fabric_weight
                  ? `${form.fabric_weight} GSM`
                  : t('Not provided', 'لم يتم التحديد')
              }}</span
            >
          </div>
          <!-- Composition -->
          <div
            class="-m-2 flex flex-col gap-1.5 rounded-lg p-2 transition-colors focus-within:bg-slate-50"
          >
            <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{
              t('Composition', 'نسبة المكونات')
            }}</span>
            <span class="text-sm font-bold text-slate-900">{{
              form.fabric_composition || t('Not provided', 'لم يتم التحديد')
            }}</span>
          </div>
        </div>

        <div
          v-if="form.colors && form.colors.length"
          class="mt-4 border-t border-slate-100 p-6 pt-0"
        >
          <span
            class="mb-3 block text-[10px] font-black tracking-widest text-slate-400 uppercase"
            >{{ t('Color Palette', 'مجموعة الألوان') }}</span
          >
          <div class="flex flex-wrap gap-3">
            <div
              v-for="color in form.colors"
              :key="color.hex"
              class="flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5"
            >
              <div
                class="h-4 w-4 rounded-full border border-black/10 shadow-sm"
                :style="{ background: color.hex }"
              ></div>
              <span class="text-xs font-bold text-slate-700">{{ color.name }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. Measurements & Allocation -->
      <div
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)]"
      >
        <div
          class="flex items-center justify-between border-b border-indigo-100 bg-indigo-50/50 px-6 py-4"
        >
          <div class="flex items-center gap-3">
            <div
              class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 font-black text-indigo-600"
            >
              3
            </div>
            <h3 class="text-lg font-bold text-slate-800">
              {{ t('Sizes & Allocation', 'المقاسات والكمية') }}
            </h3>
          </div>
        </div>

        <div class="p-6">
          <div v-if="computedAllocations.length" class="mb-6 flex flex-wrap gap-4">
            <div
              v-for="(sz, idx) in computedAllocations"
              :key="idx"
              class="flex items-center overflow-hidden rounded-xl border border-slate-200 bg-white shadow-[0_2px_8px_-2px_rgba(0,0,0,0.05)] transition-all"
            >
              <div
                class="border-r border-slate-200 bg-slate-50 px-4 py-2 text-sm font-black text-slate-700 uppercase rtl:border-r-0 rtl:border-l"
              >
                {{ sz.size }}
              </div>
              <div class="bg-white px-4 py-2 text-sm font-bold text-indigo-600">
                {{ sz.quantity }}
                <span class="ml-1 text-[11px] text-slate-400">{{ t('pcs', 'قطعة') }}</span>
              </div>
            </div>
          </div>
          <div
            v-else
            class="mb-6 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-600"
          >
            <svg
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
            >
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="8" x2="12" y2="12" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            {{
              t('Total allocation must be greater than 0.', 'مجموع الكميات يجب أن يكون أكبر من 0.')
            }}
          </div>

          <div class="flex items-center justify-between border-t border-slate-100 pt-5">
            <span class="text-xs font-black tracking-widest text-slate-500 uppercase">{{
              t('Total Production Quantity', 'إجمالي كمية الإنتاج')
            }}</span>
            <div
              class="text-2xl leading-none font-black tabular-nums"
              :class="totalAllocation > 0 ? 'text-indigo-600' : 'text-red-500'"
            >
              {{ totalAllocation }}
            </div>
          </div>

          <!-- Measurement Matrix Preview -->
          <div
            v-if="form.measurements && form.measurements.length"
            class="mt-8 border-t border-slate-100 pt-6"
          >
            <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
              <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{
                t('Measurements Matrix', 'مصفوفة القياسات')
              }}</span>
              <div
                v-if="form.measurement_tolerance"
                class="rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-[11px] font-bold text-indigo-700"
              >
                {{ t('Global Tolerance:', 'التفاوت المسموح:') }}
                <span class="mx-1 text-indigo-900 ltr:font-mono">{{
                  form.measurement_tolerance
                }}</span>
              </div>
            </div>

            <div class="custom-scrollbar overflow-x-auto rounded-xl border border-slate-200">
              <table class="w-full text-left text-[13px] whitespace-nowrap rtl:text-right">
                <thead class="border-b border-slate-200 bg-slate-50/80">
                  <tr>
                    <th
                      class="sticky left-0 min-w-[150px] bg-slate-50/80 px-4 py-3 text-[11px] font-extrabold tracking-widest text-slate-500 uppercase rtl:right-0"
                    >
                      {{ t('Point of Measurement', 'نقطة القياس') }}
                    </th>
                    <th
                      v-for="size in sizes"
                      :key="size"
                      class="w-24 border-l border-slate-200/60 px-4 py-3 text-center font-black text-indigo-900"
                    >
                      {{ size }}
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                  <tr
                    v-for="(measure, idx) in form.measurements"
                    :key="idx"
                    class="transition-colors hover:bg-slate-50/50"
                  >
                    <td
                      class="sticky left-0 bg-white px-4 py-3 font-bold text-slate-700 shadow-[1px_0_0_0_#f1f5f9] rtl:right-0 rtl:shadow-[-1px_0_0_0_#f1f5f9]"
                    >
                      {{ measure.name || `${t('Point', 'نقطة')} ${idx + 1}` }}
                    </td>
                    <td
                      v-for="size in sizes"
                      :key="size"
                      class="border-l border-slate-100 bg-slate-50/30 px-4 py-3 text-center font-semibold text-slate-600"
                    >
                      {{ measure.values?.[size] || '0' }}
                      <span class="text-[10px] text-slate-400">cm</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div
              v-if="form.measurement_notes"
              class="mt-4 rounded-xl border border-yellow-100 bg-yellow-50/50 p-4"
            >
              <span
                class="mb-1 block text-[10px] font-black tracking-widest text-yellow-600 uppercase"
                >{{ t('Special Instructions', 'تعليمات خاصة') }}</span
              >
              <p class="text-[12px] font-semibold whitespace-pre-line text-slate-700">
                {{ form.measurement_notes }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- 4. Details -->
      <div
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)]"
      >
        <div
          class="flex items-center justify-between border-b border-purple-100 bg-purple-50/50 px-6 py-4"
        >
          <div class="flex items-center gap-3">
            <div
              class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 font-black text-purple-600"
            >
              4
            </div>
            <h3 class="text-lg font-bold text-slate-800">
              {{ t('Branding & Technical Specs', 'العلامة التجارية والمواصفات') }}
            </h3>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-6 p-6 md:grid-cols-4">
          <div
            class="-m-2 flex flex-col gap-1.5 rounded-lg p-2 transition-colors focus-within:bg-slate-50"
          >
            <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{
              t('Main Label', 'ليبل الرقبة')
            }}</span>
            <span class="text-sm font-bold text-slate-900">{{
              form.main_label_type || t('Not provided', 'لم يتم التحديد')
            }}</span>
          </div>
          <div
            class="-m-2 flex flex-col gap-1.5 rounded-lg p-2 transition-colors focus-within:bg-slate-50"
          >
            <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{
              t('Size Label', 'طريقة تثبيت المقاس')
            }}</span>
            <span class="text-sm font-bold text-slate-900">{{
              form.size_label_type || t('Not provided', 'لم يتم التحديد')
            }}</span>
          </div>
          <div
            class="-m-2 flex flex-col gap-1.5 rounded-lg p-2 transition-colors focus-within:bg-slate-50"
          >
            <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{
              t('Care Label', 'ليبل العناية')
            }}</span>
            <span class="text-sm font-bold text-slate-900">{{
              form.care_label_type || t('Not provided', 'لم يتم التحديد')
            }}</span>
          </div>
          <div
            class="-m-2 flex flex-col gap-1.5 rounded-lg p-2 transition-colors focus-within:bg-slate-50"
          >
            <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{
              t('External Hangtags', 'تاق خارجي')
            }}</span>
            <span
              class="text-sm font-bold"
              :class="form.hangtag_required ? 'text-purple-600' : 'text-slate-500'"
              >{{
                form.hangtag_required ? t('Required', 'مطلوب') : t('Not Required', 'غير مطلوب')
              }}</span
            >
          </div>
        </div>
        <div v-if="form.notes" class="border-t border-slate-100 bg-amber-50/30 p-6">
          <span
            class="mb-2 block text-[10px] font-black tracking-widest text-amber-600 uppercase"
            >{{ t('Manufacturing Instructions', 'تعليمات التصنيع') }}</span
          >
          <p class="text-[13px] leading-relaxed font-semibold whitespace-pre-line text-slate-700">
            {{ form.notes }}
          </p>
        </div>
      </div>

      <!-- 5. Assets -->
      <div
        class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)]"
      >
        <div
          class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-6 py-4"
        >
          <div class="flex items-center gap-3">
            <div
              class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 font-black text-emerald-600"
            >
              5
            </div>
            <h3 class="text-lg font-bold text-slate-800">
              {{ t('Visual Assets', 'الصور والملفات') }}
            </h3>
          </div>
        </div>
        <div class="p-6">
          <!-- Needs Front Image -->
          <div
            v-if="!form.design_front_image"
            class="mb-4 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-600"
          >
            <svg
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
            >
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="8" x2="12" y2="12" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            {{ t('Front design image is required to proceed.', 'صورة الواجهة الأمامية مطلوبة.') }}
          </div>

          <div class="custom-scrollbar flex gap-4 overflow-x-auto pb-4">
            <div
              v-if="previews.design_front_image"
              class="relative w-32 shrink-0 overflow-hidden rounded-xl border border-slate-200"
            >
              <img :src="previews.design_front_image" class="h-32 w-full object-cover" />
              <div
                class="absolute inset-x-0 bottom-0 truncate bg-black/60 px-1 py-1 text-center text-[10px] font-bold text-white"
              >
                Front View
              </div>
            </div>
            <div
              v-if="previews.design_back_image"
              class="relative w-32 shrink-0 overflow-hidden rounded-xl border border-slate-200"
            >
              <img :src="previews.design_back_image" class="h-32 w-full object-cover" />
              <div
                class="absolute inset-x-0 bottom-0 truncate bg-black/60 px-1 py-1 text-center text-[10px] font-bold text-white"
              >
                Back View
              </div>
            </div>
            <div
              v-if="previews.technical_sketch"
              class="relative w-32 shrink-0 overflow-hidden rounded-xl border border-slate-200"
            >
              <img :src="previews.technical_sketch" class="h-32 w-full object-cover" />
              <div
                class="absolute inset-x-0 bottom-0 truncate bg-black/60 px-1 py-1 text-center text-[10px] font-bold text-white"
              >
                Sketch
              </div>
            </div>
            <div
              v-for="(img, idx) in galleryPreviews"
              :key="idx"
              class="relative w-32 shrink-0 overflow-hidden rounded-xl border border-slate-200"
            >
              <img :src="img.preview" class="h-32 w-full object-cover" />
              <div
                class="absolute inset-x-0 bottom-0 truncate bg-black/60 px-1 py-1 text-center text-[10px] font-bold text-white"
              >
                Reference
              </div>
            </div>
            <div
              v-if="
                !previews.design_front_image &&
                !previews.design_back_image &&
                !previews.technical_sketch &&
                galleryPreviews.length === 0
              "
              class="w-full py-8 text-center text-sm font-medium text-slate-400 italic"
            >
              {{ t('No assets uploaded yet.', 'لم يتم رفع أي ملفات بعد.') }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { inject, computed } from 'vue';

  const {
    form,
    errors,
    t,
    isRtl,
    categories,
    itemTypes,
    fits,
    fabricList,
    previews,
    galleryPreviews,
  } = inject('orderWizardState');

  const categoryName = computed(
    () => categories.value.find((c) => c.id === form.category_id)?.name || ''
  );
  const itemTypeName = computed(
    () => itemTypes.value.find((c) => c.id === form.item_type_id)?.name || ''
  );
  const fitName = computed(() => fits.value.find((c) => c.id === form.fit_id)?.name || '');

  const fabricTypeName = computed(() => {
    // Wait if the user typed custom text vs chose from ID
    const found = fabricList.value.find(
      (f) => f.id === form.fabric_type || f.name === form.fabric_type
    );
    return found ? found.name : form.fabric_type;
  });

  const seasonsAr = {
    spring: 'ربيع',
    summer: 'صيف',
    autumn: 'خريف',
    winter: 'شتاء',
  };
  const formattedSeasons = computed(() => {
    return form.seasons.map((s) => t(s, seasonsAr[s] || s)).join(', ');
  });

  const computedAllocations = computed(() => {
    return Object.entries(form.allocation)
      .filter(([_, qty]) => Number(qty) > 0)
      .map(([size, quantity]) => ({ size, quantity: Number(quantity) }));
  });

  const totalAllocation = computed(() => {
    return Object.values(form.allocation).reduce((acc, curr) => acc + (Number(curr) || 0), 0);
  });

  const sizes = computed(() => Object.keys(form.allocation || {}));
</script>

<style scoped>
  @reference "../../../../../css/app.css";
  .custom-scrollbar::-webkit-scrollbar {
    height: 6px;
    width: 6px;
  }
  .custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
  }
  .custom-scrollbar::-webkit-scrollbar-thumb {
    @apply rounded-full bg-slate-200;
  }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    @apply bg-slate-300;
  }
</style>
