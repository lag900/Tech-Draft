<template>
  <div class="step-content animate-fade-in">
    <div class="step-header mb-8">
      <h2 class="mb-2 text-3xl font-bold text-slate-900">
        {{ t('Materials & Visuals', 'الخامات والألوان') }}
      </h2>
      <p class="font-medium text-slate-500">
        {{
          t(
            'Define the primary fabric and colorways for this design.',
            'حدد القماش الأساسي ومجموعة الألوان لهذا التصميم.'
          )
        }}
      </p>
    </div>

    <!-- Fabric Specification Card -->
    <div class="card mb-8">
      <h3 class="mb-6 flex items-center gap-2 text-xl font-bold text-slate-900">
        <svg
          width="22"
          height="22"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          class="text-teal-500"
        >
          <path
            d="M11 15h2M11 9h2m-7 8h12a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"
          />
        </svg>
        {{ t('Primary Fabric Specifications', 'مواصفات القماش الأساسي') }}
      </h3>

      <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-3">
        <SearchableSelect
          v-model="form.fabric_type"
          :options="fabricList"
          :label="t('Fabric Type', 'نوع القماش')"
          :placeholder="t('Select or type...', 'اختر أو اكتب...')"
          :is-rtl="isRtl"
          :error="errors.fabric_type"
          allow-custom
          required
          :help-text="t('Main material for the product.', 'المادة الأساسية للمنتج.')"
        />
        <div class="field-item">
          <BaseInput
            v-model="form.fabric_weight"
            type="number"
            :label="t('Weight (GSM)', 'الوزن (جرام)')"
            placeholder="320"
            :error="errors.fabric_weight"
            required
            :help-text="t('Grams per square meter.', 'عدد الجرامات لكل متر مربع.')"
          >
            <template #iconRight
              ><span class="text-xs font-bold text-slate-400">GSM</span></template
            >
          </BaseInput>
        </div>
        <BaseInput
          v-model="form.texture"
          :label="t('Feel / Texture', 'الملمس')"
          :placeholder="t('Ex: Soft, Peached, Ribbed...', 'مثال: ناعم، مبرد، مضلع...')"
        />
      </div>

      <div class="mb-2 grid grid-cols-1 gap-6 md:grid-cols-3">
        <BaseInput
          v-model="form.fabric_composition"
          :label="t('Composition', 'نسبة المكونات')"
          placeholder="Ex: 80% Cotton / 20% Polyester"
          :help-text="t('Material ratios (Fiber content)', 'نسب الألياف المكونة للقماش.')"
        />
        <BaseInput
          v-model="form.yarn_type"
          :label="t('Yarn Type', 'نوع الخيط')"
          placeholder="Ex: Ring Spun, Carded..."
        />
        <BaseInput
          v-model="form.fabric_structure"
          :label="t('Fabric Structure', 'بنية النسيج')"
          placeholder="Ex: Single Jersey, Pique, Terry..."
        />
      </div>

      <!-- Factory Detail Area (Admin Only) -->
      <div
        v-if="userRole === 'admin'"
        class="-mx-6 mt-8 rounded-b-xl border-t border-dashed border-slate-100 bg-slate-50/50 px-6 pt-6 pb-2"
      >
        <h4 class="mb-4 text-sm font-bold tracking-widest text-slate-500 uppercase">
          {{ t('Internal Factory Details', 'تفاصيل المصنع الداخلية') }}
        </h4>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <BaseInput v-model="form.fabric_code" :label="t('Fabric SKU / Code', 'كود القماش')" />
          <BaseInput v-model="form.fabric_supplier" :label="t('Supplier Name', 'اسم المورد')" />
        </div>
      </div>
    </div>

    <!-- Redesigned Color Picker Section -->
    <div class="card bg-white">
      <div class="mb-8 flex items-center justify-between">
        <div>
          <h3 class="mb-2 flex items-center gap-2 text-xl font-bold text-slate-900">
            <svg
              width="22"
              height="22"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              class="text-pink-500"
            >
              <path
                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
              />
            </svg>
            {{ t('Color Ways / Palette', 'مجموعة الألوان') }}
          </h3>
          <p class="text-xs text-slate-500">
            {{
              t(
                'Add one or more colors for this production line.',
                'أضف لوناً واحداً أو أكثر لهذا خط الإنتاج.'
              )
            }}
          </p>
        </div>
        <button
          type="button"
          class="inline-flex items-center gap-2 rounded-xl bg-pink-50 px-4 py-2 text-sm font-bold text-pink-600 transition-all hover:bg-pink-100"
          @click="addColor"
        >
          <svg
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="3"
          >
            <path d="M12 5v14M5 12h14" />
          </svg>
          {{ t('New Color', 'لون جديد') }}
        </button>
      </div>

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <TransitionGroup name="list">
          <div
            v-for="(color, index) in form.colors"
            :key="index"
            class="group relative flex flex-col rounded-2xl border-2 p-4 transition-all"
            :style="{ borderColor: color.hex + '33', backgroundColor: color.hex + '08' }"
          >
            <!-- Delete Button -->
            <button
              v-if="form.colors.length > 1"
              type="button"
              class="absolute top-2 right-2 z-10 flex h-7 w-7 items-center justify-center rounded-full bg-white text-slate-300 shadow-sm transition-colors hover:text-red-500"
              @click="removeColor(index)"
            >
              <svg
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="3"
              >
                <path d="M18 6 6 18M6 6l12 12" />
              </svg>
            </button>

            <!-- Color Square & Picker -->
            <div
              class="group/swatch relative mb-4 h-16 w-full cursor-pointer overflow-hidden rounded-xl border border-black/5 shadow-inner"
              :style="{ backgroundColor: color.hex }"
            >
              <input
                v-model="color.hex"
                type="color"
                class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
              />
              <div
                class="pointer-events-none absolute inset-0 flex items-center justify-center bg-black/10 text-xs font-bold text-white opacity-0 backdrop-blur-sm transition-opacity group-hover/swatch:opacity-100"
              >
                {{ t('Select', 'اختر') }}
              </div>
            </div>

            <!-- Details Inputs -->
            <div class="space-y-3">
              <input
                v-model="color.name"
                class="saas-input-mini"
                :placeholder="t('Color Name', 'اسم اللون')"
              />
              <div class="flex items-center gap-2">
                <input
                  v-model="color.code"
                  class="saas-input-mini flex-1"
                  :placeholder="t('Pantone #', 'رقم بانتون')"
                />
              </div>
            </div>
          </div>
        </TransitionGroup>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { inject, computed } from 'vue';
  import BaseInput from '../../../../components/UI/BaseInput.vue';
  import SearchableSelect from '../../../../components/UI/SearchableSelect.vue';

  const { form, errors, isRtl, t, fabricList, user } = inject('orderWizardState');

  const userRole = computed(() => user.value?.role);

  const addColor = () => {
    form.colors.push({ hex: '#0ea5e9', name: '', code: '' });
  };

  const removeColor = (idx) => {
    form.colors.splice(idx, 1);
  };
</script>

<style scoped>
  @reference "../../../../../css/app.css";

  .saas-input-mini {
    @apply h-9 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-medium transition-all focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none;
  }

  .list-enter-active,
  .list-leave-active {
    transition: all 0.4s ease;
  }
  .list-enter-from,
  .list-leave-to {
    opacity: 0;
    transform: translateY(20px);
  }
</style>
