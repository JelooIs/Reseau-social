<?php
require_once __DIR__ . '/../src/Infrastructure/PrivateMessage/PrivateMessageRepositoryAdapter.php';
require_once __DIR__ . '/../src/UseCase/PrivateMessage/SendPrivateMessageUseCase.php';
require_once __DIR__ . '/../src/UseCase/PrivateMessage/EditPrivateMessageUseCase.php';
require_once 'models/User.php';

class PrivateMessageController {
    public function inbox() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); } 
        if (!isset($_SESSION['user'])) {
            header('Location: index.php');
            exit();
        }

        $repo = new PrivateMessageRepositoryAdapter();
        $sendUseCase = new SendPrivateMessageUseCase($repo);
        $editPmUseCase = new EditPrivateMessageUseCase($repo);
        $userModel = new User();

        // send message
        if (isset($_POST['send_pm'])) {
            $to = intval($_POST['to_user']);
            $text = trim($_POST['pm_message']);
            if ($text !== '') {
                $sendUseCase->execute($_SESSION['user']['id'], $to, $text);
                header('Location: index.php?action=pm&with=' . $to);
                exit();
            }
        }

        // edit private message
        if (isset($_POST['edit_pm']) && isset($_POST['pm_id']) && isset($_POST['pm_message_edit'])) {
            $pm_id = intval($_POST['pm_id']);
            $new_msg = trim($_POST['pm_message_edit']);
            if ($new_msg !== '') {
                // Only sender or admin can edit
                $user_id = $_SESSION['user']['id'];
                $is_admin = isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
                // Fetch message to check sender
                $msg = null;
                $withParam = isset($_GET['with']) ? intval($_GET['with']) : 0;
                foreach ($repo->messagesBetween($user_id, $withParam, 1000, 0) as $m) {
                    if ($m['id'] == $pm_id) {
                        $msg = $m;
                        break;
                    }
                }
                if ($msg && ($msg['sender_id'] == $user_id || $is_admin)) {
                    $editUserId = $is_admin ? $msg['sender_id'] : $user_id;
                    $editPmUseCase->execute($pm_id, $editUserId, $new_msg);
                }
                header('Location: index.php?action=pm&with=' . (isset($_GET['with']) ? intval($_GET['with']) : ''));
                exit();
            }
        }

        $threads = $repo->threadsForUser($_SESSION['user']['id']);

        // open specific thread
        $with = isset($_GET['with']) ? intval($_GET['with']) : null;
        $messages = [];
        $partner = null;
        if ($with) {
            $messages = $repo->messagesBetween($_SESSION['user']['id'], $with, 100, 0);
            $partner = $userModel->findById($with);
        }

        require 'views/pm_inbox.view.php';
    }
}
