<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } 

require_once 'controllers/HomeController.php';
require_once 'controllers/RegisterController.php';
require_once 'controllers/LoginController.php';
require_once 'controllers/LogoutController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/PrivateMessageController.php';
require_once 'controllers/SubjectController.php';
require_once 'controllers/ReportController.php';
require_once 'controllers/AdminReportController.php';

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
else {
    $controller = new HomeController();
    $controller->index();
}
?>
