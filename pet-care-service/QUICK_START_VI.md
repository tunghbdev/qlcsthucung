# Hướng Dẫn Khởi Động Nhanh - Pet Care Service

## 🚀 Khởi Động Nhanh

### Yêu Cầu Tiên Quyết
- PHP 8.1+
- Node.js 16+
- Composer
- MySQL (chạy trên XAMPP)

### Bước 1: Setup Lần Đầu

Nếu lần đầu tiên setup dự án, chạy script setup:

```bash
# macOS/Linux
bash setup.sh

# hoặc chạy thủ công từng bước
composer install
npm install
php artisan key:generate
php artisan migrate
```

### Bước 2: Tạo Database

1. Mở XAMPP Control Panel
2. Khởi động MySQL
3. Mở phpMyAdmin (http://localhost/phpmyadmin)
4. Tạo database mới: `pet_care_service`

### Bước 3: Cấu Hình .env

Mở file `.env` và cập nhật:

```
APP_NAME="Pet Care Service"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pet_care_service
DB_USERNAME=root
DB_PASSWORD=
```

### Bước 4: Chạy Ứng Dụng

**Terminal 1 - Build Vue assets**
```bash
npm run dev
```

**Terminal 2 - Chạy Laravel server**
```bash
php artisan serve
```

Mở trình duyệt và vào: **http://localhost:8000**

## 📱 Tài Khoản Test

### Admin
```
Email: admin@example.com
Password: password
```

### Khách Hàng
```
Email: demo@example.com
Password: password
```

### Nhân Viên
```
Email: staff@example.com
Password: password
```

## 🗂️ Cấu Trúc Thư Mục

```
resources/js/
├── App.vue              # Root component
├── app.js               # Entry point
├── router.js            # Route definitions
├── components/
│   └── SidebarNav.vue   # Navigation sidebar
├── pages/
│   ├── Home.vue
│   ├── Auth/
│   │   ├── Login.vue
│   │   └── Register.vue
│   ├── Dashboard/
│   │   └── Dashboard.vue
│   ├── Admin/           # Admin pages
│   ├── Customer/        # Customer pages
│   └── Staff/           # Staff pages
└── layouts/             # Layout components
```

## 🛠️ Các Lệnh Hữu Ích

### Development
```bash
npm run dev              # Build assets (dev mode)
npm run build            # Build assets (production)
php artisan serve        # Run Laravel server
php artisan tinker       # Laravel REPL
```

### Database
```bash
php artisan migrate              # Run migrations
php artisan migrate:rollback     # Rollback migrations
php artisan migrate:reset        # Reset all migrations
php artisan db:seed              # Seed database
php artisan make:migration <name> # Create new migration
```

### Code Generation
```bash
php artisan make:model <name>           # Create model
php artisan make:controller <name>      # Create controller
php artisan make:migration <name>       # Create migration
php artisan make:seeder <name>          # Create seeder
```

## 📝 Chức Năng Chính (Hiện Có)

### ✅ Hoàn Thành
- [x] Giao diện Home
- [x] Hệ thống đăng nhập/đăng ký
- [x] Dashboard cho các vai trò
- [x] Admin: Quản lý dịch vụ
- [x] Admin: Quản lý lịch hẹn
- [x] Admin: Quản lý hóa đơn
- [x] Admin: Quản lý nhân viên
- [x] Admin: Quản lý khách hàng
- [x] Admin: Báo cáo thống kê
- [x] Customer: Đặt lịch dịch vụ
- [x] Customer: Quản lý thú cưng
- [x] Customer: Xem hóa đơn
- [x] Customer: Thông tin cá nhân
- [x] Staff: Xem lịch của mình
- [x] Staff: Thông tin cá nhân

### 🔄 Tiếp Theo (Database & API)
- [ ] Database schema & migrations
- [ ] API endpoints (RESTful)
- [ ] Authentication (Laravel Sanctum)
- [ ] Email notifications
- [ ] Payment integration
- [ ] File uploads
- [ ] Unit & Feature tests

## 🎨 Thiết Kế & Framework

- **UI Framework**: Bootstrap 5
- **Icons**: Bootstrap Icons
- **Colors**: Primary (#0d6efd), Success (#198754), Warning (#ffc107)
- **Responsive**: Mobile-first design

## 🔒 Bảo Mật (Cần Triển Khai)

- Xác thực API (JWT/Sanctum)
- Middleware cho phân quyền
- CSRF protection
- Rate limiting
- Input validation
- SQL injection prevention

## 📞 Hỗ Trợ

Nếu gặp vấn đề:

1. Kiểm tra lại PHP version: `php -v`
2. Kiểm tra Node version: `node -v`
3. Đảm bảo MySQL đang chạy
4. Xóa cache: `php artisan cache:clear`
5. Xóa compiled views: `php artisan view:clear`

## 📚 Tài Liệu

- [Laravel Docs](https://laravel.com/docs)
- [Vue.js 3 Docs](https://vuejs.org/)
- [Bootstrap 5 Docs](https://getbootstrap.com/docs/5.0/)
- [Vue Router Docs](https://router.vuejs.org/)

---

**Version**: 1.0.0  
**Last Updated**: 20 tháng 4, 2026
