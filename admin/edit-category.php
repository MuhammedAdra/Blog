<?php
$page_title = "Edit Category";
require_once '../config/database.php';
require_once '../config/constants.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
require_login();
require_admin();

// Check if category ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    set_message(ERROR_MESSAGE, 'Invalid category ID');
    redirect_to('manage-categories.php');
}

$category_id = $_GET['id'];

// Get category details
$category_query = "SELECT * FROM categories WHERE id = ?";
$category_stmt = $conn->prepare($category_query);
$category_stmt->bind_param('i', $category_id);
$category_stmt->execute();
$category_result = $category_stmt->get_result();

// Check if category exists
if ($category_result->num_rows === 0) {
    set_message(ERROR_MESSAGE, 'Category not found');
    redirect_to('manage-categories.php');
}

$category = $category_result->fetch_assoc();

// Process edit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean_input($_POST['title']);
    $description = clean_input($_POST['description']);
    
    if (empty($title)) {
        set_message(ERROR_MESSAGE, 'Category title is required');
    } else {
        // Check if title already exists (excluding current category)
        $check_query = "SELECT * FROM categories WHERE title = ? AND id != ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('si', $title, $category_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            set_message(ERROR_MESSAGE, 'Category title already exists');
        } else {
            // Update category
            $update_query = "UPDATE categories SET title = ?, description = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param('ssi', $title, $description, $category_id);
            
            if ($update_stmt->execute()) {
                set_message(SUCCESS_MESSAGE, 'Category updated successfully');
                redirect_to('manage-categories.php');
            } else {
                set_message(ERROR_MESSAGE, 'Failed to update category');
            }
        }
    }
}
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
                        <a href="manage-posts.php">
                            <i class="uil uil-postcard"></i>
                            <h5>Manage Posts</h5>
                        </a>
                    </li>
                    <li>
                        <a href="manage-categories.php" class="active">
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
                <h2>Edit Category</h2>
                
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $category_id; ?>" method="POST" class="edit-category__form">
                    <div class="form__control">
                        <label for="title">Category Title</label>
                        <input type="text" id="title" name="title" value="<?php echo $category['title']; ?>" placeholder="Enter category title" required>
                    </div>
                    <div class="form__control">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Enter category description"><?php echo $category['description']; ?></textarea>
                    </div>
                    <div class="form__actions">
                        <button type="submit" class="btn btn-primary">Update Category</button>
                        <a href="manage-categories.php" class="btn btn-outline">Cancel</a>
                    </div>
                </form>
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
