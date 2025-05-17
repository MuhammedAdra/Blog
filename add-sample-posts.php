<?php
require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'includes/functions.php';

// التحقق من وجود مستخدم مسؤول
$admin_query = "SELECT * FROM users WHERE is_admin = 1 LIMIT 1";
$admin_result = $conn->query($admin_query);
$admin = $admin_result->fetch_assoc();

if (!$admin) {
    echo "لا يوجد مستخدم مسؤول. يرجى إنشاء مستخدم مسؤول أولاً.";
    exit;
}

$admin_id = $admin['id'];

// الحصول على الفئات
$categories_query = "SELECT * FROM categories";
$categories_result = $conn->query($categories_query);
$categories = [];

while ($category = $categories_result->fetch_assoc()) {
    $categories[$category['title']] = $category['id'];
}

// إضافة مقالات متنوعة
$sample_posts = [
    // مقال سفر
    [
        'title' => 'استكشاف جمال الطبيعة في جزر المالديف',
        'body' => '<h2>جنة على الأرض</h2>
        <p>تعتبر جزر المالديف واحدة من أجمل الوجهات السياحية في العالم، حيث تتميز بشواطئها البيضاء الناعمة ومياهها الفيروزية الصافية.</p>
        <p>تقع جزر المالديف في المحيط الهندي وتتكون من حوالي 1200 جزيرة مرجانية، منها حوالي 200 جزيرة مأهولة بالسكان.</p>
        <h3>أفضل الأنشطة في المالديف</h3>
        <ul>
            <li>الغوص واستكشاف الشعاب المرجانية</li>
            <li>الاسترخاء على الشواطئ البيضاء</li>
            <li>الإقامة في الفنادق العائمة فوق الماء</li>
            <li>ركوب القوارب الزجاجية لمشاهدة الحياة البحرية</li>
            <li>الاستمتاع بالمأكولات البحرية الطازجة</li>
        </ul>
        <p>أفضل وقت لزيارة المالديف هو بين نوفمبر وأبريل، حيث يكون الطقس مشمسًا ومعتدلًا.</p>',
        'excerpt' => 'اكتشف جمال جزر المالديف الساحرة بشواطئها البيضاء ومياهها الفيروزية وتعرف على أفضل الأنشطة والأوقات لزيارة هذه الجنة الاستوائية.',
        'category' => 'السفر',
        'is_featured' => 1,
        'thumbnail' => 'maldives.jpg',
        'tags' => 'سفر, سياحة, جزر, المالديف, شواطئ'
    ],
    
    // مقال تكنولوجيا
    [
        'title' => 'مستقبل الذكاء الاصطناعي وتأثيره على حياتنا',
        'body' => '<h2>ثورة الذكاء الاصطناعي</h2>
        <p>يشهد العالم تطورًا متسارعًا في مجال الذكاء الاصطناعي، مما يؤثر على مختلف جوانب حياتنا اليومية والمهنية.</p>
        <p>من المتوقع أن يساهم الذكاء الاصطناعي في حل العديد من المشكلات المعقدة في مجالات الطب والتعليم والبيئة وغيرها.</p>
        <h3>تطبيقات الذكاء الاصطناعي</h3>
        <ol>
            <li>التشخيص الطبي ودعم القرارات الصحية</li>
            <li>السيارات ذاتية القيادة</li>
            <li>المساعدين الافتراضيين مثل سيري وأليكسا</li>
            <li>التنبؤ بالطقس والكوارث الطبيعية</li>
            <li>تحسين كفاءة استهلاك الطاقة</li>
        </ol>
        <p>رغم الفوائد العديدة للذكاء الاصطناعي، هناك مخاوف بشأن تأثيره على سوق العمل وخصوصية البيانات والأمن السيبراني.</p>',
        'excerpt' => 'استكشف مستقبل الذكاء الاصطناعي وتطبيقاته المختلفة وتأثيره المتوقع على حياتنا اليومية والمهنية، مع نظرة على الفرص والتحديات التي يقدمها.',
        'category' => 'التكنولوجيا',
        'is_featured' => 0,
        'thumbnail' => 'ai-future.jpg',
        'tags' => 'تكنولوجيا, ذكاء اصطناعي, مستقبل, تقنية'
    ],
    
    // مقال صحة
    [
        'title' => 'فوائد التمارين الرياضية للصحة النفسية والجسدية',
        'body' => '<h2>الرياضة والصحة</h2>
        <p>تعتبر ممارسة التمارين الرياضية بانتظام من أهم العوامل للحفاظ على صحة جيدة، سواء على المستوى الجسدي أو النفسي.</p>
        <p>أظهرت الدراسات أن ممارسة الرياضة لمدة 30 دقيقة يوميًا يمكن أن تقلل من خطر الإصابة بالعديد من الأمراض المزمنة.</p>
        <h3>الفوائد الصحية للرياضة</h3>
        <ul>
            <li>تقوية عضلة القلب وتحسين الدورة الدموية</li>
            <li>تقليل مستويات التوتر والقلق</li>
            <li>تحسين جودة النوم</li>
            <li>زيادة الطاقة والحيوية</li>
            <li>المساعدة في الحفاظ على وزن صحي</li>
        </ul>
        <p>ينصح الخبراء بممارسة مزيج من تمارين القوة والتمارين الهوائية للحصول على أفضل النتائج الصحية.</p>',
        'excerpt' => 'تعرف على الفوائد المتعددة للتمارين الرياضية للصحة النفسية والجسدية، وكيف يمكن أن تساعد 30 دقيقة من النشاط البدني يوميًا في تحسين جودة حياتك.',
        'category' => 'الصحة',
        'is_featured' => 0,
        'thumbnail' => 'exercise.jpg',
        'tags' => 'صحة, رياضة, لياقة, تمارين, صحة نفسية'
    ],
    
    // مقال طبخ
    [
        'title' => 'وصفة سهلة لتحضير الكنافة بالقشطة',
        'body' => '<h2>الكنافة بالقشطة</h2>
        <p>تعتبر الكنافة من أشهر الحلويات العربية، وتتميز بمذاقها اللذيذ وقيمتها الغذائية العالية.</p>
        <p>إليكم وصفة سهلة وسريعة لتحضير الكنافة بالقشطة في المنزل.</p>
        <h3>المكونات</h3>
        <ul>
            <li>500 غرام عجينة كنافة</li>
            <li>200 غرام زبدة مذابة</li>
            <li>500 مل حليب</li>
            <li>3 ملاعق كبيرة نشا</li>
            <li>3 ملاعق كبيرة سكر</li>
            <li>1 ملعقة صغيرة فانيليا</li>
            <li>2 كوب سكر</li>
            <li>1 كوب ماء</li>
            <li>عصير نصف ليمونة</li>
            <li>فستق حلبي للتزيين</li>
        </ul>
        <h3>طريقة التحضير</h3>
        <ol>
            <li>نقوم بتفتيت عجينة الكنافة ونضيف إليها الزبدة المذابة ونخلطها جيدًا.</li>
            <li>نضع نصف كمية العجينة في صينية مدهونة بالزبدة ونضغط عليها قليلًا.</li>
            <li>لتحضير القشطة، نضع الحليب في قدر على النار ونضيف النشا والسكر والفانيليا ونحرك حتى تتكاثف.</li>
            <li>نضع القشطة فوق طبقة الكنافة ثم نغطيها بالنصف الآخر من العجينة.</li>
            <li>نخبز الكنافة في فرن محمى مسبقًا على درجة حرارة 180 درجة مئوية لمدة 30-35 دقيقة حتى تصبح ذهبية اللون.</li>
            <li>لتحضير القطر، نضع السكر والماء في قدر على النار ونضيف عصير الليمون ونتركه يغلي لمدة 10 دقائق.</li>
            <li>نسكب القطر الساخن على الكنافة الساخنة ونزينها بالفستق الحلبي.</li>
        </ol>
        <p>قدموا الكنافة ساخنة أو باردة حسب الرغبة، وبالهناء والشفاء!</p>',
        'excerpt' => 'تعلم كيفية تحضير الكنافة بالقشطة اللذيذة في المنزل بخطوات بسيطة وسهلة. وصفة تقليدية بنكهة مميزة ستنال إعجاب العائلة والضيوف.',
        'category' => 'الطبخ',
        'is_featured' => 0,
        'thumbnail' => 'kunafa.jpg',
        'tags' => 'طبخ, حلويات, وصفات, كنافة, حلويات شرقية'
    ],
    
    // مقال رياضة
    [
        'title' => 'أهم النصائح لممارسة رياضة الجري بشكل صحيح',
        'body' => '<h2>فن الجري</h2>
        <p>تعتبر رياضة الجري من أكثر الرياضات شعبية وفعالية لتحسين اللياقة البدنية وحرق السعرات الحرارية.</p>
        <p>لكن لتحقيق أقصى استفادة من هذه الرياضة وتجنب الإصابات، يجب ممارستها بالطريقة الصحيحة.</p>
        <h3>نصائح لممارسة الجري بشكل صحيح</h3>
        <ol>
            <li><strong>اختيار الحذاء المناسب:</strong> استثمر في حذاء جري جيد يناسب شكل قدمك ويوفر الدعم المناسب.</li>
            <li><strong>الإحماء قبل الجري:</strong> قم بتمارين إحماء لمدة 5-10 دقائق لتهيئة العضلات والمفاصل.</li>
            <li><strong>البدء ببطء:</strong> إذا كنت مبتدئًا، ابدأ بالمشي السريع ثم التناوب بين المشي والجري.</li>
            <li><strong>الحفاظ على وضعية صحيحة:</strong> حافظ على استقامة الظهر والرأس والنظر للأمام.</li>
            <li><strong>التنفس بشكل صحيح:</strong> تنفس بشكل طبيعي من الأنف والفم معًا.</li>
            <li><strong>زيادة المسافة تدريجيًا:</strong> لا تزيد مسافة الجري أكثر من 10% أسبوعيًا.</li>
            <li><strong>الاستماع لجسمك:</strong> توقف إذا شعرت بألم حاد وليس مجرد تعب.</li>
        </ol>
        <p>تذكر أن الاستمرارية هي المفتاح لتحقيق نتائج جيدة في رياضة الجري، حاول الالتزام بجدول منتظم للتمرين.</p>',
        'excerpt' => 'اكتشف أهم النصائح لممارسة رياضة الجري بشكل صحيح وآمن، من اختيار الحذاء المناسب إلى تقنيات التنفس الصحيحة وكيفية تجنب الإصابات الشائعة.',
        'category' => 'الرياضة',
        'is_featured' => 0,
        'thumbnail' => 'running.jpg',
        'tags' => 'رياضة, جري, لياقة, تمارين, صحة'
    ]
];

// تحميل الصور
$sample_images = [
    'maldives.jpg' => 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
    'ai-future.jpg' => 'https://images.unsplash.com/photo-1677442135968-6d89469c6409?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1332&q=80',
    'exercise.jpg' => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
    'kunafa.jpg' => 'https://images.unsplash.com/photo-1579888071069-c107a6f79d82?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
    'running.jpg' => 'https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80'
];

// تحميل الصور
foreach ($sample_images as $filename => $url) {
    $img_path = 'images/posts/' . $filename;
    
    // التحقق من وجود الصورة
    if (!file_exists($img_path)) {
        $img_content = file_get_contents($url);
        if ($img_content !== false) {
            file_put_contents($img_path, $img_content);
            echo "تم تحميل الصورة: " . $filename . "<br>";
        } else {
            echo "فشل في تحميل الصورة: " . $filename . "<br>";
        }
    } else {
        echo "الصورة موجودة بالفعل: " . $filename . "<br>";
    }
}

echo "<hr>";

// إضافة المقالات
foreach ($sample_posts as $post) {
    // التحقق من وجود المقال
    $check_query = "SELECT * FROM posts WHERE title = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param('s', $post['title']);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        // الحصول على معرف الفئة
        $category_id = $categories[$post['category']] ?? null;
        
        if ($category_id) {
            // إضافة المقال
            $insert_query = "INSERT INTO posts (title, body, thumbnail, category_id, author_id, is_featured, excerpt, tags, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'published')";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param('sssiisss', 
                $post['title'], 
                $post['body'], 
                $post['thumbnail'], 
                $category_id, 
                $admin_id, 
                $post['is_featured'], 
                $post['excerpt'], 
                $post['tags']
            );
            
            if ($insert_stmt->execute()) {
                echo "تمت إضافة مقال: " . $post['title'] . "<br>";
                
                // إذا كان المقال مميزًا، قم بإلغاء تمييز المقالات الأخرى
                if ($post['is_featured']) {
                    $post_id = $insert_stmt->insert_id;
                    $unfeature_query = "UPDATE posts SET is_featured = 0 WHERE id != ?";
                    $unfeature_stmt = $conn->prepare($unfeature_query);
                    $unfeature_stmt->bind_param('i', $post_id);
                    $unfeature_stmt->execute();
                }
            } else {
                echo "فشل في إضافة مقال: " . $post['title'] . " - " . $conn->error . "<br>";
            }
        } else {
            echo "فئة غير موجودة: " . $post['category'] . " للمقال: " . $post['title'] . "<br>";
        }
    } else {
        echo "المقال موجود بالفعل: " . $post['title'] . "<br>";
    }
}

echo "<br>تم الانتهاء من إضافة المقالات النموذجية.";
echo "<br><a href='index.php'>العودة إلى الصفحة الرئيسية</a>";
?>
