<?php
require_once '../config/database.php';
require_once '../config/constants.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
require_login();
require_admin();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - لوحة التحكم</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- IconScout Unicons -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo ROOT_URL; ?>style.css">
    <!-- Notifications CSS -->
    <link rel="stylesheet" href="<?php echo ROOT_URL; ?>admin/css/notifications.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="container nav__container">
            <a href="<?php echo ROOT_URL; ?>index.php" class="nav__logo"><i class="uil uil-blog"></i> مدونتي</a>
            <ul class="nav__items">
                <li><a href="<?php echo ROOT_URL; ?>blog.php"><i class="uil uil-document-layout-left"></i> المدونة</a></li>
                <li><a href="<?php echo ROOT_URL; ?>about.php"><i class="uil uil-info-circle"></i> من نحن</a></li>
                <li><a href="<?php echo ROOT_URL; ?>services.php"><i class="uil uil-briefcase-alt"></i> خدماتنا</a></li>
                <li><a href="<?php echo ROOT_URL; ?>contact-new.php"><i class="uil uil-envelope"></i> اتصل بنا</a></li>
                <li class="nav__profile">
                    <div class="avatar">
                        <img src="<?php echo ROOT_URL . 'images/users/' . $_SESSION['user_avatar']; ?>" alt="صورة الملف الشخصي">
                    </div>
                    <ul>
                        <li><a href="<?php echo ROOT_URL; ?>admin/dashboard.php"><i class="uil uil-dashboard"></i> لوحة التحكم</a></li>
                        <li><a href="<?php echo ROOT_URL; ?>profile.php"><i class="uil uil-user-circle"></i> ملفي الشخصي</a></li>
                        <li><a href="<?php echo ROOT_URL; ?>logout.php"><i class="uil uil-signout"></i> تسجيل الخروج</a></li>
                    </ul>
                </li>
            </ul>
            <button id="open__nav-btn"><i class="uil uil-bars"></i></button>
            <button id="close__nav-btn"><i class="uil uil-multiply"></i></button>
        </div>
    </nav>
    <!-- End of Navigation -->

    <!-- Hidden Message Container (Will be transformed to notifications) -->
    <div class="hidden-messages" style="display: none;">
        <?php display_message(); ?>
    </div>
