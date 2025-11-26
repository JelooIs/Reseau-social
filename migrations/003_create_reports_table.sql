-- Create reports table for user reports on subjects and comments
CREATE TABLE IF NOT EXISTS `reports` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `reporter_id` INT NOT NULL,
  `type` ENUM('subject', 'comment') NOT NULL,
  `target_id` INT NOT NULL,
  `reason` TEXT NOT NULL,
  `status` ENUM('pending', 'resolved', 'dismissed') DEFAULT 'pending',
  `admin_note` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `resolved_at` TIMESTAMP NULL,
  FOREIGN KEY (`reporter_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX(`type`),
  INDEX(`target_id`),
  INDEX(`status`),
  INDEX(`created_at`)
);
