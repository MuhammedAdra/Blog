<?php
$page_title = "إنشاء حساب جديد";
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
        set_message(ERROR_MESSAGE, 'جميع الحقول مطلوبة');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_message(ERROR_MESSAGE, 'يرجى إدخال بريد إلكتروني صالح');
    } elseif (strlen($password) < 6) {
        set_message(ERROR_MESSAGE, 'يجب أن تكون كلمة المرور 6 أحرف على الأقل');
    } elseif ($password !== $confirm_password) {
        set_message(ERROR_MESSAGE, 'كلمات المرور غير متطابقة');
    } else {
        // Check if username or email already exists
        $check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('ss', $username, $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            set_message(ERROR_MESSAGE, 'اسم المستخدم أو البريد الإلكتروني موجود بالفعل');
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
            
            // Insert user
            $insert_query = "INSERT INTO users (firstname, lastname, username, email, password, avatar, is_admin) VALUES (?, ?, ?, ?, ?, ?, 0)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param('ssssss', $firstname, $lastname, $username, $email, $hashed_password, $avatar_name);
            
            if ($insert_stmt->execute()) {
                // Set session variables
                $user_id = $conn->insert_id;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_username'] = $username;
                $_SESSION['user_firstname'] = $firstname;
                $_SESSION['user_lastname'] = $lastname;
                $_SESSION['user_avatar'] = $avatar_name;
                $_SESSION['user_is_admin'] = 0;
                
                // Store success notification in session
                $_SESSION['auth_notification'] = [
                    'type' => 'success',
                    'message' => 'تم إنشاء حسابك بنجاح! مرحباً ' . $firstname . '!'
                ];
                
                redirect_to('index.php');
            } else {
                set_message(ERROR_MESSAGE, 'فشل في إنشاء الحساب. يرجى المحاولة مرة أخرى.');
            }
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
                <h2>إنشاء حساب جديد</h2>
                <p>أنشئ حسابك للوصول إلى جميع ميزات المدونة</p>
            </div>
            
            <div class="auth-body">
                <!-- Hidden Message Container (Will be transformed to notifications) -->
                <div class="hidden-messages" style="display: none;">
                    <?php display_message(); ?>
                </div>
                
                <div class="avatar-preview">
                    <img src="<?php echo ROOT_URL; ?>images/users/<?php echo DEFAULT_AVATAR; ?>" id="avatar-preview-img" alt="صورة الملف الشخصي">
                </div>
                
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="auth-form" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstname">الاسم الأول</label>
                                <input type="text" id="firstname" name="firstname" placeholder="أدخل اسمك الأول" required>
                                <i class="uil uil-user"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lastname">الاسم الأخير</label>
                                <input type="text" id="lastname" name="lastname" placeholder="أدخل اسمك الأخير" required>
                                <i class="uil uil-user"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">اسم المستخدم</label>
                        <input type="text" id="username" name="username" placeholder="اختر اسم مستخدم" required>
                        <i class="uil uil-at"></i>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <input type="email" id="email" name="email" placeholder="أدخل بريدك الإلكتروني" required>
                        <i class="uil uil-envelope"></i>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">كلمة المرور</label>
                        <input type="password" id="password" name="password" placeholder="أنشئ كلمة مرور" required>
                        <i class="uil uil-lock"></i>
                        <span class="password-toggle" onclick="togglePassword('password')">
                            <i class="uil uil-eye"></i>
                        </span>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">تأكيد كلمة المرور</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="أكد كلمة المرور" required>
                        <i class="uil uil-lock"></i>
                        <span class="password-toggle" onclick="togglePassword('confirmPassword')">
                            <i class="uil uil-eye"></i>
                        </span>
                    </div>
                    
                    <div class="form-group file-input">
                        <label for="avatar" class="file-input-label">
                            <i class="uil uil-image-upload"></i>
                            <span>اختر صورة شخصية (اختياري)</span>
                        </label>
                        <input type="file" id="avatar" name="avatar" accept="image/*" onchange="previewAvatar(this)">
                    </div>
                    
                    <button type="submit" class="auth-btn">
                        <i class="uil uil-user-plus"></i> إنشاء حساب
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
                <p>لديك حساب بالفعل؟ <a href="signin-new.php">تسجيل الدخول</a></p>
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
        
        // Preview avatar image
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    document.getElementById('avatar-preview-img').src = e.target.result;
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
