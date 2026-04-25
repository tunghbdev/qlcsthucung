<template>
  <div class="container-fluid py-4">
    <SidebarNav />

    <div class="main-content">
      <div class="page-header mb-4">
        <h2 class="mb-0">
          <i class="bi bi-speedometer2"></i> Dashboard Quản Trị
        </h2>
        <small class="text-muted">Tổng quan thông tin kinh doanh và hoạt động dịch vụ</small>
      </div>

      <div class="card">
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

            <div class="row mb-4 g-3">
              <div class="col-md-3">
                <div class="stat-card">
                  <i class="bi bi-people display-4 text-primary"></i>
                  <h6>Tổng Khách Hàng</h6>
                  <h3 class="text-primary">{{ stats.totalCustomers }}</h3>
                </div>
              </div>
              <div class="col-md-3">
                <div class="stat-card">
                  <i class="bi bi-wallet-fill display-4 text-success"></i>
                  <h6>Tổng Doanh Thu</h6>
                  <h3 class="text-success">{{ formatCurrency(stats.totalRevenue) }}</h3>
                </div>
              </div>
              <div class="col-md-3">
                <div class="stat-card">
                  <i class="bi bi-calendar2-event display-4 text-warning"></i>
                  <h6>Lịch Hôm Nay</h6>
                  <h3 class="text-warning">{{ stats.todayAppointments }}</h3>
                </div>
              </div>
              <div class="col-md-3">
                <div class="stat-card">
                  <i class="bi bi-check-circle display-4 text-info"></i>
                  <h6>Hoàn Thành</h6>
                  <h3 class="text-info">{{ stats.completedAppointments }}</h3>
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
.stat-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.stat-card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
  transform: translateY(-4px);
}

.stat-card i {
  font-size: 2.5rem;
  margin-bottom: 10px;
}

.stat-card h6 {
  font-size: 0.85rem;
  font-weight: 600;
  color: #6c757d;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 10px;
}

.stat-card h3 {
  font-size: 2rem;
  font-weight: 700;
  margin: 0;
}

.card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  margin-bottom: 20px;
}

.card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
}

.badge {
  padding: 0.4rem 0.8rem;
  border-radius: 12px;
  font-weight: 500;
  font-size: 0.85rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.progress-bar {
  background-color: #3498db;
  height: 8px;
  border-radius: 4px;
}

.btn {
  border-radius: 8px;
  transition: all 0.3s ease;
  margin-bottom: 10px;
  font-weight: 500;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.text-muted {
  color: #6c757d;
}

.border-bottom {
  border-bottom: 1px solid #e9ecef !important;
}

.d-flex {
  display: flex;
}

.justify-content-between {
  justify-content: space-between;
}

.mb-3 {
  margin-bottom: 1rem;
}

.pb-3 {
  padding-bottom: 1rem;
}

.mt-2 {
  margin-top: 0.5rem;
}

.mt-4 {
  margin-top: 2rem;
}

@media (max-width: 768px) {
  .stat-card {
    margin-bottom: 15px;
  }

  .stat-card h3 {
    font-size: 1.5rem;
  }

  .stat-card i {
    font-size: 2rem;
  }
}
</style>
