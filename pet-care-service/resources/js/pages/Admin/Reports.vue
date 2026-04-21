<template>
  <div class="container-fluid mt-5">
    <div class="row">
      <div class="col-md-3">
        <SidebarNav :userRole="userRole" />
      </div>
      <div class="col-md-9">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Báo Cáo & Thống Kê</h4>
          </div>
          <div class="card-body">
            <div class="row mb-4">
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <strong>Doanh Thu Tháng Này</strong>
                    <h3 class="text-success mt-2">{{ formatCurrency(monthlyRevenue) }}</h3>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <strong>Tổng Lịch Hẹn</strong>
                    <h3 class="text-info mt-2">{{ totalAppointments }}</h3>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <strong>Khách Hàng Mới</strong>
                    <h3 class="text-warning mt-2">{{ newCustomers }}</h3>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <strong>Dịch Vụ Bán Chạy</strong>
                    <h3 class="text-primary mt-2">{{ topService }}</h3>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mb-4">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h5 class="mb-0">Doanh Thu Theo Tháng</h5>
                  </div>
                  <div class="card-body">
                    <div class="chart">
                      <canvas id="revenueChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h5 class="mb-0">Dịch Vụ Bán Chạy</h5>
                  </div>
                  <div class="card-body">
                    <div v-for="service in topServices" :key="service.id" class="mb-3">
                      <div class="d-flex justify-content-between mb-2">
                        <span>{{ service.name }}</span>
                        <span class="badge bg-primary">{{ service.count }} đơn</span>
                      </div>
                      <div class="progress">
                        <div class="progress-bar" :style="{width: (service.count / 20 * 100) + '%'}"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <button @click="exportMonthlyReport" class="btn btn-success me-2">
                  <i class="bi bi-download"></i> Xuất Báo Cáo Tháng
                </button>
                <button @click="exportYearlyReport" class="btn btn-success">
                  <i class="bi bi-download"></i> Xuất Báo Cáo Năm
                </button>
              </div>
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
  name: 'AdminReports',
  components: {
    SidebarNav
  },
  data() {
    return {
      userRole: 'admin',
      monthlyRevenue: 85000000,
      totalAppointments: 125,
      newCustomers: 18,
      topService: 'Tắm',
      topServices: [
        { id: 1, name: 'Tắm', count: 45 },
        { id: 2, name: 'Cắt Tỉa Lông', count: 38 },
        { id: 3, name: 'Tiêm Phòng', count: 25 },
        { id: 4, name: 'Huấn Luyện', count: 12 },
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
    exportMonthlyReport() {
      alert('Xuất báo cáo tháng thành file PDF/Excel');
    },
    exportYearlyReport() {
      alert('Xuất báo cáo năm thành file PDF/Excel');
    }
  }
};
</script>

<style scoped>
.card {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.progress-bar {
  background-color: #3498db;
}

.chart {
  min-height: 300px;
}
</style>
