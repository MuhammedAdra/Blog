<!-- Dashboard Sidebar -->
<aside class="dashboard__sidebar">
    <ul>
        <li>
            <a href="<?php echo ROOT_URL; ?>admin/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="uil uil-dashboard"></i>
                <h5>لوحة التحكم</h5>
            </a>
        </li>
        <li>
            <a href="<?php echo ROOT_URL; ?>admin/add-post-new.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'add-post-new.php' ? 'active' : ''; ?>">
                <i class="uil uil-plus-circle"></i>
                <h5>إضافة مقال</h5>
            </a>
        </li>
        <li>
            <a href="<?php echo ROOT_URL; ?>admin/manage-posts.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage-posts.php' ? 'active' : ''; ?>">
                <i class="uil uil-postcard"></i>
                <h5>إدارة المقالات</h5>
            </a>
        </li>
        <li>
            <a href="<?php echo ROOT_URL; ?>admin/manage-categories-new.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage-categories-new.php' || basename($_SERVER['PHP_SELF']) == 'edit-category-new.php' ? 'active' : ''; ?>">
                <i class="uil uil-list-ul"></i>
                <h5>إدارة الفئات</h5>
            </a>
        </li>
        <li>
            <a href="<?php echo ROOT_URL; ?>admin/manage-users-new.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage-users-new.php' ? 'active' : ''; ?>">
                <i class="uil uil-users-alt"></i>
                <h5>إدارة المستخدمين</h5>
            </a>
        </li>
        <li>
            <a href="<?php echo ROOT_URL; ?>admin/manage-comments.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage-comments.php' ? 'active' : ''; ?>">
                <i class="uil uil-comments"></i>
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
