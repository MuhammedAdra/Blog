<?php
require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'includes/functions.php';

// تكوين PHP
echo "<h1>تكوين PHP</h1>";
echo "<ul>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>max_execution_time: " . ini_get('max_execution_time') . "</li>";
echo "<li>max_input_time: " . ini_get('max_input_time') . "</li>";
echo "<li>memory_limit: " . ini_get('memory_limit') . "</li>";
echo "<li>file_uploads: " . (ini_get('file_uploads') ? 'On' : 'Off') . "</li>";
echo "</ul>";

// التحقق من المجلدات
echo "<h1>التحقق من المجلدات</h1>";
$upload_dir = 'images/posts/';
echo "<ul>";
echo "<li>المجلد موجود: " . (file_exists($upload_dir) ? 'نعم' : 'لا') . "</li>";
echo "<li>المجلد قابل للكتابة: " . (is_writable($upload_dir) ? 'نعم' : 'لا') . "</li>";
echo "</ul>";

// إذا لم يكن المجلد موجودًا، قم بإنشائه
if (!file_exists($upload_dir)) {
    if (mkdir($upload_dir, 0777, true)) {
        echo "<p style='color: green;'>تم إنشاء المجلد بنجاح.</p>";
    } else {
        echo "<p style='color: red;'>فشل في إنشاء المجلد.</p>";
    }
}

// اختبار تحميل الصور
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h1>نتائج التحميل</h1>";

    if (isset($_FILES['test_image']) && $_FILES['test_image']['error'] === 0) {
        echo "<pre>";
        print_r($_FILES['test_image']);
        echo "</pre>";

        // تحميل الصورة باستخدام وظيفة upload_image
        $thumbnail_name = upload_image($_FILES['test_image'], $upload_dir);

        if ($thumbnail_name) {
            echo "<p style='color: green;'>تم تحميل الصورة بنجاح: " . $thumbnail_name . "</p>";
            echo "<img src='" . $upload_dir . $thumbnail_name . "' style='max-width: 300px;'>";
        } else {
            echo "<p style='color: red;'>فشل في تحميل الصورة.</p>";
        }
    } else {
        echo "<p style='color: red;'>لم يتم تحديد صورة أو حدث خطأ أثناء التحميل.</p>";
        if (isset($_FILES['test_image'])) {
            echo "<p>رمز الخطأ: " . $_FILES['test_image']['error'] . "</p>";

            // شرح رموز الأخطاء
            $error_codes = [
                0 => 'لا يوجد خطأ، تم تحميل الملف بنجاح.',
                1 => 'حجم الملف المحمل أكبر من الحد المسموح به في php.ini.',
                2 => 'حجم الملف المحمل أكبر من الحد المسموح به في نموذج HTML.',
                3 => 'تم تحميل جزء من الملف فقط.',
                4 => 'لم يتم تحميل أي ملف.',
                6 => 'المجلد المؤقت مفقود.',
                7 => 'فشل في كتابة الملف على القرص.',
                8 => 'أوقف امتداد PHP تحميل الملف.'
            ];

            echo "<p>شرح الخطأ: " . ($error_codes[$_FILES['test_image']['error']] ?? 'خطأ غير معروف') . "</p>";
        }
    }
}

// نموذج اختبار التحميل
echo "<h1>اختبار تحميل الصور</h1>";
echo "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='POST' enctype='multipart/form-data'>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label for='test_image'>اختر صورة للتحميل:</label><br>";
echo "<input type='file' name='test_image' id='test_image' accept='image/*'>";
echo "</div>";
echo "<button type='submit' style='padding: 10px 20px; background-color: #4361ee; color: white; border: none; border-radius: 5px; cursor: pointer;'>تحميل الصورة</button>";
echo "</form>";

// روابط مفيدة
echo "<h1>روابط مفيدة</h1>";
echo "<ul>";
echo "<li><a href='index.php'>الصفحة الرئيسية</a></li>";
echo "<li><a href='admin/dashboard.php'>لوحة التحكم</a></li>";
echo "<li><a href='admin/add-post.php'>إضافة مقال جديد</a></li>";
echo "</ul>";