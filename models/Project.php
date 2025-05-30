<?php
require_once 'config/database.php';

class Project {
    private $conn;
    private $table_name = "Project";

    public $id;
    public $projectName;
    public $description;
    public $startDate;
    public $endDate;
    public $status;
    public $managerId;
    public $createdAt;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (Id, ProjectName, Description, StartDate, EndDate, Status, ManagerId)
                VALUES
                (:id, :projectName, :description, :startDate, :endDate, :status, :managerId)";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->projectName = htmlspecialchars(strip_tags($this->projectName));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->startDate = htmlspecialchars(strip_tags($this->startDate));
        $this->endDate = htmlspecialchars(strip_tags($this->endDate));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->managerId = htmlspecialchars(strip_tags($this->managerId));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":projectName", $this->projectName);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":startDate", $this->startDate);
        $stmt->bindParam(":endDate", $this->endDate);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":managerId", $this->managerId);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll($offset = 0, $limit = 8, $status = '', $sortBy = '') {
        $query = "SELECT p.*, 
                        (SELECT COUNT(*) FROM ProjectMember WHERE ProjectId = p.Id) as total_members,
                        u.Name as manager_name
                 FROM " . $this->table_name . " p
                 LEFT JOIN User u ON p.ManagerId = u.Id
                 WHERE 1=1";

        if($status !== '' && $status !== null) {
            $query .= " AND p.Status = :status";
        }

        switch($sortBy) {
            case 'name_asc':
                $query .= " ORDER BY p.ProjectName ASC, p.StartDate DESC";
                break;
            case 'name_desc':
                $query .= " ORDER BY p.ProjectName DESC, p.StartDate DESC";
                break;
            case 'date_asc':
                $query .= " ORDER BY p.StartDate ASC, p.ProjectName ASC";
                break;
            case 'date_desc':
                $query .= " ORDER BY p.StartDate DESC, p.ProjectName ASC";
                break;
            default:
                $query .= " ORDER BY p.StartDate DESC, p.ProjectName ASC";
        }

        $query .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        
        if($status !== '' && $status !== null) {
            $stmt->bindParam(":status", $status);
        }
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    public function readOne() {
        $query = "SELECT p.*, 
                        (SELECT COUNT(*) FROM ProjectMember WHERE ProjectId = p.Id) as total_members,
                        u.Name as manager_name
                 FROM " . $this->table_name . " p
                 LEFT JOIN User u ON p.ManagerId = u.Id
                 WHERE p.Id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET ProjectName = :projectName,
                    Description = :description,
                    StartDate = :startDate,
                    EndDate = :endDate,
                    Status = :status,
                    ManagerId = :managerId
                WHERE Id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->projectName = htmlspecialchars(strip_tags($this->projectName));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->startDate = htmlspecialchars(strip_tags($this->startDate));
        $this->endDate = htmlspecialchars(strip_tags($this->endDate));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->managerId = htmlspecialchars(strip_tags($this->managerId));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":projectName", $this->projectName);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":startDate", $this->startDate);
        $stmt->bindParam(":endDate", $this->endDate);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":managerId", $this->managerId);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        // First delete all project members
        $query = "DELETE FROM ProjectMember WHERE ProjectId = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        // Then delete the project
        $query = "DELETE FROM " . $this->table_name . " WHERE Id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function countAll($status = '') {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " p WHERE 1=1";
        
        if($status !== '' && $status !== null) {
            $query .= " AND p.Status = :status";
        }

        $stmt = $this->conn->prepare($query);
        
        if($status !== '' && $status !== null) {
            $stmt->bindParam(":status", $status);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public static function getStatusName($status) {
        switch($status) {
            case 0:
                return "Mới tạo";
            case 1:
                return "Đang thực hiện";
            case 2:
                return "Hoàn thành";
            case 3:
                return "Hủy bỏ";
            default:
                return "Không xác định";
        }
    }
}
?>