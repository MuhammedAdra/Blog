<?php
$page_title = "Home";
require_once 'partials/header.php';

// Get featured post
$featured_query = "SELECT p.*, c.title AS category_title, u.firstname, u.lastname, u.avatar
                  FROM posts p
                  JOIN categories c ON p.category_id = c.id
                  JOIN users u ON p.author_id = u.id
                  WHERE p.is_featured = 1 AND p.status = 'published'
                  ORDER BY p.created_at DESC
                  LIMIT 1";
$featured_result = $conn->query($featured_query);
$featured_post = $featured_result->fetch_assoc();

// Get recent posts (excluding featured)
$recent_query = "SELECT p.*, c.title AS category_title, u.firstname, u.lastname, u.avatar
                FROM posts p
                JOIN categories c ON p.category_id = c.id
                JOIN users u ON p.author_id = u.id
                WHERE p.status = 'published' ";

// Exclude featured post if exists
if ($featured_post) {
    $recent_query .= "AND p.id != {$featured_post['id']} ";
}

$recent_query .= "ORDER BY p.created_at DESC LIMIT 6";
$recent_result = $conn->query($recent_query);

// Get all categories
$categories_query = "SELECT c.*, COUNT(p.id) AS post_count
                    FROM categories c
                    LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published'
                    GROUP BY c.id
                    ORDER BY post_count DESC
                    LIMIT 6";
$categories_result = $conn->query($categories_query);
?>

<!-- Hero Section -->
<section class="hero animate-on-scroll">
    <div class="container hero__container">
        <div class="hero__content">
            <h1>استكشف العالم من خلال الكلمات</h1>
            <p>اكتشف مقالات وقصص وأدلة مفيدة في مختلف المواضيع. انضم إلى مجتمعنا من القراء والكتاب المتحمسين لمشاركة المعرفة والإلهام.</p>
            <div class="hero__buttons">
                <a href="blog.php" class="btn btn-primary"><i class="uil uil-book-open"></i> قراءة المقالات</a>
                <a href="signup-new.php" class="btn btn-outline"><i class="uil uil-users-alt"></i> انضم إلى المجتمع</a>
            </div>
        </div>
        <div class="hero__image">
            <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" alt="Hero Image">
        </div>
    </div>
</section>
<!-- End of Hero Section -->

<!-- Featured Posts -->
<section class="featured animate-on-scroll">
    <div class="container featured__container">
        <h2 class="section__title">المقالات المميزة</h2>

        <div class="featured__posts">
            <?php if($featured_post): ?>
            <!-- Featured Post Grid -->
            <div class="featured__grid">
                <div class="post__thumbnail">
                    <img src="<?php echo ROOT_URL . 'images/posts/' . $featured_post['thumbnail']; ?>" alt="<?php echo $featured_post['title']; ?>" loading="lazy">
                </div>
                <div class="post__content">
                    <div class="post__info">
                        <a href="category-posts.php?id=<?php echo $featured_post['category_id']; ?>" class="category__button">
                            <i class="uil uil-tag-alt"></i> <?php echo $featured_post['category_title']; ?>
                        </a>
                        <div class="post__date">
                            <i class="uil uil-calendar-alt"></i>
                            <span><?php echo format_date($featured_post['created_at']); ?></span>
                        </div>
                    </div>
                    <h3 class="post__title">
                        <a href="post.php?id=<?php echo $featured_post['id']; ?>"><?php echo $featured_post['title']; ?></a>
                    </h3>
                    <p class="post__body">
                        <?php echo $featured_post['excerpt'] ? $featured_post['excerpt'] : get_excerpt($featured_post['body']); ?>
                    </p>
                    <div class="post__author">
                        <div class="post__author-avatar">
                            <img src="<?php echo ROOT_URL . 'images/users/' . $featured_post['avatar']; ?>" alt="Author Avatar" loading="lazy">
                        </div>
                        <div class="post__author-info">
                            <h5>بواسطة: <?php echo $featured_post['firstname'] . ' ' . $featured_post['lastname']; ?></h5>
                            <small><?php echo $featured_post['category_title']; ?> متخصص</small>
                        </div>
                    </div>
                    <a href="post.php?id=<?php echo $featured_post['id']; ?>" class="btn btn-primary btn-sm">
                        قراءة المقال كاملاً <i class="uil uil-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- More Featured Posts -->
            <div class="more-featured">
                <?php
                // Get 3 more featured posts
                $more_featured_query = "SELECT p.*, c.title AS category_title, u.firstname, u.lastname, u.avatar
                                      FROM posts p
                                      JOIN categories c ON p.category_id = c.id
                                      JOIN users u ON p.author_id = u.id
                                      WHERE p.is_featured = 1 AND p.status = 'published' AND p.id != ?
                                      ORDER BY p.created_at DESC LIMIT 3";
                $more_featured_stmt = $conn->prepare($more_featured_query);
                $more_featured_stmt->bind_param('i', $featured_post['id']);
                $more_featured_stmt->execute();
                $more_featured_result = $more_featured_stmt->get_result();

                if($more_featured_result->num_rows > 0):
                    $delay = 1;
                    while($post = $more_featured_result->fetch_assoc()):
                ?>
                <article class="featured-card animate-on-scroll" style="--delay: <?php echo $delay++; ?>;">
                    <div class="featured-card__thumbnail">
                        <img src="<?php echo ROOT_URL . 'images/posts/' . $post['thumbnail']; ?>" alt="<?php echo $post['title']; ?>" loading="lazy">
                    </div>
                    <div class="featured-card__content">
                        <a href="category-posts.php?id=<?php echo $post['category_id']; ?>" class="category__button">
                            <i class="uil uil-tag-alt"></i> <?php echo $post['category_title']; ?>
                        </a>
                        <h4><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo $post['title']; ?></a></h4>
                        <div class="featured-card__author">
                            <div class="post__author-avatar">
                                <img src="<?php echo ROOT_URL . 'images/users/' . $post['avatar']; ?>" alt="Author Avatar" loading="lazy">
                            </div>
                            <div class="post__author-info">
                                <h5><?php echo $post['firstname'] . ' ' . $post['lastname']; ?></h5>
                                <small><?php echo format_date($post['created_at']); ?></small>
                            </div>
                        </div>
                    </div>
                </article>
                <?php
                    endwhile;
                endif;
                ?>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <p>لا توجد مقالات مميزة متاحة حالياً.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<!-- End of Featured Posts -->

<!-- Recent Posts -->
<section class="posts animate-on-scroll">
    <div class="container posts__container">
        <h2 class="section__title">أحدث المقالات</h2>

        <div class="posts__grid">
            <?php
            $delay = 1;
            while($post = $recent_result->fetch_assoc()):
            ?>
            <!-- Post -->
            <article class="post animate-on-scroll" style="--delay: <?php echo $delay++; ?>;">
                <div class="post__thumbnail">
                    <img src="<?php echo ROOT_URL . 'images/posts/' . $post['thumbnail']; ?>" alt="<?php echo $post['title']; ?>" loading="lazy">
                </div>
                <div class="post__content">
                    <div class="post__info">
                        <a href="category-posts.php?id=<?php echo $post['category_id']; ?>" class="category__button">
                            <i class="uil uil-tag-alt"></i> <?php echo $post['category_title']; ?>
                        </a>
                        <div class="post__date">
                            <i class="uil uil-calendar-alt"></i>
                            <span><?php echo format_date($post['created_at']); ?></span>
                        </div>
                    </div>
                    <h3 class="post__title"><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo $post['title']; ?></a></h3>
                    <p class="post__body"><?php echo $post['excerpt'] ? $post['excerpt'] : get_excerpt($post['body']); ?></p>
                    <div class="post__author">
                        <div class="post__author-avatar">
                            <img src="<?php echo ROOT_URL . 'images/users/' . $post['avatar']; ?>" alt="Author Avatar" loading="lazy">
                        </div>
                        <div class="post__author-info">
                            <h5>بواسطة: <?php echo $post['firstname'] . ' ' . $post['lastname']; ?></h5>
                            <small><?php echo $post['category_title']; ?></small>
                        </div>
                    </div>
                </div>
            </article>
            <?php endwhile; ?>

            <?php if($recent_result->num_rows === 0): ?>
            <div class="alert alert-info">
                <p>لا توجد مقالات متاحة حالياً.</p>
            </div>
            <?php endif; ?>
        </div>

        <div class="view-more text-center mt-5">
            <a href="blog.php" class="btn btn-primary">عرض جميع المقالات <i class="uil uil-arrow-right"></i></a>
        </div>
    </div>
</section>
<!-- End of Recent Posts -->

<!-- Categories Section -->
<section class="categories animate-on-scroll">
    <div class="container categories__container">
        <div class="categories__header">
            <h2 class="section__title">وصفات شهية ونصائح للطبخ</h2>
            <p class="categories__description">استكشف مجموعة متنوعة من المواضيع والمقالات المصنفة حسب اهتماماتك</p>
        </div>

        <div class="categories__slider">
            <?php
            $cat_delay = 1;
            while($category = $categories_result->fetch_assoc()):
            ?>
            <!-- Category -->
            <a href="category-posts.php?id=<?php echo $category['id']; ?>" class="category__card animate-on-scroll" style="--delay: <?php echo $cat_delay++; ?>;">
                <div class="category__icon">
                    <?php
                    // Assign different icons based on category name
                    $icon = 'uil-folder';
                    $title = strtolower($category['title']);

                    if (strpos($title, 'travel') !== false) {
                        $icon = 'uil-plane';
                    } elseif (strpos($title, 'food') !== false) {
                        $icon = 'uil-restaurant';
                    } elseif (strpos($title, 'tech') !== false) {
                        $icon = 'uil-laptop';
                    } elseif (strpos($title, 'science') !== false) {
                        $icon = 'uil-atom';
                    } elseif (strpos($title, 'photo') !== false) {
                        $icon = 'uil-camera';
                    } elseif (strpos($title, 'wild') !== false) {
                        $icon = 'uil-trees';
                    }
                    ?>
                    <i class="uil <?php echo $icon; ?>"></i>
                </div>
                <h4><?php echo $category['title']; ?></h4>
                <p><?php echo $category['post_count']; ?> مقال</p>
            </a>
            <?php endwhile; ?>

            <?php if($categories_result->num_rows === 0): ?>
            <div class="alert alert-info">
                <p>لا توجد فئات متاحة حالياً.</p>
            </div>
            <?php endif; ?>
        </div>

        <div class="categories__nav">
            <button class="categories__nav-btn prev"><i class="uil uil-angle-left-b"></i></button>
            <button class="categories__nav-btn next"><i class="uil uil-angle-right-b"></i></button>
        </div>
    </div>
</section>
<!-- End of Categories Section -->

<!-- Newsletter Section -->
<section class="newsletter animate-on-scroll">
    <div class="container newsletter__container">
        <div class="newsletter__content">
            <h2>اشترك في نشرتنا الإخبارية</h2>
            <p>ابق على اطلاع بأحدث مقالاتنا والأخبار والمحتوى الحصري الذي يتم تسليمه مباشرة إلى بريدك الإلكتروني.</p>
            <form class="newsletter__form" action="subscribe.php" method="post">
                <input type="email" name="email" placeholder="أدخل عنوان بريدك الإلكتروني" required>
                <button type="submit" class="btn btn-primary">اشترك الآن <i class="uil uil-envelope"></i></button>
            </form>
            <small>بالاشتراك، فإنك توافق على <a href="#">سياسة الخصوصية</a> الخاصة بنا</small>
        </div>
        <div class="newsletter__image">
            <img src="https://images.unsplash.com/photo-1499951360447-b19be8fe80f5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" alt="Newsletter Image" loading="lazy">
        </div>
    </div>
</section>
<!-- End of Newsletter Section -->

<?php require_once 'partials/footer.php'; ?>
