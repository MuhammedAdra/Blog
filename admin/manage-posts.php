<?php
$page_title = "Manage Posts";
require_once '../config/database.php';
require_once '../config/constants.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
require_login();
require_admin();

// Pagination
$posts_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $posts_per_page;

// Search functionality
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$search_condition = '';
if (!empty($search)) {
    $search_condition = "AND (p.title LIKE '%$search%' OR p.body LIKE '%$search%' OR p.tags LIKE '%$search%')";
}

// Filter by category
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$category_condition = '';
if ($category_filter > 0) {
    $category_condition = "AND p.category_id = $category_filter";
}

// Filter by status
$status_filter = isset($_GET['status']) ? clean_input($_GET['status']) : '';
$status_condition = '';
if (!empty($status_filter) && in_array($status_filter, ['published', 'draft'])) {
    $status_condition = "AND p.status = '$status_filter'";
}

// Get total posts count for pagination
$count_query = "SELECT COUNT(*) as total FROM posts p WHERE 1=1 $search_condition $category_condition $status_condition";
$count_result = $conn->query($count_query);
$total_posts = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $posts_per_page);

// Get posts
$posts_query = "SELECT p.*, c.title AS category_title, u.firstname, u.lastname
               FROM posts p
               JOIN categories c ON p.category_id = c.id
               JOIN users u ON p.author_id = u.id
               WHERE 1=1 $search_condition $category_condition $status_condition
               ORDER BY p.created_at DESC
               LIMIT $offset, $posts_per_page";
$posts_result = $conn->query($posts_query);

// Get all categories for filter
$categories_query = "SELECT * FROM categories ORDER BY title";
$categories_result = $conn->query($categories_query);
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
            <a href="<?php echo ROOT_URL; ?>index.php" class="nav__logo">Modern Blog</a>
            <ul class="nav__items">
                <li><a href="<?php echo ROOT_URL; ?>blog.php">Blog</a></li>
                <li><a href="<?php echo ROOT_URL; ?>about.php">About</a></li>
                <li><a href="<?php echo ROOT_URL; ?>services.php">Services</a></li>
                <li><a href="<?php echo ROOT_URL; ?>contact.php">Contact</a></li>
                <li class="nav__profile">
                    <div class="avatar">
                        <img src="<?php echo ROOT_URL . 'images/users/' . $_SESSION['user_avatar']; ?>" alt="Profile Avatar">
                    </div>
                    <ul>
                        <li><a href="<?php echo ROOT_URL; ?>admin/dashboard.php">Dashboard</a></li>
                        <li><a href="<?php echo ROOT_URL; ?>logout.php">Logout</a></li>
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
                            <h5>Dashboard</h5>
                        </a>
                    </li>
                    <li>
                        <a href="add-post.php">
                            <i class="uil uil-plus-circle"></i>
                            <h5>Add Post</h5>
                        </a>
                    </li>
                    <li>
                        <a href="manage-posts.php" class="active">
                            <i class="uil uil-postcard"></i>
                            <h5>Manage Posts</h5>
                        </a>
                    </li>
                    <li>
                        <a href="manage-categories.php">
                            <i class="uil uil-list-ul"></i>
                            <h5>Manage Categories</h5>
                        </a>
                    </li>
                    <li>
                        <a href="manage-users.php">
                            <i class="uil uil-users-alt"></i>
                            <h5>Manage Users</h5>
                        </a>
                    </li>
                    <li>
                        <a href="manage-comments.php">
                            <i class="uil uil-comment-dots"></i>
                            <h5>Manage Comments</h5>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo ROOT_URL; ?>profile.php">
                            <i class="uil uil-user-circle"></i>
                            <h5>Edit Profile</h5>
                        </a>
                    </li>
                </ul>
            </aside>

            <!-- Dashboard Main Content -->
            <main class="dashboard__main">
                <h2>إدارة المقالات</h2>

                <!-- Search and Filter -->
                <div class="search-and-filter">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="GET" class="search__form">
                        <div class="form__row">
                            <div class="form__control">
                                <input type="text" name="search" placeholder="ابحث عن مقالات..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="form__control">
                                <select name="category">
                                    <option value="0">جميع الفئات</option>
                                    <?php
                                    $categories_result->data_seek(0); // Reset result pointer
                                    while($category = $categories_result->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo $category['title']; ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form__control">
                                <select name="status">
                                    <option value="">جميع الحالات</option>
                                    <option value="published" <?php echo $status_filter === 'published' ? 'selected' : ''; ?>>منشور</option>
                                    <option value="draft" <?php echo $status_filter === 'draft' ? 'selected' : ''; ?>>مسودة</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="uil uil-filter"></i> تصفية</button>
                            <a href="manage-posts.php" class="btn btn-outline"><i class="uil uil-refresh"></i> إعادة تعيين</a>
                        </div>
                    </form>
                </div>

                <!-- Posts Table -->
                <div class="posts__table">
                    <table class="dashboard__table">
                        <thead>
                            <tr>
                                <th>العنوان</th>
                                <th>الفئة</th>
                                <th>الكاتب</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>مميز</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($posts_result->num_rows > 0): ?>
                                <?php while($post = $posts_result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="post-title-with-image">
                                            <img src="../images/posts/<?php echo !empty($post['thumbnail']) ? $post['thumbnail'] : 'default-post.jpg'; ?>" alt="<?php echo $post['title']; ?>" class="post-thumbnail">
                                            <span><?php echo $post['title']; ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo $post['category_title']; ?></td>
                                    <td><?php echo $post['firstname'] . ' ' . $post['lastname']; ?></td>
                                    <td><?php echo format_date($post['created_at']); ?></td>
                                    <td>
                                        <?php if($post['status'] == 'published'): ?>
                                            <span class="badge bg-success">منشور</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">مسودة</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($post['is_featured']): ?>
                                            <span class="badge bg-primary">مميز</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">لا</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions">
                                        <a href="<?php echo ROOT_URL; ?>post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-accent" title="عرض" target="_blank"><i class="uil uil-eye"></i></a>
                                        <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary" title="تعديل"><i class="uil uil-edit"></i></a>
                                        <a href="delete-post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المقال؟')" title="حذف"><i class="uil uil-trash-alt"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">لا توجد مقالات</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                <div class="pagination">
                    <?php if($page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="page-link"><i class="uil uil-angle-right"></i> السابق</a>
                    <?php endif; ?>

                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if($page < $total_pages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="page-link">التالي <i class="uil uil-angle-left"></i></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="actions-bar">
                    <a href="add-post.php" class="btn btn-primary"><i class="uil uil-plus"></i> إضافة مقال جديد</a>
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
