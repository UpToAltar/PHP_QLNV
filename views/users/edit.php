<?php include 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-user-edit"></i> Cập nhật nhân viên</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="users.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<?php if(isset($error)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Họ và tên *</label>
                        <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($user['Name']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($user['Email']); ?>" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['Phone']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="position" class="form-label">Chức vụ</label>
                        <input type="text" class="form-control" id="position" name="position" value="<?php echo htmlspecialchars($user['Position']); ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="departmentId" class="form-label">Phòng ban</label>
                        <select class="form-select" id="departmentId" name="departmentId">
                            <option value="">Chọn phòng ban</option>
                            <?php foreach($departments as $dept): ?>
                                <option value="<?php echo $dept['Id']; ?>" <?php if($user['DepartmentId'] == $dept['Id']) echo 'selected'; ?>><?php echo htmlspecialchars($dept['Name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="dateIn" class="form-label">Ngày vào làm</label>
                        <?php
                        $dateInValue = '';
                        if (!empty($user['DateIn'])) {
                            $dateInValue = date('Y-m-d', strtotime($user['DateIn']));
                        }
                        ?>
                        <input type="date" class="form-control" id="dateIn" name="dateIn" value="<?php echo $dateInValue; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="birthDay" class="form-label">Ngày sinh</label>
                        <input type="date" class="form-control" id="birthDay" name="birthDay" value="<?php echo htmlspecialchars($user['BirthDay']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Để trống nếu không đổi">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="1" <?php if($user['Status'] == 1) echo 'selected'; ?>>Hoạt động</option>
                            <option value="0" <?php if($user['Status'] == 0) echo 'selected'; ?>>Không hoạt động</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="role" class="form-label">Vai trò</label>
                        <select class="form-select" id="role" name="role">
                            <option value="1" <?php if($user['Role'] == 1) echo 'selected'; ?>>Nhân viên</option>
                            <option value="2" <?php if($user['Role'] == 2) echo 'selected'; ?>>Quản lý</option>
                            <option value="3" <?php if($user['Role'] == 3) echo 'selected'; ?>>Admin</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="isManager" name="isManager" <?php if($user['isManager']) echo 'checked'; ?>>
                    <label class="form-check-label" for="isManager">
                        Là quản lý
                    </label>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="users.php" class="btn btn-secondary me-md-2">Hủy</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?> 