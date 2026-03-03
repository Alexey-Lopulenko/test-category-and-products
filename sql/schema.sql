DROP DATABASE IF EXISTS catalog_test;
CREATE DATABASE catalog_test
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE catalog_test;

SET NAMES utf8mb4;
SET time_zone = '+00:00';

DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;

CREATE TABLE categories (
                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                            name VARCHAR(255) NOT NULL,
                            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            PRIMARY KEY (id),
                            KEY idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
                          id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                          category_id BIGINT UNSIGNED NOT NULL,
                          name VARCHAR(255) NOT NULL,
                          price DECIMAL(10,2) NOT NULL,
                          created_at DATETIME NOT NULL,
                          PRIMARY KEY (id),
                          KEY idx_category (category_id),
                          KEY idx_created (created_at),
                          KEY idx_price (price),
                          CONSTRAINT fk_products_category
                              FOREIGN KEY (category_id) REFERENCES categories(id)
                                  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;