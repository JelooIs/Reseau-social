<?php
require_once 'models/Subject.php';
require_once 'models/Comment.php';
require_once 'models/PrivateMessage.php';

class SubjectController {
    public function index() {
        $subjectModel = new Subject();

        // Handle subject creation
        if (isset($_POST['create_subject']) && isset($_SESSION['user'])) {
            $title = htmlspecialchars($_POST['subject_title']);
            $image = null;

            if (!empty($_FILES['subject_image']['name'])) {
                $image = 'uploads/' . uniqid() . '_' . basename($_FILES['subject_image']['name']);
                move_uploaded_file($_FILES['subject_image']['tmp_name'], $image);
            }

            $subjectModel->create($_SESSION['user']['id'], $title, $image);
            header('Location: index.php?action=subject');
            exit();
        }

        // Handle subject deletion (admin only)
        if (isset($_POST['delete_subject']) && isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
            $subjectId = intval($_POST['subject_id']);
            $subject = $subjectModel->findById($subjectId);
            
            if ($subject) {
                $reason = htmlspecialchars(trim($_POST['deletion_reason'] ?? ''));
                
                // Send private message to subject creator
                $pmModel = new PrivateMessage();
                $adminName = htmlspecialchars($_SESSION['user']['pseudo'] ?? 'Administrateur');
                $subjectTitle = htmlspecialchars($subject['title']);
                
                $notificationMessage = "Notification: Votre sujet \"$subjectTitle\" a été supprimé par un administrateur.\n\n";
                if ($reason !== '') {
                    $notificationMessage .= "Raison: $reason";
                } else {
                    $notificationMessage .= "Raison: Non spécifiée.";
                }
                
                // Get admin user id (from session)
                $pmModel->send($_SESSION['user']['id'], $subject['user_id'], $notificationMessage);
                
                // Delete the subject
                $subjectModel->delete($subjectId);
                $_SESSION['flash_message'] = 'Le sujet a été supprimé et l\'utilisateur a été notifié.';
                $_SESSION['flash_type'] = 'success';
            }
            
            header('Location: index.php?action=subject');
            exit();
        }

        // Load subjects with pagination
        $limit = 12;
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $limit;

        $subjects = $subjectModel->all($limit, $offset);
        $totalSubjects = $subjectModel->count();
        $totalPages = ceil($totalSubjects / $limit);

        require 'views/subjects.view.php';
    }

    public function detail() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?action=subject');
            exit();
        }

        $subjectId = intval($_GET['id']);
        $subjectModel = new Subject();
        $subject = $subjectModel->findById($subjectId);

        if (!$subject) {
            header('Location: index.php?action=subject');
            exit();
        }

        // Comments handling
        $commentModel = new Comment();

        // Create comment
        if (isset($_POST['post_comment']) && isset($_SESSION['user'])) {
            $message = trim($_POST['comment_message']);
            if ($message !== '') {
                $commentModel->create($subjectId, $_SESSION['user']['id'], htmlspecialchars($message));
                $_SESSION['flash_message'] = 'Votre commentaire a été posté avec succès.';
                $_SESSION['flash_type'] = 'success';
            }
            header('Location: index.php?action=subject&id=' . $subjectId . '#comments');
            exit();
        }

        // Delete comment (admin or owner)
        if (isset($_POST['delete_comment']) && isset($_SESSION['user'])) {
            $commentId = intval($_POST['comment_id']);
            $comment = $commentModel->findById($commentId);
            if ($comment) {
                if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['id'] == $comment['user_id']) {
                    $commentModel->delete($commentId);
                    $_SESSION['flash_message'] = 'Le commentaire a été supprimé.';
                    $_SESSION['flash_type'] = 'success';
                }
            }
            header('Location: index.php?action=subject&id=' . $subjectId . '#comments');
            exit();
        }

        // Pagination for comments
        $cLimit = 50;
        $cPage = isset($_GET['cpage']) ? max(1, intval($_GET['cpage'])) : 1;
        $cOffset = ($cPage - 1) * $cLimit;

        $comments = $commentModel->allForSubject($subjectId, $cLimit, $cOffset);
        $totalComments = $commentModel->countForSubject($subjectId);
        $totalCommentPages = ($totalComments > 0) ? ceil($totalComments / $cLimit) : 1;

        require 'views/subject_detail.view.php';
    }
}
