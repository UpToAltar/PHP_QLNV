<?php include 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-user"></i> Thông tin nhân viên</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="users.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <strong>Thông tin cơ bản</strong>
            </div>
            <div class="card-body">
                <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($user['Name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['Email']); ?></p>
                <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($user['Phone']); ?></p>
                <p><strong>Chức vụ:</strong> <?php echo htmlspecialchars($user['Position']); ?></p>
                <p><strong>Phòng ban:</strong> <?php echo htmlspecialchars($user['department_name']); ?></p>
                <p><strong>Ngày vào làm:</strong> <?php echo !empty($user['DateIn']) ? date('d/m/Y', strtotime($user['DateIn'])) : '-'; ?></p>
                <p><strong>Trạng thái:</strong> <?php echo $user['Status'] == 1 ? '<span class="badge bg-success">Hoạt động</span>' : '<span class="badge bg-danger">Không hoạt động</span>'; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <strong>Bảng lương các tháng</strong>
            </div>
            <div class="card-body">
                <?php if(!empty($salaries)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Tháng</th>
                                <th>Lương cơ bản</th>
                                <th>Tổng thưởng</th>
                                <th>Tổng lương</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($salaries as $salary): ?>
                            <tr>
                                <td><?php echo date('m/Y', strtotime($salary['Month'])); ?></td>
                                <td><?php echo number_format($salary['BaseSalary'], 0, ',', '.'); ?> VNĐ</td>
                                <td><?php echo number_format($salary['total_bonus'], 0, ',', '.'); ?> VNĐ</td>
                                <td><?php echo number_format($salary['BaseSalary'] + $salary['total_bonus'], 0, ',', '.'); ?> VNĐ</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">Chưa có dữ liệu lương.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <strong>Dự án đã tham gia</strong>
            </div>
            <div class="card-body">
                <?php if(!empty($projects)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Tên dự án</th>
                                <th>Mô tả</th>
                                <th>Thời gian</th>
                                <th>Vai trò</th>
                                <th>Ngày tham gia</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($projects as $project): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($project['ProjectName']); ?></td>
                                <td><?php echo htmlspecialchars($project['Description']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($project['StartDate'])); ?> - <?php echo date('d/m/Y', strtotime($project['EndDate'])); ?></td>
                                <td><?php echo htmlspecialchars($project['Role'] ?? ''); ?></td>
                                <td><?php echo isset($project['DateJoin']) ? date('d/m/Y H:i', strtotime($project['DateJoin'])) : '-'; ?></td>
                                <td><?php echo Project::getStatusName($project['Status']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">Chưa tham gia dự án nào.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?> 