export const ROLES = {
  SUPER_ADMIN: 'superadmin',
  ADMIN: 'admin',
  MANAGER: 'manager',
  DATA_ENTRY: 'dataentry',
  DESIGNER: 'designer',
  PRODUCTION: 'production',
  CLIENT: 'client',
};

export const MODULES = {
  DASHBOARD: 'dashboard',
  ORDERS: 'orders',
  DESIGNS: 'designs',
  FABRICS: 'fabrics',
  CATEGORIES: 'categories',
  ITEM_TYPES: 'item_types',
  FIT_TYPES: 'fit_types',
  MEASUREMENTS: 'measurements',
  PRODUCTS: 'products',
  CLIENTS: 'clients',
  TEAM: 'team',
  ROLES: 'roles',
  SETTINGS: 'settings',
};

export const ACTIONS = {
  VIEW: 'view',
  CREATE: 'create',
  EDIT: 'edit',
  DELETE: 'delete',
  EXPORT: 'export',
  IMPORT: 'import',
  APPROVE: 'approve',
};

// Flattened permission list for easy checking
export const PERMISSIONS = {
  MANAGE_ORDERS: 'orders.view',
  MANAGE_DESIGNS: 'designs.view',
  MANAGE_FABRICS: 'fabrics.view',
  MANAGE_CATEGORIES: 'categories.view',
  MANAGE_USERS: 'users.view',
  SYSTEM_SETTINGS: 'settings.view',
  MANAGE_CLIENTS: 'clients.view',
};

Object.values(MODULES).forEach((module) => {
  Object.values(ACTIONS).forEach((action) => {
    const key = `${module.toUpperCase()}_${action.toUpperCase()}`;
    if (!PERMISSIONS[key]) {
      PERMISSIONS[key] = `${module}.${action}`;
    }
  });
});

// Role descriptions
export const ROLE_INFO = {
  [ROLES.SUPER_ADMIN]: {
    name: 'Super Admin',
    description: 'Full system access, manage roles and configuration.',
    color: '#ef4444',
  },
  [ROLES.ADMIN]: {
    name: 'Administrator',
    description: 'Manage users, orders, and system core modules.',
    color: '#3b82f6',
  },
  [ROLES.MANAGER]: {
    name: 'Manager',
    description: 'Quality control, approval workflow and team management.',
    color: '#10b981',
  },
  [ROLES.DATA_ENTRY]: {
    name: 'Data Entry',
    description: 'Creating orders, fabrics, and updating records.',
    color: '#8b5cf6',
  },
  [ROLES.DESIGNER]: {
    name: 'Designer',
    description: 'Sketching, pattern review and design management.',
    color: '#ec4899',
  },
  [ROLES.PRODUCTION]: {
    name: 'Production',
    description: 'Manufacturing oversight and floor operations.',
    color: '#f97316',
  },
  [ROLES.CLIENT]: {
    name: 'Client',
    description: 'External brand access for creating and tracking orders.',
    color: '#f59e0b',
  },
};

const ALL_PERMISSIONS = Object.values(PERMISSIONS);

export const ROLE_PERMISSIONS = {
  [ROLES.SUPER_ADMIN]: ALL_PERMISSIONS,
  [ROLES.ADMIN]: ALL_PERMISSIONS.filter(
    (p) => !p.includes('settings.delete') && !p.includes('roles.delete')
  ),
  [ROLES.MANAGER]: [
    'dashboard.view',
    'orders.view',
    'orders.edit',
    'orders.approve',
    'orders.export',
    'fabrics.view',
    'fabrics.edit',
    'designs.view',
    'designs.edit',
    'categories.view',
    'item_types.view',
    'fit_types.view',
    'clients.view',
    'team.view',
  ],
  [ROLES.DESIGNER]: [
    'dashboard.view',
    'orders.view',
    'orders.edit',
    'designs.create',
    'designs.view',
    'designs.edit',
    'designs.delete',
    'fabrics.view',
    'measurements.view',
    'measurements.create',
    'measurements.edit',
  ],
  [ROLES.PRODUCTION]: [
    'dashboard.view',
    'orders.view',
    'orders.edit',
    'fabrics.view',
    'fabrics.edit',
    'measurements.view',
  ],
  [ROLES.DATA_ENTRY]: [
    'dashboard.view',
    'orders.create',
    'orders.view',
    'orders.edit',
    'fabrics.create',
    'fabrics.view',
    'fabrics.edit',
    'designs.create',
    'designs.view',
    'designs.edit',
    'categories.create',
    'categories.view',
    'item_types.create',
    'item_types.view',
    'fit_types.create',
    'fit_types.view',
  ],
  [ROLES.CLIENT]: [
    'dashboard.view',
    'orders.create',
    'orders.view',
    'orders.edit',
    'designs.view',
    'designs.create',
    'products.view',
    'categories.view',
    'item_types.view',
    'fit_types.view',
    'fabrics.view',
  ],
};

/**
 * Check if a user has a specific permission.
 * Uses DB-stored permissions (user.permissions array) first,
 * falls back to static ROLE_PERMISSIONS map.
 */
export function hasPermission(user, permission) {
  if (!user || !user.role) return false;
  if (user.role === ROLES.SUPER_ADMIN) return true;

  const permissions = user.permissions || ROLE_PERMISSIONS[user.role] || [];
  return permissions.includes(permission);
}

/**
 * Shorthand: check module.action
 */
export function can(user, module, action) {
  return hasPermission(user, `${module}.${action}`);
}

/**
 * Check if user can view a specific module (used for sidebar filtering).
 */
export function canViewModule(user, module) {
  return hasPermission(user, `${module}.view`);
}

/**
 * Get the user object from localStorage with permissions.
 * Caches parsed result.
 */
let _cachedUser = null;
let _cachedUserRaw = null;

export function getUser() {
  try {
    const raw = localStorage.getItem('user');
    if (raw !== _cachedUserRaw) {
      _cachedUserRaw = raw;
      _cachedUser = JSON.parse(raw || 'null');
    }
    return _cachedUser;
  } catch (e) {
    return null;
  }
}

/**
 * Refresh the cached user permissions from the API
 * and store updated user object in localStorage.
 */
export async function refreshUserPermissions(axios) {
  try {
    const res = await axios.get('/api/me', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` },
    });
    const userData = res.data;

    // Merge permissions from role
    if (userData.role && !userData.permissions) {
      // Load from roles if not embedded
      try {
        const rolesRes = await axios.get('/api/permissions', {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` },
        });
        const userRole = rolesRes.data.find((r) => r.slug === userData.role);
        if (userRole) {
          userData.permissions = userRole.permissions || [];
        }
      } catch (e) {
        // Fall back to static
        userData.permissions = ROLE_PERMISSIONS[userData.role] || [];
      }
    }

    localStorage.setItem('user', JSON.stringify(userData));
    _cachedUser = userData;
    _cachedUserRaw = JSON.stringify(userData);
    return userData;
  } catch (e) {
    return getUser();
  }
}
