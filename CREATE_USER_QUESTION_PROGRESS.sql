-- =====================================================
-- Migration: Create user_question_progress table
-- Feature: "2x richtig in Folge" System
-- Datum: 2025-10-15
-- =====================================================

-- 1. Erstelle die neue Tabelle
CREATE TABLE `user_question_progress` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `question_id` BIGINT UNSIGNED NOT NULL,
    `consecutive_correct` INT NOT NULL DEFAULT 0 COMMENT 'Anzahl richtiger Antworten in Folge (0, 1, 2+)',
    `last_answered_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    
    -- Unique constraint: Pro User nur ein Fortschritt pro Frage
    UNIQUE KEY `user_question_progress_user_id_question_id_unique` (`user_id`, `question_id`),
    
    -- Foreign Keys
    CONSTRAINT `user_question_progress_user_id_foreign` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) 
        ON DELETE CASCADE,
    
    CONSTRAINT `user_question_progress_question_id_foreign` 
        FOREIGN KEY (`question_id`) 
        REFERENCES `questions` (`id`) 
        ON DELETE CASCADE,
    
    -- Indizes für Performance
    KEY `user_question_progress_user_id_consecutive_correct_index` (`user_id`, `consecutive_correct`),
    KEY `user_question_progress_last_answered_at_index` (`last_answered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Migriere bestehende solved_questions (diese sind bereits gemeistert)
INSERT INTO `user_question_progress` (`user_id`, `question_id`, `consecutive_correct`, `last_answered_at`, `created_at`, `updated_at`)
SELECT 
    u.id AS user_id,
    j.question_id,
    2 AS consecutive_correct, -- Als gemeistert markieren
    NOW() AS last_answered_at,
    NOW() AS created_at,
    NOW() AS updated_at
FROM `users` u
CROSS JOIN JSON_TABLE(
    IFNULL(u.solved_questions, '[]'),
    '$[*]' COLUMNS(question_id INT PATH '$')
) j
WHERE u.solved_questions IS NOT NULL
  AND JSON_LENGTH(u.solved_questions) > 0
ON DUPLICATE KEY UPDATE 
    `consecutive_correct` = VALUES(`consecutive_correct`),
    `updated_at` = NOW();

-- 3. Migriere bestehende exam_failed_questions (diese brauchen noch 2x richtig)
INSERT INTO `user_question_progress` (`user_id`, `question_id`, `consecutive_correct`, `last_answered_at`, `created_at`, `updated_at`)
SELECT 
    u.id AS user_id,
    j.question_id,
    0 AS consecutive_correct, -- Noch nicht richtig beantwortet
    NOW() AS last_answered_at,
    NOW() AS created_at,
    NOW() AS updated_at
FROM `users` u
CROSS JOIN JSON_TABLE(
    IFNULL(u.exam_failed_questions, '[]'),
    '$[*]' COLUMNS(question_id INT PATH '$')
) j
WHERE u.exam_failed_questions IS NOT NULL
  AND JSON_LENGTH(u.exam_failed_questions) > 0
  AND NOT EXISTS (
    -- Nur hinzufügen, wenn nicht bereits als "solved" migriert
    SELECT 1 FROM `user_question_progress` uqp
    WHERE uqp.user_id = u.id 
      AND uqp.question_id = j.question_id
  )
ON DUPLICATE KEY UPDATE 
    `last_answered_at` = NOW(),
    `updated_at` = NOW();

-- =====================================================
-- Verification Queries (zum Testen)
-- =====================================================

-- Prüfe ob Tabelle existiert
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    CREATE_TIME
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'user_question_progress';

-- Zeige Anzahl migrierter Einträge
SELECT 
    COUNT(*) as total_entries,
    SUM(CASE WHEN consecutive_correct >= 2 THEN 1 ELSE 0 END) as mastered,
    SUM(CASE WHEN consecutive_correct = 1 THEN 1 ELSE 0 END) as one_correct,
    SUM(CASE WHEN consecutive_correct = 0 THEN 1 ELSE 0 END) as failed
FROM user_question_progress;

-- Zeige Fortschritt pro User
SELECT 
    u.id,
    u.name,
    COUNT(uqp.id) as questions_tracked,
    SUM(CASE WHEN uqp.consecutive_correct >= 2 THEN 1 ELSE 0 END) as mastered,
    SUM(CASE WHEN uqp.consecutive_correct = 1 THEN 1 ELSE 0 END) as one_correct,
    SUM(CASE WHEN uqp.consecutive_correct = 0 THEN 1 ELSE 0 END) as failed
FROM users u
LEFT JOIN user_question_progress uqp ON u.id = uqp.user_id
GROUP BY u.id, u.name
ORDER BY questions_tracked DESC
LIMIT 10;

-- =====================================================
-- Rollback (falls nötig)
-- =====================================================

-- VORSICHT: Löscht die komplette Tabelle und alle Daten!
-- DROP TABLE IF EXISTS `user_question_progress`;

