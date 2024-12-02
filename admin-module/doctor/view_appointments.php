<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../config.php');

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}
$doctor_id = $_SESSION['user_id'];

// Obtener citas pendientes
$sql = "SELECT a.id AS appointment_id, p.first_name, p.last_name, av.date, av.start_time, av.end_time
        FROM appointments a
        JOIN availability av ON a.availability_id = av.id
        JOIN user_details p ON a.patient_id = p.user_id
        WHERE a.doctor_id = ? AND a.status = 'Pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Appointments</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class='floating-card'>
        <h2 class="text-center mb-4">Pending Appointments</h2>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='floating-card'>";
                echo "<h5>Patient: " . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</h5>";
                echo "<p>Date: " . htmlspecialchars($row['date']) . "</p>";
                echo "<p>Time: " . htmlspecialchars($row['start_time']) . " - " . htmlspecialchars($row['end_time']) . "</p>";
                echo "<form action='respond_appointment.php' method='post' class='d-inline-block'>";
                echo "<input type='hidden' name='appointment_id' value='" . htmlspecialchars($row['appointment_id']) . "'>";
                echo "<button type='submit' name='response' value='accept' class='btn btn-primary'>Accept</button>";
                echo "<button type='submit' name='response' value='reject' class='btn btn-secondary'>Reject</button>";
                echo "</form>";
                echo "</div><hr>";
            }
        } else {
            echo "<div class='alert alert-info text-center'>No pending appointments.</div>";
        }
        ?>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
