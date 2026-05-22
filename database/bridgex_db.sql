-- BridgeX Platform — Database Schema
-- Created by: Nora

CREATE DATABASE IF NOT EXISTS bridgex_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bridgex_db;

-- ============================================================
-- Table: users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
                                     id          INT AUTO_INCREMENT PRIMARY KEY,
                                     name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    role        ENUM('admin','client','developer') NOT NULL DEFAULT 'client',
    created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Table: projects
-- ============================================================
CREATE TABLE IF NOT EXISTS projects (
                                        id           INT AUTO_INCREMENT PRIMARY KEY,
                                        client_id    INT          NOT NULL,
                                        project_type VARCHAR(50)  NOT NULL,
    title        VARCHAR(200) NOT NULL,
    description  TEXT         NOT NULL,
    budget       DECIMAL(10,2) NOT NULL,
    duration     VARCHAR(50)  NOT NULL,
    features     TEXT,
    status       ENUM('open','in_progress','completed','cancelled') NOT NULL DEFAULT 'open',
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Table: offers
-- ============================================================
CREATE TABLE IF NOT EXISTS offers (
                                      id            INT AUTO_INCREMENT PRIMARY KEY,
                                      project_id    INT          NOT NULL,
                                      developer_id  INT          NOT NULL,
                                      price         DECIMAL(10,2) NOT NULL,
    delivery_time VARCHAR(50)  NOT NULL,
    message       TEXT         NOT NULL,
    status        ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id)   REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (developer_id) REFERENCES users(id)    ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Table: messages (Contact Form)
-- ============================================================
CREATE TABLE IF NOT EXISTS messages (
                                        id         INT AUTO_INCREMENT PRIMARY KEY,
                                        name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    message    TEXT         NOT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Table: reviews
-- ============================================================
CREATE TABLE IF NOT EXISTS reviews (
                                       id           INT AUTO_INCREMENT PRIMARY KEY,
                                       project_id   INT NOT NULL,
                                       client_id    INT NOT NULL,
                                       developer_id INT NOT NULL,
                                       rating       TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment      TEXT,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id)   REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (developer_id) REFERENCES users(id)    ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Default Admin Account
-- password: Admin@123  (hashed with PASSWORD_BCRYPT)
-- ============================================================
INSERT INTO users (name, email, password, role) VALUES
    (
        'Admin BridgeX',
        'admin@bridgex.com',
        '$2y$12$YKpGCVbOqO7.UcbZ3R/GaOv9FJXxLBRRlMlTGv2rOe7XjQdMDWlly',
        'admin'
    );