<?php
$page_title = "إدارة الفئات";

// Process add category form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    require_once '../config/database.php';
    require_once '../config/constants.php';
    require_once '../includes/functions.php';

    // Check if user is logged in and is admin
    require_login();
    require_admin();

    $title = clean_input($_POST['title']);
    $description = clean_input($_POST['description']);

    if (empty($title)) {
        set_message(ERROR_MESSAGE, 'عنوان الفئة مطلوب');
    } else {
        // Check if category already exists
        $check_query = "SELECT * FROM categories WHERE title = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('s', $title);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            set_message(ERROR_MESSAGE, 'الفئة موجودة بالفعل');
        } else {
            // Insert category
            $insert_query = "INSERT INTO categories (title, description) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param('ss', $title, $description);

            if ($insert_stmt->execute()) {
                set_message(SUCCESS_MESSAGE, 'تمت إضافة الفئة بنجاح');
                redirect_to('manage-categories.php');
            } else {
                set_message(ERROR_MESSAGE, 'فشل في إضافة الفئة');
            }
        }
    }
}

// Get all categories
$categories_query = "SELECT c.*, COUNT(p.id) AS post_count
                    FROM categories c
                    LEFT JOIN posts p ON c.id = p.category_id
                    GROUP BY c.id
                    ORDER BY c.title";
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
                        <a href="manage-categories.php" class="active">
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
                <h2>إدارة الفئات</h2>

                <div class="dashboard__layout">
                    <!-- Add Category Form -->
                    <div class="add-category">
                        <div class="card">
                            <div class="card__header">
                                <h3><i class="uil uil-plus-circle"></i> إضافة فئة جديدة</h3>
                            </div>
                            <div class="card__body">
                                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="add-category__form">
                                    <div class="form__control">
                                        <label for="title">عنوان الفئة</label>
                                        <input type="text" id="title" name="title" placeholder="أدخل عنوان الفئة" required>
                                    </div>
                                    <div class="form__control">
                                        <label for="description">الوصف</label>
                                        <textarea id="description" name="description" rows="3" placeholder="أدخل وصف الفئة"></textarea>
                                    </div>
                                    <button type="submit" name="add_category" class="btn btn-primary"><i class="uil uil-plus"></i> إضافة فئة</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Categories Table -->
                    <div class="categories__table">
                        <div class="card">
                            <div class="card__header">
                                <h3><i class="uil uil-list-ul"></i> جميع الفئات</h3>
                                <div class="card__actions">
                                    <div class="search-box">
                                        <input type="text" id="categorySearch" placeholder="ابحث عن فئة..." onkeyup="searchCategories()">
                                        <i class="uil uil-search"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card__body">
                                <table class="dashboard__table" id="categoriesTable">
                        <thead>
                            <tr>
                                <th>العنوان</th>
                                <th>الوصف</th>
                                <th>المقالات</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($categories_result->num_rows > 0): ?>
                                <?php while($category = $categories_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $category['title']; ?></td>
                                    <td><?php echo $category['description'] ? $category['description'] : 'لا يوجد وصف'; ?></td>
                                    <td><?php echo $category['post_count']; ?></td>
                                    <td class="table__actions">
                                        <a href="edit-category.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-primary" title="تعديل"><i class="uil uil-edit"></i></a>
                                        <a href="delete-category.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذه الفئة؟ سيتم إلغاء تصنيف جميع المقالات في هذه الفئة.')" title="حذف"><i class="uil uil-trash-alt"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">لا توجد فئات</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                            </div>
                        </div>
                    </div>
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

    <!-- Category Search Script -->
    <script>
        function searchCategories() {
            // Get input value
            var input = document.getElementById("categorySearch");
            var filter = input.value.toUpperCase();
            var table = document.getElementById("categoriesTable");
            var tr = table.getElementsByTagName("tr");
            var found = false;

            // Loop through all table rows, and hide those who don't match the search query
            for (var i = 1; i < tr.length; i++) { // Start from 1 to skip header row
                var td = tr[i].getElementsByTagName("td")[0]; // Title column
                var tdDesc = tr[i].getElementsByTagName("td")[1]; // Description column

                if (td || tdDesc) {
                    var txtValue = td.textContent || td.innerText;
                    var txtValueDesc = tdDesc.textContent || tdDesc.innerText;

                    if (txtValue.toUpperCase().indexOf(filter) > -1 || txtValueDesc.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                        found = true;
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }

            // Show a message if no results found
            var noResults = document.getElementById("noResults");
            if (!noResults) {
                noResults = document.createElement("tr");
                noResults.id = "noResults";
                noResults.innerHTML = '<td colspan="4" class="text-center">لا توجد نتائج مطابقة للبحث</td>';
                table.appendChild(noResults);
            }

            noResults.style.display = found ? "none" : "";
        }
    </script>
</body>
</html>
