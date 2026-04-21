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

            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Mã HĐ</th>
                  <th>Dịch Vụ</th>
                  <th>Thú Cưng</th>
                  <th>Ngày</th>
                  <th>Số Tiền</th>
                  <th>Trạng Thái</th>
                  <th>Hành Động</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="invoice in invoices" :key="invoice.id">
                  <td>#{{ invoice.id }}</td>
                  <td>{{ invoice.service }}</td>
                  <td>{{ invoice.pet }}</td>
                  <td>{{ invoice.date }}</td>
                  <td>{{ formatCurrency(invoice.amount) }}</td>
                  <td>
                    <span :class="'badge bg-' + (invoice.paymentStatus === 'paid' ? 'success' : 'warning')">
                      {{ invoice.paymentStatus === 'paid' ? 'Đã Thanh Toán' : 'Chưa Thanh Toán' }}
                    </span>
                  </td>
                  <td>
                    <button @click="viewInvoice(invoice)" class="btn btn-sm btn-info me-2">
                      <i class="bi bi-eye"></i> Xem
                    </button>
                    <button v-if="invoice.paymentStatus === 'pending'" @click="payInvoice(invoice)" class="btn btn-sm btn-success">
                      <i class="bi bi-cash-coin"></i> Thanh Toán
                    </button>
                    <button v-else @click="printInvoice(invoice)" class="btn btn-sm btn-secondary">
                      <i class="bi bi-printer"></i> In
                    </button>
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

export default {
  name: 'CustomerInvoices',
  components: {
    SidebarNav
  },
  data() {
    return {
      userRole: 'customer',
      invoices: [
        {
          id: 'INV001',
          service: 'Tắm',
          pet: 'Bess (Chó)',
          date: '2024-04-19',
          amount: 150000,
          paymentStatus: 'paid'
        },
        {
          id: 'INV002',
          service: 'Cắt Tỉa Lông',
          pet: 'Miu (Mèo)',
          date: '2024-04-20',
          amount: 200000,
          paymentStatus: 'pending'
        },
        {
          id: 'INV003',
          service: 'Tiêm Phòng',
          pet: 'Bess (Chó)',
          date: '2024-04-18',
          amount: 300000,
          paymentStatus: 'paid'
        },
      ]
    };
  },
  computed: {
    totalSpent() {
      return this.invoices.reduce((sum, inv) => sum + inv.amount, 0);
    },
    paidAmount() {
      return this.invoices
        .filter(inv => inv.paymentStatus === 'paid')
        .reduce((sum, inv) => sum + inv.amount, 0);
    },
    pendingAmount() {
      return this.invoices
        .filter(inv => inv.paymentStatus === 'pending')
        .reduce((sum, inv) => sum + inv.amount, 0);
    }
  },
  methods: {
    formatCurrency(value) {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(value);
    },
    viewInvoice(invoice) {
      alert('Chi tiết hóa đơn: ' + invoice.id);
    },
    payInvoice(invoice) {
      if (confirm('Thanh toán hóa đơn ' + invoice.id + ' - ' + this.formatCurrency(invoice.amount) + '?')) {
        invoice.paymentStatus = 'paid';
        alert('Thanh toán thành công!');
      }
    },
    printInvoice(invoice) {
      alert('In hóa đơn: ' + invoice.id);
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
