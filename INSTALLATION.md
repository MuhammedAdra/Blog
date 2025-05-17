# Modern Blog Installation Guide

Follow these steps to install and set up the Modern Blog system on your server.

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)

## Installation Steps

### 1. Database Setup

1. Create a new MySQL database for your blog
2. Import the `database.sql` file into your database:
   ```
   mysql -u username -p database_name < database.sql
   ```
   Or use phpMyAdmin to import the file

### 2. Configuration

1. Open `config/database.php` and update the database connection parameters:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_database_username');
   define('DB_PASS', 'your_database_password');
   define('DB_NAME', 'your_database_name');
   ```

2. Open `config/constants.php` and update the ROOT_URL to match your domain:
   ```php
   define('ROOT_URL', 'http://yourdomain.com/blog/');
   ```

### 3. File Permissions

Make sure the following directories are writable by the web server:
- `images/posts/`
- `images/users/`

For Linux/Unix systems:
```
chmod 755 images/posts
chmod 755 images/users
```

### 4. Web Server Configuration

#### For Apache

Make sure mod_rewrite is enabled and create/edit the .htaccess file in the root directory:

```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /blog/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.+)$ index.php?url=$1 [QSA,L]
</IfModule>
```

#### For Nginx

Add the following to your server block:

```
location /blog/ {
    try_files $uri $uri/ /blog/index.php?url=$uri&$args;
}
```

### 5. Default Admin Account

After installation, you can log in with the default admin account:
- Email: admin@example.com
- Password: admin123

**Important:** Change the default admin password immediately after your first login.

### 6. Testing the Installation

1. Visit your blog URL (e.g., http://yourdomain.com/blog/)
2. Try to log in with the default admin credentials
3. Create a test post to verify everything is working correctly

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify your database credentials in `config/database.php`
   - Make sure your MySQL server is running

2. **File Upload Issues**
   - Check file permissions on the images directories
   - Verify PHP file upload settings in php.ini

3. **Blank Page or 500 Error**
   - Check your PHP error logs
   - Enable error reporting for debugging:
     ```php
     ini_set('display_errors', 1);
     error_reporting(E_ALL);
     ```

4. **URL Rewriting Not Working**
   - Verify mod_rewrite is enabled (Apache)
   - Check your .htaccess file or Nginx configuration

## Support

If you encounter any issues during installation, please check the documentation or create an issue in the project repository.
