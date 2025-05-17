<?php
require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'includes/functions.php';

// Process subscription form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = clean_input($_POST['email']);
    
    // Validate email
    if (empty($email)) {
        set_message(ERROR_MESSAGE, 'Email is required');
        redirect_to($_SERVER['HTTP_REFERER'] ?? 'index.php');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_message(ERROR_MESSAGE, 'Please enter a valid email');
        redirect_to($_SERVER['HTTP_REFERER'] ?? 'index.php');
    } else {
        // Check if email already exists in subscribers
        $check_query = "SELECT * FROM subscribers WHERE email = ?";
        
        // Create subscribers table if it doesn't exist
        $conn->query("CREATE TABLE IF NOT EXISTS subscribers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(100) UNIQUE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('s', $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            set_message(SUCCESS_MESSAGE, 'You are already subscribed to our newsletter');
        } else {
            // Insert new subscriber
            $insert_query = "INSERT INTO subscribers (email) VALUES (?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param('s', $email);
            
            if ($insert_stmt->execute()) {
                set_message(SUCCESS_MESSAGE, 'Thank you for subscribing to our newsletter!');
            } else {
                set_message(ERROR_MESSAGE, 'Failed to subscribe. Please try again');
            }
        }
        
        // Redirect back to the referring page
        redirect_to($_SERVER['HTTP_REFERER'] ?? 'index.php');
    }
} else {
    // If not a POST request, redirect to home page
    redirect_to('index.php');
}
