<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $result = $conn->query("SELECT * FROM users WHERE username = '$username'");
    $user = $result->fetch_assoc();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        redirect('index.php');
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PeopleOps</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        /* login page - didn't want to make it too fancy */
        .login-wrapper { display:flex; justify-content:center; align-items:center; min-height:80vh; }
        .login-box { background:white; padding:40px; border-radius:12px; box-shadow:0 2px 20px rgba(0,0,0,0.1); width:100%; max-width:400px; }
        .login-box h1 { text-align:center; margin-bottom:10px; color:#333; }
        .login-box p { text-align:center; color:#666; margin-bottom:30px; font-size:14px; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-box">
            <h1><?= SITE_NAME ?></h1>
            <p>Employee Management System</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="enter your username">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="enter password">
                </div>
                <button type="submit" class="btn btn-success" style="width:100%;">Login</button>
            </form>
            
            <div style="margin-top:20px; padding:15px; background:#f8f9fa; border-radius:8px; font-size:12px; color:#666;">
                <p style="margin:0;"><strong>Demo Credentials:</strong></p>
                <p style="margin:5px 0 0;">Admin: admin / admin123</p>
            </div>
        </div>
    </div>
</body>
</html>
