<template>
  <div class="container-fluid mt-5">
    <div class="row">
      <div class="col-md-3">
        <SidebarNav :userRole="userRole" />
      </div>
      <div class="col-md-9">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Quản Lý Khách Hàng</h4>
          </div>
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-6">
                <input v-model="filters.search" type="text" class="form-control" placeholder="Tìm kiếm khách hàng">
              </div>
              <div class="col-md-6">
                <button @click="exportCustomers" class="btn btn-success w-100">
                  <i class="bi bi-download"></i> Xuất Excel
                </button>
              </div>
            </div>

            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Tên Khách Hàng</th>
                  <th>Email</th>
                  <th>Số Điện Thoại</th>
                  <th>Tổng Chi Tiêu</th>
                  <th>Lượt Đặt Lịch</th>
                  <th>Ngày Đăng Ký</th>
                  <th>Hành Động</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="customer in filteredCustomers" :key="customer.id">
                  <td>{{ customer.name }}</td>
                  <td>{{ customer.email }}</td>
                  <td>{{ customer.phone }}</td>
                  <td>{{ formatCurrency(customer.totalSpent) }}</td>
                  <td>{{ customer.appointmentCount }}</td>
                  <td>{{ customer.registeredDate }}</td>
                  <td>
                    <button @click="viewCustomer(customer)" class="btn btn-sm btn-info me-2">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button @click="contactCustomer(customer)" class="btn btn-sm btn-primary me-2">
                      <i class="bi bi-envelope"></i>
                    </button>
                    <button @click="deleteCustomer(customer.id)" class="btn btn-sm btn-danger">
                      <i class="bi bi-trash"></i>
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
  name: 'AdminCustomers',
  components: {
    SidebarNav
  },
  data() {
    return {
      userRole: 'admin',
      filters: {
        search: ''
      },
      customers: [
        {
          id: 1,
          name: 'Nguyễn Văn A',
          email: 'nguyenvana@example.com',
          phone: '0912345678',
          totalSpent: 1500000,
          appointmentCount: 10,
          registeredDate: '2023-01-15'
        },
        {
          id: 2,
          name: 'Trần Thị B',
          email: 'tranthib@example.com',
          phone: '0987654321',
          totalSpent: 2000000,
          appointmentCount: 15,
          registeredDate: '2023-03-20'
        },
        {
          id: 3,
          name: 'Phạm Văn C',
          email: 'phamvanc@example.com',
          phone: '0901234567',
          totalSpent: 800000,
          appointmentCount: 5,
          registeredDate: '2024-01-10'
        },
        {
          id: 4,
          name: 'Võ Thị D',
          email: 'vothid@example.com',
          phone: '0923456789',
          totalSpent: 1200000,
          appointmentCount: 8,
          registeredDate: '2024-02-05'
        },
      ]
    };
  },
  computed: {
    filteredCustomers() {
      return this.customers.filter(customer => 
        customer.name.toLowerCase().includes(this.filters.search.toLowerCase()) ||
        customer.email.toLowerCase().includes(this.filters.search.toLowerCase()) ||
        customer.phone.includes(this.filters.search)
      );
    }
  },
  methods: {
    formatCurrency(value) {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(value);
    },
    viewCustomer(customer) {
      alert('Xem chi tiết khách hàng: ' + customer.name);
    },
    contactCustomer(customer) {
      alert('Gửi email đến: ' + customer.email);
    },
    deleteCustomer(id) {
      if (confirm('Bạn có chắc chắn muốn xóa khách hàng này?')) {
        this.customers = this.customers.filter(c => c.id !== id);
      }
    },
    exportCustomers() {
      alert('Xuất danh sách khách hàng thành file Excel');
    }
  }
};
</script>

<style scoped>
.card {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.btn-sm {
  padding: 5px 10px;
}

.table {
  background-color: white;
}
</style>
