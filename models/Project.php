<?php
require_once 'config/database.php';

class Project {
    private $conn;
    private $table_name = "Project";

    public $id;
    public $projectName;
    public $startDate;
    public $endDate;
    public $description;
    public $status;
    public $managerId;
    public $createdAt;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET id=:id, projectName=:projectName, startDate=:startDate, 
                      endDate=:endDate, description=:description, status=:status, 
                      managerId=:managerId, createdAt=:createdAt";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->projectName = htmlspecialchars(strip_tags($this->projectName));
        $this->description = htmlspecialchars(strip_tags($this->description));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":projectName", $this->projectName);
        $stmt->bindParam(":startDate", $this->startDate);
        $stmt->bindParam(":endDate", $this->endDate);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":managerId", $this->managerId);
        $stmt->bindParam(":createdAt", $this->createdAt);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT p.*, u.Name as manager_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN User u ON p.ManagerId = u.Id 
                  ORDER BY p.createdAt DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT p.*, u.Name as manager_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN User u ON p.ManagerId = u.Id 
                  WHERE p.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->projectName = $row['ProjectName'];
            $this->startDate = $row['StartDate'];
            $this->endDate = $row['EndDate'];
            $this->description = $row['Description'];
            $this->status = $row['Status'];
            $this->managerId = $row['ManagerId'];
            $this->createdAt = $row['CreatedAt'];
        }
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET projectName=:projectName, startDate=:startDate, endDate=:endDate, 
                      description=:description, status=:status, managerId=:managerId 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':projectName', $this->projectName);
        $stmt->bindParam(':startDate', $this->startDate);
        $stmt->bindParam(':endDate', $this->endDate);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':managerId', $this->managerId);
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

    // Đếm tổng số dự án
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['total'] : 0;
    }
}
?>
