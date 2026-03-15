<template>
  <Layout>
    <div class="roles-view" :class="{ 'rtl': isRtl }">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('Identity Registry', 'سجل الهويات') }}</h1>
          <p class="page-subtitle">{{ t('Manage system roles and access control levels', 'إدارة أدوار النظام ومستويات التحكم في الوصول') }}</p>
        </div>
        <div class="header-actions">
          <BaseButton variant="white" @click="$router.push('/settings/permissions')" class="mr-2">
            <template #icon-left>
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </template>
            {{ t('Access Matrix', 'مصفوفة الوصول') }}
          </BaseButton>
          <BaseButton v-if="can('roles.edit')" @click="openCreateModal">
            <template #icon-left>
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            </template>
            {{ t('Add Role', 'إضافة دور') }}
          </BaseButton>
        </div>
      </div>

      <div class="roles-grid" v-if="!loading">
        <BaseCard v-for="role in allRoles" :key="role.slug" class="role-card" :class="{ 'is-disabled': role.status === 'disabled' }">
          <div class="role-header">
            <div class="role-icon-box" :style="{ backgroundColor: (role.color || '#64748b') + '15', color: role.color || '#64748b' }">
               <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="7" r="4"/><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/></svg>
            </div>
            <div class="role-meta">
               <div class="name-status">
                 <h3 class="role-name">{{ t(role.name, role.name) }}</h3>
                 <span class="status-badge" :class="role.status">{{ t(role.status, role.status === 'active' ? 'نشط' : 'معطل') }}</span>
               </div>
               <span class="role-key">{{ role.slug }}</span>
            </div>
          </div>
          
          <p class="role-description">{{ t(role.description, role.description) || t('No description provided.', 'لا يوجد وصف.') }}</p>

          <div class="role-stats">
             <div class="stat-item">
                <span class="stat-label">{{ t('Permissions', 'الصلاحيات') }}</span>
                <span class="stat-value">{{ getPermissionCount(role) }}</span>
             </div>
             <div class="stat-item">
                <span class="stat-label">{{ t('Active Users', 'المستخدمون') }}</span>
                <span class="stat-value">{{ userCounts[role.slug] || 0 }}</span>
             </div>
          </div>

          <template #footer>
             <div class="role-actions">
                <BaseButton variant="ghost" size="sm" @click="editPermissions(role)">
                   {{ t('Manage Permissions', 'إدارة الصلاحيات') }}
                </BaseButton>
                <div class="action-group">
                  <button class="icon-action-btn" @click="toggleRoleStatus(role)" :title="t('Toggle Status', 'تغيير الحالة')">
                    <svg v-if="role.status === 'active'" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
                    <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4"/><path d="m16.2 7.8 2.9-2.9"/><path d="M18 12h4"/><path d="m16.2 16.2 2.9 2.9"/><path d="M12 18v4"/><path d="m7.8 16.2-2.9 2.9"/><path d="M2 12h4"/><path d="m7.8 7.8-2.9-2.9"/></svg>
                  </button>
                  <BaseButton variant="white" size="sm" @click="viewUsers(role.slug)">
                     <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  </BaseButton>
                </div>
             </div>
          </template>
        </BaseCard>
      </div>

      <!-- Add Role Modal -->
      <BaseModal 
        :show="showCreateModal" 
        @close="showCreateModal = false"
        :title="t('Create New Role', 'إنشاء دور جديد')"
        :subtitle="t('Define security permissions and access level', 'تحديد صلاحيات الأمان ومستوى الوصول')"
        size="lg"
        :isRtl="isRtl"
      >
        <div class="modal-form">
          <BaseInput v-model="form.name" :label="t('Role Name', 'اسم الدور')" :placeholder="t('Ex: Quality Inspector', 'مثال: مراقب جودة')" required class="mb-2" />
          <BaseTextarea v-model="form.description" :label="t('Description', 'الوصف')" :placeholder="t('Describe the responsibilities of this role...', 'وصف مسؤوليات هذا الدور...')" rows="3" />
        </div>

        <template #footer>
          <BaseButton variant="white" @click="showCreateModal = false">{{ t('Cancel', 'إلغاء') }}</BaseButton>
          <BaseButton :loading="saving" @click="saveRole">{{ t('Create', 'إنشاء') }}</BaseButton>
        </template>
      </BaseModal>
    </div>
  </Layout>
</template>

<script setup>
import Layout from '../../components/Layout.vue';
import BaseCard from '../../components/UI/BaseCard.vue';
import BaseButton from '../../components/UI/BaseButton.vue';
import BaseModal from '../../components/UI/BaseModal.vue';
import BaseInput from '../../components/UI/BaseInput.vue';
import BaseTextarea from '../../components/UI/BaseTextarea.vue';
import { ROLES, ROLE_INFO, ROLE_PERMISSIONS, MODULES, ACTIONS, hasPermission } from '../../utils/permissions';
import { ref, onMounted, computed, reactive } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const currentUser = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const can = (perm) => hasPermission(currentUser.value, perm);

const userCounts = ref({});
const customRoles = ref([]);
const loading = ref(true);
const saving = ref(false);
const showCreateModal = ref(false);

const isRtl = computed(() => localStorage.getItem('lang') === 'ar');
const t = (en, ar) => isRtl.value ? ar : en;

const form = reactive({
  name: '',
  description: '',
  color: '#0ea5e9'
});

const allRoles = computed(() => {
  return customRoles.value.map(role => ({
    ...role,
    isSystem: ['superadmin', 'admin', 'manager', 'designer', 'dataentry', 'client'].includes(role.slug),
    permissions: role.permissions || []
  }));
});

const getPermissionCount = (role) => {
   if (role.slug === ROLES.SUPER_ADMIN) return t('All', 'الكل');
   return (role.permissions || []).length;
};

const fetchRoles = async () => {
   loading.value = true;
   try {
      const res = await axios.get('/api/roles', {
         headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      customRoles.value = res.data.roles;
      userCounts.value = res.data.userCounts;
   } catch (e) {}
   loading.value = false;
};

onMounted(() => {
  if (!can('roles.view')) return router.push('/dashboard');
  fetchRoles();
});

const openCreateModal = () => {
  form.name = '';
  form.description = '';
  form.color = '#0ea5e9';
  showCreateModal.value = true;
};

const saveRole = async () => {
  if (!form.name) return;
  saving.value = true;
  try {
     await axios.post('/api/roles', form, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
     });
     showCreateModal.value = false;
     fetchRoles();
  } catch (e) {
    alert(t('Error saving role.', 'فشل حفظ الدور.'));
  }
  saving.value = false;
};

const toggleRoleStatus = async (role) => {
  if (role.isSystem) {
    alert(t('System roles cannot be disabled.', 'لا يمكن تعطيل أدوار النظام.'));
    return;
  }
  try {
    await axios.patch(`/api/roles/${role.id}/toggle-status`, {}, {
       headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    fetchRoles();
  } catch (e) {}
};

const editPermissions = (role) => {
   router.push({ path: '/settings/permissions', query: { role: role.slug } });
};

const viewUsers = (roleSlug) => {
   router.push({ path: '/users', query: { role: roleSlug } });
};
</script>

<style scoped>
.roles-view { padding-bottom: 4rem; }
.page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; }
.page-title { font-size: 1.75rem; font-weight: 800; color: #1a3a5f; margin: 0; }
.page-subtitle { color: #64748b; font-size: 0.9375rem; margin-top: 0.25rem; }

.roles-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem; }

.role-card { transition: all 0.3s; }
.role-card.is-disabled { opacity: 0.7; filter: grayscale(0.5); border-style: dashed; }

.role-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem; }
.role-icon-box { width: 52px; height: 52px; border-radius: 16px; display: flex; align-items: center; justify-content: center; }
.role-name { font-size: 1.125rem; font-weight: 800; color: #1a3a5f; margin: 0; }
.role-key { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.05em; }

.name-status { display: flex; align-items: center; gap: 0.75rem; }
.status-badge { font-size: 0.65rem; font-weight: 800; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; }
.status-badge.active { background: #f0fdf4; color: #16a34a; }
.status-badge.disabled { background: #fef2f2; color: #ef4444; }

.role-description { font-size: 0.875rem; color: #64748b; line-height: 1.5; margin-bottom: 1.5rem; height: 3rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-clamp: 2; }

.role-stats { display: flex; gap: 2rem; border-top: 1px solid #f1f5f9; padding-top: 1.25rem; }
.stat-item { display: flex; flex-direction: column; gap: 0.25rem; }
.stat-label { font-size: 0.65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; }
.stat-value { font-size: 1.125rem; font-weight: 800; color: #1a3a5f; }

.role-actions { display: flex; justify-content: space-between; align-items: center; width: 100%; gap: 0.75rem; }
.action-group { display: flex; align-items: center; gap: 0.5rem; }

.icon-action-btn { background: #f8fafc; border: 1px solid #f1f5f9; color: #64748b; padding: 6px; border-radius: 8px; cursor: pointer; transition: all 0.2s; display: flex; }
.icon-action-btn:hover { background: #f1f5f9; color: #0ea5e9; border-color: #0ea5e9; }

/* Modal Styles */
.modal-form { padding: 0.5rem 0; }
.color-selector { display: flex; align-items: center; gap: 1rem; height: 44px; padding: 0 1rem; border: 1px solid #e2e8f0; border-radius: 10px; }
.role-color-picker { width: 32px; height: 32px; border: none; padding: 0; background: none; cursor: pointer; }
.color-hex { font-size: 0.8125rem; font-weight: 700; color: #475569; font-family: monospace; }

.section-title-sm { font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem; }

.module-item { background: #f8fafc; border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem; border: 1px solid #f1f5f9; }
.module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px dashed #e2e8f0; }
.module-name { font-weight: 700; color: #1e293b; font-size: 0.9375rem; }
.select-all-btn { font-size: 0.7rem; font-weight: 800; color: #0ea5e9; background: none; border: none; cursor: pointer; text-transform: uppercase; }

.actions-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; }
.action-checkbox { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
.action-checkbox input { width: 16px; height: 16px; border-radius: 4px; border: 2px solid #cbd5e1; cursor: pointer; }
.action-label { font-size: 0.8125rem; font-weight: 600; color: #475569; }

.mt-1 { margin-top: 1rem; }
.mr-2 { margin-right: 0.5rem; }

.rtl .page-header { text-align: right; }
.rtl .role-header { text-align: right; }
.rtl .mr-2 { margin-right: 0; margin-left: 0.5rem; }
</style>
