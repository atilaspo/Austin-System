<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}

$patient_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $location = $_POST['location'];
    $contact = $_POST['contact'];
    $other_details = $_POST['other_details'];

    // Check if patient details already exist
    $check_sql = "SELECT * FROM user_details WHERE user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $patient_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Update existing details
        $update_sql = "UPDATE user_details SET first_name = ?, last_name = ?, location = ?, contact = ?, other_details = ? WHERE user_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssssi", $first_name, $last_name, $location, $contact, $other_details, $patient_id);
    } else {
        // Insert new details
        $insert_sql = "INSERT INTO user_details (user_id, first_name, last_name, location, contact, other_details) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("isssss", $patient_id, $first_name, $last_name, $location, $contact, $other_details);
    }

    if ($stmt->execute()) {
        $message = "Details updated successfully.";
    } else {
        $message = "Error updating details: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch current details
$details_sql = "SELECT * FROM user_details WHERE user_id = ?";
$details_stmt = $conn->prepare($details_sql);
$details_stmt->bind_param("i", $patient_id);
$details_stmt->execute();
$details_result = $details_stmt->get_result();
$details = $details_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="container mt-4">
    <div class="floating-card">
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <h2>Update Your Details</h2>
        <form method="post">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($details['first_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($details['last_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($details['location'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="contact">Contact:</label>
                <input type="text" class="form-control" id="contact" name="contact" value="<?php echo htmlspecialchars($details['contact'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="other_details">Other Details:</label>
                <textarea class="form-control" id="other_details" name="other_details" rows="4"><?php echo htmlspecialchars($details['other_details'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Details</button>
        </form>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
