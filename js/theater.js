$(document).ready(function () {
  function showTheaters(city) {
    $(".cgv-theaters").removeClass("active");
    $(".cgv-city-col li").removeClass("active");
    $("#theaters-" + city).addClass("active");
    $("#city-" + city).addClass("active");
  }
  // Gán sự kiện click cho các tỉnh/thành
  $(".cgv-city-col li").click(function () {
    var id = $(this).attr("id");
    if (id && id.startsWith("city-")) {
      var city = id.replace("city-", "");
      showTheaters(city);
    }
  });
  // Mặc định hiện Hồ Chí Minh
  showTheaters("hcm");
});

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
