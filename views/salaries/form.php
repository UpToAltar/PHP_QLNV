<?php include 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-money-bill-wave"></i> 
        <?php echo isset($salary) ? 'Chỉnh sửa lương' : 'Tạo lương mới'; ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="salaries.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
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

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="salaries.php?action=<?php echo isset($salary) ? 'update&id=' . $salary['Id'] : 'create'; ?>" method="POST">
                    <div class="mb-3">
                        <label for="userId" class="form-label">Nhân viên <span class="text-danger">*</span></label>
                        <select class="form-select" id="userId" name="userId" required <?php echo isset($_GET['view']) ? 'disabled' : ''; ?>>
                            <option value="">Chọn nhân viên</option>
                            <?php foreach($users as $user): ?>
                                <option value="<?php echo $user['Id']; ?>" <?php echo (isset($salary) && $salary['UserId'] == $user['Id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['Name'] . ' - ' . $user['Position']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="month" class="form-label">Tháng <span class="text-danger">*</span></label>
                        <input type="month" class="form-control" id="month" name="month" 
                               value="<?php echo isset($salary) ? date('Y-m', strtotime($salary['Month'])) : ''; ?>" 
                               required <?php echo (isset($_GET['view']) || isset($salary)) ? 'disabled' : ''; ?>>
                    </div>

                    <div class="mb-3">
                        <label for="baseSalary" class="form-label">Lương cơ bản <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="baseSalary" name="baseSalary" 
                                   value="<?php echo isset($salary) ? $salary['BaseSalary'] : ''; ?>" 
                                   required <?php echo isset($_GET['view']) ? 'disabled' : ''; ?>>
                            <span class="input-group-text">VNĐ</span>
                        </div>
                    </div>

                    <?php if(!isset($_GET['view'])): ?>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 
                            <?php echo isset($salary) ? 'Cập nhật' : 'Tạo mới'; ?>
                        </button>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <?php if(!isset($salary)): ?>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lịch sử lương</h5>
            </div>
            <div class="card-body">
                <?php if(empty($userSalaries)): ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-history fa-2x mb-2"></i>
                        <p>Chưa có lịch sử lương</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tháng</th>
                                    <th>Lương cơ bản</th>
                                    <th>Tổng thưởng</th>
                                    <th>Tổng lương</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($userSalaries as $userSalary): ?>
                                <tr>
                                    <td><?php echo date('m/Y', strtotime($userSalary['Month'])); ?></td>
                                    <td><?php echo number_format($userSalary['BaseSalary'], 0, ',', '.'); ?> VNĐ</td>
                                    <td><?php echo number_format($userSalary['total_bonus'] ?? 0, 0, ',', '.'); ?> VNĐ</td>
                                    <td>
                                        <strong class="text-primary">
                                            <?php echo number_format(($userSalary['BaseSalary'] + ($userSalary['total_bonus'] ?? 0)), 0, ',', '.'); ?> VNĐ
                                        </strong>
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
    <?php endif; ?>

    <?php if(isset($salary)): ?>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Danh sách thưởng</h5>
            </div>
            <div class="card-body">
                <?php if(!isset($_GET['view'])): ?>
                <form action="salaries.php?action=addBonus" method="POST" class="mb-4">
                    <input type="hidden" name="salaryId" value="<?php echo $salary['Id']; ?>">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <select class="form-select" name="type" required>
                                <option value="">Chọn loại thưởng</option>
                                <option value="0">Thưởng tùy chọn</option>
                                <option value="1">Thưởng sinh nhật</option>
                                <option value="2">Thưởng lễ, tết</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="description" placeholder="Lý do thưởng" required>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="number" class="form-control" name="amount" placeholder="Số tiền" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <?php endif; ?>

                <?php if(empty($bonuses)): ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-gift fa-2x mb-2"></i>
                        <p>Chưa có khoản thưởng nào</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Loại thưởng</th>
                                    <th>Lý do</th>
                                    <th>Số tiền</th>
                                    <?php if(!isset($_GET['view'])): ?>
                                    <th>Thao tác</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($bonuses as $bonus): ?>
                                <tr>
                                    <td><?php echo Bonus::getTypeName($bonus['Type']); ?></td>
                                    <td><?php echo htmlspecialchars($bonus['Description']); ?></td>
                                    <td><?php echo number_format($bonus['Amount'], 0, ',', '.'); ?> VNĐ</td>
                                    <?php if(!isset($_GET['view'])): ?>
                                    <td>
                                        <a href="salaries.php?action=deleteBonus&bonusId=<?php echo $bonus['Id']; ?>&salaryId=<?php echo $salary['Id']; ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Bạn có chắc muốn xóa khoản thưởng này?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th colspan="2">Tổng thưởng</th>
                                    <th colspan="<?php echo isset($_GET['view']) ? '1' : '2'; ?>">
                                        <?php 
                                        $totalBonus = array_sum(array_column($bonuses, 'Amount'));
                                        echo number_format($totalBonus, 0, ',', '.'); 
                                        ?> VNĐ
                                    </th>
                                </tr>
                                <tr class="table-success">
                                    <th colspan="2">Tổng lương</th>
                                    <th colspan="<?php echo isset($_GET['view']) ? '1' : '2'; ?>">
                                        <?php 
                                        $totalSalary = $salary['BaseSalary'] + $totalBonus;
                                        echo number_format($totalSalary, 0, ',', '.'); 
                                        ?> VNĐ
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('userId').addEventListener('change', function() {
    if(this.value) {
        const url = new URL(window.location.href);
        url.searchParams.set('userId', this.value);
        window.location.href = url.toString();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const selectedUserId = urlParams.get('userId');
    if(selectedUserId) {
        document.getElementById('userId').value = selectedUserId;
    }
});
</script>

<?php include 'views/layouts/footer.php'; ?> 