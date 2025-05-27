<?php include 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-money-bill-wave"></i> Quản lý lương</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="salaries.php?action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tạo lương mới
        </a>
    </div>
</div>

<?php
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'] ?? 'success';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<?php if(isset($message)): ?>
    <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show">
        <i class="fas fa-<?php echo $message_type === 'error' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <input type="hidden" name="action" value="index">
            
            <div class="col-md-4">
                <label for="userId" class="form-label">Lọc theo nhân viên</label>
                <select class="form-select" id="userId" name="userId">
                    <option value="">Tất cả nhân viên</option>
                    <?php foreach($users as $user): ?>
                        <option value="<?php echo $user['Id']; ?>" <?php echo (isset($_GET['userId']) && $_GET['userId'] == $user['Id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['Name'] . ' - ' . $user['Position']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label for="sort" class="form-label">Sắp xếp theo</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="">Mặc định</option>
                    <option value="salary_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'salary_asc') ? 'selected' : ''; ?>>
                        Lương cơ bản (tăng dần)
                    </option>
                    <option value="salary_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'salary_desc') ? 'selected' : ''; ?>>
                        Lương cơ bản (giảm dần)
                    </option>
                    <option value="total_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'total_asc') ? 'selected' : ''; ?>>
                        Tổng lương (tăng dần)
                    </option>
                    <option value="total_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'total_desc') ? 'selected' : ''; ?>>
                        Tổng lương (giảm dần)
                    </option>
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Lọc
                </button>
                <a href="salaries.php" class="btn btn-secondary">
                    <i class="fas fa-sync"></i> Đặt lại
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Phòng ban</th>
                        <th>Tháng</th>
                        <th>Lương cơ bản</th>
                        <th>Tổng thưởng</th>
                        <th>Tổng lương</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($salaries as $salary): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars($salary['user_name']); ?></strong>
                                    <div class="small text-muted"><?php echo htmlspecialchars($salary['user_position']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($salary['department_name']); ?></td>
                        <td><?php echo date('m/Y', strtotime($salary['Month'])); ?></td>
                        <td><?php echo number_format($salary['BaseSalary'], 0, ',', '.'); ?> VNĐ</td>
                        <td><?php echo number_format($salary['total_bonus'] ?? 0, 0, ',', '.'); ?> VNĐ</td>
                        <td>
                            <strong class="text-primary">
                                <?php echo number_format(($salary['BaseSalary'] + ($salary['total_bonus'] ?? 0)), 0, ',', '.'); ?> VNĐ
                            </strong>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="salaries.php?action=view&id=<?php echo $salary['Id']; ?>" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="salaries.php?action=edit&id=<?php echo $salary['Id']; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="salaries.php?action=delete&id=<?php echo $salary['Id']; ?>" 
                                   class="btn btn-outline-danger" 
                                   onclick="return confirm('Bạn có chắc muốn xóa bản ghi lương này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <?php if(isset($totalPages) && $totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php if((isset($_GET['page']) && $_GET['page'] == $i) || (!isset($_GET['page']) && $i == 1)) echo 'active'; ?>">
                        <a class="page-link" href="?<?php 
                            $params = $_GET; 
                            $params['page'] = $i; 
                            echo http_build_query($params); 
                        ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?> 