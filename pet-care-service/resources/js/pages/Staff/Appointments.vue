<template>
  <div class="container-fluid py-4">
    <SidebarNav />

    <div class="main-content">
      <div class="page-header mb-4">
        <h2 class="mb-0">
          <i class="bi bi-calendar-event"></i> Lịch Hẹn Của Tôi
        </h2>
        <small class="text-muted">Xem và quản lý các lịch hẹn được gán cho bạn</small>
      </div>

      <div class="card">
        <div class="card-body">
          <!-- Error Alert -->
          <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ error }}
            <button type="button" class="btn-close" @click="error = null"></button>
          </div>

          <!-- Loading -->
          <div v-if="loading" class="alert alert-info">
            <div class="spinner-border spinner-border-sm" role="status">
              <span class="visually-hidden">Đang tải...</span>
            </div>
            Đang tải...
          </div>

          <!-- Stats -->
          <div v-else class="row mb-4 g-3">
            <div class="col-md-3">
              <div class="stat-card">
                <i class="bi bi-calendar display-4 text-primary"></i>
                <h6>Tổng Lịch Hẹn</h6>
                <h3 class="text-primary">{{ totalAppointments }}</h3>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stat-card">
                <i class="bi bi-hourglass-split display-4 text-warning"></i>
                <h6>Chờ Thực Hiện</h6>
                <h3 class="text-warning">{{ pendingCount }}</h3>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stat-card">
                <i class="bi bi-play-circle display-4 text-info"></i>
                <h6>Đang Thực Hiện</h6>
                <h3 class="text-info">{{ processingCount }}</h3>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stat-card">
                <i class="bi bi-check-circle display-4 text-success"></i>
                <h6>Hoàn Thành</h6>
                <h3 class="text-success">{{ completedCount }}</h3>
              </div>
            </div>
          </div>
          <!-- Tabs -->
          <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                @click="activeTab = 'today'"
                :class="{ active: activeTab === 'today' }"
                type="button"
              >
                <i class="bi bi-calendar2-day"></i> Hôm Nay
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                @click="activeTab = 'all'"
                :class="{ active: activeTab === 'all' }"
                type="button"
              >
                <i class="bi bi-list"></i> Tất Cả Lịch Hẹn
              </button>
            </li>
          </ul>

          <!-- Today Tab -->
          <div v-if="activeTab === 'today'" class="tab-pane fade show active">
            <div v-if="todayAppointments.length === 0" class="alert alert-info">
              <i class="bi bi-inbox"></i> Hôm nay bạn không có lịch hẹn nào.
            </div>
            <div v-else class="row g-3">
              <div v-for="apt in todayAppointments" :key="apt.id" class="col-md-6">
                <div class="appointment-card">
                  <div class="appointment-header">
                    <h6>{{ apt.pet.name }}</h6>
                    <span :class="'badge bg-' + getStatusColor(apt.status)">
                      {{ getStatusLabel(apt.status) }}
                    </span>
                  </div>
                  <div class="appointment-body">
                    <p class="mb-2">
                      <strong>🐾 Thú Cưng:</strong> {{ apt.pet.type }}<br>
                      <strong>💼 Dịch Vụ:</strong> {{ apt.service.name }}<br>
                      <strong>💰 Giá:</strong> {{ formatCurrency(apt.service.price) }}
                    </p>
                    <p class="text-muted mb-0">
                      <i class="bi bi-clock"></i> {{ apt.appointment_time }}
                    </p>
                  </div>
                  <div class="appointment-actions">
                    <button 
                      @click="showStatusModal(apt)"
                      class="btn btn-sm btn-primary"
                    >
                      <i class="bi bi-pencil"></i> Cập Nhật
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- All Tab -->
          <div v-if="activeTab === 'all'" class="tab-pane fade show active">
            <div class="mb-3">
              <select v-model="statusFilter" class="form-select form-select-sm">
                <option value="">Tất Cả Trạng Thái</option>
                <option value="pending">Chờ Xử Lý</option>
                <option value="confirmed">Đã Xác Nhận</option>
                <option value="processing">Đang Thực Hiện</option>
                <option value="completed">Đã Hoàn Thành</option>
              </select>
            </div>

            <div v-if="filteredAppointments.length === 0" class="alert alert-info">
              <i class="bi bi-inbox"></i> Không có lịch hẹn nào.
            </div>

            <table v-else class="table table-hover table-striped">
              <thead class="table-dark">
                <tr>
                  <th>Thú Cưng</th>
                  <th>Khách Hàng</th>
                  <th>Dịch Vụ</th>
                  <th>Ngày Giờ</th>
                  <th>Trạng Thái</th>
                  <th>Hành Động</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="apt in filteredAppointments" :key="apt.id">
                  <td>
                    <i class="bi bi-paw"></i> {{ apt.pet.name }}
                    <small class="d-block text-muted">{{ apt.pet.type }}</small>
                  </td>
                  <td>
                    <div>{{ apt.customer.name }}</div>
                    <small class="text-muted">{{ apt.customer.phone }}</small>
                  </td>
                  <td>{{ apt.service.name }}</td>
                  <td>
                    {{ formatDate(apt.appointment_date) }}
                    <br>
                    <strong>{{ apt.appointment_time }}</strong>
                  </td>
                  <td>
                    <span :class="'badge bg-' + getStatusColor(apt.status)">
                      {{ getStatusLabel(apt.status) }}
                    </span>
                  </td>
                  <td>
                    <button 
                      @click="showStatusModal(apt)"
                      class="btn btn-sm btn-outline-primary"
                    >
                      <i class="bi bi-pencil"></i> Cập Nhật
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Status Update Modal -->
      <div v-if="selectedAppointment && showModal" class="modal d-block" :style="{ backgroundColor: 'rgba(0,0,0,0.5)' }">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Cập Nhật Trạng Thái Lịch Hẹn</h5>
              <button type="button" class="btn-close" @click="closeModal()"></button>
            </div>
            <div class="modal-body">
              <p class="mb-3">
                <strong>{{ selectedAppointment.customer.name }}</strong> - 
                <i class="bi bi-paw"></i> {{ selectedAppointment.pet.name }}
              </p>
              <p class="text-muted mb-3">
                {{ selectedAppointment.service.name }}<br>
                {{ formatDate(selectedAppointment.appointment_date) }} 
                {{ selectedAppointment.appointment_time }}
              </p>

              <div class="mb-3">
                <label class="form-label">Trạng Thái Mới:</label>
                <select v-model="statusForm.status" class="form-select">
                  <option value="pending">Chờ Xử Lý</option>
                  <option value="confirmed">Đã Xác Nhận</option>
                  <option value="processing">Đang Thực Hiện</option>
                  <option value="completed">Đã Hoàn Thành</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Ghi Chú:</label>
                <textarea 
                  v-model="statusForm.notes" 
                  class="form-control" 
                  rows="3"
                  placeholder="Thêm ghi chú..."
                ></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeModal()">Hủy</button>
              <button 
                type="button" 
                class="btn btn-primary" 
                @click="updateStatus()"
                :disabled="loading"
              >
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Cập Nhật
              </button>
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
  name: 'StaffAppointments',
  components: {
    SidebarNav
  },
  data() {
    return {
      loading: false,
      error: null,
      appointments: [],
      activeTab: 'today',
      statusFilter: '',
      selectedAppointment: null,
      showModal: false,
      statusForm: {
        status: '',
        notes: ''
      }
    };
  },
  computed: {
    todayAppointments() {
      const today = new Date().toISOString().split('T')[0];
      return this.appointments.filter(apt => apt.appointment_date === today);
    },
    filteredAppointments() {
      if (!this.statusFilter) return this.appointments;
      return this.appointments.filter(apt => apt.status === this.statusFilter);
    },
    totalAppointments() {
      return this.appointments.length;
    },
    pendingCount() {
      return this.appointments.filter(apt => apt.status === 'pending' || apt.status === 'confirmed').length;
    },
    processingCount() {
      return this.appointments.filter(apt => apt.status === 'processing').length;
    },
    completedCount() {
      return this.appointments.filter(apt => apt.status === 'completed').length;
    }
  },
  mounted() {
    this.fetchAppointments();
  },
  methods: {
    async fetchAppointments() {
      try {
        this.loading = true;
        this.error = null;
        const response = await api.get('/appointments');
        this.appointments = response.data.data || [];
        this.appointments.sort((a, b) => 
          new Date(a.appointment_date + ' ' + a.appointment_time) - 
          new Date(b.appointment_date + ' ' + b.appointment_time)
        );
      } catch (error) {
        this.error = 'Lỗi khi tải lịch hẹn: ' + (error.response?.data?.message || error.message);
      } finally {
        this.loading = false;
      }
    },

    showStatusModal(apt) {
      this.selectedAppointment = apt;
      this.statusForm = {
        status: apt.status,
        notes: apt.notes || ''
      };
      this.showModal = true;
    },

    closeModal() {
      this.showModal = false;
      this.selectedAppointment = null;
      this.statusForm = {
        status: '',
        notes: ''
      };
    },

    async updateStatus() {
      if (!this.selectedAppointment) return;

      try {
        this.loading = true;
        await api.put(`/appointments/${this.selectedAppointment.id}`, {
          status: this.statusForm.status,
          notes: this.statusForm.notes
        });

        if (this.statusForm.status === 'completed') {
          alert('Lịch hẹn hoàn thành! Hóa đơn sẽ được tạo tự động.');
        } else {
          alert('Cập nhật trạng thái thành công!');
        }

        this.closeModal();
        this.fetchAppointments();
      } catch (error) {
        this.error = 'Lỗi: ' + (error.response?.data?.message || error.message);
      } finally {
        this.loading = false;
      }
    },

    formatDate(date) {
      return new Date(date).toLocaleDateString('vi-VN');
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
        confirmed: 'success'
      };
      return colors[status] || 'secondary';
    },

    getStatusLabel(status) {
      const labels = {
        completed: 'Đã Hoàn Thành',
        processing: 'Đang Thực Hiện',
        pending: 'Chờ Xử Lý',
        confirmed: 'Đã Xác Nhận'
      };
      return labels[status] || status;
    }
  }
};
</script>

<style scoped>
.nav-link {
  cursor: pointer;
  color: #666;
}

.nav-link.active {
  color: #0d6efd;
  border-bottom: 2px solid #0d6efd;
}

.appointment-card {
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  padding: 15px;
  transition: all 0.3s ease;
}

.appointment-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.appointment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  padding-bottom: 12px;
  border-bottom: 1px solid #e9ecef;
}

.appointment-header h6 {
  margin: 0;
  font-weight: 600;
  color: #333;
}

.appointment-body {
  margin-bottom: 12px;
  font-size: 0.95rem;
}

.appointment-body p {
  margin-bottom: 8px;
  line-height: 1.6;
}

.appointment-actions {
  display: flex;
  gap: 8px;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1050;
  width: 100%;
  height: 100%;
  overflow: hidden;
  outline: 0;
}

.modal-dialog-centered {
  display: flex;
  align-items: center;
  min-height: calc(100% - 1rem);
}

.table {
  margin-bottom: 0;
}

.badge {
  padding: 0.35rem 0.65rem;
  border-radius: 12px;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}
</style>
