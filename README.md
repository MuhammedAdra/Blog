# Modern Blog System

A complete blog system with user authentication, admin panel, and dynamic content management.

## Features

- **User Authentication**
  - Registration and login
  - User profiles
  - Role-based access control (admin/regular users)

- **Content Management**
  - Create, edit, and delete blog posts
  - Categories management
  - Featured posts
  - Tags support
  - Draft/published status

- **Admin Dashboard**
  - Comprehensive statistics
  - User management
  - Content moderation
  - Comment approval system

- **Frontend Features**
  - Responsive design
  - Search functionality
  - Category filtering
  - Related posts
  - Comments system
  - Newsletter subscription

## Installation

1. **Database Setup**
   - Import the `database.sql` file into your MySQL database
   - Default admin credentials: admin@example.com / admin123

2. **Configuration**
   - Update database connection details in `config/database.php`
   - Set your site URL in `config/constants.php`

3. **File Permissions**
   - Ensure the `images/posts` and `images/users` directories are writable

## Directory Structure

- `admin/` - Admin panel files
- `config/` - Configuration files
- `includes/` - Helper functions and utilities
- `partials/` - Reusable page components
- `images/` - Image uploads directory

## Technologies Used

- PHP 7.4+
- MySQL 5.7+
- HTML5, CSS3, JavaScript
- Bootstrap 5
- TinyMCE for rich text editing

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Credits

Developed by Modern Blog Team
