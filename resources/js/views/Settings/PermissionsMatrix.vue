<template>
  <Layout>
    <div class="perm-view" :class="{ rtl: isRtl }">
      <!-- ── PAGE HEADER ── -->
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('Security Access Matrix', 'مصفوفة الوصول الأمني') }}</h1>
          <p class="page-subtitle">
            {{
              t(
                'Manage role permissions using the visual builder below',
                'إدارة صلاحيات الأدوار عبر المحرر البصري'
              )
            }}
          </p>
        </div>
        <div class="header-actions">
          <BaseButton variant="white" @click="$router.push('/settings/roles')">
            <template #icon-left>
              <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
              >
                <path d="m15 18-6-6 6-6" />
              </svg>
            </template>
            {{ t('Roles', 'الأدوار') }}
          </BaseButton>
          <BaseButton :loading="saving" @click="saveMatrix">
            <template #icon-left>
              <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
              >
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                <polyline points="17 21 17 13 7 13 7 21" />
                <polyline points="7 3 7 8 15 8" />
              </svg>
            </template>
            {{ t('Save', 'حفظ') }}
          </BaseButton>
        </div>
      </div>

      <!-- ── CONTROLS BAR ── -->
      <div class="controls-bar">
        <!-- Role Selector -->
        <div class="role-tabs">
          <button
            v-for="role in dynamicRoles"
            :key="role.slug"
            class="role-tab"
            :class="{ active: selectedRole === role.slug }"
            @click="selectedRole = role.slug"
          >
            <span class="role-dot" :style="{ background: role.color }"></span>
            {{ role.name }}
            <span class="role-count">{{ getRolePermCount(role.slug) }}</span>
          </button>
        </div>

        <!-- Search -->
        <div class="search-wrap">
          <svg
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.35-4.35" />
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="t('Search permissions or modules...', 'بحث في الصلاحيات أو الوحدات...')"
            class="search-input"
          />
          <button v-if="searchQuery" class="clear-search" @click="searchQuery = ''">
            <svg
              width="14"
              height="14"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
            >
              <path d="M18 6 6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- ── SUPER ADMIN BANNER ── -->
      <div v-if="selectedRole === 'superadmin'" class="superadmin-banner">
        <svg
          width="20"
          height="20"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
        </svg>
        <span>{{
          t(
            'Super Admin has unrestricted access to all modules and actions.',
            'المسؤول الأعلى لديه وصول غير مقيد لجميع الوحدات والإجراءات.'
          )
        }}</span>
      </div>

      <!-- ── MODULE CARDS GRID ── -->
      <div class="modules-grid">
        <div
          v-for="module in filteredModules"
          :key="module.key"
          class="module-card"
          :class="{ collapsed: collapsedModules[module.key] }"
        >
          <!-- Card Header -->
          <div class="module-header" @click="toggleCollapse(module.key)">
            <div class="module-header-left">
              <div
                class="module-icon"
                :style="{ background: module.color + '18', color: module.color }"
              >
                <span>{{ module.icon }}</span>
              </div>
              <div>
                <h3 class="module-name">{{ t(module.label, module.labelAr) }}</h3>
                <p class="module-meta">
                  {{ getGrantedCount(module.key) }} / {{ module.actions.length }}
                  {{ t('permissions granted', 'صلاحية ممنوحة') }}
                </p>
              </div>
            </div>
            <div class="module-header-right">
              <!-- Grant All Toggle -->
              <button
                class="grant-all-btn"
                :class="{ active: isAllGranted(module.key) }"
                :title="t('Toggle all', 'تبديل الكل')"
                @click.stop="toggleGrantAll(module.key)"
              >
                {{
                  isAllGranted(module.key)
                    ? t('Revoke All', 'إلغاء الكل')
                    : t('Grant All', 'منح الكل')
                }}
              </button>
              <svg
                class="collapse-chevron"
                :class="{ rotated: !collapsedModules[module.key] }"
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="m6 9 6 6 6-6" />
              </svg>
            </div>
          </div>

          <!-- Permissions Grid -->
          <div v-show="!collapsedModules[module.key]" class="permissions-grid">
            <label
              v-for="action in module.actions"
              :key="action.key"
              class="perm-pill"
              :class="{
                granted: isChecked(selectedRole, module.key, action.key),
                disabled: !isActionAllowed(action),
                locked: !isActionAllowed(action),
              }"
            >
              <input
                type="checkbox"
                :checked="isChecked(selectedRole, module.key, action.key)"
                :disabled="!isActionAllowed(action)"
                @change="toggleCheck(selectedRole, module.key, action.key)"
              />
              <div class="perm-pill-inner">
                <span class="perm-icon">{{ action.icon }}</span>
                <div class="perm-text">
                  <span class="perm-label">{{ t(action.label, action.labelAr) }}</span>
                  <code class="perm-key">{{ module.key }}.{{ action.key }}</code>
                </div>

                <span class="action-lvl-badge" :class="getLevelBadge(action.level).class">
                  {{ t(getLevelBadge(action.level).label, getLevelBadge(action.level).labelAr) }}
                </span>

                <span class="perm-check" :class="{ 'is-locked': !isActionAllowed(action) }">
                  <svg
                    v-if="!isActionAllowed(action)"
                    width="14"
                    height="14"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                  >
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                  </svg>
                  <svg
                    v-else-if="isChecked(selectedRole, module.key, action.key)"
                    width="14"
                    height="14"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="3"
                  >
                    <polyline points="20 6 9 17 4 12" />
                  </svg>
                </span>
              </div>
            </label>
          </div>

          <!-- Progress bar -->
          <div v-show="!collapsedModules[module.key]" class="module-progress">
            <div class="progress-bar">
              <div
                class="progress-fill"
                :style="{
                  width: (getGrantedCount(module.key) / module.actions.length) * 100 + '%',
                  background: module.color,
                }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty search state -->
      <div v-if="filteredModules.length === 0" class="empty-search">
        <svg
          width="48"
          height="48"
          viewBox="0 0 24 24"
          fill="none"
          stroke="#e2e8f0"
          stroke-width="1.5"
        >
          <circle cx="11" cy="11" r="8" />
          <path d="m21 21-4.35-4.35" />
        </svg>
        <p>{{ t('No modules match your search.', 'لا توجد وحدات تطابق بحثك.') }}</p>
      </div>

      <!-- ── FLOATING SAVE BAR ── -->
      <Transition name="save-bar">
        <div v-if="isDirty" class="floating-save-bar">
          <span>{{ t('You have unsaved changes', 'لديك تغييرات غير محفوظة') }}</span>
          <div class="save-bar-actions">
            <button class="discard-btn" @click="discardChanges">{{ t('Discard', 'تجاهل') }}</button>
            <button class="confirm-save-btn" :disabled="saving" @click="saveMatrix">
              {{ saving ? t('Saving...', 'جارٍ الحفظ...') : t('Save Changes', 'حفظ التغييرات') }}
            </button>
          </div>
        </div>
      </Transition>
    </div>
  </Layout>
</template>

<script setup>
  import { useLang } from '../../composables/useLang';
  import Layout from '../../components/Layout.vue';
  import BaseButton from '../../components/UI/BaseButton.vue';
  import { ROLE_INFO, hasPermission, refreshUserPermissions } from '../../utils/permissions';
  import { ref, computed, onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  import axios from 'axios';

  const router = useRouter();
  const currentUser = ref(JSON.parse(localStorage.getItem('user') || 'null'));
  const can = (perm) => hasPermission(currentUser.value, perm);

  const { isRtl, t } = useLang();

  /* ─── State ─── */
  const saving = ref(false);
  const dynamicRoles = ref([]);
  const localMatrix = ref({});
  const originalMatrix = ref({});
  const selectedRole = ref('admin');
  const searchQuery = ref('');
  const collapsedModules = ref({});
  const isDirty = ref(false);

  /* ─── Shared action list ─── */
  const standardActions = [
    { key: 'view', label: 'View', labelAr: 'عرض', icon: '👁️', level: 10 },
    { key: 'create', label: 'Create', labelAr: 'إنشاء', icon: '➕', level: 50 },
    { key: 'edit', label: 'Edit', labelAr: 'تعديل', icon: '✏️', level: 50 },
    { key: 'export', label: 'Export', labelAr: 'تصدير', icon: '📤', level: 60 },
    { key: 'import', label: 'Import', labelAr: 'استيراد', icon: '📥', level: 80 },
    { key: 'approve', label: 'Approve', labelAr: 'اعتماد', icon: '✅', level: 80 },
    { key: 'delete', label: 'Delete', labelAr: 'حذف', icon: '🗑️', level: 90 },
  ];

  /* ─── Module definitions with icons ─── */
  const moduleDefinitions = [
    {
      key: 'dashboard',
      label: 'Dashboard',
      labelAr: 'لوحة التحكم',
      icon: '📊',
      color: '#6366f1',
      actions: [{ key: 'view', label: 'View', labelAr: 'عرض', icon: '👁️', level: 10 }],
    },
    {
      key: 'orders',
      label: 'Orders',
      labelAr: 'الطلبات',
      icon: '📋',
      color: '#3b82f6',
      actions: standardActions,
    },
    {
      key: 'designs',
      label: 'Designs',
      labelAr: 'التصاميم',
      icon: '🎨',
      color: '#ec4899',
      actions: standardActions,
    },
    {
      key: 'fabrics',
      label: 'Fabrics',
      labelAr: 'الأقمشة',
      icon: '🧵',
      color: '#14b8a6',
      actions: standardActions,
    },
    {
      key: 'categories',
      label: 'Categories',
      labelAr: 'التصنيفات',
      icon: '📂',
      color: '#f59e0b',
      actions: standardActions,
    },
    {
      key: 'item_types',
      label: 'Item Types',
      labelAr: 'أنواع القطع',
      icon: '👕',
      color: '#8b5cf6',
      actions: standardActions,
    },
    {
      key: 'fit_types',
      label: 'Fit Types',
      labelAr: 'أنواع القصات',
      icon: '📏',
      color: '#d946ef',
      actions: standardActions,
    },
    {
      key: 'measurements',
      label: 'Measurements',
      labelAr: 'القياسات',
      icon: '📐',
      color: '#06b6d4',
      actions: standardActions,
    },
    {
      key: 'products',
      label: 'Products',
      labelAr: 'المنتجات',
      icon: '📦',
      color: '#f97316',
      actions: standardActions,
    },
    {
      key: 'clients',
      label: 'Clients',
      labelAr: 'العملاء',
      icon: '🤝',
      color: '#10b981',
      actions: standardActions,
    },
    {
      key: 'team',
      label: 'Team Members',
      labelAr: 'فريق العمل',
      icon: '👥',
      color: '#8b5cf6',
      actions: standardActions,
    },
    {
      key: 'roles',
      label: 'Identity & Roles',
      labelAr: 'الأدوار والهوية',
      icon: '🛡️',
      color: '#f43f5e',
      actions: standardActions,
    },
    {
      key: 'settings',
      label: 'Settings',
      labelAr: 'الإعدادات',
      icon: '⚙️',
      color: '#64748b',
      actions: standardActions,
    },
  ];

  /* ─── Filtered modules (by search) ─── */
  const filteredModules = computed(() => {
    if (!searchQuery.value) return moduleDefinitions;
    const q = searchQuery.value.toLowerCase();
    return moduleDefinitions.filter(
      (m) =>
        m.label.toLowerCase().includes(q) ||
        m.labelAr.includes(q) ||
        m.actions.some(
          (a) =>
            a.label.toLowerCase().includes(q) ||
            a.labelAr.includes(q) ||
            `${m.key}.${a.key}`.includes(q)
        )
    );
  });

  /* ─── Action Levels ─── */
  const getSelectedRoleLevel = () => {
    const roleObj = dynamicRoles.value.find((r) => r.slug === selectedRole.value);
    return roleObj?.level || ROLE_INFO[selectedRole.value]?.level || 0;
  };

  const isActionAllowed = (action) => {
    // Allow admins to assign any permission to any role, removing the restrictive UX lock
    return true;
  };

  const getLevelBadge = (level) => {
    const lvl = level || 10;
    if (lvl <= 40) return { label: 'Low', labelAr: 'منخفض', class: 'lvl-low' };
    if (lvl <= 70) return { label: 'Mid', labelAr: 'متوسط', class: 'lvl-med' };
    if (lvl <= 90) return { label: 'High', labelAr: 'مرتفع', class: 'lvl-high' };
    return { label: 'Crit', labelAr: 'حرج', class: 'lvl-crit' };
  };

  /* ─── Permission helpers ─── */
  const isChecked = (roleSlug, module, action) => {
    const perm = `${module}.${action}`;
    return (localMatrix.value[roleSlug] || []).includes(perm);
  };

  const toggleCheck = (roleSlug, module, action) => {
    const perm = `${module}.${action}`;
    const current = [...(localMatrix.value[roleSlug] || [])];
    const idx = current.indexOf(perm);
    if (idx > -1) current.splice(idx, 1);
    else current.push(perm);
    localMatrix.value = { ...localMatrix.value, [roleSlug]: current };
    isDirty.value = true;
  };

  const isAllGranted = (moduleKey) => {
    const module = moduleDefinitions.find((m) => m.key === moduleKey);
    if (!module) return false;
    const allowedActions = module.actions.filter((a) => isActionAllowed(a));
    if (allowedActions.length === 0) return false;
    return allowedActions.every((a) => isChecked(selectedRole.value, moduleKey, a.key));
  };

  const toggleGrantAll = (moduleKey) => {
    const module = moduleDefinitions.find((m) => m.key === moduleKey);
    if (!module) return;
    const allGranted = isAllGranted(moduleKey);
    const current = [...(localMatrix.value[selectedRole.value] || [])];
    module.actions.forEach((a) => {
      if (!isActionAllowed(a)) return;
      const perm = `${moduleKey}.${a.key}`;
      const idx = current.indexOf(perm);
      if (allGranted && idx > -1) current.splice(idx, 1);
      else if (!allGranted && idx === -1) current.push(perm);
    });
    localMatrix.value = { ...localMatrix.value, [selectedRole.value]: current };
    isDirty.value = true;
  };

  const getGrantedCount = (moduleKey) => {
    const module = moduleDefinitions.find((m) => m.key === moduleKey);
    if (!module) return 0;
    return module.actions.filter((a) => isChecked(selectedRole.value, moduleKey, a.key)).length;
  };

  const getRolePermCount = (roleSlug) => {
    return (localMatrix.value[roleSlug] || []).length;
  };

  const toggleCollapse = (moduleKey) => {
    collapsedModules.value = {
      ...collapsedModules.value,
      [moduleKey]: !collapsedModules.value[moduleKey],
    };
  };

  /* ─── API ─── */
  const fetchMatrix = async () => {
    try {
      const res = await axios.get('/api/permissions', {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` },
      });
      dynamicRoles.value = res.data;
      const matrix = {};
      res.data.forEach((role) => {
        matrix[role.slug] = role.permissions || [];
      });
      localMatrix.value = JSON.parse(JSON.stringify(matrix));
      originalMatrix.value = JSON.parse(JSON.stringify(matrix));
      if (res.data.length)
        selectedRole.value =
          res.data.find((r) => r.slug !== 'superadmin')?.slug || res.data[0].slug;
    } catch (e) {
      console.error('Failed to load matrix', e);
    }
  };

  const saveMatrix = async () => {
    saving.value = true;
    try {
      await axios.post('/api/permissions', localMatrix.value, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` },
      });
      originalMatrix.value = JSON.parse(JSON.stringify(localMatrix.value));
      isDirty.value = false;
      showToast(t('Permissions saved successfully!', 'تم حفظ الصلاحيات بنجاح!'));
      // Refresh current user's permissions in case they were modified
      await refreshUserPermissions(axios);
    } catch (e) {
      showToast(t('Failed to save permissions.', 'فشل حفظ الصلاحيات.'), 'error');
    }
    saving.value = false;
  };

  const discardChanges = () => {
    localMatrix.value = JSON.parse(JSON.stringify(originalMatrix.value));
    isDirty.value = false;
  };

  /* ─── Toast ─── */
  const toastMsg = ref('');
  const toastType = ref('success');
  const showToast = (msg, type = 'success') => {
    toastMsg.value = msg;
    toastType.value = type;
    setTimeout(() => (toastMsg.value = ''), 3000);
  };

  onMounted(() => {
    if (!can('settings.view')) return router.push('/dashboard');
    fetchMatrix();
  });
</script>

<style scoped>
  /* ════ BASE ════ */
  .perm-view {
    padding-bottom: 6rem;
  }

  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    gap: 1rem;
    flex-wrap: wrap;
  }
  .page-title {
    font-size: 1.75rem;
    font-weight: 900;
    color: #1e293b;
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
    flex-shrink: 0;
  }

  /* ════ CONTROLS BAR ════ */
  .controls-bar {
    display: flex;
    gap: 1rem;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
  }

  .role-tabs {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    flex: 1;
  }

  .role-tab {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 999px;
    border: 1.5px solid #e2e8f0;
    background: white;
    font-size: 0.8125rem;
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
  }
  .role-tab:hover {
    border-color: #cbd5e1;
    color: #1e293b;
    background: #f8fafc;
  }
  .role-tab.active {
    border-color: #0ea5e9;
    background: #f0f9ff;
    color: #0369a1;
  }

  .role-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
  }
  .role-count {
    background: #f1f5f9;
    color: #64748b;
    border-radius: 999px;
    padding: 1px 7px;
    font-size: 0.7rem;
    font-weight: 800;
  }
  .role-tab.active .role-count {
    background: #bae6fd;
    color: #0369a1;
  }

  .search-wrap {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: white;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 0 1rem;
    height: 44px;
    min-width: 260px;
    color: #94a3b8;
    transition: all 0.2s;
  }
  .search-wrap:focus-within {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.08);
  }
  .search-input {
    border: none !important;
    box-shadow: none !important;
    outline: none !important;
    padding: 0 !important;
    background: transparent;
    font-size: 0.875rem;
    color: #1e293b;
    flex: 1;
    min-width: 0;
  }
  .clear-search {
    background: none;
    border: none;
    cursor: pointer;
    color: #94a3b8;
    display: flex;
    align-items: center;
    padding: 2px;
  }
  .clear-search:hover {
    color: #ef4444;
  }

  /* ════ SUPERADMIN BANNER ════ */
  .superadmin-banner {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: linear-gradient(135deg, #fff7ed, #fef3c7);
    border: 1px solid #fcd34d;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    color: #92400e;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
  }

  /* ════ MODULES GRID ════ */
  .modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
    gap: 1.25rem;
  }

  .module-card {
    background: white;
    border-radius: 16px;
    border: 1.5px solid #f1f5f9;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    transition: all 0.2s;
    display: flex;
    flex-direction: column;
  }
  .module-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07);
    border-color: #e2e8f0;
  }

  /* Module Header */
  .module-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.25rem 1rem;
    cursor: pointer;
    user-select: none;
    gap: 0.75rem;
  }
  .module-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 0;
  }
  .module-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
  }
  .module-name {
    font-size: 1rem;
    font-weight: 800;
    color: #1e293b;
    margin: 0;
  }
  .module-meta {
    font-size: 0.75rem;
    color: #94a3b8;
    margin: 0;
    font-weight: 600;
  }

  .module-header-right {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
  }

  .grant-all-btn {
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    border: 1.5px solid #e2e8f0;
    background: white;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
  }
  .grant-all-btn:hover {
    border-color: #0ea5e9;
    color: #0ea5e9;
    background: #f0f9ff;
  }
  .grant-all-btn.active {
    border-color: #ef4444;
    color: #ef4444;
    background: #fff1f2;
  }

  .collapse-chevron {
    color: #cbd5e1;
    transition: transform 0.25s;
    flex-shrink: 0;
  }
  .collapse-chevron.rotated {
    transform: rotate(180deg);
  }

  /* Permissions Grid */
  .permissions-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
    padding: 0 1.25rem;
    padding-bottom: 0.5rem;
  }

  .perm-pill {
    cursor: pointer;
    border-radius: 10px;
    border: 1.5px solid #f1f5f9;
    background: #fafbfc;
    padding: 0.6rem 0.75rem;
    transition: all 0.18s;
    display: block;
  }
  .perm-pill:hover {
    border-color: #e2e8f0;
    background: #f8fafc;
  }
  .perm-pill.granted {
    border-color: #bae6fd;
    background: #f0f9ff;
  }
  .perm-pill.disabled {
    cursor: not-allowed;
    opacity: 0.75;
  }
  .perm-pill input {
    display: none;
  }

  .perm-pill-inner {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .perm-icon {
    font-size: 1rem;
    flex-shrink: 0;
    line-height: 1;
  }
  .perm-text {
    flex: 1;
    min-width: 0;
  }
  .perm-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 700;
    color: #334155;
  }
  .perm-key {
    display: block;
    font-size: 0.65rem;
    color: #94a3b8;
    font-weight: 600;
    letter-spacing: 0.02em;
    margin-top: 1px;
  }
  .perm-pill.granted .perm-label {
    color: #0369a1;
  }
  .perm-pill.granted .perm-key {
    color: #7dd3fc;
  }

  .action-lvl-badge {
    font-size: 0.65rem;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 6px;
    text-transform: uppercase;
    margin-right: 6px;
    margin-left: auto;
  }
  .action-lvl-badge.lvl-low {
    background: #f1f5f9;
    color: #64748b;
  }
  .action-lvl-badge.lvl-med {
    background: #fef08a;
    color: #854d0e;
  }
  .action-lvl-badge.lvl-high {
    background: #fed7aa;
    color: #c2410c;
  }
  .action-lvl-badge.lvl-crit {
    background: #fecdd3;
    color: #be123c;
  }

  .perm-check {
    width: 18px;
    height: 18px;
    border-radius: 5px;
    border: 1.5px solid #e2e8f0;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.15s;
  }
  .perm-check.is-locked {
    background: #f8fafc;
    border-color: #f1f5f9;
    color: #94a3b8;
  }
  .perm-pill.granted .perm-check:not(.is-locked) {
    background: #0ea5e9;
    border-color: #0ea5e9;
    color: white;
  }

  .perm-pill.disabled.locked {
    background: #f8fafc;
    border-color: #f1f5f9;
    cursor: not-allowed;
    opacity: 0.75;
  }
  .perm-pill.disabled.locked:hover {
    border-color: #f1f5f9;
  }

  /* Progress Bar */
  .module-progress {
    padding: 0.75rem 1.25rem 1.25rem;
    margin-top: auto;
  }
  .progress-bar {
    height: 4px;
    background: #f1f5f9;
    border-radius: 999px;
    overflow: hidden;
  }
  .progress-fill {
    height: 100%;
    border-radius: 999px;
    transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* ════ EMPTY STATE ════ */
  .empty-search {
    text-align: center;
    padding: 4rem 2rem;
    color: #94a3b8;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
  }
  .empty-search p {
    font-size: 1rem;
    font-weight: 600;
  }

  /* ════ FLOATING SAVE BAR ════ */
  .floating-save-bar {
    position: fixed;
    bottom: 1.5rem;
    left: 50%;
    transform: translateX(-50%);
    background: #1e293b;
    color: white;
    border-radius: 14px;
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    z-index: 500;
    font-size: 0.875rem;
    font-weight: 600;
    white-space: nowrap;
  }
  .save-bar-actions {
    display: flex;
    gap: 0.75rem;
  }
  .discard-btn {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: transparent;
    color: #94a3b8;
    font-size: 0.8125rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
  }
  .discard-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
  }
  .confirm-save-btn {
    padding: 0.5rem 1.25rem;
    border-radius: 8px;
    border: none;
    background: #0ea5e9;
    color: white;
    font-size: 0.8125rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
  }
  .confirm-save-btn:hover {
    background: #0284c7;
  }
  .confirm-save-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  /* Save bar animation */
  .save-bar-enter-active,
  .save-bar-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .save-bar-enter-from,
  .save-bar-leave-to {
    opacity: 0;
    transform: translateX(-50%) translateY(20px);
  }

  /* ════ RESPONSIVE ════ */
  @media (max-width: 768px) {
    .page-header {
      flex-direction: column;
      align-items: flex-start;
    }
    .controls-bar {
      flex-direction: column;
      align-items: stretch;
    }
    .role-tabs {
      overflow-x: auto;
      flex-wrap: nowrap;
      padding-bottom: 0.25rem;
    }
    .search-wrap {
      min-width: unset;
    }
    .modules-grid {
      grid-template-columns: 1fr;
    }
    .permissions-grid {
      grid-template-columns: 1fr;
    }
    .floating-save-bar {
      left: 1rem;
      right: 1rem;
      transform: none;
      white-space: normal;
      flex-direction: column;
      align-items: flex-start;
      gap: 0.75rem;
    }
    .save-bar-actions {
      width: 100%;
    }
    .discard-btn,
    .confirm-save-btn {
      flex: 1;
    }
  }

  /* ════ RTL ════ */
  .rtl .page-header {
    text-align: right;
  }
  .rtl .perm-text {
    text-align: right;
  }
  .rtl .module-header-left {
    flex-direction: row-reverse;
  }
  .rtl .module-header-right {
    flex-direction: row-reverse;
  }
  .rtl .perm-pill-inner {
    flex-direction: row-reverse;
  }
  .rtl .role-tab {
    flex-direction: row-reverse;
  }
</style>
