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