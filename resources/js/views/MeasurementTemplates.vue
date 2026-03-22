<template>
  <Layout>
    <div class="templates-view" :class="{ rtl: isRtl }">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('Measurement Template Builder', 'بناء نماذج القياسات') }}</h1>
          <p class="page-subtitle">
            {{
              t(
                'Define structured size charts for each item type',
                'تحديد جداول مقاسات منظمة لكل نوع قطعة'
              )
            }}
          </p>
        </div>
        <div v-if="templateId" class="header-actions">
          <BaseButton v-if="can('measurements.export')" variant="white" @click="exportTemplate">
            <template #icon-left>
              <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" />
              </svg>
            </template>
            {{ t('Export', 'تصدير') }}
          </BaseButton>
          <BaseButton
            v-if="can('measurements.create')"
            variant="white"
            @click="$refs.importInput.click()"
          >
            <template #icon-left>
              <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12" />
              </svg>
            </template>
            {{ t('Import', 'استيراد') }}
          </BaseButton>
          <input ref="importInput" type="file" hidden accept=".csv" @change="importTemplate" />
        </div>
      </div>

      <div class="main-container">
        <!-- Sidebar: Item Type Selector -->
        <aside class="sidebar">
          <BaseCard class="selector-card" :title="t('Item Type', 'نوع القطعة')">
            <!-- Searchable Category Dropdown -->
            <div class="field-item">
              <label class="field-label">{{
                t('Select Parent Category', 'اختر التصنيف الرئيسي')
              }}</label>
              <div ref="catDropdownRef" class="searchable-dropdown">
                <button
                  class="dropdown-trigger"
                  :class="{ open: catOpen, 'has-value': selectedParentId }"
                  type="button"
                  @click.stop="
                    catOpen = !catOpen;
                    typeOpen = false;
                  "
                >
                  <span class="trigger-text">
                    {{
                      selectedParentId
                        ? parentCategories.find((c) => c.id === selectedParentId)?.name
                        : t('Select Category...', 'اختر التصنيف...')
                    }}
                  </span>
                  <svg
                    class="trigger-chevron"
                    :class="{ rotated: catOpen }"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                  >
                    <path d="m6 9 6 6 6-6" />
                  </svg>
                </button>

                <Transition name="dropdown-pop">
                  <div v-if="catOpen" class="dropdown-panel">
                    <div class="search-box">
                      <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                      >
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                      </svg>
                      <input
                        ref="catSearchInput"
                        v-model="catSearch"
                        type="text"
                        :placeholder="t('Search category...', 'بحث في التصنيفات...')"
                        class="search-input"
                        autofocus
                        @click.stop
                      />
                    </div>
                    <div class="dropdown-list">
                      <button
                        class="dropdown-option"
                        :class="{ active: selectedParentId === null }"
                        type="button"
                        @click="selectCategory(null)"
                      >
                        {{ t('– All Categories', '– كل التصنيفات') }}
                      </button>
                      <button
                        v-for="cat in filteredCategories"
                        :key="cat.id"
                        class="dropdown-option"
                        :class="{ active: selectedParentId === cat.id }"
                        type="button"
                        @click="selectCategory(cat.id)"
                      >
                        {{ cat.name }}
                      </button>
                      <div v-if="filteredCategories.length === 0" class="no-results">
                        {{ t('No results found', 'لا توجد نتائج') }}
                      </div>
                    </div>
                  </div>
                </Transition>
              </div>
            </div>

            <!-- Searchable Item Type Dropdown -->
            <div class="field-item mt-1-5">
              <label class="field-label">{{ t('Select Item Type', 'اختر نوع القطعة') }}</label>
              <div ref="typeDropdownRef" class="searchable-dropdown">
                <button
                  class="dropdown-trigger"
                  :class="{
                    open: typeOpen,
                    'has-value': selectedItemTypeId,
                    disabled: !selectedParentId,
                  }"
                  type="button"
                  :disabled="!selectedParentId"
                  @click.stop="
                    selectedParentId && (typeOpen = !typeOpen);
                    catOpen = false;
                  "
                >
                  <span class="trigger-text">
                    {{
                      selectedItemTypeId
                        ? itemTypes.find((t) => t.id === selectedItemTypeId)?.name
                        : t('Choose Item Type...', 'اختر نوع القطعة...')
                    }}
                  </span>
                  <svg
                    class="trigger-chevron"
                    :class="{ rotated: typeOpen }"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                  >
                    <path d="m6 9 6 6 6-6" />
                  </svg>
                </button>

                <Transition name="dropdown-pop">
                  <div v-if="typeOpen" class="dropdown-panel">
                    <div class="search-box">
                      <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                      >
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                      </svg>
                      <input
                        v-model="typeSearch"
                        type="text"
                        :placeholder="t('Search item type...', 'بحث في أنواع القطع...')"
                        class="search-input"
                        autofocus
                        @click.stop
                      />
                    </div>
                    <div class="dropdown-list">
                      <button
                        v-for="type in filteredItemTypes"
                        :key="type.id"
                        class="dropdown-option"
                        :class="{ active: selectedItemTypeId === type.id }"
                        type="button"
                        @click="selectItemType(type.id)"
                      >
                        {{ type.name }}
                      </button>
                      <div v-if="filteredItemTypes.length === 0" class="no-results">
                        {{ t('No item types found', 'لا توجد أنواع قطع') }}
                      </div>
                    </div>
                  </div>
                </Transition>
              </div>
            </div>

            <div v-if="!selectedItemTypeId" class="info-box mt-2">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="16" x2="12" y2="12" />
                <line x1="12" y1="8" x2="12.01" y2="8" />
              </svg>
              <p>
                {{
                  t(
                    'Select an item type to start building its measurement template.',
                    'اختر نوع القطعة للبدء في بناء نموذج القياس.'
                  )
                }}
              </p>
            </div>
          </BaseCard>

          <!-- Clone Template -->
          <BaseCard
            v-if="selectedItemTypeId"
            class="mt-2"
            :title="t('Clone Template', 'نسخ من قالب')"
          >
            <div class="field-item">
              <label class="field-label">{{
                t('Copy from another template', 'نسخ من قالب آخر')
              }}</label>
              <BaseButton
                v-if="can('measurements.create')"
                variant="white"
                size="sm"
                block
                class="mt-1 justify-between border-neutral-200 font-medium text-neutral-600 shadow-sm hover:text-blue-600"
                @click="cloneModalOpen = true"
              >
                {{ t('Search Templates...', 'البحث في القوالب...') }}
                <template #icon-right>
                  <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </template>
              </BaseButton>
            </div>
          </BaseCard>

          <BaseCard
            v-if="selectedItemTypeId"
            class="preview-card mt-2"
            :title="t('Client Preview', 'معاينة العميل')"
          >
            <p class="preview-note">
              {{ t('How customers see the table:', 'كيف يرى العملاء الجدول:') }}
            </p>
            <div class="preview-area">
              <MeasurementTable :labels="measurements" :model-value="{}" :is-rtl="isRtl" />
            </div>
          </BaseCard>
        </aside>

        <!-- Main Content: Template Builder -->
        <main class="content">
          <BaseCard v-if="selectedItemTypeId" class="builder-card">
            <template #header>
              <div class="card-header-flex items-center">
                <div class="flex items-center gap-3">
                  <h2 class="card-title m-0">{{ t('Measurement Points', 'نقاط القياس') }}</h2>
                  <!-- Status Indicator -->
                  <div class="flex items-center">
                    <span
                      v-if="saving"
                      class="flex items-center gap-1 rounded-md border border-blue-100/50 bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-500"
                    >
                      <svg class="h-3 w-3 animate-spin" viewBox="0 0 24 24">
                        <circle
                          class="opacity-25"
                          cx="12"
                          cy="12"
                          r="10"
                          stroke="currentColor"
                          stroke-width="4"
                          fill="none"
                        ></circle>
                        <path
                          class="opacity-75"
                          fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                      </svg>
                      {{ t('Saving...', 'جاري الحفظ...') }}
                    </span>
                    <span
                      v-else-if="isDirty"
                      class="flex items-center gap-1 rounded-md border border-amber-200/50 bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-600 transition-all"
                    >
                      <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500"></span>
                      {{ t('Unsaved changes', 'تغييرات غير محفوظة') }}
                    </span>
                    <span
                      v-else-if="templateId"
                      class="flex items-center gap-1 rounded-md border border-emerald-200/50 bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-600 transition-all"
                    >
                      <svg
                        width="12"
                        height="12"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="3"
                        stroke-linecap="round"
                      >
                        <path d="M20 6L9 17l-5-5" />
                      </svg>
                      {{ t('Saved', 'تم الحفظ') }}
                    </span>
                  </div>
                </div>
                <div class="header-btns">
                  <BaseButton variant="secondary" size="sm" @click="showBulkAdd = !showBulkAdd">
                    {{ t('Bulk Add', 'إضافة جماعية') }}
                  </BaseButton>
                  <BaseButton variant="white" size="sm" @click="addMeasurement">
                    <template #icon-left>
                      <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="3"
                      >
                        <path d="M12 5v14M5 12h14" />
                      </svg>
                    </template>
                    {{ t('Add Row', 'إضافة سطر') }}
                  </BaseButton>
                </div>
              </div>
            </template>

            <!-- Bulk Add -->
            <div v-if="showBulkAdd" class="bulk-add-area animate-fade-in">
              <p class="bulk-note">
                {{
                  t(
                    'Paste multiple labels (one per line):',
                    'الصق تسميات متعددة (واحدة في كل سطر):'
                  )
                }}
              </p>
              <textarea
                v-model="bulkText"
                class="modern-textarea-sm"
                rows="4"
                :placeholder="t('Length\nChest\nSleeve...', 'الطول\nالصدر\nالأكمام...')"
              ></textarea>
              <div class="mt-1 flex justify-end">
                <BaseButton size="sm" @click="processBulkAdd">{{
                  t('Add to Table', 'إضافة للجدول')
                }}</BaseButton>
              </div>
            </div>

            <div
              v-if="isLoadingTemplate"
              class="flex flex-col items-center justify-center p-12 text-center"
            >
              <svg class="mb-4 h-10 w-10 animate-spin text-blue-500" viewBox="0 0 24 24">
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                  fill="none"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              <h3 class="mb-1 font-bold text-blue-900">
                {{ t('Loading templates...', 'جاري تحميل النموذج...') }}
              </h3>
              <p class="text-sm text-slate-500">
                {{
                  t(
                    'Please wait while we fetch the measurement data.',
                    'يرجى الانتظار بينما نقوم بجلب بيانات القياسات.'
                  )
                }}
              </p>
            </div>

            <div v-else-if="measurements.length === 0" class="empty-state">
              <div class="empty-icon-wrap">
                <svg
                  width="48"
                  height="48"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="#e2e8f0"
                  stroke-width="1.5"
                >
                  <path d="M12 2v20M2 12h20" />
                  <circle cx="12" cy="12" r="10" stroke-dasharray="2 2" />
                </svg>
              </div>
              <h3>{{ t('No measurement points yet', 'لا توجد نقاط قياس بعد') }}</h3>
              <p>
                {{
                  t(
                    'Start by adding measurement points for this item type.',
                    'ابدأ بإضافة نقاط القياس لنوع القطعة هذا.'
                  )
                }}
              </p>
              <BaseButton class="mt-1" @click="addMeasurement">{{
                t('Create First Point', 'إنشاء أول نقطة')
              }}</BaseButton>
            </div>

            <div v-else class="table-responsive">
              <!-- Mobile card view for measurements -->
              <div class="mobile-measurements">
                <div v-for="(m, idx) in measurements" :key="idx" class="measure-card-mobile">
                  <div class="measure-card-row">
                    <span class="measure-card-label">{{ t('Name', 'الاسم') }}</span>
                    <input
                      :ref="(el) => setInputRef(el, idx)"
                      v-model="m.name"
                      type="text"
                      class="table-input"
                      :placeholder="t('Ex: Waist', 'مثال: الوسط')"
                      @keyup.enter="handleEnter(idx)"
                    />
                  </div>
                  <div class="measure-card-row">
                    <span class="measure-card-label">{{ t('Default', 'الافتراضي') }}</span>
                    <input
                      v-model="m.defaultValue"
                      type="number"
                      step="0.1"
                      class="table-input"
                      placeholder="0"
                    />
                  </div>
                  <div class="measure-card-row">
                    <span class="measure-card-label">{{ t('Unit', 'الوحدة') }}</span>
                    <select v-model="m.unit" class="table-select">
                      <option value="cm">cm</option>
                      <option value="inch">inch</option>
                    </select>
                  </div>
                  <button class="delete-btn" @click="removeMeasurement(idx)">
                    <svg
                      width="16"
                      height="16"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <path
                        d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                      />
                    </svg>
                    {{ t('Delete', 'حذف') }}
                  </button>
                </div>
              </div>

              <!-- Desktop table view -->
              <table class="builder-table desktop-only">
                <thead>
                  <tr>
                    <th>{{ t('Measurement Name', 'اسم القياس') }}</th>
                    <th>{{ t('Default Value', 'القيمة الافتراضية') }}</th>
                    <th>{{ t('Unit', 'الوحدة') }}</th>
                    <th class="text-right">{{ t('Actions', 'الإجراءات') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(m, idx) in measurements" :key="idx" class="builder-row">
                    <td>
                      <input
                        :ref="(el) => setInputRef(el, idx)"
                        v-model="m.name"
                        type="text"
                        class="table-input"
                        :placeholder="t('Ex: Waist', 'مثال: الوسط')"
                        @keyup.enter="handleEnter(idx)"
                      />
                    </td>
                    <td>
                      <input
                        v-model="m.defaultValue"
                        type="number"
                        step="0.1"
                        class="table-input width-80"
                        placeholder="0"
                      />
                    </td>
                    <td>
                      <select v-model="m.unit" class="table-select">
                        <option value="cm">cm</option>
                        <option value="inch">inch</option>
                      </select>
                    </td>
                    <td class="text-right">
                      <button
                        class="delete-btn"
                        :title="t('Delete', 'حذف')"
                        @click="removeMeasurement(idx)"
                      >
                        <svg
                          width="18"
                          height="18"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="2"
                        >
                          <path
                            d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                          />
                        </svg>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <template v-if="selectedItemTypeId" #footer>
              <div class="builder-footer">
                <BaseButton
                  v-if="can('measurements.create')"
                  :loading="saving"
                  size="lg"
                  block
                  @click="saveTemplate"
                >
                  {{ t('Save Template', 'حفظ النموذج') }}
                </BaseButton>
              </div>
            </template>
          </BaseCard>

          <div v-else class="welcome-card animate-fade-in">
            <div class="welcome-visual">
              <svg
                width="64"
                height="64"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#0ea5e9"
                stroke-width="1.5"
              >
                <path
                  d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"
                />
                <path d="M2 21c0-3 1.85-5.36 5.08-6C10 14.5 12 15 15 15" />
              </svg>
            </div>
            <h2>{{ t('Select an Item Type to Begin', 'اختر نوع القطعة للبدء') }}</h2>
            <p>
              {{
                t(
                  'You can manage reusable measurement points for different categories of clothing here.',
                  'يمكنك إدارة نقاط القياس القابلة لإعادة الاستخدام لمختلف فئات الملابس هنا.'
                )
              }}
            </p>
          </div>
        </main>
      </div>
    </div>

    <!-- Alert Modal -->
    <AlertModal
      :show="alertModal.show"
      :title="alertModal.title"
      :message="alertModal.message"
      :type="alertModal.type"
      :is-rtl="isRtl"
      @close="alertModal.show = false"
    />

    <!-- Clone Template Modal -->
    <BaseModal
      :show="cloneModalOpen"
      :title="t('Clone Template', 'استنساخ قالب')"
      :subtitle="
        t(
          'Search and copy measurements from an existing template.',
          'البحث ونسخ القياسات من قالب موجود.'
        )
      "
      size="md"
      :is-rtl="isRtl"
      @close="cloneModalOpen = false"
    >
      <div class="space-y-4 p-2">
        <div class="relative">
          <svg
            class="absolute top-1/2 left-3 -translate-y-1/2 text-neutral-400"
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.35-4.35" />
          </svg>
          <input
            v-model="cloneSearch"
            type="text"
            class="w-full rounded-xl border border-neutral-200 bg-neutral-50 py-3 pr-4 pl-10 text-sm transition-all outline-none focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20"
            :placeholder="t('Search categories or item types...', 'ابحث عن تصنيفات أو قطع...')"
            autofocus
          />
        </div>

        <div
          class="custom-scrollbar max-h-[300px] overflow-y-auto rounded-xl border border-neutral-100 bg-neutral-50/50"
        >
          <div
            v-if="filteredCloneTemplates.length === 0"
            class="p-6 text-center text-sm text-neutral-500"
          >
            {{ t('No templates found.', 'لم يتم العثور على قوالب.') }}
          </div>
          <button
            v-for="tpl in filteredCloneTemplates"
            :key="tpl.id"
            class="flex w-full items-center justify-between border-b border-neutral-100 p-4 text-left transition-colors last:border-0 hover:bg-blue-50 hover:text-blue-700"
            @click="executeClone(tpl)"
          >
            <span class="text-[14px] font-semibold text-neutral-800">{{
              tpl.item_type?.name || tpl.category?.name
            }}</span>
            <span class="rounded-full bg-neutral-200 px-2 py-1 text-xs font-medium text-neutral-600"
              >{{ tpl.labels?.length || 0 }} {{ t('points', 'نقاط') }}</span
            >
          </button>
        </div>
      </div>
    </BaseModal>
  </Layout>
</template>

<script setup>
  import { useLang } from '../composables/useLang';
  import Layout from '../components/Layout.vue';
  import BaseCard from '../components/UI/BaseCard.vue';
  import BaseButton from '../components/UI/BaseButton.vue';
  import MeasurementTable from '../components/MeasurementTable.vue';
  import AlertModal from '../components/UI/AlertModal.vue';
  import { hasPermission } from '../utils/permissions';
  import { ref, reactive, onMounted, computed, nextTick, onUnmounted } from 'vue';
  import { useRouter, onBeforeRouteLeave } from 'vue-router';
  import axios from 'axios';

  const router = useRouter();
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
  const can = (perm) => hasPermission(user.value, perm);

  const parentCategories = ref([]);
  const itemTypes = ref([]);
  const selectedParentId = ref(null);
  const selectedItemTypeId = ref(null);
  const measurements = ref([]);
  const originalMeasurements = ref('[]');
  const templateId = ref(null);
  const saving = ref(false);
  const isLoadingTemplate = ref(false);
  const importInput = ref(null);

  // Compute if local state differs from backend truth
  const isDirty = computed(() => {
    return JSON.stringify(measurements.value) !== originalMeasurements.value;
  });

  const allTemplates = ref([]);
  const showBulkAdd = ref(false);
  const bulkText = ref('');
  const inputRefs = ref([]);
  const cloneModalOpen = ref(false);
  const cloneSearch = ref('');

  // Searchable dropdown state
  const catOpen = ref(false);
  const typeOpen = ref(false);
  const catSearch = ref('');
  const typeSearch = ref('');

  const alertModal = reactive({
    show: false,
    title: '',
    message: '',
    type: 'info',
  });

  const showAlert = (message, title = '', type = 'info') => {
    alertModal.message = message;
    alertModal.title = title || (type === 'error' ? t('Error', 'خطأ') : t('Notification', 'تنبيه'));
    alertModal.type = type;
    alertModal.show = true;
  };
  const catDropdownRef = ref(null);
  const typeDropdownRef = ref(null);

  const { isRtl, t } = useLang();

  const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };

  // Filtered dropdown results
  const filteredCategories = computed(() =>
    parentCategories.value.filter((c) =>
      c.name.toLowerCase().includes(catSearch.value.toLowerCase())
    )
  );

  const filteredItemTypes = computed(() =>
    itemTypes.value.filter((t) => t.name.toLowerCase().includes(typeSearch.value.toLowerCase()))
  );

  const filteredCloneTemplates = computed(() =>
    allTemplates.value.filter((tpl) => {
      const name = (tpl.item_type?.name || tpl.category?.name || '').toLowerCase();
      return name.includes(cloneSearch.value.toLowerCase());
    })
  );

  // Select handlers
  const selectCategory = (id) => {
    selectedParentId.value = id;
    catOpen.value = false;
    catSearch.value = '';
    fetchItemTypes();
  };

  const selectItemType = (id) => {
    selectedItemTypeId.value = id;
    typeOpen.value = false;
    typeSearch.value = '';
    fetchTemplate();
  };

  // Close dropdowns when clicking outside
  const handleOutsideClick = (e) => {
    if (catDropdownRef.value && !catDropdownRef.value.contains(e.target)) {
      catOpen.value = false;
      catSearch.value = '';
    }
    if (typeDropdownRef.value && !typeDropdownRef.value.contains(e.target)) {
      typeOpen.value = false;
      typeSearch.value = '';
    }
  };

  const handleBeforeUnload = (e) => {
    if (isDirty.value) {
      e.preventDefault();
      e.returnValue = '';
    }
  };

  onBeforeRouteLeave((to, from, next) => {
    if (isDirty.value) {
      const confirmLeave = window.confirm(
        t(
          'You have unsaved changes. Are you sure you want to leave?',
          'لديك تغييرات غير محفوظة. هل أنت متأكد أنك تريد المغادرة؟'
        )
      );
      if (confirmLeave) {
        next();
      } else {
        next(false);
      }
    } else {
      next();
    }
  });

  const setInputRef = (el, idx) => {
    if (el) inputRefs.value[idx] = el;
  };

  const fetchParentCategories = async () => {
    try {
      const res = await axios.get('/api/categories?status=active', { headers });
      parentCategories.value = (res.data || []).filter((c) => !c.parent_id);
    } catch (e) {
      console.warn(e);
    }
  };

  const fetchAllTemplates = async () => {
    try {
      const res = await axios.get('/api/measurement-templates', { headers });
      allTemplates.value = res.data;
    } catch (e) {
      console.warn(e);
    }
  };

  const fetchItemTypes = async () => {
    selectedItemTypeId.value = null;
    measurements.value = [];
    templateId.value = null;
    if (!selectedParentId.value) {
      itemTypes.value = [];
      return;
    }
    try {
      const res = await axios.get(
        `/api/item-types?category_id=${selectedParentId.value}&status=active`,
        { headers }
      );
      itemTypes.value = res.data;
    } catch (e) {
      console.warn(e);
    }
  };

  const fetchTemplate = async () => {
    if (!selectedItemTypeId.value) return;

    // Clear data immediately to prevent showing stale fields
    measurements.value = [];
    templateId.value = null;
    originalMeasurements.value = '[]';
    isLoadingTemplate.value = true;

    try {
      const res = await axios.get(`/api/measurement-templates/${selectedItemTypeId.value}`, {
        headers,
        params: { _t: Date.now() }, // Prevent aggressive browser XHR caching
      });
      templateId.value = res.data.id;
      measurements.value = (res.data.labels || []).map((l) => {
        if (typeof l === 'string') return { name: l, defaultValue: 0, unit: 'cm' };
        return l;
      });
      originalMeasurements.value = JSON.stringify(measurements.value);
    } catch (e) {
      measurements.value = [];
      templateId.value = null;
      originalMeasurements.value = '[]';
    } finally {
      isLoadingTemplate.value = false;
    }
  };

  onMounted(() => {
    if (!can('measurements.view')) return router.push('/dashboard');
    fetchParentCategories();
    fetchAllTemplates();
    window.addEventListener('click', handleOutsideClick);
    window.addEventListener('beforeunload', handleBeforeUnload);
  });

  onUnmounted(() => {
    window.removeEventListener('click', handleOutsideClick);
    window.removeEventListener('beforeunload', handleBeforeUnload);
  });

  const addMeasurement = async () => {
    measurements.value.push({ name: '', defaultValue: 0, unit: 'cm' });
    await nextTick();
    const lastIdx = measurements.value.length - 1;
    if (inputRefs.value[lastIdx]) inputRefs.value[lastIdx].focus();
  };

  const handleEnter = (idx) => {
    if (idx === measurements.value.length - 1 && measurements.value[idx].name.trim()) {
      addMeasurement();
    }
  };

  const processBulkAdd = () => {
    const lines = bulkText.value.split('\n').filter((l) => l.trim());
    lines.forEach((name) => {
      measurements.value.push({ name: name.trim(), defaultValue: 0, unit: 'cm' });
    });
    bulkText.value = '';
    showBulkAdd.value = false;
  };

  const executeClone = (tpl) => {
    if (tpl) {
      measurements.value = JSON.parse(
        JSON.stringify(
          tpl.labels.map((l) => {
            if (typeof l === 'string') return { name: l, defaultValue: 0, unit: 'cm' };
            return l;
          })
        )
      );
      cloneModalOpen.value = false;
      showAlert(
        t("Labels copied! Don't forget to save.", 'تم نسخ التسميات! لا تنسى الحفظ.'),
        '',
        'info'
      );
    }
  };

  const removeMeasurement = async (idx) => {
    measurements.value.splice(idx, 1);
  };

  const saveTemplate = async (isQuiet = false) => {
    const quietMode = isQuiet === true;
    if (!selectedItemTypeId.value) return;
    if (!quietMode) saving.value = true;
    try {
      const res = await axios.post(
        '/api/measurement-templates',
        {
          category_id: selectedParentId.value,
          item_type_id: selectedItemTypeId.value,
          labels: measurements.value, // Even empty array is sent
        },
        { headers }
      );
      // Ensure we lock in the templateId immediately if it was created
      if (res.data) {
        if (res.data.id) templateId.value = res.data.id;
        if (res.data.labels !== undefined) {
          // Backend is the single source of truth
          measurements.value = (res.data.labels || []).map((l) => {
            if (typeof l === 'string') return { name: l, defaultValue: 0, unit: 'cm' };
            return l;
          });
        }
      }
      originalMeasurements.value = JSON.stringify(measurements.value);
      if (!quietMode)
        showAlert(t('Template saved successfully!', 'تم حفظ النموذج بنجاح!'), '', 'success');
      fetchAllTemplates(); // Background refresh of templates pool
    } catch (e) {
      if (!quietMode) showAlert(t('Error saving template', 'خطأ في حفظ النموذج'), '', 'error');
    } finally {
      if (!quietMode) saving.value = false;
    }
  };

  const exportTemplate = async () => {
    if (!templateId.value) return;
    try {
      const res = await axios.get(`/api/measurement-templates/${templateId.value}/export`, {
        headers,
        responseType: 'blob',
      });
      const url = window.URL.createObjectURL(new Blob([res.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `template_${selectedItemTypeId.value}.csv`);
      document.body.appendChild(link);
      link.click();
      link.remove();
    } catch (e) {
      console.warn(e);
    }
  };

  const importTemplate = async (event) => {
    const file = event.target.files[0];
    if (!file || !templateId.value) return;
    const formData = new FormData();
    formData.append('file', file);
    try {
      await axios.post(`/api/measurement-templates/${templateId.value}/import`, formData, {
        headers: { ...headers, 'Content-Type': 'multipart/form-data' },
      });
      fetchTemplate();
      showAlert(t('Import successful!', 'تم الاستيراد بنجاح!'), '', 'success');
    } catch (e) {
      showAlert(t('Import failed', 'فشل الاستيراد'), '', 'error');
    }
    event.target.value = '';
  };
</script>

<style scoped>
  /* ======== BASE LAYOUT ======== */
  .templates-view {
    padding-bottom: 5rem;
    width: 100%;
  }
  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    gap: 1rem;
    flex-wrap: wrap;
  }
  .page-title {
    font-size: 1.75rem;
    font-weight: 900;
    color: #1e293b;
    margin: 0;
  }
  .page-subtitle {
    color: #64748b;
    font-size: 0.9375rem;
    margin-top: 0.25rem;
    font-weight: 500;
  }
  .header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
  }

  .main-container {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 2rem;
    width: 100%;
  }

  .field-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
  }
  .field-item {
    margin-bottom: 1rem;
  }

  /* ======== SEARCHABLE DROPDOWN ======== */
  .searchable-dropdown {
    position: relative;
    width: 100%;
  }

  .dropdown-trigger {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    padding: 0 1rem;
    height: 48px;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    background: white;
    font-size: 0.9375rem;
    font-weight: 600;
    color: #94a3b8;
    cursor: pointer;
    transition: all 0.2s;
    text-align: left;
    box-sizing: border-box;
  }
  .dropdown-trigger.has-value {
    color: #1e293b;
  }
  .dropdown-trigger.open {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.08);
  }
  .dropdown-trigger.disabled {
    background: #f8fafc;
    cursor: not-allowed;
    opacity: 0.6;
  }
  .dropdown-trigger:hover:not(.disabled) {
    border-color: #cbd5e1;
  }

  .trigger-text {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .trigger-chevron {
    flex-shrink: 0;
    color: #94a3b8;
    transition: transform 0.2s;
  }
  .trigger-chevron.rotated {
    transform: rotate(180deg);
  }

  .dropdown-panel {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    width: 100%;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow:
      0 12px 30px -8px rgba(0, 0, 0, 0.14),
      0 4px 10px -4px rgba(0, 0, 0, 0.08);
    z-index: 999;
    overflow: hidden;
    min-width: 200px;
  }

  .search-box {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    color: #94a3b8;
  }

  .search-input {
    flex: 1;
    border: none !important;
    box-shadow: none !important;
    outline: none !important;
    padding: 0 !important;
    font-size: 0.875rem;
    color: #1e293b;
    background: transparent;
    min-width: 0;
    width: 100%;
  }

  .dropdown-list {
    max-height: 280px;
    overflow-y: auto;
    padding: 0.375rem;
  }

  .dropdown-option {
    width: 100%;
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    border: none;
    background: none;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    color: #475569;
    cursor: pointer;
    text-align: left;
    transition: all 0.15s;
    min-height: 44px;
  }
  .dropdown-option:hover {
    background: #f8fafc;
    color: #0ea5e9;
  }
  .dropdown-option.active {
    background: #f0f9ff;
    color: #0ea5e9;
    font-weight: 700;
  }

  .no-results {
    padding: 1.5rem;
    text-align: center;
    color: #94a3b8;
    font-size: 0.875rem;
  }

  /* Dropdown animation */
  .dropdown-pop-enter-active,
  .dropdown-pop-leave-active {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .dropdown-pop-enter-from,
  .dropdown-pop-leave-to {
    opacity: 0;
    transform: translateY(-8px) scale(0.97);
  }

  /* ======== EXISTING STYLES ======== */
  .custom-select {
    width: 100%;
    height: 44px;
    padding: 0 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: white;
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1e293b;
    transition: all 0.2s;
    cursor: pointer;
  }
  .custom-select:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.05);
    outline: none;
  }
  .s-sm {
    height: 38px;
    font-size: 0.8125rem;
  }

  .info-box {
    background: #f0f9ff;
    padding: 1.25rem;
    border-radius: 14px;
    border: 1px dashed #bae6fd;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
  }
  .info-box p {
    font-size: 0.8125rem;
    color: #0369a1;
    font-weight: 600;
    line-height: 1.5;
    margin: 0;
  }
  .info-box svg {
    color: #0ea5e9;
    flex-shrink: 0;
    margin-top: 1px;
  }

  .preview-note {
    font-size: 0.75rem;
    font-weight: 700;
    color: #64748b;
    margin-top: -0.5rem;
    margin-bottom: 1rem;
  }
  .preview-area {
    width: 100%;
    overflow-x: auto;
  }

  .builder-card {
    min-height: 500px;
    display: flex;
    flex-direction: column;
  }
  .card-header-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    flex-wrap: wrap;
    gap: 0.75rem;
  }
  .card-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: #1e293b;
    margin: 0;
  }
  .header-btns {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
  }

  .bulk-add-area {
    background: #f8fafc;
    padding: 1.25rem;
    border-bottom: 1px solid #f1f5f9;
  }
  .bulk-note {
    font-size: 0.75rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 0.5rem;
  }
  .modern-textarea-sm {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem;
    font-family: inherit;
    font-size: 0.875rem;
    line-height: 1.5;
    resize: vertical;
  }

  .empty-state {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    text-align: center;
  }
  .empty-icon-wrap {
    width: 80px;
    height: 80px;
    background: #f8fafc;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
  }
  .empty-state h3 {
    font-size: 1.25rem;
    font-weight: 800;
    color: #1e293b;
    margin: 0 0 0.5rem;
  }
  .empty-state p {
    color: #64748b;
    max-width: 300px;
    line-height: 1.6;
  }

  /* Desktop Table */
  .desktop-only {
    display: table;
  }
  .mobile-measurements {
    display: none;
  }

  .builder-table {
    width: 100%;
    border-collapse: collapse;
  }
  .builder-table th {
    text-align: left;
    padding: 1rem 1.5rem;
    background: #fafbfc;
    font-size: 0.7rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    border-bottom: 1px solid #f1f5f9;
  }
  .builder-row td {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f8fafc;
    vertical-align: middle;
  }

  .table-input {
    width: 100%;
    border: 1px solid transparent;
    background: transparent;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-weight: 600;
    color: #1e293b;
    transition: all 0.2s;
  }
  .table-input:hover {
    background: #f1f5f9;
    border-color: #e2e8f0;
  }
  .table-input:focus {
    background: white;
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.05);
    outline: none;
  }
  .width-80 {
    max-width: 100px;
  }

  .table-select {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.4rem 0.6rem;
    font-size: 0.8125rem;
    font-weight: 700;
    color: #475569;
    outline: none;
    cursor: pointer;
    height: 36px;
  }

  .delete-btn {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    border: none;
    background: #fef2f2;
    color: #ef4444;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
  }
  .delete-btn:hover {
    background: #ef4444;
    color: white;
    transform: rotate(8deg);
  }

  .builder-footer {
    padding: 1.5rem;
    display: flex;
    justify-content: flex-end;
    border-top: 1px solid #f1f5f9;
    background: #fafbfc;
    border-radius: 0 0 16px 16px;
  }

  .welcome-card {
    min-height: 500px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    background: white;
    border-radius: 12px;
    border: 1px solid #f1f5f9;
    padding: 3rem 1.5rem;
  }
  .welcome-visual {
    margin-bottom: 2rem;
    animation: float 6s ease-in-out infinite;
  }
  .welcome-card h2 {
    font-size: 1.5rem;
    font-weight: 800;
    color: #1e293b;
    margin: 0 0 0.75rem;
  }
  .welcome-card p {
    color: #64748b;
    max-width: 380px;
    line-height: 1.6;
    font-size: 1rem;
  }

  @keyframes float {
    0%,
    100% {
      transform: translateY(0);
    }
    50% {
      transform: translateY(-15px);
    }
  }

  .text-right {
    text-align: right;
  }
  .mt-1-5 {
    margin-top: 1.5rem;
  }
  .mt-1 {
    margin-top: 1rem;
  }
  .mt-2 {
    margin-top: 2rem;
  }
  .flex {
    display: flex;
  }
  .gap-1 {
    gap: 1rem;
  }
  .justify-end {
    justify-content: flex-end;
  }

  /* ======== MOBILE RESPONSIVE ======== */
  @media (max-width: 1024px) {
    .main-container {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 768px) {
    .page-title {
      font-size: 1.375rem;
    }
    .page-header {
      flex-direction: column;
      align-items: flex-start;
    }
    .header-actions {
      width: 100%;
    }
    .header-actions > * {
      flex: 1;
    }

    .dropdown-list {
      max-height: 50vh;
    }

    .builder-footer {
      flex-direction: column;
    }
    .builder-footer button {
      width: 100% !important;
    }

    /* Switch to mobile card view */
    .desktop-only {
      display: none !important;
    }
    .mobile-measurements {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      padding: 1rem;
    }

    .measure-card-mobile {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 1rem;
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .measure-card-row {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .measure-card-label {
      font-size: 0.7rem;
      font-weight: 800;
      color: #94a3b8;
      text-transform: uppercase;
      min-width: 70px;
      flex-shrink: 0;
    }

    .measure-card-row .table-input,
    .measure-card-row .table-select {
      flex: 1;
      min-width: 0;
      border: 1px solid #e2e8f0;
      background: white;
    }

    .measure-card-mobile .delete-btn {
      width: 100%;
      height: 40px;
      border-radius: 8px;
      font-size: 0.8125rem;
      font-weight: 700;
      gap: 0.5rem;
    }

    .header-btns {
      flex-wrap: nowrap;
    }

    .welcome-card {
      min-height: 300px;
      padding: 2rem 1rem;
    }
    .welcome-card h2 {
      font-size: 1.25rem;
    }
  }

  /* RTL */
  .rtl .page-header {
    text-align: right;
  }
  .rtl .builder-table th {
    text-align: right;
  }
  .rtl .builder-footer {
    justify-content: flex-start;
  }
  .rtl .preview-area {
    transform-origin: top right;
  }
  .rtl .text-right {
    text-align: left;
  }
  .rtl .dropdown-trigger {
    text-align: right;
  }
  .rtl .dropdown-option {
    text-align: right;
  }
  .rtl .measure-card-label {
    text-align: right;
  }
</style>
