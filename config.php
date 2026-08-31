<?php
// PeopleOps - Employee Management System
// database config
// TODO: move these to .env for production

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default
define('DB_NAME', 'peopleops_db');
define('SITE_NAME', 'PeopleOps');
define('CURRENCY', '₹');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

if (session_status() == PHP_SESSION_NONE) session_start();

function isLoggedIn() { return isset($_SESSION['user_id']); }
function requireLogin() { if (!isLoggedIn()) { header("Location: login.php"); exit(); } }
function sanitize($data) { global $conn; return $conn->real_escape_string(htmlspecialchars($data)); }
function formatCurrency($amount) { return CURRENCY . number_format($amount, 2); }
function redirect($url) { header("Location: $url"); exit(); }
function setMessage($type, $msg) { $_SESSION['flash'] = ['type'=>$type, 'msg'=>$msg]; }
function getMessage() { if (isset($_SESSION['flash'])) { $m=$_SESSION['flash']; unset($_SESSION['flash']); return $m; } return null; }

// generate employee ID like EMP001, EMP002...
function generateEmpId() {
    global $conn;
    $result = $conn->query("SELECT emp_id FROM employees ORDER BY id DESC LIMIT 1");
    if ($result->num_rows > 0) {
        $last = $result->fetch_assoc();
        $num = intval(str_replace('EMP', '', $last['emp_id'])) + 1;
        return 'EMP' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }
    return 'EMP001';
}
?>
