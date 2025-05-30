<?php include 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-users"></i> Quản lý nhân viên</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="users.php?action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm nhân viên
        </a>
    </div>
</div>

<!-- Form tìm kiếm và lọc -->
<form class="row g-2 mb-3" method="get" action="users.php">
    <div class="col-md-4">
        <input type="text" class="form-control" name="search" placeholder="Tìm kiếm tên, email, điện thoại..." value="<?php echo htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
    </div>
    <div class="col-md-3">
        <select class="form-select" name="department">
            <option value="all">Tất cả phòng ban</option>
            <?php foreach($departments as $dept): ?>
                <option value="<?php echo $dept['Id']; ?>" <?php if(isset($_GET['department']) && $_GET['department'] == $dept['Id']) echo 'selected'; ?>><?php echo htmlspecialchars($dept['Name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-search"></i> Tìm kiếm</button>
    </div>
</form>

<?php
// Hiển thị message từ session (flash message)
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
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Chức vụ</th>
                        <th>Ngày sinh</th>
                        <th>Phòng ban</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars($user['Name']); ?></strong>
                                    <?php if($user['isManager']): ?>
                                        <span class="badge bg-warning ms-1">Manager</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($user['Email']); ?></td>
                        <td><?php echo htmlspecialchars($user['Phone']); ?></td>
                        <td><?php echo htmlspecialchars($user['Position']); ?></td>
                        <td><?php echo !empty($user['BirthDay']) ? date('d/m/Y', strtotime($user['BirthDay'])) : '-'; ?></td>
                        <td><?php echo htmlspecialchars($user['department_name']); ?></td>
                        <td>
                            <?php if($user['Status'] == 1): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Không hoạt động</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="users.php?action=edit&id=<?php echo $user['Id']; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="users.php?action=delete&id=<?php echo $user['Id']; ?>" 
                                   class="btn btn-outline-danger" 
                                   onclick="return confirm('Bạn có chắc muốn xóa nhân viên này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <a href="users.php?action=view&id=<?php echo $user['Id']; ?>" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i>
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
        <nav>
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
