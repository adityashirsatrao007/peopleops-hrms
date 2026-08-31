<?php
// PeopleOps - Employee Management System
// Database configuration - supports PostgreSQL (Render) and MySQL (local XAMPP)
require_once __DIR__ . '/includes/db_compat.php';

$dbUrl = getenv('DATABASE_URL');
if ($dbUrl) {
    $parts = parse_url($dbUrl);
    $host = $parts['host'] ?? 'localhost';
    $port = $parts['port'] ?? '5432';
    $dbname = ltrim($parts['path'], '/');
    $user = $parts['user'] ?? '';
    $pass = $parts['pass'] ?? '';
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $conn = new CompatDB($dsn, $user, $pass);
    $conn->query("SET search_path TO hrms, public");
} else {
    $host = getenv('DB_HOST') ?: 'localhost';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';
    $dbname = getenv('DB_NAME') ?: 'peopleops_db';
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $conn = new CompatDB($dsn, $user, $pass);
}

define('SITE_NAME', 'PeopleOps');
define('CURRENCY', '₹');

if (session_status() == PHP_SESSION_NONE) session_start();

function isLoggedIn() { return isset($_SESSION['user_id']); }
function requireLogin() { if (!isLoggedIn()) { header("Location: login.php"); exit(); } }
function sanitize($data) { global $conn; return $conn->real_escape_string(htmlspecialchars($data)); }
function formatCurrency($amount) { return CURRENCY . number_format($amount, 2); }
function redirect($url) { header("Location: $url"); exit(); }
function setMessage($type, $msg) { $_SESSION['flash'] = ['type'=>$type, 'msg'=>$msg]; }
function getMessage() { if (isset($_SESSION['flash'])) { $m=$_SESSION['flash']; unset($_SESSION['flash']); return $m; } return null; }

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
