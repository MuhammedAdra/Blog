<?php
// Clean and sanitize input data
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Redirect to a specific page
function redirect_to($page) {
    header("Location: " . ROOT_URL . $page);
    exit();
}

// Set flash message
function set_message($type, $message) {
    $_SESSION[$type] = $message;
}

// Display flash message and clear it
function display_message() {
    if (isset($_SESSION[ERROR_MESSAGE])) {
        echo '<div class="alert__message error">';
        echo '<p>' . $_SESSION[ERROR_MESSAGE] . '</p>';
        echo '</div>';
        unset($_SESSION[ERROR_MESSAGE]);
    }

    if (isset($_SESSION[SUCCESS_MESSAGE])) {
        echo '<div class="alert__message success">';
        echo '<p>' . $_SESSION[SUCCESS_MESSAGE] . '</p>';
        echo '</div>';
        unset($_SESSION[SUCCESS_MESSAGE]);
    }
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function is_admin() {
    return isset($_SESSION['user_is_admin']) && $_SESSION['user_is_admin'] == 1;
}

// Require login to access a page
function require_login() {
    if (!is_logged_in()) {
        set_message(ERROR_MESSAGE, 'You must be logged in to access this page');
        redirect_to('signin.php');
    }
}

// Require admin privileges to access a page
function require_admin() {
    if (!is_admin()) {
        set_message(ERROR_MESSAGE, 'You do not have permission to access this page');
        redirect_to('index.php');
    }
}

// Format date
function format_date($date) {
    return date('F j, Y', strtotime($date));
}

// Get excerpt from text
function get_excerpt($text, $limit = 150) {
    if (strlen($text) > $limit) {
        $text = substr($text, 0, $limit);
        $text = substr($text, 0, strrpos($text, ' '));
        $text .= '...';
    }
    return $text;
}

// Count posts by category
function count_posts_by_category($conn, $category_id) {
    $query = "SELECT COUNT(*) as count FROM posts WHERE category_id = ? AND status = 'published'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Upload image
function upload_image($file, $destination_folder) {
    // Debug information
    error_log('Upload image function called');
    error_log('File: ' . print_r($file, true));
    error_log('Destination folder: ' . $destination_folder);

    // Create directory if it doesn't exist
    if (!file_exists($destination_folder)) {
        error_log('Creating directory: ' . $destination_folder);
        if (!mkdir($destination_folder, 0777, true)) {
            error_log('Failed to create directory: ' . $destination_folder);
            return false;
        }
    }

    // Get file info
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];

    // Check if file was uploaded
    if (empty($file_name) || empty($file_tmp)) {
        error_log('No file uploaded or temporary file missing');
        return false;
    }

    // Get file extension
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Allowed extensions
    $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');

    // Check if extension is allowed
    if (in_array($file_ext, $allowed_ext)) {
        // Check for errors
        if ($file_error === 0) {
            // Check file size (5MB max)
            if ($file_size <= 5242880) {
                // Create unique file name
                $new_file_name = uniqid('', true) . '.' . $file_ext;
                $file_destination = $destination_folder . $new_file_name;

                error_log('Moving file to: ' . $file_destination);

                // Move file to destination
                if (move_uploaded_file($file_tmp, $file_destination)) {
                    error_log('File uploaded successfully: ' . $new_file_name);
                    return $new_file_name;
                } else {
                    error_log('Failed to move uploaded file: ' . error_get_last()['message']);
                    return false;
                }
            } else {
                error_log('File too large: ' . $file_size . ' bytes');
                return false;
            }
        } else {
            error_log('File upload error: ' . $file_error);
            return false;
        }
    } else {
        error_log('Invalid file extension: ' . $file_ext);
        return false;
    }
}
