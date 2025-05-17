<?php
$page_title = "Manage Comments";
require_once '../config/database.php';
require_once '../config/constants.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
require_login();
require_admin();

// Process approve/reject comment
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $comment_id = (int)$_GET['id'];
    
    if ($action === 'approve') {
        $update_query = "UPDATE comments SET status = 'approved' WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('i', $comment_id);
        
        if ($update_stmt->execute()) {
            set_message(SUCCESS_MESSAGE, 'Comment approved successfully');
        } else {
            set_message(ERROR_MESSAGE, 'Failed to approve comment');
        }
    } elseif ($action === 'reject') {
        $update_query = "UPDATE comments SET status = 'rejected' WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('i', $comment_id);
        
        if ($update_stmt->execute()) {
            set_message(SUCCESS_MESSAGE, 'Comment rejected successfully');
        } else {
            set_message(ERROR_MESSAGE, 'Failed to reject comment');
        }
    }
    
    redirect_to('manage-comments.php');
}

// Pagination
$comments_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $comments_per_page;

// Filter by status
$status_filter = isset($_GET['status']) ? clean_input($_GET['status']) : '';
$status_condition = '';
if (!empty($status_filter)) {
    $status_condition = "AND c.status = '$status_filter'";
}

// Search functionality
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$search_condition = '';
if (!empty($search)) {
    $search_condition = "AND (c.comment LIKE '%$search%' OR p.title LIKE '%$search%' OR u.firstname LIKE '%$search%' OR u.lastname LIKE '%$search%')";
}

// Get total comments count for pagination
$count_query = "SELECT COUNT(*) as total FROM comments c 
                LEFT JOIN posts p ON c.post_id = p.id 
                LEFT JOIN users u ON c.user_id = u.id 
                WHERE 1=1 $status_condition $search_condition";
$count_result = $conn->query($count_query);
$total_comments = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_comments / $comments_per_page);

// Get comments
$comments_query = "SELECT c.*, p.title AS post_title, u.firstname, u.lastname, u.avatar 
                  FROM comments c 
                  LEFT JOIN posts p ON c.post_id = p.id 
                  LEFT JOIN users u ON c.user_id = u.id 
                  WHERE 1=1 $status_condition $search_condition
                  ORDER BY c.created_at DESC 
                  LIMIT $offset, $comments_per_page";
$comments_result = $conn->query($comments_query);
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
                        <a href="manage-users.php">
                            <i class="uil uil-users-alt"></i>
                            <h5>إدارة المستخدمين</h5>
                        </a>
                    </li>
                    <li>
                        <a href="manage-comments.php" class="active">
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
                <h2>إدارة التعليقات</h2>
                
                <!-- Search and Filter -->
                <div class="search-and-filter">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="GET" class="search__form">
                        <div class="form__row">
                            <div class="form__control">
                                <input type="text" name="search" placeholder="بحث في التعليقات..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="form__control">
                                <select name="status">
                                    <option value="">جميع الحالات</option>
                                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>قيد الانتظار</option>
                                    <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>مقبول</option>
                                    <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>مرفوض</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="uil uil-filter"></i> تصفية</button>
                            <a href="manage-comments.php" class="btn btn-outline"><i class="uil uil-refresh"></i> إعادة تعيين</a>
                        </div>
                    </form>
                </div>
                
                <!-- Comments List -->
                <div class="comments__container">
                    <?php if($comments_result->num_rows > 0): ?>
                        <?php while($comment = $comments_result->fetch_assoc()): ?>
                        <div class="comment">
                            <div class="comment__info">
                                <div class="comment__author">
                                    <img src="<?php echo ROOT_URL . 'images/users/' . $comment['avatar']; ?>" alt="صورة المعلق">
                                    <div>
                                        <h5><?php echo $comment['firstname'] . ' ' . $comment['lastname']; ?></h5>
                                        <small><?php echo format_date($comment['created_at']); ?></small>
                                    </div>
                                </div>
                                <div class="comment__status">
                                    <?php if($comment['status'] === 'pending'): ?>
                                        <span class="badge bg-warning">قيد الانتظار</span>
                                    <?php elseif($comment['status'] === 'approved'): ?>
                                        <span class="badge bg-success">مقبول</span>
                                    <?php elseif($comment['status'] === 'rejected'): ?>
                                        <span class="badge bg-danger">مرفوض</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <p class="comment__body">
                                <?php echo $comment['comment']; ?>
                            </p>
                            <p class="comment__post">
                                على: <a href="<?php echo ROOT_URL; ?>post.php?id=<?php echo $comment['post_id']; ?>"><?php echo $comment['post_title']; ?></a>
                            </p>
                            <div class="comment__actions">
                                <?php if($comment['status'] === 'pending'): ?>
                                <a href="manage-comments.php?action=approve&id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-success"><i class="uil uil-check"></i> قبول</a>
                                <a href="manage-comments.php?action=reject&id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-danger"><i class="uil uil-times"></i> رفض</a>
                                <?php elseif($comment['status'] === 'approved'): ?>
                                <a href="manage-comments.php?action=reject&id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-danger"><i class="uil uil-times"></i> رفض</a>
                                <?php elseif($comment['status'] === 'rejected'): ?>
                                <a href="manage-comments.php?action=approve&id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-success"><i class="uil uil-check"></i> قبول</a>
                                <?php endif; ?>
                                <a href="delete-comment.php?id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من حذف هذا التعليق؟')"><i class="uil uil-trash-alt"></i> حذف</a>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-comments">
                            <p>لا توجد تعليقات</p>
                        </div>
                    <?php endif; ?>
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
