<?php 
// Đảm bảo session được khởi tạo
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'admin/config/config.php'; 
?>

<!-- CSS và JavaScript đã được tách ra file riêng: css/theater.css và js/theater.js -->

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

<!-- CSS và JavaScript đã được tách ra file riêng -->