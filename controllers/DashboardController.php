<?php
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Department.php';
require_once 'models/Project.php';
require_once 'models/Absence.php';

class DashboardController {
    private $db;
    private $user;
    private $department;
    private $project;
    private $absence;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
        $this->department = new Department($this->db);
        $this->project = new Project($this->db);
        $this->absence = new Absence($this->db);
    }

    public function getTotalUsers() {
        return $this->user->countAll();
    }

    public function getTotalDepartments() {
        return $this->department->countAll();
    }

    public function getTotalProjects() {
        return $this->project->countAll();
    }

    public function getTotalAbsences() {
        return $this->absence->countAll();
    }

    public function getBirthdaysToday() {
        return $this->user->getBirthdaysToday();
    }

    public function getBirthdaysTodayWithDepartment() {
        return $this->user->getBirthdaysTodayWithDepartment();
    }

    public function getDepartmentManagers() {
        return $this->user->getDepartmentManagers();
    }

    public function getEmployeeStatsByDepartment() {
        return $this->user->countByDepartment();
    }

    public function getEmployeeStatsByDepartmentWithName() {
        return $this->user->countByDepartmentWithName();
    }
}
?> 