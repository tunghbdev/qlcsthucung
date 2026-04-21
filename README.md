# PET CARE SERVICE MANAGEMENT SYSTEM

## 1. Giới thiệu hệ thống

Hệ thống quản lý dịch vụ chăm sóc thú cưng được xây dựng nhằm hỗ trợ các nghiệp vụ:

* Quản lý khách hàng
* Quản lý thú cưng
* Quản lý dịch vụ
* Đặt lịch chăm sóc (Appointment)
* Quản lý nhân viên
* Thanh toán và hóa đơn
* Đánh giá dịch vụ

Hệ thống được xây dựng dưới dạng **RESTful API sử dụng Laravel**, phục vụ cho việc tích hợp với frontend hoặc mobile app.

---

## 2. Công nghệ sử dụng

* Backend: Laravel (PHP)
* Cơ sở dữ liệu: MySQL
* ORM: Eloquent ORM
* API: RESTful API
* Công cụ: Composer, Artisan CLI

---

## 3. Kiến trúc hệ thống

Hệ thống sử dụng mô hình:

### MVC (Model - Controller - Routing)

* **Model (app/Models)**: Đại diện cho bảng trong database
* **Controller (app/Http/Controllers/Api)**: Xử lý logic và API
* **Routes (routes/api.php)**: Định nghĩa endpoint API

---

## 4. Cấu trúc thư mục 

```id="tree1"
pet-care-service/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           ├── AppointmentController.php
│   │           ├── AuthController.php
│   │           ├── PetController.php
│   │           └── ServiceController.php
│   │
│   ├── Models/
│   │   ├── Appointment.php
│   │   ├── Customer.php
│   │   ├── Invoice.php
│   │   ├── Payment.php
│   │   ├── Pet.php
│   │   ├── Review.php
│   │   ├── Service.php
│   │   ├── Staff.php
│   │   ├── StaffService.php
│   │   └── User.php
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── schema.sql
│
├── routes/
│   ├── api.php
│   ├── web.php
│
├── resources/views/
├── config/
├── storage/
├── tests/
├── artisan
├── composer.json
```

---

## 5. PHÂN TÍCH CHI TIẾT MODEL 

Hệ thống sử dụng **Eloquent ORM** để ánh xạ mỗi Model với một bảng trong database.

---

### 5.1 Model: User

#### Vai trò

* Đại diện cho tài khoản đăng nhập hệ thống
* Dùng cho xác thực (Authentication)

#### Chức năng

* Đăng ký
* Đăng nhập (AuthController xử lý)
* Phân quyền (có thể mở rộng)

---

### 5.2 Model: Customer

#### Vai trò

* Lưu thông tin khách hàng

#### Quan hệ

* 1 Customer có nhiều Pet
* 1 Customer có nhiều Appointment
* 1 Customer có nhiều Review

#### Ý nghĩa

Là thực thể trung tâm đại diện người sử dụng dịch vụ.

---

### 5.3 Model: Pet

#### Vai trò

* Lưu thông tin thú cưng:

  * Tên
  * Loại
  * Tuổi
  * Giống

#### Quan hệ

* Thuộc về 1 Customer (belongsTo)
* Có nhiều Appointment

#### Cách sử dụng

* Khi khách hàng đặt lịch, phải chọn Pet

---

### 5.4 Model: Service

#### Vai trò

* Danh sách dịch vụ:

  * Tắm
  * Cắt tỉa
  * Khám bệnh

#### Quan hệ

* Many-to-many với Staff (thông qua StaffService)
* Có nhiều Appointment
* Có nhiều Review

---

### 5.5 Model: Staff

#### Vai trò

* Nhân viên cung cấp dịch vụ

#### Quan hệ

* Many-to-many với Service
* Có nhiều Appointment

#### Ý nghĩa

Cho phép phân công nhân viên phù hợp với từng dịch vụ

---

### 5.6 Model: StaffService 

#### Vai trò

* Bảng trung gian giữa:

  * Staff và Service

#### Ý nghĩa

* Một nhân viên có thể làm nhiều dịch vụ
* Một dịch vụ có nhiều nhân viên

---

### 5.7 Model: Appointment

#### Vai trò

* Quản lý lịch đặt dịch vụ

#### Đây là MODEL QUAN TRỌNG NHẤT

#### Quan hệ

* belongsTo Customer
* belongsTo Pet
* belongsTo Service
* belongsTo Staff
* hasOne Invoice

#### Chức năng

* Tạo lịch hẹn
* Quản lý trạng thái (pending, completed,...)

---

### 5.8 Model: Invoice

#### Vai trò

* Hóa đơn cho mỗi lịch hẹn

#### Quan hệ

* belongsTo Appointment
* hasOne Payment

---

### 5.9 Model: Payment

#### Vai trò

* Lưu thông tin thanh toán

#### Quan hệ

* belongsTo Invoice

#### Chức năng

* Ghi nhận trạng thái thanh toán
* Phương thức thanh toán

---

### 5.10 Model: Review

#### Vai trò

* Đánh giá dịch vụ

#### Quan hệ

* belongsTo Customer
* belongsTo Service

#### Chức năng

* Lưu rating và comment

---

## 6. LUỒNG HOẠT ĐỘNG HỆ THỐNG

1. User đăng ký / đăng nhập
2. Tạo Customer
3. Thêm Pet
4. Chọn Service
5. Tạo Appointment
6. Gán Staff
7. Tạo Invoice
8. Thanh toán (Payment)
9. Đánh giá (Review)

---

## 7. API CONTROLLER

### AuthController

* Đăng ký
* Đăng nhập

### PetController

* CRUD Pet

### ServiceController

* Lấy danh sách dịch vụ

### AppointmentController

* Tạo lịch
* Xem lịch
* Cập nhật trạng thái

---

## 8. DATABASE

* Sử dụng migrations trong:

```
database/migrations/
```

* Hoặc import:

```
database/schema.sql
```

---

## 9. CÀI ĐẶT

### 1. Clone project

```bash
git clone <repo>
cd pet-care-service
```

### 2. Cài đặt

```bash
composer install
cp .env.example .env
php artisan key:generate
```

### 3. Cấu hình database

```
DB_DATABASE=petcare
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Chạy migration

```bash
php artisan migrate
```

### 5. Chạy server

```bash
php artisan serve
```

---

## 10. KẾT LUẬN

Hệ thống đã xây dựng đầy đủ các thành phần:

* Quản lý dữ liệu bằng Model
* Xử lý logic bằng Controller
* API rõ ràng, dễ mở rộng

Đặc biệt, mô hình dữ liệu được thiết kế theo quan hệ chặt chẽ, giúp hệ thống dễ dàng mở rộng trong tương lai.
