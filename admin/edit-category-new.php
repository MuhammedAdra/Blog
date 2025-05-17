<?php
$page_title = "تعديل الفئة";

// Check if category ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    require_once '../config/database.php';
    require_once '../config/constants.php';
    require_once '../includes/functions.php';
    
    set_message(ERROR_MESSAGE, 'معرف الفئة غير صالح');
    redirect_to('manage-categories-new.php');
}

$category_id = $_GET['id'];

// Process edit category form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
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
        // Check if category already exists (excluding current category)
        $check_query = "SELECT * FROM categories WHERE title = ? AND id != ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('si', $title, $category_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            set_message(ERROR_MESSAGE, 'الفئة موجودة بالفعل');
        } else {
            // Update category
            $update_query = "UPDATE categories SET title = ?, description = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param('ssi', $title, $description, $category_id);

            if ($update_stmt->execute()) {
                set_message(SUCCESS_MESSAGE, 'تم تحديث الفئة بنجاح');
                
                // Store notification in session for JavaScript to display
                $_SESSION['admin_notification'] = [
                    'type' => 'success',
                    'message' => 'تم تحديث الفئة "' . $title . '" بنجاح'
                ];
                
                redirect_to('manage-categories-new.php');
            } else {
                set_message(ERROR_MESSAGE, 'فشل في تحديث الفئة');
            }
        }
    }
}

require_once '../config/database.php';
require_once '../config/constants.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
require_login();
require_admin();

// Get category details
$category_query = "SELECT * FROM categories WHERE id = ?";
$category_stmt = $conn->prepare($category_query);
$category_stmt->bind_param('i', $category_id);
$category_stmt->execute();
$category_result = $category_stmt->get_result();

if ($category_result->num_rows === 0) {
    set_message(ERROR_MESSAGE, 'الفئة غير موجودة');
    redirect_to('manage-categories-new.php');
}

$category = $category_result->fetch_assoc();

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
                <h2>تعديل الفئة</h2>

                <div class="card">
                    <div class="card__header">
                        <h3><i class="uil uil-edit"></i> تعديل الفئة "<?php echo $category['title']; ?>"</h3>
                        <div class="card__actions">
                            <a href="manage-categories-new.php" class="btn btn-sm btn-secondary">
                                <i class="uil uil-arrow-right"></i> العودة إلى الفئات
                            </a>
                        </div>
                    </div>
                    <div class="card__body">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?id=' . $category_id); ?>" method="POST" class="edit-category__form" data-notify="true" data-notify-action="تم تحديث الفئة" data-notify-type="success">
                            <div class="form__control">
                                <label for="title">عنوان الفئة</label>
                                <input type="text" id="title" name="title" value="<?php echo $category['title']; ?>" placeholder="أدخل عنوان الفئة" required>
                            </div>
                            <div class="form__control">
                                <label for="description">الوصف</label>
                                <textarea id="description" name="description" rows="5" placeholder="أدخل وصف الفئة"><?php echo $category['description']; ?></textarea>
                            </div>
                            <div class="form__actions">
                                <button type="submit" name="update_category" class="btn btn-primary">
                                    <i class="uil uil-check"></i> حفظ التغييرات
                                </button>
                                <a href="manage-categories-new.php" class="btn btn-secondary">
                                    <i class="uil uil-times"></i> إلغاء
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </section>
    <!-- End of Dashboard -->

<?php
// Include footer
include_once 'partials/footer.php';
?>
