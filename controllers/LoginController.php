<?php 
require_once 'models/User.php';

class LoginController {
    public function login() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (isset($_POST['login'])) {
            if (!empty($_POST['email']) && !empty($_POST['password'])) {
                $email = htmlspecialchars($_POST['email']);
                $password = htmlspecialchars($_POST['password']);

                $userModel = new User();
                $user = $userModel->verify($email, $password);
                if ($user) {
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'pseudo' => $user['pseudo'] ?? '',
                        'email' => $user['email'],
                        'role' => $user['role']
                    ];
                    header('Location: index.php');
                    exit();
                } else {
                    $_SESSION['login_error'] = "Email ou mot de passe incorrect.";
                    header('Location: index.php?showLogin=1');
                    exit();
                }
            } else {
                $_SESSION['login_error'] = "Tous les champs sont requis.";
                header('Location: index.php?showLogin=1');
                exit();
            }
        }
    }
}