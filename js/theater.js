// Global function để có thể gọi từ HTML onclick
window.showTheaters = function (city) {
  console.log("Showing theaters for:", city);

  // Hide all theater containers
  $(".cgv-theaters").hide().removeClass("active");
  $(".cgv-city-col li").removeClass("active");

  // Show selected theater container
  $("#theaters-" + city)
    .show()
    .addClass("active");
  $("#city-" + city).addClass("active");

  // Debug log
  console.log("Active theater container:", "#theaters-" + city);
  console.log("Found elements:", $("#theaters-" + city).length);
};

$(document).ready(function () {
  // Gán sự kiện click cho các tỉnh/thành
  $(".cgv-city-col li").click(function () {
    var id = $(this).attr("id");
    if (id && id.startsWith("city-")) {
      var city = id.replace("city-", "");
      showTheaters(city);
    }
  });

  // Gán sự kiện click cho các rạp
  $(document).on("click", ".cgv-theater-list li", function () {
    var theaterName = $(this).text();
    showTheaterInfo(theaterName);
  });

  // Mặc định hiện Hồ Chí Minh
  setTimeout(function () {
    showTheaters("hcm");
  }, 100);
});

// Function hiển thị thông tin rạp
window.showTheaterInfo = function (theaterName) {
  // Tạo modal hiển thị thông tin rạp
  const modalHtml = `
        <div id="theaterInfoModal" class="theater-modal" style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        ">
            <div class="theater-modal-content" style="
                background: #1a1a1a;
                border: 2px solid #e71a0f;
                border-radius: 10px;
                padding: 30px;
                max-width: 500px;
                width: 90%;
                color: #fff;
                position: relative;
                box-shadow: 0 10px 30px rgba(231, 26, 15, 0.3);
            ">
                <span class="theater-close" style="
                    position: absolute;
                    top: 15px;
                    right: 20px;
                    font-size: 28px;
                    cursor: pointer;
                    color: #e71a0f;
                ">&times;</span>
                
                <h2 style="color: #e71a0f; text-align: center; margin-bottom: 20px;">
                    🎬 ${theaterName}
                </h2>
                
                <div class="theater-info" style="margin-bottom: 20px;">
                    <p style="margin-bottom: 10px;"><strong>📍 Địa chỉ:</strong> ${getTheaterAddress(
                      theaterName
                    )}</p>
                    <p style="margin-bottom: 10px;"><strong>📞 Hotline:</strong> 1900 6017</p>
                    <p style="margin-bottom: 10px;"><strong>🎭 Số phòng chiếu:</strong> ${getTheaterScreens(
                      theaterName
                    )}</p>
                    <p style="margin-bottom: 15px;"><strong>⏰ Giờ hoạt động:</strong> 9:00 - 23:00</p>
                </div>
                
                <div class="theater-actions" style="text-align: center; margin-top: 20px;">
                    <button onclick="viewTheaterShowtimes('${theaterName}')" style="
                        background: #e71a0f;
                        color: white;
                        border: none;
                        padding: 12px 25px;
                        border-radius: 5px;
                        cursor: pointer;
                        margin: 0 10px;
                        font-size: 14px;
                        transition: all 0.3s ease;
                    ">🎫 Xem Lịch Chiếu</button>
                    
                    <button onclick="openGoogleMaps('${theaterName}')" style="
                        background: #4CAF50;
                        color: white;
                        border: none;
                        padding: 12px 25px;
                        border-radius: 5px;
                        cursor: pointer;
                        margin: 0 10px;
                        font-size: 14px;
                        transition: all 0.3s ease;
                    ">🗺️ Bản Đồ</button>
                </div>
            </div>
        </div>
    `;

  // Xóa modal cũ nếu có
  $("#theaterInfoModal").remove();

  // Thêm modal mới
  $("body").append(modalHtml);

  // Sự kiện đóng modal
  $(".theater-close, #theaterInfoModal").click(function (e) {
    if (e.target === this) {
      $("#theaterInfoModal").remove();
    }
  });

  // Animation
  $("#theaterInfoModal").hide().fadeIn(300);
};

// Function lấy địa chỉ rạp (data mẫu)
function getTheaterAddress(theaterName) {
  const addresses = {
    "CGV Vincom Center Landmark 81":
      "Tầng 3 & 4, Vincom Center Landmark 81, 772 Điện Biên Phủ, Quận Bình Thạnh",
    "CGV Crescent Mall": "Tầng 5, Crescent Mall, 101 Tôn Dật Tiên, Quận 7",
    "CGV Vincom Center Bà Triệu":
      "Tầng 4 & 5, Vincom Center Bà Triệu, 191 Bà Triệu, Hai Bà Trưng, Hà Nội",
    "CGV Vĩnh Trung Plaza":
      "Tầng 4, Vĩnh Trung Plaza, 255-257 Hùng Vương, Thanh Khê, Đà Nẵng",
    "CGV Sense City":
      "Tầng 4, Sense City, 12 Nguyễn Văn Linh, Ninh Kiều, Cần Thơ",
  };
  return addresses[theaterName] || "Địa chỉ chi tiết sẽ được cập nhật";
}

// Function lấy số phòng chiếu (data mẫu)
function getTheaterScreens(theaterName) {
  const screens = {
    "CGV Vincom Center Landmark 81": "8 phòng",
    "CGV Crescent Mall": "6 phòng",
    "CGV Vincom Center Bà Triệu": "7 phòng",
    "CGV Vĩnh Trung Plaza": "5 phòng",
    "CGV Sense City": "4 phòng",
  };
  return screens[theaterName] || "5-8 phòng";
}

// Function xem lịch chiếu rạp
window.viewTheaterShowtimes = function (theaterName) {
  $("#theaterInfoModal").remove();

  // Chuyển đến trang đặt vé với thông tin rạp
  const theaterParam = encodeURIComponent(theaterName);
  window.location.href = `index.php?quanly=ve&theater=${theaterParam}`;
};

// Function mở Google Maps
window.openGoogleMaps = function (theaterName) {
  const query = encodeURIComponent(theaterName + " CGV Vietnam");
  const url = `https://www.google.com/maps/search/?api=1&query=${query}`;
  window.open(url, "_blank");
};

$(document).ready(function () {
  // Function để hiển thị lịch chiếu
  window.showTheaterShowtimes = function (theaterId) {
    // Hiển thị modal
    $("#showtimesModal").show();

    // Lấy thông tin rạp
    const theaterName = $(
      `.theater-card[data-theater-id="${theaterId}"] h3`
    ).text();
    $("#modalTheaterName").text(theaterName);

    // Hiển thị loading
    $("#showtimesList").html(
      '<div style="text-align: center; padding: 40px; color: #ccc;"><p>Đang tải lịch chiếu...</p></div>'
    );

    // Gửi AJAX request để lấy lịch chiếu
    $.ajax({
      url: "pages/actions/get_theater_showtimes.php",
      type: "POST",
      data: { theater_id: theaterId },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          displayShowtimes(response.data);
        } else {
          $("#showtimesList").html(`
                        <div style="text-align: center; padding: 40px; color: #ccc;">
                            <p>Không có lịch chiếu nào cho rạp này.</p>
                        </div>
                    `);
        }
      },
      error: function () {
        $("#showtimesList").html(`
                    <div style="text-align: center; padding: 40px; color: #f44336;">
                        <p>Có lỗi xảy ra khi tải lịch chiếu. Vui lòng thử lại!</p>
                    </div>
                `);
      },
    });
  };

  // Function để hiển thị danh sách lịch chiếu
  function displayShowtimes(movies) {
    if (!movies || movies.length === 0) {
      $("#showtimesList").html(`
                <div style="text-align: center; padding: 40px; color: #ccc;">
                    <p>Không có lịch chiếu nào cho rạp này.</p>
                </div>
            `);
      return;
    }

    let html = "";
    movies.forEach((movie) => {
      html += `
                <div class="movie-showtimes">
                    <div class="movie-header">
                        <img src="${movie.poster_url}" alt="${movie.title}" 
                             style="width: 60px; height: 80px; object-fit: cover; border-radius: 5px;">
                        <div class="movie-info">
                            <h3>${movie.title}</h3>
                            <p>${movie.genre} • ${movie.duration} phút</p>
                        </div>
                    </div>
                    <div class="showtimes">
            `;

      movie.showtimes.forEach((showtime) => {
        html += `
                    <button class="showtime-btn" onclick="bookShowtime(${
                      showtime.id
                    })">
                        <div>${showtime.show_time}</div>
                        <div class="price">${formatPrice(showtime.price)}</div>
                    </button>
                `;
      });

      html += `
                    </div>
                </div>
            `;
    });

    $("#showtimesList").html(html);
  }

  // Function để đặt vé cho suất chiếu
  window.bookShowtime = function (showtimeId) {
    window.location.href = `index.php?quanly=ve&showtime_id=${showtimeId}`;
  };

  // Function để mở Google Maps
  window.openMap = function (latitude, longitude, theaterName) {
    const url = `https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}&query_place_id=${encodeURIComponent(
      theaterName
    )}`;
    window.open(url, "_blank");
  };

  // Function để format giá
  function formatPrice(price) {
    return new Intl.NumberFormat("vi-VN", {
      style: "currency",
      currency: "VND",
    }).format(price);
  }

  // Đóng modal khi click vào nền
  $(window).on("click", function (event) {
    const $modal = $("#showtimesModal");
    if (event.target === $modal[0]) {
      $modal.hide();
    }
  });

  // Đóng modal khi click nút close
  $(".close").on("click", function () {
    $("#showtimesModal").hide();
  });

  // Đóng modal bằng phím ESC
  $(document).on("keydown", function (event) {
    if (event.key === "Escape") {
      $("#showtimesModal").hide();
    }
  });

  // Hover effects cho theater cards
  $(".theater-card").hover(
    function () {
      $(this)
        .css("transform", "translateY(-5px)")
        .css("border-color", "#e50914");
    },
    function () {
      $(this).css("transform", "translateY(0)").css("border-color", "#333");
    }
  );

  // Hover effects cho buttons
  $(".btn-showtimes, .btn-map").hover(
    function () {
      $(this).css("transform", "scale(1.05)");
    },
    function () {
      $(this).css("transform", "scale(1)");
    }
  );
});
