<?php include 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-calendar-alt"></i> Quản lý nghỉ phép</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="absences.php?action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tạo đơn nghỉ phép
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
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Phòng ban</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Lý do</th>
                        <th>Trạng thái</th>
                        <th>Người duyệt</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($absences as $absence): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars($absence['user_name']); ?></strong>
                                    <div class="small text-muted"><?php echo htmlspecialchars($absence['user_position']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($absence['department_name']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($absence['StartDate'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($absence['EndDate'])); ?></td>
                        <td><?php echo htmlspecialchars($absence['Reason']); ?></td>
                        <td>
                            <?php
                            $statusClass = '';
                            $statusText = '';
                            switch($absence['Status']) {
                                case 0:
                                    $statusClass = 'warning';
                                    $statusText = 'Chờ xác nhận';
                                    break;
                                case 1:
                                    $statusClass = 'success';
                                    $statusText = 'Đã duyệt';
                                    break;
                                case 2:
                                    $statusClass = 'danger';
                                    $statusText = 'Từ chối';
                                    break;
                            }
                            ?>
                            <span class="badge bg-<?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                        </td>
                        <td>
                            <?php if($absence['approved_by_name']): ?>
                                <?php echo htmlspecialchars($absence['approved_by_name']); ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <?php if($absence['Status'] == 0): ?>
                                    <a href="absences.php?action=view&id=<?php echo $absence['Id']; ?>" class="btn btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="absences.php?action=approve&id=<?php echo $absence['Id']; ?>" 
                                       class="btn btn-outline-success" 
                                       onclick="return confirm('Bạn có chắc muốn duyệt đơn nghỉ phép này?')">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="absences.php?action=reject&id=<?php echo $absence['Id']; ?>" 
                                       class="btn btn-outline-danger" 
                                       onclick="return confirm('Bạn có chắc muốn từ chối đơn nghỉ phép này?')">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    <a href="absences.php?action=edit&id=<?php echo $absence['Id']; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="absences.php?action=delete&id=<?php echo $absence['Id']; ?>" 
                                       class="btn btn-outline-danger" 
                                       onclick="return confirm('Bạn có chắc muốn xóa đơn nghỉ phép này?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="absences.php?action=view&id=<?php echo $absence['Id']; ?>" class="btn btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                <?php endif; ?>
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
                            $params = $_GET; $params['page'] = $i; echo http_build_query($params); 
                        ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?> 