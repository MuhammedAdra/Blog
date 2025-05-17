<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Process contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        set_message(ERROR_MESSAGE, 'جميع الحقول مطلوبة');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_message(ERROR_MESSAGE, 'يرجى إدخال بريد إلكتروني صالح');
    } else {
        // Insert message into database
        $query = "INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssss', $name, $email, $subject, $message);
        
        if ($stmt->execute()) {
            set_message(SUCCESS_MESSAGE, 'تم إرسال رسالتك بنجاح. سنتواصل معك قريباً.');
            redirect_to('contact.php');
        } else {
            set_message(ERROR_MESSAGE, 'حدث خطأ أثناء إرسال رسالتك. يرجى المحاولة مرة أخرى.');
        }
    }
}

// Page title
$page_title = 'اتصل بنا';

// Include header
include_once 'partials/header.php';
?>

<!-- Contact Section -->
<section class="contact">
    <div class="container contact__container">
        <div class="contact__header">
            <h1>اتصل بنا</h1>
            <p>نحن هنا للإجابة على استفساراتك ومساعدتك في أي وقت</p>
        </div>
        
        <div class="contact__wrapper">
            <div class="contact__info">
                <div class="contact__card">
                    <div class="contact__card-icon">
                        <i class="uil uil-envelope"></i>
                    </div>
                    <h3>البريد الإلكتروني</h3>
                    <p>info@example.com</p>
                    <a href="mailto:info@example.com" class="btn btn-outline">أرسل بريداً إلكترونياً</a>
                </div>
                
                <div class="contact__card">
                    <div class="contact__card-icon">
                        <i class="uil uil-phone"></i>
                    </div>
                    <h3>الهاتف</h3>
                    <p>+123 456 7890</p>
                    <a href="tel:+1234567890" class="btn btn-outline">اتصل بنا</a>
                </div>
                
                <div class="contact__card">
                    <div class="contact__card-icon">
                        <i class="uil uil-map-marker"></i>
                    </div>
                    <h3>العنوان</h3>
                    <p>123 شارع المثال، المدينة، البلد</p>
                    <a href="https://maps.google.com" target="_blank" class="btn btn-outline">عرض على الخريطة</a>
                </div>
                
                <div class="contact__socials">
                    <h3>تابعنا على</h3>
                    <div class="contact__socials-links">
                        <a href="#" target="_blank"><i class="uil uil-facebook-f"></i></a>
                        <a href="#" target="_blank"><i class="uil uil-twitter"></i></a>
                        <a href="#" target="_blank"><i class="uil uil-instagram"></i></a>
                        <a href="#" target="_blank"><i class="uil uil-linkedin"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="contact__form-container">
                <h3>أرسل لنا رسالة</h3>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="contact__form">
                    <div class="form__control">
                        <label for="name">الاسم</label>
                        <input type="text" id="name" name="name" placeholder="أدخل اسمك" required>
                    </div>
                    
                    <div class="form__control">
                        <label for="email">البريد الإلكتروني</label>
                        <input type="email" id="email" name="email" placeholder="أدخل بريدك الإلكتروني" required>
                    </div>
                    
                    <div class="form__control">
                        <label for="subject">الموضوع</label>
                        <input type="text" id="subject" name="subject" placeholder="أدخل موضوع الرسالة" required>
                    </div>
                    
                    <div class="form__control">
                        <label for="message">الرسالة</label>
                        <textarea id="message" name="message" rows="7" placeholder="اكتب رسالتك هنا..." required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="uil uil-message"></i> إرسال الرسالة
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
<!-- End of Contact Section -->

<!-- Map Section -->
<section class="map">
    <div class="container">
        <div class="map__container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.30591910525!2d-74.25986432970718!3d40.697149422113014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2s!4v1625124127903!5m2!1sen!2s" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>
<!-- End of Map Section -->

<!-- FAQ Section -->
<section class="faq">
    <div class="container faq__container">
        <div class="faq__header">
            <h2>الأسئلة الشائعة</h2>
            <p>إليك بعض الأسئلة الشائعة التي قد تساعدك</p>
        </div>
        
        <div class="faq__list">
            <div class="faq__item">
                <div class="faq__question">
                    <h4>كيف يمكنني إنشاء حساب؟</h4>
                    <i class="uil uil-plus"></i>
                </div>
                <div class="faq__answer">
                    <p>يمكنك إنشاء حساب بالنقر على زر "تسجيل" في أعلى الصفحة، ثم ملء النموذج بالمعلومات المطلوبة.</p>
                </div>
            </div>
            
            <div class="faq__item">
                <div class="faq__question">
                    <h4>كيف يمكنني نشر مقال؟</h4>
                    <i class="uil uil-plus"></i>
                </div>
                <div class="faq__answer">
                    <p>بعد تسجيل الدخول، يمكنك النقر على "لوحة التحكم" ثم "إضافة مقال" لكتابة ونشر مقال جديد.</p>
                </div>
            </div>
            
            <div class="faq__item">
                <div class="faq__question">
                    <h4>هل يمكنني تعديل مقالاتي بعد نشرها؟</h4>
                    <i class="uil uil-plus"></i>
                </div>
                <div class="faq__answer">
                    <p>نعم، يمكنك تعديل مقالاتك في أي وقت من خلال الذهاب إلى "لوحة التحكم" ثم "إدارة المقالات".</p>
                </div>
            </div>
            
            <div class="faq__item">
                <div class="faq__question">
                    <h4>كيف يمكنني الاشتراك في النشرة الإخبارية؟</h4>
                    <i class="uil uil-plus"></i>
                </div>
                <div class="faq__answer">
                    <p>يمكنك الاشتراك في النشرة الإخبارية من خلال إدخال بريدك الإلكتروني في نموذج الاشتراك الموجود في أسفل الصفحة الرئيسية.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End of FAQ Section -->

<?php
// Include footer
include_once 'partials/footer.php';
?>

<script>
    // FAQ Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const faqItems = document.querySelectorAll('.faq__item');
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq__question');
            
            question.addEventListener('click', () => {
                // Close all other items
                faqItems.forEach(otherItem => {
                    if (otherItem !== item && otherItem.classList.contains('active')) {
                        otherItem.classList.remove('active');
                    }
                });
                
                // Toggle current item
                item.classList.toggle('active');
            });
        });
    });
</script>
