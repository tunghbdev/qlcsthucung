<template>
  <div class="container-fluid py-4">
    <SidebarNav />

    <div class="main-content">
      <div class="page-header mb-4">
        <h2 class="mb-0">
          <i class="bi bi-clipboard-check"></i> Duyệt Yêu Cầu Dịch Vụ
        </h2>
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

          <!-- Tabs -->
          <ul v-else class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                @click="filterStatus = 'pending'"
                :class="{ active: filterStatus === 'pending' }"
                type="button"
              >
                <i class="bi bi-hourglass-split"></i>
                Chờ Duyệt
                <span class="badge bg-warning ms-2">{{ countByStatus('pending') }}</span>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                @click="filterStatus = 'approved'"
                :class="{ active: filterStatus === 'approved' }"
                type="button"
              >
                <i class="bi bi-check-circle"></i>
                Đã Duyệt
                <span class="badge bg-success ms-2">{{ countByStatus('approved') }}</span>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                @click="filterStatus = 'rejected'"
                :class="{ active: filterStatus === 'rejected' }"
                type="button"
              >
                <i class="bi bi-x-circle"></i>
                Từ Chối
                <span class="badge bg-danger ms-2">{{ countByStatus('rejected') }}</span>
              </button>
            </li>
          </ul>

          <!-- Empty State -->
          <div v-if="!loading && filteredRequests.length === 0" class="alert alert-info text-center py-5">
            <i class="bi bi-inbox display-4"></i>
            <p class="mt-3">Không có yêu cầu nào trong trạng thái này.</p>
          </div>

          <!-- Requests List -->
          <div v-else class="row g-3">
            <div v-for="req in filteredRequests" :key="req.id" class="col-md-6">
              <div class="card h-100 border-top-3" :class="'border-' + getStatusColor(req.status)">
                <div class="card-header" :class="'bg-' + getStatusColor(req.status) + ' bg-opacity-10'">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h6 class="mb-1">{{ req.customer.user.name }}</h6>
                      <small class="text-muted">{{ req.customer.user.email }}</small>
                    </div>
                    <span :class="'badge bg-' + getStatusColor(req.status)">
                      {{ getStatusLabel(req.status) }}
                    </span>
                  </div>
                </div>

                <div class="card-body">
                  <div class="mb-3">
                    <div class="row">
                      <div class="col-sm-6">
                        <small class="text-muted d-block mb-2">
                          <strong><i class="bi bi-paw"></i> Thú Cưng:</strong>
                        </small>
                        <span>{{ req.pet.name }} ({{ req.pet.type }})</span>
                      </div>
                      <div class="col-sm-6">
                        <small class="text-muted d-block mb-2">
                          <strong><i class="bi bi-gear"></i> Dịch Vụ:</strong>
                        </small>
                        <span>{{ req.service.name }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <div class="col-sm-6">
                      <small class="text-muted d-block mb-1">
                        <strong><i class="bi bi-calendar"></i> Ngày:</strong>
                      </small>
                      {{ formatDate(req.requested_date) }}
                    </div>
                    <div class="col-sm-6">
                      <small class="text-muted d-block mb-1">
                        <strong><i class="bi bi-clock"></i> Giờ:</strong>
                      </small>
                      {{ req.requested_time }}
                    </div>
                  </div>

                  <div v-if="req.notes" class="mb-3 p-3 bg-light rounded">
                    <small class="text-muted d-block mb-1"><strong>Ghi Chú Khách Hàng:</strong></small>
                    <p class="mb-0">{{ req.notes }}</p>
                  </div>

                  <div class="mb-3 pt-2 border-top">
                    <small class="text-muted">
                      <strong>Giá Dịch Vụ:</strong> {{ formatCurrency(req.service.price) }}
                    </small>
                    <br />
                    <small class="text-muted">
                      <strong>Gửi lúc:</strong> {{ formatDateTime(req.created_at) }}
                    </small>
                  </div>

                  <!-- Rejection Reason (if rejected) -->
                  <div v-if="req.status === 'rejected' && req.rejection_reason" class="alert alert-danger py-2 mb-3">
                    <small>
                      <strong>Lý Do Từ Chối:</strong> {{ req.rejection_reason }}
                    </small>
                  </div>

                  <!-- Action Buttons (only for pending) -->
                  <div v-if="req.status === 'pending'" class="d-flex gap-2">
                    <button
                      @click="showApproveForm(req)"
                      class="btn btn-sm btn-success flex-grow-1"
                      :disabled="loading"
                    >
                      <i class="bi bi-check-circle"></i> Duyệt
                    </button>
                    <button
                      @click="showRejectForm(req)"
                      class="btn btn-sm btn-danger flex-grow-1"
                      :disabled="loading"
                    >
                      <i class="bi bi-x-circle"></i> Từ Chối
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Approve Confirmation Modal -->
      <div v-if="selectedRequest && showApproveModal" class="modal d-block" :style="{ backgroundColor: 'rgba(0,0,0,0.5)' }">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-success bg-opacity-10">
              <h5 class="modal-title">Xác Nhận Duyệt Yêu Cầu</h5>
              <button
                type="button"
                class="btn-close"
                @click="closeModals()"
              ></button>
            </div>
            <div class="modal-body">
              <p>Bạn chắc chắn muốn duyệt yêu cầu này?</p>
              <div class="alert alert-info">
                <small>
                  <strong>Khách Hàng:</strong> {{ selectedRequest.customer.user.name }}<br />
                  <strong>Thú Cưng:</strong> {{ selectedRequest.pet.name }}<br />
                  <strong>Dịch Vụ:</strong> {{ selectedRequest.service.name }}<br />
                  <strong>Ngày/Giờ:</strong> {{ formatDate(selectedRequest.requested_date) }} lúc
                  {{ selectedRequest.requested_time }}
                </small>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeModals()">Hủy</button>
              <button
                type="button"
                class="btn btn-success"
                @click="approveRequest()"
                :disabled="loading"
              >
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Duyệt
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Reject Modal -->
      <div v-if="selectedRequest && showRejectModal" class="modal d-block" :style="{ backgroundColor: 'rgba(0,0,0,0.5)' }">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-danger bg-opacity-10">
              <h5 class="modal-title">Lý Do Từ Chối</h5>
              <button
                type="button"
                class="btn-close"
                @click="closeModals()"
              ></button>
            </div>
            <div class="modal-body">
              <p>Vui lòng nhập lý do từ chối yêu cầu:</p>
              <textarea
                v-model="rejectionReason"
                class="form-control"
                rows="4"
                placeholder="Nhập lý do từ chối..."
              ></textarea>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeModals()">Hủy</button>
              <button
                type="button"
                class="btn btn-danger"
                @click="rejectRequest()"
                :disabled="loading || !rejectionReason.trim()"
              >
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Từ Chối
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
  name: 'AdminServiceRequests',
  components: {
    SidebarNav
  },
  data() {
    return {
      loading: false,
      error: null,
      requests: [],
      filterStatus: 'pending',
      selectedRequest: null,
      showApproveModal: false,
      showRejectModal: false,
      rejectionReason: ''
    };
  },
  computed: {
    filteredRequests() {
      return this.requests.filter(req => req.status === this.filterStatus);
    }
  },
  mounted() {
    this.fetchRequests();
  },
  methods: {
    async fetchRequests() {
      try {
        this.loading = true;
        this.error = null;
        const response = await api.get('/service-requests');
        this.requests = response.data.data;
      } catch (error) {
        this.error = 'Lỗi khi tải yêu cầu: ' + (error.response?.data?.message || error.message);
        console.error('Error fetching requests:', error);
      } finally {
        this.loading = false;
      }
    },

    showApproveForm(req) {
      this.selectedRequest = req;
      this.showApproveModal = true;
    },

    showRejectForm(req) {
      this.selectedRequest = req;
      this.rejectionReason = '';
      this.showRejectModal = true;
    },

    closeModals() {
      this.showApproveModal = false;
      this.showRejectModal = false;
      this.selectedRequest = null;
      this.rejectionReason = '';
    },

    async approveRequest() {
      if (!this.selectedRequest) return;

      try {
        this.loading = true;
        await api.post(`/service-requests/${this.selectedRequest.id}/approve`);
        alert('Yêu cầu đã được duyệt! Lịch hẹn đã được tạo.');
        this.closeModals();
        this.fetchRequests();
      } catch (error) {
        this.error = 'Lỗi khi duyệt: ' + (error.response?.data?.message || error.message);
        console.error('Error approving request:', error);
      } finally {
        this.loading = false;
      }
    },

    async rejectRequest() {
      if (!this.selectedRequest || !this.rejectionReason.trim()) return;

      try {
        this.loading = true;
        await api.post(`/service-requests/${this.selectedRequest.id}/reject`, {
          rejection_reason: this.rejectionReason
        });
        alert('Yêu cầu đã được từ chối.');
        this.closeModals();
        this.fetchRequests();
      } catch (error) {
        this.error = 'Lỗi khi từ chối: ' + (error.response?.data?.message || error.message);
        console.error('Error rejecting request:', error);
      } finally {
        this.loading = false;
      }
    },

    countByStatus(status) {
      return this.requests.filter(req => req.status === status).length;
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

.border-top-3 {
  border-top-width: 3px !important;
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
</style>
