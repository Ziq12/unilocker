<?php
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $receiver_id = $_POST['receiver_id'];

    // VULNERABILITY: No validation on file type or name. 
    // An attacker can upload "shell.php" and execute it.
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);

    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
        // VULNERABILITY: Storing plaintext PIN
        $pin = rand(100000, 999999);
        $sql = "INSERT INTO deliveries (receiver_id, locker_id, pin, photo_path, deposit_type) 
                VALUES ('$receiver_id', '1', '$pin', '$target_file', 'Known')";
        mysqli_query($conn, $sql);

        echo "Deposit successful. PIN is: " . $pin; // Also an Information Disclosure flaw
    }
}
?>