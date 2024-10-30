<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';

// Verificar si el usuario ha iniciado sesión y si es un clinic
if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 4) {
    header('Location: ../login.php');
    exit();
}

$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = $_POST['report_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $contact = $_POST['contact'];
    $report = $_POST['report'];

    // Update patient details
    $update_patient_sql = "UPDATE user_details SET first_name = ?, last_name = ?, contact = ? WHERE user_id = (SELECT patient_id FROM reports WHERE id = ?)";
    $stmt_patient = $conn->prepare($update_patient_sql);
    if ($stmt_patient === false) {
        die('Prepare error: ' . $conn->error);
    }
    $stmt_patient->bind_param("sssi", $first_name, $last_name, $contact, $report_id);
    if (!$stmt_patient->execute()) {
        echo "Error updating patient details: " . $stmt_patient->error;
    }
    $stmt_patient->close();

    // Update report details
    $update_report_sql = "UPDATE reports SET report = ? WHERE id = ?";
    $stmt_report = $conn->prepare($update_report_sql);
    if ($stmt_report === false) {
        die('Prepare error: ' . $conn->error);
    }
    $stmt_report->bind_param("si", $report, $report_id);
    if ($stmt_report->execute()) {
        echo "Report updated successfully.";
    } else {
        echo "Error updating report: " . $stmt_report->error;
    }
    $stmt_report->close();
}

// Obtener los reportes
$query = "SELECT r.id, r.date, c.complaint, r.report, d.userId as doctor_name, ud.first_name, ud.last_name, ud.contact 
          FROM reports r 
          JOIN complaints c ON r.complaint_id = c.id 
          JOIN users d ON r.doctor_id = d.id 
          JOIN user_details ud ON r.patient_id = ud.user_id";
$result = $conn->query($query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Report Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div id="wrapper">

        <!-- Page Content -->
            <div class="container-fluid">
                <div class="floating-card">
                    <h2>Update Report Details</h2>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <form method="post">
                                        <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                        <h5 class="card-title">Report ID: <?php echo htmlspecialchars($row['id']); ?></h5>
                                        <p class="card-text"><strong>Date:</strong> <?php echo htmlspecialchars($row['date']); ?></p>
                                        <div class="form-group">
                                            <label for="first_name">First Name:</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($row['first_name']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="last_name">Last Name:</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($row['last_name']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="contact">Contact:</label>
                                            <input type="text" class="form-control" id="contact" name="contact" value="<?php echo htmlspecialchars($row['contact']); ?>" required>
                                        </div>
                                        <p class="card-text"><strong>Complaint:</strong> <?php echo htmlspecialchars($row['complaint']); ?></p>
                                        <p class="card-text"><strong>Doctor:</strong> <?php echo htmlspecialchars($row['doctor_name']); ?></p>
                                        <div class="form-group">
                                            <label for="report">Report:</label>
                                            <textarea class="form-control" id="report" name="report" rows="4"><?php echo htmlspecialchars($row['report']); ?></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update Report</button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No reports found.</p>
                    <?php endif; ?>
                </div>
            </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
