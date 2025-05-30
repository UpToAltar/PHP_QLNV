<?php
require_once 'config/database.php';

class User {
    private $conn;
    private $table_name = "User";

    public $id;
    public $name;
    public $email;
    public $phone;
    public $position;
    public $dateIn;
    public $status;
    public $departmentId;
    public $role;
    public $createdAt;
    public $password;
    public $birthDay;
    public $isManager;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email AND password = :password";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET id=:id, name=:name, email=:email, phone=:phone, position=:position, 
                      dateIn=:dateIn, status=:status, departmentId=:departmentId, role=:role, 
                      createdAt=:createdAt, password=:password, birthDay=:birthDay, isManager=:isManager";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":position", $this->position);
        $stmt->bindParam(":dateIn", $this->dateIn);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":departmentId", $this->departmentId);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":createdAt", $this->createdAt);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":birthDay", $this->birthDay);
        $stmt->bindParam(":isManager", $this->isManager);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT u.*, d.Name as department_name 
                  FROM " . $this->table_name . " u 
                  LEFT JOIN Department d ON u.DepartmentId = d.Id 
                  ORDER BY u.createdAt DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($id) {
        $query = "SELECT u.*, d.Name as department_name FROM " . $this->table_name . " u LEFT JOIN Department d ON u.DepartmentId = d.Id WHERE u.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, email=:email, phone=:phone, position=:position, 
                      status=:status, departmentId=:departmentId, role=:role, 
                      birthDay=:birthDay, isManager=:isManager 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':position', $this->position);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':departmentId', $this->departmentId);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':birthDay', $this->birthDay);
        $stmt->bindParam(':isManager', $this->isManager);
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

    // Đếm tổng số nhân viên
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['total'] : 0;
    }

    // Lấy danh sách sinh nhật hôm nay
    public function getBirthdaysToday() {
        $query = "SELECT Name, DepartmentId FROM " . $this->table_name . " WHERE DATE_FORMAT(BirthDay, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thống kê số nhân viên theo phòng ban
    public function countByDepartment() {
        $query = "SELECT DepartmentId, COUNT(*) as total FROM " . $this->table_name . " GROUP BY DepartmentId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách sinh nhật hôm nay kèm tên phòng ban
    public function getBirthdaysTodayWithDepartment() {
        $query = "SELECT u.Id, u.Name, u.Position, u.BirthDay, d.Name as department_name 
                  FROM " . $this->table_name . " u 
                  LEFT JOIN Department d ON u.DepartmentId = d.Id 
                  WHERE DATE_FORMAT(u.BirthDay, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách trưởng phòng các phòng ban
    public function getDepartmentManagers() {
        $query = "SELECT u.Name, u.Position, d.Name as department_name 
                  FROM " . $this->table_name . " u 
                  LEFT JOIN Department d ON u.DepartmentId = d.Id 
                  WHERE u.isManager = 1 AND u.Status = 1 
                  ORDER BY d.Name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thống kê số nhân viên theo phòng ban kèm tên phòng ban
    public function countByDepartmentWithName() {
        $query = "SELECT d.Name as department_name, COUNT(u.Id) as total 
                  FROM Department d 
                  LEFT JOIN " . $this->table_name . " u ON d.Id = u.DepartmentId 
                  GROUP BY d.Id, d.Name 
                  ORDER BY d.Name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tìm kiếm, lọc, phân trang user
    public function searchAndFilter($search = '', $departmentId = '', $offset = 0, $limit = 10) {
        $query = "SELECT u.*, d.Name as department_name FROM " . $this->table_name . " u LEFT JOIN Department d ON u.DepartmentId = d.Id WHERE 1";
        $params = [];
        if ($search !== '') {
            $query .= " AND (u.Name LIKE :search OR u.Email LIKE :search OR u.Phone LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        if ($departmentId !== '' && $departmentId !== 'all') {
            $query .= " AND u.DepartmentId = :departmentId";
            $params[':departmentId'] = $departmentId;
        }
        $query .= " ORDER BY u.createdAt DESC LIMIT :offset, :limit";
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Đếm tổng số user cho tìm kiếm, lọc
    public function countSearchAndFilter($search = '', $departmentId = '') {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " u WHERE 1";
        $params = [];
        if ($search !== '') {
            $query .= " AND (u.Name LIKE :search OR u.Email LIKE :search OR u.Phone LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        if ($departmentId !== '' && $departmentId !== 'all') {
            $query .= " AND u.DepartmentId = :departmentId";
            $params[':departmentId'] = $departmentId;
        }
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['total'] : 0;
    }
}
?>
