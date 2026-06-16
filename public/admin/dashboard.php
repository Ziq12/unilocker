<?php
session_start();
require '../../config/db.php';
require '../../includes/header.php';

// 🚨 VULNERABILITY 1: Broken Access Control (Missing RBAC) 🚨
// It only checks if the user is logged in. It DOES NOT check if $_SESSION['role'] == 'admin'.
// A regular student can access this page!
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$message = '';

// 🚨 VULNERABILITY 2: SQL Injection in Search 🚨
$search_result = null;
if (isset($_GET['search_id'])) {
    $search_id = $_GET['search_id']; // No sanitization or prepared statements
    $sql_search = "SELECT d.delivery_id, u.student_id, l.locker_number, d.pin, d.status 
                   FROM deliveries d 
                   LEFT JOIN users u ON d.receiver_id = u.user_id 
                   JOIN lockers l ON d.locker_id = l.locker_id 
                   WHERE d.delivery_id = $search_id";
    
    $result_search = mysqli_query($conn, $sql_search);
    if ($result_search && mysqli_num_rows($result_search) > 0) {
        $search_result = mysqli_fetch_assoc($result_search);
    } else {
        $message = "No delivery found with ID: " . htmlspecialchars($search_id);
    }
}

// Fetch all deliveries for the main table (Information Disclosure: Shows plaintext PINs)
$sql_all = "SELECT d.delivery_id, u.student_id, u.name as receiver_name, l.locker_number, d.pin, d.deposit_type, d.status, d.created_at 
            FROM deliveries d 
            LEFT JOIN users u ON d.receiver_id = u.user_id 
            JOIN lockers l ON d.locker_id = l.locker_id 
            ORDER BY d.created_at DESC";
$all_deliveries = mysqli_query($conn, $sql_all);
?>



<!-- Search Form (Vulnerable to SQLi) -->
<div class="card mb-4 border-danger">
    <div class="card-header bg-danger text-white">Search Delivery by ID</div>
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-auto">
                <input type="text" name="search_id" class="form-control" placeholder="Enter Delivery ID (e.g., 1 OR 1=1)" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-danger">Search</button>
            </div>
        </form>
        <?php if($search_result): ?>
            <div class="alert alert-info mt-3">
                <strong>Found:</strong> Delivery #<?php echo $search_result['delivery_id']; ?> | 
                Student: <?php echo $search_result['student_id'] ?: 'Anonymous'; ?> | 
                Locker: <?php echo $search_result['locker_number']; ?> | 
                <span class="text-danger">PIN: <?php echo $search_result['pin']; ?></span> | 
                Status: <?php echo $search_result['status']; ?>
            </div>
        <?php elseif(isset($_GET['search_id'])): ?>
            <div class="alert alert-warning mt-3"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>
</div>

<!-- All Deliveries Table -->
<div class="card">
    <div class="card-header bg-dark text-white">All System Deliveries</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Receiver</th>
                        <th>Locker</th>
                        <th>Type</th>
                        <th>PIN</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($all_deliveries)): ?>
                        <tr>
                            <td><?php echo $row['delivery_id']; ?></td>
                            <td><?php echo $row['receiver_name'] ? $row['receiver_name'] . ' (' . $row['student_id'] . ')' : 'Anonymous'; ?></td>
                            <td><?php echo $row['locker_number']; ?></td>
                            <td><?php echo $row['deposit_type']; ?></td>
                            <td class="text-danger fw-bold"><?php echo $row['pin']; ?></td>
                            <td>
                                <span class="badge bg-<?php echo $row['status'] == 'Collected' ? 'success' : 'warning'; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>