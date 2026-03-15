<template>
  <Layout>
    <div class="design-create-view" :class="{ 'rtl': isRtl }">
      <div class="page-header">
        <h1 class="page-title">{{ t('Create Design', 'إنشاء تصميم') }}</h1>
      </div>

      <BaseCard>
        <form @submit.prevent="submitDesign">
           <div class="form-grid">
              <BaseInput v-model="form.name" :label="t('Design Name', 'اسم التصميم')" required />
              
              <div class="field-item">
                 <label class="field-label">{{ t('Category', 'التصنيف') }}</label>
                 <select v-model="form.category_id" class="custom-select" required>
                    <option value="">{{ t('Select a category', 'اختر تصنيفاً') }}</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }} <template v-if="cat.parent">({{ cat.parent.name }})</template></option>
                 </select>
              </div>
           </div>

           <div class="mt-2">
              <label class="field-label">{{ t('Design Image', 'صورة التصميم') }}</label>
              <input type="file" @change="handleFile" accept="image/*" class="custom-file-input" />
           </div>

           <div class="mt-2">
             <BaseTextarea v-model="form.description" :label="t('Description', 'الوصف')" rows="4" />
           </div>

           <div class="mt-3 text-right">
              <BaseButton type="submit" :loading="loading">{{ t('Save Design', 'حفظ التصميم') }}</BaseButton>
           </div>
        </form>
      </BaseCard>
    </div>
  </Layout>
</template>

<script setup>
import Layout from '../../components/Layout.vue';
import BaseCard from '../../components/UI/BaseCard.vue';
import BaseInput from '../../components/UI/BaseInput.vue';
import BaseTextarea from '../../components/UI/BaseTextarea.vue';
import BaseButton from '../../components/UI/BaseButton.vue';
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { hasPermission } from '../../utils/permissions';

const isRtl = computed(() => localStorage.getItem('lang') === 'ar');
const t = (en, ar) => isRtl.value ? ar : en;
const router = useRouter();

const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const can = (perm) => hasPermission(user.value, perm);

const categories = ref([]);
const loading = ref(false);
const file = ref(null);

const form = reactive({
  name: '',
  category_id: '',
  description: ''
});

onMounted(async () => {
   if (!can('designs.create')) return router.push('/dashboard');
   try {
      const res = await axios.get('/api/categories', {
         headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      
      let flat = [];
      if (res.data && Array.isArray(res.data)) {
         res.data.forEach(p => {
            flat.push(p);
            if(p.children && Array.isArray(p.children)) {
               p.children.forEach(c => {
                 flat.push({ ...c, parent: p });
               });
            }
         });
      }
      categories.value = flat; 
   } catch(e) {}
});

const handleFile = (e) => {
   if(e.target.files?.length > 0) {
      file.value = e.target.files[0];
   }
};

const submitDesign = async () => {
   loading.value = true;
   try {
      const formData = new FormData();
      formData.append('name', form.name);
      formData.append('category_id', form.category_id);
      if (form.description) formData.append('description', form.description);
      if (file.value) formData.append('image', file.value);

      await axios.post('/api/designs', formData, {
         headers: { 
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
            'Content-Type': 'multipart/form-data'
         }
      });
      router.push('/designs');
   } catch (e) {
      alert(t('Error creating design', 'خطأ في إنشاء التصميم'));
   } finally {
      loading.value = false;
   }
};

</script>

<style scoped>
.page-header { margin-bottom: 2rem; }
.page-title { font-size: 1.75rem; font-weight: 800; color: #1a3a5f; margin: 0; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.field-label { display: block; font-size: 0.8125rem; font-weight: 700; color: #475569; margin-bottom: 0.5rem; }
.custom-select { width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 10px; background: white; font-size: 0.9375rem; }
.custom-file-input { display: block; width: 100%; padding: 0.5rem; border: 1px dashed #cbd5e1; border-radius: 10px; }
.text-right { text-align: right; }
.mt-2 { margin-top: 1rem; }
.mt-3 { margin-top: 1.5rem; }
.rtl .text-right { text-align: left; }
.rtl .page-header { text-align: right; }
@media (max-width: 768px) {
  .form-grid { grid-template-columns: 1fr; }
}
</style>
