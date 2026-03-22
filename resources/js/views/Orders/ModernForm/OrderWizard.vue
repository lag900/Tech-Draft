<template>
  <Layout>
    <div
      class="wizard-page-wrapper min-h-screen bg-blue-50/20 pb-52 lg:pb-32"
      :class="{ 'is-rtl': isRtl }"
    >
      <!-- Page Header & Stepper Section -->
      <div class="mb-12 border-b border-blue-100 bg-white px-4 py-12 md:px-8">
        <div class="mx-auto max-w-5xl">
          <!-- Main Title Row -->
          <div class="mb-12 flex flex-col justify-between gap-6 md:flex-row md:items-center">
            <div>
              <h1 class="mb-2 text-3xl font-bold tracking-tight text-blue-950">
                {{ t('Create Manufacturing Order', 'إنشاء طلب تصنيع جديد') }}
              </h1>
              <div class="flex items-center gap-2">
                <span
                  class="rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-[10px] font-bold tracking-wider text-blue-700 uppercase"
                  >{{ t('PLM Wizard v2.0', 'نظام إدارة الإنتاج الجيل الثاني') }}</span
                >
                <span class="text-xs font-medium text-blue-500">{{
                  t('Guided technical specification workflow', 'سير عمل موجه للمواصفات الفنية')
                }}</span>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <button
                type="button"
                class="rounded-lg border border-blue-100 bg-white px-7 py-4 text-sm font-semibold text-blue-600 shadow-[0_2px_8px_-2px_rgba(37,99,235,0.1)] transition-all hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 focus:ring-2 focus:ring-blue-600/20 focus:outline-none active:scale-95"
                @click="handleCancel"
              >
                {{ t('Exit Draft', 'خروج') }}
              </button>
            </div>
          </div>

          <!-- Dedicated Stepper Container -->
          <div
            class="rounded-2xl border border-blue-100 bg-white p-6 shadow-[0_4px_20px_-4px_rgba(37,99,235,0.1)] md:p-8"
          >
            <WizardProgress
              :steps="wizardSteps"
              :current-step="currentStep"
              :is-rtl="isRtl"
              @go-to-step="goToStep"
            />
          </div>
        </div>
      </div>

      <!-- Content Area -->
      <div class="mx-auto max-w-5xl px-4 md:px-8">
        <!-- Active Step Render -->
        <div class="wizard-step-container pb-12">
          <component
            :is="activeStepComponent"
            @upload="handleAssetUpload"
            @upload-gallery="handleGalleryUpload"
            @remove-gallery-image="removeGalleryImage"
            @apply-template="handleApplyTemplate"
            @request-next="nextStep"
          />
        </div>
      </div>

      <!-- Navigation Footer -->
      <WizardFooter
        :current-step="currentStep"
        :total-steps="wizardSteps.length"
        :is-rtl="isRtl"
        :t="t"
        :loading="loading"
        @prev="prevStep"
        @next="nextStep"
        @submit="submitOrder"
      />

      <!-- Global Modals -->
      <AlertModal
        :show="alertModal.show"
        :title="alertModal.title"
        :message="alertModal.message"
        :type="alertModal.type"
        :is-rtl="isRtl"
        @close="alertModal.show = false"
      />
    </div>
  </Layout>
</template>

<script setup>
  import {
    ref,
    reactive,
    computed,
    onMounted,
    onUnmounted,
    watch,
    markRaw,
    provide,
    nextTick,
  } from 'vue';
  import { useRouter } from 'vue-router';
  import axios from 'axios';
  import { useLang } from '../../../composables/useLang';
  import Layout from '../../../components/Layout.vue';
  import AlertModal from '../../../components/UI/AlertModal.vue';

  // Components
  import WizardProgress from './Components/WizardProgress.vue';
  import WizardFooter from './Components/WizardFooter.vue';
  import InformationStep from './Steps/InformationStep.vue';
  import MaterialsStep from './Steps/MaterialsStep.vue';
  import MeasurementsAllocationStep from './Steps/MeasurementsAllocationStep.vue';
  import SpecificationsStep from './Steps/SpecificationsStep.vue';
  import FilesStep from './Steps/FilesStep.vue';
  import ReviewStep from './Steps/ReviewStep.vue';

  const router = useRouter();
  const { isRtl, t } = useLang();
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
  const scrolled = ref(false);

  // Wizard State
  const currentStep = ref(1);
  const loading = ref(false);
  const categories = ref([]);
  const itemTypes = ref([]);
  const fits = ref([]);
  const fabricList = ref([]);
  const clients = ref([]);
  const templateSuggestions = ref([]);
  const measurementLabels = ref([]);

  const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };

  // Form State
  const form = reactive({
    client_id: '',
    title: '',
    product_code: '',
    pattern_code: '',
    version_number: 1,
    revision_number: 0,
    category_id: '',
    item_type_id: '',
    fit_id: '',
    seasons: [],
    year: new Date().getFullYear(),
    fabric_type: '',
    fabric_weight: '',
    texture: '',
    fabric_composition: '',
    yarn_type: '',
    fabric_structure: '',
    fabric_code: '',
    fabric_supplier: '',
    quantity: 0,
    allocation: {
      S: 0,
      M: 0,
      L: 0,
      XL: 0,
    },
    colors: [{ hex: '#000000', name: 'Black', code: 'P19-4008' }],
    measurements: [],
    measurement_tolerance: '± 0.5 cm',
    measurement_notes: '',
    notes: '',

    // Files
    design_front_image: null,
    design_back_image: null,
    technical_sketch: null,
    reference_images: [],

    // Specs
    main_label_type: '',
    size_label_type: '',
    care_label_type: '',
    hangtag_required: false,
    zipper_type: '',
    button_type: '',
    drawcord_type: '',
    rib_type: '',
    stitching_type: '',
    packaging_type: '',
    folding_method: '',
    barcode_required: false,
  });

  const previews = reactive({
    design_front_image: null,
    design_back_image: null,
    technical_sketch: null,
  });
  const galleryPreviews = ref([]);

  const errors = reactive({});
  const alertModal = reactive({ show: false, title: '', message: '', type: 'info' });

  const wizardSteps = [
    { labelEn: 'Information', labelAr: 'المعلومات', component: markRaw(InformationStep) },
    { labelEn: 'Materials', labelAr: 'الخامات', component: markRaw(MaterialsStep) },
    {
      labelEn: 'Measurements & Allocation',
      labelAr: 'القياسات والكميات',
      component: markRaw(MeasurementsAllocationStep),
    },
    { labelEn: 'Details', labelAr: 'المواصفات', component: markRaw(SpecificationsStep) },
    { labelEn: 'Assets', labelAr: 'الصور والملفات', component: markRaw(FilesStep) },
    { labelEn: 'Overview', labelAr: 'نظرة عامة', component: markRaw(ReviewStep) },
  ];

  const activeStepComponent = computed(() => wizardSteps[currentStep.value - 1].component);

  provide('orderWizardState', {
    form,
    errors,
    isRtl,
    t,
    categories,
    itemTypes,
    fits,
    fabricList,
    clients,
    user,
    measurementLabels,
    templateSuggestions,
    previews,
    galleryPreviews,
  });

  // Fetch Initial Data
  onMounted(async () => {
    try {
      const draftParams = localStorage.getItem('order_wizard_draft');
      if (draftParams) {
        try {
          isRestoring = true;
          const parsed = JSON.parse(draftParams);
          Object.keys(parsed).forEach((k) => {
            if (parsed[k] !== undefined) form[k] = parsed[k];
          });
          const savedStep = localStorage.getItem('order_wizard_step');
          if (savedStep) currentStep.value = parseInt(savedStep, 10) || 1;
        } catch (e) {
          console.error('Error parsing draft', e);
        }
      }

      const promises = [
        axios.get('/api/categories?status=active', { headers }),
        axios.get('/api/fabrics?status=active', { headers }),
        axios.get('/api/orders?limit=3', { headers }),
      ];
      if (user.value?.role === 'admin' || user.value?.role === 'manager') {
        promises.push(axios.get('/api/clients', { headers }));
      }
      const [catRes, fabRes, sugRes, clientRes] = await Promise.all(promises);
      categories.value = (catRes.data || []).map((c) => ({ id: c.id, name: c.name }));
      fabricList.value = (fabRes.data || []).map((f) => ({ id: f.id, name: f.name }));
      templateSuggestions.value = sugRes.data?.data || sugRes.data || [];
      if (clientRes)
        clients.value = (clientRes.data || []).map((c) => ({
          id: c.id,
          name: c.brand_name || c.name,
        }));

      // Re-trigger dependent fetches if draft was loaded
      if (form.category_id) {
        const res = await axios.get(
          `/api/item-types?category_id=${form.category_id}&status=active`,
          { headers }
        );
        itemTypes.value = (res.data || []).map((i) => ({ id: i.id, name: i.name }));
      }
      if (form.item_type_id) {
        const [fitRes, tempRes] = await Promise.all([
          axios.get(`/api/fits?item_type_id=${form.item_type_id}&status=active`, { headers }),
          axios
            .get(`/api/measurement-templates/${form.item_type_id}?_t=${Date.now()}`, { headers })
            .catch(() => ({ data: null })),
        ]);
        fits.value = (fitRes.data || []).map((f) => ({ id: f.id, name: f.name }));
        if (tempRes.data && tempRes.data.labels) {
          measurementLabels.value = tempRes.data.labels;
        }
      }
      isRestoring = false;
    } catch (e) {
      isRestoring = false;
      console.error('Failed to load initial data:', e);
    }
    window.addEventListener('scroll', handleScroll);
  });

  onUnmounted(() => window.removeEventListener('scroll', handleScroll));

  const handleScroll = () => {
    scrolled.value = window.scrollY > 20;
  };

  let isRestoring = false;

  // Watchers for cascading dropdowns
  watch(
    () => form.category_id,
    async (newVal, oldVal) => {
      if (!isRestoring && oldVal) {
        form.item_type_id = '';
        form.fit_id = '';
        itemTypes.value = [];
        form.measurements = [];
        measurementLabels.value = [];
        form.allocation = { S: 0, M: 0, L: 0, XL: 0 };
      }
      if (newVal) {
        const res = await axios.get(`/api/item-types?category_id=${newVal}&status=active`, {
          headers,
        });
        itemTypes.value = (res.data || []).map((i) => ({ id: i.id, name: i.name }));
      }
    }
  );

  watch(
    () => form.item_type_id,
    async (newVal, oldVal) => {
      if (!isRestoring && oldVal) {
        form.fit_id = '';
        fits.value = [];
        form.measurements = [];
        measurementLabels.value = [];
        form.allocation = { S: 0, M: 0, L: 0, XL: 0 };
      }
      if (newVal) {
        const [fitRes, tempRes] = await Promise.all([
          axios.get(`/api/fits?item_type_id=${newVal}&status=active`, { headers }),
          axios
            .get(`/api/measurement-templates/${newVal}?_t=${Date.now()}`, { headers })
            .catch(() => ({ data: null })),
        ]);
        fits.value = (fitRes.data || []).map((f) => ({ id: f.id, name: f.name }));
        if (tempRes.data && tempRes.data.labels) {
          measurementLabels.value = tempRes.data.labels;
        } else {
          measurementLabels.value = [];
        }
      }
    }
  );

  let debounceTimer = null;
  watch(
    () => form,
    () => {
      if (isRestoring) return;
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        const draft = { ...form };
        delete draft.design_front_image;
        delete draft.design_back_image;
        delete draft.technical_sketch;
        delete draft.reference_images;
        localStorage.setItem('order_wizard_draft', JSON.stringify(draft));
        localStorage.setItem('order_wizard_step', currentStep.value.toString());
      }, 1000);
    },
    { deep: true }
  );

  // Navigation Logic
  // Navigation Logic
  const scrollToError = async () => {
    await nextTick();
    setTimeout(() => {
      const errorEl = document.querySelector(
        '.has-error, .border-red-300, .text-red-500, .bg-red-50, .border-red-500'
      );
      if (errorEl) {
        // Prioritize scrolling to the whole field block rather than just the tiny text
        const container =
          errorEl.closest('.card') ||
          errorEl.closest('.mb-6') ||
          errorEl.closest('.step-content > div') ||
          errorEl;
        const topPos = container.getBoundingClientRect().top + window.pageYOffset - 120; // 120px offset for sticky headers and spacing
        window.scrollTo({ top: topPos, behavior: 'smooth' });
      }
    }, 100);
  };

  const nextStep = async () => {
    if (validateStep(currentStep.value)) {
      currentStep.value++;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
      await scrollToError();
    }
  };

  const prevStep = () => {
    currentStep.value--;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const goToStep = async (step) => {
    if (step < currentStep.value) {
      currentStep.value = step;
      window.scrollTo({ top: 0, behavior: 'smooth' });
      return;
    }

    // Validate all steps from current up to target
    for (let i = currentStep.value; i < step; i++) {
      if (!validateStep(i)) {
        currentStep.value = i;
        await scrollToError();
        return;
      }
    }

    currentStep.value = step;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const handleCancel = () => router.back();

  const validateStep = (step) => {
    let isValid = true;
    if (step === 1) {
      delete errors.title;
      delete errors.category_id;
      delete errors.item_type_id;
      delete errors.fit_id;
      delete errors.seasons;
      delete errors.client_id;
      if (!form.title) {
        errors.title = t('Title is required', 'العنوان مطلوب');
        isValid = false;
      }
      if (!form.category_id) {
        errors.category_id = t('Category is required', 'التصنيف مطلوب');
        isValid = false;
      }
      if (!form.item_type_id) {
        errors.item_type_id = t('Specific Item Type is required', 'نوع القطعة مطلوب');
        isValid = false;
      }
      if (!form.fit_id) {
        errors.fit_id = t('Fit Target Profile is required', 'هيكل القصة مطلوب');
        isValid = false;
      }
      if (!form.seasons.length) {
        errors.seasons = t('Please select at least one season', 'يرجى اختيار موسم واحد على الأقل');
        isValid = false;
      }
      if (user.value?.role === 'admin' && !form.client_id) {
        errors.client_id = t('Client is required', 'العميل مطلوب');
        isValid = false;
      }
    } else if (step === 2) {
      delete errors.fabric_type;
      delete errors.fabric_weight;
      if (!form.fabric_type) {
        errors.fabric_type = t('Fabric Type is required', 'نوع القماش مطلوب');
        isValid = false;
      }
      if (!form.fabric_weight) {
        errors.fabric_weight = t('Fabric Weight is required', 'وزن القماش مطلوب');
        isValid = false;
      }
    } else if (step === 3) {
      delete errors.quantity;
      const totalQty = Object.values(form.allocation).reduce(
        (acc, curr) => acc + (Number(curr) || 0),
        0
      );
      if (totalQty <= 0) {
        errors.quantity = t('Quantity must be greater than 0', 'الكمية يجب أن تكون أكبر من 0');
        showAlert(t('Please specify production volume.', 'يرجى تحديد حجم الإنتاج.'), '', 'warning');
        isValid = false;
      }
    } else if (step === 5) {
      delete errors.design_front_image;
      if (!form.design_front_image) {
        errors.design_front_image = t(
          'Front design image is required',
          'صورة التصميم الأمامية مطلوبة'
        );
        showAlert(
          t(
            'Front design image is required. Please upload it to proceed.',
            'الصورة الأمامية للتصميم مطلوبة. يرجى رفعها للمتابعة.'
          ),
          '',
          'warning'
        );
        isValid = false;
      }
    }
    return isValid;
  };

  // Asset Handling
  const handleAssetUpload = ({ file, type }) => {
    form[type] = file;
    const reader = new FileReader();
    reader.onload = (e) => (previews[type] = e.target.result);
    reader.readAsDataURL(file);
  };

  const handleGalleryUpload = (files) => {
    files.forEach((file) => {
      form.reference_images.push(file);
      const reader = new FileReader();
      reader.onload = (e) => galleryPreviews.value.push(e.target.result);
      reader.readAsDataURL(file);
    });
  };

  const removeGalleryImage = (idx) => {
    form.reference_images.splice(idx, 1);
    galleryPreviews.value.splice(idx, 1);
  };

  const handleApplyTemplate = (order) => {
    // Simulating the applyTemplate logic from OrderCreate
    form.title = order.title || form.title;
    form.category_id = order.category_id || form.category_id;
    // Deep clone or merge objects as needed
    showAlert(t('Template applied successfully!', 'تم تطبيق القالب بنجاح!'), '', 'success');
  };

  const showAlert = (message, title = '', type = 'info') => {
    alertModal.message = message;
    alertModal.title = title || (type === 'error' ? t('Error', 'خطأ') : t('Success', 'تم بنجاح'));
    alertModal.type = type;
    alertModal.show = true;
  };

  // Submission logic
  const submitOrder = async () => {
    // Validate ALL steps before submit
    for (let i = 1; i <= 5; i++) {
      if (!validateStep(i)) {
        currentStep.value = i;
        await scrollToError();
        return;
      }
    }

    loading.value = true;
    const formData = new FormData();

    // Basic Info
    formData.append('title', form.title);
    formData.append('category_id', form.category_id);
    formData.append('season', form.seasons.join(', '));
    formData.append('year', form.year);
    formData.append('notes', form.notes || '');
    if (form.client_id) formData.append('client_id', form.client_id);
    if (user.value?.role === 'client') formData.append('status', 'submitted');

    // Technical
    formData.append('product_code', form.product_code || '');
    formData.append('version_number', form.version_number);
    formData.append('revision_number', form.revision_number);

    formData.append(
      'fabric_details',
      JSON.stringify({
        type: form.fabric_type,
        weight: form.fabric_weight,
        texture: form.texture,
        composition: form.fabric_composition,
        code: form.fabric_code,
        supplier: form.fabric_supplier,
        yarn_type: form.yarn_type,
        structure: form.fabric_structure,
      })
    );

    const totalQuantity = Object.values(form.allocation).reduce(
      (acc, curr) => acc + (Number(curr) || 0),
      0
    );
    formData.append(
      'production_details',
      JSON.stringify({
        quantity: totalQuantity,
        sizes: form.allocation,
        item_type_id: form.item_type_id,
        fit_id: form.fit_id,
        tolerance: form.measurement_tolerance,
      })
    );

    formData.append('colors', JSON.stringify(form.colors));

    const formattedMeasurements = form.measurements.map((m) => {
      return {
        point: m.name,
        value: m.values[Object.keys(form.allocation)[0]] || 0,
        grading: m.values,
        tolerance: form.measurement_tolerance,
      };
    });
    formData.append('measurements', JSON.stringify(formattedMeasurements));

    // Extra Fields
    formData.append('zipper_type', form.zipper_type || '');
    formData.append('button_type', form.button_type || '');
    formData.append('main_label_type', form.main_label_type || '');
    formData.append('hangtag_required', form.hangtag_required ? 1 : 0);

    // Files
    if (form.design_front_image) formData.append('design_front_image', form.design_front_image);
    if (form.design_back_image) formData.append('design_back_image', form.design_back_image);
    if (form.technical_sketch) formData.append('technical_sketch', form.technical_sketch);

    form.reference_images.forEach((img, i) => {
      formData.append(`reference_images[${i}]`, img);
    });

    try {
      await axios.post('/api/orders', formData, {
        headers: { ...headers, 'Content-Type': 'multipart/form-data' },
      });
      localStorage.removeItem('order_wizard_draft');
      localStorage.removeItem('order_wizard_step');
      showAlert(t('Order created successfully!', 'تم إنشاء الطلب بنجاح!'), '', 'success');
      setTimeout(() => router.push('/orders'), 1500);
    } catch (e) {
      if (e.response?.status === 422) {
        Object.assign(errors, e.response.data.errors);
        showAlert(
          t(
            'Validation failed. Please check your data.',
            'فشل التحقق من البيانات. يرجى مراجعة الحقول.'
          ),
          '',
          'error'
        );
        // Go to first step with errors
        if (errors.title || errors.category_id || errors.seasons) currentStep.value = 1;
      } else {
        showAlert(
          t('Submission failed: ' + (e.response?.data?.message || e.message), 'فشل الإرسال'),
          '',
          'error'
        );
      }
    } finally {
      loading.value = false;
    }
  };
</script>

<style scoped>
  @reference "../../../../css/app.css";

  .wizard-page-wrapper {
    overflow-x: hidden;
  }
  .is-rtl {
    direction: rtl;
  }

  /* Custom fade-in for steps */
  .animate-fade-in {
    animation: step-fade-in 0.4s cubic-bezier(0.2, 1, 0.2, 1) forwards;
  }

  @keyframes step-fade-in {
    from {
      opacity: 0;
      transform: translateY(15px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .card {
    @apply rounded-2xl border border-blue-100 bg-white p-8 shadow-[0_4px_20px_-4px_rgba(37,99,235,0.05)] transition-all duration-200 hover:shadow-[0_8px_30px_-4px_rgba(37,99,235,0.1)];
  }
</style>
