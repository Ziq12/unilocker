<?php
session_start();
// Basic check (also vulnerable, as it doesn't strictly enforce login for the report yet)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require '../includes/header.php';
?>

<div class="alert alert-success">
    <h4>Welcome,
        <?php echo $_SESSION['student_id']; ?>!
    </h4>
    <p>Your Role:
        <?php echo $_SESSION['role']; ?>
    </p>
    <p>This is your student dashboard. (Module to be built next)</p>
</div>

<a href="logout.php" class="btn btn-danger">Logout</a>

<?php require '../includes/footer.php'; ?>