<?php
Class RegisterController {
    public function register() {
        $userModel = new User();
        $message = '';

        if (isset($_POST['register'])) {
            $nom = htmlspecialchars($_POST['nom']);
            $prenoms = htmlspecialchars($_POST['prenoms']);
            $email = htmlspecialchars($_POST['email']);
            $password = $_POST['password'];

            if ($userModel->findByEmail($email)) {
                $message = "Cet email existe déjà.";
            } else {
                if ($userModel->create($nom, $prenoms, $email, $password)) {
                    header('Location: index.php?showLogin=1');
                    exit();
                } else {
                    $message = "Erreur lors de l'inscription.";
                }
            }
        }

        require 'views/register.view.php';
    }
}