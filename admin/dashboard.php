<?php
$page_title = "Dashboard";
require_once '../config/database.php';
require_once '../config/constants.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
require_login();
require_admin();

// Get stats
$stats = [];

// Total posts
$posts_query = "SELECT COUNT(*) as count FROM posts";
$posts_result = $conn->query($posts_query);
$stats['posts'] = $posts_result->fetch_assoc()['count'];

// Total categories
$categories_query = "SELECT COUNT(*) as count FROM categories";
$categories_result = $conn->query($categories_query);
$stats['categories'] = $categories_result->fetch_assoc()['count'];

// Total users
$users_query = "SELECT COUNT(*) as count FROM users";
$users_result = $conn->query($users_query);
$stats['users'] = $users_result->fetch_assoc()['count'];

// Total comments
$comments_query = "SELECT COUNT(*) as count FROM comments";
$comments_result = $conn->query($comments_query);
$stats['comments'] = $comments_result->fetch_assoc()['count'];

// Recent posts
$recent_posts_query = "SELECT p.*, c.title AS category_title
                      FROM posts p
                      JOIN categories c ON p.category_id = c.id
                      ORDER BY p.created_at DESC
                      LIMIT 5";
$recent_posts_result = $conn->query($recent_posts_query);

// Recent comments
$recent_comments_query = "SELECT c.*, p.title AS post_title, u.firstname, u.lastname, u.avatar
                         FROM comments c
                         JOIN posts p ON c.post_id = p.id
                         LEFT JOIN users u ON c.user_id = u.id
                         ORDER BY c.created_at DESC
                         LIMIT 3";
$recent_comments_result = $conn->query($recent_comments_query);
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
                        <li><a href="<?php echo ROOT_URL; ?>admin/dashboard.php" class="active">Dashboard</a></li>
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
                        <a href="dashboard.php" class="active">
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
                <h2>لوحة التحكم</h2>

                <!-- Dashboard Stats -->
                <div class="dashboard__stats">
                    <div class="stat__card">
                        <div class="stat__icon">
                            <i class="uil uil-postcard"></i>
                        </div>
                        <div class="stat__info">
                            <h3><?php echo $stats['posts']; ?></h3>
                            <p>المقالات المنشورة</p>
                        </div>
                    </div>

                    <div class="stat__card">
                        <div class="stat__icon">
                            <i class="uil uil-list-ul"></i>
                        </div>
                        <div class="stat__info">
                            <h3><?php echo $stats['categories']; ?></h3>
                            <p>الفئات</p>
                        </div>
                    </div>

                    <div class="stat__card">
                        <div class="stat__icon">
                            <i class="uil uil-users-alt"></i>
                        </div>
                        <div class="stat__info">
                            <h3><?php echo $stats['users']; ?></h3>
                            <p>المستخدمين</p>
                        </div>
                    </div>

                    <div class="stat__card">
                        <div class="stat__icon">
                            <i class="uil uil-comment"></i>
                        </div>
                        <div class="stat__info">
                            <h3><?php echo $stats['comments']; ?></h3>
                            <p>التعليقات</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Posts -->
                <div class="recent__posts">
                    <h3>آخر المقالات</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>العنوان</th>
                                <th>الفئة</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($recent_posts_result->num_rows > 0): ?>
                                <?php while($post = $recent_posts_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $post['title']; ?></td>
                                    <td><?php echo $post['category_title']; ?></td>
                                    <td><?php echo format_date($post['created_at']); ?></td>
                                    <td>
                                        <?php if($post['status'] == 'published'): ?>
                                            <span class="badge bg-success">منشور</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">مسودة</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="table__actions">
                                        <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary" title="تعديل"><i class="uil uil-edit"></i></a>
                                        <a href="delete-post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المقال؟')" title="حذف"><i class="uil uil-trash-alt"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد مقالات</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <a href="manage-posts.php" class="btn btn-outline">عرض جميع المقالات <i class="uil uil-arrow-right"></i></a>
                </div>

                <!-- Recent Comments -->
                <div class="recent__comments mt-5">
                    <h3>آخر التعليقات</h3>
                    <div class="comments__container">
                        <?php if($recent_comments_result->num_rows > 0): ?>
                            <?php while($comment = $recent_comments_result->fetch_assoc()): ?>
                            <div class="comment">
                                <div class="comment__info">
                                    <div class="comment__author">
                                        <img src="<?php echo ROOT_URL . 'images/users/' . $comment['avatar']; ?>" alt="صورة المعلق">
                                        <div>
                                            <h5><?php echo $comment['firstname'] . ' ' . $comment['lastname']; ?></h5>
                                            <small><?php echo format_date($comment['created_at']); ?></small>
                                        </div>
                                    </div>
                                    <div class="comment__actions">
                                        <?php if($comment['status'] === 'pending'): ?>
                                        <a href="approve-comment.php?id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="uil uil-check"></i> قبول</a>
                                        <?php endif; ?>
                                        <a href="delete-comment.php?id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من حذف هذا التعليق؟')"><i class="uil uil-trash-alt"></i> حذف</a>
                                    </div>
                                </div>
                                <p class="comment__body">
                                    <?php echo $comment['comment']; ?>
                                </p>
                                <p class="comment__post">
                                    على: <a href="<?php echo ROOT_URL; ?>post.php?id=<?php echo $comment['post_id']; ?>"><?php echo $comment['post_title']; ?></a>
                                </p>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>لا توجد تعليقات</p>
                        <?php endif; ?>
                    </div>
                    <a href="manage-comments.php" class="btn btn-outline">عرض جميع التعليقات <i class="uil uil-arrow-right"></i></a>
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
