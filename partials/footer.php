    <!-- Footer -->
    <footer>
        <div class="container">
            <!-- Social Icons -->
            <div class="footer__socials">
                <a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                    <i class="uil uil-facebook-f"></i>
                </a>
                <a href="https://www.twitter.com" target="_blank" rel="noopener noreferrer" aria-label="Twitter">
                    <i class="uil uil-twitter"></i>
                </a>
                <a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                    <i class="uil uil-instagram"></i>
                </a>
                <a href="https://www.github.com" target="_blank" rel="noopener noreferrer" aria-label="GitHub">
                    <i class="uil uil-github"></i>
                </a>
                <a href="https://www.linkedin.com" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                    <i class="uil uil-linkedin"></i>
                </a>
            </div>

            <!-- Footer Links -->
            <div class="footer__container">
                <article>
                    <h4>من نحن</h4>
                    <p>مدونتي هي منصة مخصصة لمشاركة المعرفة والإلهام والقصص عبر مواضيع متنوعة. مهمتنا هي إعلام وترفيه وربط الناس من خلال محتوى عالي الجودة.</p>
                </article>

                <article>
                    <h4>الفئات</h4>
                    <ul>
                        <?php
                        // Get categories for footer
                        $query = "SELECT id, title FROM categories LIMIT 5";
                        $categories = $conn->query($query);
                        while($category = $categories->fetch_assoc()):
                        ?>
                        <li><a href="<?php echo ROOT_URL . 'category-posts.php?id=' . $category['id']; ?>"><?php echo $category['title']; ?></a></li>
                        <?php endwhile; ?>
                    </ul>
                </article>

                <article>
                    <h4>روابط سريعة</h4>
                    <ul>
                        <li><a href="<?php echo ROOT_URL; ?>index.php">الرئيسية</a></li>
                        <li><a href="<?php echo ROOT_URL; ?>blog.php">المدونة</a></li>
                        <li><a href="<?php echo ROOT_URL; ?>about.php">من نحن</a></li>
                        <li><a href="<?php echo ROOT_URL; ?>services.php">خدماتنا</a></li>
                        <li><a href="<?php echo ROOT_URL; ?>contact-new.php">اتصل بنا</a></li>
                        <li><a href="<?php echo ROOT_URL; ?>signin-new.php">تسجيل الدخول</a></li>
                        <li><a href="<?php echo ROOT_URL; ?>signup-new.php">إنشاء حساب</a></li>
                    </ul>
                </article>

                <article>
                    <h4>النشرة الإخبارية</h4>
                    <p>اشترك في نشرتنا الإخبارية للحصول على أحدث التحديثات.</p>
                    <form class="footer__form" action="<?php echo ROOT_URL; ?>subscribe.php" method="post">
                        <input type="email" name="email" placeholder="بريدك الإلكتروني" required>
                        <button type="submit" class="btn btn-primary">اشتراك</button>
                    </form>
                </article>
            </div>

            <!-- Copyright -->
            <div class="footer__copyright">
                <small>&copy; <?php echo date('Y'); ?> مدونتي. جميع الحقوق محفوظة.</small>
                <p class="footer__made-with">
                    صنع بكل <i class="uil uil-heart"></i> فريق مدونتي
                </p>
            </div>
        </div>
    </footer>
    <!-- End of Footer -->

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="<?php echo ROOT_URL; ?>main.js"></script>
    <!-- Notifications JavaScript -->
    <script src="<?php echo ROOT_URL; ?>admin/js/notifications.js"></script>
</body>
</html>
