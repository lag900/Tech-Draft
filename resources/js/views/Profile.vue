<template>
  <Layout>
    <div class="profile-view" :class="{ 'rtl': isRtl }">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('My Profile', 'الملف الشخصي') }}</h1>
          <p class="page-subtitle">{{ t('Manage your account settings and personal information', 'إدارة إعدادات حسابك ومعلوماتك الشخصية') }}</p>
        </div>
      </div>

      <div class="profile-grid">
        <!-- Left: Basic Info -->
        <div class="profile-main">
          <BaseCard class="profile-section" :title="t('General Information', 'المعلومات العامة')">
            <div class="avatar-upload-area">
               <div class="avatar-preview" @click="$refs.avatarInput.click()">
                   <UserAvatar :user="user" :src="avatarPreview" size="xxl" :custom-name="form.name" />
                   <button class="upload-badge">
                     <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                  </button>
                  <input type="file" ref="avatarInput" name="avatar" @change="handleAvatarChange" hidden accept="image/*">
               </div>
               <div class="avatar-info">
                  <h3>{{ form.name }}</h3>
                  <p>{{ user?.role === 'client' ? form.company_name : user?.role }}</p>
               </div>
            </div>

            <div class="form-grid-2 mt-2">
               <BaseInput v-model="form.name" :label="t('Full Name', 'الاسم الكامل')" required />
               <BaseInput v-model="form.email" :label="t('Email Address', 'البريد الإلكتروني')" type="email" required />
               <BaseInput v-model="form.phone" :label="t('Phone Number', 'رقم الهاتف')" />
               <BaseInput v-model="form.company_name" :label="t('Company Name', 'اسم الشركة')" :disabled="user?.role !== 'client'" />
            </div>

            <template #footer>
               <div class="flex justify-end">
                  <BaseButton :loading="loading" @click="saveProfile">{{ t('Save Changes', 'حفظ التغييرات') }}</BaseButton>
               </div>
            </template>
          </BaseCard>

          <BaseCard class="profile-section mt-2" :title="t('Security', 'الأمان')">
            <div class="form-grid-2">
               <BaseInput v-model="passForm.current_password" type="password" :label="t('Current Password', 'كلمة المرور الحالية')" />
               <div></div><!-- Spacer -->
               <BaseInput v-model="passForm.new_password" type="password" :label="t('New Password', 'كلمة المرور الجديدة')" />
               <BaseInput v-model="passForm.new_password_confirmation" type="password" :label="t('Confirm New Password', 'تأكيد كلمة المرور الجديدة')" />
            </div>
            
            <template #footer>
               <div class="flex justify-end">
                  <BaseButton variant="white" :loading="passLoading" @click="changePassword">{{ t('Update Password', 'تحديث كلمة المرور') }}</BaseButton>
               </div>
            </template>
          </BaseCard>
        </div>

        <!-- Right: Stats/Info (Optional but adds to UI) -->
        <div class="profile-side">
           <BaseCard class="info-card">
              <h4 class="info-title">{{ t('Account Status', 'حالة الحساب') }}</h4>
              <div class="status-row">
                 <span class="status-label">{{ t('Status', 'الحالة') }}</span>
                 <span class="status-badge active">{{ t('Active', 'نشط') }}</span>
              </div>
              <div class="status-row">
                 <span class="status-label">{{ t('Member Since', 'عضو منذ') }}</span>
                 <span class="status-value">{{ formatDate(user?.created_at) }}</span>
              </div>
           </BaseCard>

           <BaseCard class="help-card mt-1">
              <div class="help-icon">
                 <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
              </div>
              <h4>{{ t('Need Help?', 'تحتاج مساعدة؟') }}</h4>
              <p>{{ t('Contact our support team for any issues with your account.', 'تواصل مع فريق الدعم لأي مشكلة في حسابك.') }}</p>
              <BaseButton variant="ghost" size="sm" class="mt-1">{{ t('Contact Support', 'اتصل بالدعم') }}</BaseButton>
           </BaseCard>
        </div>
      </div>
    </div>

    <!-- Alert Modal -->
    <AlertModal 
      :show="alertModal.show" 
      :title="alertModal.title" 
      :message="alertModal.message" 
      :type="alertModal.type" 
      :isRtl="isRtl" 
      @close="alertModal.show = false" 
    />
  </Layout>
</template>

<script setup>
import Layout from '../components/Layout.vue';
import BaseCard from '../components/UI/BaseCard.vue';
import BaseButton from '../components/UI/BaseButton.vue';
import BaseInput from '../components/UI/BaseInput.vue';
import UserAvatar from '../components/UI/UserAvatar.vue';
import AlertModal from '../components/UI/AlertModal.vue';
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';

const user = ref(JSON.parse(localStorage.getItem('user') || '{}'));
const isRtl = computed(() => localStorage.getItem('lang') === 'ar');
const t = (en, ar) => isRtl.value ? ar : en;

const loading = ref(false);
const passLoading = ref(false);
const avatarPreview = ref(null);
const avatarFile = ref(null);

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

const handleAvatarChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    avatarFile.value = file;
    const reader = new FileReader();
    reader.onload = (re) => {
      avatarPreview.value = re.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const form = reactive({
  name: user.value.name || '',
  email: user.value.email || '',
  phone: user.value.client?.phone || '',
  company_name: user.value.client?.company_name || '',
  avatar: user.value.avatar || ''
});

const passForm = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
});

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString(isRtl.value ? 'ar-EG' : 'en-US', {
    member: 'numeric',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const saveProfile = async () => {
  loading.value = true;
  try {
    const formData = new FormData();
    formData.append('name', form.name);
    formData.append('email', form.email);
    formData.append('phone', form.phone);
    formData.append('company_name', form.company_name);
    if (avatarFile.value) {
      formData.append('avatar', avatarFile.value);
    }

    // API call to update profile using the new POST /me/profile endpoint for file support
    const res = await axios.post('/api/me/profile', formData, {
      headers: { 
        Authorization: `Bearer ${localStorage.getItem('auth_token')}`
      }
    });

    localStorage.setItem('user', JSON.stringify(res.data));
    user.value = res.data;
    form.avatar = res.data.avatar;
    avatarFile.value = null;
    avatarPreview.value = null;


    showAlert(t('Profile updated successfully!', 'تم تحديث الملف الشخصي بنجاح!'), '', 'success');
  } catch (e) {
    console.error(e);
    showAlert(t('Failed to update profile.', 'فشل تحديث الملف الشخصي.'), '', 'error');
  }
  loading.value = false;
};

const changePassword = async () => {
  if (!passForm.new_password) return;
  passLoading.value = true;
  try {
    await axios.post('/api/me/change-password', passForm, {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    passForm.current_password = '';
    passForm.new_password = '';
    passForm.new_password_confirmation = '';
    showAlert(t('Password updated successfully!', 'تم تحديث كلمة المرور بنجاح!'), '', 'success');
  } catch (e) {
    showAlert(t('Failed to update password. Check your current password.', 'فشل تحديث كلمة المرور. تأكد من كلمة المرور الحالية.'), '', 'error');
  }
  passLoading.value = false;
};

onMounted(() => {
  const u = JSON.parse(localStorage.getItem('user') || '{}');
  form.name = u.name;
  form.email = u.email;
  form.phone = u.client?.phone || '';
  form.company_name = u.client?.company_name || '';
  form.avatar = u.avatar || '';
});
</script>

<style scoped>
.profile-view { padding-bottom: 4rem; }
.page-header { margin-bottom: 2.5rem; }
.page-title { font-size: 1.75rem; font-weight: 800; color: #1a3a5f; margin: 0; }
.page-subtitle { color: #64748b; font-size: 0.9375rem; margin-top: 0.25rem; }

.profile-grid { display: grid; grid-template-columns: 1fr 340px; gap: 2rem; align-items: flex-start; }
@media (max-width: 1024px) { .profile-grid { grid-template-columns: 1fr; } }

.avatar-upload-area { display: flex; align-items: center; gap: 2rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 2rem; margin-bottom: 1rem; }
.avatar-preview { position: relative; cursor: pointer; }
.avatar-preview img { width: 100px; height: 100px; border-radius: 24px; object-fit: cover; border: 4px solid white; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
.upload-badge { position: absolute; bottom: -5px; right: -5px; background: #0ea5e9; color: white; border-radius: 12px; border: 3px solid white; padding: 6px; cursor: pointer; display: flex; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }

.avatar-info h3 { font-size: 1.25rem; font-weight: 800; color: #1a3a5f; margin: 0; }
.avatar-info p { color: #64748b; font-size: 0.875rem; font-weight: 600; margin-top: 0.25rem; }

.mt-2 { margin-top: 2rem; }
.mt-1 { margin-top: 1rem; }
.flex { display: flex; }
.justify-end { justify-content: flex-end; }

.status-row { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; font-size: 0.875rem; }
.status-row:not(:last-child) { border-bottom: 1px solid #f8fafc; }
.status-label { color: #64748b; font-weight: 600; }
.status-value { color: #1a3a5f; font-weight: 700; }
.status-badge { font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; }
.status-badge.active { background: #f0fdf4; color: #16a34a; }

.info-title { font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem; }
.help-card { text-align: center; padding: 2rem 1.5rem; background: #f0f9ff !important; border-color: #e0f2fe !important; }
.help-icon { color: #0ea5e9; margin-bottom: 1rem; }
.help-card h4 { font-weight: 800; color: #1a3a5f; margin-bottom: 0.5rem; }
.help-card p { font-size: 0.8125rem; color: #64748b; line-height: 1.5; }

.rtl .avatar-upload-area { flex-direction: row; }
.rtl .upload-badge { right: auto; left: -5px; }
.rtl .justify-end { justify-content: flex-start; }
</style>
