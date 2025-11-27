<?php
// Basic bootstrap for tests - create an in-memory SQLite DB and required tables
$vendor = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendor)) {
    require_once $vendor;
} else {
    // Provide a tiny PHPUnit TestCase stub for static analysis when vendor isn't installed
    if (file_exists(__DIR__ . '/_stubs/phpunit_stub.php')) {
        require_once __DIR__ . '/_stubs/phpunit_stub.php';
    }
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create users table
$pdo->exec(
    "CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE,
        password TEXT,
        pseudo TEXT,
        role TEXT
    );"
);

// Create messages table
$pdo->exec(
    "CREATE TABLE messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        message TEXT,
        image TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );"
);

// Create private_messages table (minimal)
$pdo->exec(
    "CREATE TABLE private_messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        sender_id INTEGER,
        receiver_id INTEGER,
        message TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );"
);

// Expose $pdo to tests by global variable and simple getter
global $TEST_PDO;
$TEST_PDO = $pdo;

function get_test_pdo() {
    global $TEST_PDO;
    return $TEST_PDO;
}
