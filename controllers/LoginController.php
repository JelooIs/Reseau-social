<?php 
require_once 'models/User.php';

class LoginController {
    public function login() {
        if (isset($_POST['login'])) {
            if (!empty($_POST['email']) && !empty($_POST['password'])) {
                $email = htmlspecialchars($_POST['email']);
                $password = htmlspecialchars($_POST['password']);

                $userModel = new User();
                $user = $userModel->verify($email, $password);
                if ($user) {
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_nom'] = $user['nom'];
                    $_SESSION['user_prenoms'] = $user['prenoms'];
                    header('Location: index.php');
                    exit();
                } else {
                    $error = "Email ou mot de passe incorrect.";
                }
            } else {
                $error = "Tous les champs sont requis.";
            }
        }

        require 'views/login.view.php';
    }
}