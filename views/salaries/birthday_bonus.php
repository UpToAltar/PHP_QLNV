<?php include 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-birthday-cake"></i> Thưởng sinh nhật hôm nay</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="salaries.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-info"> <?php echo $message; ?> </div>
<?php endif; ?>

<form method="POST">
    <div class="card mb-4">
        <div class="card-body">
            <?php if (!empty($users)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Họ tên</th>
                            <th>Phòng ban</th>
                            <th>Chức vụ</th>
                            <th>Ngày sinh</th>
                            <th>Số tiền thưởng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['Name']); ?></td>
                            <td><?php echo htmlspecialchars($user['department_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($user['Position'] ?? ''); ?></td>
                            <td><?php echo !empty($user['BirthDay']) ? date('d/m/Y', strtotime($user['BirthDay'])) : '-'; ?></td>
                            <td>
                                <input type="number" class="form-control" name="bonus[<?php echo $user['Id'] ?? $user['id'] ?? ''; ?>]" min="0" placeholder="Nhập số tiền thưởng">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-gift"></i> Tạo thưởng
                </button>
            </div>
            <?php else: ?>
                <p class="text-muted">Không có nhân viên nào sinh nhật hôm nay.</p>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php include 'views/layouts/footer.php'; ?> 