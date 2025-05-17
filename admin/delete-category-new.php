<?php
require_once '../config/database.php';
require_once '../config/constants.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
require_login();
require_admin();

// Check if category ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    set_message(ERROR_MESSAGE, 'معرف الفئة غير صالح');
    redirect_to('manage-categories-new.php');
}

$category_id = $_GET['id'];

// Get category name for notification
$category_query = "SELECT title FROM categories WHERE id = ?";
$category_stmt = $conn->prepare($category_query);
$category_stmt->bind_param('i', $category_id);
$category_stmt->execute();
$category_result = $category_stmt->get_result();
$category_name = $category_result->fetch_assoc()['title'];

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
    
    set_message(SUCCESS_MESSAGE, 'تم حذف الفئة "' . $category_name . '" بنجاح');
} else {
    set_message(ERROR_MESSAGE, 'فشل في حذف الفئة');
}

// Store notification in session for JavaScript to display
$_SESSION['admin_notification'] = [
    'type' => 'success',
    'message' => 'تم حذف الفئة "' . $category_name . '" بنجاح'
];

redirect_to('manage-categories-new.php');
