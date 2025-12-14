CREATE DATABASE IF NOT EXISTS ems CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

USE ems;

CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE users (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_roles FOREIGN KEY (role_id) REFERENCES roles(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE venues (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  address VARCHAR(255) NOT NULL,
  capacity INT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  description VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE events (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT NULL,
  starts_at DATETIME NOT NULL,
  ends_at DATETIME NULL,
  venue_id BIGINT NOT NULL,
  category_id INT NULL,
  image_url VARCHAR(255) NULL,
  status ENUM('draft','published','cancelled') DEFAULT 'draft',
  INDEX idx_events_starts_at (starts_at),
  CONSTRAINT fk_events_venues FOREIGN KEY (venue_id) REFERENCES venues(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_events_categories FOREIGN KEY (category_id) REFERENCES categories(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE tickets (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  event_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  status ENUM('reserved','paid','cancelled') DEFAULT 'reserved',
  qr_code VARCHAR(120) NULL,
  purchased_at DATETIME NULL,
  INDEX idx_tickets_event (event_id),
  INDEX idx_tickets_user (user_id),
  CONSTRAINT fk_tickets_events FOREIGN KEY (event_id) REFERENCES events(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_tickets_users FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO roles (id, name) VALUES
(1, 'admin'),
(2, 'user');

INSERT INTO users (id, name, email, password_hash, role_id, created_at) VALUES
(2, 'Admin User', 'admin@example.com', '$2y$10$QqkG3PpBqsI0mxq2FqASeutmC5V6bsxG6IlNm2jE2SbPZsm6P0G7O', 1, '2025-12-08 22:31:12'),
(3, 'Super Admin', 'superadmin@gmail.com', '$2y$10$QqkG3PpBqsI0mxq2FqASeutmC5V6bsxG6IlNm2jE2SbPZsm6P0G7O', 1, '2025-12-08 22:33:57'),
(4, 'Admin 2', 'admin2@gmail.com', '$2y$10$1ozC.wpZ366lG8BD6aISvOuwDAnX6P/5IXecOJagkpSplHkaaaivG', 1, '2025-12-08 22:38:54'),
(8, 'Alen', 'alen@gmail.com', '$2y$10$l/3SjUlE1ef2SOkzZsrEuujT54Jt8ppyds2Ihuw1yYzjyzcTK5yHW', 2, '2025-12-09 00:45:45'),
(9, 'a', 'a@gmail.com', '$2y$10$US9MAgsGufGJJFZRh0rtPOdNYyjDd0vDv/V2c.bcegX6c9qJ4R8nO', 2, '2025-12-10 19:20:23');

INSERT INTO venues (id, name, address, capacity) VALUES
(1, 'Vijecnica', 'Obala Kulina bana 1', 1000),
(2, 'Skenderija', 'Terezija bb', 5000);

INSERT INTO categories (id, name, description) VALUES
(1, 'Music', 'Concerts and live music'),
(2, 'Food', 'Food festivals and street food'),
(3, 'Sports', 'Sporting events'),
(4, 'Culture', 'Theatre, art and exhibitions');

INSERT INTO events (
  id, title, description, starts_at, ends_at,
  venue_id, category_id, image_url, status
) VALUES
(1, 'Sarajevo Jazz Night',
 'A jazz concert featuring local and international artists at Vijećnica.',
 '2025-11-01 20:00:00', '2025-11-01 23:00:00',
 1, 1, NULL, 'published'),

(2, 'Winter Festival',
 'A festive celebration.',
 '2025-12-15 10:00:00', '2025-12-20 22:00:00',
 2, 2, NULL, 'published'),

(3, 'Admin Only Test',
 'Event created by admin2',
 '2026-01-01 20:00:00', NULL,
 1, NULL, NULL, 'draft');

INSERT INTO tickets (
  id, event_id, user_id, price, status, qr_code, purchased_at
) VALUES
(12, 1, 9, 10.00, 'reserved', NULL, NULL),
(14, 1, 4, 10.00, 'reserved', NULL, NULL);