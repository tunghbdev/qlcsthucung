<template>
  <div class="container-fluid mt-5">
    <div class="row">
      <div class="col-md-3">
        <SidebarNav :userRole="userRole" />
      </div>
      <div class="col-md-9">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Hóa Đơn Của Tôi</h4>
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

            <div class="row mb-3">
              <div class="col-md-4">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <strong>Tổng Chi Tiêu</strong>
                    <h4 class="text-success mt-2">{{ formatCurrency(totalSpent) }}</h4>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <strong>Đã Thanh Toán</strong>
                    <h4 class="text-info mt-2">{{ formatCurrency(paidAmount) }}</h4>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <strong>Chưa Thanh Toán</strong>
                    <h4 class="text-warning mt-2">{{ formatCurrency(pendingAmount) }}</h4>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="invoices.length === 0" class="alert alert-info">
              Bạn chưa có hóa đơn nào.
            </div>
            <table v-else class="table table-hover">
              <thead>
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
                  <td>{{ invoice.invoice_number }}</td>
                  <td>{{ invoice.appointment?.service_name }}</td>
                  <td>{{ invoice.appointment?.pet_name }}</td>
                  <td>{{ invoice.created_at.split(' ')[0] }}</td>
                  <td>{{ formatCurrency(invoice.total_amount) }}</td>
                  <td>
                    <span :class="'badge bg-' + (invoice.status === 'paid' ? 'success' : 'warning')">
                      {{ invoice.status === 'paid' ? 'Đã Thanh Toán' : 'Chưa Thanh Toán' }}
                    </span>
                  </td>
                  <td>
                    <button @click="viewInvoice(invoice)" class="btn btn-sm btn-info me-2" :disabled="loading">
                      <i class="bi bi-eye"></i> Xem
                    </button>
                    <button v-if="invoice.status === 'unpaid'" @click="openPaymentModal(invoice)" class="btn btn-sm btn-success" :disabled="loading">
                      <i class="bi bi-cash-coin"></i> Thanh Toán
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Modal -->
    <div v-if="showPaymentModal" class="modal d-block" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Thanh Toán Hóa Đơn</h5>
            <button type="button" class="btn-close" @click="showPaymentModal = false"></button>
          </div>
          <div class="modal-body">
            <div v-if="selectedInvoice" class="mb-3">
              <p><strong>Mã HĐ:</strong> {{ selectedInvoice.invoice_number }}</p>
              <p><strong>Tổng Cộng:</strong> {{ formatCurrency(selectedInvoice.total_amount) }}</p>
              <p><strong>Đã Thanh Toán:</strong> {{ formatCurrency(selectedInvoice.paid_amount) }}</p>
              <p><strong>Còn Lại:</strong> {{ formatCurrency(selectedInvoice.remaining_amount) }}</p>

              <div class="mb-3">
                <label for="paymentAmount" class="form-label">Số Tiền Thanh Toán</label>
                <input 
                  v-model.number="paymentForm.amount" 
                  type="number" 
                  class="form-control" 
                  id="paymentAmount"
                  :max="selectedInvoice.remaining_amount"
                  step="0.01"
                >
              </div>

              <div class="mb-3">
                <label for="paymentMethod" class="form-label">Phương Thức Thanh Toán</label>
                <select v-model="paymentForm.method" class="form-select" id="paymentMethod">
                  <option value="">-- Chọn phương thức --</option>
                  <option value="cash">Tiền Mặt</option>
                  <option value="credit_card">Thẻ Tín Dụng</option>
                  <option value="debit_card">Thẻ Ghi Nợ</option>
                  <option value="bank_transfer">Chuyển Khoản</option>
                  <option value="e_wallet">Ví Điện Tử</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showPaymentModal = false">Hủy</button>
            <button type="button" class="btn btn-success" @click="makePayment" :disabled="loading">Thanh Toán</button>
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
      showPaymentModal: false,
      selectedInvoice: null,
      paymentForm: {
        amount: 0,
        method: ''
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
    openPaymentModal(invoice) {
      this.selectedInvoice = invoice;
      this.paymentForm.amount = invoice.remaining_amount;
      this.paymentForm.method = '';
      this.showPaymentModal = true;
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
        this.showPaymentModal = false;
        this.selectedInvoice = null;
        this.paymentForm = { amount: 0, method: '' };
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
.card {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.table {
  background-color: white;
}

.btn-sm {
  padding: 5px 10px;
}
</style>
