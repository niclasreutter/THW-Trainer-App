-- Migration: exam_statistics Foreign Key von CASCADE auf SET NULL ändern
-- Datum: 2025-10-10

-- 1. Bestehenden Foreign Key entfernen
ALTER TABLE `exam_statistics` 
DROP FOREIGN KEY `exam_statistics_user_id_foreign`;

-- 2. Neuen Foreign Key mit SET NULL hinzufügen
ALTER TABLE `exam_statistics` 
ADD CONSTRAINT `exam_statistics_user_id_foreign` 
FOREIGN KEY (`user_id`) 
REFERENCES `users` (`id`) 
ON DELETE SET NULL;

-- Rollback (falls nötig - zurück zu CASCADE):
-- ALTER TABLE `exam_statistics` DROP FOREIGN KEY `exam_statistics_user_id_foreign`;
-- ALTER TABLE `exam_statistics` ADD CONSTRAINT `exam_statistics_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

