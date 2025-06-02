<div id="content">
        <?php if(isset($_GET['quanly'])
        ) {
           $tam = $_GET['quanly'];
        }else{
            $tam = '';
        }if($tam == 'dangnhap'){
            include 'pages/pages/login.php';
        }elseif($tam == 'dangky'){
            include 'pages/pages/register.php';
        }elseif($tam == 'phim'){
            include 'pages/layout/sidebar.php';
            include 'pages/pages/movie.php';
        }elseif($tam == 'rap'){
            include 'pages/pages/theater.php';
        }elseif($tam == 'tintuc'){
            include 'pages/newsPages.php';
        }else{
            include 'pages/homePage.php';
        } ?>
</div>
<div class="clear"></div>