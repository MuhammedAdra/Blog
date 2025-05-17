<?php
$page_title = "Add Post";
require_once '../config/database.php';
require_once '../config/constants.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
require_login();
require_admin();

// Get all categories
$categories_query = "SELECT * FROM categories ORDER BY title";
$categories_result = $conn->query($categories_query);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug information
    error_log('Form submitted: ' . print_r($_POST, true));
    error_log('Files: ' . print_r($_FILES, true));
    // Get form data
    $title = clean_input($_POST['title']);
    $body = $_POST['content'];
    $category_id = clean_input($_POST['category']);
    $excerpt = clean_input($_POST['excerpt']);
    $tags = clean_input($_POST['tags']);
    $is_featured = isset($_POST['featured']) ? 1 : 0;
    $status = isset($_POST['draft']) ? 'draft' : 'published';
    $thumbnail = $_FILES['thumbnail'];
    $author_id = $_SESSION['user_id'];

    // Debug form data
    error_log('Title: ' . $title);
    error_log('Body length: ' . strlen($body));
    error_log('Category ID: ' . $category_id);
    error_log('Excerpt: ' . $excerpt);
    error_log('Tags: ' . $tags);
    error_log('Is Featured: ' . $is_featured);
    error_log('Status: ' . $status);
    error_log('Thumbnail: ' . print_r($thumbnail, true));

    // Validate input
    if (empty($title) || empty($body) || empty($category_id)) {
        set_message(ERROR_MESSAGE, 'عنوان المقال والمحتوى والفئة مطلوبة');
        error_log('Validation error: Title, content, or category is empty');
    } elseif (!$thumbnail['name']) {
        set_message(ERROR_MESSAGE, 'يرجى تحميل صورة مصغرة');
        error_log('Validation error: No thumbnail uploaded');
    } else {
        // Upload thumbnail
        error_log('Uploading thumbnail...');

        // تأكد من وجود مجلد الصور
        $upload_dir = '../images/posts/';
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                error_log('Failed to create directory: ' . $upload_dir);
                set_message(ERROR_MESSAGE, 'فشل في إنشاء مجلد الصور. يرجى التحقق من الأذونات');
                redirect_to('add-post.php');
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
                $thumbnail_name = $new_file_name;
            } else {
                error_log('Failed to move uploaded file: ' . error_get_last()['message']);
                set_message(ERROR_MESSAGE, 'فشل في تحميل الصورة المصغرة. يرجى المحاولة مرة أخرى');
                redirect_to('add-post.php');
                exit;
            }
        } else {
            error_log('Invalid file extension: ' . $file_ext);
            set_message(ERROR_MESSAGE, 'نوع الملف غير مسموح به. يرجى استخدام صور بصيغة JPG أو PNG أو GIF');
            redirect_to('add-post.php');
            exit;
        }

        if (!$thumbnail_name) {
            set_message(ERROR_MESSAGE, 'فشل في تحميل الصورة المصغرة. يرجى المحاولة مرة أخرى');
            error_log('Error: Failed to upload thumbnail');
        } else {
            error_log('Thumbnail uploaded successfully: ' . $thumbnail_name);
            // Insert post
            $insert_query = "INSERT INTO posts (title, body, thumbnail, category_id, author_id, is_featured, excerpt, tags, status)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param('sssiiisss', $title, $body, $thumbnail_name, $category_id, $author_id, $is_featured, $excerpt, $tags, $status);

            if ($insert_stmt->execute()) {
                // If this post is featured, unfeature other posts
                if ($is_featured) {
                    $unfeature_query = "UPDATE posts SET is_featured = 0 WHERE id != ?";
                    $unfeature_stmt = $conn->prepare($unfeature_query);
                    $post_id = $insert_stmt->insert_id;
                    $unfeature_stmt->bind_param('i', $post_id);
                    $unfeature_stmt->execute();
                }

                if ($status === 'published') {
                    set_message(SUCCESS_MESSAGE, 'تم نشر المقال بنجاح');
                } else {
                    set_message(SUCCESS_MESSAGE, 'تم حفظ المقال كمسودة');
                }
                redirect_to('manage-posts.php');
            } else {
                set_message(ERROR_MESSAGE, 'فشل في إنشاء المقال. يرجى المحاولة مرة أخرى');
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
                            <h5>لوحة التحكم</h5>
                        </a>
                    </li>
                    <li>
                        <a href="add-post.php" class="active">
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
                <h2>إضافة مقال جديد</h2>

                <form class="add-post__form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                    <div class="form__control">
                        <label for="title">عنوان المقال</label>
                        <input type="text" id="title" name="title" placeholder="أدخل عنوان المقال" required>
                    </div>

                    <div class="form__control">
                        <label for="category">الفئة</label>
                        <select id="category" name="category" required>
                            <option value="" selected disabled>اختر الفئة</option>
                            <?php while($category = $categories_result->fetch_assoc()): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo $category['title']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form__control">
                        <label for="thumbnail">الصورة الرئيسية</label>
                        <input type="file" id="thumbnail" name="thumbnail" accept="image/*" required>
                        <div class="image-preview" id="thumbnailPreview">
                            <span>اسحب وأفلت الصورة هنا أو انقر للاختيار</span>
                        </div>
                    </div>

                    <div class="form__control">
                        <label for="excerpt">ملخص المقال</label>
                        <textarea id="excerpt" name="excerpt" rows="3" placeholder="ملخص موجز للمقال (150-200 حرف)" required></textarea>
                    </div>

                    <div class="form__control">
                        <label for="content">محتوى المقال</label>
                        <textarea id="content" name="content" rows="10" placeholder="اكتب محتوى المقال هنا..." required></textarea>
                    </div>

                    <div class="form__control">
                        <label for="tags">الوسوم (مفصولة بفواصل)</label>
                        <input type="text" id="tags" name="tags" placeholder="مثال: سفر, مغامرة, أوروبا">
                    </div>

                    <div class="form__control">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="featured" name="featured">
                            <label class="form-check-label" for="featured">
                                تمييز كمقال مميز
                            </label>
                        </div>
                    </div>

                    <div class="form__actions">
                        <button type="submit" class="btn btn-primary"><i class="uil uil-upload-alt"></i> نشر المقال</button>
                        <button type="submit" name="draft" value="1" class="btn btn-outline"><i class="uil uil-save"></i> حفظ كمسودة</button>
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
            const form = document.querySelector('.add-post__form');
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

    <!-- Image Preview Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const thumbnailInput = document.getElementById('thumbnail');
            const thumbnailPreview = document.getElementById('thumbnailPreview');

            // Handle file selection
            thumbnailInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        thumbnailPreview.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <div class="remove-image"><i class="uil uil-times"></i></div>
                        `;
                        thumbnailPreview.classList.add('has-image');

                        // Add event listener to remove button
                        const removeButton = thumbnailPreview.querySelector('.remove-image');
                        removeButton.addEventListener('click', function(e) {
                            e.stopPropagation();
                            thumbnailPreview.innerHTML = `<span>اسحب وأفلت الصورة هنا أو انقر للاختيار</span>`;
                            thumbnailPreview.classList.remove('has-image');
                            thumbnailInput.value = '';
                        });
                    };

                    reader.readAsDataURL(file);
                }
            });

            // Make the preview clickable to trigger file input
            thumbnailPreview.addEventListener('click', function() {
                if (!thumbnailPreview.classList.contains('has-image')) {
                    thumbnailInput.click();
                }
            });

            // Drag and drop functionality
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                thumbnailPreview.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                thumbnailPreview.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                thumbnailPreview.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                thumbnailPreview.classList.add('highlight');
            }

            function unhighlight() {
                thumbnailPreview.classList.remove('highlight');
            }

            thumbnailPreview.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const file = dt.files[0];

                if (file && file.type.startsWith('image/')) {
                    thumbnailInput.files = dt.files;
                    const event = new Event('change');
                    thumbnailInput.dispatchEvent(event);
                }
            }
        });
    </script>
</body>
</html>
