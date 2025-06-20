<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/login-form.css">
    <link rel="stylesheet" type="text/css" href="css/sidebar.css">
    <link rel="stylesheet" type="text/css" href="css/homepages.css">
    <link rel="stylesheet" type="text/css" href="css/register-form.css">
    <link rel="stylesheet" type="text/css" href="css/footer.css">
    
    <title>CGV</title>
    <link rel="icon" href="https://www.cgv.vn/media/favicon/default/cgvcinemas-vietnam-favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="container">
        <?php include 'pages/layout/header.php';
        include 'pages/layout/menu.php';
        include 'pages/layout/content.php';
        include 'pages/layout/footer.php'; ?>
    </div>
</body>
</html>