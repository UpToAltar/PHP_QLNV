<?php
session_start();
require_once 'controllers/AuthController.php';
require_once 'controllers/UserController.php';

$auth = new AuthController();
$auth->checkAuth();

$userController = new UserController();

$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch($action) {
    case 'create':
        $userController->create();
        break;
    case 'edit':
        $id = $_GET['id'];
        $userController->edit($id);
        break;
    case 'delete':
        $id = $_GET['id'];
        $userController->delete($id);
        break;
    default:
        $userController->index();
        break;
}
?>
