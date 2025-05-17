<?php
$page_title = "Sign Up";
require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'includes/functions.php';

// Check if user is already logged in
if (is_logged_in()) {
    redirect_to('index.php');
}

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $firstname = clean_input($_POST['firstname']);
    $lastname = clean_input($_POST['lastname']);
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmPassword'];
    $avatar = $_FILES['avatar'];
    
    // Validate input
    if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        set_message(ERROR_MESSAGE, 'All fields are required');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_message(ERROR_MESSAGE, 'Please enter a valid email');
    } elseif (strlen($password) < 6) {
        set_message(ERROR_MESSAGE, 'Password should be at least 6 characters');
    } elseif ($password !== $confirm_password) {
        set_message(ERROR_MESSAGE, 'Passwords do not match');
    } else {
        // Check if username or email already exists
        $check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('ss', $username, $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            set_message(ERROR_MESSAGE, 'Username or email already exists');
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Upload avatar if provided
            $avatar_name = DEFAULT_AVATAR;
            if ($avatar['name']) {
                $avatar_result = upload_image($avatar, 'images/users/');
                if ($avatar_result) {
                    $avatar_name = $avatar_result;
                }
            }
            
            // Insert new user
            $insert_query = "INSERT INTO users (firstname, lastname, username, email, password, avatar) VALUES (?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param('ssssss', $firstname, $lastname, $username, $email, $hashed_password, $avatar_name);
            
            if ($insert_stmt->execute()) {
                set_message(SUCCESS_MESSAGE, 'Registration successful. You can now log in');
                redirect_to('signin.php');
            } else {
                set_message(ERROR_MESSAGE, 'Registration failed. Please try again');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Modern Blog</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- IconScout Unicons -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <section class="form__section">
        <div class="container form__section-container">
            <h2>Sign Up</h2>
            
            <?php display_message(); ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                <div class="form__control">
                    <label for="firstname">First Name</label>
                    <input type="text" id="firstname" name="firstname" placeholder="Enter your first name" required>
                </div>
                <div class="form__control">
                    <label for="lastname">Last Name</label>
                    <input type="text" id="lastname" name="lastname" placeholder="Enter your last name" required>
                </div>
                <div class="form__control">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a username" required>
                </div>
                <div class="form__control">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form__control">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a password" required>
                </div>
                <div class="form__control">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                </div>
                <div class="form__control">
                    <label for="avatar">Profile Picture (Optional)</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary">Sign Up</button>
                <small>Already have an account? <a href="signin.php">Sign In</a></small>
            </form>
        </div>
    </section>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="main.js"></script>
</body>
</html>
