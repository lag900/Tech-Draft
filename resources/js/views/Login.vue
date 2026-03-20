<template>
  <div class="auth-page" :class="{ 'rtl': isRtl }">
    <div class="auth-card">
      <div class="auth-logo">
        <img src="../../images/logo.png" alt="TechDraft" class="logo-img">
        <h2 class="logo-text">TechDraft</h2>
      </div>
      
      <h1>{{ t('Welcome Back', 'مرحباً بعودتك') }}</h1>
      <p>{{ t('Log in to manage your manufacturing orders', 'قم بتسجيل الدخول لإدارة طلبات التصنيع الخاصة بك') }}</p>
      
      <form @submit.prevent="login">
        <div class="form-group">
          <label>{{ t('Email Address', 'البريد الإلكتروني') }}</label>
          <input v-model="form.email" type="email" required placeholder="name@company.com">
        </div>
        
        <div class="form-group">
          <label>{{ t('Password', 'كلمة المرور') }}</label>
          <div class="password-input-wrapper">
            <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required placeholder="••••••••">
            <button type="button" @click="showPassword = !showPassword" class="password-toggle">
              <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
            </button>
          </div>
        </div>

        <div v-if="error" class="error-msg">{{ error }}</div>

        <button type="submit" class="primary-btn w-full" :disabled="loading">
          {{ loading ? t('Logging in...', 'جاري الدخول...') : t('Sign In', 'تسجيل الدخول') }}
        </button>
      </form>

      <div class="auth-footer">
        {{ t("Don't have an account?", 'ليس لديك حساب؟') }} 
        <router-link to="/register">{{ t('Register here', 'اشترك هنا') }}</router-link>
      </div>

      <button @click="toggleLang" class="lang-switch">
        {{ isRtl ? 'English Version' : 'النسخة العربية' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { useLang } from '../composables/useLang';
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const loading = ref(false);
const showPassword = ref(false);
const error = ref('');
const { isRtl, t, toggleLang } = useLang();

const form = reactive({
  email: '',
  password: ''
});

// toggleLang handled by useLang

const login = async () => {
  loading.value = true;
  error.value = '';
  try {
    const response = await axios.post('/api/login', form);
    localStorage.setItem('auth_token', response.data.access_token);
    
    const user = response.data.user;
    
    // Fetch role permissions and merge into user object
    try {
      const rolesRes = await axios.get('/api/permissions', {
        headers: { Authorization: `Bearer ${response.data.access_token}` }
      });
      const userRole = rolesRes.data.find(r => r.slug === user.role);
      if (userRole) {
        user.permissions = userRole.permissions || [];
      }
    } catch (e) {
      // Permissions will fall back to static defaults
    }
    
    localStorage.setItem('user', JSON.stringify(user));
    
    // Redirect clients to create order
    if (user.role === 'client') {
      router.push('/orders/create');
    } else {
      router.push('/dashboard');
    }
  } catch (err) {
    error.value = err.response?.data?.message || t('Invalid email or password', 'البريد الإلكتروني أو كلمة المرور غلط');
  } finally {
    loading.value = false;
  }
};

// init handled by useLang
</script>

<style scoped>
.auth-page {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: 1rem;
  background: #f8fafc;
  font-family: 'Tajawal', 'Instrument Sans', sans-serif;
}

.auth-card {
  max-width: 420px;
  width: 100%;
  background: white;
  padding: 3rem 2.5rem;
  border-radius: 20px;
  box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
  text-align: center;
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

h1 { font-size: 1.5rem; margin-bottom: 0.5rem; font-weight: 800; color: #1a3a5f; }
p { color: #64748b; margin-bottom: 2rem; font-size: 0.9375rem; }

.form-group { text-align: left; margin-bottom: 1.5rem; }
.rtl .form-group { text-align: right; }

label { display: block; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.875rem; color: #475569; }
input { width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 10px; background: #fdfdfd; }
input:focus { border-color: #0ea5e9; outline: none; box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1); }

.password-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.password-toggle {
  position: absolute;
  right: 1rem;
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s;
}

.rtl .password-toggle {
  right: auto;
  left: 1rem;
}

.password-toggle:hover {
  color: #64748b;
}

.password-input-wrapper input {
  padding-right: 3rem;
}

.rtl .password-input-wrapper input {
  padding-right: 1rem;
  padding-left: 3rem;
}

.primary-btn { 
  background: #0ea5e9; 
  color: white; 
  font-weight: 800; 
  padding: 1rem; 
  border-radius: 12px; 
  border: none; 
  cursor: pointer; 
  transition: all 0.2s;
}
.primary-btn:hover:not(:disabled) { background: #0284c7; transform: translateY(-1px); }
.primary-btn:disabled { opacity: 0.7; cursor: not-allowed; }

.w-full { width: 100%; }
.error-msg { color: #ef4444; margin-bottom: 1rem; font-size: 0.8125rem; font-weight: 600; }
.auth-footer { margin-top: 2rem; font-size: 0.9375rem; color: #64748b; }
.auth-footer a { color: #0ea5e9; text-decoration: none; font-weight: 700; }

.lang-switch {
  margin-top: 2.5rem;
  background: none;
  border: none;
  color: #94a3b8;
  font-size: 0.8125rem;
  font-weight: 600;
  cursor: pointer;
  text-decoration: underline;
}

.rtl { direction: rtl; }
</style>
