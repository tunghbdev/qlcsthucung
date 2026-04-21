# PET CARE SERVICE MANAGEMENT SYSTEM

## 1. Giới thiệu

Dự án là hệ thống quản lý dịch vụ chăm sóc thú cưng được xây dựng nhằm hỗ trợ các hoạt động như:

* Quản lý khách hàng
* Quản lý thú cưng
* Đặt lịch dịch vụ
* Quản lý nhân viên
* Thanh toán và hóa đơn
* Đánh giá dịch vụ

Hệ thống được phát triển theo mô hình Web Application với backend sử dụng Laravel và frontend sử dụng VueJS.

---

## 2. Công nghệ sử dụng

### Backend

* Laravel Framework (PHP)
* RESTful API
* MySQL

### Frontend

* VueJS
* Vite
* Axios (gọi API)

### Khác

* Composer
* NodeJS & NPM
* MVC Architecture

---

## 3. Kiến trúc hệ thống

Dự án được xây dựng theo mô hình:

### MVC (Model - View - Controller)

* Model: Đại diện cho dữ liệu và xử lý logic liên quan đến database
* Controller: Xử lý request từ client, gọi Model và trả về response
* View: Giao diện người dùng (VueJS)

Ngoài ra, hệ thống sử dụng API để giao tiếp giữa frontend và backend.

---

## 4. Cấu trúc thư mục chính

```
pet-care-service/
│
├── app/
│   ├── Models/
│   ├── Http/Controllers/Api/
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── schema.sql
│
├── resources/
│   ├── js/ (VueJS)
│   ├── views/
│
├── routes/
│   ├── api.php
│   ├── web.php
│
├── public/
├── config/
```

---

## 5. Phân tích chi tiết các Model

Hệ thống sử dụng Eloquent ORM của Laravel để quản lý dữ liệu.

### 5.1 User

* Đại diện tài khoản đăng nhập hệ thống
* Dùng cho authentication
* Có thể là admin, staff hoặc customer

---

### 5.2 Customer

* Lưu thông tin khách hàng
* Quan hệ:

  * 1 Customer có nhiều Pet
  * 1 Customer có nhiều Appointment

---

### 5.3 Pet

* Lưu thông tin thú cưng (tên, loại, tuổi,...)
* Quan hệ:

  * Thuộc về 1 Customer
  * Có nhiều Appointment

---

### 5.4 Service

* Danh sách các dịch vụ chăm sóc thú cưng

  * Tắm
  * Cắt tỉa
  * Khám bệnh
* Quan hệ:

  * Nhiều Service có thể được thực hiện bởi nhiều Staff

---

### 5.5 Staff

* Nhân viên cung cấp dịch vụ
* Quan hệ:

  * Nhiều Staff phục vụ nhiều Service (many-to-many)
  * Có nhiều Appointment

---

### 5.6 StaffService

* Bảng trung gian (pivot table)
* Dùng để liên kết:

  * Staff ↔ Service

---

### 5.7 Appointment

* Lịch đặt dịch vụ
* Là bảng trung tâm của hệ thống
* Quan hệ:

  * Thuộc về Customer
  * Thuộc về Pet
  * Thuộc về Staff
  * Thuộc về Service

---

### 5.8 Invoice

* Hóa đơn thanh toán
* Quan hệ:

  * 1 Appointment có 1 Invoice

---

### 5.9 Payment

* Thông tin thanh toán
* Quan hệ:

  * Gắn với Invoice

---

### 5.10 Review

* Đánh giá dịch vụ
* Quan hệ:

  * Customer đánh giá Service

---

## 6. Luồng hoạt động hệ thống

1. Người dùng đăng ký / đăng nhập
2. Khách hàng thêm thú cưng
3. Khách hàng chọn dịch vụ
4. Đặt lịch (Appointment)
5. Nhân viên xử lý dịch vụ
6. Hệ thống tạo hóa đơn (Invoice)
7. Thanh toán (Payment)
8. Khách hàng đánh giá (Review)

---

## 7. API chính

Các API được định nghĩa trong:

```
routes/api.php
```

Bao gồm:

* Authentication API
* Pet API
* Service API
* Appointment API

Controller xử lý tại:

```
app/Http/Controllers/Api/
```

---

## 8. Cài đặt và chạy dự án

### Bước 1: Clone project

```bash
git clone <repository-url>
cd pet-care-service
```

---

### Bước 2: Cài đặt backend

```bash
composer install
cp .env.example .env
php artisan key:generate
```

---

### Bước 3: Cấu hình database

Sửa file `.env`:

```
DB_DATABASE=petcare
DB_USERNAME=root
DB_PASSWORD=
```

---

### Bước 4: Migration database

```bash
php artisan migrate
```

Hoặc import file:

```
database/schema.sql
```

---

### Bước 5: Chạy backend

```bash
php artisan serve
```

---

### Bước 6: Cài đặt frontend

```bash
npm install
npm run dev
```

---

## 9. Chức năng chính

### Quản lý khách hàng

* Thêm, sửa, xóa
* Xem thông tin

### Quản lý thú cưng

* Thêm thú cưng
* Cập nhật thông tin

### Quản lý dịch vụ

* Danh sách dịch vụ
* Giá dịch vụ

### Đặt lịch

* Tạo lịch hẹn
* Phân công nhân viên

### Thanh toán

* Tạo hóa đơn
* Xử lý thanh toán

### Đánh giá

* Khách hàng đánh giá dịch vụ

---

## 10. Hướng phát triển

* Thêm phân quyền chi tiết (Admin / Staff / Customer)
* Tích hợp thanh toán online
* Gửi email thông báo
* Dashboard thống kê nâng cao

---

## 11. Kết luận

Dự án xây dựng hệ thống quản lý dịch vụ thú cưng đầy đủ chức năng cơ bản, áp dụng mô hình MVC và RESTful API, giúp dễ dàng mở rộng và phát triển trong tương lai.
