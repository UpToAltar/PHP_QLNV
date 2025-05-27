<?php
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Department.php';

class UserController {
    private $db;
    private $user;
    private $department;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
        $this->department = new Department($this->db);
    }

    public function index() {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $departmentId = isset($_GET['department']) ? $_GET['department'] : '';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $stmt = $this->user->searchAndFilter($search, $departmentId, $offset, $limit);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalUsers = $this->user->countSearchAndFilter($search, $departmentId);
        $totalPages = ceil($totalUsers / $limit);

        $stmtDept = $this->department->readAll();
        $departments = $stmtDept->fetchAll(PDO::FETCH_ASSOC);

        include 'views/users/index.php';
    }

    public function create() {
        if($_POST) {
            $this->user->id = uniqid();
            $this->user->name = $_POST['name'];
            $this->user->email = $_POST['email'];
            $this->user->phone = $_POST['phone'];
            $this->user->position = $_POST['position'];
            $this->user->dateIn = $_POST['dateIn'];
            $this->user->status = $_POST['status'];
            $this->user->departmentId = $_POST['departmentId'];
            $this->user->role = $_POST['role'];
            $this->user->password = $_POST['password'];
            $this->user->birthDay = $_POST['birthDay'];
            $this->user->isManager = isset($_POST['isManager']) ? 1 : 0;
            $this->user->createdAt = date('Y-m-d H:i:s');

            if($this->user->create()) {
                $_SESSION['message'] = 'Thêm nhân viên thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: users.php");
                exit();
            } else {
                $_SESSION['message'] = 'Có lỗi xảy ra khi tạo nhân viên!';
                $_SESSION['message_type'] = 'error';
                header("Location: users.php");
                exit();
            }
        }
        
        $stmt = $this->department->readAll();
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include 'views/users/create.php';
    }

    public function edit($id) {
        $user = $this->user->readOne($id);

        if($_POST) {
            $this->user->id = $id;
            $this->user->name = $_POST['name'];
            $this->user->email = $user['Email'];
            $this->user->phone = $_POST['phone'];
            $this->user->position = $_POST['position'];
            $this->user->status = $_POST['status'];
            $this->user->departmentId = $_POST['departmentId'];
            $this->user->role = $_POST['role'];
            $this->user->birthDay = $_POST['birthDay'];
            $this->user->isManager = isset($_POST['isManager']) ? 1 : 0;
            $this->user->dateIn = !empty($_POST['dateIn']) ? $_POST['dateIn'] : $user['DateIn'];
            if (!empty($_POST['password'])) {
                $this->user->password = $_POST['password'];
            } else {
                $this->user->password = $user['Password'];
            }

            if($this->user->update()) {
                $_SESSION['message'] = 'Cập nhật nhân viên thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: users.php");
                exit();
            } else {
                $_SESSION['message'] = 'Có lỗi xảy ra khi cập nhật nhân viên!';
                $_SESSION['message_type'] = 'error';
                header("Location: users.php");
                exit();
            }
        }

        $stmt = $this->department->readAll();
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include 'views/users/edit.php';
    }

    public function delete($id) {
        $this->user->id = $id;
        if($this->user->delete()) {
            $_SESSION['message'] = 'Xóa nhân viên thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: users.php");
        } else {
            $_SESSION['message'] = 'Có lỗi xảy ra khi xóa nhân viên!';
            $_SESSION['message_type'] = 'error';
            header("Location: users.php");
        }
        exit();
    }
}
?>
