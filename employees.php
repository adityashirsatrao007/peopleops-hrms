<?php require_once 'includes/header.php';
require_once 'includes/ai_helpers.php';

// Handle status change
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $emp = $conn->query("SELECT status FROM employees WHERE id=$id")->fetch_assoc();
    $new_status = $emp['status'] == 'active' ? 'inactive' : 'active';
    $conn->query("UPDATE employees SET status='$new_status' WHERE id=$id");
    setMessage('success', "Employee $new_status");
    redirect('employees.php');
}

$employees = $conn->query("
    SELECT e.*, d.name as department_name,
           (SELECT COUNT(*) FROM attendance WHERE employee_id=e.id AND status='absent' 
             AND attendance_date >= CURRENT_DATE - INTERVAL '30 days') as absent_days
    FROM employees e 
    LEFT JOIN departments d ON e.department_id=d.id 
    ORDER BY e.emp_id
");

$search = $_GET['search'] ?? '';
if ($search) {
    $employees = $conn->query("
        SELECT e.*, d.name as department_name,
               (SELECT COUNT(*) FROM attendance WHERE employee_id=e.id AND status='absent' 
                AND attendance_date >= CURRENT_DATE - INTERVAL '30 days') as absent_days
        FROM employees e 
        LEFT JOIN departments d ON e.department_id=d.id 
        WHERE e.first_name LIKE '%$search%' OR e.last_name LIKE '%$search%' 
               OR e.emp_id LIKE '%$search%' OR e.email LIKE '%$search%'
        ORDER BY e.emp_id
    ");
}
?>

<h2>Employees</h2>

<div style="margin-bottom:15px;">
    <form method="GET" style="display:flex; gap:10px;">
        <input type="text" name="search" placeholder="Search by name, ID, or email..." 
               value="<?= htmlspecialchars($search) ?>" class="form-control" style="max-width:400px;">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($search): ?>
            <a href="employees.php" class="btn">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <div style="margin-bottom:15px;">
        <a href="add_employee.php" class="btn btn-success">+ Add New Employee</a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Emp ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($emp = $employees->fetch_assoc()): ?>
            <tr>
                <td><strong><?= $emp['emp_id'] ?></strong></td>
                <td>
                    <?= $emp['first_name'] . ' ' . $emp['last_name'] ?>
                    <br><small style="color:#888;"><?= $emp['email'] ?></small>
                </td>
                <td><?= $emp['department_name'] ?? 'N/A' ?></td>
                <td><?= $emp['designation'] ?></td>
                <td><?= $emp['phone'] ?></td>
                <td>
                    <span style="background: <?= $emp['status']=='active' ? '#2d8e47' : '#ea4335' ?>; 
                         color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                        <?= ucfirst($emp['status']) ?>
                    </span>
                </td>
                <td>
                    <a href="edit_employee.php?id=<?= $emp['id'] ?>" style="color:#4285f4; font-size:12px;">Edit</a>
                    <a href="?toggle=<?= $emp['id'] ?>" style="color:#fbbc04; font-size:12px; margin-left:8px;"
                       onclick="return confirm('Toggle employee status?')">
                        <?= $emp['status']=='active' ? 'Deactivate' : 'Activate' ?>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
