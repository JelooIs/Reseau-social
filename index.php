<?php
require_once 'controllers/HomeController.php';
require_once 'controllers/RegisterController.php';
require_once 'controllers/LoginController.php';

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
else {
    $controller = new HomeController();
    $controller->index();
}
?>
