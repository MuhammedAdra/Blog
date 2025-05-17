<?php
$page_title = "Manage Users";
require_once '../config/database.php';
require_once '../config/constants.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
require_login();
require_admin();

// Pagination
$users_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $users_per_page;

// Search functionality
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$search_condition = '';
if (!empty($search)) {
    $search_condition = "AND (firstname LIKE '%$search%' OR lastname LIKE '%$search%' OR username LIKE '%$search%' OR email LIKE '%$search%')";
}

// Get total users count for pagination
$count_query = "SELECT COUNT(*) as total FROM users WHERE 1=1 $search_condition";
$count_result = $conn->query($count_query);
$total_users = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $users_per_page);

// Get users
$users_query = "SELECT * FROM users WHERE 1=1 $search_condition ORDER BY created_at DESC LIMIT $offset, $users_per_page";
$users_result = $conn->query($users_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Modern Blog</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- IconScout Unicons -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo ROOT_URL; ?>style.css">
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
                <li><a href="<?php echo ROOT_URL; ?>contact.php"><i class="uil uil-envelope"></i> اتصل بنا</a></li>
                <li class="nav__profile">
                    <div class="avatar">
                        <img src="<?php echo ROOT_URL . 'images/users/' . $_SESSION['user_avatar']; ?>" alt="صورة الملف الشخصي">
                    </div>
                    <ul>
                        <li><a href="<?php echo ROOT_URL; ?>admin/dashboard.php"><i class="uil uil-dashboard"></i> لوحة التحكم</a></li>
                        <li><a href="<?php echo ROOT_URL; ?>logout.php"><i class="uil uil-signout"></i> تسجيل الخروج</a></li>
                    </ul>
                </li>
            </ul>
            <button id="open__nav-btn"><i class="uil uil-bars"></i></button>
            <button id="close__nav-btn"><i class="uil uil-multiply"></i></button>
        </div>
    </nav>
    <!-- End of Navigation -->
    
    <!-- Display Messages -->
    <?php display_message(); ?>

    <!-- Dashboard -->
    <section class="dashboard">
        <div class="container dashboard__container">
            <!-- Dashboard Sidebar -->
            <aside class="dashboard__sidebar">
                <ul>
                    <li>
                        <a href="dashboard.php">
                            <i class="uil uil-dashboard"></i>
                            <h5>الرئيسية</h5>
                        </a>
                    </li>
                    <li>
                        <a href="add-post.php">
                            <i class="uil uil-plus-circle"></i>
                            <h5>إضافة مقال</h5>
                        </a>
                    </li>
                    <li>
                        <a href="manage-posts.php">
                            <i class="uil uil-postcard"></i>
                            <h5>إدارة المقالات</h5>
                        </a>
                    </li>
                    <li>
                        <a href="manage-categories.php">
                            <i class="uil uil-list-ul"></i>
                            <h5>إدارة الفئات</h5>
                        </a>
                    </li>
                    <li>
                        <a href="manage-users.php" class="active">
                            <i class="uil uil-users-alt"></i>
                            <h5>إدارة المستخدمين</h5>
                        </a>
                    </li>
                    <li>
                        <a href="manage-comments.php">
                            <i class="uil uil-comment-dots"></i>
                            <h5>إدارة التعليقات</h5>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo ROOT_URL; ?>profile.php">
                            <i class="uil uil-user-circle"></i>
                            <h5>تعديل الملف الشخصي</h5>
                        </a>
                    </li>
                </ul>
            </aside>
            
            <!-- Dashboard Main Content -->
            <main class="dashboard__main">
                <h2>إدارة المستخدمين</h2>
                
                <!-- Search and Filter -->
                <div class="search-and-filter">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="GET" class="search__form">
                        <div class="form__row">
                            <div class="form__control">
                                <input type="text" name="search" placeholder="بحث عن مستخدمين..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="uil uil-search"></i> بحث</button>
                            <a href="manage-users.php" class="btn btn-outline"><i class="uil uil-refresh"></i> إعادة تعيين</a>
                        </div>
                    </form>
                </div>
                
                <!-- Users Table -->
                <div class="users__table">
                    <table>
                        <thead>
                            <tr>
                                <th>الصورة</th>
                                <th>الاسم</th>
                                <th>اسم المستخدم</th>
                                <th>البريد الإلكتروني</th>
                                <th>الدور</th>
                                <th>تاريخ التسجيل</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($users_result->num_rows > 0): ?>
                                <?php while($user = $users_result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="user-avatar">
                                            <img src="<?php echo ROOT_URL . 'images/users/' . $user['avatar']; ?>" alt="صورة المستخدم">
                                        </div>
                                    </td>
                                    <td><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></td>
                                    <td><?php echo $user['username']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td>
                                        <?php if($user['is_admin']): ?>
                                            <span class="badge bg-primary">مدير</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">مستخدم</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo format_date($user['created_at']); ?></td>
                                    <td class="table__actions">
                                        <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary" title="تعديل"><i class="uil uil-edit"></i></a>
                                        <?php if($user['id'] != $_SESSION['user_id']): // Prevent deleting self ?>
                                        <a href="delete-user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')" title="حذف"><i class="uil uil-trash-alt"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">لا يوجد مستخدمين</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                <div class="pagination">
                    <?php if($page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="page-link"><i class="uil uil-angle-left"></i> السابق</a>
                    <?php endif; ?>
                    
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    
                    <?php if($page < $total_pages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="page-link">التالي <i class="uil uil-angle-right"></i></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <div class="actions-bar">
                    <a href="add-user.php" class="btn btn-primary"><i class="uil uil-user-plus"></i> إضافة مستخدم جديد</a>
                </div>
            </main>
        </div>
    </section>
    <!-- End of Dashboard -->

    <!-- Footer -->
    <footer>
        <div class="container">
            <!-- Copyright -->
            <div class="footer__copyright">
                <small>&copy; <?php echo date('Y'); ?> Modern Blog. All rights reserved.</small>
                <p class="footer__made-with">
                    Made with <i class="uil uil-heart"></i> by Modern Blog Team
                </p>
            </div>
        </div>
    </footer>
    <!-- End of Footer -->

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="<?php echo ROOT_URL; ?>main.js"></script>
</body>
</html>
