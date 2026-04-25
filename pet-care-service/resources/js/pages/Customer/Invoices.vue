<template>
  <div class="container-fluid py-4">
    <SidebarNav />

    <div class="main-content">
      <div class="page-header mb-4">
        <h2 class="mb-0">
          <i class="bi bi-receipt"></i> Hóa Đơn Của Tôi
        </h2>
        <small class="text-muted">Xem và quản lý các hóa đơn dịch vụ</small>
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
            Đang xử lý...
          </div>

          <!-- Stats -->
          <!-- Stats -->
          <div class="row mb-4 g-3">
            <div class="col-md-4">
              <div class="stat-card">
                <i class="bi bi-wallet-fill display-4 text-success"></i>
                <h6>Tổng Chi Tiêu</h6>
                <h3 class="text-success">{{ formatCurrency(totalSpent) }}</h3>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stat-card">
                <i class="bi bi-check-circle display-4 text-info"></i>
                <h6>Đã Thanh Toán</h6>
                <h3 class="text-info">{{ formatCurrency(paidAmount) }}</h3>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stat-card">
                <i class="bi bi-exclamation-circle display-4 text-warning"></i>
                <h6>Chưa Thanh Toán</h6>
                <h3 class="text-warning">{{ formatCurrency(pendingAmount) }}</h3>
              </div>
            </div>
          </div>

          <!-- Invoices Table -->
          <div v-if="invoices.length === 0" class="alert alert-info">
            <i class="bi bi-inbox"></i> Bạn chưa có hóa đơn nào.
          </div>
          <table v-else class="table table-hover table-striped">
            <thead class="table-dark">
              <tr>
                <th>Mã HĐ</th>
                <th>Dịch Vụ</th>
                <th>Thú Cưng</th>
                <th>Ngày</th>
                <th>Tổng Cộng</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="invoice in invoices" :key="invoice.id">
                <td><strong>#{{ invoice.invoice_number }}</strong></td>
                <td>{{ invoice.appointment?.service_name }}</td>
                <td>
                  <i class="bi bi-paw"></i> {{ invoice.appointment?.pet_name }}
                </td>
                <td>{{ invoice.created_at.split(' ')[0] }}</td>
                <td><strong>{{ formatCurrency(invoice.total_amount) }}</strong></td>
                <td>
                  <span :class="'badge bg-' + (invoice.status === 'paid' ? 'success' : 'warning')">
                    {{ invoice.status === 'paid' ? 'Đã Thanh Toán' : 'Chưa Thanh Toán' }}
                  </span>
                </td>
                <td>
                  <button 
                    v-if="invoice.status !== 'paid'" 
                    @click="showPaymentModal(invoice)" 
                    class="btn btn-sm btn-primary"
                    :disabled="loading"
                  >
                    <i class="bi bi-cash"></i> Thanh Toán
                  </button>
                  <span v-else class="text-muted">✓</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Payment Modal -->
      <div v-if="selectedInvoice && showPayModal" class="modal d-block" :style="{ backgroundColor: 'rgba(0,0,0,0.5)' }">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Thanh Toán Hóa Đơn</h5>
              <button type="button" class="btn-close" @click="closePaymentModal()"></button>
            </div>
            <div class="modal-body">
              <p class="mb-3">
                <strong>Hóa Đơn:</strong> {{ selectedInvoice.invoice_number }}<br>
                <strong>Tổng Cộng:</strong> {{ formatCurrency(selectedInvoice.total_amount) }}<br>
                <strong>Còn Lại:</strong> 
                <span class="text-danger">
                  {{ formatCurrency(selectedInvoice.remaining_amount) }}
                </span>
              </p>

              <div class="mb-3">
                <label class="form-label">Số Tiền Thanh Toán:</label>
                <input 
                  v-model.number="paymentForm.amount" 
                  type="number" 
                  class="form-control"
                  :max="selectedInvoice.remaining_amount"
                  :disabled="loading"
                >
                <small class="text-muted">
                  Tối đa: {{ formatCurrency(selectedInvoice.remaining_amount) }}
                </small>
              </div>

              <div class="mb-3">
                <label class="form-label">Phương Thức:</label>
                <select v-model="paymentForm.method" class="form-select" :disabled="loading">
                  <option value="cash">Tiền Mặt</option>
                  <option value="card">Thẻ Tín Dụng</option>
                  <option value="transfer">Chuyển Khoản</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closePaymentModal()">Hủy</button>
              <button 
                type="button" 
                class="btn btn-primary" 
                @click="makePayment()"
                :disabled="loading"
              >
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Xác Nhận Thanh Toán
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
  name: 'CustomerInvoices',
  components: {
    SidebarNav
  },
  data() {
    return {
      userRole: 'customer',
      loading: false,
      error: null,
      invoices: [],
      showPayModal: false,
      selectedInvoice: null,
      paymentForm: {
        amount: 0,
        method: 'cash'
      }
    };
  },
  computed: {
    totalSpent() {
      return this.invoices.reduce((sum, inv) => sum + inv.total_amount, 0);
    },
    paidAmount() {
      return this.invoices.reduce((sum, inv) => sum + inv.paid_amount, 0);
    },
    pendingAmount() {
      return this.invoices.reduce((sum, inv) => sum + inv.remaining_amount, 0);
    }
  },
  mounted() {
    this.fetchInvoices();
  },
  methods: {
    async fetchInvoices() {
      try {
        this.loading = true;
        this.error = null;
        const response = await api.get('/invoices');
        this.invoices = response.data.data || [];
      } catch (error) {
        this.error = 'Lỗi khi tải hóa đơn: ' + (error.response?.data?.message || error.message);
        console.error('Error fetching invoices:', error);
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
    viewInvoice(invoice) {
      alert('Chi tiết hóa đơn: ' + invoice.invoice_number + '\n(Chi tiết đầy đủ sẽ được hiển thị trong modal)');
    },
    showPaymentModal(invoice) {
      this.selectedInvoice = invoice;
      this.paymentForm.amount = invoice.remaining_amount;
      this.paymentForm.method = 'cash';
      this.showPayModal = true;
    },
    closePaymentModal() {
      this.showPayModal = false;
      this.selectedInvoice = null;
      this.paymentForm = {
        amount: 0,
        method: 'cash'
      };
    },
    async makePayment() {
      if (!this.paymentForm.amount || !this.paymentForm.method) {
        this.error = 'Vui lòng điền đầy đủ thông tin';
        return;
      }

      try {
        this.loading = true;
        this.error = null;
        
        await api.post('/payments', {
          invoice_id: this.selectedInvoice.id,
          amount: this.paymentForm.amount,
          payment_method: this.paymentForm.method
        });

        alert('Thanh toán thành công!');
        this.showPayModal = false;
        this.selectedInvoice = null;
        this.paymentForm = { amount: 0, method: 'cash' };
        alert('Thanh toán thành công!');
        await this.fetchInvoices();
      } catch (error) {
        this.error = 'Lỗi khi thanh toán: ' + (error.response?.data?.message || error.message);
        console.error('Error making payment:', error);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.stat-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.stat-card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
  transform: translateY(-4px);
}

.stat-card i {
  font-size: 2.5rem;
  margin-bottom: 10px;
}

.stat-card h6 {
  font-size: 0.85rem;
  font-weight: 600;
  color: #6c757d;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 10px;
}

.stat-card h3 {
  font-size: 2rem;
  font-weight: 700;
  margin: 0;
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
  padding: 0.4rem 0.8rem;
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

.text-danger {
  color: #dc3545;
  font-weight: 600;
}

@media (max-width: 768px) {
  .stat-card {
    margin-bottom: 15px;
  }

  .table {
    font-size: 0.9rem;
  }

  .table tbody td {
    padding: 10px;
  }
}
</style>
