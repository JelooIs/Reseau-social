<?php
require_once 'models/Message.php';

class HomeController {
    public function index() {
        $messageModel = new Message();
        $message = '';

        if (isset($_POST['envoyer'])) {
            if (!empty($_POST['nom']) && !empty($_POST['prenoms']) && !empty($_POST['message'])) {
                $nom = htmlspecialchars($_POST['nom']);
                $prenoms = htmlspecialchars($_POST['prenoms']);
                $msg = htmlspecialchars($_POST['message']);

                $messageModel->create($nom, $prenoms, $msg);
            }
        }

        $messages = $messageModel->all();

        // Charger la vue
        require 'views/index.view.php';
    }
}
