<?php
$page_title = "إضافة مقال";
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

    // Validate input
    if (empty($title) || empty($body) || empty($category_id)) {
        set_message(ERROR_MESSAGE, 'عنوان المقال والمحتوى والفئة مطلوبة');
    } elseif (!$thumbnail['name']) {
        set_message(ERROR_MESSAGE, 'يرجى تحميل صورة مصغرة');
    } else {
        // Upload thumbnail
        $thumbnail_name = upload_image($thumbnail, '../images/posts/');
        
        if ($thumbnail_name) {
            // Insert post
            $insert_query = "INSERT INTO posts (title, body, thumbnail, category_id, author_id, is_featured, excerpt, tags, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param('sssiiisss', $title, $body, $thumbnail_name, $category_id, $author_id, $is_featured, $excerpt, $tags, $status);
            
            if ($insert_stmt->execute()) {
                set_message(SUCCESS_MESSAGE, 'تمت إضافة المقال بنجاح');
                redirect_to('manage-posts.php');
            } else {
                set_message(ERROR_MESSAGE, 'فشل في إضافة المقال');
            }
        } else {
            set_message(ERROR_MESSAGE, 'فشل في تحميل الصورة المصغرة');
        }
    }
}

// Include header
include_once 'partials/header.php';
?>

<!-- Dashboard -->
<section class="dashboard">
    <div class="container dashboard__container">
        <!-- Include Sidebar -->
        <?php include_once 'partials/sidebar.php'; ?>

        <!-- Dashboard Main Content -->
        <main class="dashboard__main">
            <h2>إضافة مقال جديد</h2>
            
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data" class="add-post__form" data-notify="true" data-notify-action="تمت إضافة المقال" data-notify-type="success">
                <!-- Main Content -->
                <div class="add-post__content">
                    <div class="form__control">
                        <label for="title">عنوان المقال</label>
                        <input type="text" id="title" name="title" placeholder="أدخل عنوان المقال" required>
                    </div>
                    
                    <div class="form__control">
                        <label for="excerpt">مقتطف المقال</label>
                        <textarea id="excerpt" name="excerpt" rows="3" placeholder="أدخل مقتطفًا قصيرًا للمقال"></textarea>
                    </div>
                    
                    <div class="form__control">
                        <label for="content">محتوى المقال</label>
                        <textarea id="content" name="content" rows="10" placeholder="أدخل محتوى المقال" required></textarea>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="add-post__sidebar">
                    <!-- Category -->
                    <div class="sidebar__card">
                        <h3>الفئة</h3>
                        <div class="form__control">
                            <select id="category" name="category" required>
                                <option value="">اختر فئة</option>
                                <?php while($category = $categories_result->fetch_assoc()): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo $category['title']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Thumbnail -->
                    <div class="sidebar__card">
                        <h3>الصورة المصغرة</h3>
                        <div class="form__control">
                            <input type="file" id="thumbnail" name="thumbnail" accept="image/*" onchange="previewThumbnail(this)" required>
                        </div>
                        <div class="thumbnail-preview" id="thumbnailPreview">
                            <img src="#" alt="معاينة الصورة المصغرة" id="thumbnailImage">
                        </div>
                    </div>
                    
                    <!-- Tags -->
                    <div class="sidebar__card">
                        <h3>الوسوم</h3>
                        <div class="form__control">
                            <input type="text" id="tags" name="tags" placeholder="أدخل الوسوم مفصولة بفواصل">
                        </div>
                    </div>
                    
                    <!-- Options -->
                    <div class="sidebar__card">
                        <h3>خيارات النشر</h3>
                        <div class="form__check">
                            <input type="checkbox" id="featured" name="featured">
                            <label for="featured">مقال مميز</label>
                        </div>
                        <div class="form__check">
                            <input type="checkbox" id="draft" name="draft">
                            <label for="draft">حفظ كمسودة</label>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="sidebar__card">
                        <div class="form__actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="uil uil-plus"></i> نشر المقال
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="uil uil-times"></i> إلغاء
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>
</section>
<!-- End of Dashboard -->

<?php
// Include footer
include_once 'partials/footer.php';
?>

<!-- TinyMCE -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    // Initialize TinyMCE
    tinymce.init({
        selector: '#content',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        directionality: 'rtl',
        language: 'ar',
        height: 400
    });
    
    // Preview thumbnail
    function previewThumbnail(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('thumbnailImage').src = e.target.result;
                document.getElementById('thumbnailPreview').style.display = 'block';
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<!-- Include Admin CSS -->
<link rel="stylesheet" href="<?php echo ROOT_URL; ?>admin/css/admin.css">
