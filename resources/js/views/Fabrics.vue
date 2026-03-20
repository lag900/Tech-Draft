<template>
  <Layout>
    <div class="fabrics-view" :class="{ 'rtl': isRtl }">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('Fabric Library', 'مكتبة الأقمشة') }}</h1>
          <p class="page-subtitle">{{ t('Manage manufacturing materials and textile specifications', 'إدارة مواد التصنيع ومواصفات المنسوجات') }}</p>
        </div>
        <BaseButton v-if="can('fabrics.create')" @click="showModal = true">
          <template #icon-left>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
          </template>
          {{ t('Add Fabric', 'إضافة قماش') }}
        </BaseButton>
      </div>

      <div class="stats-row">
         <BaseCard class="mini-stat">
            <div class="stat-icon fiber">
               <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <div class="stat-info">
               <span class="label">{{ t('Total Fabrics', 'إجمالي الأقمشة') }}</span>
               <span class="value">{{ fabrics.length }}</span>
            </div>
         </BaseCard>
      </div>

      <BaseCard no-padding class="table-card mt-2">
        <div class="table-responsive">
          <table class="modern-table">
            <thead>
              <tr>
                <th>{{ t('Fabric Name', 'اسم القماش') }}</th>
                <th>{{ t('Type', 'النوع') }}</th>
                <th class="text-right">{{ t('Actions', 'الإجراءات') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="fabric in fabrics" :key="fabric.id">
                <td><span class="fabric-name">{{ fabric.name }}</span></td>
                <td><span class="type-badge">{{ fabric.type }}</span></td>
                <td class="text-right">
                   <div class="action-btns">
                     <button v-if="can('fabrics.edit')" class="action-btn edit" @click="openEditModal(fabric)" :title="t('Edit', 'تعديل')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                     </button>
                     <button v-if="can('fabrics.delete')" class="action-btn del" @click="deleteFabric(fabric.id)" :title="t('Delete', 'حذف')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                     </button>
                   </div>
                </td>
              </tr>
              <tr v-if="fabrics.length === 0">
                <td colspan="3" class="empty-state">{{ t('No fabrics found.', 'لم يتم العثور على أقمشة.') }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </BaseCard>

      <!-- Add Fabric Modal -->
      <BaseModal 
        :show="showModal" 
        :title="editingFabric ? t('Edit Fabric', 'تعديل قماش') : t('Add New Fabric', 'إضافة قماش جديد')"
        :is-rtl="isRtl"
        @close="closeModal"
      >
        <div class="modal-form">
          <BaseInput 
            v-model="newFabric.name" 
            :label="t('Fabric Name', 'اسم القماش')" 
            :placeholder="t('e.g. Organic Cotton', 'مثال: قطن عضوي')" 
            required 
          />
          
          <div class="mt-1-5">
            <SearchableSelect
              v-model="newFabric.type"
              :label="t('Material Type', 'نوع المادة')"
              :options="materialOptions"
              :is-rtl="isRtl"
              :clearable="false"
            />
          </div>
        </div>

        <template #footer>
          <BaseButton variant="white" @click="showModal = false">{{ t('Cancel', 'إلغاء') }}</BaseButton>
          <BaseButton @click="saveFabric" :loading="saving">{{ t('Save Fabric', 'حفظ القماش') }}</BaseButton>
        </template>
      </BaseModal>

      <AlertModal 
        :show="alertModal.show" 
        :title="alertModal.title" 
        :message="alertModal.message" 
        :type="alertModal.type" 
        :isRtl="isRtl" 
        @close="alertModal.show = false" 
      />

      <ConfirmModal 
        :show="showConfirmDelete" 
        :title="t('Confirm Delete', 'تأكيد الحذف')" 
        :message="t('Are you sure you want to delete this fabric?', 'هل أنت متأكد من حذف هذا القماش؟')" 
        confirmVariant="danger" 
        :isRtl="isRtl" 
        @cancel="showConfirmDelete = false" 
        @confirm="handleConfirmDelete" 
      />
    </div>
  </Layout>
</template>

<script setup>
import { useLang } from '../composables/useLang';
import Layout from '../components/Layout.vue';
import BaseCard from '../components/UI/BaseCard.vue';
import BaseButton from '../components/UI/BaseButton.vue';
import BaseInput from '../components/UI/BaseInput.vue';
import BaseModal from '../components/UI/BaseModal.vue';
import SearchableSelect from '../components/UI/SearchableSelect.vue';
import AlertModal from '../components/UI/AlertModal.vue';
import ConfirmModal from '../components/UI/ConfirmModal.vue';
import { hasPermission } from '../utils/permissions';
import axios from 'axios';
import { ref, reactive, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const can = (perm) => hasPermission(user.value, perm);

const fabrics = ref([]);
const showModal = ref(false);
const saving = ref(false);
const editingFabric = ref(null);
const newFabric = ref({ name: '', type: 'Woven' });

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

const showConfirmDelete = ref(false);
const fabricToDelete = ref(null);

const { isRtl, t } = useLang();

const materialOptions = computed(() => [
  { value: 'Woven', label: t('Woven', 'منسوج') },
  { value: 'Knitted', label: t('Knitted', 'تريكو') },
  { value: 'Non-woven', label: t('Non-woven', 'غير منسوج') }
]);

const fetchFabrics = async () => {
  const res = await axios.get('/api/fabrics', {
    headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
  });
  fabrics.value = res.data;
};

onMounted(() => {
  if (!can('fabrics.view')) return router.push('/dashboard');
  fetchFabrics();
});

const openEditModal = (fabric) => {
  editingFabric.value = fabric;
  newFabric.value = { ...fabric };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingFabric.value = null;
  newFabric.value = { name: '', type: 'Woven' };
};

const saveFabric = async () => {
  if (!newFabric.value.name) return;
  saving.value = true;
  try {
    const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };
    if (editingFabric.value) {
      await axios.put(`/api/fabrics/${editingFabric.value.id}`, newFabric.value, { headers });
    } else {
      await axios.post('/api/fabrics', newFabric.value, { headers });
    }
    closeModal();
    fetchFabrics();
  } catch (e) {
    showAlert(t('Error saving fabric', 'فشل حفظ القماش'), '', 'error');
  } finally {
    saving.value = false;
  }
};

const deleteFabric = (id) => {
  fabricToDelete.value = id;
  showConfirmDelete.value = true;
};

const handleConfirmDelete = async () => {
  if (fabricToDelete.value) {
    try {
      await axios.delete(`/api/fabrics/${fabricToDelete.value}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      fetchFabrics();
    } catch (e) {}
    fabricToDelete.value = null;
  }
  showConfirmDelete.value = false;
};
</script>

<style scoped>
.page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; }
.page-title { font-size: 1.75rem; font-weight: 800; color: #1a3a5f; margin: 0; }
.page-subtitle { color: #64748b; font-size: 0.9375rem; margin-top: 0.25rem; }

.stats-row { margin-bottom: 1.5rem; }
.mini-stat { display: flex; align-items: center; gap: 1rem; padding: 1.25rem !important; width: fit-content; min-width: 200px; }
.stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
.stat-icon.fiber { background: #f0fdf4; color: #16a34a; }
.stat-info .label { font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; display: block; }
.stat-info .value { font-size: 1.25rem; font-weight: 800; color: #1a3a5f; }

.modern-table { width: 100%; border-collapse: collapse; }
.modern-table th { text-align: left; padding: 1rem 1.5rem; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; background: #fafbfc; border-bottom: 1px solid #f1f5f9; }
.modern-table td { padding: 1.125rem 1.5rem; border-bottom: 1px solid #f8fafc; font-size: 0.9375rem; }

.fabric-name { font-weight: 700; color: #1a3a5f; }
.type-badge { background: #f1f5f9; color: #475569; padding: 0.25rem 0.625rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }

.action-btn { width: 32px; height: 32px; border-radius: 8px; border: 1px solid #f1f5f9; background: #f8fafc; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.action-btn:hover { background: #f1f5f9; color: #1a3a5f; border-color: #cbd5e1; }
.action-btn.del:hover { background: #fff1f2; color: #f43f5e; border-color: #ffe4e6; }
.action-btns { display: flex; gap: 0.5rem; justify-content: flex-end; }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.3); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 200; }
.modal-card { background: white; width: 400px; border-radius: 20px; padding: 2.5rem; }
.modal-title { margin: 0 0 2rem; color: #1a3a5f; font-weight: 800; }
.field-label { display: block; font-size: 0.8125rem; font-weight: 700; color: #475569; margin-bottom: 0.5rem; }
.custom-select { width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 10px; background: white; font-size: 0.9375rem; }
.modal-footer { display: flex; justify-content: flex-end; gap: 0.75rem; }

.text-right { text-align: right; }
.mt-1-5 { margin-top: 1.5rem; }

.rtl .page-header, .rtl .modal-card { text-align: right; }
.rtl .modern-table th { text-align: right; }
.rtl .modal-footer { flex-direction: row-reverse; }
.rtl .text-right { text-align: left; }
.rtl .action-btns { justify-content: flex-start; }
</style>
