<?php
// 1. Safely start session ONLY if one is not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Basic check: Is the user logged in?
// (In Vulnerable v1, we intentionally do NOT check $_SESSION['role'] here 
// to allow the "Broken Access Control" vulnerability for your report)
if (!isset($_SESSION['user_id'])) {
    header("Location: /unilocker/public/login.php?error=login_required");
    exit();
}
?>