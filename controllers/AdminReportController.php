<?php
require_once 'models/Report.php';

class AdminReportController {
    public function reports() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php');
            exit();
        }

        $reportModel = new Report();
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $reports = $reportModel->all($limit, $offset, $status);
        $totalReports = $reportModel->count($status);
        $totalPages = ($totalReports > 0) ? ceil($totalReports / $limit) : 1;

        // Handle resolve/dismiss
        if (isset($_POST['resolve_report']) && isset($_POST['report_id'])) {
            $adminNote = isset($_POST['admin_note']) ? trim($_POST['admin_note']) : null;
            $reportModel->updateStatus(intval($_POST['report_id']), 'resolved', $adminNote);
            $_SESSION['flash_message'] = 'Signalement marqué comme résolu.';
            $_SESSION['flash_type'] = 'success';
            header('Location: index.php?action=reports');
            exit();
        }
        if (isset($_POST['dismiss_report']) && isset($_POST['report_id'])) {
            $adminNote = isset($_POST['admin_note']) ? trim($_POST['admin_note']) : null;
            $reportModel->updateStatus(intval($_POST['report_id']), 'dismissed', $adminNote);
            $_SESSION['flash_message'] = 'Signalement rejeté.';
            $_SESSION['flash_type'] = 'secondary';
            header('Location: index.php?action=reports');
            exit();
        }

        require 'views/reports.view.php';
    }
}
