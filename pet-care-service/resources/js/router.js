import { createRouter, createWebHistory } from 'vue-router';

// Import pages
import Home from './pages/Home.vue';
import Login from './pages/Auth/Login.vue';
import Register from './pages/Auth/Register.vue';
import Dashboard from './pages/Dashboard/Dashboard.vue';
import NotFound from './pages/NotFound.vue';

// Admin pages
import AdminDashboard from './pages/Admin/Dashboard.vue';
import AdminServices from './pages/Admin/Services.vue';
import AdminAppointments from './pages/Admin/Appointments.vue';
import AdminInvoices from './pages/Admin/Invoices.vue';
import AdminStaffs from './pages/Admin/Staffs.vue';
import AdminCustomers from './pages/Admin/Customers.vue';
import AdminReports from './pages/Admin/Reports.vue';
import AdminServiceRequests from './pages/Admin/ServiceRequests.vue';

// Customer pages
import CustomerAppointments from './pages/Customer/Appointments.vue';
import CustomerPets from './pages/Customer/Pets.vue';
import CustomerInvoices from './pages/Customer/Invoices.vue';
import CustomerProfile from './pages/Customer/Profile.vue';
import CustomerServiceRequests from './pages/Customer/ServiceRequests.vue';

// Staff pages
import StaffAppointments from './pages/Staff/Appointments.vue';
import StaffProfile from './pages/Staff/Profile.vue';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  // Admin routes
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, role: 'admin' }
  },
  {
    path: '/admin/services',
    name: 'AdminServices',
    component: AdminServices,
    meta: { requiresAuth: true, role: 'admin' }
  },
  {
    path: '/admin/appointments',
    name: 'AdminAppointments',
    component: AdminAppointments,
    meta: { requiresAuth: true, role: 'admin' }
  },
  {
    path: '/admin/invoices',
    name: 'AdminInvoices',
    component: AdminInvoices,
    meta: { requiresAuth: true, role: 'admin' }
  },
  {
    path: '/admin/staffs',
    name: 'AdminStaffs',
    component: AdminStaffs,
    meta: { requiresAuth: true, role: 'admin' }
  },
  {
    path: '/admin/customers',
    name: 'AdminCustomers',
    component: AdminCustomers,
    meta: { requiresAuth: true, role: 'admin' }
  },
  {
    path: '/admin/reports',
    name: 'AdminReports',
    component: AdminReports,
    meta: { requiresAuth: true, role: 'admin' }
  },
  {
    path: '/admin/service-requests',
    name: 'AdminServiceRequests',
    component: AdminServiceRequests,
    meta: { requiresAuth: true, role: 'admin' }
  },
  // Customer routes
  {
    path: '/customer/appointments',
    name: 'CustomerAppointments',
    component: CustomerAppointments,
    meta: { requiresAuth: true, role: 'customer' }
  },
  {
    path: '/customer/pets',
    name: 'CustomerPets',
    component: CustomerPets,
    meta: { requiresAuth: true, role: 'customer' }
  },
  {
    path: '/customer/invoices',
    name: 'CustomerInvoices',
    component: CustomerInvoices,
    meta: { requiresAuth: true, role: 'customer' }
  },
  {
    path: '/customer/profile',
    name: 'CustomerProfile',
    component: CustomerProfile,
    meta: { requiresAuth: true, role: 'customer' }
  },
  {
    path: '/customer/service-requests',
    name: 'CustomerServiceRequests',
    component: CustomerServiceRequests,
    meta: { requiresAuth: true, role: 'customer' }
  },
  // Staff routes
  {
    path: '/staff/appointments',
    name: 'StaffAppointments',
    component: StaffAppointments,
    meta: { requiresAuth: true, role: 'staff' }
  },
  {
    path: '/staff/profile',
    name: 'StaffProfile',
    component: StaffProfile,
    meta: { requiresAuth: true, role: 'staff' }
  },
  // 404
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFound
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Auth guard
router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('token');
  const userRole = localStorage.getItem('role');

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login');
  } else if (to.meta.role && userRole !== to.meta.role) {
    next('/dashboard');
  } else {
    next();
  }
});

export default router;
