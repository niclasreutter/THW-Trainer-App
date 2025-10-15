-- =====================================================
-- Migration: Add inactive_reminder_sent_at to users table
-- Feature: Inaktivitäts-Erinnerungen
-- Datum: 2025-10-16
-- =====================================================

-- 1. Füge das neue Feld zur users Tabelle hinzu
ALTER TABLE `users` 
ADD COLUMN `inactive_reminder_sent_at` TIMESTAMP NULL 
AFTER `last_activity_date`;

-- 2. Füge einen Index hinzu für bessere Performance
CREATE INDEX `users_inactive_reminder_sent_at_index` 
ON `users` (`inactive_reminder_sent_at`);

-- =====================================================
-- Verification Queries (zum Testen)
-- =====================================================

-- Prüfe ob Spalte existiert
SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'users'
  AND COLUMN_NAME = 'inactive_reminder_sent_at';

-- Zeige alle User die seit 4+ Tagen inaktiv sind
SELECT 
    id,
    name,
    email,
    email_consent,
    last_activity_date,
    DATEDIFF(NOW(), last_activity_date) as days_inactive,
    inactive_reminder_sent_at
FROM users
WHERE email_consent = 1
  AND last_activity_date IS NOT NULL
  AND last_activity_date < DATE_SUB(NOW(), INTERVAL 4 DAY)
ORDER BY last_activity_date ASC
LIMIT 10;

-- Statistik: Wie viele User sind inaktiv?
SELECT 
    CASE 
        WHEN last_activity_date IS NULL THEN 'Nie aktiv'
        WHEN last_activity_date >= DATE_SUB(NOW(), INTERVAL 1 DAY) THEN 'Aktiv (heute)'
        WHEN last_activity_date >= DATE_SUB(NOW(), INTERVAL 4 DAY) THEN 'Aktiv (1-3 Tage)'
        WHEN last_activity_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 'Inaktiv (4-6 Tage)'
        WHEN last_activity_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 'Inaktiv (7-29 Tage)'
        ELSE 'Sehr inaktiv (30+ Tage)'
    END as activity_status,
    COUNT(*) as user_count,
    SUM(CASE WHEN email_consent = 1 THEN 1 ELSE 0 END) as with_email_consent
FROM users
GROUP BY activity_status
ORDER BY 
    CASE activity_status
        WHEN 'Aktiv (heute)' THEN 1
        WHEN 'Aktiv (1-3 Tage)' THEN 2
        WHEN 'Inaktiv (4-6 Tage)' THEN 3
        WHEN 'Inaktiv (7-29 Tage)' THEN 4
        WHEN 'Sehr inaktiv (30+ Tage)' THEN 5
        ELSE 6
    END;

-- =====================================================
-- Rollback (falls nötig)
-- =====================================================

-- VORSICHT: Löscht die Spalte und alle Daten!
-- ALTER TABLE `users` DROP COLUMN `inactive_reminder_sent_at`;
-- DROP INDEX `users_inactive_reminder_sent_at_index` ON `users`;

