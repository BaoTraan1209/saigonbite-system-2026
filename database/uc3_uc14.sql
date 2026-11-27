-- SaigonBite UC3 (customer ordering) + UC14 (brand/menu management)
-- MySQL 8.0+ | Consolidated schema matching the current Laravel migrations.
-- Select or create your database before running this script.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'customer') NOT NULL,
  `remember_token` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `customer_addresses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `recipient_name` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `street` VARCHAR(255) NOT NULL,
  `district` VARCHAR(150) NOT NULL,
  `city` VARCHAR(150) NOT NULL,
  `additional_details` VARCHAR(500) NULL DEFAULT NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_addresses_user_id_is_default_index` (`user_id`, `is_default`),
  CONSTRAINT `customer_addresses_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `hubs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `address` TEXT NOT NULL,
  `phone` VARCHAR(30) NULL DEFAULT NULL,
  `capacity` INT UNSIGNED NOT NULL,
  `status` ENUM('draft', 'active', 'suspending', 'suspended', 'archived') NOT NULL DEFAULT 'draft',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `brands` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `status` ENUM('active', 'inactive', 'archived') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `brand_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `is_available` TINYINT(1) NOT NULL DEFAULT 1,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menu_items_brand_name_unique` (`brand_id`, `name`),
  CONSTRAINT `menu_items_brand_id_foreign`
    FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `hub_brands` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hub_id` BIGINT UNSIGNED NOT NULL,
  `brand_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hub_brands_hub_id_brand_id_unique` (`hub_id`, `brand_id`),
  CONSTRAINT `hub_brands_hub_id_foreign`
    FOREIGN KEY (`hub_id`) REFERENCES `hubs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hub_brands_brand_id_foreign`
    FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `hub_menu_items` (
  `hub_id` BIGINT UNSIGNED NOT NULL,
  `menu_item_id` BIGINT UNSIGNED NOT NULL,
  `is_available` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`hub_id`, `menu_item_id`),
  KEY `hub_menu_items_hub_id_is_available_index` (`hub_id`, `is_available`),
  CONSTRAINT `hub_menu_items_hub_id_foreign`
    FOREIGN KEY (`hub_id`) REFERENCES `hubs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hub_menu_items_menu_item_id_foreign`
    FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hub_id` BIGINT UNSIGNED NOT NULL,
  `brand_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `brand_name` VARCHAR(255) NULL DEFAULT NULL,
  `customer_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `customer_address_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `voucher_code` VARCHAR(100) NULL DEFAULT NULL,
  `idempotency_key` CHAR(36) NULL DEFAULT NULL,
  `customer_name` VARCHAR(255) NOT NULL,
  `customer_phone` VARCHAR(30) NOT NULL,
  `delivery_address` TEXT NOT NULL,
  `payment_method` VARCHAR(30) NOT NULL DEFAULT 'COD',
  `payment_status` ENUM('unpaid', 'paid') NOT NULL DEFAULT 'unpaid',
  `subtotal` DECIMAL(12,2) NOT NULL,
  `delivery_fee` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `surcharge` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` DECIMAL(12,2) NOT NULL,
  `status` ENUM('submitted', 'accepted', 'rejected', 'preparing', 'ready', 'delivering', 'completed', 'delivery_failed') NOT NULL DEFAULT 'submitted',
  `notes` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_idempotency_key_unique` (`idempotency_key`),
  CONSTRAINT `orders_hub_id_foreign`
    FOREIGN KEY (`hub_id`) REFERENCES `hubs` (`id`),
  CONSTRAINT `orders_brand_id_foreign`
    FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_customer_id_foreign`
    FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_customer_address_id_foreign`
    FOREIGN KEY (`customer_address_id`) REFERENCES `customer_addresses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `menu_item_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `item_name` VARCHAR(255) NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `line_total` DECIMAL(12,2) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `order_items_order_id_foreign`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_menu_item_id_foreign`
    FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `deliveries` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `shipper_name` VARCHAR(255) NULL DEFAULT NULL,
  `shipper_phone` VARCHAR(30) NULL DEFAULT NULL,
  `status` ENUM('waiting', 'assigned', 'picked_up', 'delivered', 'failed') NOT NULL DEFAULT 'waiting',
  `picked_up_at` TIMESTAMP NULL DEFAULT NULL,
  `delivered_at` TIMESTAMP NULL DEFAULT NULL,
  `cod_amount` DECIMAL(12,2) NULL DEFAULT NULL,
  `cash_collected` DECIMAL(12,2) NULL DEFAULT NULL,
  `failure_reason` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `deliveries_order_id_unique` (`order_id`),
  CONSTRAINT `deliveries_order_id_foreign`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `actor` VARCHAR(255) NULL DEFAULT NULL,
  `action` VARCHAR(255) NOT NULL,
  `subject_type` VARCHAR(255) NOT NULL,
  `subject_id` BIGINT UNSIGNED NOT NULL,
  `details` JSON NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
