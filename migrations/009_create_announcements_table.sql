-- 009_create_announcements_table.sql
CREATE TABLE IF NOT EXISTS announcements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    body TEXT NOT NULL,
    creator_id INTEGER NOT NULL,
    scope TEXT NOT NULL DEFAULT 'global', -- 'global' or 'subject'
    subject_id INTEGER DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Indexes
CREATE INDEX IF NOT EXISTS idx_announcements_scope ON announcements(scope);
CREATE INDEX IF NOT EXISTS idx_announcements_subject_id ON announcements(subject_id);
