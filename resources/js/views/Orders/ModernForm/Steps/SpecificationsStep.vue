<template>
  <div class="step-content animate-fade-in mx-auto max-w-4xl">
    <div class="step-header mb-10">
      <h2 class="mb-3 text-4xl font-bold tracking-tight text-slate-900">
        {{ t('Branding & Technical Specs', 'العلامة التجارية والمواصفات') }}
      </h2>
      <p class="max-w-2xl font-medium text-slate-500">
        {{
          t(
            'Detailed labeling, hardware, and packaging requirements for final production.',
            'حدد متطلبات الملصقات، الإضافات المعدنية، والتغليف للإنتاج النهائي.'
          )
        }}
      </p>
    </div>

    <!-- Branding Section -->
    <div class="card mb-8 shadow-sm transition-all hover:shadow-md">
      <div class="mb-8 flex items-center gap-4">
        <div
          class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600"
        >
          <svg
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.2"
          >
            <path
              d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"
            />
          </svg>
        </div>
        <div>
          <h3 class="text-xl font-black tracking-tight text-slate-800">
            {{ t('Labelling & Branding', 'الملصقات والعلامة التجارية') }}
          </h3>
          <p class="mt-0.5 text-xs font-bold tracking-wider text-slate-400 uppercase">
            {{ t('Core Identity Elements', 'عناصر الهوية الأساسية') }}
          </p>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-x-12 gap-y-8 md:grid-cols-2">
        <BaseInput
          v-model="form.main_label_type"
          :label="t('Main Neck Label Type', 'نوع ليبل الرقبة الرئيسي')"
          placeholder="Ex: Woven, Satin, Heat-Press..."
          help-text="Material and placement on neck line."
        />
        <BaseInput
          v-model="form.size_label_type"
          :label="t('Size Label Attachment', 'طريقة تثبيت ليبل المقاس')"
          placeholder="Ex: Separate tab, Printed bottom..."
          help-text="Can be bundled with main label or separate."
        />
        <BaseInput
          v-model="form.care_label_type"
          :label="t('Care & Content Label', 'ليبل العناية والمحتوى')"
          placeholder="Ex: Satin 3.5cm, Left side seam..."
          help-text="Fiber content and washing instructions."
        />

        <div class="field-item">
          <label class="input-label mb-4 block">{{
            t('External Hangtags Required?', 'هل تطلب تاق خارجي؟')
          }}</label>
          <div class="flex items-center gap-6">
            <!-- Toggle UI -->
            <button
              type="button"
              class="toggle-btn"
              :class="{ active: form.hangtag_required }"
              @click="form.hangtag_required = !form.hangtag_required"
            >
              <div class="toggle-circle"></div>
              <span
                class="group-[.active]:text-primary ml-10 text-[10px] font-black tracking-widest text-slate-400 uppercase transition-colors"
              >
                {{ form.hangtag_required ? t('Enabled', 'مفعل') : t('Disabled', 'معطل') }}
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Admin Factory Specs Zone -->
    <div
      v-if="userRole === 'admin'"
      class="card border-primary/20 mb-8 border-dashed bg-slate-50/50"
    >
      <div class="mb-8 flex items-center gap-4">
        <div
          class="text-primary flex h-10 w-10 items-center justify-center rounded-xl border border-slate-100 bg-white shadow-sm"
        >
          <svg
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <path
              d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"
            />
          </svg>
        </div>
        <h4 class="text-primary text-sm font-black tracking-widest uppercase">
          {{ t('Admin: Hardware & Production Trims', 'المصنع: الإضافات الفنية') }}
        </h4>
      </div>

      <div class="mb-8 grid grid-cols-2 gap-6 text-left md:grid-cols-3 rtl:text-right">
        <BaseInput
          v-model="form.zipper_type"
          :label="t('Zipper Spec', 'نوع السحاب')"
          placeholder="Ex: YKK #5 Nylon"
        />
        <BaseInput
          v-model="form.button_type"
          :label="t('Button Spec', 'نوع الزراير')"
          placeholder="Ex: Wood, 4Hole 1.5cm"
        />
        <BaseInput
          v-model="form.drawcord_type"
          :label="t('Cord / Elastic', 'الرباط / المطاط')"
          placeholder="Ex: Flat drawcord 10mm"
        />
        <BaseInput
          v-model="form.rib_type"
          :label="t('Ribbing Spec', 'نوع الريب')"
          placeholder="Ex: 2x2 Cotton Rib"
        />
        <BaseInput
          v-model="form.stitching_type"
          :label="t('Stitching Detail', 'نوع الخياطة')"
          placeholder="Ex: Double needle"
        />
      </div>

      <h4
        class="mb-6 flex items-center gap-2 text-xs font-black tracking-widest text-slate-400 uppercase"
      >
        <div class="h-1.5 w-1.5 rounded-full bg-slate-300"></div>
        {{ t('Packaging & Logistics', 'التغليف واللوجستيات') }}
      </h4>
      <div class="grid grid-cols-2 gap-6 lg:grid-cols-4">
        <BaseInput
          v-model="form.packaging_type"
          :label="t('Package Bag', 'تحميص التغليف')"
          placeholder="Polybag, Eco..."
        />
        <BaseInput
          v-model="form.folding_method"
          :label="t('Folding', 'طريقة الطي')"
          placeholder="Flat, Hanger..."
        />
        <div class="col-span-1 mt-4 flex h-16 items-center lg:col-span-2">
          <label class="group flex cursor-pointer items-center gap-3">
            <input
              v-model="form.barcode_required"
              type="checkbox"
              class="text-primary focus:ring-primary h-5 w-5 rounded border-slate-300 transition-all"
            />
            <span
              class="text-sm font-bold tracking-widest text-slate-500 uppercase transition-colors group-hover:text-amber-600"
              >{{ t('Barcode / SKU Labels Required', 'باركود مطلوب') }}</span
            >
          </label>
        </div>
      </div>
    </div>

    <!-- Production Notes Area -->
    <div class="card">
      <div class="mb-4 flex items-center gap-4">
        <div
          class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-400"
        >
          <svg
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
          </svg>
        </div>
        <div>
          <h3 class="text-xl font-bold text-slate-900">
            {{ t('Manufacturing Instructions', 'تعليمات التصنيع') }}
          </h3>
          <p class="text-xs font-medium tracking-tight text-slate-400">
            {{
              t(
                'Special requests for embroidery, placement, or final finish.',
                'تعليمات خاصة للتطريز، أماكن الطباعة، أو اللمسات النهائية.'
              )
            }}
          </p>
        </div>
      </div>
      <textarea
        v-model="form.notes"
        class="min-h-[160px] w-full rounded-xl border border-slate-200 bg-white p-6 text-sm font-medium text-slate-700 shadow-sm transition-all hover:border-slate-300 focus:border-slate-800 focus:ring-4 focus:ring-slate-800/10 focus:outline-none"
        :placeholder="t('Describe complex details here...', 'صف أي تفاصيل معقدة هنا...')"
      ></textarea>
    </div>
  </div>
</template>

<script setup>
  import { inject, computed } from 'vue';
  import BaseInput from '../../../../components/UI/BaseInput.vue';

  const { form, errors, isRtl, t, user } = inject('orderWizardState');

  const userRole = computed(() => user.value?.role);
</script>

<style scoped>
  @reference "../../../../../css/app.css";

  .toggle-btn {
    @apply relative h-8 w-16 rounded-full border border-slate-200 bg-slate-100 transition-colors duration-300;
  }
  .toggle-btn.active {
    @apply bg-primary/10 border-primary/20;
  }
  .toggle-circle {
    @apply absolute top-1 left-1 h-6 w-6 rounded-full bg-white shadow-md transition-all duration-300;
  }
  .active .toggle-circle {
    @apply bg-primary left-9;
  }
</style>
