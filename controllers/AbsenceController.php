<?php
require_once 'config/database.php';
require_once 'models/Absence.php';
require_once 'models/User.php';

class AbsenceController {
    private $db;
    private $absence;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->absence = new Absence($this->db);
        $this->user = new User($this->db);
    }

    public function index() {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 8;
        $offset = ($page - 1) * $limit;

        $stmt = $this->absence->readAll($offset, $limit);
        $absences = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalAbsences = $this->absence->countAll();
        $totalPages = ceil($totalAbsences / $limit);

        include 'views/absences/index.php';
    }

    public function create() {
        if($_POST) {
            $this->absence->id = uniqid();
            $this->absence->startDate = $_POST['startDate'];
            $this->absence->endDate = $_POST['endDate'];
            $this->absence->reason = $_POST['reason'];
            $this->absence->userId = $_POST['userId'];

            if($this->absence->create()) {
                $_SESSION['message'] = 'Gửi đơn nghỉ phép thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: absences.php");
                exit();
            } else {
                $_SESSION['message'] = 'Có lỗi xảy ra khi gửi đơn nghỉ phép!';
                $_SESSION['message_type'] = 'error';
                header("Location: absences.php");
                exit();
            }
        }
        
        // Lấy danh sách nhân viên
        $stmt = $this->user->readAll(0, 1000);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include 'views/absences/form.php';
    }

    public function edit($id) {
        $absence = $this->absence->readOne($id);

        if($_POST) {
            $this->absence->id = $id;
            $this->absence->startDate = $_POST['startDate'];
            $this->absence->endDate = $_POST['endDate'];
            $this->absence->reason = $_POST['reason'];
            $this->absence->userId = $_POST['userId'];
            if(isset($_POST['status'])) {
                $this->absence->status = $_POST['status'];
            }

            if($this->absence->update()) {
                $_SESSION['message'] = 'Cập nhật đơn nghỉ phép thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: absences.php");
                exit();
            } else {
                $_SESSION['message'] = 'Có lỗi xảy ra khi cập nhật đơn nghỉ phép!';
                $_SESSION['message_type'] = 'error';
                header("Location: absences.php");
                exit();
            }
        }

        // Lấy danh sách nhân viên
        $stmt = $this->user->readAll(0, 1000);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include 'views/absences/form.php';
    }

    public function delete($id) {
        $this->absence->id = $id;
        if($this->absence->delete()) {
            $_SESSION['message'] = 'Xóa đơn nghỉ phép thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: absences.php");
        } else {
            $_SESSION['message'] = 'Có lỗi xảy ra khi xóa đơn nghỉ phép!';
            $_SESSION['message_type'] = 'error';
            header("Location: absences.php");
        }
        exit();
    }

    public function approve($id) {
        $this->absence->id = $id;
        if($this->absence->approve($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Duyệt đơn nghỉ phép thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: absences.php");
        } else {
            $_SESSION['message'] = 'Có lỗi xảy ra khi duyệt đơn nghỉ phép!';
            $_SESSION['message_type'] = 'error';
            header("Location: absences.php");
        }
        exit();
    }

    public function reject($id) {
        $this->absence->id = $id;
        if($this->absence->reject($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Từ chối đơn nghỉ phép thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: absences.php");
        } else {
            $_SESSION['message'] = 'Có lỗi xảy ra khi từ chối đơn nghỉ phép!';
            $_SESSION['message_type'] = 'error';
            header("Location: absences.php");
        }
        exit();
    }

    public function view($id) {
        $absence = $this->absence->readOne($id);
        if(!$absence) {
            $_SESSION['message'] = 'Không tìm thấy đơn nghỉ phép!';
            $_SESSION['message_type'] = 'error';
            header("Location: absences.php");
            exit();
        }

        // Lấy danh sách nhân viên
        $stmt = $this->user->readAll(0, 1000);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Thêm tham số view để form biết đang ở chế độ xem
        $_GET['view'] = true;
        
        include 'views/absences/form.php';
    }
}
?> 