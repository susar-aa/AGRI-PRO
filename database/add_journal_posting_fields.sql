-- SQL to add missing posting details columns to journal_entries table
ALTER TABLE journal_entries 
ADD COLUMN posted_at DATETIME DEFAULT NULL AFTER posted_by,
ADD COLUMN reversal_of_journal_id INT UNSIGNED DEFAULT NULL AFTER posted_at,
ADD COLUMN reversal_reason TEXT DEFAULT NULL AFTER reversal_of_journal_id;
