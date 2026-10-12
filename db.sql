CREATE DATABASE IF NOT EXISTS `chargeguard`;
USE `chargeguard`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `fullname` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('user', 'admin') DEFAULT 'user',
  `deposit` INT DEFAULT 100000,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Memasukkan akun Admin default
INSERT INTO `users` (`fullname`, `email`, `password`, `role`) 
VALUES ('Admin ChargeGuard', 'admin123@gmail.com', 'admin123', 'admin')
ON DUPLICATE KEY UPDATE `id`=`id`;