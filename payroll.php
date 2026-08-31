<?php require_once 'includes/header.php';
require_once 'includes/ai_helpers.php';

// Handle payroll processing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_payroll'])) {
    $month = intval($_POST['month']);
    $year = intval($_POST['year']);
    
    $employees = $conn->query("SELECT * FROM employees WHERE status='active'");
    $processed = 0;
    
    while ($emp = $employees->fetch_assoc()) {
        // check if payroll already exists
        $exists = $conn->query("SELECT id FROM payroll WHERE employee_id={$emp['id']} AND month=$month AND year=$year");
        if ($exists->num_rows > 0) continue;
        
        $payroll = calculatePayroll($emp['basic_salary']);
        
        $conn->query("INSERT INTO payroll (employee_id, month, year, basic_salary, hra, conveyance, medical_allowance, 
                      special_allowance, gross_salary, pf_deduction, esi_deduction, professional_tax, tds, 
                      total_deductions, net_salary, status) VALUES 
                      ({$emp['id']}, $month, $year, {$payroll['basic']}, {$payroll['hra']}, {$payroll['conveyance']}, 
                       {$payroll['medical']}, {$payroll['special']}, {$payroll['gross']}, {$payroll['pf']}, 
                       {$payroll['esi']}, {$payroll['pt']}, {$payroll['tds']}, {$payroll['total_deductions']}, 
                       {$payroll['net']}, 'draft')");
        $processed++;
    }
    
    setMessage('success', "Payroll processed for $processed employees");
    redirect('payroll.php');
}

$month = intval($_GET['month'] ?? date('m'));
$year = intval($_GET['year'] ?? date('Y'));

$payroll = $conn->query("
    SELECT p.*, e.emp_id, e.first_name, e.last_name, d.name as department_name
    FROM payroll p
    JOIN employees e ON p.employee_id=e.id
    LEFT JOIN departments d ON e.department_id=d.id
    WHERE p.month=$month AND p.year=$year
    ORDER BY e.emp_id
");

$monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June', 
               'July', 'August', 'September', 'October', 'November', 'December'];
?>

<h2>Payroll - <?= $monthNames[$month] ?> <?= $year ?></h2>

<div style="margin-bottom:15px; display:flex; gap:10px; align-items:center;">
    <form method="GET" style="display:flex; gap:10px;">
        <select name="month" class="form-control" onchange="this.form.submit()">
            <?php for ($i=1; $i<=12; $i++): ?>
                <option value="<?= $i ?>" <?= $i==$month ? 'selected' : '' ?>><?= $monthNames[$i] ?></option>
            <?php endfor; ?>
        </select>
        <select name="year" class="form-control" onchange="this.form.submit()">
            <?php for ($y=2024; $y<=2027; $y++): ?>
                <option value="<?= $y ?>" <?= $y==$year ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
    </form>
    
    <form method="POST" style="margin-left:auto;">
        <input type="hidden" name="month" value="<?= $month ?>">
        <input type="hidden" name="year" value="<?= $year ?>">
        <button type="submit" name="process_payroll" class="btn btn-success"
                onclick="return confirm('Process payroll for all active employees?')">
            Process Payroll
        </button>
    </form>
</div>

<?php if ($payroll->num_rows > 0): ?>
<?php
$totalGross = 0;
$totalDeductions = 0;
$totalNet = 0;
?>
<div class="card">
    <table>
        <thead>
            <tr>
                <th>Emp ID</th>
                <th>Name</th>
                <th>Dept</th>
                <th>Basic</th>
                <th>HRA</th>
                <th>Gross</th>
                <th>PF</th>
                <th>ESI</th>
                <th>PT</th>
                <th>TDS</th>
                <th>Total Ded.</th>
                <th>Net Salary</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($p = $payroll->fetch_assoc()): 
                $totalGross += $p['gross_salary'];
                $totalDeductions += $p['total_deductions'];
                $totalNet += $p['net_salary'];
            ?>
            <tr>
                <td><?= $p['emp_id'] ?></td>
                <td><?= $p['first_name'] ?> <?= $p['last_name'] ?></td>
                <td><small><?= $p['department_name'] ?></small></td>
                <td><?= formatCurrency($p['basic_salary']) ?></td>
                <td><?= formatCurrency($p['hra']) ?></td>
                <td><strong><?= formatCurrency($p['gross_salary']) ?></strong></td>
                <td style="color:#ea4335;"><?= formatCurrency($p['pf_deduction']) ?></td>
                <td style="color:#ea4335;"><?= formatCurrency($p['esi_deduction']) ?></td>
                <td style="color:#ea4335;"><?= formatCurrency($p['professional_tax']) ?></td>
                <td style="color:#ea4335;"><?= formatCurrency($p['tds']) ?></td>
                <td style="color:#ea4335;"><?= formatCurrency($p['total_deductions']) ?></td>
                <td style="color:#2d8e47;"><strong><?= formatCurrency($p['net_salary']) ?></strong></td>
                <td>
                    <span style="background: <?= $p['status']=='paid' ? '#2d8e47' : ($p['status']=='processed' ? '#4285f4' : '#fbbc04') ?>; 
                         color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                        <?= ucfirst($p['status']) ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr style="background:#f8f9fa; font-weight:bold;">
                <td colspan="3">Total</td>
                <td></td>
                <td></td>
                <td><?= formatCurrency($totalGross) ?></td>
                <td colspan="4"></td>
                <td style="color:#ea4335;"><?= formatCurrency($totalDeductions) ?></td>
                <td style="color:#2d8e47;"><?= formatCurrency($totalNet) ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Payroll Breakdown Explanation -->
<div class="card" style="border-left: 4px solid #4285f4;">
    <h2 style="color:#4285f4;">Payroll Calculation Breakdown</h2>
    <p style="color:#666; font-size:13px;">Indian statutory deductions applied automatically</p>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
        <div>
            <h3 style="font-size:14px; margin-bottom:10px;">Earnings</h3>
            <p style="font-size:13px; margin:5px 0;"><strong>Basic Salary</strong> - As per offer letter</p>
            <p style="font-size:13px; margin:5px 0;"><strong>HRA</strong> - 40% of basic (Pune rate)</p>
            <p style="font-size:13px; margin:5px 0;"><strong>Conveyance</strong> - Fixed ₹1,600</p>
            <p style="font-size:13px; margin:5px 0;"><strong>Medical Allowance</strong> - Fixed ₹1,250</p>
            <p style="font-size:13px; margin:5px 0;"><strong>Special Allowance</strong> - 10% of basic</p>
        </div>
        <div>
            <h3 style="font-size:14px; margin-bottom:10px;">Deductions</h3>
            <p style="font-size:13px; margin:5px 0;"><strong>PF</strong> - 12% of basic (max ₹1,800)</p>
            <p style="font-size:13px; margin:5px 0;"><strong>ESI</strong> - 0.75% (if gross ≤ ₹21,000)</p>
            <p style="font-size:13px; margin:5px 0;"><strong>Professional Tax</strong> - ₹200/month (if basic > ₹25,000)</p>
            <p style="font-size:13px; margin:5px 0;"><strong>TDS</strong> - Based on old tax regime</p>
        </div>
    </div>
</div>

<?php else: ?>
<div class="card">
    <p style="color:#888;">No payroll records for <?= $monthNames[$month] ?> <?= $year ?>. Click "Process Payroll" to generate.</p>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
