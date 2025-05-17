<?php
$page_title = "My Profile";
require_once 'partials/header.php';

// Check if user is logged in
require_login();

// Get user details
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param('i', $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

// Process profile update form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $firstname = clean_input($_POST['firstname']);
    $lastname = clean_input($_POST['lastname']);
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $bio = clean_input($_POST['bio']);

    // Validate inputs
    $errors = [];

    if (empty($firstname)) {
        $errors[] = 'First name is required';
    }

    if (empty($lastname)) {
        $errors[] = 'Last name is required';
    }

    if (empty($username)) {
        $errors[] = 'Username is required';
    } else {
        // Check if username already exists (excluding current user)
        $check_query = "SELECT * FROM users WHERE username = ? AND id != ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('si', $username, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $errors[] = 'Username already exists';
        }
    }

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    } else {
        // Check if email already exists (excluding current user)
        $check_query = "SELECT * FROM users WHERE email = ? AND id != ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('si', $email, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $errors[] = 'Email already exists';
        }
    }

    // Handle avatar upload
    $avatar = $user['avatar']; // Default to current avatar

    if (isset($_FILES['avatar']) && $_FILES['avatar']['size'] > 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['avatar']['name'];
        $file_size = $_FILES['avatar']['size'];
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_type = $_FILES['avatar']['type'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_extensions)) {
            $errors[] = 'Only JPG, JPEG, PNG, and GIF files are allowed';
        }

        if ($file_size > 2097152) { // 2MB
            $errors[] = 'File size must be less than 2MB';
        }

        if (empty($errors)) {
            $new_avatar = uniqid('avatar_') . '.' . $file_ext;
            $upload_path = 'images/users/' . $new_avatar;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Delete old avatar if it's not the default
                if ($avatar !== 'default-avatar.jpg') {
                    $old_avatar_path = 'images/users/' . $avatar;
                    if (file_exists($old_avatar_path)) {
                        unlink($old_avatar_path);
                    }
                }

                $avatar = $new_avatar;
            } else {
                $errors[] = 'Failed to upload avatar';
            }
        }
    }

    // Update profile if no errors
    if (empty($errors)) {
        $update_query = "UPDATE users SET firstname = ?, lastname = ?, username = ?, email = ?, bio = ?, avatar = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('ssssssi', $firstname, $lastname, $username, $email, $bio, $avatar, $user_id);

        if ($update_stmt->execute()) {
            // Update session variables
            $_SESSION['user_firstname'] = $firstname;
            $_SESSION['user_lastname'] = $lastname;
            $_SESSION['user_avatar'] = $avatar;

            set_message(SUCCESS_MESSAGE, 'Profile updated successfully');
            redirect_to('profile.php');
        } else {
            set_message(ERROR_MESSAGE, 'Failed to update profile');
        }
    } else {
        // Display errors
        foreach ($errors as $error) {
            set_message(ERROR_MESSAGE, $error);
        }
    }
}

// Process password update form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    $errors = [];

    if (empty($current_password)) {
        $errors[] = 'Current password is required';
    } else {
        // Verify current password
        if (!password_verify($current_password, $user['password'])) {
            $errors[] = 'Current password is incorrect';
        }
    }

    if (empty($new_password)) {
        $errors[] = 'New password is required';
    } elseif (strlen($new_password) < 6) {
        $errors[] = 'New password must be at least 6 characters';
    }

    if ($new_password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }

    // Update password if no errors
    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET password = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('si', $hashed_password, $user_id);

        if ($update_stmt->execute()) {
            set_message(SUCCESS_MESSAGE, 'Password updated successfully');
            redirect_to('profile.php');
        } else {
            set_message(ERROR_MESSAGE, 'Failed to update password');
        }
    } else {
        // Display errors
        foreach ($errors as $error) {
            set_message(ERROR_MESSAGE, $error);
        }
    }
}
?>

<!-- Profile Header -->
<header class="profile-header">
    <div class="container profile-header__container">
        <div class="profile-header__content">
            <div class="profile-header__avatar">
                <img src="<?php echo ROOT_URL . 'images/users/' . $user['avatar']; ?>" alt="صورة الملف الشخصي">
                <?php if($user['is_admin']): ?>
                <div class="profile-badge">
                    <i class="uil uil-shield"></i> مدير
                </div>
                <?php endif; ?>
            </div>
            <div class="profile-header__info">
                <h1><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></h1>
                <p class="profile-bio"><?php echo $user['bio'] ? $user['bio'] : 'لا يوجد سيرة ذاتية'; ?></p>
                <div class="profile-header__meta">
                    <div class="meta-item">
                        <i class="uil uil-user"></i>
                        <span><?php echo $user['username']; ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="uil uil-envelope"></i>
                        <span><?php echo $user['email']; ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="uil uil-calendar-alt"></i>
                        <span>عضو منذ <?php echo format_date($user['created_at']); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="profile-header__stats">
            <?php
            // Get user stats
            $posts_query = "SELECT COUNT(*) as post_count FROM posts WHERE author_id = ?";
            $posts_stmt = $conn->prepare($posts_query);
            $posts_stmt->bind_param('i', $user_id);
            $posts_stmt->execute();
            $posts_result = $posts_stmt->get_result();
            $post_count = $posts_result->fetch_assoc()['post_count'];

            $comments_query = "SELECT COUNT(*) as comment_count FROM comments WHERE user_id = ?";
            $comments_stmt = $conn->prepare($comments_query);
            $comments_stmt->bind_param('i', $user_id);
            $comments_stmt->execute();
            $comments_result = $comments_stmt->get_result();
            $comment_count = $comments_result->fetch_assoc()['comment_count'];
            ?>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="uil uil-file-alt"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $post_count; ?></h3>
                    <p>المقالات</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="uil uil-comment"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $comment_count; ?></h3>
                    <p>التعليقات</p>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- End of Profile Header -->

<!-- Profile Content -->
<section class="profile-content">
    <div class="container profile-content__container">
        <!-- Display Messages -->
        <?php display_message(); ?>

        <!-- Profile Tabs -->
        <div class="profile-tabs">
            <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="edit-profile-tab" data-bs-toggle="tab" data-bs-target="#edit-profile" type="button" role="tab" aria-controls="edit-profile" aria-selected="true">
                        <i class="uil uil-user-circle"></i> تعديل الملف الشخصي
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="change-password-tab" data-bs-toggle="tab" data-bs-target="#change-password" type="button" role="tab" aria-controls="change-password" aria-selected="false">
                        <i class="uil uil-key-skeleton"></i> تغيير كلمة المرور
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="profileTabsContent">
                <!-- Edit Profile Tab -->
                <div class="tab-pane fade show active" id="edit-profile" role="tabpanel" aria-labelledby="edit-profile-tab">
                    <div class="form__section-container">
                        <h3>تعديل الملف الشخصي</h3>
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                            <div class="form__control">
                                <label for="firstname">الاسم الأول</label>
                                <input type="text" id="firstname" name="firstname" value="<?php echo $user['firstname']; ?>" required>
                            </div>
                            <div class="form__control">
                                <label for="lastname">الاسم الأخير</label>
                                <input type="text" id="lastname" name="lastname" value="<?php echo $user['lastname']; ?>" required>
                            </div>
                            <div class="form__control">
                                <label for="username">اسم المستخدم</label>
                                <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required>
                            </div>
                            <div class="form__control">
                                <label for="email">البريد الإلكتروني</label>
                                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                            </div>
                            <div class="form__control">
                                <label for="bio">السيرة الذاتية</label>
                                <textarea id="bio" name="bio" rows="5"><?php echo $user['bio']; ?></textarea>
                            </div>
                            <div class="form__control">
                                <label for="avatar">الصورة الشخصية</label>
                                <input type="file" id="avatar" name="avatar" accept="image/*">
                                <small>اترك هذا الحقل فارغًا للاحتفاظ بالصورة الحالية</small>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="uil uil-save"></i> حفظ التغييرات
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Change Password Tab -->
                <div class="tab-pane fade" id="change-password" role="tabpanel" aria-labelledby="change-password-tab">
                    <div class="form__section-container">
                        <h3>تغيير كلمة المرور</h3>
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                            <div class="form__control">
                                <label for="current_password">كلمة المرور الحالية</label>
                                <input type="password" id="current_password" name="current_password" required>
                            </div>
                            <div class="form__control">
                                <label for="new_password">كلمة المرور الجديدة</label>
                                <input type="password" id="new_password" name="new_password" required>
                                <small>يجب أن تكون كلمة المرور 6 أحرف على الأقل</small>
                            </div>
                            <div class="form__control">
                                <label for="confirm_password">تأكيد كلمة المرور</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" name="update_password" class="btn btn-primary">
                                <i class="uil uil-lock"></i> تحديث كلمة المرور
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End of Profile Content -->

<?php require_once 'partials/footer.php'; ?>
