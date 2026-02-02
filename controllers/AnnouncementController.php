<?php
require_once __DIR__ . '/../models/Announcement.php';
require_once __DIR__ . '/../models/Subject.php';
require_once __DIR__ . '/../models/User.php';

class AnnouncementController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        $annModel = new Announcement();
        $subjectModel = new Subject();

        $announcements = [];
        if (isset($_SESSION['user'])) {
            $announcements = $annModel->allVisibleToUser($_SESSION['user']['id']);
        } else {
            $announcements = $annModel->allGlobal();
        }

        // Provide subjects to allow professors to create subject-scoped announcements
        $subjects = $subjectModel->all(100, 0);

        require 'views/announcements.view.php';
    }

    public function create() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user'])) {
            header('Location: index.php');
            exit();
        }

        $annModel = new Announcement();
        $subjectModel = new Subject();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $body = trim($_POST['body'] ?? '');
            $scope = ($_POST['scope'] ?? 'global');
            $subject_id = isset($_POST['subject_id']) && $_POST['subject_id'] !== '' ? intval($_POST['subject_id']) : null;

            if ($title === '' || $body === '') {
                $_SESSION['flash_message'] = 'Titre et contenu requis pour l\'annonce.';
                $_SESSION['flash_type'] = 'danger';
                header('Location: index.php?action=announcements');
                exit();
            }

            // Permission: global only admin, subject announcements only subject owner or admin
            $userRole = $_SESSION['user']['role'] ?? 'user';
            $isAdmin = $userRole === 'admin';

            if ($scope === 'global' && !$isAdmin) {
                $_SESSION['flash_message'] = 'Seul un administrateur peut publier une annonce globale.';
                $_SESSION['flash_type'] = 'danger';
                header('Location: index.php?action=announcements');
                exit();
            }

            if ($scope === 'subject' && $subject_id !== null) {
                $subject = $subjectModel->findById($subject_id);
                if (!$isAdmin && $subject && $subject['user_id'] != $_SESSION['user']['id']) {
                    $_SESSION['flash_message'] = 'Vous n\'êtes pas autorisé à publier une annonce pour cette matière.';
                    $_SESSION['flash_type'] = 'danger';
                    header('Location: index.php?action=announcements');
                    exit();
                }
            }

            $annModel->create($_SESSION['user']['id'], $title, $body, $scope, $subject_id);
            $_SESSION['flash_message'] = 'Annonce publiée.';
            $_SESSION['flash_type'] = 'success';
            header('Location: index.php?action=announcements');
            exit();
        }

        // Non-POST fallback
        header('Location: index.php?action=announcements');
        exit();
    }
}
