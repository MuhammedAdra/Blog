<?php
$page_title = "Edit Post";
require_once '../config/database.php';
require_once '../config/constants.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
require_login();
require_admin();

// Check if post ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    set_message(ERROR_MESSAGE, 'Invalid post ID');
    redirect_to('manage-posts.php');
}

$post_id = $_GET['id'];

// Get post details
$post_query = "SELECT * FROM posts WHERE id = ?";
$post_stmt = $conn->prepare($post_query);
$post_stmt->bind_param('i', $post_id);
$post_stmt->execute();
$post_result = $post_stmt->get_result();

// Check if post exists
if ($post_result->num_rows === 0) {
    set_message(ERROR_MESSAGE, 'Post not found');
    redirect_to('manage-posts.php');
}

$post = $post_result->fetch_assoc();

// Get all categories
$categories_query = "SELECT * FROM categories ORDER BY title";
$categories_result = $conn->query($categories_query);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = clean_input($_POST['title']);
    $body = $_POST['content'];
    $category_id = clean_input($_POST['category']);
    $excerpt = clean_input($_POST['excerpt']);
    $tags = clean_input($_POST['tags']);
    $is_featured = isset($_POST['featured']) ? 1 : 0;
    $status = isset($_POST['draft']) ? 'draft' : 'published';
    $thumbnail = $_FILES['thumbnail'];

    // Validate input
    if (empty($title) || empty($body) || empty($category_id)) {
        set_message(ERROR_MESSAGE, 'عنوان المقال والمحتوى والفئة مطلوبة');
    } else {
        // Handle thumbnail upload if a new one is provided
        $thumbnail_name = $post['thumbnail']; // Default to current thumbnail

        if ($thumbnail['name']) {
            error_log('Uploading new thumbnail...');

            // تأكد من وجود مجلد الصور
            $upload_dir = '../images/posts/';
            if (!file_exists($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    error_log('Failed to create directory: ' . $upload_dir);
                    set_message(ERROR_MESSAGE, 'فشل في إنشاء مجلد الصور. يرجى التحقق من الأذونات');
                    redirect_to('edit-post.php?id=' . $post_id);
                    exit;
                }
            }

            // تحميل الصورة مباشرة بدون استخدام الوظيفة
            $file_name = $thumbnail['name'];
            $file_tmp = $thumbnail['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');

            if (in_array($file_ext, $allowed_ext)) {
                $new_file_name = uniqid('', true) . '.' . $file_ext;
                $file_destination = $upload_dir . $new_file_name;

                error_log('Moving file to: ' . $file_destination);

                if (move_uploaded_file($file_tmp, $file_destination)) {
                    error_log('Thumbnail uploaded successfully: ' . $new_file_name);

                    // Delete old thumbnail if it's not the default
                    if ($thumbnail_name !== 'default-post.jpg' && file_exists('../images/posts/' . $thumbnail_name)) {
                        unlink('../images/posts/' . $thumbnail_name);
                    }

                    $thumbnail_name = $new_file_name;
                } else {
                    error_log('Failed to move uploaded file: ' . error_get_last()['message']);
                    set_message(ERROR_MESSAGE, 'فشل في تحميل الصورة المصغرة. يرجى المحاولة مرة أخرى');
                }
            } else {
                error_log('Invalid file extension: ' . $file_ext);
                set_message(ERROR_MESSAGE, 'نوع الملف غير مسموح به. يرجى استخدام صور بصيغة JPG أو PNG أو GIF');
            }
        }

        // Update post
        $update_query = "UPDATE posts SET title = ?, body = ?, thumbnail = ?, category_id = ?, is_featured = ?, excerpt = ?, tags = ?, status = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('sssiiissi', $title, $body, $thumbnail_name, $category_id, $is_featured, $excerpt, $tags, $status, $post_id);

        if ($update_stmt->execute()) {
            // If this post is featured, unfeature other posts
            if ($is_featured) {
                $unfeature_query = "UPDATE posts SET is_featured = 0 WHERE id != ?";
                $unfeature_stmt = $conn->prepare($unfeature_query);
                $unfeature_stmt->bind_param('i', $post_id);
                $unfeature_stmt->execute();
            }

            if ($status === 'published') {
                set_message(SUCCESS_MESSAGE, 'تم تحديث المقال ونشره بنجاح');
            } else {
                set_message(SUCCESS_MESSAGE, 'تم تحديث المقال وحفظه كمسودة');
            }
            redirect_to('manage-posts.php');
        } else {
            set_message(ERROR_MESSAGE, 'فشل في تحديث المقال. يرجى المحاولة مرة أخرى');
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
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({
        selector: '#content',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        height: 400
      });
    </script>
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
                <h2>Edit Post</h2>

                <form class="edit-post__form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $post_id; ?>" method="POST" enctype="multipart/form-data">
                    <div class="form__control">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" value="<?php echo $post['title']; ?>" placeholder="Enter post title" required>
                    </div>

                    <div class="form__control">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="" disabled>Select Category</option>
                            <?php
                            $categories_result->data_seek(0); // Reset result pointer
                            while($category = $categories_result->fetch_assoc()):
                            ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $post['category_id'] ? 'selected' : ''; ?>>
                                <?php echo $category['title']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form__control">
                        <label for="thumbnail">Featured Image</label>
                        <div class="current-thumbnail">
                            <p>Current image:</p>
                            <img src="<?php echo ROOT_URL . 'images/posts/' . $post['thumbnail']; ?>" alt="Current Thumbnail" style="max-width: 200px;">
                        </div>
                        <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
                        <small>Leave empty to keep current image</small>
                        <div class="image-preview"></div>
                    </div>

                    <div class="form__control">
                        <label for="excerpt">Excerpt</label>
                        <textarea id="excerpt" name="excerpt" rows="3" placeholder="Brief summary of your post (150-200 characters)" required><?php echo $post['excerpt']; ?></textarea>
                    </div>

                    <div class="form__control">
                        <label for="content">Content</label>
                        <textarea id="content" name="content" rows="10" placeholder="Write your post content here..." required><?php echo $post['body']; ?></textarea>
                    </div>

                    <div class="form__control">
                        <label for="tags">Tags (comma separated)</label>
                        <input type="text" id="tags" name="tags" value="<?php echo $post['tags']; ?>" placeholder="e.g. travel, adventure, europe">
                    </div>

                    <div class="form__control">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="featured" name="featured" <?php echo $post['is_featured'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="featured">
                                Mark as Featured Post
                            </label>
                        </div>
                    </div>

                    <div class="form__actions">
                        <button type="submit" class="btn btn-primary">Update Post</button>
                        <button type="submit" name="draft" value="1" class="btn btn-outline" <?php echo $post['status'] === 'draft' ? 'checked' : ''; ?>>Save as Draft</button>
                        <a href="manage-posts.php" class="btn btn-outline">Cancel</a>
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
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#content',
                plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                height: 500,
                promotion: false,
                branding: false,
                setup: function(editor) {
                    editor.on('init', function() {
                        // Replace the default API key message with our custom message
                        const apiKeyMessage = document.querySelector('.tox-notification--warning');
                        if (apiKeyMessage) {
                            apiKeyMessage.style.display = 'none';
                        }
                    });
                }
            });

            // Debug form submission
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Make sure TinyMCE content is updated before form submission
                    tinymce.triggerSave();
                    console.log('Form submitted');
                });
            }
        });
    </script>
    <!-- Custom JavaScript -->
    <script src="<?php echo ROOT_URL; ?>main.js"></script>
</body>
</html>
