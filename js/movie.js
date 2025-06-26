$(document).ready(function () {
  // Function để hiển thị chi tiết phim
  window.showMovieDetail = function (movieId) {
    // Hiển thị loading
    $("#movieDetail").html(
      '<div style="text-align: center; padding: 40px; color: #ccc;"><p>Đang tải thông tin phim...</p></div>'
    );
    $("#movieModal").show();

    // Gửi AJAX request để lấy chi tiết phim
    $.ajax({
      url: "pages/actions/get_movie_detail.php",
      type: "POST",
      data: { movie_id: movieId },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          displayMovieDetail(response.data);
        } else {
          $("#movieDetail").html(`
                        <div style="text-align: center; padding: 40px; color: #f44336;">
                            <p>Không thể tải thông tin phim.</p>
                        </div>
                    `);
        }
      },
      error: function () {
        $("#movieDetail").html(`
                    <div style="text-align: center; padding: 40px; color: #f44336;">
                        <p>Có lỗi xảy ra khi tải thông tin phim.</p>
                    </div>
                `);
      },
    });
  };

  // Function để hiển thị thông tin chi tiết phim
  function displayMovieDetail(movie) {
    const html = `
            <div class="movie-detail">
                <img src="${movie.poster_url}" alt="${movie.title}">
                <div class="movie-info-detail">
                    <h2>${movie.title}</h2>
                    <p><strong>Thể loại:</strong> <span class="genre">${
                      movie.genre
                    }</span></p>
                    <p><strong>Thời lượng:</strong> ${movie.duration} phút</p>
                    <p><strong>Đạo diễn:</strong> ${
                      movie.director || "Đang cập nhật"
                    }</p>
                    <p><strong>Diễn viên:</strong> ${
                      movie.cast || "Đang cập nhật"
                    }</p>
                    <p><strong>Đánh giá:</strong> <span class="rating">${
                      movie.rating || "Chưa có"
                    }/10</span></p>
                    <p><strong>Mô tả:</strong></p>
                    <p>${
                      movie.description || "Thông tin mô tả đang được cập nhật."
                    }</p>
                    <div style="margin-top: 20px;">
                        <button class="btn-book" onclick="bookMovie(${
                          movie.id
                        })">Đặt vé ngay</button>
                    </div>
                </div>
            </div>
        `;
    $("#movieDetail").html(html);
  }

  // Function để đặt vé phim - phiên bản đơn giản nhất
  window.bookMovie = function (movieId) {
    console.log("bookMovie called with:", movieId);

    // Debug: in ra URL sẽ chuyển đến
    const url = "index.php?quanly=ve&movie_id=" + movieId;
    console.log("URL to redirect:", url);

    // Chuyển trang đơn giản
    setTimeout(function () {
      window.location = url;
    }, 100);
  };

  // Đóng modal khi click nút close
  $(".close").on("click", function () {
    $("#movieModal").hide();
  });

  // Đóng modal khi click vào nền
  $(window).on("click", function (event) {
    const $modal = $("#movieModal");
    if (event.target === $modal[0]) {
      $modal.hide();
    }
  });

  // Đóng modal bằng phím ESC
  $(document).on("keydown", function (event) {
    if (event.key === "Escape") {
      $("#movieModal").hide();
    }
  });

  // Hover effects cho movie cards
  $(".movie-card").hover(
    function () {
      $(this).css("transform", "translateY(-5px)");
      $(this).find(".movie-overlay").css("opacity", "1");
    },
    function () {
      $(this).css("transform", "translateY(0)");
      $(this).find(".movie-overlay").css("opacity", "0");
    }
  );

  // Hover effects cho buttons
  $(".btn-detail, .btn-book").hover(
    function () {
      $(this).css("transform", "scale(1.05)");
    },
    function () {
      $(this).css("transform", "scale(1)");
    }
  );
});
