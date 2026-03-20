<template>
  <Layout>
    <div class="order-create-view" :class="{ 'rtl': isRtl }">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('Create Manufacturing Order', 'إنشاء طلب تصنيع') }}</h1>
          <p class="page-subtitle">{{ t('Submit your technical specifications for production', 'أرسل مواصفاتك الفنية لبدء عملية التصنيع') }}</p>
        </div>
        <BaseButton variant="white" @click="$router.back()">{{ t('Cancel', 'إلغاء') }}</BaseButton>
      </div>

      <form @submit.prevent="submitOrder" class="order-form">
        <!-- Section: Design Information -->
        <BaseCard class="form-section" :title="t('Design Information', 'معلومات التصميم')" :subtitle="t('Product identity and style', 'هوية المنتج والستايل')">
          <div class="form-grid-top">
            <div v-if="user?.role === 'admin'" class="form-grid-2 mb-2">
              <SearchableSelect 
                v-model="form.client_id"
                :options="clientOptions"
                :label="t('Client / Brand', 'العميل / العلامة التجارية')"
                :placeholder="t('Select Client', 'اختر العميل')"
                :isRtl="isRtl"
                required
                :error="errors.client_id"
              />
            </div>

            <div class="form-grid-2">
              <BaseInput 
                v-model="form.title" 
                :label="t('Product Title', 'عنوان المنتج')" 
                required 
                :placeholder="t('Ex: Winter Oversized Hoodie', 'مثال: هودي شتوي واسع')"
                :error="errors.title"
              />
              <BaseInput 
                v-model="form.product_code" 
                :label="t('Product Code', 'كود المنتج')" 
                :placeholder="t('Ex: B1177AX', 'مثال: B1177AX')"
                :error="errors.product_code"
              />
            </div>
            <div class="form-grid-2 mt-2">
              <BaseInput 
                v-model="form.pattern_code" 
                :label="t('Pattern Code', 'كود الباترون')" 
                :placeholder="t('Ex: P-99-A', 'مثال: P-99-A')"
                :error="errors.pattern_code"
              />
              <div class="form-grid-2">
                <BaseInput v-model="form.version_number" type="number" :label="t('Version', 'الإصدار')" :error="errors.version_number" />
                <BaseInput v-model="form.revision_number" type="number" :label="t('Revision', 'المراجعة')" :error="errors.revision_number" />
              </div>
            </div>
            
            <div class="form-grid-2 mt-2">
              <SearchableSelect 
                v-model="form.category_id"
                :options="categoryOptions"
                :label="t('Category', 'التصنيف')"
                :placeholder="t('Select Category', 'اختر التصنيف')"
                :isRtl="isRtl"
                required
                :noResultsMessage="t('No categories available. Please contact admin.', 'لا توجد تصنيفات متاحة. يرجى الاتصال بالمسؤول.')"
                :error="errors.category_id"
              />

              <SearchableSelect 
                v-model="form.item_type_id"
                :options="itemTypeOptions"
                :label="t('Item Type', 'نوع القطعة')"
                :placeholder="t('Select Item Type', 'اختر نوع القطعة')"
                :isRtl="isRtl"
                :disabled="!form.category_id"
                required
                :noResultsMessage="t('No item types found for this category.', 'لم يتم العثور على أنواع قطع لهذا التصنيف.')"
                :error="errors.item_type_id"
              />
            </div>

            <div class="form-grid-2 mt-2">
              <SearchableSelect 
                v-model="form.fit_id"
                :options="fitOptions"
                :label="t('Fit / Cut', 'القصة / Fit')"
                :placeholder="t('Select Fit', 'اختر القصة')"
                :isRtl="isRtl"
                :disabled="!form.item_type_id"
                required
                :noResultsMessage="t('No fit types available for this item.', 'لا توجد قصات متاحة لهذه القطعة.')"
                :error="errors.fit_id"
              />

              <div class="field-item">
                <label class="input-label">{{ t('Season', 'الموسم') }}</label>
                <div class="checkbox-group">
                   <label v-for="sea in seasonOptions" :key="sea.value" class="check-label">
                      <input type="checkbox" :value="sea.value" v-model="form.seasons">
                      {{ sea.label }}
                   </label>
                </div>
                <p v-if="errors.season" class="error-msg">{{ errors.season }}</p>
              </div>
            </div>

            <div class="form-grid-2 mt-2">
              <div class="field-item">
                <SearchableSelect 
                  v-model="form.year"
                  :options="yearOptions"
                  :label="t('Production Year', 'سنة الإنتاج')"
                  :isRtl="isRtl"
                  required
                  :error="errors.year"
                />
              </div>
            </div>
          </div>
        </BaseCard>

        <!-- Section: Fabric Details -->
        <BaseCard class="form-section fabric-section" :title="t('Fabric & Material', 'الأقمشة والمواد')" :subtitle="t('Define the primary material for this design', 'حدد المادة الأساسية لهذا التصميم')">
          <div class="form-grid-3">
             <SearchableSelect 
               v-model="form.fabric_type"
               :options="fabricOptions"
               :label="t('Fabric Type', 'نوع القماش')"
               :placeholder="t('Select or type...', 'اختر أو اكتب...')"
               :isRtl="isRtl"
               allowCustom
               required
               :noResultsMessage="t('No fabrics available in stock.', 'لا توجد أقمشة متاحة في المخزن.')"
             />
             <div class="field-item">
               <BaseInput v-model="form.fabric_weight" type="number" :label="t('Fabric Weight (gsm)', 'وزن القماش (جرام)')" placeholder="320" required />
               <p class="field-help">{{ t('Grams per square meter (GSM)', 'متر لكل جرام / جرام لكل متر مربع') }}</p>
             </div>
             <BaseInput v-model="form.texture" :label="t('Fabric Texture', 'ملمس القماش')" :placeholder="t('Ex: Soft, Ribbed...', 'مثال: ناعم، مضلع...')" />
          </div>
          <div class="form-grid-3 mt-2">
             <BaseInput v-model="form.fabric_composition" :label="t('Composition', 'تكوين القماش')" placeholder="80% Cotton / 20% Poly" />
             <BaseInput v-model="form.yarn_type" :label="t('Yarn Type', 'نوع الخيط')" placeholder="Ring Spun" />
             <BaseInput v-model="form.fabric_structure" :label="t('Fabric Structure', 'هيكل القماش')" placeholder="Single Jersey, Pique..." />
          </div>
          <div v-if="user.role === 'admin'" class="form-grid-2 mt-2 pt-2 border-t">
             <BaseInput v-model="form.fabric_code" :label="t('Internal Fabric Code', 'كود القماش الداخلي')" />
             <BaseInput v-model="form.fabric_supplier" :label="t('Fabric Supplier', 'مورد القماش')" />
          </div>
        </BaseCard>

        <!-- Section: Production Details -->
        <BaseCard class="form-section" :title="t('Sizes & Quantity', 'المقاسات والكمية')" :subtitle="t('Define size distribution and total volume', 'حدد توزيع المقاسات والكمية الإجمالية')">
          <div class="size-management-container">
            <div class="size-header-row mb-05">
               <span class="size-th">{{ t('Size', 'المقاس') }}</span>
               <span class="size-th">{{ t('Quantity', 'الكمية') }}</span>
               <span class="size-th"></span>
            </div>
            
            <div class="size-breakdown-table mb-1">
               <div v-for="(item, idx) in form.size_breakdown" :key="idx" class="size-row">
                  <input type="text" v-model="item.size" :placeholder="t('Ex: XL', 'مثال: XL')" class="mini-input">
                  <input type="number" v-model="item.qty" :placeholder="t('0', '٠')" class="mini-input">
                  <button type="button" class="remove-btn" @click="form.size_breakdown.splice(idx, 1)" v-if="form.size_breakdown.length > 1">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 6 6 18M6 6l12 12"/></svg>
                  </button>
               </div>
               
               <button type="button" class="add-btn-outline mb-1-5" @click="openSizeModal">
                 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M12 5v14M5 12h14"/></svg>
                 {{ t('Add Size', 'إضافة مقاس') }}
               </button>
            </div>

            <div class="total-qty-footer">
               <div class="total-label-box">
                  <span class="total-label">{{ t('Total Quantity (Auto)', 'إجمالي الكمية (تلقائي)') }}</span>
                  <div class="total-value-display">{{ form.quantity }}</div>
               </div>
            </div>
          </div>
          
          <div class="mt-1">
            <label class="input-label">{{ t('Color Palette', 'لوحة الألوان') }}</label>
            <div class="color-grid">
              <TransitionGroup name="list">
                <div v-for="(color, index) in form.colors" :key="index" class="color-card">
                  <div class="color-swatch-row">
                     <input type="color" v-model="color.hex" class="mini-color-picker">
                     <div class="color-swatch-preview" :style="{ background: color.hex }"></div>
                  </div>
                  <input type="text" v-model="color.name" :placeholder="t('Name', 'الاسم')" class="mini-input mb-05">
                  <input type="text" v-model="color.code" :placeholder="t('Pantone', 'بانتون')" class="mini-input">
                  <button type="button" class="remove-color-btn" @click="removeColor(index)" v-if="form.colors.length > 1">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 6 6 18M6 6l12 12"/></svg>
                  </button>
                </div>
              </TransitionGroup>
              <button type="button" class="add-color-btn" @click="addColor">
                 <div class="add-icon-circle">
                   <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M12 5v14M5 12h14"/></svg>
                 </div>
                 <span>{{ t('Add Color', 'إضافة لون') }}</span>
              </button>
            </div>
          </div>
        </BaseCard>

        <!-- Section: Measurements -->
        <BaseCard class="form-section" :title="t('Technical Measurements', 'القياسات الفنية')" :subtitle="t('Specify dimensions in cm', 'يرجى تحديد الأبعاد بالسنتيمتر')">
           <div class="suggestions-area mb-1-5" v-if="templateSuggestions.length">
              <p class="suggestions-title">{{ t('Suggested Templates (From your past orders)', 'قوالب مقترحة (من طلباتك السابقة)') }}</p>
              <div class="suggestions-grid">
                 <button 
                    v-for="sug in templateSuggestions.slice(0, 3)" 
                    :key="sug.id" 
                    type="button"
                    class="suggestion-btn"
                    @click="applyTemplate(sug)"
                 >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    <span>{{ sug.title }} (#{{ sug.order_code }})</span>
                 </button>
              </div>
           </div>

           <MeasurementTable 
            v-model="form.measurements" 
            :labels="measurementLabels"
            :isRtl="isRtl"
           />
           <div class="mt-2" style="max-width: 200px">
              <BaseInput v-model="form.measurement_tolerance" :label="t('Tolerance (±)', 'نسبة السماح (±)')" placeholder="0.5 cm" />
           </div>
        </BaseCard>

        <!-- Section: Branding & Production Notes -->
        <BaseCard class="form-section" :title="t('Branding & Notes', 'البراندنج والملاحظات')">
           <div class="specs-group mb-2">
              <h5 class="specs-title">{{ t('Branding Requirements', 'متطلبات البراندنج') }}</h5>
              <div class="form-grid-2">
                 <BaseInput v-model="form.main_label_type" :label="t('Main Label Type', 'نوع الليبل الرئيسي')" placeholder="Woven, Silicon..." />
                 <BaseInput v-model="form.size_label_type" :label="t('Size Label Type', 'نوع ليبل المقاس')" />
              </div>
              <div class="form-grid-2 mt-1">
                 <BaseInput v-model="form.care_label_type" :label="t('Care Label Instructions', 'تعليمات الغسيل')" placeholder="Wash 30°C, No Bleach..." />
                 <div class="field-item">
                    <label class="input-label">{{ t('Hang Tag Required', 'تاق خارجي مطلوب؟') }}</label>
                    <div class="checkbox-group">
                       <input type="checkbox" v-model="form.hangtag_required">
                       <span>{{ t('Yes', 'نعم') }}</span>
                    </div>
                 </div>
              </div>
           </div>

           <div class="specs-group">
              <h5 class="specs-title">{{ t('Manufacturing Instructions', 'تعليمات التصنيع') }}</h5>
              <textarea 
                v-model="form.notes" 
                class="full-textarea" 
                :placeholder="t('Add instructions for logo placement, embroidery, special packaging...', 'أضف تعليمات لمكان اللوجو، التطريز، التغليف الخاص...')"
              ></textarea>
           </div>
        </BaseCard>

        <!-- Section: Factory Technical Specs (Admin Only) -->
        <BaseCard v-if="user.role === 'admin'" class="form-section admin-specs-card" :title="t('Factory Technical Specs', 'المواصفات الفنية للمصنع')">
           <div class="specs-group mb-2">
              <h5 class="specs-title">{{ t('Trim & Accessories', 'الإضافات والإكسسوارات') }}</h5>
              <div class="form-grid-3">
                 <BaseInput v-model="form.zipper_type" :label="t('Zipper Type', 'نوع السحاب')" />
                 <BaseInput v-model="form.button_type" :label="t('Button Type', 'نوع الزراير')" />
                 <BaseInput v-model="form.drawcord_type" :label="t('Drawcord Type', 'نوع الرباط')" />
              </div>
              <div class="form-grid-2 mt-1">
                 <BaseInput v-model="form.rib_type" :label="t('Rib Type', 'نوع الريب')" />
                 <BaseInput v-model="form.stitching_type" :label="t('Stitching Type', 'نوع الخياطة')" />
              </div>
           </div>

           <div class="specs-group">
              <h5 class="specs-title">{{ t('Packaging & Logistics', 'التغليف واللوجستيات') }}</h5>
              <div class="form-grid-3">
                 <BaseInput v-model="form.packaging_type" :label="t('Package Type', 'نوع التغليف')" />
                 <BaseInput v-model="form.folding_method" :label="t('Folding Method', 'طريقة الطي')" />
                 <div class="field-item">
                    <label class="input-label">{{ t('Barcode Required', 'باركود مطلوب؟') }}</label>
                    <div class="checkbox-group">
                       <input type="checkbox" v-model="form.barcode_required">
                       <span>{{ t('Yes', 'نعم') }}</span>
                    </div>
                 </div>
              </div>
           </div>
        </BaseCard>

        <!-- Section: Design Visuals -->
        <BaseCard class="form-section" :title="t('Upload Design Files', 'رفع ملفات التصميم')" :subtitle="t('Upload sketches, technical drawings, or reference photos', 'ارفع سكتشات، رسومات فنية، أو صور مرجعية')">
           <div class="asset-uploads-grid mb-2">
              <div class="asset-box">
                 <label>{{ t('Front View', 'الواجهة الأمامية') }}</label>
                 <div class="photo-u" @click="$refs.frontInput.click()">
                    <input type="file" hidden ref="frontInput" @change="e => handleAssetUpload(e, 'design_front_image')">
                    <img v-if="previews_special.design_front_image" :src="previews_special.design_front_image">
                    <div v-else class="u-placeholder">+</div>
                 </div>
              </div>
              <div class="asset-box">
                 <label>{{ t('Back View', 'الواجهة الخلفية') }}</label>
                 <div class="photo-u" @click="$refs.backInput.click()">
                    <input type="file" hidden ref="backInput" @change="e => handleAssetUpload(e, 'design_back_image')">
                    <img v-if="previews_special.design_back_image" :src="previews_special.design_back_image">
                    <div v-else class="u-placeholder">+</div>
                 </div>
              </div>
              <div class="asset-box">
                 <label>{{ t('Technical Sketch', 'رسم تقني / سكتش') }}</label>
                 <div class="photo-u" @click="$refs.sketchInput.click()">
                    <input type="file" hidden ref="sketchInput" @change="e => handleAssetUpload(e, 'technical_sketch')">
                    <img v-if="previews_special.technical_sketch" :src="previews_special.technical_sketch">
                    <div v-else class="u-placeholder">+</div>
                 </div>
              </div>
           </div>

           <div 
             class="upload-area"
             @dragover.prevent="dragOver = true"
             @dragleave.prevent="dragOver = false"
             @drop.prevent="handleDrop"
             :class="{ 'dragging': dragOver }"
             @click="$refs.fileInput.click()"
           >
             <input type="file" multiple @change="handleFileUpload" hidden ref="fileInput">
             <div class="upload-v">
               <div class="upload-icon-box">
                 <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
               </div>
               <p class="upload-title"><strong>{{ t('Click to upload', 'اضغط للرفع') }}</strong> {{ t('reference images', 'أو ارفع صور مرجعية') }}</p>
               <p class="upload-subtitle">{{ t('PNG, JPG or PDF up to 10MB', 'PNG، JPG أو PDF بحد أقصى ١٠ ميجابايت') }}</p>
             </div>
           </div>
          
          <div class="image-gallery mt-2" v-if="previews.length">
            <TransitionGroup name="list">
              <div v-for="(src, idx) in previews" :key="idx" class="gallery-item">
                 <img :src="src">
                 <button type="button" class="del-btn" @click.stop="removeImage(idx)">
                   <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 6 6 18M6 6l12 12"/></svg>
                 </button>
              </div>
            </TransitionGroup>
          </div>
        </BaseCard>

        <!-- Section: Notes -->
        <BaseCard class="form-section" :title="t('Additional Notes', 'ملاحظات إضافية')" :subtitle="t('Extra instructions', 'تعليمات إضافية للمصنع')">
           <BaseTextarea 
              v-model="form.notes" 
              :placeholder="t('Describe labels, packaging, or special stitching...', 'صف الملصقات، التغليف، أو خياطة خاصة...')"
              rows="5"
            />
        </BaseCard>

        <div class="form-footer mt-3">
           <BaseButton type="submit" size="lg" :loading="loading" block>
             {{ t('Create Manufacturing Order', 'إنشاء طلب التصنيع') }}
           </BaseButton>
        </div>
      </form>

      <!-- Modals -->
      <AddSizeModal 
        :show="showSizeModal" 
        :isRtl="isRtl" 
        @close="showSizeModal = false" 
        @add="handleAddSize" 
      />

      <AlertModal 
        :show="alertModal.show" 
        :title="alertModal.title" 
        :message="alertModal.message" 
        :type="alertModal.type" 
        :isRtl="isRtl" 
        @close="alertModal.show = false" 
      />
    </div>
  </Layout>
</template>

<script setup>
import { useLang } from '../../composables/useLang';
import Layout from '../../components/Layout.vue';
import BaseCard from '../../components/UI/BaseCard.vue';
import BaseButton from '../../components/UI/BaseButton.vue';
import BaseInput from '../../components/UI/BaseInput.vue';
import BaseTextarea from '../../components/UI/BaseTextarea.vue';
import SearchableSelect from '../../components/UI/SearchableSelect.vue';
import MeasurementTable from '../../components/MeasurementTable.vue';
import AddSizeModal from '../../components/UI/AddSizeModal.vue';
import AlertModal from '../../components/UI/AlertModal.vue';
import { ref, reactive, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import { useRouter, useRoute } from 'vue-router';
import { hasPermission } from '../../utils/permissions';

const router = useRouter();
const route = useRoute();
const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const can = (perm) => hasPermission(user.value, perm);
const categories = ref([]);
const itemTypes = ref([]);
const fits = ref([]);
const fabricList = ref([]);
const templateSuggestions = ref([]);
const measurementLabels = ref([]);
const clients = ref([]);
const loading = ref(false);
const dragOver = ref(false);
const previews = ref([]);
const errors = reactive({});
const previews_special = reactive({
   design_front_image: null,
   design_back_image: null,
   technical_sketch: null
});

// Modal State
const showSizeModal = ref(false);
const alertModal = reactive({
  show: false,
  title: '',
  message: '',
  type: 'info'
});

const showAlert = (message, title = '', type = 'info') => {
  alertModal.message = message;
  alertModal.title = title || (type === 'error' ? t('Error', 'خطأ') : t('Notification', 'تنبيه'));
  alertModal.type = type;
  alertModal.show = true;
};

const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };

onMounted(async () => {
  if (!can('orders.create')) return router.push('/dashboard');
  try {
    const promises = [
      axios.get('/api/categories?status=active', { headers }),
      axios.get('/api/fabrics?status=active', { headers }),
      axios.get('/api/orders?limit=5', { headers })
    ];

    if (user.value?.role === 'admin' || user.value?.role === 'manager') {
      promises.push(axios.get('/api/clients', { headers }));
    }
    
    const [catRes, fabRes, sugRes, clientRes] = await Promise.all(promises);
    
    categories.value = catRes.data || [];
    fabricList.value = fabRes.data || [];
    templateSuggestions.value = sugRes.data?.data || sugRes.data || [];
    if (clientRes) {
      clients.value = clientRes.data || [];
    }
    
    // Auto-fill if reorder_id is present
    if (route.query.reorder_id) {
       const reorderRes = await axios.get(`/api/orders/${route.query.reorder_id}`, { headers });
       if (reorderRes.data) {
          applyTemplate(reorderRes.data);
          showAlert(t('Template applied from past order.', 'تم تطبيق القالب من الطلب السابق.'), '', 'success');
       }
    }
  } catch (e) {
    console.error('Failed to load initial data:', e);
  }
});

const { isRtl, t } = useLang();

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
  fabric_code: '',
  fabric_supplier: '',
  yarn_type: '',
  fabric_structure: '',
  quantity: '',
  sizes: '', // Keeping legacy text field
  size_breakdown: [{ size: '', qty: '' }], // New structured table
  colors: [{ hex: '#0ea5e9', name: isRtl.value ? 'أزرق' : 'Blue', code: '' }],
  measurements: {},
  measurement_tolerance: '',
  notes: '',
  images: [],
  
  // Design Specific Assets
  design_front_image: null,
  design_back_image: null,
  technical_sketch: null,
  reference_images: [],

  // Trim & Accessories
  zipper_type: '',
  button_type: '',
  drawcord_type: '',
  rib_type: '',
  stitching_type: '',

  // Labels
  main_label_type: '',
  care_label_type: '',
  size_label_type: '',
  hangtag_type: '',
  hangtag_required: false,

  // Packaging
  packaging_type: '',
  folding_method: '',
  barcode_required: false
});

const seasonOptions = computed(() => [
  { value: 'spring', label: t('Spring', 'الربيع') },
  { value: 'summer', label: t('Summer', 'الصيف') },
  { value: 'autumn', label: t('Autumn', 'الخريف') },
  { value: 'winter', label: t('Winter', 'الشتاء') }
]);

const yearOptions = computed(() => {
   const current = new Date().getFullYear();
   return [
      { value: current, label: current.toString() },
      { value: current + 1, label: (current + 1).toString() },
      { value: current + 2, label: (current + 2).toString() }
   ];
});

const fabricOptions = computed(() => fabricList.value.map(f => ({ value: f.id, label: `${f.name} - ${f.material_type || 'Unknown'}` })));

const categoryOptions = computed(() => categories.value.map(c => ({ value: c.id, label: c.name })));
const itemTypeOptions = computed(() => itemTypes.value.map(i => ({ value: i.id, label: i.name })));
const fitOptions = computed(() => fits.value.map(f => ({ value: f.id, label: f.name })));
const clientOptions = computed(() => clients.value.map(c => ({ value: c.id, label: c.brand_name || c.name })));

watch(() => form.category_id, async (newVal) => {
  form.item_type_id = '';
  form.fit_id = '';
  itemTypes.value = [];
  fits.value = [];
  if (newVal) {
    const res = await axios.get(`/api/item-types?category_id=${newVal}&status=active`, { headers });
    itemTypes.value = res.data;
  }
});

watch(() => form.item_type_id, async (newVal) => {
  form.fit_id = '';
  fits.value = [];
  if (newVal) {
    const [fitRes, tempRes] = await Promise.all([
      axios.get(`/api/fits?item_type_id=${newVal}&status=active`, { headers }),
      axios.get(`/api/measurement-templates/${newVal}`, { headers }).catch(() => ({ data: null }))
    ]);
    fits.value = fitRes.data;
    if (tempRes.data && tempRes.data.labels) {
      measurementLabels.value = tempRes.data.labels;
    }
  }
});

watch(() => form.size_breakdown, (newVal) => {
  const total = newVal.reduce((acc, curr) => {
    const qty = parseInt(curr.qty) || 0;
    return acc + qty;
  }, 0);
  form.quantity = total;
}, { deep: true });

const applyTemplate = (order) => {
  form.title = order.title || form.title;
  form.product_code = order.product_code || form.product_code;
  form.category_id = order.category_id || form.category_id;
  if (order.client_id && (user.value?.role === 'admin' || user.value?.role === 'manager')) {
     form.client_id = order.client_id;
  }
  
  if (order.season) {
     const seasonsArr = order.season.split(',').map(s => s.trim().toLowerCase());
     form.seasons = seasonOptions.value.filter(opt => seasonsArr.includes(opt.value)).map(opt => opt.value);
  }
  
  const fd = order.fabric_details || order.fabric || {};
  form.fabric_type = fd.type || order.fabric_code || form.fabric_type;
  form.fabric_weight = fd.weight || order.fabric_weight || form.fabric_weight;
  form.texture = fd.texture || form.texture;
  form.fabric_composition = fd.composition || form.fabric_composition;
  form.fabric_code = fd.code || form.fabric_code;
  form.fabric_supplier = fd.supplier || form.fabric_supplier;
  form.yarn_type = fd.yarn_type || form.yarn_type;
  form.fabric_structure = fd.structure || form.fabric_structure;
  
  const pd = order.production_details || {};
  form.item_type_id = pd.item_type_id || form.item_type_id;
  form.fit_id = pd.fit_id || form.fit_id;
  form.measurement_tolerance = pd.tolerance || form.measurement_tolerance;
  
  if (order.colors && Array.isArray(order.colors) && order.colors.length > 0) {
     form.colors = order.colors.map(c => ({
        hex: c.hex || c.color_hex || '#000000',
        name: c.name || c.color_name || '',
        code: c.pantone || c.color_code || ''
     }));
  }

  // Measurements
  let meas = null;
  if (order.measurements) meas = order.measurements;
  else if (pd.measurements) meas = pd.measurements;
  
  if (meas) {
     if (typeof meas === 'string') {
       try { meas = JSON.parse(meas); } catch(e){ meas = {}; }
     }
     form.measurements = { ...meas };
  }
  
  // Trims & Accessories
  const trims = order.trims || pd.trims || {};
  form.zipper_type = pd.zipper_type || order.zipper_type || form.zipper_type;
  form.button_type = pd.button_type || order.button_type || form.button_type;
  form.drawcord_type = pd.cord_type || order.drawcord_type || form.drawcord_type;
  form.rib_type = pd.rib_type || order.rib_type || form.rib_type;
  form.stitching_type = pd.stitch_type || order.stitching_type || form.stitching_type;

  const pkg = order.packaging || order;
  form.main_label_type = pd.main_label_type || pkg.main_label_type || form.main_label_type;
  form.care_label_type = pd.care_label_type || pkg.care_label_type || form.care_label_type;
  form.size_label_type = pd.size_label_type || pkg.size_label_type || form.size_label_type;
  form.packaging_type = pd.packaging_type || pkg.packaging_type || form.packaging_type;
  form.folding_method = pd.folding_method || pkg.folding_method || form.folding_method;
  form.barcode_required = pkg.barcode_required || form.barcode_required;
  form.notes = order.notes || form.notes;
};

const addColor = () => form.colors.push({ hex: '#000000', name: '' });
const removeColor = (idx) => form.colors.splice(idx, 1);

const handleFileUpload = (e) => {
  if (e.target.files?.length) {
    Array.from(e.target.files).forEach(processFile);
  }
};

const handleDrop = (e) => {
  dragOver.value = false;
  Array.from(e.dataTransfer.files).forEach(processFile);
};

const processFile = (file) => {
  form.reference_images.push(file); // Reference images go here now
  const reader = new FileReader();
  reader.onload = (re) => previews.value.push(re.target.result);
  reader.readAsDataURL(file);
};

const handleAssetUpload = (e, key) => {
   const file = e.target.files[0];
   if (!file) return;
   form[key] = file;
   const reader = new FileReader();
   reader.onload = (re) => previews_special[key] = re.target.result;
   reader.readAsDataURL(file);
};

const openSizeModal = () => {
  showSizeModal.value = true;
};

const handleAddSize = (newSize) => {
  // If the first row is empty, replace it
  if (form.size_breakdown.length === 1 && !form.size_breakdown[0].size && !form.size_breakdown[0].qty) {
    form.size_breakdown[0] = { size: newSize.name, qty: newSize.quantity };
  } else {
    form.size_breakdown.push({ size: newSize.name, qty: newSize.quantity });
  }
};

const removeImage = (idx) => {
  form.reference_images.splice(idx, 1);
  previews.value.splice(idx, 1);
};

const submitOrder = async () => {
  if (!form.title || !form.seasons.length) {
    showAlert(t('Please fill all required fields, including Season.', 'الرجاء ملء جميع الحقول المطلوبة، بما في ذلك الموسم.'), '', 'error');
    return;
  }
  
  if (form.quantity <= 0) {
    showAlert(t('Please provide quantities in the Size Breakdown table.', 'يرجى تقديم الكميات في جدول توزيع المقاسات.'), '', 'error');
    return;
  }
  loading.value = true;
  Object.keys(errors).forEach(key => delete errors[key]);
  
  const formData = new FormData();
  if (user.value?.role === 'admin' || user.value?.role === 'manager') {
      if (form.client_id) formData.append('client_id', form.client_id);
  }
  formData.append('title', form.title);
  if (form.category_id) formData.append('category_id', form.category_id);
  formData.append('season', form.seasons.join(', '));
  formData.append('year', form.year);
  formData.append('notes', form.notes || '');
  
  if (user.value?.role === 'client') {
     formData.append('status', 'submitted');
  }

  // Identification
  if (form.product_code) formData.append('product_code', form.product_code);
  if (form.pattern_code) formData.append('pattern_code', form.pattern_code);
  formData.append('version_number', form.version_number);
  formData.append('revision_number', form.revision_number);
  
  formData.append('fabric_details', JSON.stringify({
    type: form.fabric_type,
    weight: form.fabric_weight,
    texture: form.texture,
    composition: form.fabric_composition,
    code: form.fabric_code,
    supplier: form.fabric_supplier,
    yarn_type: form.yarn_type,
    structure: form.fabric_structure
  }));

  formData.append('production_details', JSON.stringify({
    quantity: form.quantity,
    sizes_text: form.size_breakdown.filter(i => i.size).map(i => i.size).join(', '),
    sizes: form.size_breakdown.reduce((acc, curr) => {
       if (curr.size) acc[curr.size] = curr.qty;
       return acc;
    }, {}),
    item_type_id: form.item_type_id,
    fit_id: form.fit_id,
    tolerance: form.measurement_tolerance
  }));
  
  const sizesText = form.size_breakdown.filter(i => i.size).map(i => i.size).join(', ');
  if (sizesText) formData.append('sizes', sizesText);

  formData.append('colors', JSON.stringify(form.colors));
  formData.append('measurements', JSON.stringify(form.measurements));
  
  // Trim, Labels & Packaging
  if (form.zipper_type) formData.append('zipper_type', form.zipper_type);
  if (form.button_type) formData.append('button_type', form.button_type);
  if (form.drawcord_type) formData.append('drawcord_type', form.drawcord_type);
  if (form.rib_type) formData.append('rib_type', form.rib_type);
  if (form.stitching_type) formData.append('stitching_type', form.stitching_type);
  if (form.main_label_type) formData.append('main_label_type', form.main_label_type);
  if (form.care_label_type) formData.append('care_label_type', form.care_label_type);
  if (form.size_label_type) formData.append('size_label_type', form.size_label_type);
  if (form.hangtag_type) formData.append('hangtag_type', form.hangtag_type);
  if (form.packaging_type) formData.append('packaging_type', form.packaging_type);
  if (form.folding_method) formData.append('folding_method', form.folding_method);
  formData.append('barcode_required', form.barcode_required ? 1 : 0);

  // File Uploads
  if (form.design_front_image) formData.append('design_front_image', form.design_front_image);
  if (form.design_back_image) formData.append('design_back_image', form.design_back_image);
  if (form.technical_sketch) formData.append('technical_sketch', form.technical_sketch);
  
  form.reference_images.forEach((img, i) => {
     if (img instanceof File) {
        formData.append(`reference_images[${i}]`, img);
     }
  });

  try {
    await axios.post('/api/orders', formData, {
      headers: { 
        'Content-Type': 'multipart/form-data',
        Authorization: `Bearer ${localStorage.getItem('auth_token')}`
      }
    });
    router.push('/orders');
  } catch (e) {
    if (e.response?.status === 422) {
       const backendErrors = e.response.data.errors;
       if (backendErrors) {
          Object.assign(errors, Object.fromEntries(
             Object.entries(backendErrors).map(([key, value]) => [key, Array.isArray(value) ? value[0] : value])
          ));
          showAlert(t('Please correct the highlighted errors.', 'الرجاء تصحيح الأخطاء الموضحة.'), '', 'error');
       }
    } else {
       showAlert(t('Error submitting order: ' + (e.response?.data?.message || e.message), 'فشل إرسال الطلب: ' + (e.response?.data?.message || e.message)), '', 'error');
    }
    console.error('Order submission error:', e.response?.data || e);
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
/* Styles remain same as before, only form-grid-top updated */
.order-create-view { max-width: 800px; margin: 0 auto; padding-bottom: 5rem; overflow-x: hidden; box-sizing: border-box; width: 100%; }
.page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; }
.page-title { font-size: 1.75rem; font-weight: 800; color: #1a3a5f; margin: 0; }
.page-subtitle { color: #64748b; font-size: 0.9375rem; margin-top: 0.25rem; }

.color-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 1rem; margin-top: 0.5rem;}
.color-card { background: white; border: 1px solid #e2e8f0; padding: 0.6rem; border-radius: 14px; position: relative; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.03); }
.color-card:hover { border-color: #0ea5e9; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }

.color-swatch-row { display: flex; gap: 0.5rem; height: 36px; margin-bottom: 0.5rem; }
.mini-color-picker { width: 36px; height: 36px; padding: 0; border: none; background: none; cursor: pointer; flex-shrink: 0; }
.color-swatch-preview { flex: 1; border-radius: 8px; border: 1px solid rgba(0,0,0,0.05); }
.mini-input { width: 100%; border: 1px solid #f1f5f9; border-radius: 8px; padding: 6px 10px; font-size: 0.8125rem; background: #f8fafc; color: #1a3a5f; transition: all 0.2s; }
.mini-input:focus { outline: none; border-color: #0ea5e9; background: white; }

.remove-color-btn { position: absolute; top: -6px; right: -6px; background: #ef4444; color: white; border: none; width: 22px; height: 22px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3); transition: all 0.2s; }
.add-color-btn { height: 100%; min-height: 105px; border: 2px dashed #e2e8f0; border-radius: 14px; background: white; color: #64748b; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.75rem; transition: all 0.2s; font-weight: 600; font-size: 0.875rem; }
.add-icon-circle { width: 32px; height: 32px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }

.upload-area { border: 2px dashed #e2e8f0; border-radius: 20px; padding: 3.5rem 2rem; text-align: center; cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); background: #fafbfc; }
.upload-icon-box { color: #0ea5e9; margin-bottom: 1.25rem; background: white; width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
.upload-title { font-size: 1rem; color: #0f172a; margin: 0; }
.upload-subtitle { font-size: 0.8125rem; color: #64748b; margin-top: 0.5rem !important; }

.image-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 1.25rem; }
.gallery-item { position: relative; aspect-ratio: 1; border-radius: 14px; overflow: hidden; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transition: all 0.3s; }
.gallery-item img { width: 100%; height: 100%; object-fit: cover; }
.del-btn { position: absolute; top: 6px; right: 6px; border: none; background: rgba(255,255,255,0.9); color: #ef4444; border-radius: 50%; width: 26px; height: 26px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.2s; }

.form-footer { display: flex; justify-content: center; padding-top: 2rem; }
.form-section { margin-bottom: 16px; padding: 12px; border-bottom: 1px solid #f1f5f9; }
.form-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2.5rem; }
.form-grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 2.5rem; }
.form-grid-top { display: flex; flex-direction: column; gap: 1rem; }
.field-full { margin-bottom: 1rem; }
.form-section * { box-sizing: border-box; max-width: 100%; }

.checkbox-group { display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 0.75rem; }
.check-label { display: flex; align-items: center; gap: 0.625rem; cursor: pointer; color: #1a3a5f; font-weight: 500; font-size: 0.9375rem; }
.check-label input { width: 1.2rem; height: 1.2rem; accent-color: #0ea5e9; cursor: pointer; }

.suggestions-area { background: #f0f9ff; border: 1px dashed #bae6fd; border-radius: 12px; padding: 1rem 1.25rem; }
.suggestions-title { font-size: 0.8125rem; font-weight: 800; color: #0284c7; text-transform: uppercase; margin-bottom: 0.75rem; }
.suggestions-grid { display: flex; gap: 0.75rem; flex-wrap: wrap; }
.suggestion-btn { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.875rem; background: white; border: 1px solid #bae6fd; border-radius: 8px; color: #0369a1; font-weight: 600; font-size: 0.8125rem; cursor: pointer; transition: all 0.2s; }

.rtl .page-header { text-align: right; }
.rtl .upload-area { direction: rtl; }
.rtl .remove-color-btn { right: auto; left: -6px; }
.rtl .del-btn { right: auto; left: 6px; }

/* Tech Pack Extension Styles */
.mb-05 { margin-bottom: 0.5rem; }
.size-row { display: grid; grid-template-columns: 1fr 1fr 40px; gap: 0.75rem; margin-bottom: 0.75rem; align-items: center; }
.remove-btn { background: #fee2e2; color: #ef4444; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.remove-btn:hover { background: #fecaca; }
.add-btn-outline { width: 100%; padding: 0.75rem; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; color: #64748b; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s; }
.add-btn-outline:hover { background: #f1f5f9; border-color: #0ea5e9; color: #0ea5e9; }

.specs-group { background: #f8fafc; padding: 1.25rem; border-radius: 12px; border: 1px solid #f1f5f9; }
.specs-title { margin: 0 0 1rem; font-size: 0.875rem; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 0.025em; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem; }

.asset-uploads-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
.asset-box label { display: block; font-size: 0.8125rem; font-weight: 600; color: #64748b; margin-bottom: 0.75rem; }
.photo-u { width: 100%; aspect-ratio: 1; background: #f1f5f9; border-radius: 16px; border: 2px dashed #cbd5e1; cursor: pointer; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.photo-u:hover { border-color: #0ea5e9; background: #f0f9ff; }
.photo-u img { width: 100%; height: 100%; object-fit: cover; }
.u-placeholder { font-size: 2rem; color: #94a3b8; font-weight: 300; }

.size-management-container { background: white; padding: 2rem; border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
.size-header-row { display: grid; grid-template-columns: 1fr 1fr 40px; gap: 0.75rem; padding: 0 0.5rem; }
.size-th { font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
.mb-1-5 { margin-bottom: 1.5rem; }

.total-qty-footer { display: flex; justify-content: flex-end; padding-top: 1.5rem; border-top: 2px solid #f1f5f9; }
.total-label-box { display: flex; align-items: center; gap: 1.5rem; background: #f8fafc; padding: 0.75rem 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0; }
.total-label { font-weight: 700; color: #1e293b; font-size: 0.9375rem; }
.total-value-display { font-size: 1.25rem; font-weight: 900; color: #0ea5e9; min-width: 60px; text-align: center; }

.pt-2 { padding-top: 0.5rem; }
.border-t { border-top: 1px solid #f1f5f9; }
.full-textarea { width: 100%; min-height: 120px; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 12px; background: #fcfdfe; color: #1e293b; font-size: 0.9375rem; font-family: inherit; resize: vertical; transition: all 0.2s; }
.full-textarea:focus { border-color: #0ea5e9; outline: none; background: white; box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.05); }

.error-msg { color: #ef4444; font-size: 0.75rem; font-weight: 600; margin-top: 4px; }
.mb-2 { margin-bottom: 2rem; }
.mt-1 { margin-top: 1rem; }
.mt-2 { margin-top: 2rem; }
.mt-3 { margin-top: 3rem; }
.mb-1 { margin-bottom: 1rem; }

/* ===== Mobile Responsive ===== */
@media (max-width: 768px) {
  .order-create-view {
    max-width: 100%;
    padding: 0 0.5rem 4rem;
  }

  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .page-title {
    font-size: 1.35rem;
  }

  .page-subtitle {
    font-size: 0.8125rem;
  }

  .form-section {
    margin-bottom: 2rem;
    padding: 16px;
  }

  .form-grid-2,
  .form-grid-3 {
    grid-template-columns: 1fr;
    gap: 0;
  }

  .form-grid-2 .form-grid-2 {
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  .asset-uploads-grid {
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  .asset-uploads-grid .asset-box:last-child {
    grid-column: span 2;
  }

  .photo-u {
    aspect-ratio: 4 / 3;
  }

  .upload-area {
    padding: 2rem 1rem;
    border-radius: 14px;
  }

  .upload-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    margin-bottom: 1rem;
  }

  .upload-title {
    font-size: 0.875rem;
  }

  .upload-subtitle {
    font-size: 0.75rem;
  }

  .color-grid {
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    gap: 0.75rem;
  }

  .add-color-btn {
    min-height: 90px;
  }

  .size-management-container {
    padding: 1rem;
    border-radius: 14px;
  }

  .size-row {
    grid-template-columns: 1fr 1fr 36px;
    gap: 0.5rem;
  }

  .size-header-row {
    grid-template-columns: 1fr 1fr 36px;
    gap: 0.5rem;
  }

  .total-qty-footer {
    justify-content: center;
  }

  .total-label-box {
    gap: 1rem;
    padding: 0.625rem 1rem;
    width: 100%;
    justify-content: space-between;
  }

  .specs-group {
    padding: 1rem;
  }

  .checkbox-group {
    gap: 1rem;
  }

  .suggestions-grid {
    flex-direction: column;
  }

  .suggestion-btn {
    width: 100%;
    justify-content: center;
  }

  .image-gallery {
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    gap: 0.75rem;
  }

  .form-footer {
    padding-top: 1rem;
  }
}

@media (max-width: 400px) {
  .order-create-view {
    padding: 0 0.25rem 3rem;
  }

  .page-title {
    font-size: 1.2rem;
  }

  .form-section {
    padding: 12px;
    margin-bottom: 1.5rem;
  }

  .form-grid-2 .form-grid-2 {
    grid-template-columns: 1fr;
    gap: 0;
  }

  .asset-uploads-grid {
    grid-template-columns: 1fr;
  }

  .asset-uploads-grid .asset-box:last-child {
    grid-column: span 1;
  }

  .color-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .mini-input {
    font-size: 0.75rem;
    padding: 5px 8px;
  }
}
</style>
