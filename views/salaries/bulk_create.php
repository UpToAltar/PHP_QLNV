<?php include 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-money-bill-wave"></i> Tạo bảng lương hàng loạt
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
                <form action="salaries.php?action=bulkCreate" method="POST">
                    <div class="mb-3">
                        <label for="sourceMonth" class="form-label">Tháng lấy dữ liệu <span class="text-danger">*</span></label>
                        <input type="month" class="form-control" id="sourceMonth" name="sourceMonth" required>
                    </div>

                    <div class="mb-3">
                        <label for="targetMonth" class="form-label">Tháng tạo bảng lương <span class="text-danger">*</span></label>
                        <input type="month" class="form-control" id="targetMonth" name="targetMonth" required>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Tạo bảng lương
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?> 