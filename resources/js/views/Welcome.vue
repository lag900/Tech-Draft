<template>
  <div class="welcome-container" :class="{ 'rtl': isRtl }">
    <!-- Navigation Bar -->
    <nav class="navbar">
      <div class="container nav-content">
        <div class="logo-group">
          <div class="logo-icon-bg">
            <img src="../../images/logo.png" alt="TechDraft Logo" class="nav-logo" @error="handleLogoError">
          </div>
          <span class="logo-text">TechDraft</span>
        </div>
        
        <div class="nav-actions">
           <template v-if="user">
              <span class="user-greeting" v-if="!isRtl">Hi, {{ user.name.split(' ')[0] }}</span>
              <button @click="logout" class="nav-link-btn">{{ t('Logout', 'تسجيل خروج') }}</button>
              <router-link :to="user.role === 'client' ? '/orders' : '/dashboard'" class="primary-btn">
                {{ t('Go to App', 'انتقل للتطبيق') }}
              </router-link>
           </template>
           <template v-else>
              <router-link to="/login" class="nav-link">{{ t('Login', 'تسجيل الدخول') }}</router-link>
              <router-link to="/register" class="primary-btn">{{ t('Register Now', 'اشترك الآن') }}</router-link>
           </template>
           <button @click="toggleLang" class="lang-toggle-minimal">
             {{ isRtl ? 'EN' : 'AR' }}
           </button>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container hero-grid">
        <div class="hero-content">
          <h1 class="hero-title">{{ t('Clothing Manufacturing Platform', 'منصة تصنيع الملابس الأولى') }}</h1>
          <p class="hero-subtitle">
            {{ t('Manage clothing production between brands and factories easily. The ultimate digital pattern & tech pack platform.', 'أدر عملية تصنيع الملابس بين العلامات التجارية والمصانع بكل سهولة. منصة الملفات الفنية والقوالب الرقمية المتكاملة.') }}
          </p>
          <div class="hero-btns">
            <router-link :to="user ? '/orders/create' : '/register'" class="primary-btn-lg">{{ t('Create Manufacturing Order', 'إنشاء طلب تصنيع') }}</router-link>
            <router-link to="/register" class="secondary-btn-lg" v-if="!user">{{ t('Join as Brand', 'انضم كعلامة تجارية') }}</router-link>
          </div>
        </div>
        <div class="hero-visual">
          <!-- Recreating that tech-pack feel visually -->
          <div class="visual-card animate-float">
             <div class="pattern-header">
                <span class="dot-red"></span>
                <span class="dot-yellow"></span>
                <span class="dot-green"></span>
             </div>
             <div class="pattern-body">
                <svg viewBox="0 0 200 200" fill="none" class="pattern-svg">
                  <path d="M50 40 L150 40 L160 160 L40 160 Z" stroke="#0EA5E9" stroke-width="2" stroke-dasharray="4 4"/>
                  <path d="M50 40 Q100 20 150 40" stroke="#0EA5E9" stroke-width="2"/>
                  <circle cx="100" cy="100" r="50" stroke="#E0F2FE" stroke-width="1" fill="#E0F2FE" fill-opacity="0.2"/>
                  <line x1="100" y1="40" x2="100" y2="160" stroke="#0EA5E9" stroke-width="0.5"/>
                  <line x1="40" y1="100" x2="160" y2="100" stroke="#0EA5E9" stroke-width="0.5"/>
                </svg>
                <div class="spec-label">{{ t('Digital Pattern', 'قالب رقمي') }}</div>
             </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
      <div class="container">
        <div class="section-header">
           <h2>{{ t('Everything You Need to Scale', 'كل ما تحتاجه لتوسيع أعمالك') }}</h2>
           <p>{{ t('Built for professional clothing brands and modern factories.', 'مصممة خصيصاً لماركات الملابس الاحترافية والمصانع العصرية.') }}</p>
        </div>
        
        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 15.5 2 2"/><path d="m11 12 2 2"/><path d="m14.5 8.5 2 2"/><path d="M17.61 2.81a3 3 0 1 1 4.24 4.24l-14.85 14.85a3 3 0 0 1-2.12.88L2 23l.12-2.88a3 3 0 0 1 .88-2.12L17.61 2.81Z"/></svg>
            </div>
            <h3>{{ t('Create Manufacturing Orders', 'إنشاء طلبات تصنيع') }}</h3>
            <p>{{ t('Upload designs and measurements with detailed tech packs for precise production.', 'ارفع تصميماتك وقياساتك مع ملفات فنية مفصلة لضمان دقة الإنتاج.') }}</p>
          </div>
          
          <div class="feature-card">
            <div class="feature-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C10 14.5 12 15 15 15"/></svg>
            </div>
            <h3>{{ t('Fabric and Color Selection', 'اختيار الأقمشة والألوان') }}</h3>
            <p>{{ t('Choose materials and colors accurately using Pantone codes and texture details.', 'اختر الأنماط والألوان بدقة باستعمال أكواد بانتون وتفاصيل الخامات.') }}</p>
          </div>
          
          <div class="feature-card">
            <div class="feature-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
            </div>
            <h3>{{ t('Track Order Status', 'تتبع حالة الطلب') }}</h3>
            <p>{{ t('Monitor production progress in real-time from pattern making to final delivery.', 'راقب تقدم الإنتاج في الوقت الفعلي من مرحلة القالب حتى التسليم النهائي.') }}</p>
          </div>
          
          <div class="feature-card">
            <div class="feature-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <h3>{{ t('Direct Factory Communication', 'تواصل مباشر مع المصنع') }}</h3>
            <p>{{ t('Chat directly with manufacturers to clarify details and ensure quality standards.', 'تواصل مباشرة مع المصنعين لتوضيح التفاصيل وضمان معايير الجودة.') }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works">
      <div class="container">
        <div class="section-header">
           <h2>{{ t('How TechDraft Works', 'كيف تعمل المنصة') }}</h2>
        </div>
        
        <div class="steps-grid">
          <div class="step-item">
            <div class="step-number">1</div>
            <h4>{{ t('Create your brand account', 'أنشئ حساباً لعلامتك التجارية') }}</h4>
            <p>{{ t('Sign up and set up your brand profile in minutes.', 'سجل الآن وأنشئ بروفايل علامتك التجارية في دقائق.') }}</p>
          </div>
          <div class="step-item">
            <div class="step-number">2</div>
            <h4>{{ t('Submit manufacturing orders', 'أرسل طلبات التصنيع') }}</h4>
            <p>{{ t('Fill in technical specs and upload your beautiful designs.', 'أدخل المواصفات الفنية وارفع تصميماتك الرائعة.') }}</p>
          </div>
          <div class="step-item">
            <div class="step-number">3</div>
            <h4>{{ t('Communicate & Track', 'تواصل وتتبع') }}</h4>
            <p>{{ t('Chat with factories and watch your design come to life.', 'تواصل مع المصانع وشاهد تصميماتك تتحول لواقع ملموس.') }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-card">
          <h2 style="color: white;">{{ t('Start managing your clothing production today.', 'ابدأ في إدارة إنتاج ملابسك اليوم.') }}</h2>
          <router-link to="/register" class="white-btn-lg">{{ t('Register Now', 'اشترك الآن') }}</router-link>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="container footer-content">
        <div class="footer-brand">
          <div class="logo-group">
            <img src="../../images/logo.png" alt="Logo" class="footer-logo">
            <span class="logo-text">TechDraft</span>
          </div>
          <p>© 2026 TechDraft. {{ t('All rights reserved.', 'جميع الحقوق محفوظة.') }}</p>
        </div>
        <div class="footer-links">
          <a href="#">{{ t('Privacy', 'الخصوصية') }}</a>
          <a href="#">{{ t('Terms', 'الشروط') }}</a>
          <a href="#">{{ t('Contact', 'اتصل بنا') }}</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

import { useLang } from '../composables/useLang';

const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const { isRtl, t, toggleLang } = useLang();

const logout = () => {
  localStorage.clear();
  window.location.reload();
};

const handleLogoError = (e) => {
  // If no logo image exists yet, we show the emoji icon as fallback
  e.target.style.display = 'none';
};

// Initialization handled by useLang
</script>

<style scoped>
.welcome-container {
  font-family: 'Instrument Sans', 'Noto Sans Arabic', sans-serif;
  background-color: white;
  color: #1a3a5f;
  overflow-x: hidden;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

/* Navbar */
.navbar {
  height: 80px;
  display: flex;
  align-items: center;
  border-bottom: 1px solid #f1f5f9;
  position: sticky;
  top: 0;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  z-index: 100;
}

.nav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
}

.logo-group {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.nav-logo {
  height: 42px;
}

.logo-text {
  font-size: 1.5rem;
  font-weight: 800;
  color: #1a3a5f;
  letter-spacing: -0.02em;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.nav-link {
  text-decoration: none;
  font-weight: 600;
  color: #64748b;
  font-size: 0.9375rem;
}

.primary-btn {
  background: #0ea5e9;
  color: white;
  padding: 0.625rem 1.25rem;
  border-radius: 99px;
  text-decoration: none;
  font-weight: 700;
  font-size: 0.9375rem;
  transition: all 0.2s;
}

.primary-btn:hover {
  background: #0284c7;
  transform: translateY(-1px);
}

/* Hero Section */
.hero-section {
  padding: 6rem 0;
  background: linear-gradient(180deg, #f8fafc 0%, white 100%);
}

.hero-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
}

@media (max-width: 768px) {
  .hero-grid { grid-template-columns: 1fr; text-align: center; }
  .hero-btns { justify-content: center; }
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 900;
  line-height: 1.1;
  color: #1a3a5f;
  margin-bottom: 1.5rem;
}

.hero-subtitle {
  font-size: 1.25rem;
  color: #64748b;
  line-height: 1.6;
  margin-bottom: 2.5rem;
  max-width: 500px;
}

.hero-btns {
  display: flex;
  gap: 1rem;
}

.primary-btn-lg {
  background: #0ea5e9;
  color: white;
  padding: 1rem 2rem;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 800;
  font-size: 1.125rem;
  transition: all 0.2s;
  box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.3);
}

.secondary-btn-lg {
  background: white;
  color: #1a3a5f;
  padding: 1rem 2rem;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 800;
  font-size: 1.125rem;
  border: 2px solid #e2e8f0;
  transition: all 0.2s;
}

.visual-card {
  background: white;
  border-radius: 24px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  border: 1px solid #f1f5f9;
}

.pattern-header {
  display: flex;
  gap: 6px;
  margin-bottom: 2rem;
}

.dot-red { width: 12px; height: 12px; background: #ef4444; border-radius: 50%; }
.dot-yellow { width: 12px; height: 12px; background: #f59e0b; border-radius: 50%; }
.dot-green { width: 12px; height: 12px; background: #10b981; border-radius: 50%; }

.pattern-body {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.pattern-svg {
  width: 100%;
  max-width: 300px;
}

.spec-label {
  margin-top: 1.5rem;
  font-family: monospace;
  font-size: 0.75rem;
  background: #f1f5f9;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  color: #94a3b8;
}

/* Features */
.features-section {
  padding: 6rem 0;
}

.section-header {
  text-align: center;
  margin-bottom: 4rem;
}

.section-header h2 {
  font-size: 2.25rem;
  font-weight: 800;
  margin-bottom: 1rem;
}

.section-header p {
  color: #64748b;
  font-size: 1.125rem;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
}

.feature-card {
  padding: 2.5rem;
  background: white;
  border-radius: 20px;
  border: 1px solid #f1f5f9;
  transition: all 0.3s;
}

.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
  border-color: #e0f2fe;
}

.feature-icon {
  font-size: 2rem;
  margin-bottom: 1.5rem;
}

.feature-card h3 {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.feature-card p {
  color: #64748b;
  line-height: 1.6;
  font-size: 0.9375rem;
}

/* How It Works */
.how-it-works {
  padding: 6rem 0;
  background: #f8fafc;
}

.steps-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 3rem;
}

@media (max-width: 768px) {
  .steps-grid { grid-template-columns: 1fr; }
}

.step-item {
  text-align: center;
}

.step-number {
  width: 48px;
  height: 48px;
  background: #0ea5e9;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  margin: 0 auto 1.5rem;
  box-shadow: 0 4px 10px rgba(14, 165, 233, 0.3);
}

.step-item h4 {
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
}

.step-item p {
  color: #64748b;
  font-size: 0.9375rem;
}

/* CTA Section */
.cta-section {
  padding: 6rem 0;
}

.cta-card {
  background: #1a3a5f;
  border-radius: 32px;
  padding: 4rem;
  text-align: center;
  color: white;
  position: relative;
  overflow: hidden;
}

.cta-card h2 {
  font-size: 2.5rem;
  font-weight: 800;
  margin-bottom: 2.5rem;
}

.white-btn-lg {
  background: white;
  color: #1a3a5f;
  padding: 1.25rem 2.5rem;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 800;
  font-size: 1.25rem;
  transition: all 0.2s;
  display: inline-block;
}

.white-btn-lg:hover {
  transform: scale(1.05);
}

/* Footer */
.footer {
  padding: 4rem 0;
  border-top: 1px solid #f1f5f9;
}

.footer-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

@media (max-width: 640px) {
  .footer-content { flex-direction: column; gap: 2rem; text-align: center; }
}

.footer-logo { height: 32px; }

.footer-brand p {
  color: #94a3b8;
  font-size: 0.875rem;
  margin-top: 1rem;
}

.footer-links {
  display: flex;
  gap: 2rem;
}

.footer-links a {
  text-decoration: none;
  color: #64748b;
  font-size: 0.875rem;
  font-weight: 600;
}

/* RTL Support */
.rtl { direction: rtl; }
.rtl .hero-visual { text-align: left; }
.rtl .primary-btn { padding: 0.625rem 1.5rem; }

@keyframes float {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
  100% { transform: translateY(0px); }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.lang-toggle-minimal {
  background: none;
  border: 1px solid #e2e8f0;
  color: #64748b;
  padding: 4px 8px;
  border-radius: 6px;
  font-weight: 700;
  font-size: 0.75rem;
  cursor: pointer;
}
</style>
