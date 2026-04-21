<template>
  <div class="container-fluid mt-5">
    <div class="row">
      <div class="col-md-3">
        <SidebarNav :userRole="userRole" />
      </div>
      <div class="col-md-9">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Thông Tin Cá Nhân</h4>
          </div>
          <div class="card-body">
            <div v-if="!isEditing" class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label"><strong>Họ và Tên:</strong></label>
                  <p>{{ profile.name }}</p>
                </div>
                <div class="mb-3">
                  <label class="form-label"><strong>Email:</strong></label>
                  <p>{{ profile.email }}</p>
                </div>
                <div class="mb-3">
                  <label class="form-label"><strong>Số Điện Thoại:</strong></label>
                  <p>{{ profile.phone }}</p>
                </div>
                <div class="mb-3">
                  <label class="form-label"><strong>Ngày Sinh:</strong></label>
                  <p>{{ profile.dob }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label"><strong>Địa Chỉ:</strong></label>
                  <p>{{ profile.address }}</p>
                </div>
                <div class="mb-3">
                  <label class="form-label"><strong>Thành Phố:</strong></label>
                  <p>{{ profile.city }}</p>
                </div>
                <div class="mb-3">
                  <label class="form-label"><strong>Mã Bưu Điện:</strong></label>
                  <p>{{ profile.zipCode }}</p>
                </div>
                <div class="mb-3">
                  <label class="form-label"><strong>Ngày Đăng Ký:</strong></label>
                  <p>{{ profile.registeredDate }}</p>
                </div>
              </div>
            </div>

            <div v-else class="form-section">
              <form @submit.prevent="saveProfile">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="name" class="form-label">Họ và Tên</label>
                      <input v-model="profile.name" type="text" class="form-control" id="name">
                    </div>
                    <div class="mb-3">
                      <label for="email" class="form-label">Email</label>
                      <input v-model="profile.email" type="email" class="form-control" id="email">
                    </div>
                    <div class="mb-3">
                      <label for="phone" class="form-label">Số Điện Thoại</label>
                      <input v-model="profile.phone" type="tel" class="form-control" id="phone">
                    </div>
                    <div class="mb-3">
                      <label for="dob" class="form-label">Ngày Sinh</label>
                      <input v-model="profile.dob" type="date" class="form-control" id="dob">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="address" class="form-label">Địa Chỉ</label>
                      <input v-model="profile.address" type="text" class="form-control" id="address">
                    </div>
                    <div class="mb-3">
                      <label for="city" class="form-label">Thành Phố</label>
                      <input v-model="profile.city" type="text" class="form-control" id="city">
                    </div>
                    <div class="mb-3">
                      <label for="zipCode" class="form-label">Mã Bưu Điện</label>
                      <input v-model="profile.zipCode" type="text" class="form-control" id="zipCode">
                    </div>
                  </div>
                </div>
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-check2"></i> Lưu Thay Đổi
                </button>
                <button type="button" @click="isEditing = false" class="btn btn-secondary ms-2">
                  <i class="bi bi-x"></i> Hủy
                </button>
              </form>
            </div>

            <div class="mt-4 pt-3 border-top">
              <h5>Bảo Mật</h5>
              <button @click="showChangePasswordForm = !showChangePasswordForm" class="btn btn-warning">
                <i class="bi bi-lock"></i> Thay Đổi Mật Khẩu
              </button>

              <div v-if="showChangePasswordForm" class="card mt-3">
                <div class="card-header bg-warning">
                  <h5 class="mb-0">Thay Đổi Mật Khẩu</h5>
                </div>
                <div class="card-body">
                  <form @submit.prevent="changePassword">
                    <div class="mb-3">
                      <label for="oldPassword" class="form-label">Mật Khẩu Cũ</label>
                      <input v-model="passwordForm.oldPassword" type="password" class="form-control" id="oldPassword" required>
                    </div>
                    <div class="mb-3">
                      <label for="newPassword" class="form-label">Mật Khẩu Mới</label>
                      <input v-model="passwordForm.newPassword" type="password" class="form-control" id="newPassword" required>
                    </div>
                    <div class="mb-3">
                      <label for="confirmPassword" class="form-label">Xác Nhận Mật Khẩu Mới</label>
                      <input v-model="passwordForm.confirmPassword" type="password" class="form-control" id="confirmPassword" required>
                    </div>
                    <button type="submit" class="btn btn-success">
                      <i class="bi bi-check2"></i> Lưu
                    </button>
                    <button type="button" @click="showChangePasswordForm = false" class="btn btn-secondary ms-2">
                      <i class="bi bi-x"></i> Hủy
                    </button>
                  </form>
                </div>
              </div>
            </div>

            <div v-if="!isEditing && !showChangePasswordForm" class="mt-4">
              <button @click="isEditing = true" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Chỉnh Sửa Thông Tin
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

export default {
  name: 'CustomerProfile',
  components: {
    SidebarNav
  },
  data() {
    return {
      userRole: 'customer',
      isEditing: false,
      showChangePasswordForm: false,
      profile: {
        name: 'Nguyễn Văn A',
        email: 'nguyenvana@example.com',
        phone: '0912345678',
        dob: '1990-05-15',
        address: '123 Đường Abc, Quận 1',
        city: 'Thành Phố Hồ Chí Minh',
        zipCode: '700000',
        registeredDate: '2023-01-15'
      },
      passwordForm: {
        oldPassword: '',
        newPassword: '',
        confirmPassword: ''
      }
    };
  },
  methods: {
    saveProfile() {
      alert('Thông tin đã được cập nhật!');
      this.isEditing = false;
    },
    changePassword() {
      if (this.passwordForm.newPassword !== this.passwordForm.confirmPassword) {
        alert('Mật khẩu mới không khớp!');
        return;
      }

      alert('Mật khẩu đã được thay đổi!');
      this.passwordForm = {
        oldPassword: '',
        newPassword: '',
        confirmPassword: ''
      };
      this.showChangePasswordForm = false;
    }
  }
};
</script>

<style scoped>
.card {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.form-section {
  background-color: #f8f9fa;
  padding: 20px;
  border-radius: 5px;
}

.btn {
  margin-right: 10px;
  margin-bottom: 10px;
}
</style>
