<template>
  <Layout>
    <div class="user-management-view" :class="{ rtl: isRtl }">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('Identity & Access', 'الهوية والوصول') }}</h1>
          <p class="page-subtitle">
            {{
              t(
                'Manage your team members, departments and system roles',
                'إدارة أعضاء الفريق، الأقسام وأدوار النظام'
              )
            }}
          </p>
        </div>
        <div class="header-actions">
          <BaseButton variant="white" @click="$router.push('/settings/roles')">{{
            t('Roles Registry', 'سجل الأدوار')
          }}</BaseButton>
          <BaseButton v-if="can('team.create')" @click="openCreateModal">
            <template #icon-left>
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <line x1="19" y1="8" x2="19" y2="14" />
                <line x1="16" y1="11" x2="22" y2="11" />
              </svg>
            </template>
            {{ t('Add User', 'إضافة مستخدم') }}
          </BaseButton>
        </div>
      </div>

      <!-- Filters & Search -->
      <BaseCard class="mb-2" no-padding>
        <div class="filter-bar">
          <div class="search-box">
            <svg
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="#94a3b8"
              stroke-width="2"
            >
              <circle cx="11" cy="11" r="8" />
              <path d="m21 21-4.35-4.35" />
            </svg>
            <input
              v-model="search"
              type="text"
              :placeholder="t('Search name, email...', 'بحث بالاسم، البريد...')"
            />
          </div>
          <div class="filter-actions">
            <select v-model="filterRole" class="custom-select-small">
              <option value="">{{ t('All Roles', 'كل الأدوار') }}</option>
              <option v-for="(info, key) in INTERNAL_ROLES" :key="key" :value="key">
                {{ t(info.name, info.name) }}
              </option>
            </select>
            <select v-model="filterStatus" class="custom-select-small">
              <option value="">{{ t('All Status', 'كل الحالات') }}</option>
              <option value="active">{{ t('Active', 'نشط') }}</option>
              <option value="disabled">{{ t('Disabled', 'معطل') }}</option>
            </select>
          </div>
        </div>
      </BaseCard>

      <!-- User Table -->
      <BaseCard no-padding class="table-card">
        <div class="table-responsive">
          <table class="modern-table">
            <thead>
              <tr>
                <th>{{ t('User', 'المستخدم') }}</th>
                <th>{{ t('Role', 'الدور') }}</th>
                <th>{{ t('Department', 'القسم') }}</th>
                <th>{{ t('Status', 'الحالة') }}</th>
                <th class="text-right">{{ t('Actions', 'الإجراءات') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in filteredUsers" :key="user.id">
                <td>
                  <div class="user-info-cell">
                    <UserAvatar :user="user" size="md" />
                    <div class="user-text">
                      <span class="u-name">{{ user.name }}</span>
                      <span class="u-email">{{ user.email }}</span>
                      <span v-if="user.client" class="u-brand"
                        >🏢 {{ user.client.brand_name }}</span
                      >
                    </div>
                  </div>
                </td>
                <td>
                  <span
                    class="role-badge"
                    :style="{
                      backgroundColor: getRoleColor(user.role) + '15',
                      color: getRoleColor(user.role),
                    }"
                  >
                    {{
                      t(
                        ROLE_INFO[user.role]?.name || user.role,
                        ROLE_INFO[user.role]?.name || user.role
                      )
                    }}
                  </span>
                </td>
                <td>
                  <span class="dept-text">{{ user.department || '---' }}</span>
                </td>
                <td>
                  <span class="status-indicator" :class="user.status">
                    {{ t(user.status, user.status === 'active' ? 'نشط' : 'معطل') }}
                  </span>
                </td>
                <td class="text-right">
                  <div class="action-btns">
                    <button
                      v-if="can('team.edit')"
                      class="icon-btn edit"
                      :title="t('Edit', 'تعديل')"
                      @click="openEditModal(user)"
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
                      v-if="can('team.edit')"
                      class="icon-btn toggle"
                      :title="t('Toggle Status', 'تغيير الحالة')"
                      @click="toggleUserStatus(user)"
                    >
                      <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                      >
                        <path d="M18.36 6.64a9 9 0 1 1-12.73 0" />
                        <line x1="12" y1="2" x2="12" y2="12" />
                      </svg>
                    </button>
                    <button
                      v-if="can('team.delete')"
                      v-show="user.role !== 'superadmin'"
                      class="icon-btn delete"
                      @click="deleteUser(user)"
                    >
                      <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                      >
                        <path
                          d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                        />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="filteredUsers.length === 0">
                <td colspan="5" class="empty-state">
                  {{ t('No users found.', 'لا يوجد مستخدمين.') }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </BaseCard>

      <!-- Create/Edit Modal -->
      <div v-if="showModal" class="modal-overlay" @click="closeModal">
        <div class="modal-card" @click.stop>
          <div class="modal-head">
            <h3>
              {{
                editingUser
                  ? t('Edit User', 'تعديل مستخدم')
                  : t('Create New User', 'إنشاء مستخدم جديد')
              }}
            </h3>
            <button class="close-btn" @click="closeModal">×</button>
          </div>

          <form class="modal-form" @submit.prevent="saveUser">
            <div class="form-grid">
              <div class="field">
                <label>{{ t('Full Name', 'الاسم بالكامل') }}</label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  :placeholder="t('John Doe', 'جون دو')"
                />
              </div>
              <div class="field">
                <label>{{ t('Email Address', 'البريد الإلكتروني') }}</label>
                <input v-model="form.email" type="email" required placeholder="name@company.com" />
              </div>
              <div class="field">
                <label>{{ t('Password', 'كلمة المرور') }}</label>
                <input
                  v-model="form.password"
                  type="password"
                  :required="!editingUser"
                  :placeholder="
                    editingUser
                      ? t('Leave blank to keep current', 'اتركه فارغاً للحفاظ على الحالي')
                      : ''
                  "
                />
              </div>
              <div class="field">
                <label>{{ t('Assigned Role', 'الدور المعين') }}</label>
                <select v-model="form.role" required>
                  <option v-for="(info, key) in INTERNAL_ROLES" :key="key" :value="key">
                    {{ t(info.name, info.name) }}
                  </option>
                </select>
              </div>
              <div class="field">
                <label>{{ t('Department', 'القسم') }}</label>
                <input
                  v-model="form.department"
                  type="text"
                  :placeholder="t('Ex: Production, QC', 'مثال: الإنتاج، الجودة')"
                />
              </div>
              <div class="field">
                <label>{{ t('Account Status', 'حالة الحساب') }}</label>
                <select v-model="form.status">
                  <option value="active">{{ t('Active', 'نشط') }}</option>
                  <option value="disabled">{{ t('Disabled', 'معطل') }}</option>
                </select>
              </div>
            </div>

            <div class="modal-footer">
              <BaseButton variant="white" type="button" @click="closeModal">{{
                t('Cancel', 'إلغاء')
              }}</BaseButton>
              <BaseButton type="submit" :loading="saving">{{
                t('Save User', 'حفظ المستخدم')
              }}</BaseButton>
            </div>
          </form>
        </div>
      </div>

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
          t(
            'Are you sure you want to delete this user? This action cannot be undone.',
            'هل أنت متأكد من حذف هذا المستخدم؟ لا يمكن التراجع عن هذا الإجراء.'
          )
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
  import { useLang } from '../../composables/useLang';
  import Layout from '../../components/Layout.vue';
  import BaseCard from '../../components/UI/BaseCard.vue';
  import BaseButton from '../../components/UI/BaseButton.vue';
  import UserAvatar from '../../components/UI/UserAvatar.vue';
  import AlertModal from '../../components/UI/AlertModal.vue';
  import ConfirmModal from '../../components/UI/ConfirmModal.vue';
  import { ROLE_INFO, hasPermission } from '../../utils/permissions';
  import { ref, onMounted, computed, reactive } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import axios from 'axios';

  const route = useRoute();
  const router = useRouter();
  const currentUser = ref(JSON.parse(localStorage.getItem('user') || 'null'));
  const can = (perm) => hasPermission(currentUser.value, perm);

  const users = ref([]);
  const search = ref('');
  const filterRole = ref(route.query.role || '');
  const filterStatus = ref('');
  const showModal = ref(false);
  const editingUser = ref(null);
  const saving = ref(false);

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
  const userToDelete = ref(null);

  const { isRtl, t } = useLang();

  const form = ref({
    name: '',
    email: '',
    password: '',
    role: 'dataentry',
    department: '',
    status: 'active',
  });

  onMounted(() => {
    if (!can('team.view')) return router.push('/dashboard');
    fetchUsers();
  });

  async function fetchUsers() {
    try {
      const res = await axios.get('/api/users', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` },
      });
      users.value = res.data;
    } catch (e) {
      console.warn(e);
    }
  }

  const INTERNAL_ROLES = computed(() => {
    const roles = { ...ROLE_INFO };
    delete roles.client;
    return roles;
  });

  const filteredUsers = computed(() => {
    return (users.value || []).filter((u) => {
      if (u.role === 'client') return false;
      const matchesSearch =
        (u.name || '').toLowerCase().includes(search.value.toLowerCase()) ||
        (u.email || '').toLowerCase().includes(search.value.toLowerCase());
      const matchesRole = !filterRole.value || u.role === filterRole.value;
      const matchesStatus = !filterStatus.value || u.status === filterStatus.value;
      return matchesSearch && matchesRole && matchesStatus;
    });
  });

  const getRoleColor = (role) => ROLE_INFO[role]?.color || '#64748b';

  const openCreateModal = () => {
    editingUser.value = null;
    form.value = {
      name: '',
      email: '',
      password: '',
      role: 'dataentry',
      department: '',
      status: 'active',
    };
    showModal.value = true;
  };

  const openEditModal = (user) => {
    editingUser.value = user;
    form.value = { ...user, password: '' };
    showModal.value = true;
  };

  const closeModal = () => (showModal.value = false);

  const saveUser = async () => {
    saving.value = true;
    try {
      const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };
      if (editingUser.value) {
        await axios.put(`/api/users/${editingUser.value.id}`, form.value, { headers });
      } else {
        await axios.post('/api/users', form.value, { headers });
      }
      await fetchUsers();
      closeModal();
    } catch (e) {
      showAlert(t('Error saving user.', 'خطأ في حفظ المستخدم.'), '', 'error');
    } finally {
      saving.value = false;
    }
  };

  const toggleUserStatus = async (user) => {
    try {
      await axios.patch(
        `/api/users/${user.id}/toggle-status`,
        {},
        {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` },
        }
      );
      fetchUsers();
    } catch (e) {
      console.warn(e);
    }
  };

  const deleteUser = (user) => {
    userToDelete.value = user;
    showConfirmDelete.value = true;
  };

  const handleConfirmDelete = async () => {
    if (userToDelete.value) {
      try {
        await axios.delete(`/api/users/${userToDelete.value.id}`, {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` },
        });
        fetchUsers();
      } catch (e) {
        console.warn(e);
      }
      userToDelete.value = null;
    }
    showConfirmDelete.value = false;
  };
</script>

<style scoped>
  .user-management-view {
    padding-bottom: 4rem;
  }
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
  .header-actions {
    display: flex;
    gap: 0.75rem;
  }

  .filter-bar {
    padding: 1.25rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
  }
  .search-box {
    position: relative;
    flex: 1;
    max-width: 400px;
  }
  .search-box svg {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
  }
  .search-box input {
    width: 100%;
    height: 42px;
    padding-left: 2.5rem;
    border: 1px solid #f1f5f9;
    background: #fafbfc;
    border-radius: 12px;
    font-size: 0.875rem;
  }
  .search-box input:focus {
    background: white;
    border-color: #0ea5e9;
    outline: none;
  }

  .filter-actions {
    display: flex;
    gap: 0.75rem;
  }
  .custom-select-small {
    height: 42px;
    padding: 0 1rem;
    border: 1px solid #f1f5f9;
    background: #fafbfc;
    border-radius: 10px;
    font-size: 0.8125rem;
    font-weight: 700;
    color: #475569;
    outline: none;
    cursor: pointer;
  }

  .modern-table {
    width: 100%;
    border-collapse: collapse;
  }
  .modern-table th {
    text-align: left;
    padding: 1.25rem 1.5rem;
    background: #fafbfc;
    font-size: 0.75rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    border-bottom: 1px solid #f1f5f9;
  }
  .modern-table td {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f8fafc;
    font-size: 0.875rem;
  }

  .user-info-cell {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  .user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.125rem;
  }
  .user-text {
    display: flex;
    flex-direction: column;
  }
  .u-name {
    font-weight: 800;
    color: #1a3a5f;
  }
  .u-email {
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 600;
  }
  .u-brand {
    font-size: 0.7rem;
    color: #0ea5e9;
    font-weight: 800;
    text-transform: uppercase;
    margin-top: 4px;
  }

  .role-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 800;
  }
  .dept-text {
    font-weight: 700;
    color: #64748b;
    font-size: 0.8125rem;
  }

  .status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 800;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .status-indicator::before {
    content: '';
    width: 8px;
    height: 8px;
    border-radius: 50%;
  }
  .status-indicator.active {
    color: #10b981;
  }
  .status-indicator.active::before {
    background: #10b981;
  }
  .status-indicator.disabled {
    color: #ef4444;
  }
  .status-indicator.disabled::before {
    background: #ef4444;
  }

  .action-btns {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
  }
  .icon-btn {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #f1f5f9;
    background: white;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
  }
  .icon-btn:hover {
    border-color: #0ea5e9;
    color: #0ea5e9;
    background: #f0f9ff;
  }
  .icon-btn.delete:hover {
    border-color: #ef4444;
    color: #ef4444;
    background: #fef2f2;
  }

  /* Modal */
  .modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 200;
  }
  .modal-card {
    background: white;
    width: 600px;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  }
  .modal-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
  }
  .modal-head h3 {
    margin: 0;
    font-size: 1.35rem;
    font-weight: 800;
    color: #1a3a5f;
  }
  .close-btn {
    background: none;
    border: none;
    font-size: 2rem;
    color: #94a3b8;
    cursor: pointer;
    line-height: 1;
  }

  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
  }
  .field {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  .field label {
    font-size: 0.75rem;
    font-weight: 800;
    color: #475569;
    text-transform: uppercase;
  }
  .field input,
  .field select {
    height: 46px;
    padding: 0 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    outline: none;
    font-size: 0.9375rem;
  }
  .field input:focus,
  .field select:focus {
    border-color: #0ea5e9;
  }

  .modal-footer {
    margin-top: 2.5rem;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    border-top: 1px solid #f1f5f9;
    padding-top: 1.5rem;
  }

  .mb-2 {
    margin-bottom: 2rem;
  }
  .text-right {
    text-align: right;
  }

  .rtl .page-header,
  .rtl .filter-bar,
  .rtl .modal-head {
    text-align: right;
  }
  .rtl .search-box svg {
    left: auto;
    right: 12px;
  }
  .rtl .search-box input {
    padding-left: 1rem;
    padding-right: 2.5rem;
  }
  .rtl .modern-table th {
    text-align: right;
  }
  .rtl .action-btns {
    justify-content: flex-start;
  }
</style>
