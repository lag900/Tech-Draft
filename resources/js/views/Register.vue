<template>
  <div class="auth-page px-1" :class="{ 'rtl': isRtl }">
    <div class="auth-card" :class="{ 'client-wide': form?.role === 'client' }">
      <div class="auth-logo">
        <img src="../../images/logo.png" alt="TechDraft" class="logo-img">
        <h2 class="logo-text">TechDraft</h2>
      </div>

      <h1>{{ t('Create Account', 'إنشاء حساب جديد') }}</h1>
      <p>{{ t('Join our clothing manufacturing platform', 'انضم لمنصة تصنيع الملابس الأولى') }}</p>
      <form @submit.prevent="register">
        <!-- Simple Client Info -->
        <div class="register-sections animate-slide">
           <div class="register-section">
              <div class="form-grid-2">
                 <div class="form-group">
                    <label>{{ t('Full Name', 'الاسم الكامل') }}</label>
                    <input v-model="form.name" type="text" required :placeholder="t('Ex: John Doe', 'مثال: أحمد محمد')">
                 </div>
                 <div class="form-group">
                    <label>{{ t('Brand Name', 'اسم البراند') }}</label>
                    <input v-model="form.brand_name" type="text" required :placeholder="t('Brand Name', 'اسم البراند')">
                 </div>
                  <div class="form-group">
                     <label>{{ t('Company Name', 'اسم الشركة') }}</label>
                     <input v-model="form.company_name" type="text" :placeholder="t('Official company name (Optional)', 'اسم الشركة الرسمي (اختياري)')">
                  </div>
                 <div class="form-group">
                    <label>{{ t('Email Address', 'البريد الإلكتروني') }}</label>
                    <input v-model="form.email" type="email" required placeholder="name@company.com">
                 </div>
                 <div class="form-group">
                    <label>{{ t('Phone Number', 'رقم الهاتف') }}</label>
                    <input v-model="form.phone" type="text" required placeholder="+20 123 456 789">
                 </div>
                  <div class="form-group grid-span-all">
                     <SearchableSelect
                        v-model="form.country"
                        :options="localizedCountries"
                        :label="t('Country', 'الدولة')"
                        :placeholder="t('Select your country', 'اختر الدولة')"
                        :is-rtl="isRtl"
                        clearable
                     />
                  </div>
              </div>
           </div>
        </div>

        <!-- Password Section (Common) -->
        <div class="register-section mt-2">
           <h3 class="section-title-auth">{{ t('Security', 'الأمان') }}</h3>
           <div class="form-grid-2">
              <div class="form-group">
                <label>{{ t('Password', 'كلمة المرور') }}</label>
                <div class="password-input-wrapper">
                  <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required placeholder="••••••••">
                  <button type="button" @click="showPassword = !showPassword" class="password-toggle">
                    <svg v-if="!showPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                  </button>
                </div>
              </div>
              <div class="form-group">
                <label>{{ t('Confirm Password', 'تأكيد كلمة المرور') }}</label>
                <div class="password-input-wrapper">
                  <input v-model="form.password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" required placeholder="••••••••">
                  <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="password-toggle">
                    <svg v-if="!showConfirmPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                  </button>
                </div>
              </div>
           </div>
        </div>

        <div v-if="error" class="error-msg">{{ error }}</div>

        <button type="submit" class="primary-btn w-full btn-lg mt-3" :disabled="loading">
          {{ loading ? t('Registering...', 'جاري التسجيل...') : t('Sign Up', 'إنشاء حساب') }}
        </button>
      </form>

      <div class="auth-footer">
        {{ t('Already have an account?', 'لديك حساب بالفعل؟') }} 
        <router-link to="/login">{{ t('Login', 'سجل دخولك هنا') }}</router-link>
      </div>

      <button @click="toggleLang" class="lang-switch">
        {{ isRtl ? 'English Version' : 'النسخة العربية' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import SearchableSelect from '../components/UI/SearchableSelect.vue';
import { countries } from '../utils/countries';

const router = useRouter();
const loading = ref(false);
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const error = ref('');
if (!localStorage.getItem('lang')) localStorage.setItem('lang', 'ar');
const isRtl = ref(localStorage.getItem('lang') === 'ar');

const t = (en, ar) => isRtl.value ? ar : en;

const localizedCountries = computed(() => {
  return countries.map(c => ({
    value: c.value,
    name: isRtl.value ? c.ar : c.en
  }));
});

const form = reactive({
  name: '',
  email: '',
  role: 'client',
  brand_name: '',
  company_name: '',
  phone: '',
  country: 'EG',
  password: '',
  password_confirmation: ''
});

const toggleLang = () => {
  isRtl.value = !isRtl.value;
  localStorage.setItem('lang', isRtl.value ? 'ar' : 'en');
  document.documentElement.dir = isRtl.value ? 'rtl' : 'ltr';
};

const register = async () => {
  loading.value = true;
  error.value = '';
  try {
    const response = await axios.post('/api/register', form);
    localStorage.setItem('auth_token', response.data.access_token);
    localStorage.setItem('user', JSON.stringify(response.data.user));
    
    // Redirect clients to create order
    if (response.data.user.role === 'client') {
      router.push('/orders/create');
    } else {
      router.push('/dashboard');
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Registration failed';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  document.documentElement.dir = isRtl.value ? 'rtl' : 'ltr';
});
</script>

<style scoped>
.auth-page {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: 4rem 1rem;
  background: #f8fafc;
  font-family: 'Instrument Sans', 'Noto Sans Arabic', sans-serif;
}

.auth-card {
  max-width: 500px;
  width: 100%;
  background: white;
  padding: 3.5rem 3rem;
  border-radius: 24px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
  text-align: center;
  transition: max-width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.auth-card.client-wide {
   max-width: 800px;
}

.auth-logo {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 2rem;
}

.logo-img { height: 60px; }
.logo-text { font-size: 1.25rem; font-weight: 800; color: #1a3a5f; margin: 0; }

h1 { font-size: 1.75rem; font-weight: 800; color: #1a3a5f; margin-bottom: 0.5rem; }
p { color: #64748b; margin-bottom: 2.5rem; }

.section-title-auth {
   text-align: left;
   font-size: 0.875rem;
   font-weight: 800;
   color: #94a3b8;
   text-transform: uppercase;
   letter-spacing: 0.05em;
   margin-bottom: 1.25rem;
   padding-bottom: 0.5rem;
   border-bottom: 1px solid #f1f5f9;
}
.rtl .section-title-auth { text-align: right; }

.role-selector {
   display: grid;
   grid-template-columns: 1fr 1fr;
   gap: 1rem;
   margin-bottom: 2rem;
}

.role-option {
   padding: 1.25rem;
   border: 2px solid #f1f5f9;
   border-radius: 16px;
   cursor: pointer;
   display: flex;
   gap: 1rem;
   align-items: center;
   text-align: left;
   transition: all 0.2s;
}
.rtl .role-option { text-align: right; direction: rtl; }

.role-option:hover { border-color: #e2e8f0; background: #fafbfc; }
.role-option.active { border-color: #0ea5e9; background: #f0f9ff; }

.role-icon { font-size: 1.5rem; }
.role-name { font-weight: 800; font-size: 0.9375rem; color: #1a3a5f; }
.role-desc { font-size: 0.75rem; color: #94a3b8; font-weight: 600; }

.form-group { text-align: left; margin-bottom: 1.25rem; }
.rtl .form-group { text-align: right; }

label { display: block; margin-bottom: 0.5rem; font-weight: 700; color: #475569; font-size: 0.8125rem; }
input, .auth-select { width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 12px; background: #fdfdfd; font-size: 0.9375rem; color: #1a3a5f; }
input:focus, .auth-select:focus { border-color: #0ea5e9; outline: none; box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1); }

.form-grid-2 {
   display: grid;
   grid-template-columns: 1fr 1fr;
   gap: 1rem;
   align-items: end;
}
.grid-span-all {
   grid-column: span 2;
}
@media (max-width: 640px) { 
   .form-grid-2 { grid-template-columns: 1fr; } 
   .grid-span-all { grid-column: span 1; }
}

.password-input-wrapper { position: relative; display: flex; align-items: center; }
.password-toggle { position: absolute; right: 0.75rem; background: none; border: none; color: #94a3b8; cursor: pointer; display: flex; align-items: center; z-index: 10; }
.rtl .password-toggle { right: auto; left: 0.75rem; }
.password-input-wrapper input { padding-right: 2.5rem; }
.rtl .password-input-wrapper input { padding-right: 1rem; padding-left: 2.5rem; }

.primary-btn { background: #0ea5e9; color: white; padding: 1rem; border-radius: 12px; font-weight: 800; border: none; cursor: pointer; transition: all 0.2s; }
.primary-btn:hover:not(:disabled) { background: #0284c7; transform: translateY(-1px); box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.3); }
.primary-btn:disabled { opacity: 0.7; cursor: not-allowed; }

.btn-lg { padding: 1.125rem; font-size: 1.125rem; }
.mt-2 { margin-top: 2rem; }
.mt-3 { margin-top: 3rem; }
.w-full { width: 100%; }

.error-msg { color: #ef4444; margin-bottom: 1.5rem; font-size: 0.8125rem; font-weight: 600; }
.auth-footer { margin-top: 2rem; color: #64748b; font-size: 0.9375rem; }
.auth-footer a { color: #0ea5e9; text-decoration: none; font-weight: 700; }

.lang-switch { margin-top: 2.5rem; background: none; border: none; color: #94a3b8; font-size: 0.8125rem; font-weight: 600; cursor: pointer; text-decoration: underline; }
.rtl { direction: rtl; }

@keyframes slide { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide { animation: slide 0.3s ease-out; }
</style>
