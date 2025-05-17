<?php
require_once '../config/database.php';
require_once '../config/constants.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
require_login();
require_admin();

// Check if category ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    set_message(ERROR_MESSAGE, 'Invalid category ID');
    redirect_to('manage-categories.php');
}

$category_id = $_GET['id'];

// Delete category
$delete_query = "DELETE FROM categories WHERE id = ?";
$delete_stmt = $conn->prepare($delete_query);
$delete_stmt->bind_param('i', $category_id);

if ($delete_stmt->execute()) {
    // Update posts to uncategorized
    $update_query = "UPDATE posts SET category_id = NULL WHERE category_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('i', $category_id);
    $update_stmt->execute();
    
    set_message(SUCCESS_MESSAGE, 'Category deleted successfully');
} else {
    set_message(ERROR_MESSAGE, 'Failed to delete category');
}

redirect_to('manage-categories.php');
