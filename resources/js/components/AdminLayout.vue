<template>
  <div
    class="layout"
    :class="{
      rtl: isRtl,
      'sidebar-collapsed': isCollapsed,
      'mobile-sidebar-open': isMobileSidebarOpen,
    }"
  >
    <!-- Mobile Overlay Backdrop -->
    <div
      v-if="isMobileSidebarOpen"
      class="mobile-overlay"
      @click="isMobileSidebarOpen = false"
    ></div>
    <!-- Sidebar -->
    <aside class="sidebar no-print">
      <div class="sidebar-header">
        <router-link to="/" class="logo">
          <div class="logo-box-img">
            <img src="../../images/logo.png" alt="TechDraft" class="sidebar-logo" />
          </div>
          <Transition name="fade-slide">
            <div v-if="!isCollapsed" class="logo-text-group">
              <span class="logo-text">TechDraft</span>
              <span class="logo-tagline">{{ t('Digital Pattern', 'باترون رقمي') }}</span>
            </div>
          </Transition>
        </router-link>
        <button class="collapse-toggle" @click="isCollapsed = !isCollapsed">
          <svg
            v-if="!isCollapsed"
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="m15 18-6-6 6-6" />
          </svg>
          <svg
            v-else
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="m9 18 6-6-6-6" />
          </svg>
        </button>
      </div>

      <div class="sidebar-menu">
        <div class="menu-section">
          <div v-if="!isCollapsed" class="section-title">{{ t('General', 'عام') }}</div>
          <router-link
            v-if="hasPermission(user, 'dashboard.view')"
            to="/dashboard"
            class="nav-item"
          >
            <div class="nav-icon-box">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect width="7" height="9" x="3" y="3" rx="1" />
                <rect width="7" height="5" x="14" y="3" rx="1" />
                <rect width="7" height="9" x="14" y="12" rx="1" />
                <rect width="7" height="5" x="3" y="16" rx="1" />
              </svg>
            </div>
            <span v-if="!isCollapsed" class="nav-label">{{ t('Dashboard', 'لوحة التحكم') }}</span>
          </router-link>

          <router-link v-if="hasPermission(user, 'orders.view')" to="/orders" class="nav-item">
            <div class="nav-icon-box">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <line x1="10" y1="9" x2="8" y2="9" />
              </svg>
            </div>
            <span v-if="!isCollapsed" class="nav-label">{{ t('Orders', 'الطلبات') }}</span>
          </router-link>

          <router-link v-if="hasPermission(user, 'designs.view')" to="/designs" class="nav-item">
            <div class="nav-icon-box">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="13.5" cy="6.5" r=".5" />
                <circle cx="17.5" cy="10.5" r=".5" />
                <circle cx="8.5" cy="7.5" r=".5" />
                <circle cx="6.5" cy="12.5" r=".5" />
                <path
                  d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.92 0 1.7-.39 2.3-1 .6-.6 1-1.45 1-2.4 0-1.5 1.5-3 3-3 2.5 0 4.5-2.24 4.5-5 0-4.75-4.03-8.5-8.5-8.5z"
                />
              </svg>
            </div>
            <span v-if="!isCollapsed" class="nav-label">{{ t('Designs', 'التصاميم') }}</span>
          </router-link>
        </div>

        <!-- Inventory -->
        <div
          v-if="
            hasPermission(user, 'fabrics.view') ||
            hasPermission(user, 'categories.view') ||
            hasPermission(user, 'item_types.view') ||
            hasPermission(user, 'fit_types.view') ||
            hasPermission(user, 'measurements.view')
          "
          class="menu-section"
        >
          <div v-if="!isCollapsed" class="section-title">{{ t('Inventory', 'المخزون') }}</div>
          <router-link v-if="hasPermission(user, 'fabrics.view')" to="/fabrics" class="nav-item">
            <div class="nav-icon-box">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <polygon points="12 2 2 7 12 12 22 7 12 2" />
                <polyline points="2 17 12 22 22 17" />
                <polyline points="2 12 12 17 22 12" />
              </svg>
            </div>
            <span v-if="!isCollapsed" class="nav-label">{{ t('Fabrics', 'الأقمشة') }}</span>
          </router-link>
          <router-link
            v-if="hasPermission(user, 'categories.view')"
            to="/categories"
            class="nav-item"
          >
            <div class="nav-icon-box">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect width="7" height="7" x="3" y="3" rx="1" />
                <rect width="7" height="7" x="14" y="3" rx="1" />
                <rect width="7" height="7" x="14" y="14" rx="1" />
                <rect width="7" height="7" x="3" y="14" rx="1" />
              </svg>
            </div>
            <span v-if="!isCollapsed" class="nav-label">{{ t('Categories', 'التصنيفات') }}</span>
          </router-link>
          <router-link
            v-if="hasPermission(user, 'item_types.view')"
            to="/settings/item-types"
            class="nav-item"
          >
            <div class="nav-icon-box">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </div>
            <span v-if="!isCollapsed" class="nav-label">{{ t('Item Types', 'أنواع القطع') }}</span>
          </router-link>
          <router-link
            v-if="hasPermission(user, 'fit_types.view')"
            to="/settings/fits"
            class="nav-item"
          >
            <div class="nav-icon-box">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M20 7h-9m9 5h-7m7 5H7" />
              </svg>
            </div>
            <span v-if="!isCollapsed" class="nav-label">{{ t('Fit Types', 'أنواع القصات') }}</span>
          </router-link>
          <router-link
            v-if="hasPermission(user, 'measurements.view')"
            to="/measurements"
            class="nav-item"
          >
            <div class="nav-icon-box">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M4 12V4h16v8M4 12v8h16v-8M4 12h16M12 4v16M8 12v4M16 12v4" />
              </svg>
            </div>
            <span v-if="!isCollapsed" class="nav-label">{{ t('Measurements', 'المقاسات') }}</span>
          </router-link>
          <router-link v-if="hasPermission(user, 'products.view')" to="/products" class="nav-item">
            <div class="nav-icon-box">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path
                  d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"
                />
                <path d="m3.3 7 8.7 5 8.7-5" />
                <path d="M12 22V12" />
              </svg>
            </div>
            <span v-if="!isCollapsed" class="nav-label">{{ t('Products', 'المنتجات') }}</span>
          </router-link>
        </div>

        <!-- System -->
        <div
          v-if="
            hasPermission(user, 'clients.view') ||
            hasPermission(user, 'team.view') ||
            hasPermission(user, 'roles.view') ||
            hasPermission(user, 'settings.view')
          "
          class="menu-section"
        >
          <div v-if="!isCollapsed" class="section-title">{{ t('Administrative', 'الإدارة') }}</div>
          <router-link v-if="hasPermission(user, 'clients.view')" to="/clients" class="nav-item">
            <div class="nav-icon-box">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
              </svg>
            </div>
            <span v-if="!isCollapsed" class="nav-label">{{ t('Clients', 'العملاء') }}</span>
          </router-link>
          <router-link v-if="hasPermission(user, 'team.view')" to="/users" class="nav-item">
            <div class="nav-icon-box">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
              </svg>
            </div>
            <span v-if="!isCollapsed" class="nav-label">{{
              t('Team Members', 'أعضاء الفريق')
            }}</span>
          </router-link>
          <router-link
            v-if="hasPermission(user, 'roles.view')"
            to="/settings/roles"
            class="nav-item"
          >
            <div class="nav-icon-box">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
              </svg>
            </div>
            <span v-if="!isCollapsed" class="nav-label">{{
              t('Identity & Roles', 'الأدوار والهويات')
            }}</span>
          </router-link>
          <router-link v-if="hasPermission(user, 'settings.view')" to="/settings" class="nav-item">
            <div class="nav-icon-box">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path
                  d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.72V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"
                />
                <circle cx="12" cy="12" r="3" />
              </svg>
            </div>
            <span v-if="!isCollapsed" class="nav-label">{{ t('Settings', 'الإعدادات') }}</span>
          </router-link>
        </div>
      </div>

      <div class="sidebar-footer">
        <button class="logout-link" @click="logout">
          <div class="nav-icon-box">
            <svg
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
              <polyline points="16 17 21 12 16 7" />
              <line x1="21" y1="12" x2="9" y2="12" />
            </svg>
          </div>
          <span v-if="!isCollapsed" class="nav-label">{{ t('Logout', 'تسجيل الخروج') }}</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-body">
      <header class="top-nav no-print">
        <div class="top-nav-left">
          <!-- Mobile Hamburger Button -->
          <button class="hamburger-btn" @click="isMobileSidebarOpen = !isMobileSidebarOpen">
            <svg
              width="22"
              height="22"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
            >
              <path d="M3 12h18M3 6h18M3 18h18" />
            </svg>
          </button>
          <div class="breadcrumb flex min-w-0 shrink items-center">
            <template v-if="breadcrumbLinks.length > 0">
              <template v-for="(link, index) in breadcrumbLinks" :key="index">
                <router-link
                  v-if="index < breadcrumbLinks.length - 1"
                  :to="link.path"
                  class="bc-item hidden md:inline"
                >
                  {{ link.label }}
                </router-link>
                <span v-else class="bc-item active truncate">{{ link.label }}</span>
                <span v-if="index < breadcrumbLinks.length - 1" class="bc-sep hidden md:inline"
                  >/</span
                >
              </template>
            </template>
            <template v-else>
              <span class="bc-item active truncate">{{ routeName }}</span>
            </template>
          </div>
        </div>

        <div class="top-nav-right flex shrink-0 items-center">
          <div v-if="!isCollapsed" class="header-search">
            <svg
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <circle cx="11" cy="11" r="8" />
              <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input type="text" :placeholder="t('Search...', 'بحث...')" />
          </div>

          <button class="nav-icon-btn lang-btn" @click="toggleLang">
            {{ isRtl ? 'EN' : 'AR' }}
          </button>

          <!-- Notifications Dropdown -->
          <div ref="notificationsRef" class="dropdown-wrapper">
            <button
              class="nav-icon-btn notifications"
              @click="isNotificationsOpen = !isNotificationsOpen"
            >
              <svg
                width="22"
                height="22"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
              </svg>
              <span class="notification-dot"></span>
            </button>
            <Transition name="dropdown">
              <div v-if="isNotificationsOpen" class="nav-dropdown notifications-dropdown">
                <div class="dropdown-header">{{ t('Notifications', 'الإشعارات') }}</div>
                <div class="empty-notifications">
                  {{ t('No new notifications', 'لا توجد إشعارات جديدة') }}
                </div>
              </div>
            </Transition>
          </div>

          <!-- Profile Dropdown -->
          <div ref="profileRef" class="dropdown-wrapper">
            <button
              class="profile-preview clickable"
              @click="isUserDropdownOpen = !isUserDropdownOpen"
            >
              <UserAvatar :user="user" size="md" />
            </button>
            <Transition name="dropdown">
              <div v-if="isUserDropdownOpen" class="nav-dropdown profile-dropdown">
                <div class="dropdown-profile-info">
                  <div class="u-name">{{ user?.name }}</div>
                  <div class="u-role">{{ user?.role }}</div>
                </div>
                <div class="dropdown-divider"></div>
                <router-link
                  to="/profile"
                  class="dropdown-item"
                  @click="isUserDropdownOpen = false"
                >
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
                <button class="dropdown-item logout-item" @click="logout">
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
      </header>

      <div class="page-content">
        <div class="page-container animate-slide-up">
          <slot />
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
  import { ref, onMounted, computed, watch, onUnmounted } from 'vue';
  import { useRouter, useRoute } from 'vue-router';
  import axios from 'axios';
  import UserAvatar from './UI/UserAvatar.vue';
  import { hasPermission } from '../utils/permissions';
  import { useLang } from '../composables/useLang';

  const { t, isRtl, toggleLang } = useLang();
  const router = useRouter();
  const route = useRoute();

  const getUser = () => {
    try {
      return JSON.parse(localStorage.getItem('user') || 'null');
    } catch (e) {
      return null;
    }
  };
  const user = ref(getUser());
  const isCollapsed = ref(localStorage.getItem('sidebar-collapsed') === 'true');
  const isUserDropdownOpen = ref(false);
  const isNotificationsOpen = ref(false);
  const profileRef = ref(null);
  const notificationsRef = ref(null);
  const isMobileSidebarOpen = ref(false);

  watch(
    () => route.path,
    () => {
      isMobileSidebarOpen.value = false;
    }
  );

  const routeName = computed(() => {
    const name = route.name || route.path.split('/').pop() || 'Dashboard';
    return name.charAt(0).toUpperCase() + name.slice(1);
  });

  const breadcrumbLinks = computed(() => {
    const links = [];
    const parts = route.path.split('/').filter((p) => p);

    if (parts.length === 0) return [];

    // Home link
    links.push({ label: t('Dashboard', 'لوحة التحكم'), path: '/dashboard' });

    if (parts[0] === 'orders' && parts[1]) {
      links.push({ label: t('Orders', 'الطلبات'), path: '/orders' });
      // If it's a detail page and we have the order code (mocked for now or extracted from state)
      // In a real app, we'd get this from a store or props
      const orderCode = route.params.id || parts[1];
      links.push({ label: `Order #${orderCode}`, path: route.path });
    } else if (parts[0] === 'categories') {
      links.push({ label: t('Categories', 'التصنيفات'), path: '/categories' });
    } else if (parts[0] === 'fabrics') {
      links.push({ label: t('Fabrics', 'الأقمشة'), path: '/fabrics' });
    } else {
      links.push({ label: routeName.value, path: route.path });
    }

    return links;
  });

  watch(isCollapsed, (val) => {
    localStorage.setItem('sidebar-collapsed', val);
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

  const closeDropdowns = (e) => {
    if (profileRef.value && !profileRef.value.contains(e.target)) isUserDropdownOpen.value = false;
    if (notificationsRef.value && !notificationsRef.value.contains(e.target))
      isNotificationsOpen.value = false;
  };

  onMounted(() => {
    window.addEventListener('click', closeDropdowns);
  });

  onUnmounted(() => {
    window.removeEventListener('click', closeDropdowns);
  });
</script>

<style scoped>
  /* Inheriting same styles for consistency but can be specialized */
  .layout {
    display: flex;
    min-height: 100vh;
    background: #fcfdfe;
    font-family: 'Instrument Sans', sans-serif;
  }
  .sidebar {
    width: 280px;
    background: white;
    border-right: 1px solid #f1f5f9;
    display: flex;
    flex-direction: column;
    transition: 0.3s;
    position: sticky;
    top: 0;
    height: 100vh;
    z-index: 200;
  }
  .sidebar-collapsed .sidebar {
    width: 80px;
  }
  .sidebar-header {
    padding: 1.5rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .logo {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .logo-box-img {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    overflow: hidden;
    background: white;
  }
  .sidebar-logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }
  .logo-text {
    font-weight: 700;
    font-size: 1.15rem;
    color: #0f172a;
  }
  .logo-tagline {
    font-size: 0.75rem;
    color: #64748b;
  }
  .collapse-toggle {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    border: 1px solid #f1f5f9;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }
  .sidebar-menu {
    flex: 1;
    padding: 0 0.75rem;
    overflow-y: auto;
  }
  .menu-section {
    margin-bottom: 1.5rem;
  }
  .section-title {
    font-size: 0.65rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    padding: 0 0.75rem;
    margin-bottom: 0.5rem;
  }
  .nav-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.7rem 0.75rem;
    color: #64748b;
    text-decoration: none;
    font-weight: 600;
    border-radius: 10px;
    transition: 0.2s;
  }
  .nav-item:hover {
    background: #f8fafc;
    color: #0ea5e9;
  }
  .nav-item.router-link-active {
    background: #f0f9ff;
    color: #0ea5e9;
  }
  .nav-icon-box {
    width: 20px;
    display: flex;
    justify-content: center;
  }
  .sidebar-footer {
    padding: 1.25rem 0.75rem;
    border-top: 1px solid #f8fafc;
  }
  .user-pill {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 12px;
    margin-bottom: 0.75rem;
  }
  .user-avatar {
    width: 34px;
    height: 34px;
    background: #0ea5e9;
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
  }
  .user-name {
    font-size: 0.8125rem;
    font-weight: 700;
  }
  .user-role {
    font-size: 0.65rem;
    color: #94a3b8;
    font-weight: 700;
    text-transform: uppercase;
  }
  .logout-link {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    color: #f87171;
    background: none;
    border: none;
    cursor: pointer;
    border-radius: 10px;
    font-weight: 700;
  }
  .logout-link:hover {
    background: #fff1f2;
  }
  .main-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
    overflow-x: hidden;
  }

  @media print {
    .no-print {
      display: none !important;
    }
    .layout {
      display: block !important;
      background: white !important;
    }
    .main-body {
      display: block !important;
      padding: 0 !important;
      margin: 0 !important;
    }
    .page-content {
      padding: 0 !important;
      margin: 0 !important;
    }
    .page-container {
      max-width: none !important;
      margin: 0 !important;
    }
  }

  .top-nav {
    height: 64px;
    background: white;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.5rem;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  }

  .top-nav-left {
    display: flex;
    align-items: center;
    min-width: 0;
    gap: 0.5rem;
    flex: 1;
  }

  .breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #94a3b8;
  }

  .bc-item {
    color: #94a3b8;
    text-decoration: none;
    transition: color 0.2s;
  }

  .bc-item:hover {
    color: #0ea5e9;
  }

  .bc-item.active {
    color: #1e293b;
    font-weight: 700;
  }

  .bc-sep {
    color: #e2e8f0;
    font-weight: 400;
  }

  .top-nav-right {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    flex-shrink: 0;
  }

  .header-search {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    padding: 0 1rem;
    height: 38px;
    border-radius: 10px;
    width: 280px;
    color: #94a3b8;
    transition: all 0.2s;
  }

  .header-search:focus-within {
    background: white;
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
  }

  .header-search input {
    border: none;
    background: transparent;
    outline: none;
    font-size: 0.875rem;
    width: 100%;
    color: #1e293b;
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

  .lang-btn {
    font-size: 0.7rem;
    font-weight: 800;
    width: auto;
    padding: 0 0.75rem;
  }

  .notifications .notification-dot {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 8px;
    height: 8px;
    background: #ef4444;
    border-radius: 50%;
    border: 2px solid white;
  }

  .profile-preview {
    display: flex;
    align-items: center;
  }

  .avatar-img {
    width: 38px;
    height: 38px;
    border-radius: 50% !important;
    object-fit: cover;
    border: 1px solid #f1f5f9;
  }

  /* Dropdowns */
  .dropdown-wrapper {
    position: relative;
  }
  .clickable {
    cursor: pointer;
    transition: transform 0.2s;
  }
  .clickable:hover {
    transform: scale(1.05);
  }

  .nav-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    background: white;
    border-radius: 16px;
    box-shadow:
      0 20px 25px -5px rgba(0, 0, 0, 0.1),
      0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border: 1px solid #f1f5f9;
    z-index: 200;
    min-width: 240px;
    padding: 0.75rem;
    animation: dropdownIn 0.2s ease-out;
  }

  @keyframes dropdownIn {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .dropdown-header {
    padding: 0.5rem 0.75rem;
    font-weight: 800;
    font-size: 0.75rem;
    color: #94a3b8;
    text-transform: uppercase;
  }

  .empty-notifications {
    padding: 2rem 1rem;
    text-align: center;
    color: #94a3b8;
    font-size: 0.875rem;
  }

  .dropdown-profile-info {
    padding: 0.75rem 1rem;
  }

  .dropdown-profile-info .u-name {
    font-weight: 800;
    color: #1a3a5f;
    font-size: 0.9375rem;
  }
  .dropdown-profile-info .u-role {
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 700;
    text-transform: uppercase;
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
    width: 100%;
    border: none;
    background: none;
    cursor: pointer;
    text-align: left;
  }

  .dropdown-item:hover {
    background: #f8fafc;
    color: #0ea5e9;
  }
  .logout-item {
    color: #f87171;
  }
  .logout-item:hover {
    background: #fff1f2;
    color: #ef4444;
  }

  /* Transitions */
  .dropdown-enter-active,
  .dropdown-leave-active {
    transition: all 0.2s ease;
  }
  .dropdown-enter-from,
  .dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
  }

  .page-content {
    padding: 2rem;
    flex: 1;
    overflow-x: hidden;
  }
  .page-container {
    max-width: 1280px;
    margin: 0 auto;
  }
  .rtl .sidebar {
    border-right: none;
    border-left: 1px solid #f1f5f9;
  }
  .rtl .header-search {
    flex-direction: row-reverse;
  }
  .rtl .header-search input {
    text-align: right;
  }
  .rtl .nav-icon-btn .notification-dot {
    right: auto;
    left: 8px;
  }

  .rtl .nav-dropdown {
    right: auto;
    left: 0;
  }
  .rtl .dropdown-item {
    text-align: right;
    flex-direction: row-reverse;
  }
  .rtl .dropdown-profile-info {
    text-align: right;
  }

  .animate-slide-up {
    animation: slideUp 0.4s ease-out;
  }
  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(15px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* ========== MOBILE RESPONSIVENESS ========== */
  .hamburger-btn {
    display: none;
    width: 44px;
    height: 44px;
    align-items: center;
    justify-content: center;
    background: none;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    color: #475569;
    transition: all 0.2s;
    margin-right: 0.25rem;
    flex-shrink: 0;
  }
  .hamburger-btn:hover {
    background: #f8fafc;
    color: #0ea5e9;
  }

  .mobile-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.5);
    backdrop-filter: blur(4px);
    z-index: 199;
  }

  /* Mobile Bottom Navigation */
  .mobile-bottom-nav {
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
    padding: 0 0.5rem;
    align-items: center;
    justify-content: space-around;
  }

  .bottom-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    padding: 6px 10px;
    border-radius: 10px;
    color: #94a3b8;
    text-decoration: none;
    font-size: 0.6rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    transition: all 0.2s;
    min-width: 52px;
    background: none;
    border: none;
    cursor: pointer;
  }
  .bottom-nav-item.router-link-active {
    color: #0ea5e9;
  }
  .bottom-nav-item:hover {
    color: #0ea5e9;
  }

  @media (max-width: 768px) {
    .hamburger-btn {
      display: flex;
    }
    .mobile-overlay {
      display: block;
    }

    .sidebar {
      position: fixed;
      left: -300px;
      top: 0;
      height: 100vh;
      width: 280px !important;
      z-index: 200;
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
    }
    .rtl .sidebar {
      left: auto;
      right: -300px;
    }

    .mobile-sidebar-open .sidebar {
      left: 0 !important;
    }
    .rtl.mobile-sidebar-open .sidebar {
      left: auto !important;
      right: 0 !important;
    }

    .sidebar-collapsed .sidebar {
      width: 280px !important;
    }
    .collapse-toggle {
      display: none;
    }

    .main-body {
      width: 100%;
    }

    .top-nav {
      padding: 0 0.75rem;
      gap: 0.5rem;
    }
    .top-nav-right {
      gap: 0.5rem;
    }
    .header-search {
      display: none;
    }
    .breadcrumb {
      font-size: 0.95rem;
    }
    .nav-icon-btn {
      width: 36px;
      height: 36px;
    }
    .hamburger-btn {
      width: 36px;
      height: 36px;
    }
    .profile-preview:deep(.user-avatar) {
      width: 36px !important;
      height: 36px !important;
      border-radius: 50% !important;
    }

    .page-content {
      padding: 1rem;
      padding-bottom: 80px;
    }

    .mobile-bottom-nav {
      display: flex;
    }

    /* Touch targets */
    .nav-icon-btn {
      width: 44px;
      height: 44px;
    }
    .nav-item {
      min-height: 48px;
    }
  }

  @media (min-width: 769px) and (max-width: 1024px) {
    .sidebar {
      width: 220px;
    }
    .sidebar-collapsed .sidebar {
      width: 70px;
    }
    .page-content {
      padding: 1.5rem;
    }
    .header-search {
      width: 200px;
    }
  }
</style>
