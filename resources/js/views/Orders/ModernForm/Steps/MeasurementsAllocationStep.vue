<template>
  <div
    class="animate-fade-in mx-auto flex w-full max-w-[1400px] flex-col gap-6 pb-10 lg:flex-row lg:gap-8"
  >
    <!-- Left/Top Panel: Allocation & Summary -->
    <div class="flex w-full shrink-0 flex-col gap-6 lg:order-last lg:w-[340px]">
      <div
        class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-[0_8px_30px_-4px_rgba(59,130,246,0.1)] lg:sticky lg:top-8"
      >
        <div
          class="flex items-center justify-between border-b border-blue-100 bg-blue-50/50 px-6 py-5"
        >
          <div class="flex items-center gap-3">
            <div
              class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600"
            >
              <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                <line x1="3" y1="9" x2="21" y2="9" />
                <line x1="9" y1="21" x2="9" y2="9" />
              </svg>
            </div>
            <h3 class="font-bold tracking-tight text-blue-950">
              {{ t('Size Allocation', 'توزيع الكميات') }}
            </h3>
          </div>
        </div>

        <div class="p-0">
          <table class="w-full border-collapse text-left rtl:text-right">
            <thead>
              <tr class="border-b border-blue-100 bg-slate-50/50">
                <th
                  class="w-1/4 px-5 py-3 text-[11px] font-bold tracking-wider text-blue-500 uppercase"
                >
                  {{ t('Size', 'المقاس') }}
                </th>
                <th class="px-5 py-3 text-[11px] font-bold tracking-wider text-blue-500 uppercase">
                  {{ t('Quantity', 'الكمية') }}
                </th>
                <th class="w-12 px-2 py-3"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-blue-50/50">
              <tr
                v-for="(qty, size) in form.allocation"
                :key="size"
                class="group transition-colors hover:bg-blue-50/20"
              >
                <td class="px-5 py-3 align-middle">
                  <div class="flex h-10 items-center text-[13px] font-bold text-blue-900 uppercase">
                    {{ size }}
                  </div>
                </td>
                <td class="px-5 py-3 align-middle">
                  <input
                    v-model.number="form.allocation[size]"
                    type="number"
                    min="0"
                    class="h-10 w-full rounded-lg border border-blue-100 bg-white px-3 text-sm font-bold text-blue-950 tabular-nums transition-all outline-none hover:border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                    @focus="$event.target.select()"
                  />
                </td>
                <td class="px-2 py-3 text-right align-middle">
                  <button
                    v-if="Object.keys(form.allocation).length > 1"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-300 transition-colors hover:bg-red-50 hover:text-red-500"
                    @click="removeSize(size)"
                  >
                    <svg
                      width="16"
                      height="16"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2.5"
                      stroke-linecap="round"
                    >
                      <path d="M18 6L6 18M6 6l12 12" />
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
            <tfoot>
              <!-- Add Size Input Row -->
              <tr v-if="isAddingSize" class="bg-blue-50/30">
                <td class="border-t border-blue-100 px-5 py-3">
                  <input
                    v-model="newSizeName"
                    type="text"
                    class="h-9 w-full rounded-md border border-blue-200 px-2 text-xs font-bold text-blue-950 uppercase outline-none focus:border-blue-500"
                    :placeholder="t('Size', 'مقاس')"
                    autofocus
                    @keyup.enter="confirmAddSize"
                  />
                </td>
                <td colspan="2" class="border-t border-blue-100 px-5 py-3 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      class="h-9 rounded-md border border-blue-200 bg-white px-3 text-xs font-bold text-slate-500 transition-colors hover:bg-slate-50"
                      @click="isAddingSize = false"
                    >
                      {{ t('Cancel', 'إلغاء') }}
                    </button>
                    <button
                      class="h-9 rounded-md bg-blue-600 px-4 text-xs font-bold text-white shadow-sm transition-colors hover:bg-blue-700"
                      @click="confirmAddSize"
                    >
                      {{ t('Add', 'إضافة') }}
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-else>
                <td colspan="3" class="border-t border-blue-100 px-5 py-3">
                  <button
                    class="flex w-full items-center justify-center gap-2 rounded-lg border border-dashed border-blue-200 py-2 text-xs font-semibold text-blue-600 transition-all hover:border-blue-400 hover:bg-blue-50"
                    @click="isAddingSize = true"
                  >
                    <svg
                      width="14"
                      height="14"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="3"
                      stroke-linecap="round"
                    >
                      <path d="M12 5v14M5 12h14" />
                    </svg>
                    {{ t('Add Custom Size', 'إضافة مقاس مخصص') }}
                  </button>
                </td>
              </tr>

              <!-- Total Units Row -->
              <tr class="border-t-2 border-blue-100 bg-slate-50/50">
                <td class="px-5 py-4 text-[13px] font-bold tracking-widest text-blue-950 uppercase">
                  {{ t('Total', 'الإجمالي') }}
                </td>
                <td colspan="2" class="px-5 py-4 text-right">
                  <span
                    class="text-3xl leading-none font-black tracking-tighter text-blue-600 tabular-nums"
                    :class="{ 'text-red-500': totalUnits === 0 }"
                    >{{ totalUnits }}</span
                  >
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="flex flex-col gap-3 border-t border-blue-100 bg-white p-5">
          <!-- Validation Feedback -->
          <div
            v-if="totalUnits === 0"
            class="flex items-start gap-2 rounded-lg border border-red-100 bg-red-50 p-3 text-[13px] leading-snug font-medium text-red-600"
          >
            <svg
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
              class="mt-0.5 shrink-0"
            >
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="8" x2="12" y2="12" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            <span>{{
              t(
                'Total allocation must be greater than 0 to proceed.',
                'مجموع الكميات يجب أن يكون أكبر من 0 للمتابعة.'
              )
            }}</span>
          </div>

          <!-- Primary Action Button injected into panel for clear context -->
          <button
            type="button"
            class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-b from-blue-500 to-blue-600 px-6 py-3 text-sm font-bold text-white shadow-[0_4px_14px_0_rgba(37,99,235,0.39)] transition-all outline-none hover:-translate-y-px hover:shadow-[0_6px_20px_rgba(37,99,235,0.23)] focus:ring-4 focus:ring-blue-500/20 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0"
            :disabled="totalUnits === 0"
            @click="$emit('request-next')"
          >
            {{ t('Continue to Review', 'المتابعة للمراجعة') }}
            <svg
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="3"
              stroke-linecap="round"
              :class="{ 'rotate-180': isRtl }"
            >
              <path d="M5 12h14M12 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Main Table: Measurements -->
    <div class="flex min-w-0 flex-1 flex-col gap-6">
      <!-- Template Suggestions -->
      <div
        v-if="templateSuggestions.length"
        class="custom-scrollbar flex items-center gap-3 overflow-x-auto pb-2"
      >
        <span
          class="shrink-0 text-[11px] font-bold tracking-widest whitespace-nowrap text-slate-400 uppercase"
          >{{ t('Quick Templates:', 'قوالب سريعة:') }}</span
        >
        <button
          v-for="sug in templateSuggestions.slice(0, 4)"
          :key="sug.id"
          class="group flex shrink-0 items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 transition-all hover:border-blue-300 hover:shadow-md hover:shadow-blue-500/5"
          @click="applyTemplate(sug)"
        >
          <svg
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            class="text-blue-500 transition-transform group-hover:scale-110"
          >
            <path d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
          <span class="text-[13px] font-semibold text-slate-700">{{ sug.title }}</span>
        </button>
      </div>

      <!-- Matrix Table Box -->
      <div
        class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
      >
        <div
          class="relative z-20 flex items-center justify-between border-b border-blue-100 bg-white px-6 py-5"
        >
          <div>
            <h3 class="text-lg font-bold tracking-tight text-blue-950">
              {{ t('Measurements Matrix', 'مصفوفة القياسات') }}
            </h3>
            <p class="mt-1 max-w-sm text-[13px] text-blue-500">
              {{
                t(
                  'Define precise dimensions per size in cm. Columns auto-sync with your size allocation.',
                  'حدد الأبعاد الدقيقة لكل مقاس بالسنتيمتر. الأعمدة تتزامن تلقائياً مع أحجام التوزيع.'
                )
              }}
            </p>
          </div>
          <button
            class="flex items-center gap-2 rounded-xl bg-blue-50 px-5 py-2.5 text-[13px] font-bold text-blue-700 transition-colors outline-none hover:bg-blue-100 focus:ring-4 focus:ring-blue-100"
            @click="addMeasurement"
          >
            <svg
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="3"
              stroke-linecap="round"
            >
              <path d="M12 5v14M5 12h14" />
            </svg>
            <span class="hidden sm:inline">{{ t('Add Measurement', 'إضافة نقطة قياس') }}</span>
          </button>
        </div>

        <div class="custom-scrollbar relative z-10 min-w-full overflow-x-auto pb-2">
          <table class="w-full border-collapse text-left whitespace-nowrap rtl:text-right">
            <thead>
              <tr class="border-b border-blue-100 bg-blue-50/50">
                <th
                  class="sticky left-0 z-20 min-w-[200px] bg-blue-50/50 px-6 py-4 text-[11px] font-bold tracking-wider text-blue-500 uppercase shadow-[1px_0_0_0_#dbeafe] rtl:right-0 rtl:shadow-[-1px_0_0_0_#dbeafe]"
                >
                  {{ t('Point of Measurement', 'نقطة القياس') }}
                </th>
                <th
                  v-for="size in sizes"
                  :key="size"
                  class="w-28 border-l border-blue-50 px-4 py-4 text-center text-[12px] font-bold text-blue-800"
                >
                  {{ size }}
                </th>
                <th class="w-14 border-l border-blue-50 px-4 py-4"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-blue-50">
              <tr
                v-for="(measure, idx) in form.measurements"
                :key="idx"
                class="group transition-colors hover:bg-blue-50/30"
              >
                <td
                  class="sticky left-0 z-10 bg-white p-0 align-top shadow-[1px_0_0_0_#eff6ff] group-hover:bg-[#f8fafc] rtl:right-0 rtl:shadow-[-1px_0_0_0_#eff6ff]"
                >
                  <div class="flex h-full min-h-[56px] items-center px-3">
                    <span class="w-6 shrink-0 text-center text-[10px] font-bold text-blue-300">{{
                      idx + 1
                    }}</span>
                    <input
                      v-model="measure.name"
                      type="text"
                      class="h-full flex-1 border-none bg-transparent px-3 py-4 text-[13px] font-semibold text-blue-900 placeholder-blue-300 focus:ring-0 focus:outline-none"
                      :placeholder="t('e.g. Chest Width', 'مثال: عرض الصدر')"
                    />
                  </div>
                </td>
                <td v-for="size in sizes" :key="size" class="border-l border-blue-50 p-2 align-top">
                  <input
                    v-model.number="measure.values[size]"
                    type="number"
                    step="0.5"
                    class="h-11 w-full rounded-lg border border-transparent bg-blue-50/30 px-2 text-center text-[14px] font-medium text-blue-800 tabular-nums transition-all outline-none focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-500/10"
                    placeholder="0.0"
                    @focus="$event.target.select()"
                  />
                </td>
                <td class="border-l border-blue-50 p-2 text-center align-top">
                  <button
                    class="inline-flex h-11 w-11 items-center justify-center rounded-xl text-slate-400 transition-colors hover:bg-red-50 hover:text-red-500 focus:opacity-100 sm:opacity-0 sm:group-hover:opacity-100"
                    @click="removeMeasurement(idx)"
                  >
                    <svg
                      width="18"
                      height="18"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2.5"
                      stroke-linecap="round"
                    >
                      <path d="M18 6L6 18M6 6l12 12" />
                    </svg>
                  </button>
                </td>
              </tr>
              <tr v-if="!form.measurements.length">
                <td :colspan="sizes.length + 2" class="px-6 py-16 text-center">
                  <div
                    class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 text-blue-300"
                  >
                    <svg
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                      <line x1="3" y1="9" x2="21" y2="9" />
                      <line x1="9" y1="21" x2="9" y2="9" />
                    </svg>
                  </div>
                  <p class="text-[14px] font-medium text-blue-500">
                    {{ t('No measurements defined.', 'لم يتم تحديد قياسات.') }}
                  </p>
                  <p class="mt-1 text-[13px] text-blue-400">
                    {{
                      t(
                        'Click "Add Measurement" to start building your fit matrix.',
                        'انقر على "إضافة نقطة قياس" للبدء في بناء مصفوفة القياسات.'
                      )
                    }}
                  </p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tolerance & Notes Card -->
      <div
        class="rounded-2xl border border-blue-100 bg-white p-6 shadow-[0_4px_20px_-4px_rgba(37,99,235,0.05)]"
      >
        <div class="flex flex-col gap-6 sm:flex-row">
          <div class="w-full shrink-0 sm:w-64">
            <label class="mb-2 block text-[13px] font-semibold text-blue-900">{{
              t('Global Tolerance', 'التفاوت العام المسموح')
            }}</label>
            <input
              v-model="form.measurement_tolerance"
              type="text"
              class="h-[42px] w-full rounded-xl border border-blue-200 bg-white px-4 text-sm font-semibold text-blue-950 transition-all outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
              placeholder="± 0.5 cm"
            />
          </div>
          <div class="flex-1">
            <label class="mb-2 block text-[13px] font-semibold text-blue-900">{{
              t('Special Instructions', 'تعليمات خاصة للقياسات')
            }}</label>
            <textarea
              v-model="form.measurement_notes"
              class="min-h-[100px] w-full rounded-xl border border-blue-200 bg-white p-4 text-sm font-medium text-blue-800 transition-all outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
              :placeholder="
                t(
                  'Ex: All measurements are taken flat. Tolerance applies to pre-production samples.',
                  'مثال: جميع القياسات تؤخذ بوضع مسطح.'
                )
              "
            ></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { inject, computed, ref, watch } from 'vue';

  const { form, t, measurementLabels, templateSuggestions } = inject('orderWizardState');

  const totalUnits = computed(() => {
    return Object.values(form.allocation || {}).reduce((acc, val) => acc + (Number(val) || 0), 0);
  });

  const sizes = computed(() => Object.keys(form.allocation || {}));

  // Fallback initialization
  if (!form.measurements) form.measurements = [];
  if (!form.allocation || Object.keys(form.allocation).length === 0) {
    form.allocation = { S: 0, M: 0, L: 0, XL: 0 };
  }

  // Ensure all measurements have properties for all sizes
  watch(
    sizes,
    (newSizes) => {
      form.measurements.forEach((m) => {
        if (!m.values) m.values = {};
        newSizes.forEach((s) => {
          if (m.values[s] === undefined) m.values[s] = 0;
        });
      });
    },
    { deep: true, immediate: true }
  );

  // Listen globally to see if templates were fetched initially and inject them if measurementLabels exist
  watch(
    measurementLabels,
    (labels) => {
      if (labels && labels.length && !form.measurements.length) {
        labels.forEach((label) => {
          const name = typeof label === 'object' ? label.name : label;
          const values = {};
          sizes.value.forEach((s) => (values[s] = 0));
          form.measurements.push({ name, values });
        });
      }
    },
    { immediate: true }
  );

  const addMeasurement = () => {
    const values = {};
    sizes.value.forEach((s) => (values[s] = 0));
    form.measurements.push({ name: '', values });
  };

  const removeMeasurement = (idx) => {
    form.measurements.splice(idx, 1);
  };

  const isAddingSize = ref(false);
  const newSizeName = ref('');

  const confirmAddSize = () => {
    const size = newSizeName.value.trim().toUpperCase();
    if (size && !form.allocation[size]) {
      form.allocation[size] = 0;
    }
    newSizeName.value = '';
    isAddingSize.value = false;
  };

  const removeSize = (sizeToRemove) => {
    delete form.allocation[sizeToRemove];
    // Remove from all measurements
    form.measurements.forEach((m) => {
      delete m.values[sizeToRemove];
    });
  };

  const applyTemplate = (order) => {
    // Only applying labels if available in the fake template object logic
    // Normally would hit an endpoint
  };
</script>

<style scoped>
  @reference "../../../../../css/app.css";

  .custom-scrollbar::-webkit-scrollbar {
    height: 8px;
    width: 8px;
  }
  .custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
  }
  .custom-scrollbar::-webkit-scrollbar-thumb {
    @apply rounded-full bg-blue-300;
    border: 2px solid transparent;
    background-clip: padding-box;
  }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    @apply bg-blue-500;
  }
</style>
