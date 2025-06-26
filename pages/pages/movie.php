<?php require_once 'admin/config/config.php'; ?>

<div class="main-content">
    <div class="movies-container">
        <h2 style="color: #fff; text-align: center; margin: 20px 0;">DANH SÁCH PHIM</h2>
        
        <!-- Bộ lọc thể loại -->
        <div class="filter-section" style="text-align: center; margin: 20px 0;">
            <?php
            $selected_genre = isset($_GET['genre']) ? $_GET['genre'] : '';
            if ($selected_genre) {
                echo '<p style="color: #e71a0f; font-size: 18px; margin-bottom: 10px;">Thể loại: ' . htmlspecialchars($selected_genre) . '</p>';
                echo '<a href="index.php?quanly=phim" style="color: #ccc; text-decoration: none;">← Xem tất cả phim</a>';
            }
            ?>
        </div>
        
        <div class="movies-grid">
            <?php
            // Xây dựng câu query dựa trên filter
            $sql = "SELECT * FROM movies WHERE status = 'showing'";
            if ($selected_genre) {
                $sql .= " AND genre LIKE '%" . mysqli_real_escape_string($conn, $selected_genre) . "%'";
            }
            $sql .= " ORDER BY created_at DESC";
            
            $result = mysqli_query($conn, $sql);
            
            if ($result && mysqli_num_rows($result) > 0) {
                while($movie = mysqli_fetch_assoc($result)) {
                    echo '<div class="movie-card">';
                    echo '<div class="movie-poster">';
                    echo '<img src="' . $movie['poster_url'] . '" alt="' . htmlspecialchars($movie['title']) . '">';
                    echo '<div class="movie-overlay">';
                    echo '<button class="btn-detail" onclick="showMovieDetail(' . $movie['id'] . ')">Chi tiết</button>';
                    echo '<button class="btn-book" onclick="bookMovie(' . $movie['id'] . ')">Đặt vé</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="movie-info">';
                    echo '<h3>' . htmlspecialchars($movie['title']) . '</h3>';
                    echo '<p class="genre">' . htmlspecialchars($movie['genre']) . '</p>';
                    echo '<p class="duration">' . $movie['duration'] . ' phút</p>';
                    echo '<p class="rating">⭐ ' . $movie['rating'] . '/10</p>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p style="color: #fff; text-align: center;">Hiện tại không có phim nào đang chiếu.</p>';
            }
            ?>
        </div>
    </div>
</div>

<!-- Modal chi tiết phim -->
<div id="movieModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="movieDetails"></div>
    </div>
</div>

<script>
function showMovieDetail(movieId) {
    // Gọi AJAX để lấy chi tiết phim
    fetch('pages/actions/get_movie_detail.php?id=' + movieId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const movie = data.movie;
                document.getElementById('movieDetails').innerHTML = `
                    <div class="movie-detail">
                        <img src="${movie.poster_url}" alt="${movie.title}" style="width: 200px; float: left; margin-right: 20px;">
                        <div class="movie-info-detail">
                            <h2>${movie.title}</h2>
                            <p><strong>Thể loại:</strong> ${movie.genre}</p>
                            <p><strong>Thời lượng:</strong> ${movie.duration} phút</p>
                            <p><strong>Ngày khởi chiếu:</strong> ${movie.release_date}</p>
                            <p><strong>Đánh giá:</strong> ⭐ ${movie.rating}/10</p>
                            <p><strong>Mô tả:</strong></p>
                            <p>${movie.description}</p>
                        </div>
                        <div class="clear"></div>
                    </div>
                `;
                document.getElementById('movieModal').style.display = 'block';
            }
        });
}

function bookMovie(movieId) {
    <?php if (isset($_SESSION['user_id'])): ?>
        // Đã đăng nhập, chuyển đến trang đặt vé
        window.location.href = 'index.php?quanly=ve&movie_id=' + movieId;
    <?php else: ?>
        // Chưa đăng nhập, yêu cầu đăng nhập
        alert('Vui lòng đăng nhập để đặt vé!');
        window.location.href = 'index.php?quanly=dangnhap';
    <?php endif; ?>
}

// Đóng modal
document.getElementsByClassName('close')[0].onclick = function() {
    document.getElementById('movieModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('movieModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

<style>
.movies-container {
    padding: 30px;
    background-color: #000;
    max-width: 1200px;
    margin: 0 auto;
}

.movies-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
    margin-top: 30px;
}

.movie-card {
    background-color: #1a1a1a;
    border-radius: 10px;
    overflow: hidden;
    transition: transform 0.3s ease;
    position: relative;
}

.movie-card:hover {
    transform: translateY(-5px);
}

.movie-poster {
    position: relative;
    overflow: hidden;
}

.movie-poster img {
    width: 100%;
    height: 400px;
    object-fit: cover;
}

.movie-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.movie-card:hover .movie-overlay {
    opacity: 1;
}

.btn-detail, .btn-book {
    padding: 12px 24px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
    transition: all 0.3s ease;
}

.btn-detail {
    background-color: #666;
    color: white;
}

.btn-book {
    background-color: #e50914;
    color: white;
}

.btn-detail:hover, .btn-book:hover {
    transform: scale(1.05);
}

.movie-info {
    padding: 18px;
    color: white;
}

.movie-info h3 {
    margin-bottom: 10px;
    font-size: 20px;
}

.movie-info p {
    margin: 5px 0;
    color: #ccc;
}

.genre {
    color: #e50914 !important;
    font-weight: bold;
}

.rating {
    color: #ffd700 !important;
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.9);
}

.modal-content {
    background-color: #1a1a1a;
    margin: 5% auto;
    padding: 20px;
    border-radius: 10px;
    width: 80%;
    max-width: 800px;
    color: white;
    position: relative;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    position: absolute;
    right: 20px;
    top: 15px;
}

.close:hover {
    color: #fff;
}

.movie-detail img {
    border-radius: 10px;
}

.movie-info-detail h2 {
    color: #e50914;
    margin-bottom: 15px;
}

.movie-info-detail p {
    margin: 10px 0;
    line-height: 1.6;
}

/* Responsive cho movies grid */
@media (max-width: 1200px) {
    .movies-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
    }
}

@media (max-width: 900px) {
    .movies-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .movies-container {
        padding: 20px;
    }
}

@media (max-width: 600px) {
    .movies-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .movies-container {
        padding: 15px;
    }
}
</style>