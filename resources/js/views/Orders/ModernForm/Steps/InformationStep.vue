<template>
  <div class="step-content animate-fade-in">
    <div class="step-header mb-8">
      <h2 class="mb-2 text-3xl font-bold text-slate-900">
        {{ t('Product Identity', 'هوية المنتج') }}
      </h2>
      <p class="font-medium text-slate-500">
        {{
          t(
            'Define the core specifications and classification of your design.',
            'حدد المواصفات الأساسية وتصنيف تصميمك.'
          )
        }}
      </p>
    </div>

    <!-- Admin Client Selection -->
    <div v-if="userRole === 'admin'" class="card border-primary/20 bg-primary/5 mb-6">
      <div class="mb-4 flex items-center gap-4">
        <div
          class="bg-primary/20 text-primary flex h-10 w-10 items-center justify-center rounded-full"
        >
          <svg
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
          </svg>
        </div>
        <div>
          <h4 class="font-bold text-slate-800">
            {{ t('Client / Brand Selection', 'اختيار العميل / العلامة التجارية') }}
          </h4>
          <p class="text-xs text-slate-500">
            {{
              t(
                'Project will be assigned to this brand account.',
                'سيتم ربط هذا الطلب بحساب العلامة التجارية المحدد.'
              )
            }}
          </p>
        </div>
      </div>
      <SearchableSelect
        v-model="form.client_id"
        :options="clients"
        :label="t('Client Name', 'اسم العميل')"
        :placeholder="t('Search for a client...', 'ابحث عن عميل...')"
        :is-rtl="isRtl"
        :error="errors.client_id"
        required
      />
    </div>

    <!-- Basic Info Grid -->
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
      <BaseInput
        v-model="form.title"
        :label="t('Design Title', 'عنوان التصميم')"
        :placeholder="t('Ex: Oversized Heavy Hoodie', 'مثال: هودي شتوي واسع')"
        :error="errors.title"
        :help-text="
          t('Helpful descriptive name for your product.', 'اسم وصفي يساعدك على تمييز المنتج.')
        "
        required
      />
      <BaseInput
        v-model="form.product_code"
        :label="t('SKU / Product Code', 'كود التخزين / الكود الفني')"
        :placeholder="t('Ex: WD-2024-001', 'مثال: WD-2024-001')"
        :error="errors.product_code"
        :help-text="
          t(
            'Leave empty to generate automatically, or enter your own code.',
            'سيتم توليده تلقائياً، ويمكنك إدخال كود خاص يدوياً.'
          )
        "
      />
    </div>

    <!-- Classification Card -->
    <div class="card mb-8 border-blue-100 p-6 shadow-[0_4px_20px_-4px_rgba(37,99,235,0.05)] lg:p-8">
      <h3 class="mb-8 flex items-center gap-3 text-xl font-bold text-blue-950">
        <div
          class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600"
        >
          <svg
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
            <circle cx="12" cy="7" r="4" />
          </svg>
        </div>
        {{ t('Product Classification', 'تصنيف المنتج') }}
      </h3>

      <div class="relative flex max-w-2xl flex-col gap-6">
        <!-- Vertical Step Line for visual path -->
        <div
          v-if="form.category_id"
          class="absolute top-[44px] bottom-[30px] left-[13px] hidden w-[3px] rounded-full bg-blue-100 transition-all delay-100 duration-700 sm:block md:left-[17px]"
          :class="isRtl ? 'right-[13px] left-auto md:right-[17px]' : ''"
        ></div>

        <!-- 1. Category -->
        <div
          class="relative z-10 w-full sm:pl-10 lg:pl-12"
          :class="isRtl ? 'sm:pr-10 sm:pl-0 lg:pr-12 lg:pl-0' : ''"
        >
          <div
            class="absolute top-[1.8rem] left-0 flex hidden h-7 w-7 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white shadow-sm sm:flex"
            :class="isRtl ? 'right-0 left-auto' : ''"
          >
            1
          </div>
          <SearchableSelect
            v-model="form.category_id"
            :options="categories"
            :label="t('Primary Category', 'التصنيف الرئيسي')"
            :placeholder="t('Select category to continue...', 'اختر التصنيف للمتابعة...')"
            :is-rtl="isRtl"
            :error="errors.category_id"
            required
          />
        </div>

        <!-- 2. Item Type -->
        <Transition name="slide-fade">
          <div
            v-if="form.category_id"
            class="relative z-10 w-full sm:pl-10 lg:pl-12"
            :class="isRtl ? 'sm:pr-10 sm:pl-0 lg:pr-12 lg:pl-0' : ''"
          >
            <div
              class="absolute top-[1.8rem] left-0 flex hidden h-7 w-7 items-center justify-center rounded-full bg-blue-500 text-xs font-bold text-white shadow-[0_2px_10px_rgba(59,130,246,0.3)] sm:flex"
              :class="isRtl ? 'right-0 left-auto' : ''"
            >
              2
            </div>
            <SearchableSelect
              v-model="form.item_type_id"
              :options="itemTypes"
              :label="t('Specific Item Type', 'نوع القطعة الخاص')"
              :placeholder="t('Select item type...', 'اختر نوع القطعة...')"
              :is-rtl="isRtl"
              :error="errors.item_type_id"
              required
            />
          </div>
        </Transition>

        <!-- 3. Fit Pattern -->
        <Transition name="slide-fade">
          <div
            v-if="form.item_type_id"
            class="relative z-10 w-full sm:pl-10 lg:pl-12"
            :class="isRtl ? 'sm:pr-10 sm:pl-0 lg:pr-12 lg:pl-0' : ''"
          >
            <div
              class="absolute top-[1.8rem] left-0 flex hidden h-7 w-7 items-center justify-center rounded-full bg-blue-400 text-xs font-bold text-white shadow-[0_2px_10px_rgba(96,165,250,0.3)] sm:flex"
              :class="isRtl ? 'right-0 left-auto' : ''"
            >
              3
            </div>
            <SearchableSelect
              v-model="form.fit_id"
              :options="fits"
              :label="t('Fit Target Profile', 'هيكل القصة المستهدفة')"
              :placeholder="t('Select fit type...', 'اختر هيكل القصة...')"
              :is-rtl="isRtl"
              :error="errors.fit_id"
              required
            />
          </div>
        </Transition>
      </div>
    </div>

    <!-- Versioning & Season -->
    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
      <div class="card">
        <h3 class="mb-6 text-xl font-bold text-slate-900">
          {{ t('Seasons & Timeline', 'المواسم والجدول الزمني') }}
        </h3>
        <div class="space-y-6">
          <div class="field-item">
            <label class="input-label mb-3 block">
              {{ t('Target Seasons', 'المواسم المستهدفة') }} <span class="text-red-500">*</span>
            </label>
            <div
              class="grid grid-cols-2 gap-3 transition-colors duration-300"
              :class="{ '-m-2 rounded-xl bg-red-50/50 p-2 ring-1 ring-red-500': errors.seasons }"
            >
              <div
                v-for="season in availableSeasons"
                :key="season.value"
                class="season-check-card"
                :class="[
                  { active: form.seasons.includes(season.value) },
                  errors.seasons && !form.seasons.length
                    ? 'border-red-200 hover:border-red-300'
                    : '',
                ]"
                @click="toggleSeason(season.value)"
              >
                <div class="mb-1 text-2xl">{{ season.icon }}</div>
                <span class="text-xs font-bold uppercase">{{
                  isRtl ? season.labelAr : season.labelEn
                }}</span>
                <div class="check-indicator">
                  <svg
                    width="12"
                    height="12"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="4"
                  >
                    <polyline points="20 6 9 17 4 12" />
                  </svg>
                </div>
              </div>
            </div>
            <span v-if="errors.seasons" class="mt-2 block text-xs font-semibold text-red-500">{{
              errors.seasons
            }}</span>
          </div>
          <YearPicker
            v-model="form.year"
            :years="yearOptions.map((y) => y.id)"
            :label="t('Production Year', 'سنة الإنتاج')"
            :help-text="
              t('Affects seasonal planning and collections.', 'تؤثر على تخطيط المواسم والمجموعات.')
            "
            :is-rtl="isRtl"
            required
          />
        </div>
      </div>

      <div class="card border-dashed bg-slate-50">
        <h3 class="mb-6 text-xl font-bold text-slate-900">
          {{ t('Technical Versioning', 'إصدار المواصفات') }}
        </h3>
        <div class="grid grid-cols-2 gap-4">
          <BaseInput
            v-model="form.version_number"
            type="number"
            :label="t('Version', 'الإصدار')"
            :error="errors.version_number"
            help-text="Initial production baseline (1.0)"
          />
          <BaseInput
            v-model="form.revision_number"
            type="number"
            :label="t('Revision', 'المراجعة')"
            :error="errors.revision_number"
            help-text="Internal minor changes"
          />
        </div>
        <div class="mt-4 flex items-start gap-3 rounded-xl border border-slate-100 bg-white p-4">
          <div class="mt-1 text-amber-500">
            <svg
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="16" x2="12" y2="12" />
              <line x1="12" y1="8" x2="12.01" y2="8" />
            </svg>
          </div>
          <p class="text-xs text-slate-500 italic">
            {{
              t(
                'Versioning helps track changes between samples and final production.',
                'يساعد نظام الإصدارات في تتبع التعديلات المتبادلة بين العينات والإنتاج النهائي.'
              )
            }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { inject, computed, watch } from 'vue';
  import BaseInput from '../../../../components/UI/BaseInput.vue';
  import SearchableSelect from '../../../../components/UI/SearchableSelect.vue';
  import YearPicker from '../../../../components/UI/YearPicker.vue';

  const { form, errors, isRtl, t, categories, itemTypes, fits, clients, user } =
    inject('orderWizardState');

  const userRole = computed(() => user.value?.role);

  // Clean nested dependencies progressively on change
  watch(
    () => form.category_id,
    (newVal) => {
      if (!newVal) {
        form.item_type_id = '';
        form.fit_id = '';
      }
    }
  );

  watch(
    () => form.item_type_id,
    (newVal) => {
      if (!newVal) {
        form.fit_id = '';
      }
    }
  );

  const availableSeasons = [
    { value: 'spring', labelEn: 'Spring', labelAr: 'الربيع', icon: '🌸' },
    { value: 'summer', labelEn: 'Summer', labelAr: 'الصيف', icon: '☀️' },
    { value: 'autumn', labelEn: 'Autumn', labelAr: 'الخريف', icon: '🍂' },
    { value: 'winter', labelEn: 'Winter', labelAr: 'الشتاء', icon: '❄️' },
  ];

  const yearOptions = computed(() => {
    const currentYear = new Date().getFullYear();
    return Array.from({ length: 12 }, (_, i) => {
      const y = currentYear + i;
      return { id: y, name: y.toString() };
    });
  });

  const toggleSeason = (val) => {
    const index = form.seasons.indexOf(val);
    if (index > -1) {
      form.seasons.splice(index, 1);
    } else {
      form.seasons.push(val);
    }
  };
</script>

<style scoped>
  @reference "../../../../../css/app.css";

  .season-check-card {
    @apply relative flex cursor-pointer flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white p-4 transition-all duration-200 hover:border-slate-300 hover:shadow-md;
  }
  .season-check-card.active {
    @apply border-primary bg-primary/5 text-primary;
  }
  .check-indicator {
    @apply bg-primary absolute top-1 right-1 flex h-4 w-4 scale-0 items-center justify-center rounded-full text-white transition-transform;
  }
  .active .check-indicator {
    @apply scale-100;
  }

  .slide-fade-enter-active {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .slide-fade-leave-active {
    transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
  }
  .slide-fade-enter-from,
  .slide-fade-leave-to {
    transform: translateY(-20px);
    opacity: 0;
  }
</style>
