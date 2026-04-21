<template>
  <div class="container-fluid mt-5">
    <div class="row">
      <div class="col-md-3">
        <SidebarNav :userRole="userRole" />
      </div>
      <div class="col-md-9">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Quản Lý Dịch Vụ</h4>
          </div>
          <div class="card-body">
            <button @click="showAddForm = true" class="btn btn-success mb-3">
              <i class="bi bi-plus"></i> Thêm Dịch Vụ Mới
            </button>

            <div v-if="showAddForm" class="card mb-3 border-success">
              <div class="card-header bg-success text-white">
                <h5 class="mb-0">Thêm Dịch Vụ Mới</h5>
              </div>
              <div class="card-body">
                <form @submit.prevent="addService">
                  <div class="mb-3">
                    <label for="name" class="form-label">Tên Dịch Vụ</label>
                    <input v-model="newService.name" type="text" class="form-control" id="name" required>
                  </div>
                  <div class="mb-3">
                    <label for="description" class="form-label">Mô Tả</label>
                    <textarea v-model="newService.description" class="form-control" id="description"></textarea>
                  </div>
                  <div class="mb-3">
                    <label for="price" class="form-label">Giá (VND)</label>
                    <input v-model="newService.price" type="number" class="form-control" id="price" required>
                  </div>
                  <div class="mb-3">
                    <label for="duration" class="form-label">Thời Lượng (phút)</label>
                    <input v-model="newService.duration" type="number" class="form-control" id="duration" required>
                  </div>
                  <button type="submit" class="btn btn-success">Lưu</button>
                  <button type="button" @click="showAddForm = false" class="btn btn-secondary ms-2">Hủy</button>
                </form>
              </div>
            </div>

            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Tên Dịch Vụ</th>
                  <th>Mô Tả</th>
                  <th>Giá</th>
                  <th>Thời Lượng</th>
                  <th>Trạng Thái</th>
                  <th>Hành Động</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="service in services" :key="service.id">
                  <td>{{ service.name }}</td>
                  <td>{{ service.description }}</td>
                  <td>{{ formatCurrency(service.price) }}</td>
                  <td>{{ service.duration }} phút</td>
                  <td>
                    <span v-if="service.is_active" class="badge bg-success">Hoạt động</span>
                    <span v-else class="badge bg-danger">Vô hiệu</span>
                  </td>
                  <td>
                    <button @click="editService(service)" class="btn btn-sm btn-warning me-2">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button @click="toggleService(service)" class="btn btn-sm btn-info me-2">
                      <i class="bi bi-toggle2-on"></i>
                    </button>
                    <button @click="deleteService(service.id)" class="btn btn-sm btn-danger">
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
  name: 'AdminServices',
  components: {
    SidebarNav
  },
  data() {
    return {
      userRole: 'admin',
      showAddForm: false,
      newService: {
        name: '',
        description: '',
        price: 0,
        duration: 0
      },
      services: [
        {
          id: 1,
          name: 'Dịch vụ tắm',
          description: 'Tắm chuyên nghiệp với sản phẩm cao cấp',
          price: 150000,
          duration: 60,
          is_active: true
        },
        {
          id: 2,
          name: 'Cắt tỉa lông',
          description: 'Cắt lông định hình theo ý muốn',
          price: 200000,
          duration: 90,
          is_active: true
        },
        {
          id: 3,
          name: 'Tiêm phòng',
          description: 'Tiêm phòng và chăm sóc sức khỏe',
          price: 300000,
          duration: 30,
          is_active: true
        },
        {
          id: 4,
          name: 'Huấn luyện thú cưng',
          description: 'Huấn luyện thú cưng chuyên nghiệp',
          price: 500000,
          duration: 120,
          is_active: true
        },
        {
          id: 5,
          name: 'Chăm sóc hộ',
          description: 'Dịch vụ chăm sóc thú cưng tại nhà',
          price: 100000,
          duration: 45,
          is_active: true
        }
      ]
    };
  },
  methods: {
    formatCurrency(value) {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(value);
    },
    addService() {
      if (this.newService.name) {
        this.services.push({
          id: Math.max(...this.services.map(s => s.id)) + 1,
          ...this.newService,
          is_active: true
        });
        this.newService = {
          name: '',
          description: '',
          price: 0,
          duration: 0
        };
        this.showAddForm = false;
        alert('Dịch vụ đã được thêm!');
      }
    },
    editService(service) {
      alert('Chỉnh sửa dịch vụ: ' + service.name);
    },
    toggleService(service) {
      service.is_active = !service.is_active;
    },
    deleteService(id) {
      if (confirm('Bạn có chắc chắn muốn xóa dịch vụ này?')) {
        this.services = this.services.filter(s => s.id !== id);
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

.badge {
  padding: 5px 10px;
}
</style>
