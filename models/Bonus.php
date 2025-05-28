<?php
require_once 'config/database.php';

class Bonus {
    private $conn;
    private $table_name = "Bonus";

    public $id;
    public $salaryId;
    public $description;
    public $amount;
    public $type;
    public $createdAt;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (Id, SalaryId, Description, Amount, Type)
                VALUES
                (:id, :salaryId, :description, :amount, :type)";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->salaryId = htmlspecialchars(strip_tags($this->salaryId));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->type = htmlspecialchars(strip_tags($this->type));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":salaryId", $this->salaryId);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":type", $this->type);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getBySalaryId($salaryId) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE SalaryId = :salaryId 
                 ORDER BY CreatedAt DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":salaryId", $salaryId);
        $stmt->execute();

        return $stmt;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE Id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function hasBirthdayBonus($userId, $year) {
        $query = "SELECT COUNT(*) as total 
                 FROM " . $this->table_name . " b
                 JOIN Salary s ON b.SalaryId = s.Id
                 WHERE s.UserId = :userId 
                 AND b.Type = 1 
                 AND YEAR(s.Month) = :year";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":userId", $userId);
        $stmt->bindParam(":year", $year);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] > 0;
    }

    public static function getTypeName($type) {
        switch($type) {
            case 0:
                return "Thưởng tùy chọn";
            case 1:
                return "Thưởng sinh nhật";
            case 2:
                return "Thưởng lễ, tết";
            default:
                return "Không xác định";
        }
    }
}
?> 