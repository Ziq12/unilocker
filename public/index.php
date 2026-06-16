<?php
session_start();
require '../includes/header.php';
?>

<div class="text-center mt-5">
    <h1 class="display-4 fw-bold text-primary">🔒 UniLocker</h1>
    <p class="lead text-muted">Secure Campus Delivery & Item Exchange System</p>
    <p class="mb-4">A secure way for students to receive parcels, food, and exchange items without the risk of theft or
        animal interference.</p>

    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center mt-4">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php" class="btn btn-primary btn-lg px-4 gap-3">Go to My Dashboard</a>
            <a href="deposit.php" class="btn btn-outline-success btn-lg px-4">Make a Public Deposit</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-primary btn-lg px-4 gap-3">Student / Admin Login</a>
            <a href="deposit.php" class="btn btn-outline-success btn-lg px-4">Public Deposit (No Login)</a>
        <?php endif; ?>
    </div>
</div>

<?php require '../includes/footer.php'; ?>