<?php include 'views/layouts/header.php'; ?>

<?php
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'] ?? 'success';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-building"></i> Quản lý phòng ban</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="departments.php?action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm phòng ban
        </a>
    </div>
</div>

<?php if(isset($message)): ?>
    <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show">
        <i class="fas fa-<?php echo $message_type === 'error' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <?php foreach($departments as $dept): ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="card-title">
                        <i class="fas fa-building text-primary"></i>
                        <a href="departments.php?action=edit&id=<?php echo urlencode($dept['Id']); ?>" class="text-decoration-none text-dark">
                            <?php echo htmlspecialchars($dept['Name']); ?>
                        </a>
                    </h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="departments.php?action=edit&id=<?php echo $dept['Id']; ?>">
                                <i class="fas fa-edit"></i> Sửa
                            </a></li>
                            <li><a class="dropdown-item text-danger" href="departments.php?action=delete&id=<?php echo $dept['Id']; ?>" 
                                   onclick="return confirm('Bạn có chắc muốn xóa phòng ban này?')">
                                <i class="fas fa-trash"></i> Xóa
                            </a></li>
                        </ul>
                    </div>
                </div>
                
                <p class="card-text text-muted">
                    <?php echo htmlspecialchars($dept['Description']); ?>
                </p>
                
                <div class="mt-auto">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-success fw-bold">
                            <i class="fas fa-dollar-sign"></i>
                            <?php echo number_format($dept['Budget'], 0, ',', '.'); ?> VNĐ
                        </span>
                        <small class="text-muted">Ngân sách</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php include 'views/layouts/footer.php'; ?>
