<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../config.php');

if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 3) {
    exit('Unauthorized access');
}

$doctor_id = $_SESSION['user_id'];
$date = isset($_GET['date']) ? $_GET['date'] : ''; // Fecha seleccionada o vacío para ver todos

// Construir la consulta según si hay una fecha seleccionada o no
if ($date) {
    $sql = "SELECT a.*, u.first_name, u.last_name, u.contact, av.date AS appointment_date, av.start_time, av.end_time
            FROM appointments a
            JOIN user_details u ON a.patient_id = u.user_id
            JOIN availability av ON a.availability_id = av.id
            WHERE a.status = 'Accepted' AND a.doctor_id = ? AND av.date = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error); // Mostrar error específico si la consulta falla
    }
    $stmt->bind_param("is", $doctor_id, $date);
} else {
    $sql = "SELECT a.*, u.first_name, u.last_name, u.contact, av.date AS appointment_date, av.start_time, av.end_time
            FROM appointments a
            JOIN user_details u ON a.patient_id = u.user_id
            JOIN availability av ON a.availability_id = av.id
            WHERE a.status = 'Accepted' AND a.doctor_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error); // Mostrar error específico si la consulta falla
    }
    $stmt->bind_param("i", $doctor_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accepted Appointments</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Accepted Appointments for <?php echo $date ? htmlspecialchars($date, ENT_QUOTES) : 'All Dates'; ?></h2>
        <form action="view_accepted_appointments.php" method="get" class="form-inline mb-4">
            <label for="date" class="mr-2">Select Date:</label>
            <input type="date" name="date" id="date" class="form-control mr-2" value="<?php echo htmlspecialchars($date, ENT_QUOTES); ?>">
            <button type="submit" class="btn btn-primary mr-2">Filter</button>
            <a href="doctor_dashboard.php?page=view_accepted_appointments" class="btn btn-secondary">Reset</a> <!-- Botón de Reset -->
        </form>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Contact</th>
                    <th>Appointment Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($row['contact'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['appointment_date'])), ENT_QUOTES); ?></td> <!-- Muestra la fecha del appointment -->
                    <td><?php echo htmlspecialchars(date('H:i', strtotime($row['start_time'])), ENT_QUOTES); ?></td> <!-- Muestra la hora de inicio -->
                    <td><?php echo htmlspecialchars(date('H:i', strtotime($row['end_time'])), ENT_QUOTES); ?></td> <!-- Muestra la hora de fin -->
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
