<link rel="stylesheet" href="../../css/checkout.css">

<?php
session_name('CGV_SESSION');
session_start();
require_once __DIR__ . '/../../admin/config/config.php';

// Lấy ghế đã chọn
$selected_seats = $_SESSION['selected_seats'] ?? [];
$ticket_price = 75000; // giá vé 1 ghế
$total_ticket = count($selected_seats) * $ticket_price;

// Lấy combo đã chọn
$selected_combos = $_SESSION['selected_combos'] ?? [];
$combo_details = [];
$total_combo = 0;

if (!empty($selected_combos)) {
    // Lấy thông tin combo từ DB
    $ids = implode(',', array_map('intval', array_keys($selected_combos)));
    $sql = "SELECT * FROM combos WHERE id IN ($ids)";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $cid = $row['id'];
        $qty = $selected_combos[$cid];
        $combo_total = $qty * $row['price'];
        $combo_details[] = [
            'name' => $row['name'],
            'quantity' => $qty,
            'price' => $row['price'],
            'total' => $combo_total
        ];
        $total_combo += $combo_total;
    }
}

$total = $total_ticket + $total_combo;
?>

<div class="checkout-container">
  <div class="checkout-header">
    <span class="checkout-icon">🧾</span>
    <h2>Xác nhận thanh toán</h2>
  </div>
  <div class="checkout-box">
    <h4>Thông tin đặt vé</h4>
    <p><b>Ghế đã chọn:</b> 
    <?php 
    $seat_name = array_map(function($seat) {
        return is_array($seat) ? ($seat['id'] ?? '??') : $seat;
    }, $selected_seats);
    echo implode(', ', $seat_name); 
    ?>
    </p>
    <p><b>Giá vé:</b> <?php echo number_format($ticket_price); ?> VNĐ x <?php echo count($selected_seats); ?> = <span class="text-danger fw-bold"><?php echo number_format($total_ticket); ?> VNĐ</span></p>
    <?php if (!empty($combo_details)): ?>
        <?php foreach ($combo_details as $combo): ?>
            <p>
                <b>Combo:</b> <?php echo htmlspecialchars($combo['name']); ?> x <?php echo $combo['quantity']; ?> = 
                <span class="text-danger fw-bold"><?php echo number_format($combo['total']); ?> VNĐ</span>
            </p>
        <?php endforeach; ?>
    <?php endif; ?>
    <hr>
    <p class="checkout-total">Tổng cộng: <span><?php echo number_format($total); ?> VNĐ</span></p>
  </div>
  <form id="checkout-form" onsubmit="return submitBooking(event);">
    <div class="checkout-actions">
      <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-check"></i> Xác nhận thanh toán</button>
      <a href="select_combo.php" class="btn btn-secondary btn-lg ms-2"><i class="fas fa-arrow-left"></i> Quay lại chọn combo</a>
    </div>
  </form>
</div>

<script>
function submitBooking(e) {
    e.preventDefault();

    // Lấy dữ liệu từ PHP (render ra JS)
    var selectedSeats = <?php echo json_encode($selected_seats); ?>;
    var showtimeId = <?php echo isset($_SESSION['showtime_id']) ? intval($_SESSION['showtime_id']) : 0; ?>;
    var totalAmount = <?php echo json_encode($total); ?>;

    // Lấy combo từ session
    var selectedCombos = <?php echo json_encode($_SESSION['selected_combos'] ?? []); ?>;

    if (!selectedSeats || selectedSeats.length === 0) {
        alert("Bạn chưa chọn ghế!");
        return false;
    }

    var bookingData = {
        showtime_id: showtimeId,
        seats: selectedSeats,
        total_amount: totalAmount,
        combos: selectedCombos
    };

    // Gửi AJAX
    fetch("../actions/process_booking.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(bookingData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Đặt vé thành công! Mã đặt vé: " + data.booking_code);
            window.location.href = "booking_history.php";
        } else {
            alert("Lỗi: " + data.message);
        }
    })
    .catch(err => {
        alert("Có lỗi xảy ra khi gửi dữ liệu!");
        console.error(err);
    });

    return false;
}
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">