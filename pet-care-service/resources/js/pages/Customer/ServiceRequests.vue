<template>
  <div class="container-fluid py-4">
    <SidebarNav />

    <div class="main-content">
      <div class="page-header mb-4">
        <h2 class="mb-0">Yêu Cầu Dịch Vụ</h2>
      </div>

      <div class="card">
        <div class="card-body">
          <!-- Tabs -->
          <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
              <button
                class="nav-link active"
                @click="activeTab = 'new'"
                :class="{ active: activeTab === 'new' }"
                type="button"
              >
                <i class="bi bi-plus-circle"></i> Gửi Yêu Cầu
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                @click="activeTab = 'history'"
                :class="{ active: activeTab === 'history' }"
                type="button"
              >
                <i class="bi bi-clock-history"></i> Lịch Sử Yêu Cầu
              </button>
            </li>
          </ul>

          <!-- Error Alert -->
          <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ error }}
            <button type="button" class="btn-close" @click="error = null"></button>
          </div>

          <!-- New Request Form -->
          <div v-if="activeTab === 'new'" class="tab-pane fade show active">
            <div class="row">
              <div class="col-md-8">
                <form @submit.prevent="submitRequest">
                  <div class="mb-3">
                    <label for="pet" class="form-label">
                      <i class="bi bi-paw"></i> Chọn Thú Cưng
                    </label>
                    <select v-model="newRequest.pet_id" id="pet" class="form-select" required>
                      <option value="">-- Chọn thú cưng --</option>
                      <option v-for="pet in pets" :key="pet.id" :value="pet.id">
                        {{ pet.name }} ({{ pet.type }})
                      </option>
                    </select>
                    <small v-if="!pets.length" class="text-danger">
                      Bạn chưa có thú cưng nào. Vui lòng thêm thú cưng trước.
                    </small>
                  </div>

                  <div class="mb-3">
                    <label for="service" class="form-label">
                      <i class="bi bi-gear"></i> Chọn Dịch Vụ
                    </label>
                    <select v-model="newRequest.service_id" id="service" class="form-select" required>
                      <option value="">-- Chọn dịch vụ --</option>
                      <option v-for="service in services" :key="service.id" :value="service.id">
                        {{ service.name }} - {{ formatCurrency(service.price) }}
                      </option>
                    </select>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="date" class="form-label">
                          <i class="bi bi-calendar"></i> Ngày Yêu Cầu
                        </label>
                        <input
                          v-model="newRequest.requested_date"
                          type="date"
                          id="date"
                          class="form-control"
                          required
                          :min="minDate"
                        />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="time" class="form-label">
                          <i class="bi bi-clock"></i> Giờ Yêu Cầu
                        </label>
                        <input
                          v-model="newRequest.requested_time"
                          type="time"
                          id="time"
                          class="form-control"
                          required
                        />
                      </div>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="notes" class="form-label">
                      <i class="bi bi-chat"></i> Ghi Chú (Tùy Chọn)
                    </label>
                    <textarea
                      v-model="newRequest.notes"
                      id="notes"
                      class="form-control"
                      rows="3"
                      placeholder="Nhập ghi chú về yêu cầu của bạn..."
                    ></textarea>
                  </div>

                  <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                      <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                      <i class="bi bi-send"></i> Gửi Yêu Cầu
                    </button>
                    <button type="reset" class="btn btn-secondary">
                      <i class="bi bi-arrow-counterclockwise"></i> Xóa
                    </button>
                  </div>
                </form>
              </div>

              <!-- Info Box -->
              <div class="col-md-4">
                <div class="card bg-light">
                  <div class="card-body">
                    <h6 class="card-title">
                      <i class="bi bi-info-circle"></i> Hướng Dẫn
                    </h6>
                    <ul class="small mb-0">
                      <li>Chọn thú cưng và dịch vụ bạn muốn</li>
                      <li>Chọn ngày và giờ phù hợp</li>
                      <li>Thêm ghi chú nếu cần</li>
                      <li>Gửi yêu cầu để chờ duyệt từ admin</li>
                      <li>Admin sẽ xác nhận trong 24 giờ</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- History Tab -->
          <div v-if="activeTab === 'history'" class="tab-pane fade show active">
            <div v-if="loading" class="alert alert-info">
              <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Đang tải...</span>
              </div>
              Đang tải...
            </div>

            <div v-else-if="!myRequests.length" class="alert alert-info">
              <i class="bi bi-inbox"></i> Bạn chưa gửi yêu cầu nào.
            </div>

            <div v-else>
              <div v-for="req in myRequests" :key="req.id" class="card mb-3">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-8">
                      <h6 class="card-title mb-2">
                        <i class="bi bi-paw"></i> {{ req.pet.name }}
                        <span class="badge" :class="'bg-' + getStatusColor(req.status)">
                          {{ getStatusLabel(req.status) }}
                        </span>
                      </h6>

                      <div class="row mb-2">
                        <div class="col-sm-6">
                          <small class="text-muted">
                            <strong>Dịch Vụ:</strong> {{ req.service.name }}
                          </small>
                        </div>
                        <div class="col-sm-6">
                          <small class="text-muted">
                            <strong>Giá:</strong> {{ formatCurrency(req.service.price) }}
                          </small>
                        </div>
                      </div>

                      <div class="row mb-2">
                        <div class="col-sm-6">
                          <small class="text-muted">
                            <i class="bi bi-calendar"></i> {{ formatDate(req.requested_date) }}
                          </small>
                        </div>
                        <div class="col-sm-6">
                          <small class="text-muted">
                            <i class="bi bi-clock"></i> {{ req.requested_time }}
                          </small>
                        </div>
                      </div>

                      <div v-if="req.notes" class="mb-2">
                        <small class="text-muted">
                          <strong>Ghi Chú:</strong> {{ req.notes }}
                        </small>
                      </div>

                      <div v-if="req.status === 'rejected' && req.rejection_reason" class="alert alert-danger mt-2 mb-0 py-2">
                        <small>
                          <strong>Lý Do Từ Chối:</strong> {{ req.rejection_reason }}
                        </small>
                      </div>
                    </div>

                    <div class="col-md-4 text-end">
                      <small class="text-muted d-block mb-2">
                        Gửi lúc: {{ formatDateTime(req.created_at) }}
                      </small>

                      <div v-if="req.reviewed_at" class="mb-2">
                        <small class="text-muted d-block">
                          Duyệt lúc: {{ formatDateTime(req.reviewed_at) }}
                        </small>
                        <small class="text-muted" v-if="req.reviewer">
                          Bởi: {{ req.reviewer.name }}
                        </small>
                      </div>

                      <div v-if="req.status === 'pending'" class="mt-3">
                        <button
                          @click="cancelRequest(req.id)"
                          class="btn btn-sm btn-outline-danger"
                          :disabled="loading"
                        >
                          <i class="bi bi-x-circle"></i> Hủy Yêu Cầu
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
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
import api from '@/services/api';

export default {
  name: 'CustomerServiceRequests',
  components: {
    SidebarNav
  },
  data() {
    return {
      activeTab: 'new',
      loading: false,
      error: null,
      pets: [],
      services: [],
      myRequests: [],
      newRequest: {
        pet_id: '',
        service_id: '',
        requested_date: '',
        requested_time: '',
        notes: ''
      },
      minDate: new Date().toISOString().split('T')[0]
    };
  },
  computed: {
    tomorrow() {
      const date = new Date();
      date.setDate(date.getDate() + 1);
      return date.toISOString().split('T')[0];
    }
  },
  mounted() {
    this.fetchPetsAndServices();
    this.fetchMyRequests();
  },
  methods: {
    async fetchPetsAndServices() {
      try {
        const [petsRes, servicesRes] = await Promise.all([
          api.get('/pets'),
          api.get('/services')
        ]);
        this.pets = petsRes.data.data;
        this.services = servicesRes.data.data;
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    },

    async fetchMyRequests() {
      try {
        this.loading = true;
        const response = await api.get('/service-requests');
        this.myRequests = response.data.data;
      } catch (error) {
        this.error = 'Lỗi khi tải yêu cầu: ' + (error.response?.data?.message || error.message);
        console.error('Error fetching requests:', error);
      } finally {
        this.loading = false;
      }
    },

    async submitRequest() {
      try {
        this.loading = true;
        this.error = null;
        await api.post('/service-requests', this.newRequest);
        // Reset form
        this.newRequest = {
          pet_id: '',
          service_id: '',
          requested_date: '',
          requested_time: '',
          notes: ''
        };
        // Show success and refresh requests
        alert('Yêu cầu đã được gửi! Admin sẽ xem xét trong 24 giờ.');
        this.activeTab = 'history';
        this.fetchMyRequests();
      } catch (error) {
        this.error = 'Lỗi khi gửi yêu cầu: ' + (error.response?.data?.message || error.message);
        console.error('Error submitting request:', error);
      } finally {
        this.loading = false;
      }
    },

    async cancelRequest(id) {
      if (!confirm('Bạn chắc chắn muốn hủy yêu cầu này?')) return;

      try {
        this.loading = true;
        await api.delete(`/service-requests/${id}`);
        this.fetchMyRequests();
        alert('Yêu cầu đã được hủy.');
      } catch (error) {
        this.error = 'Lỗi khi hủy yêu cầu: ' + (error.response?.data?.message || error.message);
        console.error('Error cancelling request:', error);
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

    formatDate(date) {
      return new Date(date).toLocaleDateString('vi-VN');
    },

    formatDateTime(dateTime) {
      return new Date(dateTime).toLocaleString('vi-VN');
    },

    getStatusColor(status) {
      const colors = {
        pending: 'warning',
        approved: 'success',
        rejected: 'danger'
      };
      return colors[status] || 'secondary';
    },

    getStatusLabel(status) {
      const labels = {
        pending: 'Chờ Duyệt',
        approved: 'Đã Duyệt',
        rejected: 'Từ Chối'
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

.card {
  border: 1px solid #dee2e6;
  border-radius: 8px;
}
</style>
