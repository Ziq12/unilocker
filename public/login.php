<?php
session_start();
require '../config/db.php';
require '../includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // VULNERABILITY 1: SQL Injection (Direct string concatenation)
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // VULNERABILITY 2: Plaintext password comparison
        if ($password === $user['password']) {

            // VULNERABILITY 3: Session Fixation (Session ID is NOT regenerated)
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['role'] = $user['role'];

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // VULNERABILITY 4: Account Enumeration (Tells attacker the password is wrong, confirming email exists)
            $error = "Invalid password for this email address.";
        }
    } else {
        // VULNERABILITY 4: Account Enumeration (Tells attacker the email doesn't exist)
        $error = "No account found with this email address.";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">UniLocker Login</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">University Email</label>
                        <input type="text" name="email" class="form-control" required
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form