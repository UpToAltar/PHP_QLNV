<?php include 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-calendar-alt"></i> 
        <?php echo isset($absence) ? 'Chỉnh sửa đơn nghỉ phép' : 'Tạo đơn nghỉ phép mới'; ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="absences.php" class="btn btn-outline-secondary">
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

<div class="card">
    <div class="card-body">
        <form action="absences.php?action=<?php echo isset($absence) ? 'update&id=' . $absence['Id'] : 'create'; ?>" method="POST">
            <div class="mb-3">
                <label for="userId" class="form-label">Nhân viên <span class="text-danger">*</span></label>
                <select class="form-select" id="userId" name="userId" required>
                    <option value="">Chọn nhân viên</option>
                    <?php foreach($users as $user): ?>
                        <option value="<?php echo $user['Id']; ?>" <?php echo (isset($absence) && $absence['UserId'] == $user['Id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['Name'] . ' - ' . $user['Position']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="startDate" name="startDate" 
                               value="<?php echo isset($absence) ? date('Y-m-d', strtotime($absence['StartDate'])) : ''; ?>" 
                               required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="endDate" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="endDate" name="endDate" 
                               value="<?php echo isset($absence) ? date('Y-m-d', strtotime($absence['EndDate'])) : ''; ?>" 
                               required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="reason" class="form-label">Lý do nghỉ phép <span class="text-danger">*</span></label>
                <textarea class="form-control" id="reason" name="reason" rows="4" required><?php echo isset($absence) ? htmlspecialchars($absence['Reason']) : ''; ?></textarea>
            </div>

            <?php if(isset($absence)): ?>
            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="statusPending" value="0" 
                               <?php echo (!isset($absence) || $absence['Status'] == 0) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="statusPending">
                            <span class="badge bg-warning">Chờ xác nhận</span>
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="statusApproved" value="1"
                               <?php echo (isset($absence) && $absence['Status'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="statusApproved">
                            <span class="badge bg-success">Đã duyệt</span>
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="statusRejected" value="2"
                               <?php echo (isset($absence) && $absence['Status'] == 2) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="statusRejected">
                            <span class="badge bg-danger">Từ chối</span>
                        </label>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="text-end">
                <?php if(!isset($_GET['view']) || $_GET['view'] !== true): ?>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> 
                    <?php echo isset($absence) ? 'Cập nhật' : 'Tạo mới'; ?>
                </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validate date range
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');

    function validateDates() {
        if (startDate.value && endDate.value) {
            if (new Date(startDate.value) > new Date(endDate.value)) {
                endDate.setCustomValidity('Ngày kết thúc phải sau ngày bắt đầu');
            } else {
                endDate.setCustomValidity('');
            }
        }
    }

    startDate.addEventListener('change', validateDates);
    endDate.addEventListener('change', validateDates);
});
</script>

<?php include 'views/layouts/footer.php'; ?> 