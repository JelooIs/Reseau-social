<?php
require_once 'controllers/HomeController.php';
require_once 'controllers/RegisterController.php';
require_once 'controllers/LoginController.php';
require_once 'controllers/LogoutController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/PrivateMessageController.php';

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
else {
    $controller = new HomeController();
    $controller->index();
}
?>
