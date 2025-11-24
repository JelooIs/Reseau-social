<?php
require_once 'models/Message.php';

class HomeController {
    public function index() {
        session_start();
        $messageModel = new Message();

        // Suppression
        if (isset($_POST['delete']) && isset($_SESSION['user'])) {
            $messageModel->delete($_POST['message_id']);
        }

        // Modification
        if (isset($_POST['edit']) && isset($_SESSION['user'])) {
            $msg = htmlspecialchars($_POST['message']);
            $image = null;
            if (!empty($_FILES['image']['name'])) {
                $image = 'uploads/' . uniqid() . '_' . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], $image);
            }
            $messageModel->update($_POST['message_id'], $msg, $image);
        }

        // Création
        if (isset($_POST['envoyer']) && isset($_SESSION['user'])) {
            $user_id = $_SESSION['user']['id'];
            $msg = htmlspecialchars($_POST['message']);
            $image = null;
            if (!empty($_FILES['image']['name'])) {
                $image = 'uploads/' . uniqid() . '_' . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], $image);
            }
            $messageModel->create($user_id, $msg, $image);
        }

        $limit = 10;
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $limit;

        $messages = $messageModel->all($limit, $offset);
        $totalMessages = $messageModel->count();
        $totalPages = ceil($totalMessages / $limit);

        require 'views/index.view.php';
    }
}

