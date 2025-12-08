<?php
// Quick migration runner for database modifications
require_once 'config/database.php';

if (!isset($_GET['confirm']) || $_GET['confirm'] !== '1') {
    die('Migration requires confirmation. Add ?confirm=1 to the URL.');
}

try {
    $database = new Database();
    $pdo = $database->pdo;
    
    // Migration 008: Add 'message' type to reports table
    $sql = "ALTER TABLE `reports` 
            MODIFY COLUMN `type` ENUM('subject', 'comment', 'message') NOT NULL;";
    
    $pdo->exec($sql);
    
    echo "✅ Migration 008 executed successfully! The 'message' type has been added to reports table.";
} catch (PDOException $e) {
    echo "❌ Migration failed: " . htmlspecialchars($e->getMessage());
}
?>
