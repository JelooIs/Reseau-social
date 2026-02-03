<?php
// Migration runner for announcements (compatible with CLI and web)
require_once 'config/database.php';

$confirmed = false;
if (PHP_SAPI === 'cli') {
    global $argv;
    $confirmed = isset($argv[1]) && ($argv[1] === '1' || $argv[1] === 'confirm');
} else {
    $confirmed = isset($_GET['confirm']) && $_GET['confirm'] === '1';
}

if (!$confirmed) {
    die('Migration requires confirmation. Use CLI arg "1" or ?confirm=1 in the URL.');
}

try {
    $database = new Database();
    $pdo = $database->pdo;

    $stmts = [];
    $stmts[] = "CREATE TABLE IF NOT EXISTS announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title TEXT NOT NULL,
        body TEXT NOT NULL,
        creator_id INT NOT NULL,
        scope VARCHAR(20) NOT NULL DEFAULT 'global',
        subject_id INT DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $stmts[] = "CREATE INDEX IF NOT EXISTS idx_announcements_scope ON announcements(scope);";
    $stmts[] = "CREATE INDEX IF NOT EXISTS idx_announcements_subject_id ON announcements(subject_id);";

    foreach ($stmts as $sql) {
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            // Try without IF NOT EXISTS for index in case MySQL version doesn't support it
            if (stripos($sql, 'CREATE INDEX IF NOT EXISTS') !== false) {
                $sql2 = str_ireplace('IF NOT EXISTS ', '', $sql);
                try { $pdo->exec($sql2); } catch (PDOException $e2) { /* ignore */ }
            }
        }
    }

    echo "✅ Migration 009 executed (announcements table).\n";
} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
}
