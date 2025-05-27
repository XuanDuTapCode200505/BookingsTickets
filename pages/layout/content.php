        <div id="content">
            <?php include 'pages/secondary_pages/sidebar.php'; ?>
            <div class="main-content">
                <?php if(isset($_GET['quanly'])
                ) {
            $tam = $_GET['quanly'];
                }else{
                    $tam = '';
                }if($tam == 'dangnhap'){
                    include 'pages/secondary_pages/dangnhap.php';
                }elseif($tam == 'phim'){
                    include 'pages/secondary_pages/phim.php';
                }elseif($tam == 'rap'){
                    include 'pages/secondary_pages/rap.php';
                }elseif($tam == 'tintuc'){
                    include 'pages/secondary_pages/tintuc.php';
                }else{
                    include 'pages/secondary_pages/trangchu.php';
                } ?>
            </div>
        </div>
        <div class="clear"></div>