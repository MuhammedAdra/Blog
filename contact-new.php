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
            redirect_to('contact-new.php');
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

<!-- Contact Hero Section -->
<section class="contact-hero">
    <div class="container">
        <div class="contact-hero__content">
            <h1>تواصل معنا</h1>
            <p>نحن هنا للإجابة على استفساراتك ومساعدتك في أي وقت</p>
        </div>
    </div>
</section>

<!-- Contact Main Section -->
<section class="contact-main">
    <div class="container">
        <div class="contact-main__wrapper">
            <!-- Contact Form -->
            <div class="contact-form__container">
                <div class="contact-form__header">
                    <div class="contact-form__icon">
                        <i class="uil uil-message"></i>
                    </div>
                    <h2>أرسل لنا رسالة</h2>
                    <p>يمكنك التواصل معنا عبر ملء النموذج أدناه وسنقوم بالرد عليك في أقرب وقت ممكن</p>
                </div>
                
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="contact-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">الاسم</label>
                            <div class="input-with-icon">
                                <input type="text" id="name" name="name" placeholder="أدخل اسمك" required>
                                <i class="uil uil-user"></i>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">البريد الإلكتروني</label>
                            <div class="input-with-icon">
                                <input type="email" id="email" name="email" placeholder="أدخل بريدك الإلكتروني" required>
                                <i class="uil uil-envelope"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">الموضوع</label>
                        <div class="input-with-icon">
                            <input type="text" id="subject" name="subject" placeholder="أدخل موضوع الرسالة" required>
                            <i class="uil uil-subject"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">الرسالة</label>
                        <div class="input-with-icon textarea">
                            <textarea id="message" name="message" rows="5" placeholder="اكتب رسالتك هنا..." required></textarea>
                            <i class="uil uil-comment-alt-lines"></i>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="uil uil-message"></i> إرسال الرسالة
                    </button>
                </form>
            </div>
            
            <!-- Contact Info -->
            <div class="contact-info">
                <div class="contact-info__card">
                    <div class="contact-info__header">
                        <h3>معلومات الاتصال</h3>
                        <p>يمكنك التواصل معنا مباشرة عبر أي من وسائل الاتصال التالية</p>
                    </div>
                    
                    <ul class="contact-info__list">
                        <li>
                            <div class="contact-info__icon">
                                <i class="uil uil-phone"></i>
                            </div>
                            <div class="contact-info__details">
                                <h4>الهاتف</h4>
                                <p>+123 456 7890</p>
                                <a href="tel:+1234567890" class="contact-link">اتصل الآن</a>
                            </div>
                        </li>
                        
                        <li>
                            <div class="contact-info__icon">
                                <i class="uil uil-envelope"></i>
                            </div>
                            <div class="contact-info__details">
                                <h4>البريد الإلكتروني</h4>
                                <p>info@example.com</p>
                                <a href="mailto:info@example.com" class="contact-link">أرسل بريداً إلكترونياً</a>
                            </div>
                        </li>
                        
                        <li>
                            <div class="contact-info__icon">
                                <i class="uil uil-map-marker"></i>
                            </div>
                            <div class="contact-info__details">
                                <h4>العنوان</h4>
                                <p>123 شارع المثال، المدينة، البلد</p>
                                <a href="https://maps.google.com" target="_blank" class="contact-link">عرض على الخريطة</a>
                            </div>
                        </li>
                    </ul>
                    
                    <div class="contact-info__socials">
                        <h4>تابعنا على</h4>
                        <div class="social-links">
                            <a href="#" class="social-link facebook"><i class="uil uil-facebook-f"></i></a>
                            <a href="#" class="social-link twitter"><i class="uil uil-twitter"></i></a>
                            <a href="#" class="social-link instagram"><i class="uil uil-instagram"></i></a>
                            <a href="#" class="social-link linkedin"><i class="uil uil-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="contact-info__hours">
                    <h3>ساعات العمل</h3>
                    <ul>
                        <li>
                            <span class="day">الأحد - الخميس:</span>
                            <span class="hours">9:00 صباحاً - 5:00 مساءً</span>
                        </li>
                        <li>
                            <span class="day">الجمعة:</span>
                            <span class="hours">9:00 صباحاً - 1:00 ظهراً</span>
                        </li>
                        <li>
                            <span class="day">السبت:</span>
                            <span class="hours">مغلق</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section">
    <div class="container">
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.30591910525!2d-74.25986432970718!3d40.697149422113014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2s!4v1625124127903!5m2!1sen!2s" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <div class="faq-header">
            <h2>الأسئلة الشائعة</h2>
            <p>إليك بعض الأسئلة الشائعة التي قد تساعدك</p>
        </div>
        
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question">
                    <h4>كيف يمكنني إنشاء حساب؟</h4>
                    <span class="faq-toggle"><i class="uil uil-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>يمكنك إنشاء حساب بالنقر على زر "تسجيل" في أعلى الصفحة، ثم ملء النموذج بالمعلومات المطلوبة.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h4>كيف يمكنني نشر مقال؟</h4>
                    <span class="faq-toggle"><i class="uil uil-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>بعد تسجيل الدخول، يمكنك النقر على "لوحة التحكم" ثم "إضافة مقال" لكتابة ونشر مقال جديد.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h4>هل يمكنني تعديل مقالاتي بعد نشرها؟</h4>
                    <span class="faq-toggle"><i class="uil uil-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>نعم، يمكنك تعديل مقالاتك في أي وقت من خلال الذهاب إلى "لوحة التحكم" ثم "إدارة المقالات".</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h4>كيف يمكنني الاشتراك في النشرة الإخبارية؟</h4>
                    <span class="faq-toggle"><i class="uil uil-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>يمكنك الاشتراك في النشرة الإخبارية من خلال إدخال بريدك الإلكتروني في نموذج الاشتراك الموجود في أسفل الصفحة الرئيسية.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include_once 'partials/footer.php';
?>

<style>
/* Contact Page Styles */
.contact-hero {
    background: linear-gradient(to right, var(--color-primary), var(--color-primary-vibrant));
    padding: 5rem 0;
    color: var(--color-white);
    text-align: center;
    position: relative;
    overflow: hidden;
}

.contact-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.contact-hero__content h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
    position: relative;
}

.contact-hero__content p {
    font-size: 1.2rem;
    max-width: 600px;
    margin: 0 auto;
    opacity: 0.9;
}

.contact-main {
    padding: 5rem 0;
    background: var(--color-white);
    margin-top: -3rem;
    position: relative;
    z-index: 2;
}

.contact-main__wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    align-items: start;
}

.contact-form__container {
    background: var(--color-white);
    border-radius: var(--card-border-radius-3);
    box-shadow: var(--shadow-lg);
    padding: 2.5rem;
    transition: var(--transition);
    border: 1px solid rgba(67, 97, 238, 0.1);
}

.contact-form__container:hover {
    box-shadow: var(--shadow-xl);
    transform: translateY(-5px);
}

.contact-form__header {
    text-align: center;
    margin-bottom: 2rem;
}

.contact-form__icon {
    width: 70px;
    height: 70px;
    background: var(--color-primary-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}

.contact-form__icon i {
    font-size: 2.5rem;
    color: var(--color-primary);
}

.contact-form__header h2 {
    font-size: 1.8rem;
    margin-bottom: 0.5rem;
    color: var(--color-gray-900);
}

.contact-form__header p {
    color: var(--color-gray-700);
    font-size: 1rem;
}

.contact-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 500;
    color: var(--color-gray-800);
    font-size: 0.95rem;
}

.input-with-icon {
    position: relative;
}

.input-with-icon input,
.input-with-icon textarea {
    width: 100%;
    padding: 1rem 1rem 1rem 3rem;
    border: 1px solid var(--color-gray-300);
    border-radius: var(--card-border-radius-2);
    font-family: inherit;
    font-size: 1rem;
    transition: var(--transition);
}

.input-with-icon.textarea {
    height: 100%;
}

.input-with-icon textarea {
    resize: none;
    height: 150px;
}

.input-with-icon input:focus,
.input-with-icon textarea:focus {
    border-color: var(--color-primary);
    outline: none;
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
}

.input-with-icon i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-gray-600);
    font-size: 1.2rem;
}

.input-with-icon.textarea i {
    top: 1.5rem;
    transform: none;
}

.contact-form .btn {
    align-self: flex-start;
    padding: 1rem 2rem;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.contact-form .btn i {
    font-size: 1.2rem;
}

.contact-info {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.contact-info__card {
    background: var(--color-white);
    border-radius: var(--card-border-radius-3);
    box-shadow: var(--shadow-lg);
    padding: 2.5rem;
    transition: var(--transition);
    border: 1px solid rgba(67, 97, 238, 0.1);
}

.contact-info__card:hover {
    box-shadow: var(--shadow-xl);
    transform: translateY(-5px);
}

.contact-info__header {
    margin-bottom: 2rem;
}

.contact-info__header h3 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: var(--color-gray-900);
}

.contact-info__header p {
    color: var(--color-gray-700);
    font-size: 1rem;
}

.contact-info__list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.contact-info__list li {
    display: flex;
    gap: 1.5rem;
    align-items: flex-start;
}

.contact-info__icon {
    width: 50px;
    height: 50px;
    background: var(--color-primary-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.contact-info__icon i {
    font-size: 1.5rem;
    color: var(--color-primary);
}

.contact-info__details h4 {
    font-size: 1.1rem;
    margin-bottom: 0.3rem;
    color: var(--color-gray-900);
}

.contact-info__details p {
    color: var(--color-gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.contact-link {
    color: var(--color-primary);
    font-size: 0.9rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    transition: var(--transition);
}

.contact-link:hover {
    color: var(--color-primary-vibrant);
    text-decoration: underline;
}

.contact-info__socials {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--color-gray-200);
}

.contact-info__socials h4 {
    font-size: 1.1rem;
    margin-bottom: 1rem;
    color: var(--color-gray-900);
}

.social-links {
    display: flex;
    gap: 1rem;
}

.social-link {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-white);
    font-size: 1.2rem;
    transition: var(--transition);
}

.social-link:hover {
    transform: translateY(-5px);
}

.social-link.facebook {
    background: #4267B2;
}

.social-link.twitter {
    background: #1DA1F2;
}

.social-link.instagram {
    background: linear-gradient(45deg, #405DE6, #5851DB, #833AB4, #C13584, #E1306C, #FD1D1D);
}

.social-link.linkedin {
    background: #0077B5;
}

.contact-info__hours {
    background: var(--color-white);
    border-radius: var(--card-border-radius-3);
    box-shadow: var(--shadow-lg);
    padding: 2.5rem;
    transition: var(--transition);
    border: 1px solid rgba(67, 97, 238, 0.1);
}

.contact-info__hours:hover {
    box-shadow: var(--shadow-xl);
    transform: translateY(-5px);
}

.contact-info__hours h3 {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    color: var(--color-gray-900);
}

.contact-info__hours ul {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.contact-info__hours li {
    display: flex;
    justify-content: space-between;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--color-gray-200);
}

.contact-info__hours li:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.contact-info__hours .day {
    font-weight: 500;
    color: var(--color-gray-800);
}

.contact-info__hours .hours {
    color: var(--color-gray-700);
}

.map-section {
    padding: 0 0 5rem;
}

.map-container {
    border-radius: var(--card-border-radius-3);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    border: 1px solid rgba(67, 97, 238, 0.1);
}

.faq-section {
    padding: 5rem 0;
    background: var(--color-gray-100);
}

.faq-header {
    text-align: center;
    margin-bottom: 3rem;
}

.faq-header h2 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: var(--color-gray-900);
}

.faq-header p {
    color: var(--color-gray-700);
    font-size: 1.2rem;
    max-width: 600px;
    margin: 0 auto;
}

.faq-container {
    max-width: 800px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.faq-item {
    background: var(--color-white);
    border-radius: var(--card-border-radius-2);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    transition: var(--transition);
    border: 1px solid rgba(67, 97, 238, 0.1);
}

.faq-item:hover {
    box-shadow: var(--shadow-lg);
}

.faq-question {
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}

.faq-question h4 {
    font-size: 1.1rem;
    color: var(--color-gray-900);
}

.faq-toggle {
    width: 30px;
    height: 30px;
    background: var(--color-primary-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary);
    transition: var(--transition);
}

.faq-item.active .faq-toggle {
    background: var(--color-primary);
    color: var(--color-white);
    transform: rotate(45deg);
}

.faq-answer {
    padding: 0 1.5rem;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, padding 0.3s ease;
}

.faq-item.active .faq-answer {
    padding: 0 1.5rem 1.5rem;
    max-height: 500px;
}

.faq-answer p {
    color: var(--color-gray-700);
    line-height: 1.6;
}

/* Responsive Styles */
@media screen and (max-width: 992px) {
    .contact-main__wrapper {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}

@media screen and (max-width: 768px) {
    .contact-hero__content h1 {
        font-size: 2.5rem;
    }
    
    .contact-form__container,
    .contact-info__card,
    .contact-info__hours {
        padding: 2rem;
    }
    
    .contact-info__list li {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .contact-info__icon {
        width: 40px;
        height: 40px;
    }
    
    .contact-info__icon i {
        font-size: 1.2rem;
    }
}
</style>

<script>
    // FAQ Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const faqItems = document.querySelectorAll('.faq-item');
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            
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
