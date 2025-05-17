<?php
$page_title = "تسجيل الدخول";
require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'includes/functions.php';

// Check if user is already logged in
if (is_logged_in()) {
    redirect_to('index.php');
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email_username = clean_input($_POST['email_username']);
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']) ? true : false;
    
    // Validate input
    if (empty($email_username) || empty($password)) {
        set_message(ERROR_MESSAGE, 'جميع الحقول مطلوبة');
    } else {
        // Check if user exists
        $query = "SELECT * FROM users WHERE email = ? OR username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $email_username, $email_username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_username'] = $user['username'];
                $_SESSION['user_firstname'] = $user['firstname'];
                $_SESSION['user_lastname'] = $user['lastname'];
                $_SESSION['user_avatar'] = $user['avatar'];
                $_SESSION['user_is_admin'] = $user['is_admin'];
                
                // Set remember me cookie if checked
                if ($remember_me) {
                    $token = bin2hex(random_bytes(32));
                    $expires = time() + (30 * 24 * 60 * 60); // 30 days
                    
                    // Store token in database
                    $token_query = "INSERT INTO remember_tokens (user_id, token, expires) VALUES (?, ?, ?)";
                    $token_stmt = $conn->prepare($token_query);
                    $token_stmt->bind_param('isi', $user['id'], $token, $expires);
                    $token_stmt->execute();
                    
                    // Set cookie
                    setcookie('remember_token', $token, $expires, '/', '', false, true);
                }
                
                // Update password hash if using old algorithm
                if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                    $new_hash = password_hash($password, PASSWORD_DEFAULT);
                    $update_query = "UPDATE users SET password = ? WHERE id = ?";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->bind_param('si', $new_hash, $user['id']);
                    $update_stmt->execute();
                }
                
                // Store success notification in session
                $_SESSION['auth_notification'] = [
                    'type' => 'success',
                    'message' => 'تم تسجيل الدخول بنجاح. مرحباً ' . $user['firstname'] . '!'
                ];
                
                // Redirect based on user role
                if ($user['is_admin']) {
                    redirect_to('admin/dashboard.php');
                } else {
                    redirect_to('index.php');
                }
            } else {
                set_message(ERROR_MESSAGE, 'بيانات الدخول غير صحيحة');
            }
        } else {
            set_message(ERROR_MESSAGE, 'بيانات الدخول غير صحيحة');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - مدونتي</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- IconScout Unicons -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo ROOT_URL; ?>style.css">
    <!-- Auth CSS -->
    <link rel="stylesheet" href="<?php echo ROOT_URL; ?>css/auth.css">
    <!-- Notifications CSS -->
    <link rel="stylesheet" href="<?php echo ROOT_URL; ?>admin/css/notifications.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-container">
            <div class="auth-header">
                <div class="auth-logo">
                    <a href="<?php echo ROOT_URL; ?>"><i class="uil uil-blog"></i> مدونتي</a>
                </div>
                <h2>تسجيل الدخول</h2>
                <p>مرحباً بعودتك! يرجى تسجيل الدخول للوصول إلى حسابك</p>
            </div>
            
            <div class="auth-body">
                <!-- Hidden Message Container (Will be transformed to notifications) -->
                <div class="hidden-messages" style="display: none;">
                    <?php display_message(); ?>
                </div>
                
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="email_username">البريد الإلكتروني أو اسم المستخدم</label>
                        <input type="text" id="email_username" name="email_username" placeholder="أدخل بريدك الإلكتروني أو اسم المستخدم" required>
                        <i class="uil uil-user"></i>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">كلمة المرور</label>
                        <input type="password" id="password" name="password" placeholder="أدخل كلمة المرور" required>
                        <i class="uil uil-lock"></i>
                        <span class="password-toggle" onclick="togglePassword('password')">
                            <i class="uil uil-eye"></i>
                        </span>
                    </div>
                    
                    <div class="auth-help">
                        <div class="remember-me">
                            <input type="checkbox" id="remember_me" name="remember_me">
                            <label for="remember_me">تذكرني</label>
                        </div>
                        <a href="password-reset.php">نسيت كلمة المرور؟</a>
                    </div>
                    
                    <button type="submit" class="auth-btn">
                        <i class="uil uil-signin"></i> تسجيل الدخول
                    </button>
                    
                    <div class="auth-divider">
                        <span>أو</span>
                    </div>
                    
                    <div class="social-login">
                        <button type="button" class="social-btn google">
                            <i class="uil uil-google"></i> Google
                        </button>
                        <button type="button" class="social-btn facebook">
                            <i class="uil uil-facebook-f"></i> Facebook
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="auth-footer">
                <p>ليس لديك حساب؟ <a href="signup-new.php">إنشاء حساب جديد</a></p>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="<?php echo ROOT_URL; ?>main.js"></script>
    <!-- Notifications JavaScript -->
    <script src="<?php echo ROOT_URL; ?>admin/js/notifications.js"></script>
    
    <script>
        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.querySelector(`#${inputId} + .password-toggle i`);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('uil-eye');
                icon.classList.add('uil-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('uil-eye-slash');
                icon.classList.add('uil-eye');
            }
        }
    </script>
</body>
</html>
