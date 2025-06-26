<?php 
require_once 'admin/config/config.php';

// Kiểm tra kết nối database
if (!$conn) {
    echo '<div style="color: red; padding: 20px; text-align: center;">Lỗi kết nối database. Vui lòng thử lại sau.</div>';
    exit();
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo '<script>alert("Vui lòng đăng nhập để đặt vé!"); window.location.href = "index.php?quanly=dangnhap";</script>';
    exit();
}

$showtime_id = isset($_GET['showtime_id']) ? intval($_GET['showtime_id']) : 0;
$movie_id = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : 0;

// Nếu có movie_id, hiển thị lịch chiếu của phim đó
if ($movie_id && !$showtime_id) {
    $sql = "SELECT st.*, s.screen_name, t.name as theater_name, m.title as movie_title
            FROM showtimes st
            INNER JOIN screens s ON st.screen_id = s.id
            INNER JOIN theaters t ON s.theater_id = t.id  
            INNER JOIN movies m ON st.movie_id = m.id
            WHERE st.movie_id = ? AND st.show_date >= CURDATE()
            ORDER BY st.show_date, st.show_time";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo '<div style="color: red; padding: 20px; text-align: center;">Có lỗi xảy ra. Vui lòng thử lại sau.</div>';
        exit();
    }
    
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $showtimes = $stmt->get_result();
?>

<div class="main-content">
    <div class="booking-container">
        <h2 style="color: #fff; text-align: center; margin: 20px 0;">CHỌN LỊCH CHIẾU</h2>
        
        <div class="showtimes-selection">
            <?php if ($showtimes->num_rows > 0): ?>
                <?php while($showtime = $showtimes->fetch_assoc()): ?>
                    <div class="showtime-option">
                        <div class="showtime-info">
                            <h3><?php echo htmlspecialchars($showtime['movie_title']); ?></h3>
                            <p><strong>Rạp:</strong> <?php echo htmlspecialchars($showtime['theater_name']); ?></p>
                            <p><strong>Phòng:</strong> <?php echo htmlspecialchars($showtime['screen_name']); ?></p>
                            <p><strong>Ngày:</strong> <?php echo date('d/m/Y', strtotime($showtime['show_date'])); ?></p>
                            <p><strong>Giờ:</strong> <?php echo date('H:i', strtotime($showtime['show_time'])); ?></p>
                            <p><strong>Giá:</strong> <?php echo number_format($showtime['price'], 0, ',', '.'); ?> VNĐ</p>
                        </div>
                        <button class="btn-select-showtime" onclick="selectShowtime(<?php echo $showtime['id']; ?>)">
                            Chọn suất chiếu này
                        </button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="color: #fff; text-align: center; padding: 20px;">
                    <p>Không có lịch chiếu nào cho phim này từ hôm nay.</p>
                    <p><a href="index.php?quanly=phim" style="color: #e71a0f;">← Quay lại trang phim</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php } elseif ($showtime_id) {
    // Lấy thông tin suất chiếu
    $sql = "SELECT st.*, s.screen_name, s.id as screen_id, t.name as theater_name, m.title as movie_title, m.poster_url
            FROM showtimes st
            INNER JOIN screens s ON st.screen_id = s.id
            INNER JOIN theaters t ON s.theater_id = t.id  
            INNER JOIN movies m ON st.movie_id = m.id
            WHERE st.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $showtime_id);
    $stmt->execute();
    $showtime_result = $stmt->get_result();
    
    if ($showtime_result->num_rows > 0) {
        $showtime = $showtime_result->fetch_assoc();
        
        // Lấy ghế đã được đặt
        $booked_seats_sql = "SELECT s.seat_row, s.seat_number 
                             FROM booking_seats bs
                             INNER JOIN bookings b ON bs.booking_id = b.id
                             INNER JOIN seats s ON bs.seat_id = s.id
                             WHERE b.showtime_id = ? AND b.booking_status != 'cancelled'";
        $booked_stmt = $conn->prepare($booked_seats_sql);
        $booked_stmt->bind_param("i", $showtime_id);
        $booked_stmt->execute();
        $booked_result = $booked_stmt->get_result();
        
        $booked_seats = [];
        while($booked = $booked_result->fetch_assoc()) {
            $booked_seats[] = $booked['seat_row'] . $booked['seat_number'];
        }
?>

<div class="main-content">
    <div class="booking-container">
        <h2 style="color: #fff; text-align: center; margin: 20px 0;">ĐẶT VÉ XEM PHIM</h2>
        
        <div class="movie-booking-info">
            <img src="<?php echo $showtime['poster_url']; ?>" alt="<?php echo htmlspecialchars($showtime['movie_title']); ?>">
            <div class="booking-details">
                <h3><?php echo htmlspecialchars($showtime['movie_title']); ?></h3>
                <p><strong>Rạp:</strong> <?php echo htmlspecialchars($showtime['theater_name']); ?></p>
                <p><strong>Phòng:</strong> <?php echo htmlspecialchars($showtime['screen_name']); ?></p>
                <p><strong>Ngày:</strong> <?php echo date('d/m/Y', strtotime($showtime['show_date'])); ?></p>
                <p><strong>Giờ:</strong> <?php echo date('H:i', strtotime($showtime['show_time'])); ?></p>
                <p><strong>Giá:</strong> <span id="ticket-price"><?php echo number_format($showtime['price'], 0, ',', '.'); ?></span> VNĐ/vé</p>
            </div>
        </div>
        
        <div class="seat-selection">
            <h3 style="color: #fff; margin: 20px 0;">Chọn ghế</h3>
            
            <div class="screen">MÀN HÌNH</div>
            
            <div class="seats-container">
                <?php
                // Tạo sơ đồ ghế (giả lập 10 hàng, mỗi hàng 10 ghế)
                $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
                foreach($rows as $row) {
                    echo '<div class="seat-row">';
                    echo '<span class="row-label">' . $row . '</span>';
                    for($i = 1; $i <= 10; $i++) {
                        $seat_id = $row . $i;
                        $is_booked = in_array($seat_id, $booked_seats);
                        $seat_class = $is_booked ? 'seat booked' : 'seat available';
                        $seat_type = in_array($row, ['E', 'F', 'G']) ? 'vip' : 'standard';
                        
                        echo '<button class="' . $seat_class . ' ' . $seat_type . '" 
                              data-seat="' . $seat_id . '" 
                              data-row="' . $row . '" 
                              data-number="' . $i . '"
                              onclick="selectSeat(this)" ' . ($is_booked ? 'disabled' : '') . '>';
                        echo $i;
                        echo '</button>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
            
            <div class="seat-legend">
                <div class="legend-item">
                    <div class="seat available standard"></div>
                    <span>Ghế thường - Trống</span>
                </div>
                <div class="legend-item">
                    <div class="seat available vip"></div>
                    <span>Ghế VIP - Trống</span>
                </div>
                <div class="legend-item">
                    <div class="seat selected"></div>
                    <span>Ghế đã chọn</span>
                </div>
                <div class="legend-item">
                    <div class="seat booked"></div>
                    <span>Ghế đã đặt</span>
                </div>
            </div>
        </div>
        
        <div class="booking-summary">
            <h3 style="color: #fff;">Tóm tắt đặt vé</h3>
            <div class="summary-content">
                <p>Ghế đã chọn: <span id="selected-seats">Chưa chọn ghế</span></p>
                <p>Số lượng vé: <span id="ticket-count">0</span></p>
                <p>Tổng tiền: <span id="total-amount">0</span> VNĐ</p>
            </div>
            <button id="btn-book-tickets" class="btn-book-tickets" onclick="bookTickets()" disabled>
                ĐẶT VÉ
            </button>
        </div>
    </div>
</div>

<?php } else { ?>
<div class="main-content">
    <div class="booking-container">
        <h2 style="color: #fff; text-align: center; margin: 20px 0;">TRANG ĐẶT VÉ</h2>
        
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div style="color: #fff; text-align: center; padding: 40px;">
                <p style="font-size: 18px; margin-bottom: 20px;">Vui lòng đăng nhập để đặt vé xem phim</p>
                <a href="index.php?quanly=dangnhap" class="btn-back" style="margin-right: 15px;">Đăng nhập</a>
                <a href="index.php?quanly=dangky" class="btn-back">Đăng ký</a>
            </div>
        <?php else: ?>
            <div style="color: #fff; text-align: center; padding: 40px;">
                <p style="font-size: 18px; margin-bottom: 20px;">Chào mừng <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
                <p style="margin-bottom: 30px;">Vui lòng chọn phim để bắt đầu đặt vé</p>
                
                <div style="margin-bottom: 30px;">
                    <h3 style="color: #e71a0f; margin-bottom: 20px;">PHIM ĐANG CHIẾU</h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; max-width: 800px; margin: 0 auto;">
                        <?php
                        // Hiển thị danh sách phim
                        $movies_sql = "SELECT * FROM movies WHERE status = 'showing' LIMIT 6";
                        $movies_result = $conn->query($movies_sql);
                        
                        if ($movies_result && $movies_result->num_rows > 0) {
                            while($movie = $movies_result->fetch_assoc()) {
                                echo '<div style="background: #1a1a1a; border-radius: 10px; padding: 15px; border: 1px solid #333;">';
                                echo '<img src="' . $movie['poster_url'] . '" alt="' . htmlspecialchars($movie['title']) . '" style="width: 100%; height: 250px; object-fit: cover; border-radius: 8px; margin-bottom: 10px;">';
                                echo '<h4 style="color: #fff; margin-bottom: 8px;">' . htmlspecialchars($movie['title']) . '</h4>';
                                echo '<p style="color: #ccc; font-size: 14px; margin-bottom: 15px;">' . htmlspecialchars($movie['genre']) . '</p>';
                                echo '<a href="index.php?quanly=ve&movie_id=' . $movie['id'] . '" style="display: inline-block; background: #e71a0f; color: white; padding: 8px 16px; text-decoration: none; border-radius: 5px; font-size: 14px;">Đặt vé</a>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p style="color: #ccc; grid-column: 1 / -1;">Hiện tại chưa có phim nào đang chiếu.</p>';
                        }
                        ?>
                    </div>
                </div>
                
                <div style="margin-top: 20px;">
                    <a href="index.php?quanly=phim" class="btn-back">Xem tất cả phim</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php } } ?>

<script>
let selectedSeats = [];
let ticketPrice = <?php echo isset($showtime['price']) ? $showtime['price'] : 0; ?>;
let showtimeId = <?php echo $showtime_id; ?>;

function selectShowtime(id) {
    window.location.href = 'index.php?quanly=ve&showtime_id=' + id;
}

function selectSeat(seatElement) {
    const seatId = seatElement.dataset.seat;
    const seatType = seatElement.classList.contains('vip') ? 'vip' : 'standard';
    const price = seatType === 'vip' ? ticketPrice * 1.5 : ticketPrice;
    
    if (seatElement.classList.contains('selected')) {
        // Bỏ chọn ghế
        seatElement.classList.remove('selected');
        const index = selectedSeats.findIndex(seat => seat.id === seatId);
        if (index > -1) {
            selectedSeats.splice(index, 1);
        }
    } else {
        // Chọn ghế
        if (selectedSeats.length < 8) { // Giới hạn tối đa 8 vé
            seatElement.classList.add('selected');
            selectedSeats.push({
                id: seatId,
                row: seatElement.dataset.row,
                number: seatElement.dataset.number,
                type: seatType,
                price: price
            });
        } else {
            alert('Bạn chỉ có thể đặt tối đa 8 vé!');
            return;
        }
    }
    
    updateBookingSummary();
}

function updateBookingSummary() {
    const seatIds = selectedSeats.map(seat => seat.id).join(', ');
    const ticketCount = selectedSeats.length;
    const totalAmount = selectedSeats.reduce((total, seat) => total + seat.price, 0);
    
    document.getElementById('selected-seats').textContent = seatIds || 'Chưa chọn ghế';
    document.getElementById('ticket-count').textContent = ticketCount;
    document.getElementById('total-amount').textContent = new Intl.NumberFormat('vi-VN').format(totalAmount);
    
    const bookBtn = document.getElementById('btn-book-tickets');
    if (ticketCount > 0) {
        bookBtn.disabled = false;
        bookBtn.style.backgroundColor = '#e50914';
    } else {
        bookBtn.disabled = true;
        bookBtn.style.backgroundColor = '#666';
    }
}

function bookTickets() {
    if (selectedSeats.length === 0) {
        alert('Vui lòng chọn ít nhất một ghế!');
        return;
    }
    
    const totalAmount = selectedSeats.reduce((total, seat) => total + seat.price, 0);
    
    const bookingData = {
        showtime_id: showtimeId,
        seats: selectedSeats,
        total_amount: totalAmount
    };
    
    fetch('pages/actions/process_booking.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(bookingData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Đặt vé thành công! Mã đặt vé: ' + data.booking_code);
            window.location.href = 'index.php?quanly=lich-su-dat-ve';
        } else {
            alert('Có lỗi xảy ra: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi đặt vé!');
    });
}
</script>

<style>
.booking-container {
    padding: 20px;
    background-color: #000;
    color: white;
}

.showtimes-selection {
    display: grid;
    gap: 20px;
    margin-top: 20px;
}

.showtime-option {
    background-color: #1a1a1a;
    border-radius: 10px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid #333;
}

.showtime-option:hover {
    border-color: #e50914;
}

.showtime-info h3 {
    color: #e50914;
    margin-bottom: 10px;
}

.showtime-info p {
    margin: 5px 0;
    color: #ccc;
}

.btn-select-showtime {
    background-color: #e50914;
    color: white;
    border: none;
    padding: 15px 25px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
}

.btn-select-showtime:hover {
    background-color: #cc0812;
    transform: scale(1.05);
}

.movie-booking-info {
    display: flex;
    background-color: #1a1a1a;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
    align-items: center;
}

.movie-booking-info img {
    width: 120px;
    height: 160px;
    object-fit: cover;
    border-radius: 10px;
    margin-right: 20px;
}

.booking-details h3 {
    color: #e50914;
    margin-bottom: 15px;
    font-size: 24px;
}

.booking-details p {
    margin: 8px 0;
    color: #ccc;
}

.seat-selection {
    margin: 30px 0;
}

.screen {
    background: linear-gradient(90deg, #333, #666, #333);
    color: white;
    text-align: center;
    padding: 10px;
    margin-bottom: 30px;
    border-radius: 5px;
    font-weight: bold;
}

.seats-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.seat-row {
    display: flex;
    align-items: center;
    gap: 10px;
}

.row-label {
    width: 30px;
    text-align: center;
    color: #fff;
    font-weight: bold;
}

.seat {
    width: 35px;
    height: 35px;
    border: 2px solid #444;
    border-radius: 8px;
    cursor: pointer;
    font-size: 12px;
    font-weight: bold;
    transition: all 0.3s ease;
}

.seat.available.standard {
    background-color: #2a2a2a;
    color: #fff;
}

.seat.available.vip {
    background-color: #ffd700;
    color: #000;
}

.seat.selected {
    background-color: #e50914 !important;
    color: white;
    border-color: #e50914;
}

.seat.booked {
    background-color: #666;
    color: #999;
    cursor: not-allowed;
}

.seat:hover:not(.booked):not(.selected) {
    transform: scale(1.1);
    border-color: #e50914;
}

.seat-legend {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #ccc;
}

.legend-item .seat {
    width: 20px;
    height: 20px;
    font-size: 10px;
    cursor: default;
}

.booking-summary {
    background-color: #1a1a1a;
    border-radius: 10px;
    padding: 20px;
    margin-top: 30px;
}

.summary-content p {
    margin: 10px 0;
    font-size: 16px;
}

.summary-content span {
    color: #e50914;
    font-weight: bold;
}

.btn-book-tickets {
    width: 100%;
    background-color: #666;
    color: white;
    border: none;
    padding: 15px;
    border-radius: 5px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 20px;
    transition: all 0.3s ease;
}

.btn-book-tickets:not(:disabled):hover {
    background-color: #cc0812 !important;
    transform: scale(1.02);
}

.btn-book-tickets:disabled {
    cursor: not-allowed;
}

.btn-back {
    background-color: #e50914;
    color: white;
    text-decoration: none;
    padding: 15px 30px;
    border-radius: 5px;
    font-weight: bold;
    transition: all 0.3s ease;
    display: inline-block;
}

.btn-back:hover {
    background-color: #cc0812;
    transform: scale(1.05);
    text-decoration: none;
    color: white;
}
</style>