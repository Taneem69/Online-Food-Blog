-- Shared schema (do NOT drop or alter these tables)
-- Run this once to set up the database

CREATE DATABASE IF NOT EXISTS food_blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE food_blog;

CREATE TABLE IF NOT EXISTS users (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(150)  NOT NULL,
    email           VARCHAR(255)  NOT NULL UNIQUE,
    password_hash   VARCHAR(255)  NOT NULL,
    role            ENUM('admin','member') NOT NULL DEFAULT 'member',
    profile_picture VARCHAR(255)  DEFAULT NULL,
    remember_token  VARCHAR(255)  DEFAULT NULL,
    created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS restaurants (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(200)  NOT NULL,
    location         VARCHAR(200)  NOT NULL,
    area             VARCHAR(200)  NOT NULL,
    short_background TEXT          NOT NULL,
    goals            TEXT          NOT NULL,
    created_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS menu_items (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT           NOT NULL,
    name          VARCHAR(200)  NOT NULL,
    description   TEXT          NOT NULL,
    price         DECIMAL(10,2) NOT NULL,
    image_path    VARCHAR(255)  DEFAULT NULL,
    created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reviews (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    menu_item_id INT  NOT NULL,
    user_id      INT  NOT NULL,
    comment      TEXT NOT NULL,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)      REFERENCES users(id)      ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS food_experience_posts (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT  NOT NULL,
    title         VARCHAR(300) NOT NULL,
    content       TEXT NOT NULL,
    post_type     ENUM('restaurant','food','both') NOT NULL DEFAULT 'both',
    restaurant_id INT  DEFAULT NULL,
    menu_item_id  INT  DEFAULT NULL,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)       REFERENCES users(id)       ON DELETE CASCADE,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE SET NULL,
    FOREIGN KEY (menu_item_id)  REFERENCES menu_items(id)  ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS food_experience_comments (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    post_id    INT  NOT NULL,
    user_id    INT  NOT NULL,
    comment    TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id)  REFERENCES food_experience_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)  REFERENCES users(id)                 ON DELETE CASCADE
);

-- ⚠️  DO NOT insert admin here — run setup.php in your browser instead.
-- setup.php generates a proper bcrypt hash and inserts the admin account.

