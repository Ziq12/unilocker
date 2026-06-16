<?php
// Safely start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define the absolute base URL for your project
// This ensures links work perfectly whether you are in /public/ or /public/admin/
$base_url = "/unilocker/public/";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniLocker - Secure Campus Delivery</title>
    <!-- Bootstrap 5 CDN for professional styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo $base_url; ?>index.php">🔒 UniLocker</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>deposit.php">📦 Public Deposit</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>dashboard.php">📋 My Dashboard</a>
                        </li>
                        <!-- Admin Link -->
                        <li class="nav-item">
                            <a class="nav-link text-warning fw-bold" href="<?php echo $base_url; ?>admin/dashboard.php">⚙️
                                Admin Panel</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item d-flex align-items-center me-3">
                            <span class="text-white me-2 small">
                                <?php echo htmlspecialchars($_SESSION['student_id']); ?>
                            </span>
                            <!-- PROMINENT ROLE BADGE -->
                            <span class="badge <?php echo ($_SESSION['role'] === 'admin') ? 'bg-danger' : 'bg-success'; ?>">
                                <?php echo strtoupper(htmlspecialchars($_SESSION['role'])); ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-light btn-sm" href="<?php echo $base_url; ?>logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-light btn-sm ms-2" href="<?php echo $base_url; ?>register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="container mb-5">