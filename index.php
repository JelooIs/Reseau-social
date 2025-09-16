<?php
require_once 'controllers/HomeController.php';
require_once 'controllers/RegisterController.php';

if (isset($_GET['action']) && $_GET['action'] === 'register') {
    $controller = new RegisterController();
    $controller->register();
    exit();
} else {
    $controller = new HomeController();
    $controller->index();
}
?>
