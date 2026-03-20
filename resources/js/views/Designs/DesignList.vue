<template>
  <Layout>
    <div class="designs-view" :class="{ 'rtl': isRtl }">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('Designs', 'التصاميم') }}</h1>
          <p class="page-subtitle">{{ t('Manage product designs and technical specifications', 'إدارة تصاميم المنتجات والمواصفات الفنية') }}</p>
        </div>
        <router-link to="/designs/create" v-if="can('designs.create')">
          <BaseButton>
            <template #icon-left>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
            </template>
            {{ t('Create Design', 'إنشاء تصميم') }}
          </BaseButton>
        </router-link>
      </div>

      <div v-if="loading" class="text-center p-4">{{ t('Loading...', 'جاري التحميل...') }}</div>
      <div v-else-if="!designs?.length" class="text-center p-4">
         {{ t('No designs found.', 'لا توجد تصاميم.') }}
      </div>
      <div v-else class="designs-grid">
        <BaseCard hoverable v-for="design in designs" :key="design.id" no-padding class="design-card">
          <div class="design-preview">
            <template v-if="design.image_path">
               <img :src="`/storage/${design.image_path}`" :alt="design.name" style="width:100%; height:100%; object-fit: cover;">
            </template>
            <div v-else class="preview-placeholder">
               <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
            </div>
          </div>
          <div class="design-info">
            <h3 class="design-name">{{ design.name }}</h3>
            <p class="design-meta">{{ t('Category', 'التصنيف') }}: {{ design.category ? design.category.name : 'N/A' }}</p>
            <div class="design-footer">
               <span class="design-date">{{ t('By', 'بواسطة') }}: {{ design.creator?.name }}</span>
               <router-link :to="`/designs/${design.id}`" class="view-link">{{ t('Details', 'التفاصيل') }}</router-link>
            </div>
          </div>
        </BaseCard>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { useLang } from '../../composables/useLang';
import Layout from '../../components/Layout.vue';
import BaseCard from '../../components/UI/BaseCard.vue';
import BaseButton from '../../components/UI/BaseButton.vue';
import { hasPermission } from '../../utils/permissions';
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const { isRtl, t } = useLang();

const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const can = (perm) => hasPermission(user.value, perm);

const designs = ref([]);
const loading = ref(true);

onMounted(async () => {
   if (!can('designs.view')) return router.push('/dashboard');
   try {
      const res = await axios.get('/api/designs', {
         headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      designs.value = res.data;
   } catch(e) { } finally { loading.value = false; }
});
</script>

<style scoped>
.page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; }
.page-title { font-size: 1.75rem; font-weight: 800; color: #1a3a5f; margin: 0; }
.page-subtitle { color: #64748b; font-size: 0.9375rem; margin-top: 0.25rem; }

.designs-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }

.design-preview { height: 180px; background: #f8fafc; display: flex; align-items: center; justify-content: center; position: relative; border-bottom: 1px solid #f1f5f9; overflow: hidden; }
.preview-placeholder { color: #cbd5e1; }
.design-badge { position: absolute; top: 12px; right: 12px; background: white; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 800; border: 1px solid #f1f5f9; }

.design-info { padding: 1.25rem; }
.design-name { font-size: 1rem; font-weight: 800; color: #1a3a5f; margin: 0; }
.design-meta { font-size: 0.8125rem; color: #64748b; margin-top: 0.25rem; }

.design-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #f8fafc; }
.design-date { font-size: 0.75rem; color: #94a3b8; font-weight: 600; }
.view-link { color: #0ea5e9; text-decoration: none; font-size: 0.8125rem; font-weight: 800; }

.rtl .page-header { text-align: right; }
.text-center { text-align: center; }
.p-4 { padding: 1.5rem; }
</style>
