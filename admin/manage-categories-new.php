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
                redirect_to('manage-categories-new.php');
            } else {
                set_message(ERROR_MESSAGE, 'فشل في إضافة الفئة');
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

// Get all categories
$categories_query = "SELECT c.*, COUNT(p.id) AS post_count
                    FROM categories c
                    LEFT JOIN posts p ON c.id = p.category_id
                    GROUP BY c.id
                    ORDER BY c.title";
$categories_result = $conn->query($categories_query);

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
                <h2>إدارة الفئات</h2>

                <div class="dashboard__layout">
                    <!-- Add Category Form -->
                    <div class="add-category">
                        <div class="card">
                            <div class="card__header">
                                <h3><i class="uil uil-plus-circle"></i> إضافة فئة جديدة</h3>
                            </div>
                            <div class="card__body">
                                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="add-category__form" data-notify="true" data-notify-action="تمت إضافة الفئة" data-notify-type="success">
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
                                                <td colspan="4" class="text-center">لا توجد فئات متاحة حالياً.</td>
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

<?php
// Include footer
include_once 'partials/footer.php';
?>

<!-- Include Admin CSS -->
<link rel="stylesheet" href="<?php echo ROOT_URL; ?>admin/css/admin.css">

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
