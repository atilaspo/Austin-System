<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../config.php');

if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 4) {
    header('Location: ../login.php');
    exit();
}

// Fetch reports from the database
$sql = "SELECT r.id, r.date, c.complaint, r.report, p.first_name as patient_first_name, p.last_name as patient_last_name, d.first_name as doctor_first_name, d.last_name as doctor_last_name 
        FROM reports r 
        JOIN complaints c ON r.complaint_id = c.id 
        JOIN users pu ON c.user_id = pu.id
        JOIN user_details p ON pu.id = p.user_id 
        JOIN users du ON r.doctor_id = du.id
        JOIN user_details d ON du.id = d.user_id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <h2 class="ml-2">View Reports</h2>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Report ID: <?php echo $row['id']; ?></h5>
                        <p class="card-text"><strong>Date:</strong> <?php echo $row['date']; ?></p>
                        <p class="card-text"><strong>Complaint:</strong> <?php echo $row['complaint']; ?></p>
                        <p class="card-text"><strong>Patient:</strong> <?php echo $row['patient_first_name'] . ' ' . $row['patient_last_name']; ?></p>
                        <p class="card-text"><strong>Doctor:</strong> <?php echo $row['doctor_first_name'] . ' ' . $row['doctor_last_name']; ?></p>
                        <p class="card-text"><strong>Report:</strong> <?php echo $row['report']; ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No reports found.</p>
        <?php endif; ?>
    </div>

    <?php
    $conn->close();
    ?>
</body>

