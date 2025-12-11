<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } 

// Load user preferences if logged in
if (isset($_SESSION['user']) && !isset($_SESSION['user_preferences'])) {
    require_once 'models/UserPreferences.php';
    $prefsModel = new UserPreferences();
    $_SESSION['user_preferences'] = $prefsModel->getPreferences($_SESSION['user']['id']);
}

// Store color preferences for inline style generation
$_SESSION['color_styles'] = '';
if (isset($_SESSION['user_preferences'])) {
    $primary = htmlspecialchars($_SESSION['user_preferences']['primary_color'] ?? '#0d6efd', ENT_QUOTES, 'UTF-8');
    $secondary = htmlspecialchars($_SESSION['user_preferences']['secondary_color'] ?? '#6c757d', ENT_QUOTES, 'UTF-8');
    $accent = htmlspecialchars($_SESSION['user_preferences']['accent_color'] ?? '#198754', ENT_QUOTES, 'UTF-8');
    
    $_SESSION['color_styles'] = ":root { --primary-color: {$primary}; --secondary-color: {$secondary}; --accent-color: {$accent}; }";
}

require_once 'controllers/HomeController.php';
require_once 'controllers/RegisterController.php';
require_once 'controllers/LoginController.php';
require_once 'controllers/LogoutController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/PrivateMessageController.php';
require_once 'controllers/SubjectController.php';
require_once 'controllers/ReportController.php';
require_once 'controllers/AdminReportController.php';
require_once 'controllers/SettingsController.php';

if (isset($_GET['action']) && $_GET['action'] === 'register') {
    $controller = new RegisterController();
    $controller->register();
    exit();
}
if (isset($_GET['action']) && $_GET['action'] === 'login') {
    $controller = new LoginController();
    $controller->login();
    exit();
}
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $controller = new LogoutController();
    $controller->logout();
    exit();
}
if (isset($_GET['action']) && $_GET['action'] === 'admin') {
    $controller = new AdminController();
    $controller->admin();
    exit();
}
if (isset($_GET['action']) && $_GET['action'] === 'pm') {
    $controller = new PrivateMessageController();
    $controller->inbox();
    exit();
}
if (isset($_GET['action']) && $_GET['action'] === 'subject') {
    $controller = new SubjectController();
    if (isset($_GET['id'])) {
        $controller->detail();
    } else {
        $controller->index();
    }
    exit();
}
if (isset($_GET['action']) && $_GET['action'] === 'report') {
    $controller = new ReportController();
    $controller->create();
    exit();
}
if (isset($_GET['action']) && $_GET['action'] === 'reports') {
    $controller = new AdminReportController();
    $controller->reports();
    exit();
}
if (isset($_GET['action']) && $_GET['action'] === 'settings') {
    $controller = new SettingsController();
    $controller->settings();
    exit();
}
else {
    // Default landing: subjects catalog
    $controller = new SubjectController();
    $controller->index();
}
?>
