<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';
require_once 'models/User.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function login() {
        if($_POST) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            $user_data = $this->user->login($email, $password);
            
            if($user_data) {
                $_SESSION['user_id'] = $user_data['Id'];
                $_SESSION['user_name'] = $user_data['Name'];
                $_SESSION['user_role'] = $user_data['Role'];
                $_SESSION['is_manager'] = $user_data['isManager'];
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Email hoặc mật khẩu không đúng!";
                include 'views/auth/login.php';
            }
        } else {
            include 'views/auth/login.php';
        }
    }

    public function logout() {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    public function checkAuth() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }
    }
}
?>
