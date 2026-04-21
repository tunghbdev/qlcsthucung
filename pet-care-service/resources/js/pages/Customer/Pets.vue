<template>
  <div class="container-fluid mt-5">
    <div class="row">
      <div class="col-md-3">
        <SidebarNav :userRole="userRole" />
      </div>
      <div class="col-md-9">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Quản Lý Thú Cưng Của Tôi</h4>
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

            <button @click="showAddForm = true" class="btn btn-success mb-3" :disabled="loading">
              <i class="bi bi-plus"></i> Thêm Thú Cưng Mới
            </button>

            <div v-if="showAddForm" class="card mb-3 border-success">
              <div class="card-header bg-success text-white">
                <h5 class="mb-0">Thêm Thú Cưng Mới</h5>
              </div>
              <div class="card-body">
                <form @submit.prevent="addPet">
                  <div class="mb-3">
                    <label for="name" class="form-label">Tên Thú Cưng</label>
                    <input v-model="newPet.name" type="text" class="form-control" id="name" required>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="type" class="form-label">Loại Thú Cưng</label>
                        <select v-model="newPet.type" class="form-select" id="type" required>
                          <option value="">-- Chọn loại --</option>
                          <option value="Chó">Chó</option>
                          <option value="Mèo">Mèo</option>
                          <option value="Chim">Chim</option>
                          <option value="Chuột">Chuột</option>
                          <option value="Khác">Khác</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="breed" class="form-label">Giống Loài</label>
                        <input v-model="newPet.breed" type="text" class="form-control" id="breed">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="age" class="form-label">Tuổi (năm)</label>
                        <input v-model="newPet.age" type="number" class="form-control" id="age">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="weight" class="form-label">Cân Nặng (kg)</label>
                        <input v-model="newPet.weight" type="number" class="form-control" id="weight">
                      </div>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="description" class="form-label">Mô Tả</label>
                    <textarea v-model="newPet.description" class="form-control" id="description" rows="3"></textarea>
                  </div>
                  <button type="submit" class="btn btn-success" :disabled="loading">Lưu</button>
                  <button type="button" @click="showAddForm = false; error = null" class="btn btn-secondary ms-2">Hủy</button>
                </form>
              </div>
            </div>

            <div v-if="showEditForm && editingPet" class="card mb-3 border-warning">
              <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Chỉnh Sửa Thú Cưng: {{ editingPet.name }}</h5>
              </div>
              <div class="card-body">
                <form @submit.prevent="savePet">
                  <div class="mb-3">
                    <label for="edit-name" class="form-label">Tên Thú Cưng</label>
                    <input v-model="editingPet.name" type="text" class="form-control" id="edit-name" required>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="edit-type" class="form-label">Loại Thú Cưng</label>
                        <select v-model="editingPet.type" class="form-select" id="edit-type" required>
                          <option value="">-- Chọn loại --</option>
                          <option value="Chó">Chó</option>
                          <option value="Mèo">Mèo</option>
                          <option value="Chim">Chim</option>
                          <option value="Chuột">Chuột</option>
                          <option value="Khác">Khác</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="edit-breed" class="form-label">Giống Loài</label>
                        <input v-model="editingPet.breed" type="text" class="form-control" id="edit-breed">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="edit-age" class="form-label">Tuổi (năm)</label>
                        <input v-model="editingPet.age" type="number" class="form-control" id="edit-age">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="edit-weight" class="form-label">Cân Nặng (kg)</label>
                        <input v-model="editingPet.weight" type="number" class="form-control" id="edit-weight">
                      </div>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="edit-description" class="form-label">Mô Tả</label>
                    <textarea v-model="editingPet.description" class="form-control" id="edit-description" rows="3"></textarea>
                  </div>
                  <button type="submit" class="btn btn-warning" :disabled="loading">Lưu Thay Đổi</button>
                  <button type="button" @click="cancelEdit" class="btn btn-secondary ms-2">Hủy</button>
                </form>
              </div>
            </div>

            <div v-if="pets.length === 0 && !showAddForm && !loading" class="alert alert-info">
              Bạn chưa có thú cưng nào. Hãy thêm thú cưng đầu tiên!
            </div>

            <div class="row">
              <div v-for="pet in pets" :key="pet.id" class="col-md-6 mb-3">
                <div class="card">
                  <div class="card-header">
                    <h5 class="mb-0">{{ pet.name }}</h5>
                  </div>
                  <div class="card-body">
                    <p><strong>Loại:</strong> {{ pet.type }}</p>
                    <p><strong>Giống Loài:</strong> {{ pet.breed }}</p>
                    <p v-if="pet.age"><strong>Tuổi:</strong> {{ pet.age }} năm</p>
                    <p v-if="pet.weight"><strong>Cân Nặng:</strong> {{ pet.weight }} kg</p>
                    <p v-if="pet.description"><strong>Mô Tả:</strong> {{ pet.description }}</p>
                    <div class="btn-group w-100" role="group">
                      <button @click="startEditPet(pet)" class="btn btn-sm btn-warning" :disabled="loading">
                        <i class="bi bi-pencil"></i> Sửa
                      </button>
                      <button @click="viewPetHistory(pet)" class="btn btn-sm btn-info" :disabled="loading">
                        <i class="bi bi-history"></i> Lịch Sử
                      </button>
                      <button @click="deletePet(pet.id)" class="btn btn-sm btn-danger" :disabled="loading">
                        <i class="bi bi-trash"></i> Xóa
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
</template>

<script>
import SidebarNav from '@/components/SidebarNav.vue';
import api from '@/services/api';

export default {
  name: 'CustomerPets',
  components: {
    SidebarNav
  },
  data() {
    return {
      userRole: 'customer',
      showAddForm: false,
      showEditForm: false,
      editingPet: null,
      loading: false,
      error: null,
      newPet: {
        name: '',
        type: '',
        breed: '',
        age: '',
        weight: '',
        description: ''
      },
      pets: []
    };
  },
  mounted() {
    this.fetchPets();
  },
  methods: {
    async fetchPets() {
      try {
        this.loading = true;
        this.error = null;
        const response = await api.get('/pets');
        this.pets = response.data.data || [];
      } catch (error) {
        this.error = 'Lỗi khi tải danh sách thú cưng: ' + (error.response?.data?.message || error.message);
        console.error('Error fetching pets:', error);
      } finally {
        this.loading = false;
      }
    },
    async addPet() {
      if (!this.newPet.name || !this.newPet.type) {
        this.error = 'Vui lòng điền tên và loại thú cưng';
        return;
      }

      try {
        this.loading = true;
        this.error = null;
        const response = await api.post('/pets', {
          name: this.newPet.name,
          type: this.newPet.type,
          breed: this.newPet.breed,
          age: this.newPet.age ? parseInt(this.newPet.age) : null,
          weight: this.newPet.weight ? parseFloat(this.newPet.weight) : null,
          description: this.newPet.description
        });
        
        this.pets.push(response.data.data);
        this.newPet = {
          name: '',
          type: '',
          breed: '',
          age: '',
          weight: '',
          description: ''
        };
        this.showAddForm = false;
        alert('Thú cưng đã được thêm thành công!');
      } catch (error) {
        this.error = 'Lỗi khi thêm thú cưng: ' + (error.response?.data?.message || error.message);
        console.error('Error adding pet:', error);
      } finally {
        this.loading = false;
      }
    },
    startEditPet(pet) {
      this.editingPet = { ...pet };
      this.showEditForm = true;
    },
    async savePet() {
      if (!this.editingPet.name || !this.editingPet.type) {
        this.error = 'Vui lòng điền tên và loại thú cưng';
        return;
      }

      try {
        this.loading = true;
        this.error = null;
        const response = await api.put(`/pets/${this.editingPet.id}`, {
          name: this.editingPet.name,
          type: this.editingPet.type,
          breed: this.editingPet.breed,
          age: this.editingPet.age ? parseInt(this.editingPet.age) : null,
          weight: this.editingPet.weight ? parseFloat(this.editingPet.weight) : null,
          description: this.editingPet.description
        });

        const index = this.pets.findIndex(p => p.id === this.editingPet.id);
        if (index > -1) {
          this.pets[index] = response.data.data;
        }
        this.showEditForm = false;
        this.editingPet = null;
        alert('Thú cưng đã được cập nhật thành công!');
      } catch (error) {
        this.error = 'Lỗi khi cập nhật thú cưng: ' + (error.response?.data?.message || error.message);
        console.error('Error updating pet:', error);
      } finally {
        this.loading = false;
      }
    },
    viewPetHistory(pet) {
      alert('Lịch sử dịch vụ của: ' + pet.name + '\n(Tính năng sẽ được phát triển)');
    },
    async deletePet(id) {
      if (!confirm('Bạn có chắc chắn muốn xóa thú cưng này?')) {
        return;
      }

      try {
        this.loading = true;
        this.error = null;
        await api.delete(`/pets/${id}`);
        this.pets = this.pets.filter(p => p.id !== id);
        alert('Thú cưng đã được xóa thành công!');
      } catch (error) {
        this.error = 'Lỗi khi xóa thú cưng: ' + (error.response?.data?.message || error.message);
        console.error('Error deleting pet:', error);
      } finally {
        this.loading = false;
      }
    },
    cancelEdit() {
      this.showEditForm = false;
      this.editingPet = null;
    }
  }
};
</script>

<style scoped>
.card {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.card-header {
  background-color: #f8f9fa;
}

.btn-group {
  display: flex;
}

.btn-group .btn {
  flex: 1;
  border-radius: 0;
}

.btn-group .btn:first-child {
  border-radius: 0.25rem 0 0 0.25rem;
}

.btn-group .btn:last-child {
  border-radius: 0 0.25rem 0.25rem 0;
}
</style>
