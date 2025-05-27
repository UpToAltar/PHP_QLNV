<?php
require_once 'config/database.php';
require_once 'models/Salary.php';
require_once 'models/User.php';

class SalaryController {
    private $db;
    private $salary;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->salary = new Salary($this->db);
        $this->user = new User($this->db);
    }

    public function index() {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 6;
        $offset = ($page - 1) * $limit;

        // Lấy tham số tìm kiếm và sắp xếp
        $userId = isset($_GET['userId']) ? $_GET['userId'] : '';
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : '';

        $stmt = $this->salary->readAll($offset, $limit, $userId, $sortBy);
        $salaries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalSalaries = $this->salary->countAll($userId);
        $totalPages = ceil($totalSalaries / $limit);

        // Lấy danh sách nhân viên cho select box
        $stmt = $this->user->readAll(0, 1000);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include 'views/salaries/index.php';
    }

    public function create() {
        if($_POST) {
            $this->salary->id = uniqid();
            $this->salary->userId = $_POST['userId'];
            $this->salary->month = $_POST['month'];
            $this->salary->baseSalary = $_POST['baseSalary'];

            // Kiểm tra xem đã tồn tại lương của nhân viên trong tháng này chưa
            if($this->salary->existsForMonth()) {
                $_SESSION['message'] = 'Đã tồn tại lương của nhân viên này trong tháng này!';
                $_SESSION['message_type'] = 'error';
                header("Location: salaries.php");
                exit();
            }

            if($this->salary->create()) {
                $_SESSION['message'] = 'Tạo lương thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: salaries.php");
                exit();
            } else {
                $_SESSION['message'] = 'Có lỗi xảy ra khi tạo lương!';
                $_SESSION['message_type'] = 'error';
                header("Location: salaries.php");
                exit();
            }
        }
        
        // Lấy danh sách nhân viên
        $stmt = $this->user->readAll(0, 1000);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy lịch sử lương của nhân viên được chọn (nếu có)
        $userSalaries = [];
        if(isset($_GET['userId'])) {
            $stmt = $this->salary->getByUser($_GET['userId']);
            $userSalaries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        include 'views/salaries/form.php';
    }

    public function edit($id) {
        $this->salary->id = $id;
        $stmt = $this->salary->readOne();
        $salary = $stmt->fetch(PDO::FETCH_ASSOC);

        if($_POST) {
            $this->salary->id = $id;
            $this->salary->userId = $_POST['userId'];
            $this->salary->month = $_POST['month'];
            $this->salary->baseSalary = $_POST['baseSalary'];

            // Kiểm tra xem đã tồn tại lương của nhân viên trong tháng này chưa (trừ bản ghi hiện tại)
            if($this->salary->existsForMonth() && $salary['UserId'] != $_POST['userId']) {
                $_SESSION['message'] = 'Đã tồn tại lương của nhân viên này trong tháng này!';
                $_SESSION['message_type'] = 'error';
                header("Location: salaries.php");
                exit();
            }

            if($this->salary->update()) {
                $_SESSION['message'] = 'Cập nhật lương thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: salaries.php");
                exit();
            } else {
                $_SESSION['message'] = 'Có lỗi xảy ra khi cập nhật lương!';
                $_SESSION['message_type'] = 'error';
                header("Location: salaries.php");
                exit();
            }
        }

        // Lấy danh sách nhân viên
        $stmt = $this->user->readAll(0, 1000);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách thưởng
        $stmt = $this->salary->getBonuses();
        $bonuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include 'views/salaries/form.php';
    }

    public function delete($id) {
        $this->salary->id = $id;
        if($this->salary->delete()) {
            $_SESSION['message'] = 'Xóa lương thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: salaries.php");
        } else {
            $_SESSION['message'] = 'Có lỗi xảy ra khi xóa lương!';
            $_SESSION['message_type'] = 'error';
            header("Location: salaries.php");
        }
        exit();
    }

    public function view($id) {
        $this->salary->id = $id;
        $stmt = $this->salary->readOne();
        $salary = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$salary) {
            $_SESSION['message'] = 'Không tìm thấy bản ghi lương!';
            $_SESSION['message_type'] = 'error';
            header("Location: salaries.php");
            exit();
        }

        // Lấy danh sách nhân viên
        $stmt = $this->user->readAll(0, 1000);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách thưởng
        $stmt = $this->salary->getBonuses();
        $bonuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $_GET['view'] = true;
        include 'views/salaries/form.php';
    }

    public function addBonus() {
        if($_POST) {
            $this->salary->id = $_POST['salaryId'];
            if($this->salary->addBonus($_POST['description'], $_POST['amount'])) {
                $_SESSION['message'] = 'Thêm thưởng thành công!';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Có lỗi xảy ra khi thêm thưởng!';
                $_SESSION['message_type'] = 'error';
            }
            header("Location: salaries.php?action=edit&id=" . $_POST['salaryId']);
            exit();
        }
    }

    public function deleteBonus($bonusId, $salaryId) {
        $this->salary->id = $salaryId;
        if($this->salary->deleteBonus($bonusId)) {
            $_SESSION['message'] = 'Xóa thưởng thành công!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Có lỗi xảy ra khi xóa thưởng!';
            $_SESSION['message_type'] = 'error';
        }
        header("Location: salaries.php?action=edit&id=" . $salaryId);
        exit();
    }
}
?> 