<template>
  <div class="container-fluid mt-5">
    <div class="row">
      <div class="col-md-3">
        <SidebarNav :userRole="userRole" />
      </div>
      <div class="col-md-9">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Lịch Của Tôi - Nhân Viên</h4>
          </div>
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <strong>Lịch Hôm Nay</strong>
                    <h3 class="text-info mt-2">{{ todayAppointments }}</h3>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <strong>Lịch Sắp Tới</strong>
                    <h3 class="text-warning mt-2">{{ upcomingAppointments }}</h3>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <strong>Đã Hoàn Thành</strong>
                    <h3 class="text-success mt-2">{{ completedAppointments }}</h3>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <strong>Doanh Thu</strong>
                    <h3 class="text-primary mt-2">{{ formatCurrency(revenue) }}</h3>
                  </div>
                </div>
              </div>
            </div>

            <ul class="nav nav-tabs mb-3">
              <li class="nav-item">
                <a class="nav-link" :class="{active: activeTab === 'today'}" href="#" @click.prevent="activeTab = 'today'">
                  Hôm Nay
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{active: activeTab === 'upcoming'}" href="#" @click.prevent="activeTab = 'upcoming'">
                  Sắp Tới
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{active: activeTab === 'history'}" href="#" @click.prevent="activeTab = 'history'">
                  Hoàn Thành
                </a>
              </li>
            </ul>

            <div v-show="activeTab === 'today'" class="tab-pane active">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Khách Hàng</th>
                    <th>Thú Cưng</th>
                    <th>Dịch Vụ</th>
                    <th>Giờ</th>
                    <th>Trạng Thái</th>
                    <th>Hành Động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="apt in todayList" :key="apt.id">
                    <td>{{ apt.customer }}</td>
                    <td>{{ apt.pet }}</td>
                    <td>{{ apt.service }}</td>
                    <td>{{ apt.time }}</td>
                    <td>
                      <span :class="'badge bg-' + getStatusColor(apt.status)">
                        {{ getStatusLabel(apt.status) }}
                      </span>
                    </td>
                    <td>
                      <button v-if="apt.status === 'pending'" @click="startAppointment(apt)" class="btn btn-sm btn-success">
                        <i class="bi bi-play"></i> Bắt Đầu
                      </button>
                      <button v-else-if="apt.status === 'processing'" @click="completeAppointment(apt)" class="btn btn-sm btn-info">
                        <i class="bi bi-check2"></i> Hoàn Thành
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-show="activeTab === 'upcoming'" class="tab-pane active">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Khách Hàng</th>
                    <th>Thú Cưng</th>
                    <th>Dịch Vụ</th>
                    <th>Ngày Giờ</th>
                    <th>Hành Động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="apt in upcomingList" :key="apt.id">
                    <td>{{ apt.customer }}</td>
                    <td>{{ apt.pet }}</td>
                    <td>{{ apt.service }}</td>
                    <td>{{ apt.dateTime }}</td>
                    <td>
                      <button @click="viewAppointment(apt)" class="btn btn-sm btn-info">
                        <i class="bi bi-eye"></i> Xem
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-show="activeTab === 'history'" class="tab-pane active">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Khách Hàng</th>
                    <th>Thú Cưng</th>
                    <th>Dịch Vụ</th>
                    <th>Ngày</th>
                    <th>Giá</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="apt in historyList" :key="apt.id">
                    <td>{{ apt.customer }}</td>
                    <td>{{ apt.pet }}</td>
                    <td>{{ apt.service }}</td>
                    <td>{{ apt.date }}</td>
                    <td>{{ formatCurrency(apt.price) }}</td>
                  </tr>
                </tbody>
              </table>
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
  name: 'StaffAppointments',
  components: {
    SidebarNav
  },
  data() {
    return {
      userRole: 'staff',
      activeTab: 'today',
      todayAppointments: 5,
      upcomingAppointments: 12,
      completedAppointments: 156,
      revenue: 25000000,
      todayList: [
        { id: 1, customer: 'Nguyễn Văn A', pet: 'Bess (Chó)', service: 'Tắm', time: '09:00', status: 'pending' },
        { id: 2, customer: 'Trần Thị B', pet: 'Miu (Mèo)', service: 'Cắt Tỉa Lông', time: '10:30', status: 'processing' },
        { id: 3, customer: 'Phạm Văn C', pet: 'Puppy (Chó)', service: 'Tiêm Phòng', time: '14:00', status: 'pending' },
      ],
      upcomingList: [
        { id: 4, customer: 'Võ Thị D', pet: 'Leo (Chó)', service: 'Huấn Luyện', dateTime: '2024-04-21 15:00' },
        { id: 5, customer: 'Lê Văn E', pet: 'Max (Chó)', service: 'Tắm', dateTime: '2024-04-22 09:00' },
      ],
      historyList: [
        { id: 10, customer: 'Nguyễn Văn A', pet: 'Bess (Chó)', service: 'Tắm', date: '2024-04-19', price: 150000 },
        { id: 11, customer: 'Trần Thị B', pet: 'Miu (Mèo)', service: 'Cắt Tỉa Lông', date: '2024-04-18', price: 200000 },
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
        completed: 'success',
        processing: 'info',
        pending: 'warning'
      };
      return colors[status] || 'secondary';
    },
    getStatusLabel(status) {
      const labels = {
        completed: 'Đã Hoàn Thành',
        processing: 'Đang Thực Hiện',
        pending: 'Chờ Xử Lý'
      };
      return labels[status] || status;
    },
    startAppointment(apt) {
      apt.status = 'processing';
      alert('Đã bắt đầu dịch vụ cho: ' + apt.customer);
    },
    completeAppointment(apt) {
      apt.status = 'completed';
      alert('Đã hoàn thành dịch vụ cho: ' + apt.customer);
    },
    viewAppointment(apt) {
      alert('Chi tiết lịch hẹn: ' + apt.customer);
    }
  }
};
</script>

<style scoped>
.card {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.nav-link {
  color: #495057;
  cursor: pointer;
}

.nav-link.active {
  color: #0d6efd;
  border-bottom: 2px solid #0d6efd;
}

.table {
  background-color: white;
}

.btn-sm {
  padding: 5px 10px;
}
</style>
