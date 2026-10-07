-- ==========================================================
-- Nobanno Agro Farm - database schema
--
-- The repository had no .sql file, so this schema was rebuilt
-- from the queries used in Model/*.php. If your existing
-- database differs, compare column names before importing.
--
-- Import:  mysql -u root -p < database/schema.sql
-- ==========================================================

CREATE DATABASE IF NOT EXISTS NobannoAgro
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE NobannoAgro;


CREATE TABLE IF NOT EXISTS users (
    user_id     INT AUTO_INCREMENT PRIMARY KEY,
    first_name  VARCHAR(50)  NOT NULL,
    last_name   VARCHAR(50)  NOT NULL,
    email       VARCHAR(120) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,           -- password_hash()
    role        ENUM('admin','customer','farmer','delivery_man')
                NOT NULL DEFAULT 'customer',
    phone       VARCHAR(20)  NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL UNIQUE,
    address     TEXT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS farmers (
    farmer_id   INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL UNIQUE,
    farm_name   VARCHAR(100) NOT NULL,
    location    VARCHAR(100) NULL,
    address     TEXT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS delivery_men (
    delivery_man_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL UNIQUE,
    vehicle_type    VARCHAR(50) NULL,
    vehicle_number  VARCHAR(50) NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS crops (
    crop_id      INT AUTO_INCREMENT PRIMARY KEY,
    farmer_id    INT NOT NULL,
    crop_name    VARCHAR(100) NOT NULL,
    category     VARCHAR(50)  NOT NULL,
    description  TEXT NULL,
    price_per_kg DECIMAL(10,2) NOT NULL,
    quantity     DECIMAL(10,2) NOT NULL DEFAULT 0,
    unit         VARCHAR(20)   NOT NULL DEFAULT 'kg',
    status       VARCHAR(20)   NOT NULL DEFAULT 'available',
    image        VARCHAR(255)  NULL,
    created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id) ON DELETE CASCADE,
    CHECK (quantity >= 0)
);

CREATE TABLE IF NOT EXISTS cart (
    cart_id     INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    crop_id     INT NOT NULL,
    quantity    INT NOT NULL DEFAULT 1,
    UNIQUE KEY uq_cart_item (customer_id, crop_id),
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    FOREIGN KEY (crop_id)     REFERENCES crops(crop_id)         ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    order_id         INT AUTO_INCREMENT PRIMARY KEY,
    customer_id      INT NOT NULL,
    total_amount     DECIMAL(10,2) NOT NULL,
    delivery_charge  DECIMAL(10,2) NOT NULL DEFAULT 0,
    delivery_address TEXT NOT NULL,
    status           VARCHAR(20) NOT NULL DEFAULT 'pending',
    order_date       TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id      INT NOT NULL,
    crop_id       INT NOT NULL,
    farmer_id     INT NOT NULL,
    quantity      DECIMAL(10,2) NOT NULL,
    price_per_kg  DECIMAL(10,2) NOT NULL,
    subtotal      DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)  REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (crop_id)   REFERENCES crops(crop_id),
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id)
);

CREATE TABLE IF NOT EXISTS payments (
    payment_id     INT AUTO_INCREMENT PRIMARY KEY,
    order_id       INT NOT NULL,
    customer_id    INT NOT NULL,
    amount         DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(30)   NOT NULL,
    transaction_id VARCHAR(100)  NULL,
    payment_status VARCHAR(20)   NOT NULL DEFAULT 'pending',  -- pending | paid | failed
    FOREIGN KEY (order_id)    REFERENCES orders(order_id)       ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

CREATE TABLE IF NOT EXISTS deliveries (
    delivery_id      INT AUTO_INCREMENT PRIMARY KEY,
    order_id         INT NOT NULL,
    delivery_man_id  INT NULL,
    delivery_address TEXT NOT NULL,
    delivery_status  VARCHAR(30) NOT NULL DEFAULT 'assigned',
    FOREIGN KEY (order_id)        REFERENCES orders(order_id)               ON DELETE CASCADE,
    FOREIGN KEY (delivery_man_id) REFERENCES delivery_men(delivery_man_id)  ON DELETE SET NULL
);


-- ----------------------------------------------------------
-- First admin account.
-- 1) make a hash:   php -r "echo password_hash('CHANGE_ME', PASSWORD_DEFAULT);"
-- 2) paste it below and run the statement.
-- ----------------------------------------------------------
-- INSERT INTO users (first_name, last_name, email, password, role, phone)
-- VALUES ('Admin', 'User', 'admin@example.com', '<PASTE_HASH_HERE>', 'admin', '01000000000');
