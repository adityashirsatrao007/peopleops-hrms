<?php require_once __DIR__ . '/../config.php'; requireLogin(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?></title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        /* quick fixes - should consolidate into style.css */
        .nav-badge { background: #4285f4; color: white; font-size: 10px; padding: 2px 6px; border-radius: 10px; margin-left: 4px; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand"><a href="index.php"><?= SITE_NAME ?></a></div>
        <div class="nav-links">
            <a href="index.php">Dashboard</a>
            <a href="employees.php">Employees</a>
            <a href="attendance.php">Attendance</a>
            <a href="leave.php">Leaves</a>
            <a href="payroll.php">Payroll</a>
            <a href="ai_insights.php">AI Insights <span class="nav-badge">NEW</span></a>
            <a href="ai_chatbot.php">HR Bot</a>
            <a href="feedback.php">Feedback</a>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>
    <main class="container">
    <?php $msg = getMessage(); if ($msg): ?>
        <div class="alert alert-<?= $msg['type'] ?>"><?= $msg['msg'] ?></div>
    <?php endif; ?>
