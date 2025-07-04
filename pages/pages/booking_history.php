<?php 
   require_once __DIR__ . '/../../admin/config/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo '<script>alert("Vui lòng đăng nhập để xem lịch sử đặt vé!"); window.location.href = "index.php?quanly=dangnhap";</script>';
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy lịch sử đặt vé
$sql = "SELECT b.*, m.title as movie_title, m.poster_url, 
               t.name as theater_name, s.screen_name,
               st.show_date, st.show_time,
               GROUP_CONCAT(CONCAT(se.seat_row, se.seat_number) ORDER BY se.seat_row, se.seat_number SEPARATOR ', ') as seats
        FROM bookings b
        INNER JOIN showtimes st ON b.showtime_id = st.id
        INNER JOIN movies m ON st.movie_id = m.id
        INNER JOIN screens s ON st.screen_id = s.id
        INNER JOIN theaters t ON s.theater_id = t.id
        LEFT JOIN booking_seats bs ON b.id = bs.booking_id
        LEFT JOIN seats se ON bs.seat_id = se.id
        WHERE b.user_id = ?
        GROUP BY b.id
        ORDER BY b.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="main-content">
    <div class="booking-history-container">
        <h2 style="color: #fff; text-align: center; margin: 20px 0;">LỊCH SỬ ĐẶT VÉ</h2>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="bookings-list">
                <?php while($booking = $result->fetch_assoc()): ?>
                    <div class="booking-card">
                        <div class="booking-header">
                            <div class="booking-code">
                                <strong>Mã đặt vé: <?php echo htmlspecialchars($booking['booking_code']); ?></strong>
                                <span class="booking-status <?php echo $booking['booking_status']; ?>">
                                    <?php 
                                    switch($booking['booking_status']) {
                                        case 'confirmed': echo 'Đã xác nhận'; break;
                                        case 'pending': echo 'Chờ xác nhận'; break;
                                        case 'cancelled': echo 'Đã hủy'; break;
                                        default: echo ucfirst($booking['booking_status']);
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="booking-date">
                                Đặt ngày: <?php echo date('d/m/Y H:i', strtotime($booking['created_at'])); ?>
                            </div>
                        </div>
                        
                        <div class="booking-details">
                            <div class="movie-info">
                                <img src="<?php echo $booking['poster_url']; ?>" alt="<?php echo htmlspecialchars($booking['movie_title']); ?>">
                                <div class="movie-details">
                                    <h3><?php echo htmlspecialchars($booking['movie_title']); ?></h3>
                                    <p><strong>Rạp:</strong> <?php echo htmlspecialchars($booking['theater_name']); ?></p>
                                    <p><strong>Phòng:</strong> <?php echo htmlspecialchars($booking['screen_name']); ?></p>
                                    <p><strong>Ngày chiếu:</strong> <?php echo date('d/m/Y', strtotime($booking['show_date'])); ?></p>
                                    <p><strong>Giờ chiếu:</strong> <?php echo date('H:i', strtotime($booking['show_time'])); ?></p>
                                    <p><strong>Ghế:</strong> <?php echo htmlspecialchars($booking['seats']); ?></p>
                                </div>
                            </div>
                            
                            <div class="booking-payment">
                                <div class="amount">
                                    <strong>Tổng tiền: <?php echo number_format($booking['total_amount'], 0, ',', '.'); ?> VNĐ</strong>
                                </div>
                                <div class="payment-status <?php echo $booking['payment_status']; ?>">
                                    <?php 
                                    switch($booking['payment_status']) {
                                        case 'paid': echo 'Đã thanh toán'; break;
                                        case 'unpaid': echo 'Chưa thanh toán'; break;
                                        case 'refunded': echo 'Đã hoàn tiền'; break;
                                        default: echo ucfirst($booking['payment_status']);
                                    }
                                    ?>
                                </div>
                                
                                <?php if ($booking['booking_status'] == 'confirmed' && strtotime($booking['show_date'] . ' ' . $booking['show_time']) > time()): ?>
                                    <button class="btn-cancel" onclick="cancelBooking(<?php echo $booking['id']; ?>)">
                                        Hủy vé
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-bookings">
                <p style="color: #fff; text-align: center; margin: 50px 0;">Bạn chưa có đặt vé nào.</p>
                <div style="text-align: center;">
                    <a href="index.php?quanly=phim" class="btn-book-now">Đặt vé ngay</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function cancelBooking(bookingId) {
    if (!confirm('Bạn có chắc chắn muốn hủy vé này không?')) {
        return;
    }
    
    fetch('pages/actions/cancel_booking.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({booking_id: bookingId})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Hủy vé thành công!');
            location.reload();
        } else {
            alert('Có lỗi xảy ra: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi hủy vé!');
    });
}

function printTicket(bookingId) {
    window.open('pages/actions/print_ticket.php?booking_id=' + bookingId, '_blank');
}
</script>

<style>
.booking-history-container {
    padding: 20px;
    background-color: #000;
    min-height: 600px;
}

.bookings-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-top: 30px;
}

.booking-card {
    background-color: #1a1a1a;
    border-radius: 10px;
    padding: 20px;
    border: 1px solid #333;
    transition: border-color 0.3s ease;
}

.booking-card:hover {
    border-color: #e50914;
}

.booking-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #333;
}

.booking-code {
    color: #e50914;
    font-size: 18px;
}

.booking-status {
    margin-left: 15px;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
}

.booking-status.confirmed {
    background-color: #28a745;
    color: white;
}

.booking-status.pending {
    background-color: #ffc107;
    color: #000;
}

.booking-status.cancelled {
    background-color: #dc3545;
    color: white;
}

.booking-date {
    color: #ccc;
    font-size: 14px;
}

.booking-details {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.movie-info {
    display: flex;
    flex: 1;
}

.movie-info img {
    width: 80px;
    height: 120px;
    object-fit: cover;
    border-radius: 5px;
    margin-right: 15px;
}

.movie-details {
    flex: 1;
}

.movie-details h3 {
    color: #e50914;
    margin-bottom: 10px;
    font-size: 20px;
}

.movie-details p {
    margin: 5px 0;
    color: #ccc;
}

.booking-payment {
    text-align: right;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
}

.amount {
    color: #ffd700;
    font-size: 18px;
}

.payment-status {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
}

.payment-status.paid {
    background-color: #28a745;
    color: white;
}

.payment-status.unpaid {
    background-color: #dc3545;
    color: white;
}

.payment-status.refunded {
    background-color: #6c757d;
    color: white;
}

.btn-cancel, .btn-print {
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    margin-left: 10px;
    transition: all 0.3s ease;
}

.btn-cancel {
    background-color: #dc3545;
    color: white;
}



.btn-cancel:hover{
    transform: scale(1.05);
}

.no-bookings {
    text-align: center;
    margin: 100px 0;
}

.btn-book-now {
    background-color: #e50914;
    color: white;
    text-decoration: none;
    padding: 15px 30px;
    border-radius: 5px;
    font-weight: bold;
    transition: all 0.3s ease;
}

.btn-book-now:hover {
    background-color: #cc0812;
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .booking-details {
        flex-direction: column;
    }
    
    .booking-payment {
        text-align: left;
        align-items: flex-start;
        margin-top: 20px;
    }
    
    .booking-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}
</style> 