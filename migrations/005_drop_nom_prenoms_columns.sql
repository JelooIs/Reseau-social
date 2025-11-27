-- Drop nom and prenoms columns from users table (no longer needed, using pseudo instead)
ALTER TABLE `users` DROP COLUMN `nom`;
ALTER TABLE `users` DROP COLUMN `prenoms`;
