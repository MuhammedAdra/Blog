<?php
// Database connection parameters
require_once 'config/database.php';

// Get admin user
$query = "SELECT * FROM users WHERE username = 'admin'";
$result = $conn->query($query);

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $stored_hash = $user['password'];
    
    // Test passwords
    $test_passwords = [
        'admin123',
        'admin',
        'password',
        '123456',
        'Admin123',
        'administrator'
    ];
    
    $matches = [];
    foreach ($test_passwords as $test_password) {
        if (password_verify($test_password, $stored_hash)) {
            $matches[] = $test_password;
        }
    }
} else {
    $error = "Admin user not found";
}

// Create a new admin user if requested
if (isset($_GET['create_admin']) && $_GET['create_admin'] === 'yes') {
    $firstname = 'Admin';
    $lastname = 'User';
    $username = 'admin2';
    $email = 'admin2@example.com';
    $password = 'admin123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $is_admin = 1;
    
    $insert_query = "INSERT INTO users (firstname, lastname, username, email, password, is_admin) 
                    VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param('sssssi', $firstname, $lastname, $username, $email, $hashed_password, $is_admin);
    
    if ($stmt->execute()) {
        $success_message = "New admin user created successfully. Username: $username, Password: $password";
    } else {
        $error_message = "Failed to create new admin user: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Check Tool</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h1 {
            margin-bottom: 30px;
            text-align: center;
        }
        code {
            background-color: #f8f9fa;
            padding: 2px 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Password Check Tool</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-header">
                Admin User Password Check
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php else: ?>
                    <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
                    <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                    <p><strong>Stored Password Hash:</strong> <code><?php echo $stored_hash; ?></code></p>
                    
                    <?php if (empty($matches)): ?>
                        <div class="alert alert-warning">
                            None of the common passwords match the stored hash. Try using the password reset tool.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success">
                            <p><strong>Matching Passwords:</strong></p>
                            <ul>
                                <?php foreach ($matches as $match): ?>
                                    <li><code><?php echo $match; ?></code></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                Create New Admin User
            </div>
            <div class="card-body">
                <p>If you can't access the existing admin account, you can create a new admin user.</p>
                <a href="?create_admin=yes" class="btn btn-warning">Create New Admin User</a>
            </div>
        </div>
        
        <div class="mt-4 text-center">
            <a href="password-reset.php" class="btn btn-primary">Go to Password Reset Tool</a>
            <a href="signin.php" class="btn btn-success">Go to Sign In</a>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
