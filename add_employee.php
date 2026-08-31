<?php require_once 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_id = generateEmpId();
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
    
    // create user account for the employee
    $username = strtolower($first_name . '.' . substr($last_name, 0, 3));
    $password = password_hash('welcome123', PASSWORD_DEFAULT);
    
    $conn->query("INSERT INTO users (username, password, full_name, email, role) VALUES 
                  ('$username', '$password', '$first_name $last_name', '$email', 'hr')");
    
    $user_id = $conn->insert_id();
    
    $conn->query("INSERT INTO employees (emp_id, first_name, last_name, email, phone, date_of_birth, gender, address, city, 
                  department_id, designation, date_of_joining, employment_type, basic_salary, pan_number, aadhar_number, 
                  bank_account, bank_name, emergency_contact, emergency_contact_name, status) VALUES 
                  ('$emp_id', '$first_name', '$last_name', '$email', '$phone', '$dob', '$gender', '$address', '$city',
                   $dept_id, '$designation', '$doj', '$emp_type', $salary, '$pan', '$aadhar', 
                   '$bank_acc', '$bank_name', '$emergency', '$emergency_name', 'active')");
    
    setMessage('success', "Employee $emp_id added successfully. Login: $username / welcome123");
    redirect('employees.php');
}

$departments = $conn->query("SELECT * FROM departments ORDER BY name");
?>

<h2>Add New Employee</h2>

<form method="POST">
    <div class="card">
        <h3>Personal Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>First Name *</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Last Name *</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Phone *</label>
                <input type="text" name="phone" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control">
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select name="gender" class="form-control">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Address</label>
            <textarea name="address" class="form-control" rows="2"></textarea>
        </div>
        <div class="form-group">
            <label>City</label>
            <input type="text" name="city" class="form-control" value="Pune">
        </div>
    </div>
    
    <div class="card">
        <h3>Employment Details</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Department *</label>
                <select name="department_id" class="form-control" required>
                    <?php while ($dept = $departments->fetch_assoc()): ?>
                        <option value="<?= $dept['id'] ?>"><?= $dept['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Designation *</label>
                <input type="text" name="designation" class="form-control" required placeholder="e.g. Software Engineer">
            </div>
            <div class="form-group">
                <label>Date of Joining *</label>
                <input type="date" name="date_of_joining" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Employment Type</label>
                <select name="employment_type" class="form-control">
                    <option value="full_time">Full Time</option>
                    <option value="part_time">Part Time</option>
                    <option value="contract">Contract</option>
                    <option value="intern">Intern</option>
                </select>
            </div>
            <div class="form-group">
                <label>Basic Salary (₹) *</label>
                <input type="number" name="basic_salary" class="form-control" required step="1000">
            </div>
        </div>
    </div>
    
    <div class="card">
        <h3>Documents & Bank Details</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>PAN Number</label>
                <input type="text" name="pan_number" class="form-control" maxlength="10" placeholder="ABCPK1234A">
            </div>
            <div class="form-group">
                <label>Aadhar Number</label>
                <input type="text" name="aadhar_number" class="form-control" maxlength="12" placeholder="123456789012">
            </div>
            <div class="form-group">
                <label>Bank Account Number</label>
                <input type="text" name="bank_account" class="form-control">
            </div>
            <div class="form-group">
                <label>Bank Name</label>
                <input type="text" name="bank_name" class="form-control" placeholder="SBI, HDFC, etc.">
            </div>
        </div>
    </div>
    
    <div class="card">
        <h3>Emergency Contact</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="emergency_contact" class="form-control">
            </div>
            <div class="form-group">
                <label>Contact Name</label>
                <input type="text" name="emergency_contact_name" class="form-control">
            </div>
        </div>
    </div>
    
    <div style="margin-top: 15px;">
        <button type="submit" class="btn btn-success">Add Employee</button>
        <a href="employees.php" class="btn">Cancel</a>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>
