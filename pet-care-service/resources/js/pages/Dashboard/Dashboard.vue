<template>
  <div class="container-fluid mt-5">
    <div class="row">
      <div class="col-md-3">
        <SidebarNav :userRole="userRole" />
      </div>
      <div class="col-md-9">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4>Dashboard</h4>
          </div>
          <div class="card-body">
            <div v-if="userRole === 'admin'" class="row">
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body">
                    <h5 class="card-title">Tổng Khách Hàng</h5>
                    <h2>{{ stats.totalCustomers }}</h2>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body">
                    <h5 class="card-title">Tổng Doanh Thu</h5>
                    <h2>{{ formatCurrency(stats.totalRevenue) }}</h2>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body">
                    <h5 class="card-title">Lịch Hôm Nay</h5>
                    <h2>{{ stats.appointmentsToday }}</h2>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body">
                    <h5 class="card-title">Nhân Viên</h5>
                    <h2>{{ stats.totalStaffs }}</h2>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="userRole === 'customer'" class="row">
              <div class="col-md-4">
                <div class="card bg-light">
                  <div class="card-body">
                    <h5 class="card-title">Lịch Sắp Tới</h5>
                    <h2>{{ stats.upcomingAppointments }}</h2>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card bg-light">
                  <div class="card-body">
                    <h5 class="card-title">Thú Cưng</h5>
                    <h2>{{ stats.totalPets }}</h2>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card bg-light">
                  <div class="card-body">
                    <h5 class="card-title">Chưa Thanh Toán</h5>
                    <h2>{{ stats.unpaidInvoices }}</h2>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="userRole === 'staff'" class="row">
              <div class="col-md-4">
                <div class="card bg-light">
                  <div class="card-body">
                    <h5 class="card-title">Lịch Hôm Nay</h5>
                    <h2>{{ stats.todayAppointments }}</h2>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card bg-light">
                  <div class="card-body">
                    <h5 class="card-title">Lịch Sắp Tới</h5>
                    <h2>{{ stats.upcomingAppointments }}</h2>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card bg-light">
                  <div class="card-body">
                    <h5 class="card-title">Đã Hoàn Thành</h5>
                    <h2>{{ stats.completedAppointments }}</h2>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-4">
              <h5>Thông Tin Tài Khoản</h5>
              <p><strong>Tên:</strong> {{ user.name }}</p>
              <p><strong>Email:</strong> {{ user.email }}</p>
              <p><strong>Vai Trò:</strong> {{ getRoleLabel(userRole) }}</p>
              <button @click="logout" class="btn btn-danger">Đăng Xuất</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SidebarNav from '@/components/SidebarNav.vue';
import api from '@/services/api';

export default {
  name: 'Dashboard',
  components: {
    SidebarNav
  },
  data() {
    return {
      userRole: localStorage.getItem('role') || 'customer',
      user: {
        name: localStorage.getItem('userName') || 'User',
        email: localStorage.getItem('userEmail') || 'user@example.com'
      },
      stats: {
        totalCustomers: 0,
        totalRevenue: 0,
        appointmentsToday: 0,
        totalStaffs: 0,
        upcomingAppointments: 0,
        totalPets: 0,
        unpaidInvoices: 0,
        todayAppointments: 0,
        completedAppointments: 0
      }
    };
  },
  mounted() {
    this.fetchStats();
  },
  methods: {
    async fetchStats() {
      try {
        if (this.userRole === 'customer') {
          // Fetch customer's pets
          const petsResponse = await api.get('/api/pets');
          this.stats.totalPets = petsResponse.data.data.length;

          // Fetch customer's appointments
          const appointmentsResponse = await api.get('/api/appointments');
          const appointments = appointmentsResponse.data.data;
          const today = new Date();
          today.setHours(0, 0, 0, 0);

          this.stats.upcomingAppointments = appointments.filter(apt => {
            const aptDate = new Date(apt.appointment_date);
            aptDate.setHours(0, 0, 0, 0);
            return aptDate >= today && apt.status !== 'cancelled';
          }).length;

          this.stats.unpaidInvoices = appointments.filter(apt => apt.status === 'pending').length;
        } else if (this.userRole === 'staff') {
          // Fetch staff appointments
          const appointmentsResponse = await api.get('/api/appointments');
          const appointments = appointmentsResponse.data.data;
          const today = new Date();
          today.setHours(0, 0, 0, 0);

          this.stats.todayAppointments = appointments.filter(apt => {
            const aptDate = new Date(apt.appointment_date);
            aptDate.setHours(0, 0, 0, 0);
            return aptDate.getTime() === today.getTime() && apt.status !== 'cancelled';
          }).length;

          this.stats.upcomingAppointments = appointments.filter(apt => {
            const aptDate = new Date(apt.appointment_date);
            aptDate.setHours(0, 0, 0, 0);
            return aptDate >= today && apt.status !== 'cancelled';
          }).length;

          this.stats.completedAppointments = appointments.filter(apt => apt.status === 'completed').length;
        }
      } catch (error) {
        console.error('Error fetching stats:', error);
      }
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(value);
    },
    getRoleLabel(role) {
      const labels = {
        admin: 'Quản Trị Viên',
        customer: 'Khách Hàng',
        staff: 'Nhân Viên'
      };
      return labels[role] || role;
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
.card {
  margin-bottom: 20px;
}

.card-header {
  font-weight: bold;
}

.bg-light {
  background-color: #f8f9fa !important;
}

h2 {
  color: #3498db;
  font-weight: bold;
  margin: 10px 0 0 0;
}
</style>
