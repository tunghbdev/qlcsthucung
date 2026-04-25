<template>
  <div class="container-fluid py-4">
    <SidebarNav />

    <div class="main-content">
      <div class="page-header mb-4">
        <h2 class="mb-0">
          <i class="bi bi-calendar-event"></i> Đặt Lịch Dịch Vụ
        </h2>
        <small class="text-muted">Quản lý lịch hẹn dịch vụ cho thú cưng của bạn</small>
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

          <!-- Tabs -->
          <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                @click="activeTab = 'new'"
                :class="{ active: activeTab === 'new' }"
                type="button"
              >
                <i class="bi bi-plus-circle"></i> Đặt Lịch Mới
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                @click="activeTab = 'list'"
                :class="{ active: activeTab === 'list' }"
                type="button"
              >
                <i class="bi bi-list"></i> Lịch Của Tôi
              </button>
            </li>
          </ul>

            <!-- Tab: Đặt Lịch Mới -->
            <div v-if="activeTab === 'new'" class="tab-pane fade show active">
              <div class="row">
                <div class="col-lg-8 mx-auto">
                  <form @submit.prevent="bookAppointment" class="booking-form">
                    <div class="form-section">
                      <h5 class="form-section-title">Thông Tin Thú Cưng</h5>
                      <div class="mb-3">
                        <label for="pet" class="form-label">
                          <i class="bi bi-paw"></i> Chọn Thú Cưng
                        </label>
                        <select v-model="booking.petId" class="form-select" id="pet" required :disabled="loading">
                          <option value="">-- Chọn thú cưng --</option>
                          <option v-for="pet in pets" :key="pet.id" :value="pet.id">
                            {{ pet.name }} ({{ pet.type }})
                          </option>
                        </select>
                        <small v-if="pets.length === 0" class="text-muted">
                          <i class="bi bi-info-circle"></i> Bạn cần thêm thú cưng trước
                        </small>
                      </div>
                    </div>

                    <div class="form-section">
                      <h5 class="form-section-title">Chọn Dịch Vụ</h5>
                      <div class="mb-3">
                        <label for="service" class="form-label">
                          <i class="bi bi-heart"></i> Dịch Vụ
                        </label>
                        <select v-model="booking.serviceId" class="form-select" id="service" required :disabled="loading">
                          <option value="">-- Chọn dịch vụ --</option>
                          <option v-for="service in services" :key="service.id" :value="service.id">
                            {{ service.name }} - {{ formatCurrency(service.price) }}
                          </option>
                        </select>
                      </div>
                    </div>

                    <div class="form-section">
                      <h5 class="form-section-title">Lịch Hẹn</h5>
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label for="date" class="form-label">
                            <i class="bi bi-calendar"></i> Ngày Hẹn
                          </label>
                          <input v-model="booking.date" type="date" class="form-control" id="date" required :disabled="loading">
                        </div>
                        <div class="col-md-6 mb-3">
                          <label for="time" class="form-label">
                            <i class="bi bi-clock"></i> Giờ Hẹn
                          </label>
                          <input v-model="booking.time" type="time" class="form-control" id="time" required :disabled="loading">
                        </div>
                      </div>
                    </div>

                    <div class="form-section">
                      <h5 class="form-section-title">Ghi Chú Thêm</h5>
                      <div class="mb-3">
                        <label for="notes" class="form-label">
                          <i class="bi bi-chat"></i> Ghi Chú (Tùy Chọn)
                        </label>
                        <textarea v-model="booking.notes" class="form-control" id="notes" rows="3" placeholder="Mô tả tình trạng thú cưng hoặc yêu cầu đặc biệt..." :disabled="loading"></textarea>
                      </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="loading">
                      <i class="bi bi-check2"></i> Xác Nhận Đặt Lịch
                    </button>
                  </form>
                </div>
              </div>
            </div>

            <!-- Tab: Lịch Của Tôi -->
            <div v-if="activeTab === 'list'" class="tab-pane fade show active">
              <div v-if="myAppointments.length === 0" class="alert alert-info">
                <i class="bi bi-inbox"></i> Bạn chưa có lịch hẹn nào. Hãy đặt lịch mới!
              </div>
              <table v-else class="table table-hover table-striped">
                <thead class="table-dark">
                  <tr>
                    <th>Thú Cưng</th>
                    <th>Dịch Vụ</th>
                    <th>Ngày Giờ</th>
                    <th>Trạng Thái</th>
                    <th>Giá</th>
                    <th>Hành Động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="apt in myAppointments" :key="apt.id">
                    <td>
                      <i class="bi bi-paw"></i> {{ apt.pet?.name }} 
                      <small class="d-block text-muted">({{ apt.pet?.type }})</small>
                    </td>
                    <td>{{ apt.service?.name }}</td>
                    <td>
                      {{ apt.appointment_date }}
                      <br>
                      <strong>{{ apt.appointment_time }}</strong>
                    </td>
                    <td>
                      <span :class="'badge bg-' + getStatusColor(apt.status)">
                        {{ getStatusLabel(apt.status) }}
                      </span>
                    </td>
                    <td>{{ formatCurrency(apt.service?.price || 0) }}</td>
                    <td>
                      <div class="btn-group btn-group-sm" role="group">
                        <button 
                          v-if="apt.status === 'pending' || apt.status === 'confirmed'" 
                          @click="cancelAppointment(apt.id)" 
                          class="btn btn-outline-danger" 
                          :disabled="loading"
                          title="Hủy lịch hẹn"
                        >
                          <i class="bi bi-x"></i> Hủy
                        </button>
                        <button 
                          v-if="apt.status === 'completed'" 
                          @click="feedbackAppointment(apt.id)" 
                          class="btn btn-outline-info" 
                          :disabled="loading"
                          title="Đánh giá dịch vụ"
                        >
                          <i class="bi bi-chat"></i> Đánh Giá
                        </button>
                        <span v-else class="text-muted">-</span>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
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
  name: 'CustomerAppointments',
  components: {
    SidebarNav
  },
  data() {
    return {
      userRole: 'customer',
      activeTab: 'new',
      loading: false,
      error: null,
      booking: {
        petId: '',
        serviceId: '',
        date: '',
        time: '',
        notes: ''
      },
      pets: [],
      services: [],
      myAppointments: []
    };
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      try {
        this.loading = true;
        this.error = null;
        
        // Fetch pets
        const petsResponse = await api.get('/pets');
        this.pets = petsResponse.data.data || [];
        
        // Fetch services
        const servicesResponse = await api.get('/services');
        this.services = servicesResponse.data || [];
        
        // Fetch appointments
        const appointmentsResponse = await api.get('/appointments');
        this.myAppointments = appointmentsResponse.data.data || [];
      } catch (error) {
        this.error = 'Lỗi khi tải dữ liệu: ' + (error.response?.data?.message || error.message);
        console.error('Error fetching data:', error);
      } finally {
        this.loading = false;
      }
    },
    async bookAppointment() {
      if (!this.booking.petId || !this.booking.serviceId || !this.booking.date || !this.booking.time) {
        this.error = 'Vui lòng điền đầy đủ thông tin';
        return;
      }

      try {
        this.loading = true;
        this.error = null;
        
        const response = await api.post('/appointments', {
          pet_id: parseInt(this.booking.petId),
          service_id: parseInt(this.booking.serviceId),
          appointment_date: this.booking.date,
          appointment_time: this.booking.time,
          notes: this.booking.notes
        });
        
        this.myAppointments.push(response.data.data);
        this.booking = {
          petId: '',
          serviceId: '',
          date: '',
          time: '',
          notes: ''
        };
        this.activeTab = 'list';
        alert('Đặt lịch thành công! Vui lòng chờ xác nhận.');
      } catch (error) {
        this.error = 'Lỗi khi đặt lịch: ' + (error.response?.data?.message || error.message);
        console.error('Error booking appointment:', error);
      } finally {
        this.loading = false;
      }
    },
    async cancelAppointment(id) {
      if (!confirm('Bạn có chắc chắn muốn hủy lịch hẹn này?')) {
        return;
      }

      try {
        this.loading = true;
        this.error = null;
        
        await api.delete(`/appointments/${id}`);
        this.myAppointments = this.myAppointments.filter(apt => apt.id !== id);
        alert('Lịch hẹn đã được hủy!');
      } catch (error) {
        this.error = 'Lỗi khi hủy lịch: ' + (error.response?.data?.message || error.message);
        console.error('Error cancelling appointment:', error);
      } finally {
        this.loading = false;
      }
    },
    feedbackAppointment(id) {
      alert('Gửi đánh giá cho lịch hẹn #' + id + '\n(Tính năng sẽ được phát triển)');
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
.page-header {
  margin-bottom: 30px;
}

.booking-form {
  background: white;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.form-section {
  margin-bottom: 25px;
  padding-bottom: 25px;
  border-bottom: 1px solid #e9ecef;
}

.form-section:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.form-section-title {
  font-size: 1rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.form-section-title i {
  color: var(--primary-color);
}

.form-label {
  font-weight: 600;
  color: #2c3e50;
  display: flex;
  align-items: center;
  gap: 8px;
}

.form-label i {
  color: var(--primary-color);
  font-size: 1.1rem;
}

.form-control,
.form-select {
  border-radius: 8px;
  transition: all 0.3s ease;
}

.table {
  margin-bottom: 0;
}

.table thead th {
  background: linear-gradient(90deg, #2c3e50, #34495e);
  color: white;
  border: none;
  font-weight: 600;
  padding: 15px;
}

.table tbody tr:hover {
  background-color: #f8f9fa;
  transform: scale(1.01);
}

.table tbody td {
  padding: 15px;
  vertical-align: middle;
}

.btn-group-sm > .btn {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

.badge {
  padding: 0.35rem 0.65rem;
  border-radius: 12px;
  font-weight: 500;
}

.nav-link {
  cursor: pointer;
  color: #666;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px !important;
}

.nav-link:hover {
  color: var(--primary-color);
}

.nav-link.active {
  color: var(--primary-color);
  border-bottom: 3px solid var(--primary-color);
  font-weight: 600;
}

.alert {
  border-radius: 8px;
  border: none;
  display: flex;
  align-items: center;
  gap: 12px;
}

.alert i {
  font-size: 1.5rem;
}

.alert-info {
  background-color: #cfe2ff;
  color: #084298;
}

.btn-lg {
  padding: 12px 24px;
  font-size: 1rem;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.btn-lg:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}

.text-muted {
  color: #6c757d;
  font-size: 0.9rem;
}

.d-block {
  display: block;
}

@media (max-width: 768px) {
  .booking-form {
    padding: 15px;
  }

  .form-section {
    margin-bottom: 15px;
    padding-bottom: 15px;
  }

  .btn-lg {
    padding: 10px 16px;
    font-size: 0.95rem;
  }

  .table {
    font-size: 0.9rem;
  }

  .table tbody td {
    padding: 10px;
  }
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
