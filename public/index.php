<?php require_once 'includes/header.php';
require_once 'includes/ai_helpers.php';

// Dashboard stats
$totalEmployees = $conn->query("SELECT COUNT(*) as c FROM employees WHERE status='active'")->fetch_assoc()['c'];
$departments = $conn->query("SELECT COUNT(*) as c FROM departments")->fetch_assoc()['c'];
$pendingLeaves = $conn->query("SELECT COUNT(*) as c FROM leaves WHERE status='pending'")->fetch_assoc()['c'];
$presentToday = $conn->query("SELECT COUNT(*) as c FROM attendance WHERE attendance_date=CURDATE() AND status='present'")->fetch_assoc()['c'];

// Monthly payroll summary
$currentMonth = date('m');
$currentYear = date('Y');
$payrollSummary = $conn->query("
    SELECT SUM(net_salary) as total_payout, COUNT(*) as processed 
    FROM payroll WHERE month=$currentMonth AND year=$currentYear AND status IN ('processed','paid')
")->fetch_assoc();

$recentLeaves = $conn->query("
    SELECT l.*, e.first_name, e.last_name, lt.name as leave_type 
    FROM leaves l 
    JOIN employees e ON l.employee_id=e.id 
    JOIN leave_types lt ON l.leave_type_id=lt.id 
    ORDER BY l.created_at DESC LIMIT 5
");

// AI: attrition risk for all employees
$employees = $conn->query("SELECT id, first_name, last_name, department_id FROM employees WHERE status='active'");
$highRiskCount = 0;
while ($emp = $employees->fetch_assoc()) {
    $risk = calculateAttritionRisk($conn, $emp['id']);
    if ($risk['level'] == 'high') $highRiskCount++;
}
?>

<h2>Dashboard</h2>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?= $totalEmployees ?></h3>
        <p>Active Employees</p>
    </div>
    <div class="stat-card">
        <h3><?= $departments ?></h3>
        <p>Departments</p>
    </div>
    <div class="stat-card">
        <h3><?= $pendingLeaves ?></h3>
        <p>Pending Leaves</p>
    </div>
    <div class="stat-card">
        <h3><?= $presentToday ?></h3>
        <p>Present Today</p>
    </div>
</div>

<!-- AI Alert: Attrition Risk -->
<?php if ($highRiskCount > 0): ?>
<div class="card" style="border-left: 4px solid #ea4335; margin-bottom: 20px;">
    <h2 style="color: #ea4335;">⚠️ Attrition Risk Alert</h2>
    <p style="color:#666; font-size:13px;">AI analysis identifies <?= $highRiskCount ?> employee(s) at high risk of leaving</p>
    <a href="ai_insights.php" class="btn" style="background:#ea4335; color:white;">View Details</a>
</div>
<?php endif; ?>

<!-- Payroll Summary -->
<div class="card" style="border-left: 4px solid #2d8e47;">
    <h2 style="color: #2d8e47;">Monthly Payroll (<?= date('M Y') ?>)</h2>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <p style="margin:0; font-size:13px; color:#666;">Total Payout</p>
            <p style="margin:5px 0 0; font-size:24px; font-weight:bold; color:#2d8e47;">
                <?= formatCurrency($payrollSummary['total_payout'] ?? 0) ?>
            </p>
        </div>
        <div>
            <p style="margin:0; font-size:13px; color:#666;">Processed Payslips</p>
            <p style="margin:5px 0 0; font-size:24px; font-weight:bold; color:#333;">
                <?= $payrollSummary['processed'] ?? 0 ?> / <?= $totalEmployees ?>
            </p>
        </div>
    </div>
    <div style="margin-top:15px;">
        <a href="payroll.php" class="btn btn-primary">View Payroll</a>
    </div>
</div>

<!-- Recent Leave Applications -->
<div class="card">
    <h2>Recent Leave Applications</h2>
    <?php if ($recentLeaves->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Leave Type</th>
                <th>From</th>
                <th>To</th>
                <th>Days</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($leave = $recentLeaves->fetch_assoc()): ?>
            <tr>
                <td><?= $leave['first_name'] . ' ' . $leave['last_name'] ?></td>
                <td><?= $leave['leave_type'] ?></td>
                <td><?= date('d M', strtotime($leave['start_date'])) ?></td>
                <td><?= date('d M', strtotime($leave['end_date'])) ?></td>
                <td><?= $leave['days'] ?></td>
                <td>
                    <span style="background: <?= $leave['status']=='approved' ? '#2d8e47' : ($leave['status']=='rejected' ? '#ea4335' : '#fbbc04') ?>; 
                         color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                        <?= ucfirst($leave['status']) ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="color:#888;">No recent leave applications.</p>
    <?php endif; ?>
</div>

<!-- Quick Links -->
<div class="card">
    <h2>Quick Actions</h2>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="add_employee.php" class="btn btn-success">+ Add Employee</a>
        <a href="attendance.php" class="btn btn-primary">Mark Attendance</a>
        <a href="leave.php" class="btn" style="background:#fbbc04; color:#333;">Review Leaves</a>
        <a href="ai_chatbot.php" class="btn" style="background:#4285f4; color:white;">🤖 HR Chatbot</a>
        <a href="ai_insights.php" class="btn" style="background:#9c27b0; color:white;">AI Analytics</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
