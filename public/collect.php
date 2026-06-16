<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require '../config/db.php';
require '../includes/header.php';

$message = '';
$error = '';

// Pre-fill locker number if clicked from dashboard
$prefill_locker = isset($_GET['locker']) ? $_GET['locker'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $locker_number = $_POST['locker_number'];
    $pin = $_POST['pin'];
    $user_id = $_SESSION['user_id'];

    // 🚨 VULNERABILITY 1: IDOR (Missing Ownership Check) 🚨
    // The query ONLY checks if the Locker and PIN match a pending delivery.
    // It DOES NOT check if receiver_id = $user_id. 
    // This means Student A can collect Student B's package if they know the PIN!

    // 🚨 VULNERABILITY 2: SQL Injection 🚨
    $sql = "SELECT d.delivery_id, d.receiver_id, l.locker_id 
            FROM deliveries d 
            JOIN lockers l ON d.locker_id = l.locker_id 
            WHERE l.locker_number = '$locker_number' AND d.pin = '$pin' AND d.status = 'Pending'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $delivery = mysqli_fetch_assoc($result);

        // Update delivery status to Collected
        $update_sql = "UPDATE deliveries SET status = 'Collected', collected_at = NOW() WHERE delivery_id = " . $delivery['delivery_id'];
        mysqli_query($conn, $update_sql);

        // Release the locker
        $release_sql = "UPDATE lockers SET status = 'Available' WHERE locker_id = " . $delivery['locker_id'];
        mysqli_query($conn, $release_sql);

        $message = "✅ Item collected successfully from Locker <strong>$locker_number</strong>!";
    } else {
        $error = "❌ Invalid Locker Number or PIN. Please try again.";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow border-primary">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">🔓 Collect Your Item</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Locker Number</label>
                        <input type="text" name="locker_number" class="form-control" required
                            value="<?php echo htmlspecialchars($prefill_locker); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Enter 6-Digit PIN</label>
                        <input type="text" name="pin" class="form-control" required maxlength="6" pattern="[0-9]{6}"
                            placeholder="e.g., 849201">
                        <small class="text-muted">You should have received this PIN from the depositor.</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Collect Item</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="dashboard.php" class="text-muted">← Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>