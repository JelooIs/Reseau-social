<?php 
require_once 'models/User.php';
require_once 'models/Message.php';

class AdminController {
    public function admin() {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php');
            exit();
        }

        $userModel = new User();
        $messageModel = new Message();
        $users = $userModel->all();
        $messages = $messageModel->all();

        if (isset($_POST['delete_user'])) {
            $userId = intval($_POST['user_id']);
            if ($userModel->delete($userId)) {
                header('Location: index.php?action=admin');
                exit();
            } else {
                $error = "Erreur lors de la suppression de l'utilisateur.";
            }
        }

        if (isset($_POST['delete_message'])) {
            $messageId = intval($_POST['message_id']);
            if ($messageModel->delete($messageId)) {
                header('Location: index.php?action=admin');
                exit();
            } else {
                $error = "Erreur lors de la suppression du message.";
            }
        }

        require 'views/admin.view.php';
    }
}