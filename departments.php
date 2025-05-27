<?php
session_start();
require_once 'controllers/AuthController.php';
require_once 'controllers/DepartmentController.php';

$auth = new AuthController();
$auth->checkAuth();

$departmentController = new DepartmentController();

$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch($action) {
    case 'create':
        $departmentController->create();
        break;
    case 'edit':
        $id = $_GET['id'];
        $departmentController->edit($id);
        break;
    case 'delete':
        $id = $_GET['id'];
        $departmentController->delete($id);
        break;
    default:
        $departmentController->index();
        break;
}
?>
