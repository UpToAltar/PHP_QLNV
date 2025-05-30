<?php
require_once 'config/database.php';

class ProjectMember {
    private $conn;
    private $table_name = "ProjectMember";

    public $id;
    public $projectId;
    public $userId;
    public $role;
    public $dateJoin;
    public $createdAt;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        if ($this->dateJoin) {
            $query = "INSERT INTO " . $this->table_name . "
                    (Id, ProjectId, UserId, Role, DateJoin)
                    VALUES
                    (:id, :projectId, :userId, :role, :dateJoin)";
        } else {
            $query = "INSERT INTO " . $this->table_name . "
                    (Id, ProjectId, UserId, Role)
                    VALUES
                    (:id, :projectId, :userId, :role)";
        }

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->projectId = htmlspecialchars(strip_tags($this->projectId));
        $this->userId = htmlspecialchars(strip_tags($this->userId));
        $this->role = htmlspecialchars(strip_tags($this->role));
        if ($this->dateJoin) {
            $this->dateJoin = htmlspecialchars(strip_tags($this->dateJoin));
        }

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":projectId", $this->projectId);
        $stmt->bindParam(":userId", $this->userId);
        $stmt->bindParam(":role", $this->role);
        if ($this->dateJoin) {
            $stmt->bindParam(":dateJoin", $this->dateJoin);
        }

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getByProjectId($projectId) {
        $query = "SELECT pm.*, u.Name as user_name, u.Position as user_position,
                        d.Name as department_name
                 FROM " . $this->table_name . " pm
                 LEFT JOIN User u ON pm.UserId = u.Id
                 LEFT JOIN Department d ON u.DepartmentId = d.Id
                 WHERE pm.ProjectId = :projectId
                 ORDER BY u.Name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":projectId", $projectId);
        $stmt->execute();

        return $stmt;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE Id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function deleteByProjectId($projectId) {
        $query = "DELETE FROM " . $this->table_name . " WHERE ProjectId = :projectId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":projectId", $projectId);
        return $stmt->execute();
    }

    public function getByUserId($userId) {
        $query = "SELECT pm.*, p.ProjectName, p.Description, p.StartDate, p.EndDate, p.Status, pm.Role, pm.DateJoin
                  FROM " . $this->table_name . " pm
                  LEFT JOIN Project p ON pm.ProjectId = p.Id
                  WHERE pm.UserId = :userId
                  ORDER BY p.StartDate DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":userId", $userId);
        $stmt->execute();
        return $stmt;
    }

    public static function getRoleName($role) {
        switch($role) {
            case 0:
                return "Thành viên";
            case 1:
                return "Trưởng nhóm";
            default:
                return "Không xác định";
        }
    }
}
?> 