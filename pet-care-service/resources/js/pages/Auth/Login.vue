<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Đăng Nhập</h4>
          </div>
          <div class="card-body">
            <form @submit.prevent="login">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input 
                  v-model="form.email" 
                  type="email" 
                  class="form-control" 
                  id="email" 
                  required
                  placeholder="Nhập email của bạn"
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
                <label for="role" class="form-label">Vai Trò</label>
                <select v-model="form.role" class="form-select" id="role">
                  <option value="customer">Khách Hàng</option>
                  <option value="staff">Nhân Viên</option>
                  <option value="admin">Quản Trị Viên</option>
                </select>
              </div>

              <div v-if="error" class="alert alert-danger">{{ error }}</div>

              <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                <span v-if="!loading">Đăng Nhập</span>
                <span v-else>Đang xử lý...</span>
              </button>
            </form>

            <p class="mt-3 text-center">
              Chưa có tài khoản? 
              <router-link to="/register" class="text-primary">Đăng Ký Ngay</router-link>
            </p>
          </div>
        </div>

        <div class="alert alert-info mt-3">
          <strong>Demo Account:</strong><br>
          Email: demo@example.com<br>
          Password: password<br>
          Role: customer
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/api.js';

export default {
  name: 'Login',
  data() {
    return {
      form: {
        email: 'demo@example.com',
        password: 'password'
      },
      loading: false,
      error: ''
    };
  },
  methods: {
    async login() {
      this.loading = true;
      this.error = '';

      try {
        const response = await api.post('/auth/login', {
          email: this.form.email,
          password: this.form.password
        });

        const { token, user } = response.data;

        // Store auth data
        localStorage.setItem('token', token);
        localStorage.setItem('role', user.role);
        localStorage.setItem('userName', user.name);
        localStorage.setItem('userEmail', user.email);

        this.$router.push('/dashboard');
      } catch (error) {
        this.error = error.response?.data?.message || 'Đăng nhập thất bại!';
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
  border-bottom: 2px solid #0d6efd;
}

.btn-primary {
  padding: 10px 20px;
  font-weight: bold;
}

.text-primary {
  text-decoration: none;
  font-weight: bold;
}

.text-primary:hover {
  text-decoration: underline;
}

.alert {
  background-color: #e7f3ff;
  border: 1px solid #b3d9ff;
  border-radius: 5px;
}
</style>
