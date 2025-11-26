<?php
require_once 'models/Report.php';
require_once 'models/Subject.php';
require_once 'models/Comment.php';

class ReportController {
    public function create() {
        if (!isset($_SESSION['user'])) {
            $_SESSION['flash_message'] = 'Vous devez être connecté pour signaler un contenu.';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        if (!isset($_POST['report_type']) || !isset($_POST['report_target_id']) || !isset($_POST['report_reason'])) {
            $_SESSION['flash_message'] = 'Données de rapport invalides.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $reportModel = new Report();
        $type = $_POST['report_type'];
        $targetId = intval($_POST['report_target_id']);
        $reason = htmlspecialchars(trim($_POST['report_reason']));

        // Validate type
        if (!in_array($type, ['subject', 'comment'])) {
            $_SESSION['flash_message'] = 'Type de rapport invalide.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Validate target exists
        if ($type === 'subject') {
            $subjectModel = new Subject();
            $target = $subjectModel->findById($targetId);
            if (!$target) {
                $_SESSION['flash_message'] = 'Le sujet n\'existe pas.';
                $_SESSION['flash_type'] = 'danger';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
        } elseif ($type === 'comment') {
            $commentModel = new Comment();
            $target = $commentModel->findById($targetId);
            if (!$target) {
                $_SESSION['flash_message'] = 'Le commentaire n\'existe pas.';
                $_SESSION['flash_type'] = 'danger';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
        }

        // Check if user already reported this
        if ($reportModel->hasReported($_SESSION['user']['id'], $type, $targetId)) {
            $_SESSION['flash_message'] = 'Vous avez déjà signalé ce contenu.';
            $_SESSION['flash_type'] = 'info';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Validate reason length
        if (strlen($reason) < 10) {
            $_SESSION['flash_message'] = 'La raison doit contenir au moins 10 caractères.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Create report
        if ($reportModel->create($_SESSION['user']['id'], $type, $targetId, $reason)) {
            $_SESSION['flash_message'] = 'Merci! Votre signalement a été enregistré. Notre équipe de modération l\'examinera bientôt.';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Une erreur est survenue lors de l\'enregistrement du rapport.';
            $_SESSION['flash_type'] = 'danger';
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>
