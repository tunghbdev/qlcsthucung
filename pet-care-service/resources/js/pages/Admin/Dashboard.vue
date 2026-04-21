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
            <div class="row mb-4">
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <i class="bi bi-people display-4 text-primary"></i>
                    <h5 class="card-title mt-2">Tổng Khách Hàng</h5>
                    <h2 class="text-primary">250</h2>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <i class="bi bi-wallet-fill display-4 text-success"></i>
                    <h5 class="card-title mt-2">Tổng Doanh Thu</h5>
                    <h2 class="text-success">{{ formatCurrency(totalRevenue) }}</h2>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <i class="bi bi-calendar2-event display-4 text-warning"></i>
                    <h5 class="card-title mt-2">Lịch Hôm Nay</h5>
                    <h2 class="text-warning">15</h2>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <i class="bi bi-person-badge display-4 text-info"></i>
                    <h5 class="card-title mt-2">Nhân Viên</h5>
                    <h2 class="text-info">12</h2>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h5 class="mb-0">Lịch Hẹn Gần Đây</h5>
                  </div>
                  <div class="card-body">
                    <table class="table table-sm">
                      <thead>
                        <tr>
                          <th>Khách Hàng</th>
                          <th>Dịch Vụ</th>
                          <th>Giờ</th>
                          <th>Trạng Thái</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="apt in recentAppointments" :key="apt.id">
                          <td>{{ apt.customer }}</td>
                          <td>{{ apt.service }}</td>
                          <td>{{ apt.time }}</td>
                          <td>
                            <span :class="'badge bg-' + getStatusColor(apt.status)">{{ apt.status }}</span>
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
                    <h5 class="mb-0">Dịch Vụ Bán Chạy Nhất</h5>
                  </div>
                  <div class="card-body">
                    <div v-for="service in topServices" :key="service.id" class="mb-3 pb-3 border-bottom">
                      <div class="d-flex justify-content-between">
                        <strong>{{ service.name }}</strong>
                        <span class="badge bg-primary">{{ service.count }} đơn</span>
                      </div>
                      <div class="progress mt-2">
                        <div class="progress-bar" :style="{width: (service.count / 20 * 100) + '%'}"></div>
                      </div>
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
              <router-link to="/admin/reports" class="btn btn-warning">
                <i class="bi bi-bar-chart"></i> Báo Cáo
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

export default {
  name: 'AdminDashboard',
  components: {
    SidebarNav
  },
  data() {
    return {
      userRole: 'admin',
      totalRevenue: 125000000,
      recentAppointments: [
        { id: 1, customer: 'Nguyễn Văn A', service: 'Tắm', time: '09:00', status: 'Đã Hoàn Thành' },
        { id: 2, customer: 'Trần Thị B', service: 'Cắt tỉa lông', time: '10:30', status: 'Đang Thực Hiện' },
        { id: 3, customer: 'Phạm Văn C', service: 'Tiêm phòng', time: '14:00', status: 'Chờ Xử Lý' },
        { id: 4, customer: 'Võ Thị D', service: 'Huấn luyện', time: '16:00', status: 'Chờ Xử Lý' },
      ],
      topServices: [
        { id: 1, name: 'Tắm', count: 18 },
        { id: 2, name: 'Cắt tỉa lông', count: 16 },
        { id: 3, name: 'Tiêm phòng', count: 12 },
        { id: 4, name: 'Huấn luyện', count: 8 },
      ]
    };
  },
  methods: {
    formatCurrency(value) {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(value);
    },
    getStatusColor(status) {
      const colors = {
        'Đã Hoàn Thành': 'success',
        'Đang Thực Hiện': 'info',
        'Chờ Xử Lý': 'warning',
        'Hủy': 'danger'
      };
      return colors[status] || 'secondary';
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
