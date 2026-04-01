-- Create roles table
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(50) UNIQUE NOT NULL,
  `label` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create permissions table
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100) UNIQUE NOT NULL,
  `label` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create role_permission mapping table
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` INT NOT NULL,
  `permission_id` INT NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
);

-- Insert roles
INSERT INTO `roles` (`name`, `label`) VALUES
('student', 'Étudiant'),
('teacher', 'Professeur'),
('bde', 'Membre du BDE'),
('ca', 'Membre du CA'),
('moderator', 'Modérateur'),
('admin', 'Administrateur');

-- Insert permissions
INSERT INTO `permissions` (`name`, `label`, `description`) VALUES
-- Subject permissions
('create_subject', 'Création de sujets', 'Pouvoir créer de nouveaux sujets de discussion'),
('edit_subject', 'Modification de sujets', 'Pouvoir modifier des sujets'),
('delete_subject', 'Suppression de sujets', 'Pouvoir supprimer des sujets'),
-- Message permissions
('send_message', 'Envoi de messages', 'Pouvoir envoyer des messages privés'),
('message_student', 'Messages avec étudiants', 'Pouvoir communiquer avec les étudiants'),
('message_teacher', 'Messages avec profs', 'Pouvoir communiquer avec les professeurs'),
-- Announcement permissions
('create_announcement', 'Créer des annonces', 'Pouvoir créer des annonces publiques'),
-- Report/Moderation permissions
('view_reports', 'Voir les signalements', 'Pouvoir consulter les signalements'),
('manage_reports', 'Gérer les signalements', 'Pouvoir traiter les signalements'),
('delete_subject_mod', 'Supprimer un sujet (modération)', 'Pouvoir supprimer les sujets'),
('edit_subject_mod', 'Modifier un sujet (modération)', 'Pouvoir modifier les sujets');

-- Assign permissions to roles
-- Student permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),  -- create_subject
(1, 6),  -- message_student
(1, 7),  -- message_teacher
(1, 11); -- send_message

-- Teacher permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(2, 1),   -- create_subject
(2, 3),   -- message_student
(2, 4),   -- message_teacher
(2, 8),   -- create_announcement
(2, 11),  -- send_message
(2, 12);  -- view_reports

-- BDE permissions (Student + Announcement)
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(3, 1),   -- create_subject
(3, 6),   -- message_student
(3, 7),   -- message_teacher
(3, 8),   -- create_announcement
(3, 11);  -- send_message

-- CA permissions (Student + Announcement)
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(4, 1),   -- create_subject
(4, 6),   -- message_student
(4, 7),   -- message_teacher
(4, 8),   -- create_announcement
(4, 11);  -- send_message

-- Moderator permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(5, 9),   -- view_reports
(5, 10),  -- manage_reports
(5, 2),   -- edit_subject_mod
(5, 13);  -- delete_subject_mod

-- Admin permissions (all)
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(6, 1),   -- create_subject
(6, 2),   -- edit_subject
(6, 3),   -- delete_subject
(6, 4),   -- message_student
(6, 5),   -- message_teacher
(6, 6),   -- message_student (direct)
(6, 7),   -- message_teacher (direct)
(6, 8),   -- create_announcement
(6, 9),   -- view_reports
(6, 10),  -- manage_reports
(6, 11),  -- send_message
(6, 12),  -- edit_subject_mod
(6, 13);  -- delete_subject_mod

-- Add role_id foreign key to users table if not exists
ALTER TABLE `users` ADD COLUMN `role_id` INT DEFAULT 1;
ALTER TABLE `users` ADD FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE SET NULL;

-- Update existing users to have role_id based on role column
UPDATE `users` SET `role_id` = 
  CASE 
    WHEN `role` = 'student' THEN 1
    WHEN `role` = 'teacher' THEN 2
    WHEN `role` = 'moderator' THEN 5
    WHEN `role` = 'admin' THEN 6
    ELSE 1
  END;
