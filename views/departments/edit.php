<?php include 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-building"></i> Chỉnh sửa phòng ban</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="departments.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<?php
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<?php if(isset($message)): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i>
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if(isset($error)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Thông tin phòng ban</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên phòng ban *</label>
                        <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($department['Name']); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="budget" class="form-label">Ngân sách *</label>
                        <input type="number" class="form-control" id="budget" name="budget" required value="<?php echo htmlspecialchars($department['Budget']); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($department['Description']); ?></textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="departments.php" class="btn btn-secondary me-md-2">Hủy</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Danh sách nhân viên</h5>
                <?php if(empty($employees)): ?>
                    <p class="text-muted">Chưa có nhân viên nào trong phòng ban này.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th>Chức vụ</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($employees as $employee): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($employee['Name']); ?></strong>
                                                <?php if($employee['isManager']): ?>
                                                    <span class="badge bg-warning ms-1">Manager</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($employee['Position']); ?></td>
                                    <td>
                                        <?php if($employee['Status'] == 1): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Không hoạt động</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="users.php?action=edit&id=<?php echo $employee['Id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?> 