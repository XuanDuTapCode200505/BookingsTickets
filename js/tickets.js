$(document).ready(function () {
  // Khởi tạo biến global
  window.selectedSeats = [];
  window.ticketPrice = 0;
  window.showtimeId = 0;

  // Lấy dữ liệu từ div hidden
  const ticketsData = $("#tickets-data");
  if (ticketsData.length) {
    window.ticketPrice = parseInt(ticketsData.data("ticket-price")) || 0;
    window.showtimeId = parseInt(ticketsData.data("showtime-id")) || 0;
  }

  // Backup: Lấy từ element price nếu có
  if (window.ticketPrice === 0) {
    const priceText = $("#ticket-price").text();
    if (priceText) {
      window.ticketPrice = parseInt(priceText.replace(/[^\d]/g, ""));
    }
  }
});

// Chọn suất chiếu
function selectShowtime(id) {
  window.location.href = `index.php?quanly=ve&showtime_id=${id}`;
}

// Chọn ghế
function selectSeat(seatElement) {
  const $seat = $(seatElement);
  const seatData = {
    id: $seat.data("seat"),
    row: $seat.data("row"),
    number: $seat.data("number"),
    type: $seat.hasClass("vip") ? "vip" : "standard",
  };
  seatData.price =
    seatData.type === "vip" ? window.ticketPrice * 1.5 : window.ticketPrice;

  if ($seat.hasClass("selected")) {
    // Bỏ chọn ghế
    $seat.removeClass("selected");
    window.selectedSeats = window.selectedSeats.filter(
      (seat) => seat.id !== seatData.id
    );
  } else {
    // Chọn ghế
    if (window.selectedSeats.length >= 8) {
      alert("Bạn chỉ có thể đặt tối đa 8 vé!");
      return;
    }
    $seat.addClass("selected");
    window.selectedSeats.push(seatData);
  }

  updateBookingSummary();
}

// Cập nhật tóm tắt đặt vé
function updateBookingSummary() {
  const seatIds = window.selectedSeats.map((seat) => seat.id).join(", ");
  const ticketCount = window.selectedSeats.length;
  const totalAmount = window.selectedSeats.reduce(
    (total, seat) => total + seat.price,
    0
  );

  // Cập nhật UI
  $("#selected-seats").text(seatIds || "Chưa chọn ghế");
  $("#ticket-count").text(ticketCount);
  $("#total-amount").text(formatNumber(totalAmount));

  // Cập nhật button đặt vé
  const $bookBtn = $("#btn-book-tickets");
  if (ticketCount > 0) {
    $bookBtn.prop("disabled", false).css("background-color", "#e50914");
  } else {
    $bookBtn.prop("disabled", true).css("background-color", "#666");
  }
}

// Đặt vé
function bookTickets() {
  if (window.selectedSeats.length === 0) {
    alert("Vui lòng chọn ít nhất một ghế!");
    return;
  }

  const totalAmount = window.selectedSeats.reduce(
    (total, seat) => total + seat.price,
    0
  );
  const bookingData = {
    showtime_id: window.showtimeId,
    seats: window.selectedSeats,
    total_amount: totalAmount,
  };

  // Nếu đã có trang chọn combo, ưu tiên chuyển sang đó
  if (typeof window.ENABLE_COMBO !== 'undefined' || true) { // luôn chuyển sang combo nếu có
    localStorage.setItem("pendingBooking", JSON.stringify(bookingData));
    window.location.href = "pages/pages/select_combo.php";
    return;
  }

  // Nếu không có trang combo, giữ nguyên luồng cũ
  // Hiển thị loading
  const $bookBtn = $("#btn-book-tickets");
  const originalText = $bookBtn.text();
  $bookBtn.text("ĐANG XỬ LÝ...").prop("disabled", true);

  // Gửi request đặt vé
  $.ajax({
    url: "pages/actions/process_booking.php",
    type: "POST",
    contentType: "application/json",
    data: JSON.stringify(bookingData),
    dataType: "json",
    success: function (data) {
      if (data.success) {
        alert(`Đặt vé thành công! Mã đặt vé: ${data.booking_code}`);
        window.location.href = "booking_history.php";
      } else {
        alert(`Có lỗi xảy ra: ${data.message}`);
        resetBookButton($bookBtn, originalText);
      }
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
      alert("Có lỗi xảy ra khi đặt vé!");
      resetBookButton($bookBtn, originalText);
    },
  });
}

// Khôi phục button đặt vé
function resetBookButton($btn, originalText) {
  $btn.text(originalText).prop("disabled", false);
}

// Utility functions
function formatNumber(number) {
  return new Intl.NumberFormat("vi-VN").format(number);
}

function formatCurrency(amount) {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(amount);
}

// Event handlers với jQuery
$(document).ready(function () {
  // Hover effects cho ghế
  $(document)
    .on("mouseenter", ".seat.available:not(.selected)", function () {
      $(this).css("transform", "scale(1.1)").css("border-color", "#e50914");
    })
    .on("mouseleave", ".seat.available:not(.selected)", function () {
      $(this).css("transform", "scale(1)").css("border-color", "#444");
    });

  // Hover effects cho button
  $(document)
    .on("mouseenter", ".btn-select-showtime, .btn-back", function () {
      $(this).css("transform", "scale(1.05)");
    })
    .on("mouseleave", ".btn-select-showtime, .btn-back", function () {
      $(this).css("transform", "scale(1)");
    });

  // Hover effect cho button đặt vé
  $(document)
    .on("mouseenter", ".btn-book-tickets:not(:disabled)", function () {
      $(this).css({
        "background-color": "#cc0812",
        transform: "scale(1.02)",
      });
    })
    .on("mouseleave", ".btn-book-tickets:not(:disabled)", function () {
      $(this).css({
        "background-color": "#e50914",
        transform: "scale(1)",
      });
    });
});
