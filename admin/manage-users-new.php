<?php
$page_title = "إدارة المستخدمين";
require_once '../config/database.php';
require_once '../config/constants.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
require_login();
require_admin();

// Get all users
$users_query = "SELECT * FROM users ORDER BY created_at DESC";
$users_result = $conn->query($users_query);

// Get user statistics
$total_users_query = "SELECT COUNT(*) as total FROM users";
$total_users_result = $conn->query($total_users_query);
$total_users = $total_users_result->fetch_assoc()['total'];

$admin_users_query = "SELECT COUNT(*) as total FROM users WHERE is_admin = 1";
$admin_users_result = $conn->query($admin_users_query);
$admin_users = $admin_users_result->fetch_assoc()['total'];

$regular_users = $total_users - $admin_users;

$new_users_query = "SELECT COUNT(*) as total FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$new_users_result = $conn->query($new_users_query);
$new_users = $new_users_result->fetch_assoc()['total'];

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
            <h2>إدارة المستخدمين</h2>
            
            <!-- User Statistics -->
            <div class="users-stats">
                <div class="stat-card">
                    <div class="stat-card__icon total-users">
                        <i class="uil uil-users-alt"></i>
                    </div>
                    <div class="stat-card__content">
                        <h3><?php echo $total_users; ?></h3>
                        <p>إجمالي المستخدمين</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-card__icon admins">
                        <i class="uil uil-shield"></i>
                    </div>
                    <div class="stat-card__content">
                        <h3><?php echo $admin_users; ?></h3>
                        <p>المشرفين</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-card__icon active-users">
                        <i class="uil uil-user"></i>
                    </div>
                    <div class="stat-card__content">
                        <h3><?php echo $regular_users; ?></h3>
                        <p>المستخدمين العاديين</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-card__icon new-users">
                        <i class="uil uil-user-plus"></i>
                    </div>
                    <div class="stat-card__content">
                        <h3><?php echo $new_users; ?></h3>
                        <p>مستخدمين جدد (30 يوم)</p>
                    </div>
                </div>
            </div>
            
            <!-- Users Filter -->
            <div class="users-filter">
                <div class="users-filter__search">
                    <input type="text" id="userSearch" placeholder="ابحث عن مستخدم..." onkeyup="searchUsers()">
                    <i class="uil uil-search"></i>
                </div>
                
                <div class="users-filter__select">
                    <select id="userTypeFilter" onchange="filterUsers()">
                        <option value="all">جميع المستخدمين</option>
                        <option value="admin">المشرفين</option>
                        <option value="user">المستخدمين العاديين</option>
                    </select>
                </div>
                
                <div class="users-filter__actions">
                    <a href="add-user.php" class="btn btn-primary">
                        <i class="uil uil-user-plus"></i> إضافة مستخدم
                    </a>
                </div>
            </div>
            
            <!-- Users Table -->
            <div class="card">
                <div class="card__header">
                    <h3><i class="uil uil-users-alt"></i> قائمة المستخدمين</h3>
                </div>
                <div class="card__body">
                    <table class="dashboard__table users-table" id="usersTable">
                        <thead>
                            <tr>
                                <th>المستخدم</th>
                                <th>البريد الإلكتروني</th>
                                <th>الدور</th>
                                <th>تاريخ التسجيل</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($users_result->num_rows > 0): ?>
                                <?php while($user = $users_result->fetch_assoc()): ?>
                                <tr class="user-row" data-type="<?php echo $user['is_admin'] ? 'admin' : 'user'; ?>">
                                    <td>
                                        <div class="user-info">
                                            <div class="avatar">
                                                <img src="<?php echo ROOT_URL . 'images/users/' . $user['avatar']; ?>" alt="صورة المستخدم">
                                            </div>
                                            <div class="user-details">
                                                <h4><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></h4>
                                                <p>@<?php echo $user['username']; ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td>
                                        <span class="badge <?php echo $user['is_admin'] ? 'admin' : 'user'; ?>">
                                            <?php echo $user['is_admin'] ? 'مشرف' : 'مستخدم'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="date"><?php echo format_date($user['created_at']); ?></span>
                                    </td>
                                    <td class="table__actions">
                                        <a href="view-user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary" title="عرض">
                                            <i class="uil uil-eye"></i>
                                        </a>
                                        <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-secondary" title="تعديل">
                                            <i class="uil uil-edit"></i>
                                        </a>
                                        <?php if($user['id'] !== $_SESSION['user_id']): ?>
                                        <a href="delete-user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')" title="حذف">
                                            <i class="uil uil-trash-alt"></i>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">لا يوجد مستخدمين متاحين حالياً.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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

<script>
    // Search users
    function searchUsers() {
        var input = document.getElementById("userSearch");
        var filter = input.value.toUpperCase();
        var table = document.getElementById("usersTable");
        var tr = table.getElementsByTagName("tr");
        var found = false;
        
        // Get current filter type
        var filterType = document.getElementById("userTypeFilter").value;
        
        // Loop through all table rows, and hide those who don't match the search query
        for (var i = 1; i < tr.length; i++) { // Start from 1 to skip header row
            var tdName = tr[i].getElementsByTagName("td")[0];
            var tdEmail = tr[i].getElementsByTagName("td")[1];
            
            if (tdName && tdEmail) {
                var nameValue = tdName.textContent || tdName.innerText;
                var emailValue = tdEmail.textContent || tdEmail.innerText;
                var rowType = tr[i].getAttribute("data-type");
                
                // Check if row matches both search and filter criteria
                var matchesSearch = nameValue.toUpperCase().indexOf(filter) > -1 || emailValue.toUpperCase().indexOf(filter) > -1;
                var matchesFilter = filterType === "all" || rowType === filterType;
                
                if (matchesSearch && matchesFilter) {
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
            noResults.innerHTML = '<td colspan="5" class="text-center">لا توجد نتائج مطابقة للبحث</td>';
            table.appendChild(noResults);
        }
        
        noResults.style.display = found ? "none" : "";
    }
    
    // Filter users by type
    function filterUsers() {
        var filterType = document.getElementById("userTypeFilter").value;
        var table = document.getElementById("usersTable");
        var tr = table.getElementsByTagName("tr");
        var found = false;
        
        // Get current search term
        var input = document.getElementById("userSearch");
        var filter = input.value.toUpperCase();
        
        // Loop through all table rows, and hide those who don't match the filter
        for (var i = 1; i < tr.length; i++) { // Start from 1 to skip header row
            var tdName = tr[i].getElementsByTagName("td")[0];
            var tdEmail = tr[i].getElementsByTagName("td")[1];
            
            if (tdName && tdEmail) {
                var nameValue = tdName.textContent || tdName.innerText;
                var emailValue = tdEmail.textContent || tdEmail.innerText;
                var rowType = tr[i].getAttribute("data-type");
                
                // Check if row matches both search and filter criteria
                var matchesSearch = nameValue.toUpperCase().indexOf(filter) > -1 || emailValue.toUpperCase().indexOf(filter) > -1;
                var matchesFilter = filterType === "all" || rowType === filterType;
                
                if (matchesSearch && matchesFilter) {
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
            noResults.innerHTML = '<td colspan="5" class="text-center">لا توجد نتائج مطابقة للبحث</td>';
            table.appendChild(noResults);
        }
        
        noResults.style.display = found ? "none" : "";
    }
</script>
