<template>
  <Layout>
    <div class="product-management-view" :class="{ 'rtl': isRtl }">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('Product Catalog', 'كتالوج المنتجات') }}</h1>
          <p class="page-subtitle">{{ t('Manage finished products, inventory and pricing', 'إدارة المنتجات النهائية، المخزون والتسعير') }}</p>
        </div>
        <BaseButton v-if="can('products.create')" @click="openCreateModal">
          <template #icon-left>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
          </template>
          {{ t('Create Product', 'إنشاء منتج') }}
        </BaseButton>
      </div>

      <!-- Filters -->
      <BaseCard class="mb-2" no-padding>
        <div class="filter-bar">
          <div class="search-box">
             <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
             <input v-model="filters.search" type="text" :placeholder="t('Search SKU, name...', 'بحث بالباركود، الاسم...')">
          </div>
          <div class="filter-actions">
             <select v-model="filters.status" class="custom-select-small">
                <option value="">{{ t('All Status', 'كل الحالات') }}</option>
                <option value="active">{{ t('Active', 'نشط') }}</option>
                <option value="inactive">{{ t('Inactive', 'معطل') }}</option>
             </select>
          </div>
        </div>
      </BaseCard>

      <!-- Products Grid -->
      <div class="products-grid" v-if="products.length">
        <BaseCard v-for="product in products" :key="product.id" class="product-card" no-padding>
          <div class="product-image">
            <img v-if="product.image_path" :src="product.image_path" :alt="product.name">
            <div v-else class="image-placeholder">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
            </div>
          </div>
          <div class="product-info">
            <div class="p-header">
              <span class="p-sku">{{ product.sku || 'NO SKU' }}</span>
              <span class="status-badge" :class="product.status">{{ t(product.status, product.status === 'active' ? 'نشط' : 'معطل') }}</span>
            </div>
            <h3 class="p-name">{{ product.name }}</h3>
            <p class="p-category" v-if="product.category">{{ product.category.name }}</p>
            <div class="p-footer">
              <span class="p-price">{{ product.price }} EGP</span>
              <div class="p-actions">
                <button v-if="can('products.edit')" @click="openEditModal(product)" class="action-btn">✏️</button>
                <button v-if="can('products.delete')" @click="deleteProduct(product.id)" class="action-btn del">🗑️</button>
              </div>
            </div>
          </div>
        </BaseCard>
      </div>

      <!-- Empty State -->
      <BaseCard v-else class="empty-state-card">
        <div class="empty-v">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
          <h2>{{ t('No products available.', 'لا توجد منتجات متاحة.') }}</h2>
          <p>{{ t("Click 'Create Product' to add your first item to the catalog.", "اضغط على 'إنشاء منتج' لإضافة أول قطعة للكتالوج.") }}</p>
          <BaseButton v-if="can('products.create')" @click="openCreateModal" variant="white" class="mt-2">{{ t('Create Product', 'إنشاء منتج') }}</BaseButton>
        </div>
      </BaseCard>

      <!-- Modal placeholder (for future implementation) -->
      <BaseModal :show="showModal" @close="showModal = false" :title="editingProduct ? t('Edit Product', 'تعديل منتج') : t('Create Product', 'إنشاء منتج')">
        <div class="modal-form">
          <BaseInput v-model="form.name" :label="t('Product Name', 'اسم المنتج')" required />
          <BaseInput v-model="form.sku" :label="t('SKU', 'الباركود')" class="mt-2" />
          <BaseInput v-model="form.price" type="number" :label="t('Price', 'السعر')" class="mt-2" />
          <select v-model="form.status" class="custom-select mt-2">
            <option value="active">{{ t('Active', 'نشط') }}</option>
            <option value="inactive">{{ t('Inactive', 'معطل') }}</option>
          </select>
        </div>
        <template #footer>
          <BaseButton variant="white" @click="showModal = false">{{ t('Cancel', 'إلغاء') }}</BaseButton>
          <BaseButton @click="saveProduct" :loading="saving">{{ t('Save', 'حفظ') }}</BaseButton>
        </template>
      </BaseModal>

      <ConfirmModal 
        :show="showConfirmDelete" 
        :title="t('Confirm Delete', 'تأكيد الحذف')" 
        :message="t('Are you sure you want to delete this product? This action cannot be undone.', 'هل أنت متأكد من حذف هذا المنتج؟ لا يمكن التراجع عن هذا الإجراء.')" 
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
import BaseModal from '../../components/UI/BaseModal.vue';
import BaseInput from '../../components/UI/BaseInput.vue';
import ConfirmModal from '../../components/UI/ConfirmModal.vue';
import { hasPermission } from '../../utils/permissions';
import { ref, reactive, onMounted, computed, watch } from 'vue';
import axios from 'axios';

const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const can = (perm) => hasPermission(user.value, perm);
const isRtl = computed(() => localStorage.getItem('lang') === 'ar');
const t = (en, ar) => isRtl.value ? ar : en;

const products = ref([]);
const loading = ref(true);
const saving = ref(false);
const showModal = ref(false);
const editingProduct = ref(null);

const showConfirmDelete = ref(false);
const productToDelete = ref(null);

const confirmDelete = (id) => {
  productToDelete.value = id;
  showConfirmDelete.value = true;
};

const handleConfirmDelete = async () => {
  if (productToDelete.value) {
    try {
      await axios.delete(`/api/products/${productToDelete.value}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      await fetchProducts();
    } catch (e) {}
    productToDelete.value = null;
  }
  showConfirmDelete.value = false;
};

const filters = reactive({
  search: '',
  status: ''
});

const form = reactive({
  name: '',
  sku: '',
  price: 0,
  status: 'active'
});

const fetchProducts = async () => {
  loading.value = true;
  try {
    const res = await axios.get('/api/products', {
      params: filters,
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    products.value = res.data.data || res.data;
  } catch (e) {}
  loading.value = false;
};

onMounted(fetchProducts);
watch(filters, fetchProducts, { deep: true });

const openCreateModal = () => {
  editingProduct.value = null;
  form.name = '';
  form.sku = '';
  form.price = 0;
  form.status = 'active';
  showModal.value = true;
};

const openEditModal = (p) => {
  editingProduct.value = p;
  form.name = p.name;
  form.sku = p.sku;
  form.price = p.price;
  form.status = p.status;
  showModal.value = true;
};

const saveProduct = async () => {
  saving.value = true;
  try {
    const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };
    if (editingProduct.value) {
      await axios.put(`/api/products/${editingProduct.value.id}`, form, { headers });
    } else {
      await axios.post('/api/products', form, { headers });
    }
    await fetchProducts();
    showModal.value = false;
  } catch (e) {}
  saving.value = false;
};

const deleteProduct = (id) => {
  confirmDelete(id);
};
</script>

<style scoped>
.product-management-view { max-width: 1200px; margin: 0 auto; padding-bottom: 2rem; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.page-title { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0; }
.page-subtitle { color: #64748b; font-size: 0.875rem; }

.filter-bar { display: flex; justify-content: space-between; align-items: center; padding: 1rem; }
.search-box { position: relative; flex: 1; max-width: 400px; display: flex; align-items: center; gap: 0.75rem; }
.search-box input { width: 100%; border: none; background: transparent; outline: none; font-size: 0.875rem; color: #1e293b; }

.products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
.product-card { transition: all 0.2s; border: 1px solid #f1f5f9; overflow: hidden; height: 100%; display: flex; flex-direction: column; }
.product-card:hover { transform: translateY(-4px); box-shadow: 0 12px 20px -10px rgba(0,0,0,0.1); border-color: #0ea5e9; }

.product-image { aspect-ratio: 4/3; background: #f8fafc; display: flex; align-items: center; justify-content: center; color: #cbd5e1; border-bottom: 1px solid #f1f5f9; }
.product-image img { width: 100%; height: 100%; object-fit: cover; }

.product-info { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
.p-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
.p-sku { font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
.p-name { font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0 0 0.25rem 0; }
.p-category { font-size: 0.8125rem; color: #64748b; margin-bottom: 1rem; }

.p-footer { margin-top: auto; display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid #f1f5f9; }
.p-price { font-size: 1.25rem; font-weight: 800; color: #0ea5e9; }

.p-actions { display: flex; gap: 0.5rem; }
.action-btn { background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.action-btn:hover { background: #e2e8f0; }
.action-btn.del:hover { background: #fee2e2; }

.status-badge { font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 99px; text-transform: uppercase; }
.status-badge.active { background: #dcfce7; color: #15803d; }
.status-badge.inactive { background: #fee2e2; color: #b91c1c; }

.empty-state-card { padding: 4rem 2rem; }
.empty-v { display: flex; flex-direction: column; align-items: center; text-align: center; color: #94a3b8; }
.empty-v h2 { color: #1e293b; margin-top: 1.5rem; }
.empty-v p { max-width: 400px; margin: 0.5rem 0 1.5rem; }

.modal-form { padding: 1rem 0; }
.custom-select { width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 12px; background: white; font-size: 0.9375rem; }

.rtl .p-header, .rtl .p-footer { flex-direction: row-reverse; }
.rtl .p-name, .rtl .p-category { text-align: right; }
</style>
