<template>
  <div class="container-fluid mt-5">
    <div class="row">
      <div class="col-md-3">
        <SidebarNav :userRole="userRole" />
      </div>
      <div class="col-md-9">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Đặt Lịch Dịch Vụ</h4>
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

            <ul class="nav nav-tabs mb-3">
              <li class="nav-item">
                <a class="nav-link" :class="{active: activeTab === 'new'}" href="#" @click.prevent="activeTab = 'new'">
                  Đặt Lịch Mới
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{active: activeTab === 'list'}" href="#" @click.prevent="activeTab = 'list'">
                  Lịch Của Tôi
                </a>
              </li>
            </ul>

            <!-- Tab: Đặt Lịch Mới -->
            <div v-show="activeTab === 'new'" class="tab-pane active">
              <form @submit.prevent="bookAppointment">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="pet" class="form-label">Chọn Thú Cưng</label>
                      <select v-model="booking.petId" class="form-select" id="pet" required :disabled="loading">
                        <option value="">-- Chọn thú cưng --</option>
                        <option v-for="pet in pets" :key="pet.id" :value="pet.id">
                          {{ pet.name }} ({{ pet.type }})
                        </option>
                      </select>
                      <small v-if="pets.length === 0" class="text-muted">Bạn cần thêm thú cưng trước</small>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="service" class="form-label">Chọn Dịch Vụ</label>
                      <select v-model="booking.serviceId" class="form-select" id="service" required :disabled="loading">
                        <option value="">-- Chọn dịch vụ --</option>
                        <option v-for="service in services" :key="service.id" :value="service.id">
                          {{ service.name }} - {{ formatCurrency(service.price) }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="date" class="form-label">Ngày Hẹn</label>
                      <input v-model="booking.date" type="date" class="form-control" id="date" required :disabled="loading">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="time" class="form-label">Giờ Hẹn</label>
                      <input v-model="booking.time" type="time" class="form-control" id="time" required :disabled="loading">
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="notes" class="form-label">Ghi Chú Thêm</label>
                  <textarea v-model="booking.notes" class="form-control" id="notes" rows="3" placeholder="Ghi chú về thú cưng hoặc dịch vụ..." :disabled="loading"></textarea>
                </div>

                <button type="submit" class="btn btn-primary" :disabled="loading">
                  <i class="bi bi-check2"></i> Xác Nhận Đặt Lịch
                </button>
              </form>
            </div>

            <!-- Tab: Lịch Của Tôi -->
            <div v-show="activeTab === 'list'" class="tab-pane active">
              <div v-if="myAppointments.length === 0" class="alert alert-info">
                Bạn chưa có lịch hẹn nào. Hãy đặt lịch mới!
              </div>
              <table v-else class="table table-hover">
                <thead>
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
                    <td>{{ apt.pet?.name }} ({{ apt.pet?.type }})</td>
                    <td>{{ apt.service?.name }}</td>
                    <td>{{ apt.appointment_date }} {{ apt.appointment_time }}</td>
                    <td>
                      <span :class="'badge bg-' + getStatusColor(apt.status)">
                        {{ getStatusLabel(apt.status) }}
                      </span>
                    </td>
                    <td>{{ formatCurrency(apt.service?.price || 0) }}</td>
                    <td>
                      <button v-if="apt.status === 'pending' || apt.status === 'confirmed'" @click="cancelAppointment(apt.id)" class="btn btn-sm btn-danger" :disabled="loading">
                        <i class="bi bi-x"></i> Hủy
                      </button>
                      <button v-if="apt.status === 'completed'" @click="feedbackAppointment(apt.id)" class="btn btn-sm btn-info" :disabled="loading">
                        <i class="bi bi-chat"></i> Đánh Giá
                      </button>
                      <span v-else class="text-muted">-</span>
                    </td>
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
        
        await api.delete(`/api/appointments/${id}`);
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
.card {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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

<style scoped>
.card {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
