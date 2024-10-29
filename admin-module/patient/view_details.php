<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';

// Verificar si el usuario ha iniciado sesión y si es un paciente
if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}

// Obtener el ID del paciente de la sesión
$patient_id = $_SESSION['user_id'];

// Obtener los reportes asignados a este paciente
$query = "SELECT r.id, r.date, c.complaint, r.report, d.userId as doctor_name 
          FROM reports r 
          JOIN complaints c ON r.complaint_id = c.id 
          JOIN users d ON r.doctor_id = d.id 
          WHERE r.patient_id = ?";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die("Prepare error: " . $conn->error);
}

$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container-fluid mt-4">
    <div class="floating-card">
        <h2>View Reports</h2>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Report ID: <?php echo htmlspecialchars($row['id'] ?? ''); ?></h5>
                        <p class="card-text"><strong>Date:</strong> <?php echo htmlspecialchars($row['date'] ?? ''); ?></p>
                        <p class="card-text"><strong>Complaint:</strong> <?php echo htmlspecialchars($row['complaint'] ?? ''); ?></p>
                        <p class="card-text"><strong>Doctor:</strong> <?php echo htmlspecialchars($row['doctor_name'] ?? ''); ?></p>
                        <p class="card-text"><strong>Report:</strong> <?php echo nl2br(htmlspecialchars($row['report'] ?? '')); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No reports found.</p>
        <?php endif; ?>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
?>
