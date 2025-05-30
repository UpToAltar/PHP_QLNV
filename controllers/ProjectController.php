<?php
require_once 'config/database.php';
require_once 'models/Project.php';
require_once 'models/ProjectMember.php';
require_once 'models/User.php';

class ProjectController {
    private $db;
    private $project;
    private $projectMember;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->project = new Project($this->db);
        $this->projectMember = new ProjectMember($this->db);
        $this->user = new User($this->db);
    }

    public function index() {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 6;
        $offset = ($page - 1) * $limit;

        // Lấy tham số tìm kiếm và sắp xếp
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : '';

        $stmt = $this->project->readAll($offset, $limit, $status, $sortBy);
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalProjects = $this->project->countAll($status);
        $totalPages = ceil($totalProjects / $limit);

        include 'views/projects/index.php';
    }

    public function create() {
        if($_POST) {
            $this->project->id = uniqid();
            $this->project->projectName = $_POST['name'];
            $this->project->description = $_POST['description'];
            $this->project->startDate = $_POST['startDate'];
            $this->project->endDate = $_POST['endDate'];
            $this->project->status = $_POST['status'];
            $this->project->managerId = $_POST['managerId'];

            if($this->project->create()) {
                // Thêm thành viên
                $managerAdded = false;
                if(isset($_POST['members']) && is_array($_POST['members'])) {
                    foreach($_POST['members'] as $member) {
                        $memberData = json_decode($member, true);
                        if (!$memberData) continue;
                        // Nếu userId trùng với managerId thì đánh dấu đã thêm
                        if ($memberData['userId'] == $this->project->managerId) {
                            $managerAdded = true;
                        }
                        $this->projectMember->id = uniqid();
                        $this->projectMember->projectId = $this->project->id;
                        $this->projectMember->userId = $memberData['userId'];
                        $this->projectMember->role = $memberData['role'];
                        $this->projectMember->dateJoin = isset($memberData['dateJoin']) ? $memberData['dateJoin'] : null;
                        $this->projectMember->create();
                    }
                }
                // Nếu chưa có manager trong danh sách thì tự động thêm
                if (!$managerAdded && !empty($this->project->managerId)) {
                    $this->projectMember->id = uniqid();
                    $this->projectMember->projectId = $this->project->id;
                    $this->projectMember->userId = $this->project->managerId;
                    $this->projectMember->role = 'Quản lí';
                    $this->projectMember->dateJoin = date('Y-m-d H:i:s');
                    $this->projectMember->create();
                }

                $_SESSION['message'] = 'Tạo dự án thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: projects.php");
                exit();
            } else {
                $_SESSION['message'] = 'Có lỗi xảy ra khi tạo dự án!';
                $_SESSION['message_type'] = 'error';
                header("Location: projects.php");
                exit();
            }
        }
        
        // Lấy danh sách nhân viên
        $stmt = $this->user->readAll(0, 1000);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include 'views/projects/form.php';
    }

    public function edit($id) {
        $this->project->id = $id;
        $stmt = $this->project->readOne();
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if($_POST) {
            $this->project->id = $id;
            $this->project->projectName = $_POST['name'];
            $this->project->description = $_POST['description'];
            $this->project->startDate = $_POST['startDate'];
            $this->project->endDate = $_POST['endDate'];
            $this->project->status = $_POST['status'];
            $this->project->managerId = $_POST['managerId'];

            if($this->project->update()) {
                // Xóa thành viên cũ
                $this->projectMember->deleteByProjectId($id);

                // Thêm thành viên mới
                if(isset($_POST['members']) && is_array($_POST['members'])) {
                    foreach($_POST['members'] as $member) {
                        $memberData = json_decode($member, true);
                        if (!$memberData) continue;
                        $this->projectMember->id = uniqid();
                        $this->projectMember->projectId = $id;
                        $this->projectMember->userId = $memberData['userId'];
                        $this->projectMember->role = $memberData['role'];
                        $this->projectMember->dateJoin = isset($memberData['dateJoin']) ? $memberData['dateJoin'] : null;
                        $this->projectMember->create();
                    }
                }

                $_SESSION['message'] = 'Cập nhật dự án thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: projects.php");
                exit();
            } else {
                $_SESSION['message'] = 'Có lỗi xảy ra khi cập nhật dự án!';
                $_SESSION['message_type'] = 'error';
                header("Location: projects.php");
                exit();
            }
        }

        // Lấy danh sách nhân viên
        $stmt = $this->user->readAll(0, 1000);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách thành viên dự án
        $stmt = $this->projectMember->getByProjectId($id);
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include 'views/projects/form.php';
    }

    public function delete($id) {
        $this->project->id = $id;
        if($this->project->delete()) {
            $_SESSION['message'] = 'Xóa dự án thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: projects.php");
        } else {
            $_SESSION['message'] = 'Có lỗi xảy ra khi xóa dự án!';
            $_SESSION['message_type'] = 'error';
            header("Location: projects.php");
        }
        exit();
    }
}
?> 