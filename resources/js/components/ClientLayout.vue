<template>
  <div class="client-portal" :class="{ rtl: isRtl }">
    <!-- Top Navigation Bar -->
    <header class="client-navbar no-print">
      <div class="nav-container">
        <!-- Logo -->
        <div class="nav-brand-group">
          <router-link to="/" class="nav-brand">
            <img src="../../images/logo.png" alt="TechDraft" class="portal-logo" />
            <span class="logo-text">TechDraft</span>
          </router-link>
        </div>

        <!-- Navigation Links (Desktop) -->
        <nav class="nav-links">
          <router-link to="/dashboard" class="portal-link">
            {{ t('Dashboard', 'لوحة التحكم') }}
          </router-link>
          <router-link to="/orders" class="portal-link">
            {{ t('My Orders', 'طلباتي') }}
          </router-link>
          <router-link to="/products" class="portal-link">
            {{ t('My Products', 'منتجاتي') }}
          </router-link>
          <router-link to="/designs" class="portal-link">
            {{ t('Designs', 'التصاميم') }}
          </router-link>
        </nav>

        <!-- Right Actions -->
        <div class="nav-actions">
          <button class="lang-minimal-btn" @click="toggleLang">
            {{ isRtl ? 'EN' : 'AR' }}
          </button>

          <!-- User Dropdown -->
          <div ref="dropdownRef" class="user-dropdown-wrapper">
            <button class="avatar-trigger" @click="isDropdownOpen = !isDropdownOpen">
              <UserAvatar :user="user" size="md" />
              <svg
                :class="{ rotate: isDropdownOpen }"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
              >
                <path d="m6 9 6 6 6-6" />
              </svg>
            </button>

            <Transition name="dropdown">
              <div v-if="isDropdownOpen" class="portal-dropdown">
                <div class="dropdown-header">
                  <div class="dropdown-user-name">{{ user?.name }}</div>
                  <div class="dropdown-user-role">{{ t('Client Account', 'حساب عميل') }}</div>
                </div>
                <div class="dropdown-divider"></div>
                <router-link to="/profile" class="dropdown-item" @click="isDropdownOpen = false">
                  <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                  </svg>
                  {{ t('Profile', 'الملف الشخصي') }}
                </router-link>
                <router-link to="/orders" class="dropdown-item" @click="isDropdownOpen = false">
                  <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path
                      d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"
                    />
                    <polyline points="14 2 14 8 20 8" />
                  </svg>
                  {{ t('My Orders', 'طلباتي') }}
                </router-link>
                <router-link
                  to="/orders/create"
                  class="dropdown-item"
                  @click="isDropdownOpen = false"
                >
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
                  {{ t('Create Order', 'إنشاء طلب') }}
                </router-link>
                <div class="dropdown-divider"></div>
                <button class="dropdown-item logout-text" @click="logout">
                  <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    <polyline points="16 17 21 12 16 7" />
                    <line x1="21" y1="12" x2="9" y2="12" />
                  </svg>
                  {{ t('Logout', 'تسجيل الخروج') }}
                </button>
              </div>
            </Transition>
          </div>
        </div>
      </div>
    </header>

    <!-- Page Content -->
    <main class="portal-content">
      <div class="portal-container animate-fade-up">
        <slot />
      </div>
    </main>

    <!-- Mobile Bottom Navigation for Client -->
    <nav class="client-bottom-nav no-print">
      <router-link to="/dashboard" class="client-bottom-item">
        <svg
          width="22"
          height="22"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
          <polyline points="9 22 9 12 15 12 15 22" />
        </svg>
        <span>{{ t('Dash', 'الرئيسية') }}</span>
      </router-link>
      <router-link to="/orders" class="client-bottom-item">
        <svg
          width="22"
          height="22"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
          <polyline points="14 2 14 8 20 8" />
        </svg>
        <span>{{ t('Orders', 'طلباتي') }}</span>
      </router-link>
      <router-link to="/products" class="client-bottom-item">
        <svg
          width="22"
          height="22"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"
          />
          <path d="m3.3 7 8.7 5 8.7-5" />
          <path d="M12 22V12" />
        </svg>
        <span>{{ t('Products', 'منتجاتي') }}</span>
      </router-link>
      <router-link to="/profile" class="client-bottom-item">
        <svg
          width="22"
          height="22"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
          <circle cx="12" cy="7" r="4" />
        </svg>
        <span>{{ t('Profile', 'حسابي') }}</span>
      </router-link>
    </nav>

    <footer class="portal-footer no-print">
      <div class="container">
        <p>© 2026 TechDraft Manufacturing Platform. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
  import { ref, computed, onMounted, onUnmounted } from 'vue';
  import { useRouter } from 'vue-router';
  import axios from 'axios';
  import UserAvatar from './UI/UserAvatar.vue';
  import { useLang } from '../composables/useLang';

  const { t, isRtl, toggleLang } = useLang();

  const router = useRouter();
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
  const isDropdownOpen = ref(false);
  const dropdownRef = ref(null);

  const breadcrumbLinks = computed(() => {
    const links = [];
    const parts = router.currentRoute.value.path.split('/').filter((p) => p);

    if (parts.length === 0) return [];

    if (parts[0] === 'orders' && parts[1]) {
      links.push({ label: t('Orders', 'الطلبات'), path: '/orders' });
      const orderCode = router.currentRoute.value.params.id || parts[1];
      links.push({ label: `Order #${orderCode}`, path: router.currentRoute.value.path });
    }

    return links;
  });

  const logout = async () => {
    try {
      await axios.post(
        '/api/logout',
        {},
        {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` },
        }
      );
    } catch (e) {
      console.warn(e);
    }
    localStorage.clear();
    router.push('/login');
  };

  const closeDropdown = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
      isDropdownOpen.value = false;
    }
  };

  onMounted(() => {
    window.addEventListener('click', closeDropdown);
  });

  onUnmounted(() => {
    window.removeEventListener('click', closeDropdown);
  });
</script>

<style scoped>
  .client-portal {
    min-height: 100vh;
    background: #fcfdfe;
    display: flex;
    flex-direction: column;
    font-family: 'Instrument Sans', sans-serif;
    color: #1a3a5f;
  }

  @media print {
    .no-print {
      display: none !important;
    }
    .client-portal {
      display: block !important;
      background: white !important;
    }
    .portal-content {
      padding: 0 !important;
      margin: 0 !important;
    }
    .portal-container {
      max-width: none !important;
      margin: 0 !important;
    }
  }

  /* Navbar */
  .client-navbar {
    height: 64px;
    background: white;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  }

  .nav-container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .nav-brand-group {
    display: flex;
    align-items: center;
    gap: 1.5rem;
  }
  .nav-brand {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .portal-logo {
    width: 32px;
    height: 32px;
  }
  .logo-text {
    font-size: 1.125rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: #1a3a5f;
  }
  .nav-brand {
    text-decoration: none;
  }

  .breadcrumb-mini {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #94a3b8;
    border-left: 1px solid #f1f5f9;
    padding-left: 1.5rem;
  }
  .bc-item {
    color: #94a3b8;
    text-decoration: none;
  }
  .bc-item.active {
    color: #1e293b;
    font-weight: 700;
  }
  .bc-sep {
    color: #e2e8f0;
  }

  .nav-links {
    display: flex;
    gap: 2.5rem;
  }
  .portal-link {
    color: #64748b;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.9375rem;
    transition: all 0.2s;
    padding: 0.5rem 0;
    position: relative;
  }
  .portal-link:hover {
    color: #0ea5e9;
  }
  .portal-link.router-link-active {
    color: #0ea5e9;
  }
  .portal-link.router-link-active::after {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 100%;
    height: 2px;
    background: #0ea5e9;
    border-radius: 2px;
  }

  .nav-actions {
    display: flex;
    align-items: center;
    gap: 1.5rem;
  }

  .lang-minimal-btn {
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    padding: 0.4rem 0.75rem;
    border-radius: 8px;
    font-weight: 800;
    font-size: 0.75rem;
    cursor: pointer;
  }

  .nav-icon-btn {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    border: 1px solid #f1f5f9;
    background: white;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
  }
  .nav-icon-btn:hover {
    background: #f8fafc;
    color: #0ea5e9;
    border-color: #e2e8f0;
  }

  .notifications-bell {
    color: #94a3b8;
  }
  .bell-dot {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 8px;
    height: 8px;
    background: #0ea5e9;
    border: 2px solid white;
    border-radius: 50%;
  }

  /* Dropdown */
  .user-dropdown-wrapper {
    position: relative;
  }
  .avatar-trigger {
    display: flex;
    align-items: center;
    gap: 8px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
  }
  .avatar-trigger img {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    border: 2px solid white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  }
  .avatar-trigger svg {
    color: #94a3b8;
    transition: transform 0.3s;
  }
  .avatar-trigger svg.rotate {
    transform: rotate(180deg);
  }

  .portal-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: 240px;
    background: white;
    border-radius: 16px;
    box-shadow:
      0 20px 25px -5px rgba(0, 0, 0, 0.1),
      0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border: 1px solid #f1f5f9;
    padding: 0.75rem;
  }
  .dropdown-header {
    padding: 0.75rem 1rem;
  }
  .dropdown-user-name {
    font-weight: 800;
    font-size: 0.9375rem;
    color: #1a3a5f;
  }
  .dropdown-user-role {
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 700;
    margin-top: 2px;
  }
  .dropdown-divider {
    height: 1px;
    background: #f1f5f9;
    margin: 0.5rem 0;
  }
  .dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0.75rem 1rem;
    color: #64748b;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    border-radius: 10px;
    transition: all 0.2s;
  }
  .dropdown-item:hover {
    background: #f8fafc;
    color: #0ea5e9;
  }
  .logout-text {
    color: #ef4444;
    width: 100%;
    border: none;
    background: none;
    cursor: pointer;
  }
  .logout-text:hover {
    background: #fef2f2;
    color: #ef4444;
  }

  /* Content */
  .portal-content {
    flex: 1;
    padding: 3rem 1rem;
  }
  .portal-container {
    width: 100%;
    max-width: 1000px;
    margin: 0 auto;
  }

  .portal-footer {
    padding: 2.5rem 0;
    text-align: center;
    color: #94a3b8;
    font-size: 0.875rem;
    border-top: 1px solid #f1f5f9;
    background: white;
  }

  /* Animations */
  .dropdown-enter-active,
  .dropdown-leave-active {
    transition: all 0.2s ease;
  }
  .dropdown-enter-from,
  .dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
  }

  .animate-fade-up {
    animation: fadeUp 0.6s ease-out;
  }
  @keyframes fadeUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* RTL */
  .rtl .portal-dropdown {
    right: auto;
    left: 0;
  }
  .rtl .portal-link.router-link-active::after {
    left: auto;
    right: 0;
  }

  /* Client Bottom Navigation */
  .client-bottom-nav {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 64px;
    background: white;
    border-top: 1px solid #f1f5f9;
    z-index: 190;
    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.06);
    align-items: center;
    justify-content: space-around;
  }

  .client-bottom-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 3px;
    padding: 6px 10px;
    color: #94a3b8;
    text-decoration: none;
    font-size: 0.6rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    transition: all 0.2s;
    min-width: 64px;
  }
  .client-bottom-item.router-link-active {
    color: #0ea5e9;
  }

  /* Mobile Responsive */
  @media (max-width: 768px) {
    .nav-container {
      padding: 0 1rem;
    }

    .nav-links {
      display: none;
    }

    .client-bottom-nav {
      display: flex;
    }

    .portal-content {
      padding: 1.5rem 1rem;
      padding-bottom: 80px;
    }

    .avatar-trigger img {
      width: 34px;
      height: 34px;
    }

    .portal-dropdown {
      right: -1rem;
      width: calc(100vw - 2rem);
      max-width: 320px;
    }

    .logo-text {
      font-size: 1rem;
    }

    .portal-container {
      max-width: 100%;
    }
  }
</style>
