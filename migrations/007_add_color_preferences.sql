-- Add color customization columns to user_preferences table
ALTER TABLE `user_preferences` 
ADD COLUMN `primary_color` VARCHAR(7) DEFAULT '#0d6efd' AFTER `background_mode`,
ADD COLUMN `secondary_color` VARCHAR(7) DEFAULT '#6c757d' AFTER `primary_color`,
ADD COLUMN `accent_color` VARCHAR(7) DEFAULT '#198754' AFTER `secondary_color`,
ADD COLUMN `text_color` VARCHAR(7) DEFAULT NULL AFTER `accent_color`;

-- Create index for faster color-based queries (optional)
CREATE INDEX `idx_color_scheme` ON `user_preferences` (`primary_color`, `secondary_color`);
