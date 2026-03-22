<template>
  <Layout>
    <div class="categories-view" :class="{ rtl: isRtl }">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('Categories', 'التصنيفات') }}</h1>
          <p class="page-subtitle">
            {{
              t('Manage parent classifications for products', 'إدارة التصنيفات الرئيسية للمنتجات')
            }}
          </p>
        </div>
        <BaseButton v-if="can('categories.create')" @click="showModal = true">
          <template #icon-left>
            <svg
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path d="M12 5v14M5 12h14" />
            </svg>
          </template>
          {{ t('Add Category', 'إضافة تصنيف') }}
        </BaseButton>
      </div>

      <!-- Desktop View: Table Layout -->
      <BaseCard no-padding class="table-card mt-2 hidden md:block">
        <div class="table-responsive">
          <table class="modern-table">
            <thead>
              <tr>
                <th>{{ t('Name', 'الاسم') }}</th>
                <th>{{ t('Description', 'الوصف') }}</th>
                <th>{{ t('Status', 'الحالة') }}</th>
                <th class="text-right">{{ t('Actions', 'الإجراءات') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="cat in categories" :key="cat.id">
                <td>
                  <span class="cat-name">{{ cat.name }}</span>
                </td>
                <td>
                  <span class="desc-text">{{ cat.description || '---' }}</span>
                </td>
                <td>
                  <span class="status-badge" :class="cat.status">
                    {{
                      t(
                        cat.status === 'active' ? 'Active' : 'Inactive',
                        cat.status === 'active' ? 'نشط' : 'غير نشط'
                      )
                    }}
                  </span>
                </td>
                <td class="text-right">
                  <div class="action-btns">
                    <button
                      v-if="can('categories.edit')"
                      class="action-icon-btn edit"
                      :title="t('Edit', 'تعديل')"
                      @click="openEditModal(cat)"
                    >
                      <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                      >
                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z" />
                      </svg>
                    </button>
                    <button
                      v-if="can('categories.delete')"
                      class="action-icon-btn del"
                      :title="t('Delete', 'حذف')"
                      @click="deleteCategory(cat.id)"
                    >
                      <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                      >
                        <path d="M3 6h18" />
                        <path
                          d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                        />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="categories.length === 0">
                <td colspan="4" class="empty-state">
                  {{ t('No categories found.', 'لم يتم العثور على تصنيفات.') }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </BaseCard>

      <!-- Mobile View: Card-based Layout -->
      <div class="mt-4 flex flex-col gap-4 md:hidden">
        <div v-for="cat in categories" :key="'mob-' + cat.id" class="mobile-category-card">
          <div class="mcc-header">
            <h3 class="mcc-title">{{ cat.name }}</h3>
            <span class="status-badge" :class="cat.status">
              {{
                t(
                  cat.status === 'active' ? 'Active' : 'نشط',
                  cat.status === 'active' ? 'نشط' : 'غير نشط'
                )
              }}
            </span>
          </div>
          <p class="mcc-desc">{{ cat.description || '---' }}</p>
          <div class="mcc-actions mt-3 border-t border-slate-100 pt-3">
            <button v-if="can('categories.edit')" class="mcc-btn edit" @click="openEditModal(cat)">
              <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z" />
              </svg>
              {{ t('Edit', 'تعديل') }}
            </button>
            <button
              v-if="can('categories.delete')"
              class="mcc-btn del"
              @click="deleteCategory(cat.id)"
            >
              <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M3 6h18" />
                <path
                  d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                />
              </svg>
              {{ t('Delete', 'حذف') }}
            </button>
          </div>
        </div>

        <div
          v-if="categories.length === 0"
          class="mt-4 rounded-2xl border border-slate-100 bg-white py-10 text-center text-sm font-bold text-slate-500 shadow-sm"
        >
          {{ t('No categories found.', 'لم يتم العثور على تصنيفات.') }}
        </div>
      </div>

      <!-- Add/Edit Modal -->
      <BaseModal
        :show="showModal"
        :title="
          editingCat ? t('Edit Category', 'تعديل تصنيف') : t('Add New Category', 'إضافة تصنيف جديد')
        "
        :is-rtl="isRtl"
        @close="closeModal"
      >
        <div class="modal-form">
          <BaseInput
            v-model="form.name"
            :label="t('Category Name', 'اسم التصنيف')"
            :placeholder="t('e.g. Women Casual', 'مثال: ملابس حريمي كاجوال')"
            required
          />

          <div class="mt-1-5">
            <BaseInput
              v-model="form.description"
              :label="t('Description', 'الوصف')"
              :placeholder="t('Optional description', 'وصف اختياري')"
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
          <BaseButton variant="white" @click="showModal = false">{{
            t('Cancel', 'إلغاء')
          }}</BaseButton>
          <BaseButton :loading="saving" @click="saveCategory">{{
            t('Save Changes', 'حفظ التغييرات')
          }}</BaseButton>
        </template>
      </BaseModal>

      <AlertModal
        :show="alertModal.show"
        :title="alertModal.title"
        :message="alertModal.message"
        :type="alertModal.type"
        :is-rtl="isRtl"
        @close="alertModal.show = false"
      />

      <ConfirmModal
        :show="showConfirmDelete"
        :title="t('Confirm Delete', 'تأكيد الحذف')"
        :message="
          t('Are you sure you want to delete this category?', 'هل أنت متأكد من حذف هذا التصنيف؟')
        "
        confirm-variant="danger"
        :is-rtl="isRtl"
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
  import { ref, reactive, onMounted } from 'vue';
  import { useRouter } from 'vue-router';

  const router = useRouter();
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
  const can = (perm) => hasPermission(user.value, perm);

  const categories = ref([]);
  const showModal = ref(false);
  const saving = ref(false);
  const editingCat = ref(null);
  const form = ref({ name: '', description: '', status: 'active' });

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

  const showConfirmDelete = ref(false);
  const catToDelete = ref(null);

  const { isRtl, t } = useLang();

  const statusOptions = [
    { value: 'active', label: 'Active' },
    { value: 'inactive', label: 'Inactive' },
  ];

  const fetchCategories = async () => {
    const res = await axios.get('/api/categories', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` },
    });
    categories.value = res.data;
  };

  onMounted(() => {
    if (!can('categories.view')) return router.push('/dashboard');
    fetchCategories();
  });

  const openEditModal = (cat) => {
    editingCat.value = cat;
    form.value = { ...cat };
    showModal.value = true;
  };

  const closeModal = () => {
    showModal.value = false;
    editingCat.value = null;
    form.value = { name: '', description: '', status: 'active' };
  };

  const saveCategory = async () => {
    if (!form.value.name) return;
    saving.value = true;
    try {
      const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };
      if (editingCat.value) {
        await axios.put(`/api/categories/${editingCat.value.id}`, form.value, { headers });
      } else {
        await axios.post('/api/categories', form.value, { headers });
      }
      closeModal();
      fetchCategories();
    } catch (e) {
      showAlert(t('Error saving category', 'فشل حفظ التصنيف'), '', 'error');
    } finally {
      saving.value = false;
    }
  };

  const deleteCategory = (id) => {
    catToDelete.value = id;
    showConfirmDelete.value = true;
  };

  const handleConfirmDelete = async () => {
    if (catToDelete.value) {
      try {
        const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };
        await axios.delete(`/api/categories/${catToDelete.value}`, { headers });
        fetchCategories();
      } catch (e) {
        console.warn(e);
      }
      catToDelete.value = null;
    }
    showConfirmDelete.value = false;
  };
</script>

<style scoped>
  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 2rem;
  }
  .page-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1a3a5f;
    margin: 0;
  }
  .page-subtitle {
    color: #64748b;
    font-size: 0.9375rem;
    margin-top: 0.25rem;
  }

  .modern-table {
    width: 100%;
    border-collapse: collapse;
  }
  .modern-table th {
    text-align: left;
    padding: 1rem 1.5rem;
    font-size: 0.75rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    background: #fafbfc;
    border-bottom: 1px solid #f1f5f9;
  }
  .modern-table td {
    padding: 1.125rem 1.5rem;
    border-bottom: 1px solid #f8fafc;
    font-size: 0.9375rem;
  }

  .cat-name {
    font-weight: 700;
    color: #1a3a5f;
  }
  .desc-text {
    color: #64748b;
    font-style: italic;
  }
  .status-badge {
    padding: 0.25rem 0.6rem;
    border-radius: 6px;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
  }
  .status-badge.active {
    background: #f0fdf4;
    color: #16a34a;
  }
  .status-badge.inactive {
    background: #f1f5f9;
    color: #94a3b8;
  }

  .action-icon-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid #f1f5f9;
    background: #f8fafc;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
  }
  .action-icon-btn:hover {
    color: #1a3a5f;
    background: #f1f5f9;
    border-color: #cbd5e1;
  }
  .action-icon-btn.del:hover {
    background: #fff1f2;
    color: #f43f5e;
    border-color: #ffe4e6;
  }
  .action-btns {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
  }

  .mt-1-5 {
    margin-top: 1.5rem;
  }
  .text-right {
    text-align: right;
  }

  .rtl .page-header {
    text-align: right;
  }
  .rtl .modern-table th {
    text-align: right;
  }
  .rtl .text-right {
    text-align: left;
  }
  .rtl .action-btns {
    justify-content: flex-start;
  }

  /* Mobile Categories List */
  .mobile-category-card {
    background: white;
    border-radius: 1.25rem; /* xl */
    padding: 1.25rem;
    border: 1px solid #f1f5f9;
    box-shadow:
      0 4px 6px -1px rgba(0, 0, 0, 0.05),
      0 2px 4px -2px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .mcc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
  }

  .mcc-title {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 800;
    color: #0f172a;
  }

  .mcc-desc {
    margin: 0;
    font-size: 0.8125rem;
    color: #64748b;
    line-height: 1.5;
  }

  .mcc-actions {
    display: flex;
    gap: 0.75rem;
  }

  .mcc-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .mcc-btn.edit {
    background: #f1f5f9;
    color: #334155;
  }
  .mcc-btn.edit:active {
    background: #e2e8f0;
  }

  .mcc-btn.del {
    background: #fff1f2;
    color: #e11d48;
  }
  .mcc-btn.del:active {
    background: #ffe4e6;
  }

  @media (max-width: 767px) {
    .page-header {
      flex-direction: column;
      align-items: stretch;
      gap: 1rem;
      margin-bottom: 1rem;
    }
  }
</style>
