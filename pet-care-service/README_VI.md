# Pet Care Service - Website Quản Lý Dịch Vụ Chăm Sóc Thú Cưng

Website quản lý dịch vụ chăm sóc thú cưng được xây dựng với Laravel 10 và Vue.js 3, hỗ trợ 3 vai trò: Admin, Khách Hàng, và Nhân Viên.

## Công Nghệ Sử Dụng

- **Backend**: Laravel 10
- **Frontend**: Vue.js 3
- **Database**: MySQL (XAMPP)
- **UI Framework**: Bootstrap 5
- **Build Tool**: Vite
- **HTTP Client**: Axios

## Tính Năng

### Dịch Vụ Được Hỗ Trợ
1. Tắm chuyên nghiệp
2. Cắt tỉa lông
3. Tiêm phòng
4. Huấn luyện thú cưng
5. Chăm sóc hộ

### Vai Trò Người Dùng

#### Admin (Quản Trị Viên)
- Dashboard với thống kê tổng quát
- Quản lý dịch vụ (thêm, sửa, xóa)
- Quản lý lịch hẹn toàn bộ
- Quản lý hóa đơn và thanh toán
- Quản lý nhân viên
- Quản lý khách hàng
- Báo cáo và thống kê

#### Customer (Khách Hàng)
- Đăng ký và đăng nhập tài khoản
- Quản lý thông tin cá nhân
- Quản lý thú cưng (thêm, sửa, xóa, xem lịch sử)
- Đặt lịch dịch vụ
- Xem lịch sử lịch hẹn
- Quản lý hóa đơn và thanh toán
- Xem danh sách dịch vụ

#### Staff (Nhân Viên)
- Đăng ký (cần mã nhân viên)
- Xem danh sách lịch hẹn của mình
- Cập nhật trạng thái lịch hẹn
- Xem thông tin cá nhân
- Quản lý profil

## Cài Đặt

### Yêu Cầu
- PHP 8.1+
- Composer
- Node.js 16+
- npm
- MySQL 5.7+

### Bước Cài Đặt

1. **Clone hoặc tải dự án**
   ```bash
   cd pet-care-service
   ```

2. **Cài đặt dependencies PHP**
   ```bash
   composer install
   ```

3. **Cài đặt dependencies Node.js**
   ```bash
   npm install
   ```

4. **Sao chép file .env**
   ```bash
   cp .env.example .env
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Cấu hình Database**
   
   Mở file `.env` và cấu hình:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=pet_care_service
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. **Tạo Database**
   ```bash
   mysql -u root -p
   CREATE DATABASE pet_care_service;
   EXIT;
   ```

8. **Chạy migrations**
   ```bash
   php artisan migrate
   ```

9. **Build assets**
   ```bash
   npm run dev
   ```

10. **Khởi động server**
    ```bash
    php artisan serve
    ```

    Ứng dụng sẽ chạy tại: `http://localhost:8000`

## Sử Dụng

### Tài Khoản Demo

#### Admin
- **Email**: admin@example.com
- **Password**: password
- **Role**: admin

#### Customer
- **Email**: demo@example.com
- **Password**: password
- **Role**: customer

#### Staff
- **Email**: staff@example.com
- **Password**: password
- **Role**: staff

## Cấu Trúc Dự Án

```
pet-care-service/
├── app/                      # Thư mục ứng dụng Laravel
│   ├── Http/
│   │   └── Controllers/     # API Controllers
│   ├── Models/              # Database Models
│   └── ...
├── resources/
│   ├── css/
│   │   └── app.css         # Styling
│   ├── js/
│   │   ├── app.js          # Vue app entry point
│   │   ├── App.vue         # Root component
│   │   ├── router.js       # Vue Router
│   │   ├── components/     # Reusable components
│   │   ├── pages/          # Page components
│   │   │   ├── Home.vue
│   │   │   ├── Auth/
│   │   │   ├── Admin/
│   │   │   ├── Customer/
│   │   │   └── Staff/
│   │   └── layouts/        # Layout components
│   └── views/
│       └── app.blade.php   # Main blade template
├── routes/
│   ├── web.php            # Web routes
│   └── api.php            # API routes
├── database/
│   └── migrations/        # Database migrations
├── public/                # Public assets
├── storage/               # Storage (logs, cache)
├── tests/                 # Unit & Feature tests
├── .env                   # Environment configuration
├── package.json           # Node dependencies
├── composer.json          # PHP dependencies
└── vite.config.js         # Vite configuration
```

## Các Lệnh Hữu Ích

### Phát Triển
```bash
# Build assets (development)
npm run dev

# Build assets (production)
npm run build

# Khởi động server Laravel
php artisan serve

# Chạy migrations
php artisan migrate

# Reset database
php artisan migrate:reset

# Seed database
php artisan db:seed
```

## Hướng Phát Triển Tiếp Theo

1. **Database Schema**
   - Tạo migrations cho users, pets, services, appointments, invoices, etc.

2. **API Backend**
   - Controllers cho các resources
   - Middleware cho xác thực
   - Form requests cho validation

3. **Authentication**
   - Laravel Sanctum cho token-based auth
   - JWT tokens

4. **Notifications**
   - Email notifications
   - SMS notifications
   - In-app notifications

5. **Payment Gateway**
   - Stripe/VNPay integration

6. **File Upload**
   - Hình ảnh cho pets và services
   - Avatar cho users

7. **Testing**
   - Unit tests
   - Feature tests
   - API tests

8. **Deployment**
   - Deploy to production server
   - Setup CI/CD pipeline

## Lưu Ý Quan Trọng

- Các dữ liệu hiện tại là **demo data** được hardcode trong components Vue
- Cần tạo **database migrations** để lưu trữ dữ liệu thực tế
- Cần triển khai **API endpoints** trong Laravel backend
- Cần tích hợp **xác thực** (Authentication) thực sự
- Cần thêm **validation** từ phía server

## Liên Hệ & Hỗ Trợ

Nếu có bất kỳ câu hỏi hoặc vấn đề, vui lòng liên hệ admin.

---

**Created**: 20 tháng 4, 2026
**Version**: 1.0.0
**Status**: Development
