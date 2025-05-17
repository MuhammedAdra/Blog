<?php
require_once 'config/database.php';

// إضافة فئات متنوعة
$categories = [
    ['السفر', 'مقالات عن السفر والمغامرات حول العالم'],
    ['التكنولوجيا', 'آخر أخبار وتطورات عالم التكنولوجيا'],
    ['الصحة', 'نصائح ومعلومات للحفاظ على صحة جيدة'],
    ['الطبخ', 'وصفات شهية ونصائح للطبخ'],
    ['الرياضة', 'أخبار ومعلومات عن مختلف الرياضات']
];

foreach ($categories as $category) {
    // التحقق من وجود الفئة
    $check_query = "SELECT * FROM categories WHERE title = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param('s', $category[0]);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        // إضافة الفئة إذا لم تكن موجودة
        $insert_query = "INSERT INTO categories (title, description) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param('ss', $category[0], $category[1]);
        $insert_stmt->execute();
        echo "تمت إضافة فئة: " . $category[0] . "<br>";
    } else {
        echo "الفئة موجودة بالفعل: " . $category[0] . "<br>";
    }
}

echo "<br>تم الانتهاء من إضافة الفئات.";
?>
