-- Add 'message' type to reports table to support private message reporting
ALTER TABLE `reports` 
MODIFY COLUMN `type` ENUM('subject', 'comment', 'message') NOT NULL;
