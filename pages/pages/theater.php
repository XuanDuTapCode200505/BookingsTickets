<?php require_once 'admin/config/config.php'; ?>

<style>
.cgv-container {
    background: #000;
    color: #fff;
    border: 3px solid #444;
    border-radius: 10px;
    padding: 30px 20px 20px 20px;
    max-width: 1000px;
    margin: 40px auto;
    font-family: Arial, Helvetica, sans-serif;
}
.cgv-title {
    text-align: center;
    font-size: 3em;
    font-weight: bold;
    letter-spacing: 2px;
    color: #bdbdbd;
    text-shadow: 2px 2px 6px #000;
    margin-bottom: 30px;
}
.cgv-divider {
    border: none;
    border-top: 2px solid #666;
    margin: 20px 0;
}
.cgv-cities {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    margin-bottom: 20px;
}
.cgv-city-col {
    flex: 1 1 18%;
    min-width: 150px;
    margin-bottom: 10px;
}
.cgv-city-col ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.cgv-city-col li {
    margin-bottom: 6px;
    font-size: 1.1em;
    cursor: pointer;
    transition: color 0.2s;
}
.cgv-city-col li:hover, .cgv-city-col .active {
    color: #e74c3c;
    font-weight: bold;
}
.cgv-theaters {
    margin-top: 20px;
    display: none;
    animation: fadeIn 0.4s;
}
.cgv-theaters.active {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start;
    gap: 30px 40px;
}
.cgv-theater-list {
    flex: 1 1 22%;
    min-width: 220px;
}
.cgv-theater-list ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.cgv-theater-list li {
    margin-bottom: 8px;
    font-size: 1em;
}
@media (max-width: 900px) {
    .cgv-cities, .cgv-theaters.active { flex-direction: column; }
    .cgv-city-col, .cgv-theater-list { min-width: unset; }
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>
<div class="cgv-container">
    <div class="cgv-title">CGV CINEMAS</div>
    <hr class="cgv-divider">
    <div class="cgv-cities">
        <div class="cgv-city-col">
            <ul>
                <li onclick="showTheaters('hcm')" id="city-hcm" class="active">Hồ Chí Minh</li>
                <li onclick="showTheaters('hp')" id="city-hp">Hải Phòng</li>
                <li onclick="showTheaters('dlk')" id="city-dlk">Đắk Lắk</li>
                <li onclick="showTheaters('hg')" id="city-hg">Hậu Giang</li>
                <li onclick="showTheaters('hy')" id="city-hy">Hưng Yên</li>
                <li onclick="showTheaters('pt')" id="city-pt">Phú Thọ</li>
                <li onclick="showTheaters('tn')" id="city-tn">Thái Nguyên</li>
            </ul>
        </div>
        <div class="cgv-city-col">
            <ul>
                <li onclick="showTheaters('hn')" id="city-hn">Hà Nội</li>
                <li onclick="showTheaters('qn')" id="city-qn">Quảng Ninh</li>
                <li onclick="showTheaters('tv')" id="city-tv">Trà Vinh</li>
                <li onclick="showTheaters('ht')" id="city-ht">Hà Tĩnh</li>
                <li onclick="showTheaters('kh')" id="city-kh">Khánh Hòa</li>
                <li onclick="showTheaters('qng')" id="city-qng">Quảng Ngãi</li>
                <li onclick="showTheaters('tg')" id="city-tg">Tiền Giang</li>
            </ul>
        </div>
        <div class="cgv-city-col">
            <ul>
                <li onclick="showTheaters('dn')" id="city-dn">Đà Nẵng</li>
                <li onclick="showTheaters('brvt')" id="city-brvt">Bà Rịa-Vũng Tàu</li>
                <li onclick="showTheaters('yb')" id="city-yb">Yên Bái</li>
                <li onclick="showTheaters('py')" id="city-py">Phú Yên</li>
                <li onclick="showTheaters('kt')" id="city-kt">Kon Tum</li>
                <li onclick="showTheaters('st')" id="city-st">Sóc Trăng</li>
            </ul>
        </div>
        <div class="cgv-city-col">
            <ul>
                <li onclick="showTheaters('ct')" id="city-ct">Cần Thơ</li>
                <li onclick="showTheaters('bd')" id="city-bd">Bình Định</li>
                <li onclick="showTheaters('vl')" id="city-vl">Vĩnh Long</li>
                <li onclick="showTheaters('dt')" id="city-dt">Đồng Tháp</li>
                <li onclick="showTheaters('ls')" id="city-ls">Lạng Sơn</li>
                <li onclick="showTheaters('sl')" id="city-sl">Sơn La</li>
            </ul>
        </div>
        <div class="cgv-city-col">
            <ul>
                <li onclick="showTheaters('dnai')" id="city-dnai">Đồng Nai</li>
                <li onclick="showTheaters('bdg')" id="city-bdg">Bình Dương</li>
                <li onclick="showTheaters('kg')" id="city-kg">Kiên Giang</li>
                <li onclick="showTheaters('bl')" id="city-bl">Bạc Liêu</li>
                <li onclick="showTheaters('na')" id="city-na">Nghệ An</li>
                <li onclick="showTheaters('tnh')" id="city-tnh">Tây Ninh</li>
            </ul>
        </div>
    </div>
    <hr class="cgv-divider">
    <div id="theaters-hcm" class="cgv-theaters active">
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Hùng Vương Plaza</li>
                <li>CGV Vivo City</li>
                <li>CGV Menas Mall (CGV CT Plaza)</li>
                <li>CGV Hoàng Văn Thụ</li>
                <li>CGV Vincom Center Landmark 81</li>
                <li>CGV Vincom Mega Mall Grand Park</li>
            </ul>
        </div>
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Crescent Mall</li>
                <li>CGV Pearl Plaza</li>
                <li>CGV Pandora City</li>
                <li>CGV Aeon Bình Tân</li>
                <li>CGV Satra Củ Chi</li>
            </ul>
        </div>
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Thảo Điền Pearl</li>
                <li>CGV Liberty Citypoint</li>
                <li>CGV Aeon Tân Phú</li>
                <li>CGV Saigonres Nguyễn Xí</li>
                <li>CGV Gigamall Thủ Đức</li>
            </ul>
        </div>
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Vincom Thủ Đức</li>
                <li>CGV Vincom Đồng Khởi</li>
                <li>CGV Vincom Gò Vấp</li>
                <li>CGV Sư Vạn Hạnh</li>
                <li>CGV Lý Chính Thắng</li>
            </ul>
        </div>
    </div>
    <!-- Hà Nội -->
    <div id="theaters-hn" class="cgv-theaters">
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Vincom Center Bà Triệu</li>
                <li>CGV Hồ Gươm Plaza</li>
                <li>CGV Aeon Long Biên</li>
                <li>CGV Vincom Nguyễn Chí Thanh</li>
            </ul>
        </div>
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Indochina Plaza Hà Nội</li>
                <li>CGV Rice City</li>
                <li>CGV Hà Nội Centerpoint</li>
                <li>CGV Vincom Royal City</li>
            </ul>
        </div>
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Vincom Times City</li>
                <li>CGV Vincom Long Biên</li>
                <li>CGV Mac Plaza (Machinco)</li>
                <li>CGV Trương Định Plaza</li>
            </ul>
        </div>
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Tràng Tiền Plaza</li>
                <li>CGV Sun Grand Thụy Khuê</li>
                <li>CGV Sun Grand Lương Yên</li>
                <li>CGV Vincom Bắc Từ Liêm</li>
            </ul>
        </div>
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Vincom Metropolis Liễu Giai</li>
                <li>CGV Xuân Diệu</li>
                <li>CGV Vincom Sky Lake Phạm Hùng</li>
                <li>CGV Vincom Trần Duy Hưng</li>
            </ul>
        </div>
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Aeon Hà Đông</li>
                <li>CGV Vincom Ocean Park</li>
            </ul>
        </div>
    </div>
    <!-- Đà Nẵng -->
    <div id="theaters-dn" class="cgv-theaters">
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Vĩnh Trung Plaza</li>
                <li>CGV Vincom Đà Nẵng</li>
            </ul>
        </div>
    </div>
    <!-- Cần Thơ -->
    <div id="theaters-ct" class="cgv-theaters">
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Sense City</li>
                <li>CGV Vincom Xuân Khánh</li>
                <li>CGV Vincom Hùng Vương</li>
            </ul>
        </div>
    </div>
    <!-- Đồng Nai -->
    <div id="theaters-dnai" class="cgv-theaters">
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Coopmart Biên Hòa</li>
                <li>CGV Big C Đồng Nai</li>
            </ul>
        </div>
    </div>
    <!-- Hải Phòng -->
    <div id="theaters-hp" class="cgv-theaters">
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Vincom Hải Phòng</li>
                <li>CGV Aeon Mall Hải Phòng</li>
            </ul>
        </div>
    </div>
    <!-- Quảng Ninh -->
    <div id="theaters-qn" class="cgv-theaters">
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Vincom Hạ Long</li>
                <li>CGV Vincom Móng Cái</li>
                <li>CGV Vincom Cẩm Phả</li>
            </ul>
        </div>
    </div>
    <!-- Bà Rịa-Vũng Tàu -->
    <div id="theaters-brvt" class="cgv-theaters">
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Lam Sơn Square</li>
                <li>CGV Lapen Center Vũng Tàu</li>
            </ul>
        </div>
    </div>
    <!-- Bình Định -->
    <div id="theaters-bd" class="cgv-theaters">
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Kim Cúc Plaza</li>
            </ul>
        </div>
    </div>
    <!-- Bình Dương -->
    <div id="theaters-bdg" class="cgv-theaters">
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Bình Dương Square</li>
                <li>CGV Aeon Canary</li>
            </ul>
        </div>
    </div>
    <!-- Đắk Lắk -->
    <div id="theaters-dlk" class="cgv-theaters">
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Buôn Mê Thuột</li>
            </ul>
        </div>
    </div>
    <!-- Trà  -->
    <!-- Hậu Giang -->
    <div id="theaters-hg" class="cgv-theaters">
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Hậu Giang</li>
            </ul>
        </div>
    </div>
    <!-- Hưng Yên -->
    <div id="theaters-hy" class="cgv-theaters">
        <div class="cgv-theater-list">
            <ul>
                <li>CGV Hưng Yên</li>
            </ul>
        </div>
    </div>
</div>
<script src="js/jquery-3.7.1.js"></script>
<script src="js/theater.js"></script>

<div class="main-content">
    <div class="theaters-container">
        <h2 style="color: #fff; text-align: center; margin: 20px 0;">HỆ THỐNG RẠP CGV</h2>
        
        <div class="theaters-grid">
            <?php
            $sql = "SELECT t.*, COUNT(s.id) as total_screens FROM theaters t 
                    LEFT JOIN screens s ON t.id = s.theater_id 
                    GROUP BY t.id ORDER BY t.name";
            $result = mysqli_query($conn, $sql);
            
            if ($result && mysqli_num_rows($result) > 0) {
                while($theater = mysqli_fetch_assoc($result)) {
                    echo '<div class="theater-card">';
                    echo '<div class="theater-header">';
                    echo '<h3>' . htmlspecialchars($theater['name']) . '</h3>';
                    echo '<span class="theater-screens">' . $theater['total_screens'] . ' phòng chiếu</span>';
                    echo '</div>';
                    echo '<div class="theater-info">';
                    echo '<p class="location"><i class="icon-location"></i>' . htmlspecialchars($theater['location']) . '</p>';
                    echo '<div class="theater-actions">';
                    echo '<button class="btn-showtimes" onclick="viewShowtimes(' . $theater['id'] . ')">Xem lịch chiếu</button>';
                    echo '<button class="btn-map" onclick="viewMap(\'' . htmlspecialchars($theater['location']) . '\')">Xem bản đồ</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p style="color: #fff; text-align: center;">Hiện tại không có rạp nào.</p>';
            }
            ?>
        </div>
    </div>
</div>

<!-- Modal lịch chiếu -->
<div id="showtimesModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('showtimesModal')">&times;</span>
        <div id="showtimesContent"></div>
    </div>
</div>

<script>
function viewShowtimes(theaterId) {
    fetch('pages/actions/get_theater_showtimes.php?theater_id=' + theaterId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let content = '<h2>Lịch chiếu - ' + data.theater_name + '</h2>';
                content += '<div class="showtimes-list">';
                
                if (data.movies.length > 0) {
                    data.movies.forEach(movie => {
                        content += '<div class="movie-showtimes">';
                        content += '<div class="movie-header">';
                        content += '<img src="' + movie.poster_url + '" alt="' + movie.title + '" style="width: 80px; height: 120px; object-fit: cover; border-radius: 5px;">';
                        content += '<div class="movie-info">';
                        content += '<h3>' + movie.title + '</h3>';
                        content += '<p>' + movie.genre + ' • ' + movie.duration + ' phút</p>';
                        content += '</div>';
                        content += '</div>';
                        content += '<div class="showtimes">';
                        movie.showtimes.forEach(showtime => {
                            content += '<button class="showtime-btn" onclick="bookShowtime(' + showtime.id + ')">';
                            content += showtime.show_time + '<br><span class="price">' + formatPrice(showtime.price) + '</span>';
                            content += '</button>';
                        });
                        content += '</div>';
                        content += '</div>';
                    });
                } else {
                    content += '<p>Hiện tại không có lịch chiếu nào.</p>';
                }
                
                content += '</div>';
                document.getElementById('showtimesContent').innerHTML = content;
                document.getElementById('showtimesModal').style.display = 'block';
            }
        });
}

function bookShowtime(showtimeId) {
    <?php if (isset($_SESSION['user_id'])): ?>
        // Đã đăng nhập, chuyển đến trang đặt vé
        window.location.href = 'index.php?quanly=ve&showtime_id=' + showtimeId;
    <?php else: ?>
        // Chưa đăng nhập, yêu cầu đăng nhập
        alert('Vui lòng đăng nhập để đặt vé!');
        window.location.href = 'index.php?quanly=dangnhap';
    <?php endif; ?>
}

function viewMap(location) {
    const url = 'https://www.google.com/maps/search/' + encodeURIComponent(location);
    window.open(url, '_blank');
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
}

window.onclick = function(event) {
    const modal = document.getElementById('showtimesModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

<style>
.theaters-container {
    padding: 20px;
    background-color: #000;
}

.theaters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.theater-card {
    background-color: #1a1a1a;
    border-radius: 10px;
    padding: 20px;
    transition: transform 0.3s ease;
    border: 1px solid #333;
}

.theater-card:hover {
    transform: translateY(-5px);
    border-color: #e50914;
}

.theater-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.theater-header h3 {
    color: #e50914;
    margin: 0;
    font-size: 20px;
}

.theater-screens {
    background-color: #e50914;
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
}

.theater-info {
    color: white;
}

.location {
    color: #ccc;
    margin: 10px 0;
    display: flex;
    align-items: center;
}

.theater-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn-showtimes, .btn-map {
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
}

.btn-showtimes {
    background-color: #e50914;
    color: white;
    flex: 1;
}

.btn-map {
    background-color: #666;
    color: white;
}

.btn-showtimes:hover, .btn-map:hover {
    transform: scale(1.05);
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
    margin: 2% auto;
    padding: 20px;
    border-radius: 10px;
    width: 90%;
    max-width: 1000px;
    color: white;
    position: relative;
    max-height: 90vh;
    overflow-y: auto;
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

.showtimes-list {
    margin-top: 30px;
}

.movie-showtimes {
    background-color: #2a2a2a;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.movie-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.movie-header .movie-info {
    margin-left: 15px;
}

.movie-header h3 {
    color: #e50914;
    margin: 0 0 5px 0;
}

.movie-header p {
    color: #ccc;
    margin: 0;
}

.showtimes {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.showtime-btn {
    background-color: #333;
    color: white;
    border: 1px solid #555;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    min-width: 80px;
}

.showtime-btn:hover {
    background-color: #e50914;
    border-color: #e50914;
}

.showtime-btn .price {
    font-size: 12px;
    color: #ffd700;
}
</style>