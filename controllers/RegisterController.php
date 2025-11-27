<?php
require_once 'models/User.php';

Class RegisterController {
    public function register() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (isset($_POST['register'])) {
            $userModel = new User();
            $pseudo = htmlspecialchars($_POST['pseudo']);
            $email = htmlspecialchars($_POST['email']);
            $password = $_POST['password'];

            if ($userModel->findByEmail($email)) {
                $_SESSION['register_error'] = "Cet email existe déjà.";
                header('Location: index.php?showRegister=1');
                exit();
            } else {
                if ($userModel->create($email, $password, $pseudo)) {
                    $_SESSION['flash_message'] = "Inscription réussie! Connectez-vous maintenant.";
                    $_SESSION['flash_type'] = 'success';
                    header('Location: index.php?showLogin=1');
                    exit();
                } else {
                    $_SESSION['register_error'] = "Erreur lors de l'inscription.";
                    header('Location: index.php?showRegister=1');
                    exit();
                }
            }
        }
    }
}