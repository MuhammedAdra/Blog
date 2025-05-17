<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Page title
$page_title = 'من نحن';

// Include header
include_once 'partials/header.php';
?>

<!-- Hero Section -->
<section class="about-hero">
    <div class="container about-hero__container">
        <div class="about-hero__content">
            <h1>من نحن</h1>
            <p>نحن منصة رائدة في مجال المحتوى العربي، نسعى لتقديم محتوى هادف ومفيد لجميع القراء</p>
        </div>
        <div class="about-hero__image">
            <img src="images/about-hero.jpg" alt="من نحن">
        </div>
    </div>
</section>
<!-- End of Hero Section -->

<!-- Our Story Section -->
<section class="our-story">
    <div class="container our-story__container">
        <div class="our-story__content">
            <h2>قصتنا</h2>
            <p>بدأت رحلتنا في عام 2020 بهدف إثراء المحتوى العربي على الإنترنت وتقديم محتوى متميز يلبي احتياجات القارئ العربي. نؤمن بأن المعرفة حق للجميع، ونسعى لتوفير محتوى متنوع وشامل في مختلف المجالات.</p>
            <p>مع مرور الوقت، نمت منصتنا لتصبح وجهة موثوقة للقراء الباحثين عن محتوى عربي أصيل وموثوق. نفتخر بفريقنا المتميز من الكتاب والمحررين الذين يعملون بجد لتقديم أفضل المقالات والموضوعات.</p>
        </div>
        <div class="our-story__image">
            <img src="images/our-story.jpg" alt="قصتنا">
        </div>
    </div>
</section>
<!-- End of Our Story Section -->

<!-- Our Mission Section -->
<section class="our-mission">
    <div class="container our-mission__container">
        <div class="our-mission__header">
            <h2>مهمتنا ورؤيتنا</h2>
            <p>نسعى لتحقيق رؤية طموحة في عالم المحتوى العربي</p>
        </div>

        <div class="our-mission__cards">
            <div class="mission-card">
                <div class="mission-card__icon">
                    <i class="uil uil-rocket"></i>
                </div>
                <h3>مهمتنا</h3>
                <p>تقديم محتوى عربي متميز وموثوق يثري معرفة القارئ ويساهم في تطوير مهاراته وتوسيع آفاقه.</p>
            </div>

            <div class="mission-card">
                <div class="mission-card__icon">
                    <i class="uil uil-eye"></i>
                </div>
                <h3>رؤيتنا</h3>
                <p>أن نكون المنصة الرائدة في تقديم المحتوى العربي الهادف والمتميز على مستوى العالم العربي.</p>
            </div>

            <div class="mission-card">
                <div class="mission-card__icon">
                    <i class="uil uil-heart"></i>
                </div>
                <h3>قيمنا</h3>
                <p>الجودة، المصداقية، الابتكار، احترام القارئ، والمسؤولية المجتمعية.</p>
            </div>
        </div>
    </div>
</section>
<!-- End of Our Mission Section -->

<!-- Team Section -->
<section class="team">
    <div class="container team__container">
        <div class="team__header">
            <h2>فريقنا</h2>
            <p>نفتخر بفريقنا المتميز من الخبراء والمتخصصين</p>
        </div>

        <div class="team__members">
            <div class="team-member">
                <div class="team-member__image">
                    <img src="images/team/team-1.jpg" alt="عضو الفريق">
                </div>
                <div class="team-member__info">
                    <h3>أحمد محمد</h3>
                    <p>المؤسس والرئيس التنفيذي</p>
                    <div class="team-member__socials">
                        <a href="#"><i class="uil uil-facebook-f"></i></a>
                        <a href="#"><i class="uil uil-twitter"></i></a>
                        <a href="#"><i class="uil uil-linkedin"></i></a>
                    </div>
                </div>
            </div>

            <div class="team-member">
                <div class="team-member__image">
                    <img src="images/team/team-2.jpg" alt="عضو الفريق">
                </div>
                <div class="team-member__info">
                    <h3>سارة أحمد</h3>
                    <p>رئيسة التحرير</p>
                    <div class="team-member__socials">
                        <a href="#"><i class="uil uil-facebook-f"></i></a>
                        <a href="#"><i class="uil uil-twitter"></i></a>
                        <a href="#"><i class="uil uil-linkedin"></i></a>
                    </div>
                </div>
            </div>

            <div class="team-member">
                <div class="team-member__image">
                    <img src="images/team/team-3.jpg" alt="عضو الفريق">
                </div>
                <div class="team-member__info">
                    <h3>محمد علي</h3>
                    <p>مدير التسويق</p>
                    <div class="team-member__socials">
                        <a href="#"><i class="uil uil-facebook-f"></i></a>
                        <a href="#"><i class="uil uil-twitter"></i></a>
                        <a href="#"><i class="uil uil-linkedin"></i></a>
                    </div>
                </div>
            </div>

            <div class="team-member">
                <div class="team-member__image">
                    <img src="images/team/team-4.jpg" alt="عضو الفريق">
                </div>
                <div class="team-member__info">
                    <h3>نورا خالد</h3>
                    <p>مديرة تطوير الأعمال</p>
                    <div class="team-member__socials">
                        <a href="#"><i class="uil uil-facebook-f"></i></a>
                        <a href="#"><i class="uil uil-twitter"></i></a>
                        <a href="#"><i class="uil uil-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End of Team Section -->

<!-- Testimonials Section -->
<section class="testimonials">
    <div class="container testimonials__container">
        <div class="testimonials__header">
            <h2>ماذا يقول عملاؤنا</h2>
            <p>آراء بعض القراء والمتابعين لمنصتنا</p>
        </div>

        <div class="testimonials__slider">
            <div class="testimonial">
                <div class="testimonial__content">
                    <p>"منصة رائعة تقدم محتوى متميز ومفيد. أصبحت زيارة الموقع جزءاً من روتيني اليومي للاطلاع على كل جديد."</p>
                </div>
                <div class="testimonial__author">
                    <div class="testimonial__author-image">
                        <img src="images/testimonials/testimonial-1.jpg" alt="صورة العميل">
                    </div>
                    <div class="testimonial__author-info">
                        <h4>خالد محمود</h4>
                        <p>قارئ دائم</p>
                    </div>
                </div>
            </div>

            <div class="testimonial">
                <div class="testimonial__content">
                    <p>"أحب تنوع المحتوى وجودة المقالات. الموقع سهل الاستخدام والتصفح، وأجد دائماً ما أبحث عنه بسهولة."</p>
                </div>
                <div class="testimonial__author">
                    <div class="testimonial__author-image">
                        <img src="images/testimonials/testimonial-2.jpg" alt="صورة العميل">
                    </div>
                    <div class="testimonial__author-info">
                        <h4>ريم سعيد</h4>
                        <p>كاتبة ومدونة</p>
                    </div>
                </div>
            </div>

            <div class="testimonial">
                <div class="testimonial__content">
                    <p>"منصة متميزة تقدم محتوى عربي أصيل وموثوق. أنصح بها لكل من يبحث عن معلومات قيمة ومفيدة."</p>
                </div>
                <div class="testimonial__author">
                    <div class="testimonial__author-image">
                        <img src="images/testimonials/testimonial-3.jpg" alt="صورة العميل">
                    </div>
                    <div class="testimonial__author-info">
                        <h4>عمر فاروق</h4>
                        <p>باحث أكاديمي</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="testimonials__nav">
            <button class="testimonials__nav-btn prev"><i class="uil uil-angle-left-b"></i></button>
            <button class="testimonials__nav-btn next"><i class="uil uil-angle-right-b"></i></button>
        </div>
    </div>
</section>
<!-- End of Testimonials Section -->

<!-- Call to Action Section -->
<section class="cta">
    <div class="container cta__container">
        <div class="cta__content">
            <h2>انضم إلينا اليوم</h2>
            <p>سجل الآن واستمتع بتجربة فريدة من نوعها مع منصتنا</p>
            <a href="signup-new.php" class="btn btn-primary">سجل الآن</a>
        </div>
    </div>
</section>
<!-- End of Call to Action Section -->

<?php
// Include footer
include_once 'partials/footer.php';
?>

<script>
    // Testimonials Slider
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.querySelector('.testimonials__slider');
        const prevBtn = document.querySelector('.testimonials__nav-btn.prev');
        const nextBtn = document.querySelector('.testimonials__nav-btn.next');

        if (slider && prevBtn && nextBtn) {
            let slideIndex = 0;
            const slides = slider.querySelectorAll('.testimonial');
            const slideWidth = slides[0].offsetWidth;
            const slideMargin = 30; // Adjust based on your CSS

            // Set initial position
            slider.style.transform = `translateX(0)`;

            // Previous slide
            prevBtn.addEventListener('click', function() {
                if (slideIndex > 0) {
                    slideIndex--;
                    updateSliderPosition();
                }
            });

            // Next slide
            nextBtn.addEventListener('click', function() {
                if (slideIndex < slides.length - 1) {
                    slideIndex++;
                    updateSliderPosition();
                }
            });

            function updateSliderPosition() {
                const position = -(slideIndex * (slideWidth + slideMargin));
                slider.style.transform = `translateX(${position}px)`;
            }
        }
    });
</script>
