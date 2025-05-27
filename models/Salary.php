<?php
require_once 'config/database.php';

class Salary {
    private $conn;
    private $table_name = "Salary";

    public $id;
    public $userId;
    public $month;
    public $baseSalary;
    public $createdAt;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (Id, UserId, Month, BaseSalary)
                VALUES
                (:id, :userId, :month, :baseSalary)";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->userId = htmlspecialchars(strip_tags($this->userId));
        $this->month = htmlspecialchars(strip_tags($this->month));
        $this->baseSalary = htmlspecialchars(strip_tags($this->baseSalary));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":userId", $this->userId);
        $stmt->bindParam(":month", $this->month);
        $stmt->bindParam(":baseSalary", $this->baseSalary);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll($offset = 0, $limit = 8, $userId = '', $sortBy = '') {
        $query = "SELECT s.*, u.Name as user_name, u.Position as user_position, 
                        d.Name as department_name,
                        (SELECT SUM(Amount) FROM Bonus WHERE SalaryId = s.Id) as total_bonus
                 FROM " . $this->table_name . " s
                 LEFT JOIN User u ON s.UserId = u.Id
                 LEFT JOIN Department d ON u.DepartmentId = d.Id
                 WHERE 1=1";

        // Thêm điều kiện tìm kiếm theo nhân viên
        if(!empty($userId)) {
            $query .= " AND s.UserId = :userId";
        }

        // Thêm điều kiện sắp xếp
        switch($sortBy) {
            case 'salary_asc':
                $query .= " ORDER BY s.BaseSalary ASC, s.Month DESC, u.Name ASC";
                break;
            case 'salary_desc':
                $query .= " ORDER BY s.BaseSalary DESC, s.Month DESC, u.Name ASC";
                break;
            case 'total_asc':
                $query .= " ORDER BY (s.BaseSalary + COALESCE((SELECT SUM(Amount) FROM Bonus WHERE SalaryId = s.Id), 0)) ASC, s.Month DESC, u.Name ASC";
                break;
            case 'total_desc':
                $query .= " ORDER BY (s.BaseSalary + COALESCE((SELECT SUM(Amount) FROM Bonus WHERE SalaryId = s.Id), 0)) DESC, s.Month DESC, u.Name ASC";
                break;
            default:
                $query .= " ORDER BY s.Month DESC, u.Name ASC";
        }

        $query .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        
        if(!empty($userId)) {
            $stmt->bindParam(":userId", $userId);
        }
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    public function readOne() {
        $query = "SELECT s.*, u.Name as user_name, u.Position as user_position, 
                        d.Name as department_name,
                        (SELECT SUM(Amount) FROM Bonus WHERE SalaryId = s.Id) as total_bonus
                 FROM " . $this->table_name . " s
                 LEFT JOIN User u ON s.UserId = u.Id
                 LEFT JOIN Department d ON u.DepartmentId = d.Id
                 WHERE s.Id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET UserId = :userId,
                    Month = :month,
                    BaseSalary = :baseSalary
                WHERE Id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->userId = htmlspecialchars(strip_tags($this->userId));
        $this->month = htmlspecialchars(strip_tags($this->month));
        $this->baseSalary = htmlspecialchars(strip_tags($this->baseSalary));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":userId", $this->userId);
        $stmt->bindParam(":month", $this->month);
        $stmt->bindParam(":baseSalary", $this->baseSalary);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM Bonus WHERE SalaryId = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        $query = "DELETE FROM " . $this->table_name . " WHERE Id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function countAll($userId = '') {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " s WHERE 1=1";
        
        if(!empty($userId)) {
            $query .= " AND s.UserId = :userId";
        }

        $stmt = $this->conn->prepare($query);
        
        if(!empty($userId)) {
            $stmt->bindParam(":userId", $userId);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getBonuses() {
        $query = "SELECT * FROM Bonus WHERE SalaryId = :id ORDER BY CreatedAt DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        return $stmt;
    }

    public function addBonus($description, $amount) {
        $query = "INSERT INTO Bonus (Id, SalaryId, Description, Amount)
                 VALUES (:id, :salaryId, :description, :amount)";

        $stmt = $this->conn->prepare($query);

        $bonusId = uniqid();
        $stmt->bindParam(":id", $bonusId);
        $stmt->bindParam(":salaryId", $this->id);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":amount", $amount);

        return $stmt->execute();
    }

    public function deleteBonus($bonusId) {
        $query = "DELETE FROM Bonus WHERE Id = :id AND SalaryId = :salaryId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $bonusId);
        $stmt->bindParam(":salaryId", $this->id);
        return $stmt->execute();
    }

    public function existsForMonth() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . "
                 WHERE UserId = :userId AND Month = :month";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":userId", $this->userId);
        $stmt->bindParam(":month", $this->month);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] > 0;
    }

    public function getByUser($userId) {
        $query = "SELECT s.*, u.Name as user_name, u.Position as user_position, 
                        d.Name as department_name,
                        (SELECT SUM(Amount) FROM Bonus WHERE SalaryId = s.Id) as total_bonus
                 FROM " . $this->table_name . " s
                 LEFT JOIN User u ON s.UserId = u.Id
                 LEFT JOIN Department d ON u.DepartmentId = d.Id
                 WHERE s.UserId = :userId
                 ORDER BY s.Month DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":userId", $userId);
        $stmt->execute();

        return $stmt;
    }
}
?>
