<template>
  <Layout>
    <div class="fits-view" :class="{ 'rtl': isRtl }">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('Fit / Cut Types', 'أنواع القصات') }}</h1>
          <p class="page-subtitle">{{ t('Manage fit styles linked to item types', 'إدارة ستايلات القصات المرتبطة بأنواع القطع') }}</p>
        </div>
        <BaseButton v-if="can('fit_types.create')" @click="showModal = true">
          <template #icon-left>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
          </template>
          {{ t('Add Fit Type', 'إضافة نوع قصة') }}
        </BaseButton>
      </div>

      <BaseCard no-padding class="table-card mt-2">
        <div class="table-responsive">
          <table class="modern-table">
            <thead>
              <tr>
                <th>{{ t('Name', 'الاسم') }}</th>
                <th>{{ t('Item Type', 'نوع القطعة') }}</th>
                <th>{{ t('Category', 'التصنيف') }}</th>
                <th>{{ t('Status', 'الحالة') }}</th>
                <th class="text-right">{{ t('Actions', 'الإجراءات') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="fit in fits" :key="fit.id">
                <td><span class="fit-name">{{ fit.name }}</span></td>
                <td><span class="item-text">{{ fit.item_type?.name || '---' }}</span></td>
                <td><span class="category-text">{{ fit.item_type?.category?.name || '---' }}</span></td>
                <td>
                  <span class="status-badge" :class="fit.status">
                    {{ t(fit.status === 'active' ? 'Active' : 'Inactive', fit.status === 'active' ? 'نشط' : 'غير نشط') }}
                  </span>
                </td>
                <td class="text-right">
                  <div class="action-btns">
                    <button v-if="can('fit_types.edit')" class="action-icon-btn edit" @click="openEditModal(fit)" :title="t('Edit', 'تعديل')">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                    </button>
                    <button v-if="can('fit_types.delete')" class="action-icon-btn del" @click="deleteFit(fit.id)" :title="t('Delete', 'حذف')">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="fits.length === 0">
                <td colspan="5" class="empty-state">{{ t('No fit types found.', 'لم يتم العثور على أنواع قصات.') }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </BaseCard>

      <!-- Add/Edit Modal -->
      <BaseModal 
        :show="showModal" 
        :title="editingFit ? t('Edit Fit Type', 'تعديل نوع قصة') : t('Add New Fit Type', 'إضافة نوع قصة جديد')"
        :is-rtl="isRtl"
        @close="closeModal"
      >
        <div class="modal-form">
          <BaseInput 
            v-model="form.name" 
            :label="t('Fit Type Name', 'اسم نوع القصة')" 
            :placeholder="t('e.g. Slim Fit', 'مثال: سليم فيت')" 
            required 
          />
          
          <div class="mt-1-5">
            <SearchableSelect
              v-model="form.item_type_id"
              :label="t('Item Type', 'نوع القطعة')"
              :options="itemTypeOptions"
              :is-rtl="isRtl"
              :placeholder="t('Select Item Type', 'اختر نوع القطعة')"
              required
            />
          </div>

          <div class="mt-1-5">
            <SearchableSelect
              v-model="form.status"
              :label="t('Status', 'الحالة')"
              :options="statusOptions"
              :is-rtl="isRtl"
              :clearable="false"
            />
          </div>
        </div>

        <template #footer>
          <BaseButton variant="white" @click="showModal = false">{{ t('Cancel', 'إلغاء') }}</BaseButton>
          <BaseButton @click="saveFit" :loading="saving">{{ t('Save Changes', 'حفظ التغييرات') }}</BaseButton>
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
        :message="t('Are you sure you want to delete this fit type?', 'هل أنت متأكد من حذف هذا النوع من القصات؟')" 
        confirmVariant="danger" 
        :isRtl="isRtl" 
        @cancel="showConfirmDelete = false" 
        @confirm="handleConfirmDelete" 
      />
    </div>
  </Layout>
</template>

<script setup>
import Layout from '../../components/Layout.vue';
import BaseCard from '../../components/UI/BaseCard.vue';
import BaseButton from '../../components/UI/BaseButton.vue';
import BaseInput from '../../components/UI/BaseInput.vue';
import BaseModal from '../../components/UI/BaseModal.vue';
import SearchableSelect from '../../components/UI/SearchableSelect.vue';
import AlertModal from '../../components/UI/AlertModal.vue';
import ConfirmModal from '../../components/UI/ConfirmModal.vue';
import { hasPermission } from '../../utils/permissions';
import axios from 'axios';
import { ref, reactive, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const can = (perm) => hasPermission(user.value, perm);

const fits = ref([]);
const itemTypes = ref([]);
const showModal = ref(false);
const saving = ref(false);
const editingFit = ref(null);
const form = ref({ name: '', item_type_id: null, status: 'active' });

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
const fitToDelete = ref(null);

const isRtl = computed(() => localStorage.getItem('lang') === 'ar');
const t = (en, ar) => isRtl.value ? ar : en;

const statusOptions = [
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' }
];

const itemTypeOptions = computed(() =>
  itemTypes.value.map(i => ({ id: i.id, name: i.name + ' (' + (i.category?.name || '...') + ')' }))
);

const fetchData = async () => {
  const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };
  const [fitsRes, itemsRes] = await Promise.all([
    axios.get('/api/fits', { headers }),
    axios.get('/api/item-types', { headers })
  ]);
  fits.value = fitsRes.data;
  itemTypes.value = itemsRes.data;
};

onMounted(() => {
  if (!can('fit_types.view')) return router.push('/dashboard');
  fetchData();
});

const openEditModal = (fit) => {
  editingFit.value = fit;
  form.value = { ...fit };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingFit.value = null;
  form.value = { name: '', item_type_id: null, status: 'active' };
};

const saveFit = async () => {
  if (!form.value.name || !form.value.item_type_id) return;
  saving.value = true;
  try {
    const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };
    if (editingFit.value) {
      await axios.put(`/api/fits/${editingFit.value.id}`, form.value, { headers });
    } else {
      await axios.post('/api/fits', form.value, { headers });
    }
    closeModal();
    fetchData();
  } catch (e) {
    showAlert(t('Error saving fit type', 'فشل حفظ نوع القصة'), '', 'error');
  } finally {
    saving.value = false;
  }
};

const deleteFit = (id) => {
  fitToDelete.value = id;
  showConfirmDelete.value = true;
};

const handleConfirmDelete = async () => {
  if (fitToDelete.value) {
    try {
      const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };
      await axios.delete(`/api/fits/${fitToDelete.value}`, { headers });
      fetchData();
    } catch (e) {}
    fitToDelete.value = null;
  }
  showConfirmDelete.value = false;
};
</script>

<style scoped>
.page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; }
.page-title { font-size: 1.75rem; font-weight: 800; color: #1a3a5f; margin: 0; }
.page-subtitle { color: #64748b; font-size: 0.9375rem; margin-top: 0.25rem; }

.modern-table { width: 100%; border-collapse: collapse; }
.modern-table th { text-align: left; padding: 1rem 1.5rem; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; background: #fafbfc; border-bottom: 1px solid #f1f5f9; }
.modern-table td { padding: 1.125rem 1.5rem; border-bottom: 1px solid #f8fafc; font-size: 0.9375rem; }

.fit-name { font-weight: 700; color: #1a3a5f; }
.item-text { color: #1a3a5f; font-weight: 700; }
.category-text { color: #64748b; font-weight: 600; font-size: 0.875rem; }
.status-badge { padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
.status-badge.active { background: #f0fdf4; color: #16a34a; }
.status-badge.inactive { background: #f1f5f9; color: #94a3b8; }

.action-icon-btn { width: 32px; height: 32px; border-radius: 8px; border: 1px solid #f1f5f9; background: #f8fafc; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.action-icon-btn:hover { color: #1a3a5f; background: #f1f5f9; border-color: #cbd5e1; }
.action-icon-btn.del:hover { background: #fff1f2; color: #f43f5e; border-color: #ffe4e6; }
.action-btns { display: flex; gap: 0.5rem; justify-content: flex-end; }

.mt-1-5 { margin-top: 1.5rem; }
.text-right { text-align: right; }

.rtl .page-header { text-align: right; }
.rtl .modern-table th { text-align: right; }
.rtl .text-right { text-align: left; }
.rtl .action-btns { justify-content: flex-start; }
</style>
