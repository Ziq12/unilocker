<?php
session_start();
require '../config/db.php';
require '../includes/header.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id']; // Optional
    $item_type = $_POST['item_type'];

    // --- VULNERABILITY 1: Unrestricted File Upload ---
    // No validation on file extension, MIME type, or size.
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);
    $photo_path = NULL;

    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
        $photo_path = $target_file;
    }

    // --- Find Receiver ID (if student ID was provided) ---
    $receiver_id = "NULL";
    $deposit_type = "Anonymous";

    if (!empty($student_id)) {
        // VULNERABILITY 2: SQL Injection in lookup
        $lookup_sql = "SELECT user_id FROM users WHERE student_id = '$student_id'";
        $lookup_result = mysqli_query($conn, $lookup_sql);

        if ($lookup_result && mysqli_num_rows($lookup_result) > 0) {
            $row = mysqli_fetch_assoc($lookup_result);
            $receiver_id = $row['user_id'];
            $deposit_type = "Known";
        } else {
            // VULNERABILITY 3: Silent failure / Information leakage
            $error .= "Warning: Student ID not found. Proceeding as anonymous deposit.<br>";
        }
    }

    // --- Find an available locker ---
    $locker_sql = "SELECT locker_id, locker_number FROM lockers WHERE status = 'Available' LIMIT 1";
    $locker_result = mysqli_query($conn, $locker_sql);

    if ($locker_result && mysqli_num_rows($locker_result) > 0) {
        $locker_row = mysqli_fetch_assoc($locker_result);
        $locker_id = $locker_row['locker_id'];
        $locker_number = $locker_row['locker_number'];

        // Mark locker as occupied (Vulnerable to Race Conditions - no DB locking)
        $update_locker_sql = "UPDATE lockers SET status = 'Occupied' WHERE locker_id = $locker_id";
        mysqli_query($conn, $update_locker_sql);

        // --- VULNERABILITY 4: Plaintext PIN Storage & Weak Randomness ---
        $pin = rand(100000, 999999);

        // --- VULNERABILITY 5: SQL Injection in INSERT statement ---
        $insert_sql = "INSERT INTO deliveries (receiver_id, locker_id, pin, photo_path, deposit_type, status) 
                       VALUES ($receiver_id, $locker_id, '$pin', '$photo_path', '$deposit_type', 'Pending')";

        if (mysqli_query($conn, $insert_sql)) {
            // VULNERABILITY 6: Reflected XSS via $item_type
            $message = "Success! Item (<b>$item_type</b>) deposited in Locker: <b>$locker_number</b>. <br>
                        <div class='alert alert-warning mt-2'>
                            <strong>SHARE THIS PIN WITH THE RECEIVER:</strong> <h3>$pin</h3>
                        </div>
                        <small class='text-muted'>(Note: PIN is stored in plaintext in the database for this prototype)</small>";
        } else {
            // VULNERABILITY 7: Information Disclosure (Raw DB Error)
            $error .= "Database Error: " . mysqli_error($conn);
        }
    } else {
        $error .= "No lockers available.";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow border-success">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">📦 Public Item Deposit</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($message): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Receiver Student ID <span class="text-muted">(Optional)</span></label>
                        <input type="text" name="student_id" class="form-control" placeholder="e.g., A22CS1234 (Leave blank for anonymous)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Item Type</label>
                        <input type="text" name="item_type" class="form-control" required placeholder="e.g., Food, Parcel, Document">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Proof Photo</label>
                        <input type="file" name="photo" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Deposit Item</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>