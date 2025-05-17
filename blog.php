<?php
$page_title = "Blog";
require_once 'partials/header.php';

// Pagination
$posts_per_page = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $posts_per_page;

// Search functionality
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$search_condition = '';
if (!empty($search)) {
    $search_condition = "AND (p.title LIKE '%$search%' OR p.body LIKE '%$search%' OR p.tags LIKE '%$search%')";
}

// Get total posts count for pagination
$count_query = "SELECT COUNT(*) as total FROM posts p WHERE p.status = 'published' $search_condition";
$count_result = $conn->query($count_query);
$total_posts = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $posts_per_page);

// Get posts
$posts_query = "SELECT p.*, c.title AS category_title, u.firstname, u.lastname, u.avatar
               FROM posts p
               JOIN categories c ON p.category_id = c.id
               JOIN users u ON p.author_id = u.id
               WHERE p.status = 'published' $search_condition
               ORDER BY p.created_at DESC
               LIMIT $offset, $posts_per_page";
$posts_result = $conn->query($posts_query);

// Get all categories for filter
$categories_query = "SELECT c.*, COUNT(p.id) AS post_count
                    FROM categories c
                    LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published'
                    GROUP BY c.id
                    ORDER BY c.title";
$categories_result = $conn->query($categories_query);
?>

<!-- Blog Header -->
<header class="blog-header">
    <div class="container blog-header__container">
        <h1>المدونة</h1>
        <p>اكتشف أحدث المقالات والرؤى والقصص من مجتمع الكتاب لدينا</p>
    </div>
</header>
<!-- End of Blog Header -->

<!-- Blog Filter Section -->
<section class="blog-filter">
    <div class="container blog-filter__container">
        <!-- Search Bar -->
        <div class="blog-filter__search">
            <h3><i class="uil uil-search"></i> ابحث عن مقالات</h3>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="GET" class="search__form">
                <div class="search__input-group">
                    <input type="text" name="search" placeholder="اكتب كلمات البحث هنا..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary"><i class="uil uil-search"></i> بحث</button>
                </div>
                <div class="search__popular">
                    <span>عمليات بحث شائعة:</span>
                    <a href="?search=وصفات">وصفات</a>
                    <a href="?search=نصائح">نصائح</a>
                    <a href="?search=تقنية">تقنية</a>
                </div>
            </form>
        </div>

        <!-- Categories Filter -->
        <div class="blog-filter__categories">
            <h3><i class="uil uil-apps"></i> تصفح حسب الفئة</h3>
            <div class="categories__buttons">
                <a href="blog.php" class="category__button <?php echo empty($_GET['category']) ? 'active' : ''; ?>">الكل</a>
                <?php
                $categories_result->data_seek(0); // Reset result pointer
                while($category = $categories_result->fetch_assoc()):
                ?>
                <a href="category-posts.php?id=<?php echo $category['id']; ?>" class="category__button">
                    <?php echo $category['title']; ?> <span class="count"><?php echo $category['post_count']; ?></span>
                </a>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</section>
<!-- End of Blog Filter Section -->

<!-- Posts -->
<section class="posts">
    <div class="container posts__container">
        <?php if(!empty($search)): ?>
        <h2 class="section__title">Search Results for "<?php echo htmlspecialchars($search); ?>"</h2>
        <?php endif; ?>

        <?php if($posts_result->num_rows > 0): ?>
        <div class="posts__grid">
            <?php
            $delay = 1;
            while($post = $posts_result->fetch_assoc()):
            ?>
            <!-- Post -->
            <article class="post" style="--delay: <?php echo $delay++; ?>;">
                <div class="post__thumbnail">
                    <img src="<?php echo ROOT_URL . 'images/posts/' . $post['thumbnail']; ?>" alt="<?php echo $post['title']; ?>">
                </div>
                <div class="post__content">
                    <div class="post__info">
                        <a href="category-posts.php?id=<?php echo $post['category_id']; ?>" class="category__button"><?php echo $post['category_title']; ?></a>
                        <div class="post__date">
                            <i class="uil uil-calendar-alt"></i>
                            <span><?php echo format_date($post['created_at']); ?></span>
                        </div>
                    </div>
                    <h3 class="post__title"><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo $post['title']; ?></a></h3>
                    <p class="post__body"><?php echo $post['excerpt'] ? $post['excerpt'] : get_excerpt($post['body']); ?></p>
                    <div class="post__author">
                        <div class="post__author-avatar">
                            <img src="<?php echo ROOT_URL . 'images/users/' . $post['avatar']; ?>" alt="Author Avatar">
                        </div>
                        <div class="post__author-info">
                            <h5>By: <?php echo $post['firstname'] . ' ' . $post['lastname']; ?></h5>
                            <small><?php echo $post['category_title']; ?></small>
                        </div>
                    </div>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="no-posts">
            <?php if(!empty($search)): ?>
            <p>No posts found matching "<?php echo htmlspecialchars($search); ?>".</p>
            <?php else: ?>
            <p>No posts available at the moment.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <div class="pagination">
            <?php if($page > 1): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="page-link"><i class="uil uil-angle-left"></i> Previous</a>
            <?php endif; ?>

            <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if($page < $total_pages): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="page-link">Next <i class="uil uil-angle-right"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<!-- End of Posts -->

<?php require_once 'partials/footer.php'; ?>
