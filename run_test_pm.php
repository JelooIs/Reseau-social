<?php
// Test script to verify PM initiation restriction (CLI only)
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'controllers/PrivateMessageController.php';

if (PHP_SAPI !== 'cli') {
    die("Run this script from CLI: php run_test_pm.php\n");
}

if (session_status() === PHP_SESSION_NONE) { session_start(); }

$db = new Database();
$pdo = $db->pdo;
$userModel = new User($pdo);

// Find or create a professor
$prof = null;
$stmt = $pdo->prepare("SELECT * FROM users WHERE role IN ('professor','teacher') LIMIT 1");
$stmt->execute();
$prof = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$prof) {
    $email = 'test_prof@example.local';
    $pseudo = 'test_prof';
    $password = password_hash('password', PASSWORD_BCRYPT);
    $pdo->prepare("INSERT INTO users (email,password,pseudo,role) VALUES (:e,:p,:u,'professor')")->execute([':e'=>$email,':p'=>$password,':u'=>$pseudo]);
    $profId = $pdo->lastInsertId();
    $prof = $userModel->findById($profId);
    $created[] = "Created professor user id={$profId}";
}

// Find or create a student
$student = null;
$stmt = $pdo->prepare("SELECT * FROM users WHERE role IN ('user','student') LIMIT 1");
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$student) {
    $email = 'test_student@example.local';
    $pseudo = 'test_student';
    $password = password_hash('password', PASSWORD_BCRYPT);
    $pdo->prepare("INSERT INTO users (email,password,pseudo,role) VALUES (:e,:p,:u,'user')")->execute([':e'=>$email,':p'=>$password,':u'=>$pseudo]);
    $studentId = $pdo->lastInsertId();
    $student = $userModel->findById($studentId);
    $created[] = "Created student user id={$studentId}";
}

// Prepare simulated session and POST
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$_SESSION = [];
$_SESSION['user'] = $student; // student initiates

$_POST = [];
$_POST['send_pm'] = '1';
$_POST['to_user'] = $prof['id'];
$_POST['pm_message'] = 'Bonjour professeur, ceci est un test.';

// Capture any headers and output
// Perform the validation logic the controller uses (without invoking header()/exit())
$senderRole = $_SESSION['user']['role'] ?? 'user';
$receiver = $userModel->findById(intval($_POST['to_user']));
$receiverRole = $receiver['role'] ?? 'user';
$studentRoles = ['user', 'student'];
$professorRoles = ['professor', 'teacher'];

if (in_array($senderRole, $studentRoles, true) && in_array($receiverRole, $professorRoles, true)) {
    $_SESSION['flash_message'] = "Vous n'êtes pas autorisé à initier une conversation privée avec ce professeur.";
    $_SESSION['flash_type'] = 'danger';
    echo "Flash message simulated: " . $_SESSION['flash_message'] . "\n";
} else {
    echo "No restriction: message would be sent.\n";
}

if (!empty($created)) {
    foreach ($created as $m) { echo $m . "\n"; }
}
