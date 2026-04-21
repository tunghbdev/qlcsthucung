<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header bg-success text-white">
            <h4 class="mb-0">Đăng Ký Tài Khoản</h4>
          </div>
          <div class="card-body">
            <form @submit.prevent="register">
              <div class="mb-3">
                <label for="name" class="form-label">Họ và Tên</label>
                <input 
                  v-model="form.name" 
                  type="text" 
                  class="form-control" 
                  id="name" 
                  required
                  placeholder="Nhập họ và tên"
                >
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input 
                  v-model="form.email" 
                  type="email" 
                  class="form-control" 
                  id="email" 
                  required
                  placeholder="Nhập email"
                >
              </div>

              <div class="mb-3">
                <label for="phone" class="form-label">Số Điện Thoại</label>
                <input 
                  v-model="form.phone" 
                  type="tel" 
                  class="form-control" 
                  id="phone" 
                  placeholder="Nhập số điện thoại"
                >
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Mật Khẩu</label>
                <input 
                  v-model="form.password" 
                  type="password" 
                  class="form-control" 
                  id="password" 
                  required
                  placeholder="Nhập mật khẩu"
                >
              </div>

              <div class="mb-3">
                <label for="password_confirm" class="form-label">Xác Nhận Mật Khẩu</label>
                <input 
                  v-model="form.password_confirm" 
                  type="password" 
                  class="form-control" 
                  id="password_confirm" 
                  required
                  placeholder="Xác nhận mật khẩu"
                >
              </div>

              <div v-if="error" class="alert alert-danger">{{ error }}</div>
              <div v-if="success" class="alert alert-success">{{ success }}</div>

              <button type="submit" class="btn btn-success w-100" :disabled="loading">
                <span v-if="!loading">Đăng Ký</span>
                <span v-else>Đang xử lý...</span>
              </button>
            </form>

            <p class="mt-3 text-center">
              Đã có tài khoản? 
              <router-link to="/login" class="text-success">Đăng Nhập Ngay</router-link>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/api.js';

export default {
  name: 'Register',
  data() {
    return {
      form: {
        name: '',
        email: '',
        phone: '',
        password: '',
        password_confirm: ''
      },
      loading: false,
      error: '',
      success: ''
    };
  },
  methods: {
    async register() {
      this.error = '';
      this.success = '';

      if (this.form.password !== this.form.password_confirm) {
        this.error = 'Mật khẩu không khớp!';
        return;
      }

      if (this.form.password.length < 6) {
        this.error = 'Mật khẩu phải có ít nhất 6 ký tự!';
        return;
      }

      this.loading = true;

      try {
        const response = await api.post('/auth/register', {
          name: this.form.name,
          email: this.form.email,
          phone: this.form.phone,
          password: this.form.password,
          password_confirmation: this.form.password_confirm
        });

        const { token, user } = response.data;

        // Store auth data
        localStorage.setItem('token', token);
        localStorage.setItem('role', user.role);
        localStorage.setItem('userName', user.name);
        localStorage.setItem('userEmail', user.email);

        this.success = 'Đăng ký thành công! Đang chuyển hướng...';

        setTimeout(() => {
          this.$router.push('/dashboard');
        }, 1500);
      } catch (error) {
        if (error.response?.data?.message) {
          this.error = error.response.data.message;
        } else if (error.response?.data?.errors) {
          const errors = error.response.data.errors;
          this.error = Object.values(errors)[0]?.[0] || 'Đăng ký thất bại!';
        } else {
          this.error = 'Đăng ký thất bại! Vui lòng thử lại.';
        }
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.card {
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

.card-header {
  border-bottom: 2px solid #198754;
}

.btn-success {
  padding: 10px 20px;
  font-weight: bold;
}

.text-success {
  text-decoration: none;
  font-weight: bold;
}

.text-success:hover {
  text-decoration: underline;
}
</style>
