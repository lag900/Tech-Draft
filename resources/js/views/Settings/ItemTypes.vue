<template>
  <Layout>
    <div class="item-types-view" :class="{ rtl: isRtl }">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('Item Types', 'أنواع القطع') }}</h1>
          <p class="page-subtitle">
            {{
              t('Manage item types linked to categories', 'إدارة أنواع القطع المرتبطة بالتصنيفات')
            }}
          </p>
        </div>
        <BaseButton v-if="can('item_types.create')" @click="showModal = true">
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
          {{ t('Add Item Type', 'إضافة نوع قطعة') }}
        </BaseButton>
      </div>

      <BaseCard no-padding class="table-card mt-2">
        <div class="table-responsive">
          <table class="modern-table">
            <thead>
              <tr>
                <th>{{ t('Name', 'الاسم') }}</th>
                <th>{{ t('Category', 'التصنيف') }}</th>
                <th>{{ t('Status', 'الحالة') }}</th>
                <th class="text-right">{{ t('Actions', 'الإجراءات') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in itemTypes" :key="item.id">
                <td>
                  <span class="item-name">{{ item.name }}</span>
                </td>
                <td>
                  <span class="category-text">{{ item.category?.name || '---' }}</span>
                </td>
                <td>
                  <span class="status-badge" :class="item.status">
                    {{
                      t(
                        item.status === 'active' ? 'Active' : 'Inactive',
                        item.status === 'active' ? 'نشط' : 'غير نشط'
                      )
                    }}
                  </span>
                </td>
                <td class="text-right">
                  <div class="action-btns">
                    <button
                      v-if="can('item_types.edit')"
                      class="action-icon-btn edit"
                      :title="t('Edit', 'تعديل')"
                      @click="openEditModal(item)"
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
                      v-if="can('item_types.delete')"
                      class="action-icon-btn del"
                      :title="t('Delete', 'حذف')"
                      @click="deleteItem(item.id)"
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
              <tr v-if="itemTypes.length === 0">
                <td colspan="4" class="empty-state">
                  {{ t('No item types found.', 'لم يتم العثور على أنواع قطع.') }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </BaseCard>

      <!-- Add/Edit Modal -->
      <BaseModal
        :show="showModal"
        :title="
          editingItem
            ? t('Edit Item Type', 'تعديل نوع قطعة')
            : t('Add New Item Type', 'إضافة نوع قطعة جديد')
        "
        :is-rtl="isRtl"
        @close="closeModal"
      >
        <div class="modal-form">
          <BaseInput
            v-model="form.name"
            :label="t('Item Type Name', 'اسم نوع القطعة')"
            :placeholder="t('e.g. Pants', 'مثال: بناطيل')"
            required
          />

          <div class="mt-1-5">
            <SearchableSelect
              v-model="form.category_id"
              :label="t('Category', 'التصنيف')"
              :options="categoryOptions"
              :is-rtl="isRtl"
              :placeholder="t('Select Category', 'اختر التصنيف')"
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
          <BaseButton variant="white" @click="showModal = false">{{
            t('Cancel', 'إلغاء')
          }}</BaseButton>
          <BaseButton :loading="saving" @click="saveItem">{{
            t('Save Changes', 'حفظ التغييرات')
          }}</BaseButton>
        </template>
      </BaseModal>
    </div>
  </Layout>
</template>

<script setup>
  import { useLang } from '../../composables/useLang';
  import Layout from '../../components/Layout.vue';
  import BaseCard from '../../components/UI/BaseCard.vue';
  import BaseButton from '../../components/UI/BaseButton.vue';
  import BaseInput from '../../components/UI/BaseInput.vue';
  import BaseModal from '../../components/UI/BaseModal.vue';
  import SearchableSelect from '../../components/UI/SearchableSelect.vue';
  import { hasPermission } from '../../utils/permissions';
  import { ref, onMounted, computed } from 'vue';
  import { useRouter } from 'vue-router';
  import axios from 'axios';

  const router = useRouter();
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
  const can = (perm) => hasPermission(user.value, perm);

  const itemTypes = ref([]);
  const categories = ref([]);
  const showModal = ref(false);
  const saving = ref(false);
  const editingItem = ref(null);
  const form = ref({ name: '', category_id: null, status: 'active' });

  const { isRtl, t } = useLang();

  const statusOptions = [
    { value: 'active', label: 'Active' },
    { value: 'inactive', label: 'Inactive' },
  ];

  const categoryOptions = computed(() => categories.value.map((c) => ({ id: c.id, name: c.name })));

  const fetchData = async () => {
    const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };
    const [itemsRes, catsRes] = await Promise.all([
      axios.get('/api/item-types', { headers }),
      axios.get('/api/categories', { headers }),
    ]);
    itemTypes.value = itemsRes.data;
    categories.value = catsRes.data;
  };

  onMounted(() => {
    if (!can('item_types.view')) return router.push('/dashboard');
    fetchData();
  });

  const openEditModal = (item) => {
    editingItem.value = item;
    form.value = { ...item };
    showModal.value = true;
  };

  const closeModal = () => {
    showModal.value = false;
    editingItem.value = null;
    form.value = { name: '', category_id: null, status: 'active' };
  };

  const saveItem = async () => {
    if (!form.value.name || !form.value.category_id) return;
    saving.value = true;
    try {
      const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };
      if (editingItem.value) {
        await axios.put(`/api/item-types/${editingItem.value.id}`, form.value, { headers });
      } else {
        await axios.post('/api/item-types', form.value, { headers });
      }
      closeModal();
      fetchData();
    } catch (e) {
      alert('Error saving item type');
    } finally {
      saving.value = false;
    }
  };

  const deleteItem = async (id) => {
    if (!confirm(t('Are you sure?', 'هل أنت متأكد؟'))) return;
    await axios.delete(`/api/item-types/${id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` },
    });
    fetchData();
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

  .item-name {
    font-weight: 700;
    color: #1a3a5f;
  }
  .category-text {
    color: #64748b;
    font-weight: 600;
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
</style>
