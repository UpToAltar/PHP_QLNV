<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'controllers/SalaryController.php';

$controller = new SalaryController();

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
            header("Location: salaries.php");
        }
        break;
    case 'update':
        if (isset($_GET['id'])) {
            $controller->edit($_GET['id']);
        } else {
            header("Location: salaries.php");
        }
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            $controller->delete($_GET['id']);
        } else {
            header("Location: salaries.php");
        }
        break;
    case 'view':
        if (isset($_GET['id'])) {
            $controller->view($_GET['id']);
        } else {
            header("Location: salaries.php");
        }
        break;
    case 'addBonus':
        $controller->addBonus();
        break;
    case 'deleteBonus':
        if (isset($_GET['bonusId']) && isset($_GET['salaryId'])) {
            $controller->deleteBonus($_GET['bonusId'], $_GET['salaryId']);
        } else {
            header("Location: salaries.php");
        }
        break;
    case 'bulkCreate':
        $controller->bulkCreate();
        break;
    case 'birthdayBonus':
        $controller->birthdayBonus();
        break;
    default:
        $controller->index();
        break;
}
?> 