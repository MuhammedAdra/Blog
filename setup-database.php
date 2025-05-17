<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Blog - Database Setup</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .setup-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .setup-log {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            max-height: 300px;
            overflow-y: auto;
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-warning {
            background-color: #f6c23e;
            border-color: #f6c23e;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <h1>Modern Blog - Database Setup</h1>
        <div class="setup-log">
<?php
// Database connection parameters
$host = 'localhost';
$user = 'root';
$pass = '';

// Connect to MySQL without selecting a database
$conn = new mysqli($host, $user, $pass);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS blog_db";
if ($conn->query($sql) === TRUE) {
    echo "<div class='alert alert-success'>Database created successfully</div>";
} else {
    echo "<div class='alert alert-danger'>Error creating database: " . $conn->error . "</div>";
}

// Select the database
$conn->select_db('blog_db');

// Execute SQL statements directly
$success = true;

// Create Users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT 'default-avatar.jpg',
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    echo "<div class='alert alert-danger'>Error creating users table: " . $conn->error . "</div>";
    $success = false;
} else {
    echo "<div class='alert alert-success'>Users table created successfully</div>";
}

// Create Categories table
$sql = "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    echo "<div class='alert alert-danger'>Error creating categories table: " . $conn->error . "</div>";
    $success = false;
} else {
    echo "<div class='alert alert-success'>Categories table created successfully</div>";
}

// Create Posts table
$sql = "CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    thumbnail VARCHAR(255) NOT NULL,
    category_id INT,
    author_id INT,
    is_featured TINYINT(1) DEFAULT 0,
    excerpt VARCHAR(255),
    tags VARCHAR(255),
    status ENUM('published', 'draft') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql) !== TRUE) {
    echo "<div class='alert alert-danger'>Error creating posts table: " . $conn->error . "</div>";
    $success = false;
} else {
    echo "<div class='alert alert-success'>Posts table created successfully</div>";
}

// Create Comments table
$sql = "CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT,
    comment TEXT NOT NULL,
    status ENUM('pending', 'approved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
)";

if ($conn->query($sql) !== TRUE) {
    echo "<div class='alert alert-danger'>Error creating comments table: " . $conn->error . "</div>";
    $success = false;
} else {
    echo "<div class='alert alert-success'>Comments table created successfully</div>";
}

// Check if admin user already exists
$check_admin = "SELECT * FROM users WHERE username = 'admin' OR email = 'admin@example.com'";
$result = $conn->query($check_admin);

if ($result->num_rows == 0) {
    // Create a proper password hash for admin123
    $admin_password = 'admin123';
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

    // Insert default admin user
    $sql = "INSERT INTO users (firstname, lastname, username, email, password, is_admin)
    VALUES ('Admin', 'User', 'admin', 'admin@example.com', '$hashed_password', 1)";

    if ($conn->query($sql) !== TRUE) {
        echo "<div class='alert alert-danger'>Error inserting admin user: " . $conn->error . "</div>";
        $success = false;
    } else {
        echo "<div class='alert alert-success'>Admin user created successfully</div>";
    }
} else {
    echo "<div class='alert alert-info'>Admin user already exists</div>";
}

// Check if categories already exist
$check_categories = "SELECT COUNT(*) as count FROM categories";
$result = $conn->query($check_categories);
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    // Insert sample categories
    $sql = "INSERT INTO categories (title, description) VALUES
    ('Travel', 'Articles about travel destinations, tips, and experiences'),
    ('Wildlife', 'Content related to wildlife conservation and animal species'),
    ('Food', 'Recipes, cooking tips, and food culture articles'),
    ('Technology', 'Latest tech news, reviews, and digital trends'),
    ('Science', 'Scientific discoveries, research, and explanations'),
    ('Photography', 'Photography tips, techniques, and inspiring images')";

    if ($conn->query($sql) !== TRUE) {
        echo "<div class='alert alert-danger'>Error inserting categories: " . $conn->error . "</div>";
        $success = false;
    } else {
        echo "<div class='alert alert-success'>Sample categories created successfully</div>";
    }
} else {
    echo "<div class='alert alert-info'>Categories already exist</div>";
}

// Add reset admin password functionality
if (isset($_GET['reset_admin']) && $_GET['reset_admin'] == 'true') {
    // Create a proper password hash
    $admin_password = 'admin123';
    $new_password_hash = password_hash($admin_password, PASSWORD_DEFAULT);

    // Use prepared statement for security
    $reset_sql = "UPDATE users SET password = ? WHERE username = 'admin'";
    $reset_stmt = $conn->prepare($reset_sql);
    $reset_stmt->bind_param('s', $new_password_hash);

    if ($reset_stmt->execute()) {
        echo "<div class='alert alert-success'>";
        echo "Admin password has been reset to 'admin123'<br>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-danger'>";
        echo "Error resetting admin password: " . $conn->error . "<br>";
        echo "</div>";
    }
}

if ($success) {
    echo "<div class='alert alert-success'>";
    echo "<h3>Database setup completed successfully!</h3>";
    echo "<p>You can now <a href='index.php'>visit your blog</a> or <a href='signin.php'>sign in</a> with the default admin account:</p>";
    echo "<p>Email: admin@example.com<br>";
    echo "Password: admin123</p>";
    echo "<p><a href='?reset_admin=true' class='btn btn-warning'>Reset Admin Password</a> ";
    echo "<a href='password-check.php' class='btn btn-info'>Check Password</a> ";
    echo "<a href='password-reset.php' class='btn btn-secondary'>Password Reset Tool</a></p>";
    echo "</div>";
} else {
    echo "<div class='alert alert-danger'>";
    echo "<h3>Database setup completed with errors.</h3>";
    echo "<p>Please check the error messages above.</p>";
    echo "</div>";
}

$conn->close();
?>
        </div>
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-primary">Go to Homepage</a>
            <a href="signin.php" class="btn btn-success">Sign In</a>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
