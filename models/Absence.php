<?php
require_once 'config/database.php';

class Absence {
    private $conn;
    private $table_name = "Absence";

    public $id;
    public $startDate;
    public $endDate;
    public $reason;
    public $status;
    public $userId;
    public $approvedBy;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET id=:id, startDate=:startDate, endDate=:endDate, 
                      reason=:reason, status=:status, userId=:userId";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->reason = htmlspecialchars(strip_tags($this->reason));
        $this->status = 0; // Mặc định là chờ xác nhận

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":startDate", $this->startDate);
        $stmt->bindParam(":endDate", $this->endDate);
        $stmt->bindParam(":reason", $this->reason);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":userId", $this->userId);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll($offset = 0, $limit = 10) {
        $query = "SELECT a.*, u.Name as user_name, u.Position as user_position,
                         d.Name as department_name, m.Name as approved_by_name
                  FROM " . $this->table_name . " a
                  LEFT JOIN User u ON a.UserId = u.Id
                  LEFT JOIN Department d ON u.DepartmentId = d.Id
                  LEFT JOIN User m ON a.ApprovedBy = m.Id
                  ORDER BY a.StartDate DESC
                  LIMIT :offset, :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($id) {
        $query = "SELECT a.*, u.Name as user_name, u.Position as user_position,
                         d.Name as department_name, m.Name as approved_by_name
                  FROM " . $this->table_name . " a
                  LEFT JOIN User u ON a.UserId = u.Id
                  LEFT JOIN Department d ON u.DepartmentId = d.Id
                  LEFT JOIN User m ON a.ApprovedBy = m.Id
                  WHERE a.id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET startDate=:startDate, endDate=:endDate, 
                      reason=:reason, status=:status, approvedBy=:approvedBy 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->reason = htmlspecialchars(strip_tags($this->reason));

        $stmt->bindParam(':startDate', $this->startDate);
        $stmt->bindParam(':endDate', $this->endDate);
        $stmt->bindParam(':reason', $this->reason);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':approvedBy', $this->approvedBy);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Đếm tổng số đơn nghỉ phép
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['total'] : 0;
    }

    // Duyệt đơn nghỉ phép
    public function approve($approvedBy) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status=1, approvedBy=:approvedBy 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':approvedBy', $approvedBy);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Từ chối đơn nghỉ phép
    public function reject($approvedBy) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status=2, approvedBy=:approvedBy 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':approvedBy', $approvedBy);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Lấy danh sách đơn nghỉ phép của một nhân viên
    public function getByUser($userId, $offset = 0, $limit = 10) {
        $query = "SELECT a.*, u.Name as user_name, u.Position as user_position,
                         d.Name as department_name, m.Name as approved_by_name
                  FROM " . $this->table_name . " a
                  LEFT JOIN User u ON a.UserId = u.Id
                  LEFT JOIN Department d ON u.DepartmentId = d.Id
                  LEFT JOIN User m ON a.ApprovedBy = m.Id
                  WHERE a.UserId = :userId
                  ORDER BY a.StartDate DESC
                  LIMIT :offset, :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Đếm số đơn nghỉ phép của một nhân viên
    public function countByUser($userId) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE UserId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $userId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['total'] : 0;
    }
}
?> 