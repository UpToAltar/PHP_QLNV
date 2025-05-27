<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'controllers/AuthController.php';
require_once 'controllers/DashboardController.php';

$auth = new AuthController();
$auth->checkAuth();

$dashboard = new DashboardController();
$totalUsers = $dashboard->getTotalUsers();
$totalDepartments = $dashboard->getTotalDepartments();
$totalProjects = $dashboard->getTotalProjects();
$totalAbsences = $dashboard->getTotalAbsences();
$birthdaysToday = $dashboard->getBirthdaysTodayWithDepartment();
$departmentManagers = $dashboard->getDepartmentManagers();
$employeeStats = $dashboard->getEmployeeStatsByDepartmentWithName();

include 'views/layouts/header.php';
?>

<!-- Thống kê dashboard -->
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo $totalUsers; ?></h4>
                        <p class="mb-0">Tổng nhân viên</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo $totalDepartments; ?></h4>
                        <p class="mb-0">Phòng ban</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-building fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo $totalProjects; ?></h4>
                        <p class="mb-0">Dự án</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-project-diagram fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo $totalAbsences; ?></h4>
                        <p class="mb-0">Đơn nghỉ phép</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-times fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Thống kê nhân viên theo phòng ban</h5>
            </div>
            <div class="card-body">
                <canvas id="departmentChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-birthday-cake"></i> Sinh nhật hôm nay</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php if (count($birthdaysToday) > 0): ?>
                        <?php foreach($birthdaysToday as $birthday): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo htmlspecialchars($birthday['Name']); ?></strong>
                                    <br><small class="text-muted">Phòng: <?php echo htmlspecialchars($birthday['department_name']); ?></small>
                                    <?php if (!empty($birthday['Position'])): ?>
                                        <br><small class="text-muted">Chức vụ: <?php echo htmlspecialchars($birthday['Position']); ?></small>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-primary rounded-pill">🎂</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted">
                            <i class="fas fa-calendar-day mb-2" style="font-size: 2rem;"></i>
                            <p>Không có sinh nhật hôm nay</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Trưởng phòng các phòng ban (dưới cùng) -->
<div class="card mb-4 mt-4">
    <div class="card-header">
        <h5><i class="fas fa-user-tie"></i> Trưởng phòng các phòng ban</h5>
    </div>
    <div class="card-body">
        <div class="row g-2">
            <?php if (count($departmentManagers) > 0): ?>
                <?php foreach($departmentManagers as $manager): ?>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                        <div class="d-flex align-items-center p-2 bg-light rounded shadow-sm w-100" style="min-height: 70px;">
                            <div class="flex-shrink-0 me-2">
                                <i class="fas fa-user-tie fa-lg text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <strong class="fs-6"><?php echo htmlspecialchars($manager['Name']); ?></strong>
                                <div class="text-muted small">Phòng: <?php echo htmlspecialchars($manager['department_name']); ?></div>
                                <?php if (!empty($manager['Position'])): ?>
                                    <div class="text-muted small">Chức vụ: <?php echo htmlspecialchars($manager['Position']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-muted">Chưa có trưởng phòng nào</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const departmentData = <?php echo json_encode($employeeStats); ?>;
// Tách tên phòng ban và số lượng nhân viên
const labels = departmentData.map(item => item.department_name);
const data = departmentData.map(item => item.total);
// Tạo màu ngẫu nhiên cho từng cột
function getRandomColor() {
    const r = Math.floor(Math.random() * 200 + 30);
    const g = Math.floor(Math.random() * 200 + 30);
    const b = Math.floor(Math.random() * 200 + 30);
    return `rgba(${r}, ${g}, ${b}, 0.7)`;
}
const backgroundColors = labels.map(() => getRandomColor());
const borderColors = backgroundColors.map(color => color.replace('0.7', '1'));

const ctx = document.getElementById('departmentChart').getContext('2d');
const departmentChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Số nhân viên',
            data: data,
            backgroundColor: backgroundColors,
            borderColor: borderColors,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                stepSize: 1
            }
        }
    }
});
</script>

<?php include 'views/layouts/footer.php'; ?>
