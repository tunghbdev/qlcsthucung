<template>
  <div class="container-fluid mt-5">
    <div class="row">
      <div class="col-md-3">
        <SidebarNav :userRole="userRole" />
      </div>
      <div class="col-md-9">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Quản Lý Hóa Đơn</h4>
          </div>
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-4">
                <input v-model="filters.search" type="text" class="form-control" placeholder="Tìm kiếm hóa đơn">
              </div>
              <div class="col-md-4">
                <select v-model="filters.paymentStatus" class="form-select">
                  <option value="">Tất Cả Trạng Thái Thanh Toán</option>
                  <option value="paid">Đã Thanh Toán</option>
                  <option value="pending">Chưa Thanh Toán</option>
                </select>
              </div>
              <div class="col-md-4">
                <button @click="exportInvoices" class="btn btn-success w-100">
                  <i class="bi bi-download"></i> Xuất Excel
                </button>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-4">
                <div class="card bg-light">
                  <div class="card-body">
                    <strong>Tổng Doanh Thu:</strong>
                    <h4 class="text-success">{{ formatCurrency(totalRevenue) }}</h4>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card bg-light">
                  <div class="card-body">
                    <strong>Đã Thanh Toán:</strong>
                    <h4 class="text-info">{{ formatCurrency(paidAmount) }}</h4>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card bg-light">
                  <div class="card-body">
                    <strong>Chưa Thanh Toán:</strong>
                    <h4 class="text-warning">{{ formatCurrency(pendingAmount) }}</h4>
                  </div>
                </div>
              </div>
            </div>

            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Mã HĐ</th>
                  <th>Khách Hàng</th>
                  <th>Ngày</th>
                  <th>Số Tiền</th>
                  <th>Trạng Thái</th>
                  <th>Hành Động</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="invoice in filteredInvoices" :key="invoice.id">
                  <td>#{{ invoice.id }}</td>
                  <td>{{ invoice.customer }}</td>
                  <td>{{ invoice.date }}</td>
                  <td>{{ formatCurrency(invoice.amount) }}</td>
                  <td>
                    <span :class="'badge bg-' + (invoice.paymentStatus === 'paid' ? 'success' : 'warning')">
                      {{ invoice.paymentStatus === 'paid' ? 'Đã Thanh Toán' : 'Chưa Thanh Toán' }}
                    </span>
                  </td>
                  <td>
                    <button @click="viewInvoice(invoice)" class="btn btn-sm btn-info me-2">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button @click="printInvoice(invoice)" class="btn btn-sm btn-secondary me-2">
                      <i class="bi bi-printer"></i>
                    </button>
                    <button v-if="invoice.paymentStatus === 'pending'" @click="markAsPaid(invoice)" class="btn btn-sm btn-success">
                      <i class="bi bi-check"></i> Xác Nhận
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
  name: 'AdminInvoices',
  components: {
    SidebarNav
  },
  data() {
    return {
      userRole: 'admin',
      filters: {
        search: '',
        paymentStatus: ''
      },
      invoices: [
        {
          id: 'INV001',
          customer: 'Nguyễn Văn A',
          date: '2024-04-19',
          amount: 150000,
          paymentStatus: 'paid'
        },
        {
          id: 'INV002',
          customer: 'Trần Thị B',
          date: '2024-04-19',
          amount: 200000,
          paymentStatus: 'paid'
        },
        {
          id: 'INV003',
          customer: 'Phạm Văn C',
          date: '2024-04-20',
          amount: 300000,
          paymentStatus: 'pending'
        },
        {
          id: 'INV004',
          customer: 'Võ Thị D',
          date: '2024-04-20',
          amount: 100000,
          paymentStatus: 'pending'
        },
      ]
    };
  },
  computed: {
    filteredInvoices() {
      return this.invoices.filter(inv => {
        const matchSearch = inv.customer.toLowerCase().includes(this.filters.search.toLowerCase()) ||
                          inv.id.toLowerCase().includes(this.filters.search.toLowerCase());
        const matchStatus = !this.filters.paymentStatus || inv.paymentStatus === this.filters.paymentStatus;
        return matchSearch && matchStatus;
      });
    },
    totalRevenue() {
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
    printInvoice(invoice) {
      alert('In hóa đơn: ' + invoice.id);
    },
    markAsPaid(invoice) {
      invoice.paymentStatus = 'paid';
      alert('Đã xác nhận thanh toán cho hóa đơn: ' + invoice.id);
    },
    exportInvoices() {
      alert('Xuất danh sách hóa đơn thành file Excel');
    }
  }
};
</script>

<style scoped>
.card {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.btn-sm {
  padding: 5px 10px;
}
</style>
