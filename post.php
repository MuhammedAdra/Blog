<?php
require_once 'partials/header.php';

// Check if post ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    set_message(ERROR_MESSAGE, 'معرف المقال غير صالح');
    redirect_to('blog.php');
}

$post_id = $_GET['id'];

// Get post details
$post_query = "SELECT p.*, c.title AS category_title, u.firstname, u.lastname, u.avatar, u.username
              FROM posts p
              JOIN categories c ON p.category_id = c.id
              JOIN users u ON p.author_id = u.id
              WHERE p.id = ? AND p.status = 'published'";
$post_stmt = $conn->prepare($post_query);
$post_stmt->bind_param('i', $post_id);
$post_stmt->execute();
$post_result = $post_stmt->get_result();

// Check if post exists
if ($post_result->num_rows === 0) {
    set_message(ERROR_MESSAGE, 'لم يتم العثور على المقال');
    redirect_to('blog.php');
}

$post = $post_result->fetch_assoc();
$page_title = $post['title'];

// Get post comments
$comments_query = "SELECT c.*, u.firstname, u.lastname, u.avatar
                  FROM comments c
                  LEFT JOIN users u ON c.user_id = u.id
                  WHERE c.post_id = ? AND c.status = 'approved'
                  ORDER BY c.created_at DESC";
$comments_stmt = $conn->prepare($comments_query);
$comments_stmt->bind_param('i', $post_id);
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();

// Get related posts (same category, excluding current post)
$related_query = "SELECT p.*, u.firstname, u.lastname
                 FROM posts p
                 JOIN users u ON p.author_id = u.id
                 WHERE p.category_id = ? AND p.id != ? AND p.status = 'published'
                 ORDER BY p.created_at DESC
                 LIMIT 3";
$related_stmt = $conn->prepare($related_query);
$related_stmt->bind_param('ii', $post['category_id'], $post_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();

// Process comment form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    // Check if user is logged in
    if (!is_logged_in()) {
        set_message(ERROR_MESSAGE, 'You must be logged in to comment');
        redirect_to('signin.php');
    }

    $comment_text = clean_input($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    if (empty($comment_text)) {
        set_message(ERROR_MESSAGE, 'لا يمكن أن يكون التعليق فارغًا');
    } else {
        // Insert comment
        $insert_query = "INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param('iis', $post_id, $user_id, $comment_text);

        if ($insert_stmt->execute()) {
            set_message(SUCCESS_MESSAGE, 'تم إرسال التعليق بنجاح وهو بانتظار الموافقة');
            redirect_to("post.php?id=$post_id");
        } else {
            set_message(ERROR_MESSAGE, 'فشل في إرسال التعليق');
        }
    }
}
?>

<!-- Single Post -->
<section class="single-post">
    <div class="container single-post__container">
        <h1><?php echo $post['title']; ?></h1>

        <div class="post-meta">
            <div class="post__author">
                <div class="post__author-avatar">
                    <img src="<?php echo ROOT_URL . 'images/users/' . $post['avatar']; ?>" alt="Author Avatar">
                </div>
                <div class="post__author-info">
                    <h5>بواسطة: <?php echo $post['firstname'] . ' ' . $post['lastname']; ?></h5>
                    <small><?php echo format_date($post['created_at']); ?></small>
                </div>
            </div>
            <div class="post__category">
                <a href="category-posts.php?id=<?php echo $post['category_id']; ?>" class="category__button">
                    <?php echo $post['category_title']; ?>
                </a>
            </div>
        </div>

        <div class="single-post__thumbnail">
            <img src="<?php echo ROOT_URL . 'images/posts/' . $post['thumbnail']; ?>" alt="<?php echo $post['title']; ?>">
        </div>

        <div class="single-post__content">
            <?php echo $post['body']; ?>
        </div>

        <?php if(!empty($post['tags'])): ?>
        <div class="post-tags">
            <h4>Tags:</h4>
            <div class="tags">
                <?php
                $tags = explode(',', $post['tags']);
                foreach($tags as $tag):
                    $tag = trim($tag);
                    if(!empty($tag)):
                ?>
                <a href="search.php?tag=<?php echo urlencode($tag); ?>" class="tag"><?php echo $tag; ?></a>
                <?php
                    endif;
                endforeach;
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<!-- End of Single Post -->

<!-- Comments Section -->
<section class="comments">
    <div class="container comments__container">
        <h3><?php echo $comments_result->num_rows; ?> تعليق</h3>

        <!-- Comment Form -->
        <?php if(is_logged_in()): ?>
        <div class="comment-form">
            <h4>اترك تعليقاً</h4>
            <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST">
                <textarea name="comment" rows="5" placeholder="اكتب تعليقك هنا..." required></textarea>
                <button type="submit" class="btn btn-primary"><i class="uil uil-comment-dots"></i> نشر التعليق</button>
            </form>
            <small>تتم مراجعة التعليقات وستظهر بعد الموافقة عليها</small>
        </div>
        <?php else: ?>
        <div class="comment-login-prompt">
            <p><a href="signin.php">قم بتسجيل الدخول</a> لتتمكن من ترك تعليق</p>
        </div>
        <?php endif; ?>

        <!-- Comments List -->
        <div class="comments-list">
            <?php if($comments_result->num_rows > 0): ?>
                <?php while($comment = $comments_result->fetch_assoc()): ?>
                <div class="comment">
                    <div class="comment__author">
                        <div class="comment__author-avatar">
                            <img src="<?php echo ROOT_URL . 'images/users/' . $comment['avatar']; ?>" alt="Commenter Avatar">
                        </div>
                        <div class="comment__author-info">
                            <h5><?php echo $comment['firstname'] . ' ' . $comment['lastname']; ?></h5>
                            <small><?php echo format_date($comment['created_at']); ?></small>
                        </div>
                    </div>
                    <div class="comment__content">
                        <p><?php echo $comment['comment']; ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>لا توجد تعليقات حتى الآن. كن أول من يعلق!</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<!-- End of Comments Section -->

<!-- Related Posts -->
<?php if($related_result->num_rows > 0): ?>
<section class="related-posts">
    <div class="container related-posts__container">
        <h3>مقالات ذات صلة</h3>

        <div class="related-posts__grid">
            <?php while($related = $related_result->fetch_assoc()): ?>
            <article class="post">
                <div class="post__thumbnail">
                    <img src="<?php echo ROOT_URL . 'images/posts/' . $related['thumbnail']; ?>" alt="<?php echo $related['title']; ?>">
                </div>
                <div class="post__content">
                    <h4 class="post__title">
                        <a href="post.php?id=<?php echo $related['id']; ?>"><?php echo $related['title']; ?></a>
                    </h4>
                    <div class="post__author">
                        <small>By: <?php echo $related['firstname'] . ' ' . $related['lastname']; ?></small>
                    </div>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<!-- End of Related Posts -->

<?php require_once 'partials/footer.php'; ?>
