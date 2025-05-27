<?php
require_once 'config/database.php';
require_once 'models/Department.php';
require_once 'models/User.php';

class DepartmentController {
    private $db;
    private $department;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->department = new Department($this->db);
    }

    public function index() {
        $stmt = $this->department->readAll();
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include 'views/departments/index.php';
    }

    public function create() {
        if($_POST) {
            $this->department->id = uniqid();
            $this->department->name = $_POST['name'];
            $this->department->budget = $_POST['budget'];
            $this->department->description = $_POST['description'];

            if($this->department->create()) {
                $_SESSION['message'] = 'Thêm phòng ban thành công!';
                header("Location: departments.php");
                exit();
            } else {
                $error = "Có lỗi xảy ra khi tạo phòng ban!";
            }
        }
        
        include 'views/departments/create.php';
    }

    public function edit($id) {
        $department = $this->department->readOne($id);
        
        // Lấy danh sách nhân viên của phòng ban
        $user = new User($this->db);
        $stmt = $user->searchAndFilter('', $id, 0, 1000); // Lấy tất cả nhân viên của phòng ban
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($_POST) {
            $this->department->id = $id;
            $this->department->name = $_POST['name'];
            $this->department->budget = $_POST['budget'];
            $this->department->description = $_POST['description'];

            if($this->department->update()) {
                $_SESSION['message'] = 'Cập nhật phòng ban thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: departments.php");
                exit();
            } else {
                $_SESSION['message'] = 'Có lỗi xảy ra khi cập nhật phòng ban!';
                $_SESSION['message_type'] = 'error';
                header("Location: departments.php");
                exit();
            }
        }

        include 'views/departments/edit.php';
    }

    public function delete($id) {
        $this->department->id = $id;
        
        // Kiểm tra xem phòng ban có nhân viên không
        if($this->department->hasEmployees()) {
            $_SESSION['message'] = 'Không thể xóa phòng ban vì vẫn còn nhân viên!';
            $_SESSION['message_type'] = 'error';
            header("Location: departments.php");
            exit();
        }

        if($this->department->delete()) {
            $_SESSION['message'] = 'Xóa phòng ban thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: departments.php");
        } else {
            $_SESSION['message'] = 'Có lỗi xảy ra khi xóa phòng ban!';
            $_SESSION['message_type'] = 'error';
            header("Location: departments.php");
        }
        exit();
    }
}
?>
