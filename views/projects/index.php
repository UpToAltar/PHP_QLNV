<?php include 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-project-diagram"></i> 
        Quản lý dự án
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="projects.php?action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tạo dự án mới
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
                <label for="status" class="form-label">Lọc theo trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Tất cả trạng thái</option>
                    <option value="0" <?php echo (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : ''; ?>>
                        Mới tạo
                    </option>
                    <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : ''; ?>>
                        Đang thực hiện
                    </option>
                    <option value="2" <?php echo (isset($_GET['status']) && $_GET['status'] == '2') ? 'selected' : ''; ?>>
                        Hoàn thành
                    </option>
                    <option value="3" <?php echo (isset($_GET['status']) && $_GET['status'] == '3') ? 'selected' : ''; ?>>
                        Hủy bỏ
                    </option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="sort" class="form-label">Sắp xếp theo</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="">Mặc định</option>
                    <option value="name_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : ''; ?>>
                        Tên dự án (A-Z)
                    </option>
                    <option value="name_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : ''; ?>>
                        Tên dự án (Z-A)
                    </option>
                    <option value="date_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'date_asc') ? 'selected' : ''; ?>>
                        Ngày bắt đầu (cũ nhất)
                    </option>
                    <option value="date_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'date_desc') ? 'selected' : ''; ?>>
                        Ngày bắt đầu (mới nhất)
                    </option>
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Lọc
                </button>
                <a href="projects.php" class="btn btn-secondary">
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
                        <th>Tên dự án</th>
                        <th>Mô tả</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th>Quản lý</th>
                        <th>Số thành viên</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($projects as $project): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-project-diagram text-white"></i>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars($project['ProjectName']); ?></strong>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($project['Description']); ?></td>
                        <td>
                            <?php echo date('d/m/Y', strtotime($project['StartDate'])); ?> - 
                            <?php echo date('d/m/Y', strtotime($project['EndDate'])); ?>
                        </td>
                        <td>
                            <?php
                            $statusClass = '';
                            switch($project['Status']) {
                                case 0:
                                    $statusClass = 'bg-info';
                                    break;
                                case 1:
                                    $statusClass = 'bg-warning';
                                    break;
                                case 2:
                                    $statusClass = 'bg-success';
                                    break;
                                case 3:
                                    $statusClass = 'bg-danger';
                                    break;
                            }
                            ?>
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php echo Project::getStatusName($project['Status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-user-tie text-white"></i>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars($project['manager_name']); ?></strong>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $project['total_members']; ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="projects.php?action=edit&id=<?php echo $project['Id']; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="projects.php?action=delete&id=<?php echo $project['Id']; ?>" 
                                   class="btn btn-outline-danger" 
                                   onclick="return confirm('Bạn có chắc muốn xóa dự án này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="projects.php?page=<?php echo $i; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?> 