<?php
require_once 'config/database.php';
require_once 'models/Salary.php';
require_once 'models/User.php';
require_once 'models/Bonus.php';

class SalaryController {
    private $db;
    private $salary;
    private $user;
    private $bonus;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->salary = new Salary($this->db);
        $this->user = new User($this->db);
        $this->bonus = new Bonus($this->db);
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
            $this->salary->month = date('Y-m-01', strtotime($_POST['month'] . '-01'));
            $this->salary->baseSalary = $_POST['baseSalary'];

            // Kiểm tra xem đã tồn tại lương của nhân viên trong tháng này chưa
            if($this->salary->existsForMonth()) {
                // Lấy thông tin nhân viên để hiển thị trong thông báo
                $stmt = $this->user->readOne($this->salary->userId);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $_SESSION['message'] = 'Nhân viên ' . htmlspecialchars($user['Name']) . 
                                     ' đã có bảng lương trong tháng ' . 
                                     date('m/Y', strtotime($this->salary->month)) . '!';
                $_SESSION['message_type'] = 'error';
                header("Location: salaries.php?action=create&userId=" . $this->salary->userId);
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
            $this->salary->month = $salary['Month'];
            $this->salary->baseSalary = $_POST['baseSalary'];

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
            
            // Kiểm tra nếu là thưởng sinh nhật
            if($_POST['type'] == 1) {
                // Lấy thông tin lương để biết userId
                $stmt = $this->salary->readOne();
                $salary = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Lấy năm hiện tại
                $currentYear = date('Y');
                
                // Kiểm tra xem đã có thưởng sinh nhật trong năm chưa
                if($this->bonus->hasBirthdayBonus($salary['UserId'], $currentYear)) {
                    $_SESSION['message'] = 'Nhân viên này đã được thưởng sinh nhật trong năm ' . $currentYear . '!';
                    $_SESSION['message_type'] = 'error';
                    header("Location: salaries.php?action=edit&id=" . $_POST['salaryId']);
                    exit();
                }
            }

            if($this->salary->addBonus($_POST['description'], $_POST['amount'], $_POST['type'])) {
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

    public function bulkCreate() {
        if($_POST) {
            // Kiểm tra nếu tháng đích đã qua
            $targetMonth = $_POST['targetMonth'];
            $currentMonth = date('Y-m');
            
            if($targetMonth < $currentMonth) {
                $_SESSION['message'] = 'Không thể tạo bảng lương cho tháng đã qua!';
                $_SESSION['message_type'] = 'error';
                header("Location: salaries.php?action=bulkCreate");
                exit();
            }
            
            $sourceMonth = date('Y-m-01', strtotime($_POST['sourceMonth'] . '-01'));
            $targetMonth = date('Y-m-01', strtotime($targetMonth . '-01'));
            
            // Lấy danh sách tất cả nhân viên
            $stmt = $this->user->readAll(0, 1000);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $successCount = 0;
            $skipExistsCount = 0; // Đếm số bảng lương đã tồn tại trong tháng đích
            $skipNoSourceCount = 0; // Đếm số nhân viên không có bảng lương tháng nguồn
            
            foreach($users as $user) {
                // Kiểm tra xem đã có bảng lương trong tháng đích chưa
                $this->salary->userId = $user['Id'];
                $this->salary->month = $targetMonth;
                $this->salary->id = uniqid();
                if($this->salary->existsForMonth()) {
                    $skipExistsCount++;
                    continue;
                }
                
                // Lấy bảng lương của tháng nguồn
                $this->salary->month = $sourceMonth;
                $stmt = $this->salary->getByUserAndMonth();
                $sourceSalary = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if(!$sourceSalary) {
                    $skipNoSourceCount++;
                    continue;
                }
                
                // Tạo bảng lương mới
                $this->salary->id = uniqid();
                $this->salary->userId = $user['Id'];
                $this->salary->month = $targetMonth;
                $this->salary->baseSalary = $sourceSalary['BaseSalary'];
                
                if($this->salary->create()) {
                    $successCount++;
                }
            }
            
            $_SESSION['message'] = "Đã tạo thành công $successCount bảng lương. " . 
                                 "Bỏ qua $skipExistsCount bảng lương đã tồn tại trong tháng đích. " .
                                 "Bỏ qua $skipNoSourceCount nhân viên không có bảng lương tháng nguồn.";
            $_SESSION['message_type'] = 'success';
            header("Location: salaries.php");
            exit();
        }
        
        include 'views/salaries/bulk_create.php';
    }
}
?> 