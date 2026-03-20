<template>
  <Layout>
    <div class="settings-view" :class="{ 'rtl': isRtl }">
      <div class="page-header">
         <h1 class="page-title">{{ t('System Settings', 'إعدادات النظام') }}</h1>
         <p class="page-subtitle">{{ t('Manage platform configuration, roles, and security policies', 'إدارة تكوين المنصة، الأدوار، وسياسات الأمان') }}</p>
      </div>

      <div class="settings-grid">
         <aside class="settings-nav">
            <button v-for="tab in tabs" :key="tab.id" 
                    @click="activeTab = tab.id"
                    :class="['nav-btn', { active: activeTab === tab.id }]">
               <component :is="tab.icon" class="btn-icon" />
               {{ t(tab.labelEn, tab.labelAr) }}
            </button>
         </aside>

         <main class="settings-content">
            <BaseCard v-if="activeTab === 'general'" :title="t('Platform Configuration', 'تكوين المنصة')">
               <div class="form-grid">
                  <div class="field-item">
                     <label>{{ t('Platform Name', 'اسم المنصة') }}</label>
                     <input type="text" value="TechDraft Manufacturing">
                  </div>
                  <div class="field-item">
                     <label>{{ t('Support Email', 'بريد الدعم') }}</label>
                     <input type="email" value="support@techdraft.io">
                  </div>
               </div>
               <template #footer>
                  <div class="footer-actions">
                     <BaseButton>{{ t('Save Changes', 'حفظ التغييرات') }}</BaseButton>
                  </div>
               </template>
            </BaseCard>

            <div v-if="activeTab === 'rbac'" class="rbac-settings">
               <div class="rbac-cards">
                  <BaseCard :title="t('Security Access Matrix', 'مصفوفة الوصول الأمني')" subtitle="Map modules and actions to specific roles">
                     <template #actions>
                        <BaseButton variant="ghost" size="sm" @click="$router.push('/settings/permissions')">{{ t('Open Matrix', 'فتح المصفوفة') }}</BaseButton>
                     </template>
                     <p class="settings-desc">{{ t('Configure granular permissions for each role across all system modules.', 'تكوين أذونات دقيقة لكل دور عبر جميع وحدات النظام.') }}</p>
                  </BaseCard>

                  <BaseCard :title="t('Identity Registry (Roles)', 'سجل الهويات (الأدوار)')" subtitle="Manage role descriptions and role-user mapping">
                     <template #actions>
                        <BaseButton variant="ghost" size="sm" @click="$router.push('/settings/roles')">{{ t('Manage Roles', 'إدارة الأدوار') }}</BaseButton>
                     </template>
                     <p class="settings-desc">{{ t('Edit role details, descriptions, and view how many users are assigned to each identity.', 'تحرير تفاصيل الأدوار، الأوصاف، وعرض عدد المستخدمين المخصصين لكل هوية.') }}</p>
                  </BaseCard>
               </div>
            </div>
         </main>
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
import { ref, computed, h, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const can = (perm) => hasPermission(user.value, perm);

const activeTab = ref('general');
const { isRtl, t } = useLang();

onMounted(() => {
  if (!can('settings.view')) router.push('/dashboard');
});

const IconSettings = () => h('svg', { width: 18, height: 18, viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': 2 }, [h('path', { d: 'M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.72V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z' }), h('circle', { cx: 12, cy: 12, r: 3 })]);
const IconShield = () => h('svg', { width: 18, height: 18, viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': 2 }, [h('path', { d: 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z' })]);

const tabs = [
   { id: 'general', labelEn: 'General Settings', labelAr: 'الإعدادات العامة', icon: IconSettings },
   { id: 'rbac', labelEn: 'RBAC & Identity', labelAr: 'الأدوار والهويات', icon: IconShield },
];
</script>

<style scoped>
.page-header { margin-bottom: 2rem; }
.page-title { font-size: 1.75rem; font-weight: 800; color: #1a3a5f; margin: 0; }
.page-subtitle { color: #64748b; font-size: 0.9375rem; margin-top: 0.25rem; }

.settings-grid { display: grid; grid-template-columns: 240px 1fr; gap: 2.5rem; }
@media (max-width: 768px) { .settings-grid { grid-template-columns: 1fr; } }

.settings-nav { display: flex; flex-direction: column; gap: 0.5rem; }
.nav-btn { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: 12px; background: transparent; color: #64748b; font-weight: 700; text-align: left; transition: all 0.2s; border: none; cursor: pointer; }
.nav-btn:hover { background: #f8fafc; color: #1a3a5f; }
.nav-btn.active { background: #eff6ff; color: #0ea5e9; }
.btn-icon { opacity: 0.7; }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.field-item label { display: block; font-size: 0.8125rem; font-weight: 700; color: #475569; margin-bottom: 0.5rem; }
.field-item input { width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.9375rem; color: #1a3a5f; }

.footer-actions { display: flex; justify-content: flex-end; }

.rbac-cards { display: flex; flex-direction: column; gap: 1.5rem; }
.settings-desc { font-size: 0.875rem; color: #64748b; line-height: 1.5; }

.rtl .page-header, .rtl .nav-btn { text-align: right; }
.rtl .nav-btn { flex-direction: row; } /* Standard flex row is fine, but gap needs to keep consistency */
.rtl .footer-actions { justify-content: flex-start; }
</style>
