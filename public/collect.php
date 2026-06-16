<?php
session_start();
require '../config/db.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $locker_id = $_POST['locker_id'];
    $pin = $_POST['pin'];

    // VULNERABILITY: IDOR. It only checks if the locker and PIN match ANY delivery.
    // It DOES NOT check if $_SESSION['user_id'] matches the delivery's receiver_id.
    $sql = "SELECT * FROM deliveries WHERE locker_id = '$locker_id' AND pin = '$pin' AND status = 'Pending'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Update status
        $update_sql = "UPDATE deliveries SET status = 'Collected' WHERE locker_id = '$locker_id'";
        mysqli_query($conn, $update_sql);

        $update_locker = "UPDATE lockers SET status = 'Available' WHERE locker_id = '$locker_id'";
        mysqli_query($conn, $update_locker);

        echo "Item collected successfully!";
    } else {
        echo "Invalid PIN or Locker.";
    }
}
?>