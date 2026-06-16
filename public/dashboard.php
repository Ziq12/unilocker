<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require '../config/db.php';
require '../includes/header.php';

$user_id = $_SESSION['user_id'];

// Fetch pending deliveries for this logged-in user
$sql = "SELECT d.delivery_id, l.locker_number, d.status, d.created_at 
        FROM deliveries d 
        JOIN lockers l ON d.locker_id = l.locker_id 
        WHERE d.receiver_id = $user_id AND d.status = 'Pending'";
$result = mysqli_query($conn, $sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>📦 My Pending Deliveries</h2>
    <span class="badge bg-primary fs-6">Welcome, <?php echo $_SESSION['student_id']; ?></span>
</div>

<?php if (mysqli_num_rows($result) > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Locker Number</th>
                    <th>Status</th>
                    <th>Deposited At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong><?php echo $row['locker_number']; ?></strong></td>
                        <td><span class="badge bg-warning text-dark"><?php echo $row['status']; ?></span></td>
                        <td><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                        <td>
                            <!-- Passes locker number to collect.php -->
                            <a href="collect.php?locker=<?php echo $row['locker_number']; ?>" class="btn btn-sm btn-primary">🔓
                                Collect Item</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info text-center">
        <h5>You have no pending deliveries.</h5>
        <p>Items deposited for you will appear here.</p>
    </div>
<?php endif; ?>

<?php require '../includes/footer.php'; ?>