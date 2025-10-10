-- Migration: user_id zur question_statistics Tabelle hinzufügen
-- Datum: 2025-10-10

-- Spalte hinzufügen
ALTER TABLE `question_statistics` 
ADD COLUMN `user_id` BIGINT UNSIGNED NULL AFTER `question_id`;

-- Index hinzufügen
ALTER TABLE `question_statistics` 
ADD INDEX `question_statistics_user_id_index` (`user_id`);

-- Foreign Key Constraint hinzufügen
ALTER TABLE `question_statistics` 
ADD CONSTRAINT `question_statistics_user_id_foreign` 
FOREIGN KEY (`user_id`) 
REFERENCES `users` (`id`) 
ON DELETE SET NULL;

-- Rollback (falls nötig):
-- ALTER TABLE `question_statistics` DROP FOREIGN KEY `question_statistics_user_id_foreign`;
-- ALTER TABLE `question_statistics` DROP INDEX `question_statistics_user_id_index`;
-- ALTER TABLE `question_statistics` DROP COLUMN `user_id`;

