-- Tạo database
CREATE DATABASE employee_management;
USE employee_management;

-- Bảng Department
CREATE TABLE Department (
    Id CHAR(50) PRIMARY KEY,
    Name VARCHAR(255),
    Budget DECIMAL(15,2),
    Description VARCHAR(255)
);

-- Bảng User
CREATE TABLE User (
    Id CHAR(50) PRIMARY KEY,
    Name VARCHAR(255),
    Email VARCHAR(255),
    Phone VARCHAR(255),
    Position VARCHAR(255),
    DateIn DATETIME,
    Status INT,
    DepartmentId CHAR(50),
    Role INT,
    CreatedAt DATETIME,
    Password VARCHAR(255),
    BirthDay DATE,
    isManager TINYINT(1) DEFAULT 0,
    
    FOREIGN KEY (DepartmentId) REFERENCES Department(Id)
);

-- Bảng Project
CREATE TABLE Project (
    Id CHAR(50) PRIMARY KEY,
    ProjectName VARCHAR(255),
    StartDate DATETIME,
    EndDate DATETIME,
    Description VARCHAR(255),
    Status INT,
    ManagerId CHAR(50),
    CreatedAt DATETIME,
    
    FOREIGN KEY (ManagerId) REFERENCES User(Id)
);

-- Bảng ProjectMember
CREATE TABLE ProjectMember (
    Id CHAR(50) PRIMARY KEY,
    ProjectId CHAR(50),
    UserId CHAR(50),
    DateJoin DATETIME,
    Role VARCHAR(255),
    
    FOREIGN KEY (ProjectId) REFERENCES Project(Id),
    FOREIGN KEY (UserId) REFERENCES User(Id)
);

-- Bảng Absence
CREATE TABLE Absence (
    Id CHAR(50) PRIMARY KEY,
    StartDate DATE,
    EndDate DATE,
    Reason VARCHAR(255),
    Status INT,
    UserId CHAR(50),
    ApprovedBy CHAR(50),
    
    FOREIGN KEY (UserId) REFERENCES User(Id),
    FOREIGN KEY (ApprovedBy) REFERENCES User(Id)
);

-- Bảng Salary
CREATE TABLE Salary (
    Id CHAR(50) PRIMARY KEY,
    UserId CHAR(50),
    Month DATE,
    BaseSalary DECIMAL(15,2),
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (UserId) REFERENCES User(Id)
);

-- Bảng Bonus
CREATE TABLE Bonus (
    Id CHAR(50) PRIMARY KEY,
    SalaryId CHAR(50),
    Description VARCHAR(255),
    Amount DECIMAL(15,2),
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (SalaryId) REFERENCES Salary(Id)
);

-- Dữ liệu mẫu
INSERT INTO Department (Id, Name, Budget, Description) VALUES
('dept1', 'Phòng IT', 1000000000, 'Phòng công nghệ thông tin'),
('dept2', 'Phòng HR', 500000000, 'Phòng nhân sự'),
('dept3', 'Phòng Kế toán', 300000000, 'Phòng kế toán tài chính');

INSERT INTO User (Id, Name, Email, Phone, Position, DateIn, Status, DepartmentId, Role, CreatedAt, Password, BirthDay, isManager) VALUES
('user1', 'Admin', 'admin@company.com', '0123456789', 'Quản trị viên', '2023-01-01', 1, 'dept1', 3, NOW(), MD5('123456'), '1990-01-01', 1);
