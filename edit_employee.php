<?php require_once 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setMessage('error', 'Invalid employee ID');
    redirect('employees.php');
}

$id = intval($_GET['id']);
$employee = $conn->query("SELECT * FROM employees WHERE id=$id")->fetch_assoc();

if (!$employee) {
    setMessage('error', 'Employee not found');
    redirect('employees.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $dob = sanitize($_POST['date_of_birth']);
    $gender = sanitize($_POST['gender']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $dept_id = intval($_POST['department_id']);
    $designation = sanitize($_POST['designation']);
    $doj = sanitize($_POST['date_of_joining']);
    $emp_type = sanitize($_POST['employment_type']);
    $salary = floatval($_POST['basic_salary']);
    $pan = sanitize($_POST['pan_number']);
    $aadhar = sanitize($_POST['aadhar_number']);
    $bank_acc = sanitize($_POST['bank_account']);
    $bank_name = sanitize($_POST['bank_name']);
    $emergency = sanitize($_POST['emergency_contact']);
    $emergency_name = sanitize($_POST['emergency_contact_name']);
    
    $conn->query("UPDATE employees SET 
        first_name='$first_name', last_name='$last_name', email='$email', phone='$phone',
        date_of_birth='$dob', gender='$gender', address='$address', city='$city',
        department_id=$dept_id, designation='$designation', date_of_joining='$doj',
        employment_type='$emp_type', basic_salary=$salary, pan_number='$pan',
        aadhar_number='$aadhar', bank_account='$bank_acc', bank_name='$bank_name',
        emergency_contact='$emergency', emergency_contact_name='$emergency_name'
        WHERE id=$id");
    
    setMessage('success', 'Employee updated successfully');
    redirect('employees.php');
}

$departments = $conn->query("SELECT * FROM departments ORDER BY name");
?>

<h2>Edit Employee: <?= $employee['emp_id'] ?></h2>

<form method="POST">
    <div class="card">
        <h3>Personal Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>First Name *</label>
                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($employee['first_name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Last Name *</label>
                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($employee['last_name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($employee['email']) ?>" required>
            </div>
            <div class="form-group">
                <label>Phone *</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($employee['phone']) ?>" required>
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control" value="<?= $employee['date_of_birth'] ?>">
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select name="gender" class="form-control">
                    <option value="male" <?= $employee['gender']=='male' ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= $employee['gender']=='female' ? 'selected' : '' ?>>Female</option>
                    <option value="other" <?= $employee['gender']=='other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Address</label>
            <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($employee['address']) ?></textarea>
        </div>
        <div class="form-group">
            <label>City</label>
            <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($employee['city']) ?>">
        </div>
    </div>
    
    <div class="card">
        <h3>Employment Details</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Department *</label>
                <select name="department_id" class="form-control" required>
                    <?php while ($dept = $departments->fetch_assoc()): ?>
                        <option value="<?= $dept['id'] ?>" <?= $employee['department_id']==$dept['id'] ? 'selected' : '' ?>>
                            <?= $dept['name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Designation *</label>
                <input type="text" name="designation" class="form-control" value="<?= htmlspecialchars($employee['designation']) ?>" required>
            </div>
            <div class="form-group">
                <label>Date of Joining *</label>
                <input type="date" name="date_of_joining" class="form-control" value="<?= $employee['date_of_joining'] ?>" required>
            </div>
            <div class="form-group">
                <label>Employment Type</label>
                <select name="employment_type" class="form-control">
                    <option value="full_time" <?= $employee['employment_type']=='full_time' ? 'selected' : '' ?>>Full Time</option>
                    <option value="part_time" <?= $employee['employment_type']=='part_time' ? 'selected' : '' ?>>Part Time</option>
                    <option value="contract" <?= $employee['employment_type']=='contract' ? 'selected' : '' ?>>Contract</option>
                    <option value="intern" <?= $employee['employment_type']=='intern' ? 'selected' : '' ?>>Intern</option>
                </select>
            </div>
            <div class="form-group">
                <label>Basic Salary (₹) *</label>
                <input type="number" name="basic_salary" class="form-control" value="<?= $employee['basic_salary'] ?>" required step="1000">
            </div>
        </div>
    </div>
    
    <div class="card">
        <h3>Documents & Bank Details</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>PAN Number</label>
                <input type="text" name="pan_number" class="form-control" value="<?= htmlspecialchars($employee['pan_number']) ?>" maxlength="10">
            </div>
            <div class="form-group">
                <label>Aadhar Number</label>
                <input type="text" name="aadhar_number" class="form-control" value="<?= htmlspecialchars($employee['aadhar_number']) ?>" maxlength="12">
            </div>
            <div class="form-group">
                <label>Bank Account Number</label>
                <input type="text" name="bank_account" class="form-control" value="<?= htmlspecialchars($employee['bank_account']) ?>">
            </div>
            <div class="form-group">
                <label>Bank Name</label>
                <input type="text" name="bank_name" class="form-control" value="<?= htmlspecialchars($employee['bank_name']) ?>">
            </div>
        </div>
    </div>
    
    <div class="card">
        <h3>Emergency Contact</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="emergency_contact" class="form-control" value="<?= htmlspecialchars($employee['emergency_contact']) ?>">
            </div>
            <div class="form-group">
                <label>Contact Name</label>
                <input type="text" name="emergency_contact_name" class="form-control" value="<?= htmlspecialchars($employee['emergency_contact_name']) ?>">
            </div>
        </div>
    </div>
    
    <div style="margin-top: 15px;">
        <button type="submit" class="btn btn-success">Update Employee</button>
        <a href="employees.php" class="btn">Cancel</a>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>
