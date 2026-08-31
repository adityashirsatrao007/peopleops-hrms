<?php require_once 'includes/header.php';

// Handle leave approval/rejection
if (isset($_GET['action'])) {
    $leave_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if (in_array($action, ['approved', 'rejected'])) {
        $conn->query("UPDATE leaves SET status='$action', approved_by={$_SESSION['user_id']} WHERE id=$leave_id");
        setMessage('success', "Leave $action successfully");
    }
    redirect('leave.php');
}

$pendingLeaves = $conn->query("
    SELECT l.*, e.emp_id, e.first_name, e.last_name, lt.name as leave_type, u.full_name as approver_name
    FROM leaves l 
    JOIN employees e ON l.employee_id=e.id 
    JOIN leave_types lt ON l.leave_type_id=lt.id
    LEFT JOIN users u ON l.approved_by=u.id
    WHERE l.status='pending'
    ORDER BY l.created_at DESC
");

$processedLeaves = $conn->query("
    SELECT l.*, e.emp_id, e.first_name, e.last_name, lt.name as leave_type, u.full_name as approver_name
    FROM leaves l 
    JOIN employees e ON l.employee_id=e.id 
    JOIN leave_types lt ON l.leave_type_id=lt.id
    LEFT JOIN users u ON l.approved_by=u.id
    WHERE l.status != 'pending'
    ORDER BY l.created_at DESC LIMIT 20
");

$leaveTypes = $conn->query("SELECT * FROM leave_types");
?>

<h2>Leave Management</h2>

<!-- Pending Leaves -->
<div class="card" style="border-left: 4px solid #fbbc04;">
    <h2 style="color: #fbbc04;">Pending Applications (<?= $pendingLeaves->num_rows ?>)</h2>
    
    <?php if ($pendingLeaves->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Leave Type</th>
                <th>From</th>
                <th>To</th>
                <th>Days</th>
                <th>Reason</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($leave = $pendingLeaves->fetch_assoc()): ?>
            <tr>
                <td><?= $leave['emp_id'] ?> - <?= $leave['first_name'] ?> <?= $leave['last_name'] ?></td>
                <td><?= $leave['leave_type'] ?></td>
                <td><?= date('d M Y', strtotime($leave['start_date'])) ?></td>
                <td><?= date('d M Y', strtotime($leave['end_date'])) ?></td>
                <td><?= $leave['days'] ?></td>
                <td><?= htmlspecialchars($leave['reason']) ?></td>
                <td>
                    <a href="?action=approved&id=<?= $leave['id'] ?>" class="btn btn-success" style="padding:4px 12px; font-size:12px;"
                       onclick="return confirm('Approve this leave?')">Approve</a>
                    <a href="?action=rejected&id=<?= $leave['id'] ?>" class="btn" style="background:#ea4335; color:white; padding:4px 12px; font-size:12px;"
                       onclick="return confirm('Reject this leave?')">Reject</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="color:#888;">No pending leave applications. All caught up!</p>
    <?php endif; ?>
</div>

<!-- Leave Types -->
<div class="card">
    <h2>Leave Types</h2>
    <table>
        <thead><tr><th>Type</th><th>Days Allowed/Year</th><th>Description</th></tr></thead>
        <tbody>
            <?php while ($type = $leaveTypes->fetch_assoc()): ?>
            <tr>
                <td><strong><?= $type['name'] ?></strong></td>
                <td><?= $type['days_allowed'] ?></td>
                <td><?= $type['description'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Processed Leaves -->
<div class="card">
    <h2>Recently Processed</h2>
    <?php if ($processedLeaves->num_rows > 0): ?>
    <table>
        <thead>
            <tr><th>Employee</th><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th><th>Approved By</th></tr>
        </thead>
        <tbody>
            <?php while ($leave = $processedLeaves->fetch_assoc()): ?>
            <tr>
                <td><?= $leave['first_name'] ?> <?= $leave['last_name'] ?></td>
                <td><?= $leave['leave_type'] ?></td>
                <td><?= date('d M', strtotime($leave['start_date'])) ?></td>
                <td><?= date('d M', strtotime($leave['end_date'])) ?></td>
                <td><?= $leave['days'] ?></td>
                <td>
                    <span style="background: <?= $leave['status']=='approved' ? '#2d8e47' : '#ea4335' ?>; 
                         color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                        <?= ucfirst($leave['status']) ?>
                    </span>
                </td>
                <td><?= $leave['approver_name'] ?? '-' ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="color:#888;">No processed leaves yet.</p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
