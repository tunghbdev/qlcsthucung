<template>
  <div class="container-fluid mt-5">
    <div class="row">
      <div class="col-md-3">
        <SidebarNav :userRole="userRole" />
      </div>
      <div class="col-md-9">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Quản Lý Lịch Hẹn</h4>
          </div>
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-4">
                <input 
                  v-model="filters.search" 
                  type="text" 
                  class="form-control" 
                  placeholder="Tìm kiếm khách hàng hoặc thú cưng"
                >
              </div>
              <div class="col-md-4">
                <select v-model="filters.status" class="form-select">
                  <option value="">Tất Cả Trạng Thái</option>
                  <option value="pending">Chờ Xử Lý</option>
                  <option value="processing">Đang Thực Hiện</option>
                  <option value="completed">Đã Hoàn Thành</option>
                  <option value="cancelled">Hủy</option>
                </select>
              </div>
              <div class="col-md-4">
                <button @click="exportAppointments" class="btn btn-success w-100">
                  <i class="bi bi-download"></i> Xuất Excel
                </button>
              </div>
            </div>

            <table class="table table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Khách Hàng</th>
                  <th>Thú Cưng</th>
                  <th>Dịch Vụ</th>
                  <th>Ngày Giờ</th>
                  <th>Trạng Thái</th>
                  <th>Nhân Viên</th>
                  <th>Hành Động</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="apt in filteredAppointments" :key="apt.id">
                  <td>#{{ apt.id }}</td>
                  <td>{{ apt.customer }}</td>
                  <td>{{ apt.pet }}</td>
                  <td>{{ apt.service }}</td>
                  <td>{{ apt.dateTime }}</td>
                  <td>
                    <span :class="'badge bg-' + getStatusColor(apt.status)">{{ getStatusLabel(apt.status) }}</span>
                  </td>
                  <td>{{ apt.staff }}</td>
                  <td>
                    <button @click="viewAppointment(apt)" class="btn btn-sm btn-info me-2">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button @click="editAppointmentStatus(apt)" class="btn btn-sm btn-warning me-2">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button @click="deleteAppointment(apt.id)" class="btn btn-sm btn-danger">
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
  name: 'AdminAppointments',
  components: {
    SidebarNav
  },
  data() {
    return {
      userRole: 'admin',
      filters: {
        search: '',
        status: ''
      },
      appointments: [
        {
          id: 1,
          customer: 'Nguyễn Văn A',
          pet: 'Bess (Chó)',
          service: 'Tắm',
          dateTime: '2024-04-20 09:00',
          status: 'completed',
          staff: 'Nhân viên 1'
        },
        {
          id: 2,
          customer: 'Trần Thị B',
          pet: 'Miu (Mèo)',
          service: 'Cắt tỉa lông',
          dateTime: '2024-04-20 10:30',
          status: 'processing',
          staff: 'Nhân viên 2'
        },
        {
          id: 3,
          customer: 'Phạm Văn C',
          pet: 'Puppy (Chó)',
          service: 'Tiêm phòng',
          dateTime: '2024-04-20 14:00',
          status: 'pending',
          staff: 'Nhân viên 3'
        },
        {
          id: 4,
          customer: 'Võ Thị D',
          pet: 'Leo (Chó)',
          service: 'Huấn luyện',
          dateTime: '2024-04-21 15:00',
          status: 'pending',
          staff: 'Chưa gán'
        },
      ]
    };
  },
  computed: {
    filteredAppointments() {
      return this.appointments.filter(apt => {
        const matchSearch = apt.customer.toLowerCase().includes(this.filters.search.toLowerCase()) ||
                          apt.pet.toLowerCase().includes(this.filters.search.toLowerCase());
        const matchStatus = !this.filters.status || apt.status === this.filters.status;
        return matchSearch && matchStatus;
      });
    }
  },
  methods: {
    getStatusColor(status) {
      const colors = {
        completed: 'success',
        processing: 'info',
        pending: 'warning',
        cancelled: 'danger'
      };
      return colors[status] || 'secondary';
    },
    getStatusLabel(status) {
      const labels = {
        completed: 'Đã Hoàn Thành',
        processing: 'Đang Thực Hiện',
        pending: 'Chờ Xử Lý',
        cancelled: 'Hủy'
      };
      return labels[status] || status;
    },
    viewAppointment(apt) {
      alert('Chi tiết lịch hẹn #' + apt.id);
    },
    editAppointmentStatus(apt) {
      alert('Cập nhật trạng thái lịch hẹn #' + apt.id);
    },
    deleteAppointment(id) {
      if (confirm('Bạn có chắc chắn muốn xóa lịch hẹn này?')) {
        this.appointments = this.appointments.filter(a => a.id !== id);
      }
    },
    exportAppointments() {
      alert('Xuất danh sách lịch hẹn thành file Excel');
    }
  }
};
</script>

<style scoped>
.table {
  background-color: white;
}

.btn-sm {
  padding: 5px 10px;
}

.badge {
  padding: 5px 10px;
}
</style>
