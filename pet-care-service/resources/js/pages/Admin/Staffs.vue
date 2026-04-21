<template>
  <div class="container-fluid mt-5">
    <div class="row">
      <div class="col-md-3">
        <SidebarNav :userRole="userRole" />
      </div>
      <div class="col-md-9">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Quản Lý Nhân Viên</h4>
          </div>
          <div class="card-body">
            <button @click="showAddForm = true" class="btn btn-success mb-3">
              <i class="bi bi-plus"></i> Thêm Nhân Viên Mới
            </button>

            <div v-if="showAddForm" class="card mb-3 border-success">
              <div class="card-header bg-success text-white">
                <h5 class="mb-0">Thêm Nhân Viên Mới</h5>
              </div>
              <div class="card-body">
                <form @submit.prevent="addStaff">
                  <div class="mb-3">
                    <label for="name" class="form-label">Tên Nhân Viên</label>
                    <input v-model="newStaff.name" type="text" class="form-control" id="name" required>
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input v-model="newStaff.email" type="email" class="form-control" id="email" required>
                  </div>
                  <div class="mb-3">
                    <label for="phone" class="form-label">Số Điện Thoại</label>
                    <input v-model="newStaff.phone" type="tel" class="form-control" id="phone">
                  </div>
                  <div class="mb-3">
                    <label for="services" class="form-label">Dịch Vụ Chuyên Môn</label>
                    <div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="tam" id="service1">
                        <label class="form-check-label" for="service1">Tắm</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="cat-tia" id="service2">
                        <label class="form-check-label" for="service2">Cắt Tỉa Lông</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="tiem-phong" id="service3">
                        <label class="form-check-label" for="service3">Tiêm Phòng</label>
                      </div>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-success">Lưu</button>
                  <button type="button" @click="showAddForm = false" class="btn btn-secondary ms-2">Hủy</button>
                </form>
              </div>
            </div>

            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Tên</th>
                  <th>Email</th>
                  <th>Số Điện Thoại</th>
                  <th>Dịch Vụ Chuyên Môn</th>
                  <th>Trạng Thái</th>
                  <th>Hành Động</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="staff in staffs" :key="staff.id">
                  <td>{{ staff.name }}</td>
                  <td>{{ staff.email }}</td>
                  <td>{{ staff.phone }}</td>
                  <td>{{ staff.services.join(', ') }}</td>
                  <td>
                    <span :class="'badge bg-' + (staff.is_active ? 'success' : 'danger')">
                      {{ staff.is_active ? 'Hoạt động' : 'Bị khoá' }}
                    </span>
                  </td>
                  <td>
                    <button @click="editStaff(staff)" class="btn btn-sm btn-warning me-2">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button @click="toggleStaff(staff)" class="btn btn-sm btn-info me-2">
                      <i class="bi bi-toggle2-on"></i>
                    </button>
                    <button @click="deleteStaff(staff.id)" class="btn btn-sm btn-danger">
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
  name: 'AdminStaffs',
  components: {
    SidebarNav
  },
  data() {
    return {
      userRole: 'admin',
      showAddForm: false,
      newStaff: {
        name: '',
        email: '',
        phone: '',
        services: []
      },
      staffs: [
        {
          id: 1,
          name: 'Nguyễn Văn X',
          email: 'staff1@example.com',
          phone: '0912345678',
          services: ['Tắm', 'Cắt Tỉa Lông'],
          is_active: true
        },
        {
          id: 2,
          name: 'Trần Thị Y',
          email: 'staff2@example.com',
          phone: '0987654321',
          services: ['Tiêm Phòng', 'Huấn Luyện'],
          is_active: true
        },
        {
          id: 3,
          name: 'Phạm Văn Z',
          email: 'staff3@example.com',
          phone: '0901234567',
          services: ['Chăm Sóc Hộ'],
          is_active: true
        },
      ]
    };
  },
  methods: {
    addStaff() {
      if (this.newStaff.name && this.newStaff.email) {
        this.staffs.push({
          id: Math.max(...this.staffs.map(s => s.id)) + 1,
          ...this.newStaff,
          is_active: true
        });
        this.newStaff = { name: '', email: '', phone: '', services: [] };
        this.showAddForm = false;
        alert('Nhân viên đã được thêm!');
      }
    },
    editStaff(staff) {
      alert('Chỉnh sửa nhân viên: ' + staff.name);
    },
    toggleStaff(staff) {
      staff.is_active = !staff.is_active;
    },
    deleteStaff(id) {
      if (confirm('Bạn có chắc chắn muốn xóa nhân viên này?')) {
        this.staffs = this.staffs.filter(s => s.id !== id);
      }
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
</style>
