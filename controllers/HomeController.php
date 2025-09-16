<?php
require_once 'models/Message.php';

class HomeController {
    public function index() {
        session_start();
        $messageModel = new Message();
        $message = '';

        if (isset($_POST['envoyer']) && isset($_SESSION['user'])) {
            if (!empty($_POST['message'])) {
                $user_id = $_SESSION['user']['id'];
                $msg = htmlspecialchars($_POST['message']);
                $messageModel->create($user_id, $msg);
            }
        }

        $messages = $messageModel->all();

        require 'views/index.view.php';
    }
}
