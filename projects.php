<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'controllers/ProjectController.php';

$controller = new ProjectController();

// Xử lý action
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'create':
        $controller->create();
        break;
    case 'edit':
        if (isset($_GET['id'])) {
            $controller->edit($_GET['id']);
        } else {
            header("Location: projects.php");
        }
        break;
    case 'update':
        if (isset($_GET['id'])) {
            $controller->edit($_GET['id']);
        } else {
            header("Location: projects.php");
        }
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            $controller->delete($_GET['id']);
        } else {
            header("Location: projects.php");
        }
        break;
    default:
        $controller->index();
        break;
}
?> 