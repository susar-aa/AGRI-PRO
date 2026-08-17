-- SQL to add missing posting_date column to journal_entries table
ALTER TABLE journal_entries ADD COLUMN posting_date DATE DEFAULT NULL AFTER transaction_date;
