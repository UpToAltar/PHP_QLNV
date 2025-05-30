<?php include 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-project-diagram"></i> 
        <?php echo isset($project) ? 'Chỉnh sửa dự án' : 'Tạo dự án mới'; ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="projects.php" class="btn btn-outline-secondary">
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

<form action="projects.php?action=<?php echo isset($project) ? 'update&id=' . $project['Id'] : 'create'; ?>" method="POST">
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên dự án <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo isset($project) ? $project['ProjectName'] : ''; ?>" 
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($project) ? $project['Description'] : ''; ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="startDate" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="startDate" name="startDate" 
                                       value="<?php echo isset($project) ? date('Y-m-d', strtotime($project['StartDate'])) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="endDate" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="endDate" name="endDate" 
                                       value="<?php echo isset($project) ? date('Y-m-d', strtotime($project['EndDate'])) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="managerId" class="form-label">Quản lý dự án <span class="text-danger">*</span></label>
                        <select class="form-select" id="managerId" name="managerId" required>
                            <option value="">Chọn quản lý dự án</option>
                            <?php foreach($users as $user): ?>
                                <option value="<?php echo $user['Id']; ?>" <?php echo (isset($project) && isset($project['ManagerId']) && $project['ManagerId'] == $user['Id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['Name'] . ' - ' . $user['Position']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="0" <?php echo (isset($project) && $project['Status'] == 0) ? 'selected' : ''; ?>>Mới tạo</option>
                            <option value="1" <?php echo (isset($project) && $project['Status'] == 1) ? 'selected' : ''; ?>>Đang thực hiện</option>
                            <option value="2" <?php echo (isset($project) && $project['Status'] == 2) ? 'selected' : ''; ?>>Hoàn thành</option>
                            <option value="3" <?php echo (isset($project) && $project['Status'] == 3) ? 'selected' : ''; ?>>Hủy bỏ</option>
                        </select>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 
                            <?php echo isset($project) ? 'Cập nhật' : 'Tạo mới'; ?>
                        </button>
                    </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Thành viên dự án</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <select class="form-select" id="userId">
                                <option value="">Chọn nhân viên</option>
                                <?php foreach($users as $user): ?>
                                    <option value="<?php echo $user['Id']; ?>">
                                        <?php echo htmlspecialchars($user['Name'] . ' - ' . $user['Position']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="role" placeholder="Vai trò (VD: Frontend Developer)">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary w-100" onclick="addMember()">
                                <i class="fas fa-plus"></i> Thêm
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="membersTable">
                        <thead>
                            <tr>
                                <th>Nhân viên</th>
                                <th>Vai trò</th>
                                <th>Ngày tham gia</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($members)): ?>
                                <?php foreach($members as $member): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($member['user_name']); ?></strong>
                                                <div class="small text-muted"><?php echo htmlspecialchars($member['user_position']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($member['Role']); ?></td>
                                    <td><?php echo isset($member['DateJoin']) ? date('d/m/Y H:i', strtotime($member['DateJoin'])) : '-'; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMember(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <input type="hidden" name="members[]" value='<?php echo json_encode([
                                    "userId" => $member["UserId"],
                                    "role" => $member["Role"],
                                    "dateJoin" => $member["DateJoin"] ?? null
                                ], JSON_UNESCAPED_UNICODE); ?>'>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<script>
function addMember() {
    const userId = document.getElementById('userId').value;
    const role = document.getElementById('role').value;
    const userSelect = document.getElementById('userId');
    const userText = userSelect.options[userSelect.selectedIndex].text;

    if (!userId || !role) {
        alert('Vui lòng chọn nhân viên và nhập vai trò!');
        return;
    }

    // Check if member already exists
    const existingInputs = document.querySelectorAll('input[name="members[]"]');
    for (let input of existingInputs) {
        const val = JSON.parse(input.value);
        if (val.userId === userId) {
            alert('Nhân viên này đã được thêm vào dự án!');
            return;
        }
    }

    // Lấy ngày hiện tại
    const now = new Date();
    const pad = n => n < 10 ? '0' + n : n;
    // Định dạng cho hiển thị
    const dateJoinDisplay = pad(now.getDate()) + '/' + pad(now.getMonth() + 1) + '/' + now.getFullYear() + ' ' + pad(now.getHours()) + ':' + pad(now.getMinutes());
    // Định dạng cho lưu input
    const dateJoin = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) + ' ' + pad(now.getHours()) + ':' + pad(now.getMinutes());

    // Add to table
    const tbody = document.querySelector('#membersTable tbody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <div class="d-flex align-items-center">
                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div>
                    <strong>${userText}</strong>
                </div>
            </div>
        </td>
        <td>${role}</td>
        <td>${dateJoinDisplay}</td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMember(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);

    // Thêm input ẩn vào form
    const membersInput = document.createElement('input');
    membersInput.type = 'hidden';
    membersInput.name = 'members[]';
    membersInput.value = JSON.stringify({ userId, role, dateJoin });
    tbody.parentNode.parentNode.appendChild(membersInput);
}

function removeMember(button) {
    const tr = button.closest('tr');
    const tbody = tr.parentNode;
    const trs = Array.from(tbody.children).filter(e => e.tagName === 'TR');
    const index = trs.indexOf(tr);
    tr.remove();
    // Xóa input hidden tương ứng (input nằm sau mỗi tr hoặc cuối bảng)
    const allInputs = document.querySelectorAll('input[name="members[]"]');
    if (allInputs[index]) allInputs[index].remove();
}
</script>

<?php include 'views/layouts/footer.php'; ?> 