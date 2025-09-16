<?php
require_once 'controllers/HomeController.php';
require_once 'controllers/RegisterController.php';
require_once 'controllers/LoginController.php';
require_once 'controllers/LogoutController.php';

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
else {
    $controller = new HomeController();
    $controller->index();
}
?>
