<?php
session_start();
require '../config/db.php';
require '../includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // VULNERABILITY: No input validation or sanitization
    $student_id = $_POST['student_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // VULNERABILITY: Stored in plaintext

    // VULNERABILITY: SQL Injection via direct string concatenation
    $sql = "INSERT INTO users (student_id, name, email, password, role) 
            VALUES ('$student_id', '$name', '$email', '$password', 'student')";

    if (mysqli_query($conn, $sql)) {
        $success = "Registration successful! You can now <a href='login.php'>login</a>.";
    } else {
        // VULNERABILITY: Information Disclosure (leaks database structure/errors to user)
        $error = "Registration failed: " . mysqli_error($conn);
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Student Registration</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Student ID</label>
                        <input type="text" name="student_id" class="form-control" required
                            placeholder="e.g., A22CS1234">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">University Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>
                <div class="mt-3 text-center">
                    <small>Already have an account? <a href="login.php">Login here</a></small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>