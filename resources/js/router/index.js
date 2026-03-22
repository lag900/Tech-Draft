import { createRouter, createWebHistory } from 'vue-router';
import { hasPermission, PERMISSIONS } from '../utils/permissions';

import Welcome from '../views/Welcome.vue';
import Login from '../views/Login.vue';
import Register from '../views/Register.vue';
import Dashboard from '../views/Dashboard.vue';
import OrderList from '../views/Orders/OrderList.vue';
import OrderWizard from '../views/Orders/ModernForm/OrderWizard.vue';
import OrderDetail from '../views/Orders/OrderDetail.vue';

import Categories from '../views/Categories.vue';
import Fabrics from '../views/Fabrics.vue';
import MeasurementTemplates from '../views/MeasurementTemplates.vue';

// New Views (Lazy loaded or imported directly)
const UserList = () => import('../views/Users/UserList.vue');
const Settings = () => import('../views/Settings/Settings.vue');
const Designs = () => import('../views/Designs/DesignList.vue');
const Messages = () => import('../views/Messages.vue');
const Roles = () => import('../views/Settings/Roles.vue');
const PermissionsMatrix = () => import('../views/Settings/PermissionsMatrix.vue');
const ItemTypes = () => import('../views/Settings/ItemTypes.vue');
const Fits = () => import('../views/Settings/Fits.vue');
const ProductList = () => import('../views/Products/ProductList.vue');

// Client Module Views
const ClientList = () => import('../views/Clients/ClientList.vue');
const ClientDetail = () => import('../views/Clients/ClientDetail.vue');

const routes = [
  { path: '/', component: Welcome, name: 'welcome' },
  { path: '/login', component: Login, name: 'login' },
  { path: '/register', component: Register, name: 'register' },
  {
    path: '/dashboard',
    component: Dashboard,
    name: 'dashboard',
    meta: { requiresAuth: true },
  },
  {
    path: '/clients',
    component: ClientList,
    name: 'clients',
    meta: { requiresAuth: true, permission: PERMISSIONS.MANAGE_USERS },
  },
  {
    path: '/clients/:id',
    component: ClientDetail,
    name: 'client-detail',
    meta: { requiresAuth: true, permission: PERMISSIONS.MANAGE_USERS },
  },
  {
    path: '/orders',
    component: OrderList,
    name: 'orders',
    meta: { requiresAuth: true, permission: PERMISSIONS.MANAGE_ORDERS },
  },
  {
    path: '/orders/create',
    component: OrderWizard,
    name: 'order-create',
    meta: { requiresAuth: true, permission: PERMISSIONS.MANAGE_ORDERS },
  },
  {
    path: '/orders/:id',
    component: OrderDetail,
    name: 'order-detail',
    meta: { requiresAuth: true, permission: PERMISSIONS.MANAGE_ORDERS },
    props: true,
  },
  {
    path: '/orders/:id/tech-pack',
    component: () => import('../views/Orders/TechPackGenerator.vue'),
    name: 'order-tech-pack',
    meta: { requiresAuth: true, permission: PERMISSIONS.MANAGE_ORDERS },
    props: true,
  },

  {
    path: '/messages',
    component: Messages,
    name: 'messages',
    meta: { requiresAuth: true },
  },
  {
    path: '/designs',
    component: Designs,
    name: 'designs',
    meta: { requiresAuth: true, permission: PERMISSIONS.MANAGE_DESIGNS },
  },
  {
    path: '/designs/create',
    component: () => import('../views/Designs/DesignCreate.vue'),
    name: 'design-create',
    meta: { requiresAuth: true, permission: PERMISSIONS.MANAGE_DESIGNS },
  },
  {
    path: '/designs/:id',
    component: () => import('../views/Designs/DesignDetail.vue'),
    name: 'design-detail',
    meta: { requiresAuth: true, permission: PERMISSIONS.MANAGE_DESIGNS },
  },
  {
    path: '/categories',
    component: Categories,
    name: 'categories',
    meta: { requiresAuth: true, permission: PERMISSIONS.MANAGE_CATEGORIES },
  },
  {
    path: '/fabrics',
    component: Fabrics,
    name: 'fabrics',
    meta: { requiresAuth: true, permission: PERMISSIONS.MANAGE_FABRICS },
  },
  {
    path: '/measurements',
    component: MeasurementTemplates,
    name: 'measurements',
    meta: { requiresAuth: true },
  },
  {
    path: '/users',
    component: UserList,
    name: 'users',
    meta: { requiresAuth: true, permission: PERMISSIONS.MANAGE_USERS },
  },
  {
    path: '/settings/roles',
    component: Roles,
    name: 'roles',
    meta: { requiresAuth: true, permission: PERMISSIONS.SYSTEM_SETTINGS },
  },
  {
    path: '/settings/permissions',
    component: PermissionsMatrix,
    name: 'permissions-matrix',
    meta: { requiresAuth: true, permission: PERMISSIONS.SYSTEM_SETTINGS },
  },
  {
    path: '/settings/landing-page',
    component: () => import('../views/Settings/LandingPageCms.vue'),
    name: 'landing-page-cms',
    meta: { requiresAuth: true, permission: PERMISSIONS.SYSTEM_SETTINGS },
  },
  {
    path: '/settings',
    component: Settings,
    name: 'settings',
    meta: { requiresAuth: true, permission: PERMISSIONS.SYSTEM_SETTINGS },
  },
  {
    path: '/settings/item-types',
    component: ItemTypes,
    name: 'item-types',
    meta: { requiresAuth: true, permission: PERMISSIONS.MANAGE_CATEGORIES },
  },
  {
    path: '/settings/fits',
    component: Fits,
    name: 'fits',
    meta: { requiresAuth: true, permission: PERMISSIONS.MANAGE_CATEGORIES },
  },
  {
    path: '/products',
    component: ProductList,
    name: 'products',
    meta: { requiresAuth: true, permission: 'products.view' },
  },
  {
    path: '/profile',
    component: () => import('../views/Profile.vue'),
    name: 'profile',
    meta: { requiresAuth: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

const ADMIN_ONLY_ROUTES = [
  'dashboard',
  'designs',
  'fabrics',
  'categories',
  'users',
  'roles',
  'settings',
  'permissions-matrix',
  'measurements',
  'item-types',
  'fits',
  'products',
  'landing-page-cms',
];

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token');
  const user = JSON.parse(localStorage.getItem('user') || 'null');

  if (['login', 'register'].includes(to.name) && token) {
    if (user?.role === 'client') {
      next('/orders/create');
    } else {
      next('/dashboard');
    }
  } else if (to.meta.requiresAuth && !token) {
    next('/login');
  } else if (user?.role === 'client' && ADMIN_ONLY_ROUTES.includes(to.name)) {
    // Block clients from admin routes
    next('/orders/create');
  } else if (to.meta.permission && !hasPermission(user, to.meta.permission)) {
    if (user?.role === 'client') {
      next('/orders/create');
    } else {
      next('/dashboard');
    }
  } else {
    next();
  }
});

export default router;
