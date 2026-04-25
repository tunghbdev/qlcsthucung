<template>
  <div class="container-fluid py-4">
    <SidebarNav />

    <div class="main-content">
      <div class="page-header mb-4">
        <h2 class="mb-0">
          <i class="bi bi-calendar-event"></i> Quản Lý Lịch Hẹn
        </h2>
        <small class="text-muted">Quản lý và cập nhật trạng thái tất cả lịch hẹn dịch vụ</small>
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

          <!-- Filters -->
          <div v-else class="row mb-4 g-3">
            <div class="col-md-5">
              <div class="input-group">
                <span class="input-group-text bg-white">
                  <i class="bi bi-search"></i>
                </span>
                <input 
                  v-model="filters.search" 
                  type="text" 
                  class="form-control" 
                  placeholder="Tìm kiếm khách hàng hoặc thú cưng..."
                >
              </div>
            </div>
            <div class="col-md-4">
              <select v-model="filters.status" class="form-select">
                <option value="">Tất Cả Trạng Thái</option>
                <option value="pending">Chờ Xử Lý</option>
                <option value="confirmed">Đã Xác Nhận</option>
                <option value="processing">Đang Thực Hiện</option>
                <option value="completed">Đã Hoàn Thành</option>
                <option value="cancelled">Hủy</option>
              </select>
            </div>
            <div class="col-md-3">
              <button @click="fetchAppointments" class="btn btn-primary w-100">
                <i class="bi bi-arrow-clockwise"></i> Tải Lại
              </button>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="!loading && filteredAppointments.length === 0" class="alert alert-info text-center py-5">
            <i class="bi bi-inbox display-4"></i>
            <p class="mt-3">Không có lịch hẹn nào.</p>
          </div>

          <!-- Table -->
          <table v-else class="table table-hover table-striped">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Khách Hàng</th>
                <th>Thú Cưng</th>
                <th>Dịch Vụ</th>
                <th>Ngày Giờ</th>
                <th>Trạng Thái</th>
                <th>Ghi Chú</th>
                <th>Hành Động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="apt in filteredAppointments" :key="apt.id">
                <td><strong>#{{ apt.id }}</strong></td>
                <td>{{ apt.customer.user.name }}</td>
                <td>
                  <i class="bi bi-paw"></i> {{ apt.pet.name }}
                </td>
                <td>{{ apt.service.name }}</td>
                <td>
                  {{ formatDate(apt.appointment_date) }}
                  <br>
                  <small class="text-muted">{{ apt.appointment_time }}</small>
                </td>
                <td>
                  <span :class="'badge bg-' + getStatusColor(apt.status)">
                    {{ getStatusLabel(apt.status) }}
                  </span>
                </td>
                <td>
                  <small v-if="apt.notes">{{ apt.notes.substring(0, 30) }}...</small>
                  <small v-else class="text-muted">—</small>
                </td>
                <td>
                  <div class="btn-group btn-group-sm" role="group">
                    <button 
                      @click="showStatusModal(apt)"
                      class="btn btn-outline-primary"
                      :disabled="loading"
                      title="Cập nhật trạng thái"
                    >
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button 
                      v-if="apt.status !== 'completed' && apt.status !== 'cancelled'"
                      @click="markAsCompleted(apt)"
                      class="btn btn-outline-success"
                      :disabled="loading"
                      title="Hoàn thành (tạo hóa đơn)"
                    >
                      <i class="bi bi-check-circle"></i>
                    </button>
                    <button 
                      v-if="apt.status !== 'cancelled'"
                      @click="cancelAppointment(apt.id)"
                      class="btn btn-outline-danger"
                      :disabled="loading"
                      title="Hủy lịch hẹn"
                    >
                      <i class="bi bi-x-circle"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Status Update Modal -->
      <div v-if="selectedAppointment && showUpdateModal" class="modal d-block" :style="{ backgroundColor: 'rgba(0,0,0,0.5)' }">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Cập Nhật Trạng Thái Lịch Hẹn</h5>
              <button type="button" class="btn-close" @click="closeModals()"></button>
            </div>
            <div class="modal-body">
              <p>
                <strong>Thú Cưng:</strong> {{ selectedAppointment.pet.name }}<br>
                <strong>Dịch Vụ:</strong> {{ selectedAppointment.service.name }}<br>
                <strong>Trạng Thái Hiện Tại:</strong> 
                <span :class="'badge bg-' + getStatusColor(selectedAppointment.status)">
                  {{ getStatusLabel(selectedAppointment.status) }}
                </span>
              </p>
              <div class="mb-3">
                <label class="form-label">Trạng Thái Mới:</label>
                <select v-model="statusUpdateForm.status" class="form-select">
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
                  v-model="statusUpdateForm.notes" 
                  class="form-control" 
                  rows="3"
                  placeholder="Thêm ghi chú..."
                ></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeModals()">Hủy</button>
              <button 
                type="button" 
                class="btn btn-primary" 
                @click="updateAppointmentStatus()"
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
  name: 'AdminAppointments',
  components: {
    SidebarNav
  },
  data() {
    return {
      loading: false,
      error: null,
      appointments: [],
      filters: {
        search: '',
        status: ''
      },
      selectedAppointment: null,
      showUpdateModal: false,
      statusUpdateForm: {
        status: '',
        notes: ''
      }
    };
  },
  computed: {
    filteredAppointments() {
      return this.appointments.filter(apt => {
        const matchSearch = 
          apt.customer.user.name.toLowerCase().includes(this.filters.search.toLowerCase()) ||
          apt.pet.name.toLowerCase().includes(this.filters.search.toLowerCase());
        const matchStatus = !this.filters.status || apt.status === this.filters.status;
        return matchSearch && matchStatus;
      });
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
        this.appointments = response.data.data;
      } catch (error) {
        this.error = 'Lỗi khi tải lịch hẹn: ' + (error.response?.data?.message || error.message);
        console.error('Error fetching appointments:', error);
      } finally {
        this.loading = false;
      }
    },

    showStatusModal(apt) {
      this.selectedAppointment = apt;
      this.statusUpdateForm = {
        status: apt.status,
        notes: apt.notes || ''
      };
      this.showUpdateModal = true;
    },

    closeModals() {
      this.showUpdateModal = false;
      this.selectedAppointment = null;
      this.statusUpdateForm = {
        status: '',
        notes: ''
      };
    },

    async updateAppointmentStatus() {
      if (!this.selectedAppointment) return;

      try {
        this.loading = true;
        await api.put(`/appointments/${this.selectedAppointment.id}`, {
          status: this.statusUpdateForm.status,
          notes: this.statusUpdateForm.notes
        });

        // Show message based on action
        if (this.statusUpdateForm.status === 'completed') {
          alert('Lịch hẹn đã hoàn thành! Hóa đơn sẽ được tạo tự động.');
        } else {
          alert('Cập nhật trạng thái thành công!');
        }

        this.closeModals();
        this.fetchAppointments();
      } catch (error) {
        this.error = 'Lỗi khi cập nhật: ' + (error.response?.data?.message || error.message);
        console.error('Error updating appointment:', error);
      } finally {
        this.loading = false;
      }
    },

    async markAsCompleted(apt) {
      if (!confirm('Bạn chắc chắn muốn hoàn thành lịch hẹn này? Hóa đơn sẽ được tạo tự động.')) return;

      try {
        this.loading = true;
        await api.put(`/appointments/${apt.id}`, {
          status: 'completed'
        });
        
        alert('Lịch hẹn đã hoàn thành! Hóa đơn đã được tạo tự động.');
        this.fetchAppointments();
      } catch (error) {
        this.error = 'Lỗi: ' + (error.response?.data?.message || error.message);
        console.error('Error:', error);
      } finally {
        this.loading = false;
      }
    },

    async cancelAppointment(id) {
      if (!confirm('Bạn chắc chắn muốn hủy lịch hẹn này?')) return;

      try {
        this.loading = true;
        await api.delete(`/appointments/${id}`);
        
        alert('Lịch hẹn đã được hủy.');
        this.fetchAppointments();
      } catch (error) {
        this.error = 'Lỗi: ' + (error.response?.data?.message || error.message);
        console.error('Error:', error);
      } finally {
        this.loading = false;
      }
    },

    formatDate(date) {
      return new Date(date).toLocaleDateString('vi-VN');
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
.input-group-text {
  border-right: none;
  border-color: #dee2e6;
}

.input-group .form-control {
  border-left: none;
}

.input-group .form-control:focus {
  border-left: none;
}

.btn-group-sm > .btn {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
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

.modal-header .modal-title {
  font-weight: 700;
  color: #2c3e50;
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

.spinner-border-sm {
  width: 1rem;
  height: 1rem;
  margin-right: 8px;
}

@media (max-width: 768px) {
  .table {
    font-size: 0.9rem;
  }

  .table tbody td {
    padding: 10px;
  }

  .btn-group-sm {
    display: flex;
    flex-wrap: wrap;
  }

  .btn-group-sm > .btn {
    margin-bottom: 4px;
    margin-right: 4px;
  }
}
</style>
