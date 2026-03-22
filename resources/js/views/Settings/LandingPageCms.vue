<template>
  <Layout>
    <!-- Main Wrapper -->
    <div
      class="mx-auto min-h-screen max-w-5xl pb-32 font-sans"
      :class="[isRtl ? 'rtl text-right' : 'ltr text-left']"
    >
      <!-- Sticky Header Actions (SaaS style) -->
      <div
        class="sticky top-0 z-50 flex items-center justify-between border-b border-slate-200 bg-white/80 px-6 py-4 shadow-sm backdrop-blur-md"
      >
        <div>
          <h1 class="text-xl font-bold tracking-tight text-slate-900">
            {{ t('Landing Page CMS', 'إدارة الصفحة الرئيسية') }}
          </h1>
          <p class="mt-1 text-sm text-slate-500">
            {{
              t(
                'Manage the structure and content of your welcome page dynamically.',
                'أدر محتوى وتفاصيل واجهة المستخدم الرئيسية ديناميكياً.'
              )
            }}
          </p>
        </div>
        <div class="flex items-center gap-4">
          <!-- Toast Notification -->
          <transition name="toast-fade">
            <div
              v-if="toastMessage"
              class="flex items-center gap-2 rounded-lg border border-emerald-100 bg-emerald-50 px-4 py-2 text-emerald-700 shadow-sm"
            >
              <svg
                viewBox="0 0 24 24"
                width="16"
                height="16"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="text-emerald-600"
              >
                <polyline points="20 6 9 17 4 12" />
              </svg>
              <span class="text-sm font-semibold">{{ toastMessage }}</span>
            </div>
          </transition>

          <button
            :disabled="isSaving"
            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
            @click="save"
          >
            <svg
              v-if="isSaving"
              class="mr-2 -ml-1 h-4 w-4 animate-spin text-white"
              viewBox="0 0 24 24"
              fill="none"
            >
              <circle
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
                class="opacity-25"
              ></circle>
              <path
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                class="opacity-75"
              ></path>
            </svg>
            {{ isSaving ? t('Saving...', 'جاري الحفظ...') : t('Save Changes', 'حفظ التغييرات') }}
          </button>
        </div>
      </div>

      <!-- Loading Skeleton -->
      <div v-if="isLoading" class="animate-pulse space-y-6 p-8">
        <div class="h-48 w-full rounded-2xl bg-slate-200"></div>
        <div class="h-32 w-full rounded-2xl bg-slate-200"></div>
      </div>

      <!-- Layout Container -->
      <div v-else class="space-y-8 p-6">
        <!-- Hero Section -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
            <h3 class="text-base font-semibold text-slate-800">
              {{ t('Hero Section', 'القسم الرئيسي') }}
            </h3>
            <p class="mt-1 text-xs text-slate-500">
              {{
                t(
                  'Main welcome text and subtitles prominently displayed at the top.',
                  'النصوص الترحيبية الأساسية المعروضة في أعلى الصفحة.'
                )
              }}
            </p>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
              <!-- English Column -->
              <div class="space-y-5">
                <div class="mb-2 flex items-center gap-2 border-b border-slate-100 pb-2">
                  <span class="text-xs font-bold tracking-wider text-slate-400 uppercase"
                    >English Content</span
                  >
                </div>
                <div class="space-y-1">
                  <label class="block text-sm font-medium text-slate-600">{{
                    t('Hero Title', 'العنوان الرئيسي')
                  }}</label>
                  <input
                    v-model="form.hero_title_en"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm transition-shadow focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    placeholder="e.g. Clothing Manufacturing Platform"
                  />
                </div>
                <div class="space-y-1">
                  <label class="block text-sm font-medium text-slate-600">{{
                    t('Hero Subtitle', 'العنوان الفرعي')
                  }}</label>
                  <textarea
                    v-model="form.hero_subtitle_en"
                    rows="3"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm transition-shadow focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    placeholder="Brief description..."
                  ></textarea>
                </div>
              </div>

              <!-- Arabic Column -->
              <div class="space-y-5">
                <div class="mb-2 flex items-center gap-2 border-b border-slate-100 pb-2">
                  <span class="text-xs font-bold tracking-wider text-slate-400 uppercase"
                    >المحتوى العربي</span
                  >
                </div>
                <div class="space-y-1">
                  <label class="block text-sm font-medium text-slate-600">{{
                    t('Hero Title', 'العنوان الرئيسي')
                  }}</label>
                  <input
                    v-model="form.hero_title_ar"
                    type="text"
                    dir="rtl"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm transition-shadow focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    placeholder="مثال: منصة تصنيع الملابس الأولى"
                  />
                </div>
                <div class="space-y-1">
                  <label class="block text-sm font-medium text-slate-600">{{
                    t('Hero Subtitle', 'العنوان الفرعي')
                  }}</label>
                  <textarea
                    v-model="form.hero_subtitle_ar"
                    dir="rtl"
                    rows="3"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm transition-shadow focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    placeholder="وصف موجز..."
                  ></textarea>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Features Section -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div
            class="relative z-10 flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-6 py-4"
          >
            <div>
              <h3 class="text-base font-semibold text-slate-800">
                {{ t('Features Outline', 'مميزات المنصة') }}
              </h3>
              <p class="mt-1 text-xs text-slate-500">
                {{
                  t(
                    'Key benefits and features shown in a grid.',
                    'أهم المزايا والخصائص المعروضة في شبكة.'
                  )
                }}
              </p>
            </div>
            <button
              class="inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-sm font-semibold text-emerald-600 shadow-sm transition-colors hover:bg-emerald-100 hover:text-emerald-700"
              @click="addFeature"
            >
              <svg
                class="mr-1.5 -ml-0.5 h-4 w-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
              </svg>
              {{ t('Add Feature', 'إضافة ميزة') }}
            </button>
          </div>

          <div class="space-y-4 p-6">
            <div
              v-for="(feature, index) in form.features"
              :key="`feat-${index}`"
              class="group relative rounded-xl border border-slate-200 bg-white shadow-sm transition-all hover:border-slate-300"
              @dragover.prevent
              @drop="dropFeature($event, index)"
            >
              <!-- Collapsible Header -->
              <div
                class="flex cursor-pointer items-center justify-between p-4"
                @click="toggleFeature(index)"
              >
                <div class="flex flex-1 items-center gap-4">
                  <!-- Drag Handle -->
                  <div
                    class="cursor-grab p-1 text-slate-300 hover:text-slate-500 active:cursor-grabbing"
                    draggable="true"
                    @dragstart="dragStartFeature($event, index)"
                  >
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                      <path
                        d="M8 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm0 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm0 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm12-12a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm0 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm0 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"
                      />
                    </svg>
                  </div>

                  <div class="flex items-center gap-3">
                    <div
                      class="flex h-8 w-8 items-center justify-center rounded bg-blue-50 text-blue-500"
                    >
                      <svg
                        v-if="feature.icon === 'create_order'"
                        viewBox="0 0 24 24"
                        width="16"
                        height="16"
                      >
                        <path
                          d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
                          stroke="currentColor"
                          fill="none"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      <!-- Fallback icon -->
                      <span v-else class="text-xs font-bold">{{ index + 1 }}</span>
                    </div>
                    <span class="max-w-sm truncate text-sm font-semibold text-slate-800">{{
                      feature.title_en || feature.title_ar || t('New Feature', 'ميزة جديدة')
                    }}</span>
                  </div>
                </div>

                <!-- Header Actions -->
                <div class="flex items-center gap-1">
                  <button
                    class="tooltip rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-500"
                    :title="t('Remove', 'حذف')"
                    @click.stop="removeFeature(index)"
                  >
                    <svg
                      viewBox="0 0 24 24"
                      width="18"
                      height="18"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <path
                        d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"
                      />
                    </svg>
                  </button>
                  <div
                    class="transform p-1.5 text-slate-400 transition-transform duration-200"
                    :class="{ 'rotate-180': expandedFeature === index }"
                  >
                    <svg
                      viewBox="0 0 24 24"
                      width="20"
                      height="20"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <polyline points="6 9 12 15 18 9" />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Collapsible Body -->
              <transition name="slide-down">
                <div
                  v-show="expandedFeature === index"
                  class="rounded-b-xl border-t border-slate-100 bg-slate-50/30 p-6"
                >
                  <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Identifier -->
                    <div class="col-span-full space-y-1">
                      <label
                        class="block text-xs font-medium tracking-wide text-slate-500 uppercase"
                        >{{ t('SVG Icon Identifier', 'معرف الأيقونة') }}</label
                      >
                      <select
                        v-model="feature.icon"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm transition-shadow focus:border-blue-500 focus:ring-blue-500 sm:text-sm md:w-1/2"
                      >
                        <option value="create_order">Create Order</option>
                        <option value="fabric">Fabric</option>
                        <option value="track">Track</option>
                        <option value="chat">Chat</option>
                      </select>
                    </div>

                    <div class="space-y-4">
                      <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-500 uppercase">{{
                          t('Title (English)', 'العنوان (إنجليزي)')
                        }}</label>
                        <input
                          v-model="feature.title_en"
                          type="text"
                          class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        />
                      </div>
                      <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-500 uppercase">{{
                          t('Description (English)', 'الوصف (إنجليزي)')
                        }}</label>
                        <textarea
                          v-model="feature.desc_en"
                          rows="3"
                          class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        ></textarea>
                      </div>
                    </div>

                    <div class="space-y-4">
                      <div class="space-y-1">
                        <label
                          class="block text-right text-xs font-semibold text-slate-500 uppercase"
                          >{{ t('Title (Arabic)', 'العنوان (عربي)') }}</label
                        >
                        <input
                          v-model="feature.title_ar"
                          type="text"
                          dir="rtl"
                          class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        />
                      </div>
                      <div class="space-y-1">
                        <label
                          class="block text-right text-xs font-semibold text-slate-500 uppercase"
                          >{{ t('Description (Arabic)', 'الوصف (عربي)') }}</label
                        >
                        <textarea
                          v-model="feature.desc_ar"
                          dir="rtl"
                          rows="3"
                          class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        ></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </transition>
            </div>

            <div
              v-if="form.features.length === 0"
              class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 py-8 text-center"
            >
              <span class="text-sm text-slate-500">{{
                t('No features added yet.', 'لم تتم إضافة أي مميزات.')
              }}</span>
            </div>
          </div>
        </section>

        <!-- Steps Section -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div
            class="relative z-10 flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-6 py-4"
          >
            <div>
              <h3 class="text-base font-semibold text-slate-800">
                {{ t('How It Works Steps', 'خطوات عمل المنصة') }}
              </h3>
              <p class="mt-1 text-xs text-slate-500">
                {{ t('Step-by-step instructional guide.', 'دليل تعليمي خطوة بخطوة.') }}
              </p>
            </div>
            <button
              class="inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-sm font-semibold text-emerald-600 shadow-sm transition-colors hover:bg-emerald-100 hover:text-emerald-700"
              @click="addStep"
            >
              <svg
                class="mr-1.5 -ml-0.5 h-4 w-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
              </svg>
              {{ t('Add Step', 'إضافة خطوة') }}
            </button>
          </div>

          <div class="space-y-4 p-6">
            <div
              v-for="(step, index) in form.steps"
              :key="`step-${index}`"
              class="group relative rounded-xl border border-slate-200 bg-white shadow-sm transition-all hover:border-slate-300"
              @dragover.prevent
              @drop="dropStep($event, index)"
            >
              <div
                class="flex cursor-pointer items-center justify-between p-4"
                @click="toggleStep(index)"
              >
                <div class="flex flex-1 items-center gap-4">
                  <div
                    class="cursor-grab p-1 text-slate-300 hover:text-slate-500 active:cursor-grabbing"
                    draggable="true"
                    @dragstart="dragStartStep($event, index)"
                  >
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                      <path
                        d="M8 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm0 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm0 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm12-12a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm0 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm0 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"
                      />
                    </svg>
                  </div>

                  <div class="flex items-center gap-3">
                    <div
                      class="flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-xs font-bold text-slate-500"
                    >
                      {{ index + 1 }}
                    </div>
                    <span class="max-w-sm truncate text-sm font-semibold text-slate-800">{{
                      step.title_en || step.title_ar || t('New Step', 'خطوة جديدة')
                    }}</span>
                  </div>
                </div>

                <div class="flex items-center gap-1">
                  <button
                    class="tooltip rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-500"
                    :title="t('Remove', 'حذف')"
                    @click.stop="removeStep(index)"
                  >
                    <svg
                      viewBox="0 0 24 24"
                      width="18"
                      height="18"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <path
                        d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"
                      />
                    </svg>
                  </button>
                  <div
                    class="transform p-1.5 text-slate-400 transition-transform duration-200"
                    :class="{ 'rotate-180': expandedStep === index }"
                  >
                    <svg
                      viewBox="0 0 24 24"
                      width="20"
                      height="20"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <polyline points="6 9 12 15 18 9" />
                    </svg>
                  </div>
                </div>
              </div>

              <transition name="slide-down">
                <div
                  v-show="expandedStep === index"
                  class="rounded-b-xl border-t border-slate-100 bg-slate-50/30 p-6"
                >
                  <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-4">
                      <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-500 uppercase">{{
                          t('Title (English)', 'العنوان (إنجليزي)')
                        }}</label>
                        <input
                          v-model="step.title_en"
                          type="text"
                          class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        />
                      </div>
                      <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-500 uppercase">{{
                          t('Description (English)', 'الوصف (إنجليزي)')
                        }}</label>
                        <textarea
                          v-model="step.desc_en"
                          rows="3"
                          class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        ></textarea>
                      </div>
                    </div>

                    <div class="space-y-4">
                      <div class="space-y-1">
                        <label
                          class="block text-right text-xs font-semibold text-slate-500 uppercase"
                          >{{ t('Title (Arabic)', 'العنوان (عربي)') }}</label
                        >
                        <input
                          v-model="step.title_ar"
                          type="text"
                          dir="rtl"
                          class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        />
                      </div>
                      <div class="space-y-1">
                        <label
                          class="block text-right text-xs font-semibold text-slate-500 uppercase"
                          >{{ t('Description (Arabic)', 'الوصف (عربي)') }}</label
                        >
                        <textarea
                          v-model="step.desc_ar"
                          dir="rtl"
                          rows="3"
                          class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        ></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </transition>
            </div>

            <div
              v-if="form.steps.length === 0"
              class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 py-8 text-center"
            >
              <span class="text-sm text-slate-500">{{
                t('No steps added yet.', 'لم تتم إضافة أي خطوات.')
              }}</span>
            </div>
          </div>
        </section>

        <!-- CTA Section -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
            <h3 class="text-base font-semibold text-slate-800">
              {{ t('Call to Action Section', 'قسم الإجراء النهائي') }}
            </h3>
            <p class="mt-1 text-xs text-slate-500">
              {{
                t(
                  'The final message prompting users to act.',
                  'الرسالة الختامية التي تدعو المستخدمين للتفاعل.'
                )
              }}
            </p>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
              <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-600">{{
                  t('CTA Title (English)', 'عنوان الإجراء (إنجليزي)')
                }}</label>
                <input
                  v-model="form.cta_title_en"
                  type="text"
                  class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm transition-shadow focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                />
              </div>
              <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-600">{{
                  t('CTA Title (Arabic)', 'عنوان الإجراء (عربي)')
                }}</label>
                <input
                  v-model="form.cta_title_ar"
                  type="text"
                  dir="rtl"
                  class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm transition-shadow focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                />
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </Layout>
</template>

<script setup>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  import { useLang } from '../../composables/useLang';
  import Layout from '../../components/Layout.vue';

  const { t, isRtl } = useLang();
  const isLoading = ref(true);
  const isSaving = ref(false);

  const toastMessage = ref('');
  const expandedFeature = ref(null);
  const expandedStep = ref(null);

  const form = ref({
    hero_title_en: '',
    hero_title_ar: '',
    hero_subtitle_en: '',
    hero_subtitle_ar: '',
    features: [],
    steps: [],
    cta_title_en: '',
    cta_title_ar: '',
  });

  // Load the settings
  onMounted(async () => {
    try {
      const res = await axios.get('/api/landing-page');
      form.value = { ...res.data };
    } catch (e) {
      console.error(e);
    } finally {
      isLoading.value = false;
    }
  });

  // Toast Notification Logic
  const showToast = (message) => {
    toastMessage.value = message;
    setTimeout(() => {
      toastMessage.value = '';
    }, 4000);
  };

  // Save function
  const save = async () => {
    isSaving.value = true;
    try {
      const token = localStorage.getItem('auth_token');
      await axios.put('/api/landing-page', form.value, {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${token}`,
        },
      });
      showToast(t('Changes saved successfully', 'تم حفظ التعديلات بنجاح'));

      // Collapse all accordions purely for a clean state
      expandedFeature.value = null;
      expandedStep.value = null;
    } catch (err) {
      console.error(err);
      const errText = err.response ? JSON.stringify(err.response.data) : err.message;
      alert(`Error: ${err.response?.status}. Details: ${errText}`);
    } finally {
      isSaving.value = false;
    }
  };

  // Features Logic
  const addFeature = () => {
    form.value.features.push({
      icon: 'create_order',
      title_en: '',
      title_ar: '',
      desc_en: '',
      desc_ar: '',
    });
    expandedFeature.value = form.value.features.length - 1;
  };

  const removeFeature = (index) => {
    form.value.features.splice(index, 1);
    if (expandedFeature.value === index) expandedFeature.value = null;
  };

  const toggleFeature = (index) => {
    expandedFeature.value = expandedFeature.value === index ? null : index;
  };

  // Features Drag and Drop Logic
  const dragStartFeature = (e, index) => {
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('featureIndex', index);
  };

  const dropFeature = (e, dropIndex) => {
    const dragIndex = e.dataTransfer.getData('featureIndex');
    if (dragIndex === '' || dragIndex == dropIndex) return;

    const temp = form.value.features[dragIndex];
    form.value.features.splice(dragIndex, 1);
    form.value.features.splice(dropIndex, 0, temp);

    // Reset expanded to avoid visual confusion after drag
    expandedFeature.value = null;
  };

  // Steps Logic
  const addStep = () => {
    form.value.steps.push({ title_en: '', title_ar: '', desc_en: '', desc_ar: '' });
    expandedStep.value = form.value.steps.length - 1;
  };

  const removeStep = (index) => {
    form.value.steps.splice(index, 1);
    if (expandedStep.value === index) expandedStep.value = null;
  };

  const toggleStep = (index) => {
    expandedStep.value = expandedStep.value === index ? null : index;
  };

  // Steps Drag and Drop Logic
  const dragStartStep = (e, index) => {
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('stepIndex', index);
  };

  const dropStep = (e, dropIndex) => {
    const dragIndex = e.dataTransfer.getData('stepIndex');
    if (dragIndex === '' || dragIndex == dropIndex) return;

    const temp = form.value.steps[dragIndex];
    form.value.steps.splice(dragIndex, 1);
    form.value.steps.splice(dropIndex, 0, temp);

    // Reset expanded
    expandedStep.value = null;
  };
</script>

<style scoped>
  /* RTL / LTR specific logic is mostly handled via CSS in this clean SaaS-driven structure */
  .slide-down-enter-active,
  .slide-down-leave-active {
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    transform-origin: top;
  }
  .slide-down-enter-from,
  .slide-down-leave-to {
    opacity: 0;
    transform: translateY(-10px);
  }

  .toast-fade-enter-active,
  .toast-fade-leave-active {
    transition:
      opacity 0.3s ease,
      transform 0.3s ease;
  }
  .toast-fade-enter-from,
  .toast-fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
  }
</style>
