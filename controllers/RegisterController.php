<?php
Class RegisterController {
    public function register() {
        if (isset($_POST['register'])) {
            if (!empty($_POST['nom']) && !empty($_POST['prenoms']) && !empty($_POST['email']) && !empty($_POST['password'])) {
                $nom = htmlspecialchars($_POST['nom']);
                $prenoms = htmlspecialchars($_POST['prenoms']);
                $email = htmlspecialchars($_POST['email']);
                $password = htmlspecialchars($_POST['password']);

                $userModel = new User();
                if ($userModel->findByEmail($email)) {
                    $error = "Email déjà utilisé.";
                } else {
                    $userModel->create($nom, $prenoms, $email, $password);
                    header('Location: login.php');
                    exit();
                }
            } else {
                $error = "Tous les champs sont requis.";
            }
        }

        require 'views/register.view.php';
    }
}