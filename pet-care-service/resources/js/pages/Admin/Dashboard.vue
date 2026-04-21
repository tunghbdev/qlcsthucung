<template>
  <div class="container-fluid mt-5">
    <div class="row">
      <div class="col-md-3">
        <SidebarNav :userRole="userRole" />
      </div>
      <div class="col-md-9">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Dashboard Quản Trị</h4>
          </div>
          <div class="card-body">
            <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ error }}
              <button type="button" class="btn-close" @click="error = null"></button>
            </div>

            <div v-if="loading" class="alert alert-info">
              <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Đang tải...</span>
              </div>
              Đang xử lý...
            </div>

            <div class="row mb-4">
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <i class="bi bi-people display-4 text-primary"></i>
                    <h5 class="card-title mt-2">Tổng Khách Hàng</h5>
                    <h2 class="text-primary">{{ stats.totalCustomers }}</h2>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <i class="bi bi-wallet-fill display-4 text-success"></i>
                    <h5 class="card-title mt-2">Tổng Doanh Thu</h5>
                    <h2 class="text-success">{{ formatCurrency(stats.totalRevenue) }}</h2>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <i class="bi bi-calendar2-event display-4 text-warning"></i>
                    <h5 class="card-title mt-2">Lịch Hôm Nay</h5>
                    <h2 class="text-warning">{{ stats.todayAppointments }}</h2>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <i class="bi bi-check-circle display-4 text-info"></i>
                    <h5 class="card-title mt-2">Hoàn Thành</h5>
                    <h2 class="text-info">{{ stats.completedAppointments }}</h2>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h5 class="mb-0">Lịch Hẹn Sắp Tới (7 Ngày)</h5>
                  </div>
                  <div class="card-body">
                    <div v-if="stats.upcomingAppointments.length === 0" class="alert alert-info">
                      Không có lịch hẹn sắp tới
                    </div>
                    <table v-else class="table table-sm">
                      <thead>
                        <tr>
                          <th>Khách Hàng</th>
                          <th>Dịch Vụ</th>
                          <th>Giờ</th>
                          <th>Trạng Thái</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="apt in stats.upcomingAppointments" :key="apt.id">
                          <td>{{ apt.customer_name }}</td>
                          <td>{{ apt.service_name }}</td>
                          <td>{{ apt.scheduled_at.split(' ')[1] }}</td>
                          <td>
                            <span :class="'badge bg-' + getStatusColor(apt.status)">{{ getStatusLabel(apt.status) }}</span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h5 class="mb-0">Hóa Đơn Gần Đây</h5>
                  </div>
                  <div class="card-body">
                    <div v-if="stats.recentInvoices.length === 0" class="alert alert-info">
                      Không có hóa đơn gần đây
                    </div>
                    <div v-else>
                      <div v-for="invoice in stats.recentInvoices" :key="invoice.id" class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between">
                          <strong>{{ invoice.invoice_number }}</strong>
                          <span :class="'badge bg-' + (invoice.status === 'paid' ? 'success' : 'warning')">
                            {{ invoice.status === 'paid' ? 'Đã Thanh Toán' : 'Chưa Thanh Toán' }}
                          </span>
                        </div>
                        <small class="text-muted">{{ invoice.customer_name }}</small>
                        <div class="mt-2">
                          <small>{{ formatCurrency(invoice.total_amount) }}</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-4">
              <div class="row">
                <div class="col-md-4">
                  <div class="card bg-light">
                    <div class="card-body text-center">
                      <h6>Tổng Lịch Hẹn</h6>
                      <h3 class="text-primary">{{ stats.totalAppointments }}</h3>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card bg-light">
                    <div class="card-body text-center">
                      <h6>Đã Thanh Toán</h6>
                      <h3 class="text-success">{{ formatCurrency(stats.paidRevenue) }}</h3>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card bg-light">
                    <div class="card-body text-center">
                      <h6>Chưa Thanh Toán</h6>
                      <h3 class="text-warning">{{ formatCurrency(stats.pendingRevenue) }}</h3>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-4">
              <router-link to="/admin/appointments" class="btn btn-primary me-2">
                <i class="bi bi-calendar"></i> Quản Lý Lịch Hẹn
              </router-link>
              <router-link to="/admin/services" class="btn btn-secondary me-2">
                <i class="bi bi-gear"></i> Quản Lý Dịch Vụ
              </router-link>
              <router-link to="/admin/staffs" class="btn btn-info me-2">
                <i class="bi bi-people"></i> Quản Lý Nhân Viên
              </router-link>
              <router-link to="/admin/invoices" class="btn btn-warning">
                <i class="bi bi-receipt"></i> Quản Lý Hóa Đơn
              </router-link>
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
  name: 'AdminDashboard',
  components: {
    SidebarNav
  },
  data() {
    return {
      userRole: 'admin',
      loading: false,
      error: null,
      stats: {
        totalCustomers: 0,
        totalAppointments: 0,
        completedAppointments: 0,
        totalRevenue: 0,
        paidRevenue: 0,
        pendingRevenue: 0,
        todayAppointments: 0,
        upcomingAppointments: [],
        recentInvoices: []
      }
    };
  },
  mounted() {
    this.fetchStats();
  },
  methods: {
    async fetchStats() {
      try {
        this.loading = true;
        this.error = null;
        const response = await api.get('/dashboard/stats');
        this.stats = response.data.data;
      } catch (error) {
        this.error = 'Lỗi khi tải dữ liệu: ' + (error.response?.data?.message || error.message);
        console.error('Error fetching stats:', error);
      } finally {
        this.loading = false;
      }
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(value);
    },
    getStatusColor(status) {
      const colors = {
        completed: 'success',
        processing: 'info',
        pending: 'warning',
        confirmed: 'success',
        cancelled: 'danger'
      };
      return colors[status] || 'secondary';
    },
    getStatusLabel(status) {
      const labels = {
        completed: 'Đã Hoàn Thành',
        processing: 'Đang Thực Hiện',
        pending: 'Chờ Xử Lý',
        confirmed: 'Đã Xác Nhận',
        cancelled: 'Hủy'
      };
      return labels[status] || status;
    }
  }
};
</script>

<style scoped>
.display-4 {
  font-size: 2.5rem;
}

.card {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.progress-bar {
  background-color: #3498db;
}

.btn {
  margin-bottom: 10px;
}
</style>
