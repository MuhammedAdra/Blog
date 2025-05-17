<?php
require_once '../config/database.php';
require_once '../config/constants.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
require_login();
require_admin();

// Check if post ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    set_message(ERROR_MESSAGE, 'Invalid post ID');
    redirect_to('manage-posts.php');
}

$post_id = $_GET['id'];

// Get post details to delete the thumbnail
$post_query = "SELECT thumbnail FROM posts WHERE id = ?";
$post_stmt = $conn->prepare($post_query);
$post_stmt->bind_param('i', $post_id);
$post_stmt->execute();
$post_result = $post_stmt->get_result();

if ($post_result->num_rows === 0) {
    set_message(ERROR_MESSAGE, 'Post not found');
    redirect_to('manage-posts.php');
}

$post = $post_result->fetch_assoc();
$thumbnail_path = '../images/posts/' . $post['thumbnail'];

// Delete post
$delete_query = "DELETE FROM posts WHERE id = ?";
$delete_stmt = $conn->prepare($delete_query);
$delete_stmt->bind_param('i', $post_id);

if ($delete_stmt->execute()) {
    // Delete thumbnail file if it exists
    if (file_exists($thumbnail_path) && $post['thumbnail'] !== 'default-post.jpg') {
        unlink($thumbnail_path);
    }
    
    set_message(SUCCESS_MESSAGE, 'Post deleted successfully');
} else {
    set_message(ERROR_MESSAGE, 'Failed to delete post');
}

redirect_to('manage-posts.php');
