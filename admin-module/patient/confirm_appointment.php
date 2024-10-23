<?php
include('../config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$appointment_message = "";
$appointment_status = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_SESSION['user_id'];
    $availability_id = $_POST['availability_id'];

    // Insert the appointment
    $sql = "INSERT INTO appointments (doctor_id, patient_id, availability_id, status, created_at)
            SELECT doctor_id, ?, id, 'Pending', NOW() 
            FROM availability WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $patient_id, $availability_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Mark the availability slot as booked (not available)
        $sql = "UPDATE availability SET is_available = 0 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $availability_id);
        $stmt->execute();

        $appointment_status = "success";
        $appointment_message = "Appointment booked successfully!";
    } else {
        $appointment_status = "error";
        $appointment_message = "Error booking appointment.";
    }

    // Fetch the appointment, patient, and doctor details
    $details_sql = "SELECT a.date, a.start_time, a.end_time, 
                           p.first_name AS patient_first_name, p.last_name AS patient_last_name, 
                           d.first_name AS doctor_first_name, d.last_name AS doctor_last_name, d.specialty
                    FROM appointments ap
                    JOIN availability a ON ap.availability_id = a.id
                    JOIN user_details p ON ap.patient_id = p.user_id
                    JOIN user_details d ON ap.doctor_id = d.user_id
                    WHERE ap.availability_id = ?";
    $stmt = $conn->prepare($details_sql);
    $stmt->bind_param("i", $availability_id);
    $stmt->execute();
    $appointment_details = $stmt->get_result()->fetch_assoc();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmation</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
        .success-message {
            color: #28a745;
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        .error-message {
            color: #dc3545;
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        .appointment-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .appointment-details h5 {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-5">
        <div class="text-center">
            <?php if ($appointment_status == "success"): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($appointment_message); ?>
                </div>
            <?php elseif ($appointment_status == "error"): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($appointment_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($appointment_status == "success" && $appointment_details): ?>
                <div class="appointment-details">
                    <h5>Appointment Details:</h5>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($appointment_details['date']); ?></p>
                    <p><strong>Time:</strong> <?php echo htmlspecialchars($appointment_details['start_time']); ?> - <?php echo htmlspecialchars($appointment_details['end_time']); ?></p>
                    <p><strong>Doctor:</strong> Dr. <?php echo htmlspecialchars($appointment_details['doctor_first_name'] . " " . $appointment_details['doctor_last_name']); ?> (<?php echo htmlspecialchars($appointment_details['specialty']); ?>)</p>
                    <p><strong>Patient:</strong> <?php echo htmlspecialchars($appointment_details['patient_first_name'] . " " . $appointment_details['patient_last_name']); ?></p>
                    <p>Your appointment request has been sent to the doctor. You will receive a notification once the doctor confirms the appointment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
