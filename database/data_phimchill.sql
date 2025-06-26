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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng theaters (rạp chiếu)
CREATE TABLE theaters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(255) NOT NULL,
    total_screens INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@cgv.com', 'admin123', 'admin'),
('Nguyen Van A', 'user@test.com', '123456', 'user'),
('hungphi', 'hungphi@test.com', '123456', 'user');

-- Theaters
INSERT INTO theaters (name, location, total_screens) VALUES 
('CGV Vincom Center', '191 Bà Triệu, Hai Bà Trưng, Hà Nội', 8),
('CGV Aeon Mall', 'Số 27 Cổ Linh, Long Biên, Hà Nội', 6),
('CGV Times City', '458 Minh Khai, Hai Bà Trưng, Hà Nội', 10),
('CGV Hùng Vương Plaza', '126 Hùng Vương, Q.5, TP.HCM', 7),
('CGV Crescent Mall', '101 Tôn Dật Tiên, Q.7, TP.HCM', 5);

-- Movies
INSERT INTO movies (title, description, duration, genre, release_date, poster_url, status, rating) VALUES 
('Lật Mặt 8', 'Phim hành động Việt Nam đầy kịch tính với những pha action mãn nhãn', 120, 'Hành động, Hài', '2024-01-15', 'img/Phim/latmat8.jpg', 'showing', 8.5),
('Thám Tử Kiên', 'Phim trinh thám hấp dẫn với cốt truyện ly kỳ, bí ẩn', 110, 'Trinh thám, Hành động', '2024-02-01', 'img/Phim/thamtukien.jpg', 'showing', 8.0),
('Địa Đạo', 'Phim chiến tranh lịch sử Việt Nam đầy cảm động và hùng tráng', 130, 'Chiến tranh, Hành động', '2024-01-20', 'img/Phim/diadao.jpg', 'showing', 8.8),
('Đội Săn Quỷ', 'Phim kinh dị siêu nhiên với những cảnh quay rùng rợn', 105, 'Kinh dị, Hành động', '2024-02-10', 'img/Phim/doisanquy.jpg', 'showing', 7.5),
('Shin Cậu Bé Bút Chì', 'Phim hoạt hình gia đình vui nhộn và đáng yêu', 95, 'Hoạt hình, Gia đình', '2024-01-25', 'img/Phim/shin.jpg', 'showing', 9.0),
('Doraemon Movie 44', 'Phim hoạt hình Doraemon mới nhất với cuộc phiêu lưu thú vị', 100, 'Hoạt hình, Phiêu lưu', '2024-02-05', 'img/Phim/doraemon_movie44.jpg', 'showing', 8.7);

-- Screens
INSERT INTO screens (theater_id, screen_name, total_seats) VALUES 
(1, 'Phòng 1', 120),
(1, 'Phòng 2', 100),
(1, 'Phòng 3', 80),
(2, 'Phòng 1', 150),
(2, 'Phòng 2', 120),
(3, 'Phòng 1', 200),
(3, 'Phòng 2', 180),
(4, 'Phòng 1', 140),
(4, 'Phòng 2', 100),
(5, 'Phòng 1', 90);

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
-- Hôm nay
(1, 1, CURDATE(), '09:00:00', 80000, 120),
(1, 1, CURDATE(), '12:00:00', 80000, 120),
(1, 1, CURDATE(), '15:00:00', 90000, 120),
(1, 1, CURDATE(), '18:00:00', 100000, 120),
(1, 1, CURDATE(), '21:00:00', 100000, 120),

(2, 2, CURDATE(), '10:00:00', 75000, 100),
(2, 2, CURDATE(), '13:30:00', 75000, 100),
(2, 2, CURDATE(), '16:30:00', 85000, 100),
(2, 2, CURDATE(), '19:30:00', 95000, 100),

(3, 3, CURDATE(), '11:00:00', 85000, 80),
(3, 3, CURDATE(), '14:00:00', 85000, 80),
(3, 3, CURDATE(), '17:00:00', 95000, 80),
(3, 3, CURDATE(), '20:00:00', 105000, 80),

-- Mai  
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', 80000, 120),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '12:00:00', 80000, 120),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '15:00:00', 90000, 120),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '18:00:00', 100000, 120),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '21:00:00', 100000, 120),

(4, 4, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:30:00', 90000, 140),
(4, 4, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '13:00:00', 90000, 140),
(4, 4, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '16:00:00', 100000, 140),
(4, 4, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '19:00:00', 110000, 140),

(5, 5, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:30:00', 70000, 120),
(5, 5, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '11:30:00', 70000, 120),
(5, 5, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:00:00', 80000, 120),
(5, 5, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '16:30:00', 80000, 120),

(6, 6, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00:00', 75000, 200),
(6, 6, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '12:30:00', 75000, 200),
(6, 6, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '15:30:00', 85000, 200),
(6, 6, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '18:30:00', 95000, 200),

-- Ngày kia
(2, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '09:00:00', 75000, 100),
(2, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '12:00:00', 75000, 100),
(2, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '15:00:00', 85000, 100),
(2, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '18:00:00', 95000, 100),
(2, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '21:00:00', 95000, 100),

(3, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '10:00:00', 85000, 80),
(3, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '13:00:00', 85000, 80),
(3, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '16:00:00', 95000, 80),
(3, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '19:00:00', 105000, 80),

-- Cuối tuần
(1, 1, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '08:00:00', 80000, 120),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '11:00:00', 80000, 120),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '14:00:00', 90000, 120),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '17:00:00', 100000, 120),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '20:00:00', 100000, 120),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '22:30:00', 110000, 120);

-- Bật lại foreign key checks
SET FOREIGN_KEY_CHECKS = 1;


