-- Add pseudo column to users and populate from nom + prenoms for existing users
ALTER TABLE `users` 
  ADD COLUMN `pseudo` VARCHAR(100) NULL AFTER `prenoms`;

UPDATE `users` SET pseudo = TRIM(CONCAT(IFNULL(nom, ''), ' ', IFNULL(prenoms, '')));

-- If desired, make pseudo NOT NULL in future migrations after verifying data
