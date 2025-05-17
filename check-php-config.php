<?php
// عرض معلومات PHP
echo "<h1>معلومات PHP</h1>";
echo "<h2>إصدار PHP: " . phpversion() . "</h2>";

// التحقق من إعدادات تحميل الملفات
echo "<h2>إعدادات تحميل الملفات</h2>";
echo "<ul>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>max_execution_time: " . ini_get('max_execution_time') . "</li>";
echo "<li>max_input_time: " . ini_get('max_input_time') . "</li>";
echo "<li>memory_limit: " . ini_get('memory_limit') . "</li>";
echo "<li>file_uploads: " . (ini_get('file_uploads') ? 'On' : 'Off') . "</li>";
echo "</ul>";

// التحقق من الأذونات
echo "<h2>أذونات المجلدات</h2>";
echo "<ul>";
echo "<li>images/posts/ - قابل للكتابة: " . (is_writable('images/posts/') ? 'نعم' : 'لا') . "</li>";
echo "</ul>";

// التحقق من وجود مجلد الصور
if (!file_exists('images/posts/')) {
    echo "<p style='color: red;'>تنبيه: مجلد images/posts/ غير موجود!</p>";
    
    // محاولة إنشاء المجلد
    if (mkdir('images/posts/', 0777, true)) {
        echo "<p style='color: green;'>تم إنشاء المجلد بنجاح.</p>";
    } else {
        echo "<p style='color: red;'>فشل في إنشاء المجلد.</p>";
    }
}

// اختبار تحميل الملفات
echo "<h2>اختبار تحميل الملفات</h2>";
echo "<form action='test-upload.php' method='POST' enctype='multipart/form-data'>";
echo "<input type='file' name='test_file'>";
echo "<button type='submit'>اختبار التحميل</button>";
echo "</form>";

// إنشاء ملف اختبار التحميل
$test_upload_file = <<<'EOT'
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<h1>نتائج اختبار التحميل</h1>";
    echo "<pre>";
    print_r($_FILES['test_file']);
    echo "</pre>";
    
    if ($_FILES['test_file']['error'] === 0) {
        $upload_dir = 'images/posts/';
        $file_name = $_FILES['test_file']['name'];
        $file_tmp = $_FILES['test_file']['tmp_name'];
        $file_dest = $upload_dir . $file_name;
        
        if (move_uploaded_file($file_tmp, $file_dest)) {
            echo "<p style='color: green;'>تم تحميل الملف بنجاح إلى: $file_dest</p>";
            echo "<img src='$file_dest' style='max-width: 300px;'>";
        } else {
            echo "<p style='color: red;'>فشل في تحميل الملف!</p>";
        }
    } else {
        echo "<p style='color: red;'>حدث خطأ أثناء تحميل الملف: " . $_FILES['test_file']['error'] . "</p>";
    }
}
EOT;

file_put_contents('test-upload.php', $test_upload_file);

// التحقق من وظيفة upload_image
echo "<h2>التحقق من وظيفة upload_image</h2>";
require_once 'includes/functions.php';

echo "<p>وظيفة upload_image موجودة: " . (function_exists('upload_image') ? 'نعم' : 'لا') . "</p>";

// التحقق من الاتصال بقاعدة البيانات
echo "<h2>التحقق من الاتصال بقاعدة البيانات</h2>";
require_once 'config/database.php';

echo "<p>الاتصال بقاعدة البيانات: " . ($conn ? 'ناجح' : 'فاشل') . "</p>";

// التحقق من جدول posts
$posts_table_query = "SHOW TABLES LIKE 'posts'";
$posts_table_result = $conn->query($posts_table_query);

echo "<p>جدول posts موجود: " . ($posts_table_result->num_rows > 0 ? 'نعم' : 'لا') . "</p>";

if ($posts_table_result->num_rows > 0) {
    $columns_query = "SHOW COLUMNS FROM posts";
    $columns_result = $conn->query($columns_query);
    
    echo "<h3>أعمدة جدول posts:</h3>";
    echo "<ul>";
    while ($column = $columns_result->fetch_assoc()) {
        echo "<li>" . $column['Field'] . " - " . $column['Type'] . "</li>";
    }
    echo "</ul>";
}

// التحقق من الجلسة
echo "<h2>التحقق من الجلسة</h2>";
session_start();
echo "<p>الجلسة نشطة: " . (session_status() === PHP_SESSION_ACTIVE ? 'نعم' : 'لا') . "</p>";
echo "<p>معرف المستخدم في الجلسة: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'غير موجود') . "</p>";
echo "<p>المستخدم مسؤول: " . (isset($_SESSION['user_is_admin']) && $_SESSION['user_is_admin'] == 1 ? 'نعم' : 'لا') . "</p>";

// روابط مفيدة
echo "<h2>روابط مفيدة</h2>";
echo "<ul>";
echo "<li><a href='index.php'>الصفحة الرئيسية</a></li>";
echo "<li><a href='admin/dashboard.php'>لوحة التحكم</a></li>";
echo "<li><a href='admin/add-post.php'>إضافة مقال جديد</a></li>";
echo "</ul>";
?>
