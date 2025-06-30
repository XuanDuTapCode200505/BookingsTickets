-- Tắt foreign key checks để import được
SET FOREIGN_KEY_CHECKS = 0;

-- Tạo database
CREATE DATABASE IF NOT EXISTS phimchill;
USE phimchill;

-- Xóa các bảng cũ nếu có (theo thứ tự đúng)
DROP TABLE IF EXISTS booking_seats;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS seats;
DROP TABLE IF EXISTS showtimes;
DROP TABLE IF EXISTS screens;
DROP TABLE IF EXISTS theaters;
DROP TABLE IF EXISTS cities;
DROP TABLE IF EXISTS movies;
DROP TABLE IF EXISTS users;

-- Bảng users (người dùng)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'blocked', 'deleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng cities (thành phố)
CREATE TABLE cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng theaters (rạp chiếu)
CREATE TABLE theaters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(255) NOT NULL,
    city_id INT,
    phone VARCHAR(20),
    status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active',
    total_screens INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE SET NULL
);

-- Bảng movies (phim)
CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    duration INT NOT NULL,
    genre VARCHAR(100),
    release_date DATE,
    poster_url VARCHAR(255),
    status ENUM('showing', 'coming_soon', 'ended') DEFAULT 'showing',
    rating DECIMAL(3,1) DEFAULT 0.0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng screens (phòng chiếu)
CREATE TABLE screens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    theater_id INT NOT NULL,
    screen_name VARCHAR(50) NOT NULL,
    total_seats INT DEFAULT 100,
    FOREIGN KEY (theater_id) REFERENCES theaters(id) ON DELETE CASCADE
);

-- Bảng showtimes (lịch chiếu)
CREATE TABLE showtimes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    screen_id INT NOT NULL,
    show_date DATE NOT NULL,
    show_time TIME NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    available_seats INT DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (screen_id) REFERENCES screens(id) ON DELETE CASCADE
);

-- Bảng seats (ghế ngồi)
CREATE TABLE seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    screen_id INT NOT NULL,
    seat_row VARCHAR(5) NOT NULL,
    seat_number INT NOT NULL,
    seat_type ENUM('standard', 'vip', 'couple') DEFAULT 'standard',
    FOREIGN KEY (screen_id) REFERENCES screens(id) ON DELETE CASCADE,
    UNIQUE KEY unique_seat (screen_id, seat_row, seat_number)
);

-- Bảng bookings (đặt vé)
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    showtime_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    booking_status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
    booking_code VARCHAR(20) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE
);

-- Bảng booking_seats (ghế đã đặt)
CREATE TABLE booking_seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    seat_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_id) REFERENCES seats(id) ON DELETE CASCADE
);

-- Bảng payments (thanh toán)
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    payment_method ENUM('cash', 'card', 'online') DEFAULT 'online',
    amount DECIMAL(10,2) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    transaction_id VARCHAR(100),
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Thêm dữ liệu mẫu
-- Users
INSERT INTO users (name, email, password, role, status) VALUES 
('Admin', 'admin@cgv.com', 'admin123', 'admin', 'active'),
('Nguyen Van A', 'user@test.com', '123456', 'user', 'active'),
('hungphi', 'hungphi@test.com', '123456', 'user', 'active');

-- Cities (63 Tỉnh thành phố Việt Nam)
INSERT INTO cities (code, name, status, display_order) VALUES 
-- Thành phố trực thuộc trung ương (5)
('HN', 'Hà Nội', 'active', 1),
('HCM', 'TP. Hồ Chí Minh', 'active', 2),
('HP', 'Hải Phòng', 'active', 3),
('DN', 'Đà Nẵng', 'active', 4),
('CT', 'Cần Thơ', 'active', 5),

-- Miền Bắc (26 tỉnh)
('AG', 'An Giang', 'active', 6),
('BR', 'Bà Rịa - Vũng Tàu', 'active', 7),
('BK', 'Bắc Kạn', 'active', 8),
('BG', 'Bắc Giang', 'active', 9),
('BL', 'Bạc Liêu', 'active', 10),
('BN', 'Bắc Ninh', 'active', 11),
('BT', 'Bến Tre', 'active', 12),
('BD', 'Bình Định', 'active', 13),
('BP', 'Bình Dương', 'active', 14),
('BPh', 'Bình Phước', 'active', 15),
('BTh', 'Bình Thuận', 'active', 16),
('CM', 'Cà Mau', 'active', 17),
('CB', 'Cao Bằng', 'active', 18),
('DL', 'Đắk Lắk', 'active', 19),
('DN2', 'Đắk Nông', 'active', 20),
('DB', 'Điện Biên', 'active', 21),
('DNA', 'Đồng Nai', 'active', 22),
('DT', 'Đồng Tháp', 'active', 23),
('GL', 'Gia Lai', 'active', 24),
('HG', 'Hà Giang', 'active', 25),
('HB', 'Hà Nam', 'active', 26),
('HT', 'Hà Tĩnh', 'active', 27),
('HY', 'Hải Dương', 'active', 28),
('HU', 'Hậu Giang', 'active', 29),
('HB2', 'Hòa Bình', 'active', 30),
('HY2', 'Hưng Yên', 'active', 31),
('KH', 'Khánh Hòa', 'active', 32),
('KG', 'Kiên Giang', 'active', 33),
('KT', 'Kon Tum', 'active', 34),
('LChau', 'Lai Châu', 'active', 35),
('LĐ', 'Lâm Đồng', 'active', 36),
('LS', 'Lạng Sơn', 'active', 37),
('LC', 'Lào Cai', 'active', 38),
('LAN', 'Long An', 'active', 39),
('ND', 'Nam Định', 'active', 40),
('NA', 'Nghệ An', 'active', 41),
('NB', 'Ninh Bình', 'active', 42),
('NT', 'Ninh Thuận', 'active', 43),
('PT', 'Phú Thọ', 'active', 44),
('PY', 'Phú Yên', 'active', 45),
('QB', 'Quảng Bình', 'active', 46),
('QN', 'Quảng Nam', 'active', 47),
('QG', 'Quảng Ngãi', 'active', 48),
('QNi', 'Quảng Ninh', 'active', 49),
('QT', 'Quảng Trị', 'active', 50),
('ST', 'Sóc Trăng', 'active', 51),
('SL', 'Sơn La', 'active', 52),
('TY', 'Tây Ninh', 'active', 53),
('TB', 'Thái Bình', 'active', 54),
('TNg', 'Thái Nguyên', 'active', 55),
('TH', 'Thanh Hóa', 'active', 56),
('TTH', 'Thừa Thiên Huế', 'active', 57),
('TG', 'Tiền Giang', 'active', 58),
('TV', 'Trà Vinh', 'active', 59),
('TQ', 'Tuyên Quang', 'active', 60),
('VL', 'Vĩnh Long', 'active', 61),
('VP', 'Vĩnh Phúc', 'active', 62),
('YB', 'Yên Bái', 'active', 63);

-- Theaters (Cập nhật với city_id mới)
INSERT INTO theaters (name, location, city_id, phone, status, total_screens) VALUES 
-- Hà Nội (city_id = 1)
('CGV Vincom Center', '191 Bà Triệu, Hai Bà Trưng', 1, '024 3974 3333', 'active', 8),
('CGV Aeon Mall', 'Số 27 Cổ Linh, Long Biên', 1, '024 3974 4444', 'active', 6),
('CGV Times City', '458 Minh Khai, Hai Bà Trưng', 1, '024 3974 5555', 'active', 10),
('Lotte Cinema Keangnam', '72 Phạm Hùng, Nam Từ Liêm', 1, '024 3974 6666', 'active', 8),

-- TP. Hồ Chí Minh (city_id = 2)
('CGV Hùng Vương Plaza', '126 Hùng Vương, Q.5', 2, '028 3833 6666', 'active', 7),
('CGV Crescent Mall', '101 Tôn Dật Tiên, Q.7', 2, '028 5413 7777', 'active', 5),
('Galaxy Nguyễn Du', '116 Nguyễn Du, Q.1', 2, '028 3822 8888', 'active', 6),
('CGV Parkson Saigon Tourist', '35 - 45 Nguyễn Du, Q.1', 2, '028 3825 9999', 'active', 5),
('Mega GS Cinemas Cao Thắng', '19 Cao Thắng, Q.3', 2, '028 3930 0000', 'active', 7),

-- Hải Phòng (city_id = 3)
('Lotte Cinema Hải Phòng', '200A Lê Thánh Tông, Q. Ngô Quyền', 3, '0225 3820 111', 'active', 5),
('CGV Vincom Hải Phòng', '132 Lạch Tray, Q. Ngô Quyền', 3, '0225 3820 222', 'active', 6),

-- Đà Nẵng (city_id = 4)
('CGV Vincom Đà Nẵng', '244-246 Trần Phú, Q. Hải Châu', 4, '0236 3650 999', 'active', 4),
('Lotte Cinema Đà Nẵng', '6-10-12 Trần Phú, Q. Hải Châu', 4, '0236 3650 888', 'active', 5),

-- Cần Thơ (city_id = 5)
('CGV Vincom Cần Thơ', '209 Nguyễn Văn Cừ, Q. Ninh Kiều', 5, '0292 3767 222', 'active', 6),
('Lotte Cinema Cần Thơ', '52 Trần Phú, Q. Cái Răng', 5, '0292 3767 333', 'active', 4),

-- Bình Dương (city_id = 14)
('Galaxy Bình Dương', '01 Đại Lộ Bình Dương, TP. Thủ Dầu Một', 14, '0274 3690 333', 'active', 4),
('CGV Aeon Bình Dương', '1-5 Đại Lộ Bình Dương, TP. Thủ Dầu Một', 14, '0274 3690 444', 'active', 6),

-- Đồng Nai (city_id = 22) 
('CGV Vincom Biên Hòa', '60A Nguyễn Ái Quốc, TP. Biên Hòa', 22, '0251 3836 555', 'active', 5),

-- Khánh Hòa (city_id = 32)
('CGV Vincom Nha Trang', '50 Trần Phú, TP. Nha Trang', 32, '0258 3836 666', 'active', 4),
('Lotte Cinema Nha Trang', '2C Trần Quang Khải, TP. Nha Trang', 32, '0258 3836 777', 'active', 5);

-- Movies
INSERT INTO movies (title, description, duration, genre, release_date, poster_url, status, rating) VALUES 
('Lật Mặt 8', 'Phim hành động Việt Nam đầy kịch tính với những pha action mãn nhãn', 120, 'Hành động, Hài', '2024-01-15', 'img/Phim/latmat8.jpg', 'showing', 8.5),
('Thám Tử Kiên', 'Phim trinh thám hấp dẫn với cốt truyện ly kỳ, bí ẩn', 110, 'Trinh thám, Hành động', '2024-02-01', 'img/Phim/thamtukien.jpg', 'showing', 8.0),
('Địa Đạo', 'Phim chiến tranh lịch sử Việt Nam đầy cảm động và hùng tráng', 130, 'Chiến tranh, Hành động', '2024-01-20', 'img/Phim/diadao.jpg', 'showing', 8.8),
('Đội Săn Quỷ', 'Phim kinh dị siêu nhiên với những cảnh quay rùng rợn', 105, 'Kinh dị, Hành động', '2024-02-10', 'img/Phim/doisanquy.jpg', 'showing', 7.5),
('Shin Cậu Bé Bút Chì', 'Phim hoạt hình gia đình vui nhộn và đáng yêu', 95, 'Hoạt hình, Gia đình', '2024-01-25', 'img/Phim/shin.jpg', 'showing', 9.0),
('Doraemon Movie 44', 'Phim hoạt hình Doraemon mới nhất với cuộc phiêu lưu thú vị', 100, 'Hoạt hình, Phiêu lưu', '2024-02-05', 'img/Phim/doraemon_movie44.jpg', 'showing', 8.7);

-- Screens (Phòng chiếu cho 19 rạp)
INSERT INTO screens (theater_id, screen_name, total_seats) VALUES 
-- CGV Vincom Center (8 phòng)
(1, 'Phòng 1', 120), (1, 'Phòng 2', 100), (1, 'Phòng 3', 80), (1, 'Phòng 4', 150),
(1, 'Phòng 5', 140), (1, 'Phòng 6', 160), (1, 'Phòng 7', 90), (1, 'Phòng 8', 110),

-- CGV Aeon Mall (6 phòng)
(2, 'Phòng 1', 130), (2, 'Phòng 2', 120), (2, 'Phòng 3', 100), (2, 'Phòng 4', 140),
(2, 'Phòng 5', 110), (2, 'Phòng 6', 95),

-- CGV Times City (10 phòng)
(3, 'Phòng 1', 200), (3, 'Phòng 2', 180), (3, 'Phòng 3', 160), (3, 'Phòng 4', 190),
(3, 'Phòng 5', 150), (3, 'Phòng 6', 170), (3, 'Phòng 7', 140), (3, 'Phòng 8', 130),
(3, 'Phòng 9', 120), (3, 'Phòng 10', 100),

-- Lotte Cinema Keangnam (8 phòng)
(4, 'Phòng 1', 180), (4, 'Phòng 2', 160), (4, 'Phòng 3', 140), (4, 'Phòng 4', 120),
(4, 'Phòng 5', 100), (4, 'Phòng 6', 180), (4, 'Phòng 7', 150), (4, 'Phòng 8', 130),

-- CGV Hùng Vương Plaza (7 phòng)
(5, 'Phòng 1', 140), (5, 'Phòng 2', 120), (5, 'Phòng 3', 100), (5, 'Phòng 4', 130),
(5, 'Phòng 5', 110), (5, 'Phòng 6', 90), (5, 'Phòng 7', 160),

-- CGV Crescent Mall (5 phòng)
(6, 'Phòng 1', 90), (6, 'Phòng 2', 100), (6, 'Phòng 3', 80), (6, 'Phòng 4', 120), (6, 'Phòng 5', 110),

-- Galaxy Nguyễn Du (6 phòng)
(7, 'Phòng 1', 150), (7, 'Phòng 2', 130), (7, 'Phòng 3', 120), (7, 'Phòng 4', 140),
(7, 'Phòng 5', 100), (7, 'Phòng 6', 110),

-- CGV Parkson Saigon Tourist (5 phòng)
(8, 'Phòng 1', 120), (8, 'Phòng 2', 100), (8, 'Phòng 3', 90), (8, 'Phòng 4', 110), (8, 'Phòng 5', 130),

-- Mega GS Cinemas Cao Thắng (7 phòng)
(9, 'Phòng 1', 160), (9, 'Phòng 2', 140), (9, 'Phòng 3', 120), (9, 'Phòng 4', 100),
(9, 'Phòng 5', 180), (9, 'Phòng 6', 150), (9, 'Phòng 7', 110),

-- Lotte Cinema Hải Phòng (5 phòng)
(10, 'Phòng 1', 130), (10, 'Phòng 2', 110), (10, 'Phòng 3', 120), (10, 'Phòng 4', 140), (10, 'Phòng 5', 100),

-- CGV Vincom Hải Phòng (6 phòng)
(11, 'Phòng 1', 150), (11, 'Phòng 2', 130), (11, 'Phòng 3', 110), (11, 'Phòng 4', 120),
(11, 'Phòng 5', 140), (11, 'Phòng 6', 100),

-- CGV Vincom Đà Nẵng (4 phòng)
(12, 'Phòng 1', 140), (12, 'Phòng 2', 120), (12, 'Phòng 3', 100), (12, 'Phòng 4', 160),

-- Lotte Cinema Đà Nẵng (5 phòng)
(13, 'Phòng 1', 150), (13, 'Phòng 2', 130), (13, 'Phòng 3', 110), (13, 'Phòng 4', 140), (13, 'Phòng 5', 120),

-- CGV Vincom Cần Thơ (6 phòng)
(14, 'Phòng 1', 150), (14, 'Phòng 2', 130), (14, 'Phòng 3', 120), (14, 'Phòng 4', 140),
(14, 'Phòng 5', 110), (14, 'Phòng 6', 100),

-- Lotte Cinema Cần Thơ (4 phòng)
(15, 'Phòng 1', 120), (15, 'Phòng 2', 100), (15, 'Phòng 3', 140), (15, 'Phòng 4', 110),

-- Galaxy Bình Dương (4 phòng)
(16, 'Phòng 1', 120), (16, 'Phòng 2', 100), (16, 'Phòng 3', 140), (16, 'Phòng 4', 130),

-- CGV Aeon Bình Dương (6 phòng)
(17, 'Phòng 1', 160), (17, 'Phòng 2', 140), (17, 'Phòng 3', 120), (17, 'Phòng 4', 100),
(17, 'Phòng 5', 180), (17, 'Phòng 6', 150),

-- CGV Vincom Biên Hòa (5 phòng)
(18, 'Phòng 1', 130), (18, 'Phòng 2', 110), (18, 'Phòng 3', 120), (18, 'Phòng 4', 140), (18, 'Phòng 5', 100),

-- CGV Vincom Nha Trang (4 phòng)
(19, 'Phòng 1', 140), (19, 'Phòng 2', 120), (19, 'Phòng 3', 100), (19, 'Phòng 4', 160);

-- Seats cho phòng chiếu đầu tiên (120 ghế)
INSERT INTO seats (screen_id, seat_row, seat_number, seat_type) VALUES 
-- Hàng A-D: ghế thường
(1, 'A', 1, 'standard'), (1, 'A', 2, 'standard'), (1, 'A', 3, 'standard'), (1, 'A', 4, 'standard'), (1, 'A', 5, 'standard'),
(1, 'A', 6, 'standard'), (1, 'A', 7, 'standard'), (1, 'A', 8, 'standard'), (1, 'A', 9, 'standard'), (1, 'A', 10, 'standard'),
(1, 'B', 1, 'standard'), (1, 'B', 2, 'standard'), (1, 'B', 3, 'standard'), (1, 'B', 4, 'standard'), (1, 'B', 5, 'standard'),
(1, 'B', 6, 'standard'), (1, 'B', 7, 'standard'), (1, 'B', 8, 'standard'), (1, 'B', 9, 'standard'), (1, 'B', 10, 'standard'),
(1, 'C', 1, 'standard'), (1, 'C', 2, 'standard'), (1, 'C', 3, 'standard'), (1, 'C', 4, 'standard'), (1, 'C', 5, 'standard'),
(1, 'C', 6, 'standard'), (1, 'C', 7, 'standard'), (1, 'C', 8, 'standard'), (1, 'C', 9, 'standard'), (1, 'C', 10, 'standard'),
(1, 'D', 1, 'standard'), (1, 'D', 2, 'standard'), (1, 'D', 3, 'standard'), (1, 'D', 4, 'standard'), (1, 'D', 5, 'standard'),
(1, 'D', 6, 'standard'), (1, 'D', 7, 'standard'), (1, 'D', 8, 'standard'), (1, 'D', 9, 'standard'), (1, 'D', 10, 'standard'),
-- Hàng E-G: ghế VIP
(1, 'E', 1, 'vip'), (1, 'E', 2, 'vip'), (1, 'E', 3, 'vip'), (1, 'E', 4, 'vip'), (1, 'E', 5, 'vip'),
(1, 'E', 6, 'vip'), (1, 'E', 7, 'vip'), (1, 'E', 8, 'vip'), (1, 'E', 9, 'vip'), (1, 'E', 10, 'vip'),
(1, 'F', 1, 'vip'), (1, 'F', 2, 'vip'), (1, 'F', 3, 'vip'), (1, 'F', 4, 'vip'), (1, 'F', 5, 'vip'),
(1, 'F', 6, 'vip'), (1, 'F', 7, 'vip'), (1, 'F', 8, 'vip'), (1, 'F', 9, 'vip'), (1, 'F', 10, 'vip'),
(1, 'G', 1, 'vip'), (1, 'G', 2, 'vip'), (1, 'G', 3, 'vip'), (1, 'G', 4, 'vip'), (1, 'G', 5, 'vip'),
(1, 'G', 6, 'vip'), (1, 'G', 7, 'vip'), (1, 'G', 8, 'vip'), (1, 'G', 9, 'vip'), (1, 'G', 10, 'vip'),
-- Hàng H-J: ghế thường
(1, 'H', 1, 'standard'), (1, 'H', 2, 'standard'), (1, 'H', 3, 'standard'), (1, 'H', 4, 'standard'), (1, 'H', 5, 'standard'),
(1, 'H', 6, 'standard'), (1, 'H', 7, 'standard'), (1, 'H', 8, 'standard'), (1, 'H', 9, 'standard'), (1, 'H', 10, 'standard'),
(1, 'I', 1, 'standard'), (1, 'I', 2, 'standard'), (1, 'I', 3, 'standard'), (1, 'I', 4, 'standard'), (1, 'I', 5, 'standard'),
(1, 'I', 6, 'standard'), (1, 'I', 7, 'standard'), (1, 'I', 8, 'standard'), (1, 'I', 9, 'standard'), (1, 'I', 10, 'standard'),
(1, 'J', 1, 'standard'), (1, 'J', 2, 'standard'), (1, 'J', 3, 'standard'), (1, 'J', 4, 'standard'), (1, 'J', 5, 'standard'),
(1, 'J', 6, 'standard'), (1, 'J', 7, 'standard'), (1, 'J', 8, 'standard'), (1, 'J', 9, 'standard'), (1, 'J', 10, 'standard');

-- Showtimes (Lịch chiếu từ hôm nay)
INSERT INTO showtimes (movie_id, screen_id, show_date, show_time, price, available_seats) VALUES 
-- Hôm nay - CGV Vincom Center
(1, 1, CURDATE(), '09:00:00', 80000, 120),
(1, 1, CURDATE(), '12:00:00', 80000, 120),
(1, 1, CURDATE(), '15:00:00', 90000, 120),
(1, 1, CURDATE(), '18:00:00', 100000, 120),
(1, 1, CURDATE(), '21:00:00', 100000, 120),

(2, 9, CURDATE(), '10:00:00', 75000, 130), -- CGV Aeon Mall Phòng 1
(2, 9, CURDATE(), '13:30:00', 75000, 130),
(2, 9, CURDATE(), '16:30:00', 85000, 130),
(2, 9, CURDATE(), '19:30:00', 95000, 130),

(3, 15, CURDATE(), '11:00:00', 85000, 200), -- CGV Times City Phòng 1
(3, 15, CURDATE(), '14:00:00', 85000, 200),
(3, 15, CURDATE(), '17:00:00', 95000, 200),
(3, 15, CURDATE(), '20:00:00', 105000, 200),

-- Mai - Các rạp khác nhau
(1, 25, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', 80000, 140), -- CGV Hùng Vương Phòng 1
(1, 25, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '12:00:00', 80000, 140),
(1, 25, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '15:00:00', 90000, 140),
(1, 25, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '18:00:00', 100000, 140),
(1, 25, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '21:00:00', 100000, 140),

(4, 32, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:30:00', 90000, 90), -- CGV Crescent Mall Phòng 1
(4, 32, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '13:00:00', 90000, 90),
(4, 32, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '16:00:00', 100000, 90),
(4, 32, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '19:00:00', 110000, 90),

(5, 38, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:30:00', 70000, 150), -- Galaxy Nguyễn Du Phòng 1
(5, 38, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '11:30:00', 70000, 150),
(5, 38, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:00:00', 80000, 150),
(5, 38, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '16:30:00', 80000, 150),

(6, 44, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00:00', 75000, 140), -- CGV Vincom Đà Nẵng Phòng 1
(6, 44, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '12:30:00', 75000, 140),
(6, 44, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '15:30:00', 85000, 140),
(6, 44, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '18:30:00', 95000, 140),

-- Ngày kia - Lotte Cinema Hải Phòng
(2, 48, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '09:00:00', 75000, 130),
(2, 48, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '12:00:00', 75000, 130),
(2, 48, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '15:00:00', 85000, 130),
(2, 48, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '18:00:00', 95000, 130),
(2, 48, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '21:00:00', 95000, 130),

(3, 53, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '10:00:00', 85000, 150), -- CGV Vincom Cần Thơ Phòng 1
(3, 53, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '13:00:00', 85000, 150),
(3, 53, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '16:00:00', 95000, 150),
(3, 53, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '19:00:00', 105000, 150),

-- Cuối tuần - Galaxy Bình Dương
(1, 59, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '08:00:00', 80000, 120),
(1, 59, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '11:00:00', 80000, 120),
(1, 59, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '14:00:00', 90000, 120),
(1, 59, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '17:00:00', 100000, 120),
(1, 59, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '20:00:00', 100000, 120),
(1, 59, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '22:30:00', 110000, 120);

-- Bật lại foreign key checks
SET FOREIGN_KEY_CHECKS = 1;


