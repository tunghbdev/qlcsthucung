<template>
  <nav class="navbar navbar-vertical navbar-expand-md navbar-dark sticky-top bg-dark">
    <div class="nav-wrap">
      <div class="navbar-brand">
        <h5 class="mb-0 text-white">
          <i class="bi bi-paw-fill"></i> Pet Care
        </h5>
      </div>

      <button 
        class="navbar-toggler" 
        type="button" 
        @click="toggleNav"
        aria-controls="navbar-vertical"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbar-vertical">
        <ul class="navbar-nav flex-column mt-3">
          <!-- Common -->
          <li class="nav-item">
            <router-link to="/dashboard" class="nav-link">
              <i class="bi bi-speedometer2"></i> Dashboard
            </router-link>
          </li>

          <!-- Admin Menu -->
          <template v-if="userRole === 'admin'">
            <li class="nav-header mt-3">
              <span class="text-muted small">QUẢN LÝ</span>
            </li>
            <li class="nav-item">
              <router-link to="/admin" class="nav-link">
                <i class="bi bi-house"></i> Tổng Quan
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/appointments" class="nav-link">
                <i class="bi bi-calendar-event"></i> Lịch Hẹn
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/services" class="nav-link">
                <i class="bi bi-briefcase"></i> Dịch Vụ
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/invoices" class="nav-link">
                <i class="bi bi-receipt"></i> Hóa Đơn
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/staffs" class="nav-link">
                <i class="bi bi-people"></i> Nhân Viên
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/customers" class="nav-link">
                <i class="bi bi-person-circle"></i> Khách Hàng
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/reports" class="nav-link">
                <i class="bi bi-bar-chart"></i> Báo Cáo
              </router-link>
            </li>
          </template>

          <!-- Customer Menu -->
          <template v-else-if="userRole === 'customer'">
            <li class="nav-header mt-3">
              <span class="text-muted small">CÁ NHÂN</span>
            </li>
            <li class="nav-item">
              <router-link to="/customer/appointments" class="nav-link">
                <i class="bi bi-calendar-event"></i> Đặt Lịch
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/customer/pets" class="nav-link">
                <i class="bi bi-paw"></i> Thú Cưng Của Tôi
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/customer/invoices" class="nav-link">
                <i class="bi bi-receipt"></i> Hóa Đơn
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/customer/profile" class="nav-link">
                <i class="bi bi-person"></i> Thông Tin Cá Nhân
              </router-link>
            </li>
          </template>

          <!-- Staff Menu -->
          <template v-else-if="userRole === 'staff'">
            <li class="nav-header mt-3">
              <span class="text-muted small">CÔNG VIỆC</span>
            </li>
            <li class="nav-item">
              <router-link to="/staff/appointments" class="nav-link">
                <i class="bi bi-calendar-event"></i> Lịch Của Tôi
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/staff/profile" class="nav-link">
                <i class="bi bi-person"></i> Thông Tin Cá Nhân
              </router-link>
            </li>
          </template>

          <!-- Common -->
          <li class="nav-header mt-3">
            <span class="text-muted small">Hỗ TRỢ</span>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="bi bi-question-circle"></i> Trợ Giúp
            </a>
          </li>

          <li class="nav-item mt-3">
            <button @click="logout" class="btn btn-danger w-100">
              <i class="bi bi-box-arrow-right"></i> Đăng Xuất
            </button>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'SidebarNav',
  props: {
    userRole: {
      type: String,
      default: 'customer'
    }
  },
  methods: {
    toggleNav() {
      const navbarCollapse = document.getElementById('navbar-vertical');
      if (navbarCollapse) {
        navbarCollapse.classList.toggle('show');
      }
    },
    logout() {
      localStorage.removeItem('token');
      localStorage.removeItem('role');
      localStorage.removeItem('userName');
      localStorage.removeItem('userEmail');
      this.$router.push('/login');
    }
  }
};
</script>

<style scoped>
.navbar-vertical {
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 0;
  height: 100vh;
  overflow-y: auto;
  padding: 0;
}

.nav-wrap {
  padding: 15px;
}

.navbar-brand {
  margin-bottom: 20px;
}

.nav-header {
  font-size: 12px;
  font-weight: bold;
  margin-top: 20px;
  margin-bottom: 10px;
  text-transform: uppercase;
  padding: 0 10px;
}

.navbar-nav {
  list-style: none;
  padding: 0;
}

.nav-item {
  margin-bottom: 5px;
}

.nav-link {
  color: #b8c1cc;
  text-decoration: none;
  padding: 10px 15px;
  display: block;
  border-radius: 5px;
  transition: all 0.3s ease;
}

.nav-link:hover {
  color: white;
  background-color: rgba(255, 255, 255, 0.1);
}

.nav-link.router-link-active {
  color: white;
  background-color: #0d6efd;
  font-weight: bold;
}

.nav-link i {
  margin-right: 10px;
  width: 20px;
  text-align: center;
}

.btn-danger {
  margin-top: 20px;
}

@media (max-width: 768px) {
  .navbar-vertical {
    position: relative;
    height: auto;
  }

  .collapse:not(.show) {
    display: none;
  }

  .collapse.show {
    display: block;
  }
}
</style>
