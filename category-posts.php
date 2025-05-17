<?php
require_once 'partials/header.php';

// Check if category ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    set_message(ERROR_MESSAGE, 'Invalid category ID');
    redirect_to('blog.php');
}

$category_id = $_GET['id'];

// Get category details
$category_query = "SELECT * FROM categories WHERE id = ?";
$category_stmt = $conn->prepare($category_query);
$category_stmt->bind_param('i', $category_id);
$category_stmt->execute();
$category_result = $category_stmt->get_result();

// Check if category exists
if ($category_result->num_rows === 0) {
    set_message(ERROR_MESSAGE, 'Category not found');
    redirect_to('blog.php');
}

$category = $category_result->fetch_assoc();
$page_title = $category['title'] . " - Category";

// Pagination
$posts_per_page = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $posts_per_page;

// Get total posts count for pagination
$count_query = "SELECT COUNT(*) as total FROM posts WHERE category_id = ? AND status = 'published'";
$count_stmt = $conn->prepare($count_query);
$count_stmt->bind_param('i', $category_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_posts = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $posts_per_page);

// Get posts for this category
$posts_query = "SELECT p.*, u.firstname, u.lastname, u.avatar
               FROM posts p
               JOIN users u ON p.author_id = u.id
               WHERE p.category_id = ? AND p.status = 'published'
               ORDER BY p.created_at DESC
               LIMIT ?, ?";
$posts_stmt = $conn->prepare($posts_query);
$posts_stmt->bind_param('iii', $category_id, $offset, $posts_per_page);
$posts_stmt->execute();
$posts_result = $posts_stmt->get_result();

// Get all categories for filter
$categories_query = "SELECT c.*, COUNT(p.id) AS post_count
                    FROM categories c
                    LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published'
                    GROUP BY c.id
                    ORDER BY c.title";
$categories_result = $conn->query($categories_query);
?>

<!-- Category Header -->
<header class="category-header animate-on-scroll">
    <div class="container category-header__container">
        <h1><?php echo $category['title']; ?></h1>
        <p><?php echo $category['description']; ?></p>
    </div>
</header>
<!-- End of Category Header -->

<!-- Categories Buttons -->
<section class="category__buttons animate-on-scroll">
    <div class="container category__buttons-container">
        <a href="blog.php" class="category__button"><i class="uil uil-apps"></i> الكل</a>
        <?php
        $categories_result->data_seek(0); // Reset result pointer
        $cat_delay = 1;
        while($cat = $categories_result->fetch_assoc()):
        ?>
        <a href="category-posts.php?id=<?php echo $cat['id']; ?>" class="category__button <?php echo $cat['id'] == $category_id ? 'active' : ''; ?>" style="--delay: <?php echo $cat_delay++; ?>">
            <?php
            // Assign different icons based on category name
            $icon = 'uil-folder';
            $title = strtolower($cat['title']);

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
            <i class="uil <?php echo $icon; ?>"></i> <?php echo $cat['title']; ?> (<?php echo $cat['post_count']; ?>)
        </a>
        <?php endwhile; ?>
    </div>
</section>
<!-- End of Categories Buttons -->

<!-- Posts -->
<section class="posts animate-on-scroll">
    <div class="container posts__container">
        <h2 class="section__title">مقالات <?php echo $category['title']; ?></h2>

        <?php if($posts_result->num_rows > 0): ?>
        <div class="posts__grid">
            <?php
            $delay = 1;
            while($post = $posts_result->fetch_assoc()):
            ?>
            <!-- Post -->
            <article class="post animate-on-scroll" style="--delay: <?php echo $delay++; ?>;">
                <div class="post__thumbnail">
                    <img src="<?php echo ROOT_URL . 'images/posts/' . $post['thumbnail']; ?>" alt="<?php echo $post['title']; ?>" loading="lazy">
                </div>
                <div class="post__content">
                    <div class="post__info">
                        <a href="category-posts.php?id=<?php echo $category_id; ?>" class="category__button">
                            <i class="uil uil-tag-alt"></i> <?php echo $category['title']; ?>
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
                            <img src="<?php echo ROOT_URL . 'images/users/' . $post['avatar']; ?>" alt="صورة الكاتب" loading="lazy">
                        </div>
                        <div class="post__author-info">
                            <h5>بواسطة: <?php echo $post['firstname'] . ' ' . $post['lastname']; ?></h5>
                            <small><?php echo $category['title']; ?></small>
                        </div>
                    </div>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="no-posts">
            <p>لا توجد مقالات متاحة في هذه الفئة حالياً.</p>
        </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <div class="pagination">
            <?php if($page > 1): ?>
            <a href="?id=<?php echo $category_id; ?>&page=<?php echo $page - 1; ?>" class="page-link"><i class="uil uil-angle-left"></i> السابق</a>
            <?php endif; ?>

            <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?id=<?php echo $category_id; ?>&page=<?php echo $i; ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if($page < $total_pages): ?>
            <a href="?id=<?php echo $category_id; ?>&page=<?php echo $page + 1; ?>" class="page-link">التالي <i class="uil uil-angle-right"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<!-- End of Posts -->

<?php require_once 'partials/footer.php'; ?>
