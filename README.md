# Website Đặt Vé Xem Phim CGV

Đây là website đặt vé xem phim hoàn chỉnh với giao diện giống CGV, có đầy đủ chức năng cho cả người dùng và admin.

## 🚀 Hướng Dẫn Cài Đặt

### Bước 1: Chuẩn Bị Môi Trường

- Cài đặt XAMPP (hoặc WAMP/LAMP)
- Khởi động Apache và MySQL
- Đảm bảo PHP và MySQL hoạt động bình thường

### Bước 2: Setup Database

1. Mở phpMyAdmin trong trình duyệt: `http://localhost/phpmyadmin`
2. Tạo database mới có tên `phimchill`
3. Import file `database/data_phimchill.sql` vào database
4. Kiểm tra các bảng đã được tạo thành công

### Bước 3: Cấu Hình Kết Nối

1. Mở file `admin/config/config.php`
2. Kiểm tra thông tin kết nối database:
   ```php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "phimchill";
   ```

### Bước 4: Chạy Website

1. Truy cập: `http://localhost/BookingsTickets`
2. Website sẽ hiển thị trang chủ với danh sách phim

## 👤 Tài Khoản Mặc Định

### Admin:

- Email: `admin@cgv.com`
- Password: `admin123`

### User:

- Email: `user@test.com`
- Password: `123456`
- Email: `hungphi@test.com`
- Password: `123456`

## 🎬 Chức Năng Website

### Cho Người Dùng:

- ✅ Xem danh sách phim đang chiếu
- ✅ Xem thông tin chi tiết phim
- ✅ Xem danh sách rạp và lịch chiếu
- ✅ Đặt vé online với chọn ghế
- ✅ Xem lịch sử đặt vé
- ✅ Hủy vé đã đặt
- ✅ Thanh toán online
- ✅ Đăng ký/Đăng nhập

### Cho Admin:

- ✅ Quản lý phim (thêm/sửa/xóa)
- ✅ Quản lý rạp chiếu
- ✅ Quản lý lịch chiếu
- ✅ Quản lý đặt vé
- ✅ Xem thống kê doanh thu
- ✅ Quản lý người dùng

## 🎯 Cách Sử Dụng

### Đặt Vé:

1. **Từ Trang Chủ**: Click "Đặt vé" trên poster phim
2. **Từ Trang Phim**: Click "Đặt vé" trên phim muốn xem
3. **Từ Trang Rạp**: Xem lịch chiếu → Click giờ chiếu muốn đặt

### Flow Đặt Vé:

1. Chọn phim → Hiển thị lịch chiếu
2. Chọn suất chiếu → Chuyển đến trang đặt vé
3. Chọn ghế → Xem tóm tắt đặt vé
4. Thanh toán → Hoàn tất đặt vé

## 🛠️ Cấu Trúc Thư Mục

```
BookingsTickets/
├── admin/              # Trang quản trị
├── css/               # File CSS
├── js/                # File JavaScript
├── img/               # Hình ảnh
├── pages/             # Các trang website
│   ├── actions/       # File xử lý form
│   ├── layout/        # Layout components
│   └── pages/         # Các trang chính
├── database/          # File SQL
└── index.php          # Trang chủ
```

## 🔧 Xử Lý Lỗi

### Lỗi "Không có lịch chiếu":

1. Kiểm tra database đã import chưa
2. Kiểm tra dữ liệu trong bảng `showtimes`
3. Kiểm tra kết nối database

### Lỗi không đặt được vé:

1. Đảm bảo đã đăng nhập
2. Kiểm tra session PHP
3. Kiểm tra dữ liệu bảng `users`

### Lỗi hiển thị ảnh:

1. Kiểm tra thư mục `img/Phim/`
2. Đảm bảo tên file ảnh đúng với database

## 🎨 Tùy Chỉnh

### Thay đổi màu sắc:

- Chỉnh sửa file `css/style.css`
- Màu chủ đạo CGV: `#e50914`

### Thêm phim mới:

1. Thêm ảnh vào `img/Phim/`
2. Thêm dữ liệu vào bảng `movies`
3. Tạo lịch chiếu trong bảng `showtimes`

## 📞 Hỗ Trợ

Nếu gặp vấn đề, hãy kiểm tra:

1. XAMPP đã khởi động chưa
2. Database đã import chưa
3. Đường dẫn file có đúng không
4. PHP error logs

Website được thiết kế responsive, hoạt động tốt trên desktop và mobile!
