
-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT 'default-avatar.jpg',
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Posts table
CREATE TABLE IF NOT EXISTS posts (
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
);

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT,
    comment TEXT NOT NULL,
    status ENUM('pending', 'approved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default admin user (password: admin123)
INSERT INTO users (firstname, lastname, username, email, password, is_admin)
VALUES ('Admin', 'User', 'admin', 'admin@example.com', '$2y$10$8IjGGlQtGR8YD9xDOKV.S.M0mKCxVIBzspgngL7EcAeEGd.8.TJX2', 1);

-- Insert sample categories
INSERT INTO categories (title, description) VALUES
('Travel', 'Articles about travel destinations, tips, and experiences'),
('Wildlife', 'Content related to wildlife conservation and animal species'),
('Food', 'Recipes, cooking tips, and food culture articles'),
('Technology', 'Latest tech news, reviews, and digital trends'),
('Science', 'Scientific discoveries, research, and explanations'),
('Photography', 'Photography tips, techniques, and inspiring images');
