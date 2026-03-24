-- =====================================
-- CONGO CONNECT - DATABASE SCHEMA
-- =====================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ========================
-- ROLES
-- ========================
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_key VARCHAR(50) UNIQUE,
    role_name VARCHAR(100),
    description TEXT
);

-- ========================
-- USERS
-- ========================
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id BIGINT UNSIGNED,
    status VARCHAR(50) DEFAULT 'active',
    email_verified_at DATETIME NULL,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- ========================
-- USER SETTINGS
-- ========================
CREATE TABLE user_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    interface_language VARCHAR(5) DEFAULT 'fr',
    timezone VARCHAR(50) DEFAULT 'Europe/London',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ========================
-- PROFILES
-- ========================
CREATE TABLE profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    headline VARCHAR(255),
    bio TEXT,
    country_id BIGINT UNSIGNED NULL,
    city_id BIGINT UNSIGNED NULL,
    is_public TINYINT(1) DEFAULT 1,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ========================
-- COMPANIES
-- ========================
CREATE TABLE companies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_user_id BIGINT UNSIGNED,
    name VARCHAR(255),
    description TEXT,
    country_id BIGINT UNSIGNED NULL,
    city_id BIGINT UNSIGNED NULL,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (owner_user_id) REFERENCES users(id)
);

-- ========================
-- ASSOCIATIONS
-- ========================
CREATE TABLE associations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_user_id BIGINT UNSIGNED,
    name VARCHAR(255),
    description TEXT,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (owner_user_id) REFERENCES users(id)
);

-- ========================
-- JOBS
-- ========================
CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED,
    title VARCHAR(255),
    description TEXT,
    country_id BIGINT UNSIGNED NULL,
    city_id BIGINT UNSIGNED NULL,
    created_at DATETIME,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

-- ========================
-- JOB APPLICATIONS
-- ========================
CREATE TABLE job_applications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    profile_snapshot TEXT,
    status VARCHAR(50) DEFAULT 'submitted',
    created_at DATETIME,
    FOREIGN KEY (job_id) REFERENCES jobs(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ========================
-- EVENTS
-- ========================
CREATE TABLE events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    title VARCHAR(255),
    description TEXT,
    event_date DATETIME,
    city_id BIGINT UNSIGNED NULL,
    created_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ========================
-- ARTICLES
-- ========================
CREATE TABLE articles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    title VARCHAR(255),
    content LONGTEXT,
    created_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ========================
-- NEWS FEED POSTS
-- ========================
CREATE TABLE posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    content TEXT,
    created_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ========================
-- MEDIA
-- ========================
CREATE TABLE media (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    file_path VARCHAR(255),
    file_type VARCHAR(50),
    created_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ========================
-- MESSAGES
-- ========================
CREATE TABLE conversations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at DATETIME
);

CREATE TABLE messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id BIGINT UNSIGNED,
    sender_id BIGINT UNSIGNED,
    message TEXT,
    created_at DATETIME,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id),
    FOREIGN KEY (sender_id) REFERENCES users(id)
);

-- ========================
-- SUBSCRIPTIONS
-- ========================
CREATE TABLE subscription_plans (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    slug VARCHAR(100),
    price DECIMAL(10,2),
    duration_value INT,
    duration_unit VARCHAR(20)
);

CREATE TABLE user_subscriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    plan_id BIGINT UNSIGNED,
    start_date DATETIME,
    end_date DATETIME,
    status VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id)
);

-- ========================
-- PERMISSIONS
-- ========================
CREATE TABLE platform_modules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    module_key VARCHAR(100),
    module_name VARCHAR(100)
);

CREATE TABLE module_actions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    module_id BIGINT UNSIGNED,
    action_key VARCHAR(100),
    action_name VARCHAR(100),
    FOREIGN KEY (module_id) REFERENCES platform_modules(id)
);

CREATE TABLE plan_permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    plan_id BIGINT UNSIGNED,
    module_id BIGINT UNSIGNED,
    action_id BIGINT UNSIGNED,
    is_allowed TINYINT(1),
    quota_value INT NULL,
    quota_period VARCHAR(50) NULL
);

SET FOREIGN_KEY_CHECKS = 1;