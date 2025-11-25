<?php
require_once __DIR__ . '/../src/Infrastructure/Message/MessageRepositoryAdapter.php';
require_once __DIR__ . '/../src/UseCase/Message/CreateMessageUseCase.php';
require_once __DIR__ . '/../src/UseCase/Message/EditMessageUseCase.php';

class HomeController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); } 
        $repo = new MessageRepositoryAdapter();
        $createUseCase = new CreateMessageUseCase($repo);
        $editUseCase = new EditMessageUseCase($repo);

        // Suppression
        if (isset($_POST['delete']) && isset($_SESSION['user'])) {
            $repo->delete($_POST['message_id']);
        }

        // Modification
        if (isset($_POST['edit']) && isset($_SESSION['user'])) {
            $msg = htmlspecialchars($_POST['message']);
            $image = null;
            if (!empty($_FILES['image']['name'])) {
                $image = 'uploads/' . uniqid() . '_' . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], $image);
            }
            $editUseCase->execute($_POST['message_id'], $msg, $image);
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
            $createUseCase->execute($user_id, $msg, $image);
        }

        $limit = 10;
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $limit;

        $messages = $repo->all($limit, $offset);
        $totalMessages = $repo->count();
        $totalPages = ceil($totalMessages / $limit);

        require 'views/index.view.php';
    }
}

