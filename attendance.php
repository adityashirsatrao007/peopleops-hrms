<?php require_once 'includes/header.php';

// Handle attendance marking
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_id = intval($_POST['employee_id']);
    $date = sanitize($_POST['attendance_date']);
    $check_in = sanitize($_POST['check_in']);
    $check_out = sanitize($_POST['check_out']);
    $status = sanitize($_POST['status']);
    $overtime = floatval($_POST['overtime_hours']);
    
    // check if attendance already exists
    $existing = $conn->query("SELECT id FROM attendance WHERE employee_id=$emp_id AND attendance_date='$date'");
    
    if ($existing->num_rows > 0) {
        $conn->query("UPDATE attendance SET check_in='$check_in', check_out='$check_out', 
                      status='$status', overtime_hours=$overtime WHERE employee_id=$emp_id AND attendance_date='$date'");
        setMessage('success', 'Attendance updated');
    } else {
        $conn->query("INSERT INTO attendance (employee_id, attendance_date, check_in, check_out, status, overtime_hours) 
                      VALUES ($emp_id, '$date', '$check_in', '$check_out', '$status', $overtime)");
        setMessage('success', 'Attendance marked');
    }
    redirect('attendance.php');
}

$filter_date = $_GET['date'] ?? date('Y-m-d');
$employees = $conn->query("SELECT id, emp_id, first_name, last_name FROM employees WHERE status='active' ORDER BY emp_id");
$attendance = $conn->query("
    SELECT a.*, e.emp_id, e.first_name, e.last_name 
    FROM attendance a 
    JOIN employees e ON a.employee_id=e.id 
    WHERE a.attendance_date='$filter_date'
    ORDER BY e.emp_id
");

// Calculate stats for the day
$dayStats = $conn->query("
    SELECT 
        COUNT(CASE WHEN status='present' THEN 1 END) as present,
        COUNT(CASE WHEN status='absent' THEN 1 END) as absent,
        COUNT(CASE WHEN status='half_day' THEN 1 END) as half_day,
        COUNT(CASE WHEN status='leave' THEN 1 END) as on_leave
    FROM attendance WHERE attendance_date='$filter_date'
")->fetch_assoc();
?>

<h2>Attendance Management</h2>

<!-- Day Stats -->
<div class="stats-grid" style="margin-bottom: 20px;">
    <div class="stat-card"><h3 style="color:#2d8e47"><?= $dayStats['present'] ?></h3><p>Present</p></div>
    <div class="stat-card"><h3 style="color:#ea4335"><?= $dayStats['absent'] ?></h3><p>Absent</p></div>
    <div class="stat-card"><h3 style="color:#fbbc04"><?= $dayStats['half_day'] ?></h3><p>Half Day</p></div>
    <div class="stat-card"><h3 style="color:#4285f4"><?= $dayStats['on_leave'] ?></h3><p>On Leave</p></div>
</div>

<!-- Mark Attendance Form -->
<div class="card">
    <h2>Mark Attendance</h2>
    <form method="POST" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
        <div class="form-group" style="flex:1; min-width:200px;">
            <label>Employee</label>
            <select name="employee_id" class="form-control" required>
                <?php 
                $employees->data_seek(0);
                while ($emp = $employees->fetch_assoc()): ?>
                    <option value="<?= $emp['id'] ?>"><?= $emp['emp_id'] ?> - <?= $emp['first_name'] ?> <?= $emp['last_name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="attendance_date" class="form-control" value="<?= $filter_date ?>" required>
        </div>
        <div class="form-group">
            <label>Check In</label>
            <input type="time" name="check_in" class="form-control" value="09:00">
        </div>
        <div class="form-group">
            <label>Check Out</label>
            <input type="time" name="check_out" class="form-control" value="18:00">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="half_day">Half Day</option>
                <option value="leave">Leave</option>
            </select>
        </div>
        <div class="form-group" style="width:80px;">
            <label>OT (hrs)</label>
            <input type="number" name="overtime_hours" class="form-control" value="0" step="0.25">
        </div>
        <button type="submit" class="btn btn-success">Mark</button>
    </form>
</div>

<!-- Attendance List -->
<div class="card">
    <h2>Attendance for <?= date('d M Y', strtotime($filter_date)) ?></h2>
    
    <form method="GET" style="margin-bottom:15px;">
        <label>Filter by date:</label>
        <input type="date" name="date" value="<?= $filter_date ?>" onchange="this.form.submit()">
    </form>
    
    <?php if ($attendance->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Emp ID</th>
                <th>Name</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
                <th>Overtime</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($att = $attendance->fetch_assoc()): ?>
            <tr>
                <td><?= $att['emp_id'] ?></td>
                <td><?= $att['first_name'] ?> <?= $att['last_name'] ?></td>
                <td><?= $att['check_in'] ? date('h:i A', strtotime($att['check_in'])) : '-' ?></td>
                <td><?= $att['check_out'] ? date('h:i A', strtotime($att['check_out'])) : '-' ?></td>
                <td>
                    <span style="background: <?= 
                        $att['status']=='present' ? '#2d8e47' : 
                        ($att['status']=='absent' ? '#ea4335' : 
                        ($att['status']=='half_day' ? '#fbbc04' : '#4285f4')) ?>; 
                         color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                        <?= ucfirst(str_replace('_', ' ', $att['status'])) ?>
                    </span>
                </td>
                <td><?= $att['overtime_hours'] > 0 ? $att['overtime_hours'] . 'h' : '-' ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="color:#888;">No attendance records for this date.</p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
