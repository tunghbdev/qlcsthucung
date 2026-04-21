<template>
  <div class="home-page">
    <!-- Hero Section -->
    <div class="hero-section">
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <h1 class="hero-title">🐕 Pet Care Service 🐈</h1>
        <p class="hero-subtitle">Dịch vụ chăm sóc thú cưng hàng đầu - Yêu thương & Chuyên nghiệp</p>
        <div class="hero-buttons mt-4">
          <router-link v-if="!isAuthenticated" to="/login" class="btn btn-light btn-lg me-3">
            <i class="bi bi-box-arrow-in-right"></i> Đăng Nhập
          </router-link>
          <router-link v-if="!isAuthenticated" to="/register" class="btn btn-warning btn-lg">
            <i class="bi bi-person-plus"></i> Đăng Ký
          </router-link>
          <router-link v-else to="/dashboard" class="btn btn-light btn-lg">
            <i class="bi bi-speedometer2"></i> Dashboard
          </router-link>
        </div>
      </div>
    </div>

    <!-- Services Section -->
    <div class="container mt-5">
      <div class="section-title">
        <h2>Các Dịch Vụ Của Chúng Tôi</h2>
        <p class="text-muted">Chọn dịch vụ mong muốn để bắt đầu</p>
      </div>

      <div class="row mt-4">
        <div class="col-md-4 mb-4">
          <div class="service-card" @click="handleServiceClick" :class="{ 'cursor-pointer': !isAuthenticated }">
            <div class="service-icon">
              <i class="bi bi-droplet-fill"></i>
            </div>
            <h5 class="service-title">Tắm</h5>
            <p class="service-text">Dịch vụ tắm chuyên nghiệp cho thú cưng của bạn</p>
            <div class="service-price">Từ 150.000 VNĐ</div>
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <div class="service-card" @click="handleServiceClick" :class="{ 'cursor-pointer': !isAuthenticated }">
            <div class="service-icon">
              <i class="bi bi-scissors"></i>
            </div>
            <h5 class="service-title">Cắt Tỉa Lông</h5>
            <p class="service-text">Cắt lông định hình theo ý muốn của chủ nhân</p>
            <div class="service-price">Từ 200.000 VNĐ</div>
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <div class="service-card" @click="handleServiceClick" :class="{ 'cursor-pointer': !isAuthenticated }">
            <div class="service-icon">
              <i class="bi bi-bandage-fill"></i>
            </div>
            <h5 class="service-title">Tiêm Phòng</h5>
            <p class="service-text">Tiêm phòng và chăm sóc sức khỏe toàn diện</p>
            <div class="service-price">Từ 300.000 VNĐ</div>
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <div class="service-card" @click="handleServiceClick" :class="{ 'cursor-pointer': !isAuthenticated }">
            <div class="service-icon">
              <i class="bi bi-person-badge"></i>
            </div>
            <h5 class="service-title">Huấn Luyện</h5>
            <p class="service-text">Huấn luyện thú cưng chuyên nghiệp và hiệu quả</p>
            <div class="service-price">Từ 250.000 VNĐ</div>
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <div class="service-card" @click="handleServiceClick" :class="{ 'cursor-pointer': !isAuthenticated }">
            <div class="service-icon">
              <i class="bi bi-house-fill"></i>
            </div>
            <h5 class="service-title">Chăm Sóc Hộ</h5>
            <p class="service-text">Dịch vụ chăm sóc thú cưng tại nhà của bạn</p>
            <div class="service-price">Từ 100.000 VNĐ</div>
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <div class="service-card info-card">
            <div class="service-icon">
              <i class="bi bi-star-fill"></i>
            </div>
            <h5 class="service-title">Chất Lượng Cao</h5>
            <p class="service-text">Dịch vụ chất lượng cao với giá cạnh tranh nhất</p>
            <div class="service-rating">⭐⭐⭐⭐⭐</div>
          </div>
        </div>
      </div>

      <!-- Info Section -->
      <div class="row mt-5">
        <div class="col-md-3">
          <div class="info-box">
            <h3><i class="bi bi-check-circle"></i> 100%</h3>
            <p>Đảm bảo chất lượng</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="info-box">
            <h3><i class="bi bi-people"></i> 500+</h3>
            <p>Khách hàng hài lòng</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="info-box">
            <h3><i class="bi bi-calendar-check"></i> 24/7</h3>
            <p>Hỗ trợ khách hàng</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="info-box">
            <h3><i class="bi bi-award"></i> 10+</h3>
            <p>Năm kinh nghiệm</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Login Modal Alert -->
    <div v-if="showLoginAlert" class="login-alert">
      <div class="alert-content">
        <h5><i class="bi bi-info-circle"></i> Yêu cầu đăng nhập</h5>
        <p>Vui lòng đăng nhập để sử dụng dịch vụ</p>
        <div class="alert-buttons">
          <router-link to="/login" class="btn btn-primary btn-sm">Đăng Nhập</router-link>
          <button @click="showLoginAlert = false" class="btn btn-secondary btn-sm">Đóng</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Home',
  data() {
    return {
      isAuthenticated: false,
      showLoginAlert: false
    };
  },
  mounted() {
    this.isAuthenticated = !!localStorage.getItem('token');
  },
  methods: {
    handleServiceClick() {
      if (!this.isAuthenticated) {
        this.showLoginAlert = true;
      }
    }
  }
};
</script>

<style scoped>
.home-page {
  background-color: #f8f9fa;
  min-height: 100vh;
}

/* Hero Section */
.hero-section {
  position: relative;
  height: 500px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  margin-bottom: 50px;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="1200" height="600" fill="rgba(0,0,0,0.2)"/><rect width="1200" height="600" fill="url(%23grid)"/></svg>');
  opacity: 0.5;
}

.hero-content {
  position: relative;
  z-index: 1;
  text-align: center;
  animation: slideInDown 0.8s ease-out;
}

.hero-title {
  font-size: 48px;
  font-weight: bold;
  margin-bottom: 15px;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

.hero-subtitle {
  font-size: 24px;
  margin-bottom: 20px;
  opacity: 0.95;
}

.hero-buttons {
  display: flex;
  justify-content: center;
  gap: 15px;
}

.hero-buttons .btn {
  font-weight: 600;
  padding: 12px 30px;
  border-radius: 50px;
  transition: all 0.3s ease;
}

.hero-buttons .btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

/* Section Title */
.section-title {
  text-align: center;
  margin-bottom: 40px;
}

.section-title h2 {
  font-size: 36px;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 10px;
}

.section-title .text-muted {
  font-size: 16px;
}

/* Service Cards */
.service-card {
  background: white;
  border-radius: 15px;
  padding: 30px;
  text-align: center;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  height: 100%;
  border: 2px solid transparent;
}

.service-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.cursor-pointer {
  cursor: pointer;
}

.cursor-pointer:hover {
  border-color: #667eea;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
}

.service-icon {
  font-size: 48px;
  color: #667eea;
  margin-bottom: 15px;
}

.service-title {
  font-size: 20px;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 10px;
}

.service-text {
  color: #7f8c8d;
  font-size: 14px;
  margin-bottom: 15px;
  line-height: 1.6;
}

.service-price {
  color: #667eea;
  font-weight: bold;
  font-size: 16px;
}

.service-rating {
  font-size: 18px;
  margin-top: 10px;
}

.info-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
}

.info-card .service-icon {
  color: #fff;
}

.info-card .service-title {
  color: white;
}

.info-card .service-text {
  color: rgba(255, 255, 255, 0.9);
}

.info-card .service-rating {
  color: #ffc107;
}

/* Info Section */
.info-box {
  background: white;
  padding: 30px;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.info-box:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.info-box h3 {
  color: #667eea;
  font-size: 32px;
  font-weight: bold;
  margin-bottom: 10px;
}

.info-box i {
  margin-right: 8px;
}

.info-box p {
  color: #7f8c8d;
  font-size: 14px;
}

/* Login Alert */
.login-alert {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 1050;
  animation: popIn 0.3s ease-out;
}

.login-alert::before {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: -1;
}

.alert-content {
  background: white;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  min-width: 350px;
}

.alert-content h5 {
  color: #2c3e50;
  margin-bottom: 10px;
  font-weight: bold;
}

.alert-content p {
  color: #7f8c8d;
  margin-bottom: 20px;
}

.alert-buttons {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.alert-buttons .btn {
  min-width: 100px;
}

/* Animations */
@keyframes slideInDown {
  from {
    opacity: 0;
    transform: translateY(-30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes popIn {
  from {
    opacity: 0;
    transform: translate(-50%, -50%) scale(0.8);
  }
  to {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }
}

/* Responsive */
@media (max-width: 768px) {
  .hero-title {
    font-size: 32px;
  }

  .hero-subtitle {
    font-size: 18px;
  }

  .hero-buttons {
    flex-direction: column;
  }

  .hero-buttons .btn {
    width: 100%;
  }

  .section-title h2 {
    font-size: 28px;
  }

  .service-card {
    padding: 20px;
  }

  .alert-content {
    min-width: 280px;
  }
}
</style>

