<template>
  <div class="container-fluid py-4">
    <SidebarNav />

    <div class="main-content">
      <div class="page-header mb-4">
        <h2 class="mb-0">
          <i class="bi bi-speedometer2"></i> Dashboard Nhân Viên
        </h2>
        <small class="text-muted">Xin chào, {{ staffName }}!</small>
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

          <!-- Stats Cards -->
          <div v-else class="row mb-4 g-3">
            <div class="col-md-3">
              <div class="card bg-light">
                <div class="card-body text-center">
                  <i class="bi bi-calendar-event display-4 text-primary"></i>
                  <h5 class="card-title mt-2">Tổng Lịch Hẹn</h5>
                  <h2 class="text-primary">{{ totalAppointments }}</h2>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="card bg-light">
                <div class="card-body text-center">
                  <i class="bi bi-hourglass-split display-4 text-warning"></i>
                  <h5 class="card-title mt-2">Chờ Thực Hiện</h5>
                  <h2 class="text-warning">{{ pendingAppointments }}</h2>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="card bg-light">
                <div class="card-body text-center">
                  <i class="bi bi-play-circle display-4 text-info"></i>
                  <h5 class="card-title mt-2">Đang Thực Hiện</h5>
                  <h2 class="text-info">{{ processingAppointments }}</h2>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="card bg-light">
                <div class="card-body text-center">
                  <i class="bi bi-check-circle display-4 text-success"></i>
                  <h5 class="card-title mt-2">Hoàn Thành</h5>
                  <h2 class="text-success">{{ completedAppointments }}</h2>
                </div>
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
                @click="activeTab = 'upcoming'"
                :class="{ active: activeTab === 'upcoming' }"
                type="button"
              >
                <i class="bi bi-calendar3"></i> Sắp Tới
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                @click="activeTab = 'all'"
                :class="{ active: activeTab === 'all' }"
                type="button"
              >
                <i class="bi bi-list"></i> Tất Cả ({{ appointments.length }})
              </button>
            </li>
          </ul>

          <!-- Today's Appointments -->
          <div v-if="activeTab === 'today'" class="tab-pane fade show active">
            <div v-if="todayAppointments.length === 0" class="alert alert-info">
              <i class="bi bi-inbox"></i> Hôm nay bạn không có lịch hẹn nào.
            </div>
            <div v-else class="row g-3">
              <div v-for="apt in todayAppointments" :key="apt.id" class="col-md-6">
                <div class="card h-100">
                  <div class="card-header bg-primary bg-opacity-10">
                    <h6 class="mb-0">
                      <i class="bi bi-paw"></i> {{ apt.pet.name }}
                      <span :class="'badge bg-' + getStatusColor(apt.status) + ' ms-2'">
                        {{ getStatusLabel(apt.status) }}
                      </span>
                    </h6>
                  </div>
                  <div class="card-body">
                    <p class="mb-2">
                      <strong>{{ apt.customer.name }}</strong><br>
                      <small class="text-muted">{{ apt.customer.phone }}</small>
                    </p>
                    <p class="mb-2">
                      <strong>{{ apt.service.name }}</strong><br>
                      <small>💰 {{ formatCurrency(apt.service.price) }}</small>
                    </p>
                    <p class="text-muted mb-0">
                      <i class="bi bi-clock"></i> {{ apt.appointment_time }}
                    </p>
                    <button 
                      @click="showStatusModal(apt)"
                      class="btn btn-sm btn-primary mt-3 w-100"
                    >
                      <i class="bi bi-pencil"></i> Cập Nhật Trạng Thái
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Upcoming Appointments -->
          <div v-if="activeTab === 'upcoming'" class="tab-pane fade show active">
            <div v-if="upcomingAppointments.length === 0" class="alert alert-info">
              <i class="bi bi-inbox"></i> Không có lịch hẹn sắp tới.
            </div>
            <div v-else class="row g-3">
              <div v-for="apt in upcomingAppointments" :key="apt.id" class="col-md-6">
                <div class="card h-100">
                  <div class="card-header bg-info bg-opacity-10">
                    <h6 class="mb-0">
                      <i class="bi bi-paw"></i> {{ apt.pet.name }}
                      <span :class="'badge bg-' + getStatusColor(apt.status) + ' ms-2'">
                        {{ getStatusLabel(apt.status) }}
                      </span>
                    </h6>
                  </div>
                  <div class="card-body">
                    <p class="mb-2">
                      <strong>{{ apt.customer.name }}</strong><br>
                      <small class="text-muted">{{ apt.customer.phone }}</small>
                    </p>
                    <p class="mb-2">
                      <strong>{{ apt.service.name }}</strong><br>
                      <small>💰 {{ formatCurrency(apt.service.price) }}</small>
                    </p>
                    <p class="text-muted mb-0">
                      <i class="bi bi-calendar"></i> {{ formatDate(apt.appointment_date) }}<br>
                      <i class="bi bi-clock"></i> {{ apt.appointment_time }}
                    </p>
                    <button 
                      @click="showStatusModal(apt)"
                      class="btn btn-sm btn-primary mt-3 w-100"
                    >
                      <i class="bi bi-pencil"></i> Cập Nhật Trạng Thái
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- All Appointments -->
          <div v-if="activeTab === 'all'" class="tab-pane fade show active">
            <div v-if="appointments.length === 0" class="alert alert-info">
              <i class="bi bi-inbox"></i> Bạn không có lịch hẹn nào.
            </div>

            <div v-else>
              <!-- Filter -->
              <div class="row mb-3 g-2">
                <div class="col-md-4">
                  <select v-model="statusFilter" class="form-select form-select-sm">
                    <option value="">Tất Cả Trạng Thái</option>
                    <option value="pending">Chờ Xử Lý</option>
                    <option value="confirmed">Đã Xác Nhận</option>
                    <option value="processing">Đang Thực Hiện</option>
                    <option value="completed">Đã Hoàn Thành</option>
                    <option value="cancelled">Hủy</option>
                  </select>
                </div>
              </div>

              <!-- Table -->
              <table class="table table-hover table-striped">
                <thead class="table-dark">
                  <tr>
                    <th>ID</th>
                    <th>Khách Hàng</th>
                    <th>Thú Cưng</th>
                    <th>Dịch Vụ</th>
                    <th>Ngày Giờ</th>
                    <th>Trạng Thái</th>
                    <th>Hành Động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="apt in filteredAllAppointments" :key="apt.id">
                    <td><strong>#{{ apt.id }}</strong></td>
                    <td>
                      <div>{{ apt.customer.name }}</div>
                      <small class="text-muted">{{ apt.customer.phone }}</small>
                    </td>
                    <td>
                      <i class="bi bi-paw"></i> {{ apt.pet.name }}
                      <small class="d-block text-muted">{{ apt.pet.type }}</small>
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
                        title="Cập nhật trạng thái"
                      >
                        <i class="bi bi-pencil"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Status Update Modal -->
      <div v-if="selectedAppointment && showUpdateModal" class="modal d-block" :style="{ backgroundColor: 'rgba(0,0,0,0.5)' }">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Cập Nhật Trạng Thái Lịch Hẹn</h5>
              <button type="button" class="btn-close" @click="closeModal()"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <p class="mb-2">
                  <strong>{{ selectedAppointment.customer.name }}</strong> - 
                  <i class="bi bi-paw"></i> {{ selectedAppointment.pet.name }}
                </p>
                <p class="text-muted">
                  <strong>{{ selectedAppointment.service.name }}</strong><br>
                  {{ formatDate(selectedAppointment.appointment_date) }} 
                  {{ selectedAppointment.appointment_time }}
                </p>
              </div>

              <div class="mb-3">
                <label class="form-label">Trạng Thái Mới:</label>
                <select v-model="statusForm.status" class="form-select">
                  <option value="pending">Chờ Xử Lý</option>
                  <option value="confirmed">Đã Xác Nhận</option>
                  <option value="processing">Đang Thực Hiện</option>
                  <option value="completed">Đã Hoàn Thành</option>
                  <option value="cancelled">Hủy</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Ghi Chú:</label>
                <textarea 
                  v-model="statusForm.notes" 
                  class="form-control" 
                  rows="3"
                  placeholder="Thêm ghi chú về việc thực hiện dịch vụ..."
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
  name: 'StaffDashboard',
  components: {
    SidebarNav
  },
  data() {
    return {
      loading: false,
      error: null,
      appointments: [],
      staffName: 'Nhân Viên',
      activeTab: 'today',
      statusFilter: '',
      selectedAppointment: null,
      showUpdateModal: false,
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
    upcomingAppointments() {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return this.appointments.filter(apt => {
        const aptDate = new Date(apt.appointment_date);
        return aptDate > today && apt.status !== 'completed' && apt.status !== 'cancelled';
      }).slice(0, 10);
    },
    filteredAllAppointments() {
      if (!this.statusFilter) return this.appointments;
      return this.appointments.filter(apt => apt.status === this.statusFilter);
    },
    totalAppointments() {
      return this.appointments.length;
    },
    pendingAppointments() {
      return this.appointments.filter(apt => apt.status === 'pending').length;
    },
    processingAppointments() {
      return this.appointments.filter(apt => apt.status === 'processing').length;
    },
    completedAppointments() {
      return this.appointments.filter(apt => apt.status === 'completed').length;
    }
  },
  mounted() {
    this.fetchAppointments();
    this.getStaffName();
  },
  methods: {
    async fetchAppointments() {
      try {
        this.loading = true;
        this.error = null;
        const response = await api.get('/appointments');
        this.appointments = response.data.data || [];
        // Sort by appointment date
        this.appointments.sort((a, b) => 
          new Date(a.appointment_date + ' ' + a.appointment_time) - 
          new Date(b.appointment_date + ' ' + b.appointment_time)
        );
      } catch (error) {
        this.error = 'Lỗi khi tải lịch hẹn: ' + (error.response?.data?.message || error.message);
        console.error('Error fetching appointments:', error);
      } finally {
        this.loading = false;
      }
    },

    getStaffName() {
      const name = localStorage.getItem('userName');
      if (name) {
        this.staffName = name;
      }
    },

    showStatusModal(apt) {
      this.selectedAppointment = apt;
      this.statusForm = {
        status: apt.status,
        notes: apt.notes || ''
      };
      this.showUpdateModal = true;
    },

    closeModal() {
      this.showUpdateModal = false;
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
        this.error = 'Lỗi khi cập nhật: ' + (error.response?.data?.message || error.message);
        console.error('Error:', error);
      } finally {
        this.loading = false;
      }
    },

    async updateAppointmentStatus(appointmentId, newStatus) {
      try {
        await api.put(`/appointments/${appointmentId}`, {
          status: newStatus
        });
        this.fetchAppointments();
      } catch (error) {
        this.error = 'Lỗi: ' + (error.response?.data?.message || error.message);
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
.nav-link {
  cursor: pointer;
  color: #666;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px !important;
  border-bottom: 3px solid transparent;
}

.nav-link:hover {
  color: #0d6efd;
}

.nav-link.active {
  color: #0d6efd;
  border-bottom-color: #0d6efd;
  font-weight: 600;
}

.appointment-card {
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 12px;
  padding: 15px;
  transition: all 0.3s ease;
}

.appointment-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
  border-color: #0d6efd;
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

.modal-content {
  border-radius: 12px;
  border: none;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.modal-header {
  border-bottom: 2px solid #e9ecef;
  padding: 20px;
  background-color: #f8f9fa;
}

.modal-body {
  padding: 20px;
}

.modal-footer {
  border-top: 1px solid #e9ecef;
  padding: 15px 20px;
  background-color: #f8f9fa;
}

.form-label {
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 8px;
}

.form-control,
.form-select {
  border-radius: 8px;
  border: 1px solid #dee2e6;
  transition: all 0.3s ease;
}

.form-control:focus,
.form-select:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

.table {
  margin-bottom: 0;
}

.table thead {
  background: linear-gradient(90deg, #2c3e50, #34495e);
  color: white;
}

.table thead th {
  border: none;
  font-weight: 600;
  padding: 15px;
}

.table tbody tr {
  border-bottom: 1px solid #e9ecef;
  transition: all 0.2s ease;
}

.table tbody tr:hover {
  background-color: #f8f9fa;
  transform: scale(1.01);
}

.table tbody td {
  padding: 15px;
  vertical-align: middle;
}

.badge {
  padding: 0.35rem 0.65rem;
  border-radius: 12px;
  font-weight: 500;
  font-size: 0.85rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn {
  border-radius: 8px;
  transition: all 0.3s ease;
  font-weight: 500;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

.btn-primary {
  background: linear-gradient(90deg, #0d6efd, #0b5ed7);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}

.alert {
  border-radius: 8px;
  border: none;
  display: flex;
  align-items: center;
  gap: 12px;
}

.alert-info {
  background-color: #cfe2ff;
  color: #084298;
}

.text-muted {
  color: #6c757d;
  font-size: 0.9rem;
}

@media (max-width: 768px) {
  .table {
    font-size: 0.9rem;
  }

  .table tbody td {
    padding: 10px;
  }

  .appointment-card {
    padding: 12px;
  }

  .btn-sm {
    padding: 0.2rem 0.4rem;
    font-size: 0.8rem;
  }
}
</style>
