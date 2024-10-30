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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doctor_id = $_SESSION['user_id'];
    $template_id = $_POST['template_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Obtén la plantilla seleccionada
    $sql = "SELECT days_of_week, start_time, end_time FROM availability_templates WHERE id = ? AND doctor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $template_id, $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $template = $result->fetch_assoc();
    $stmt->close();

    if ($template) {
        $days_of_week = json_decode($template['days_of_week'], true);
        $start_time = $template['start_time'];
        $end_time = $template['end_time'];

        // Genera la disponibilidad en la tabla availability
        $current_date = strtotime($start_date);
        $end_date = strtotime($end_date);

        while ($current_date <= $end_date) {
            $day_of_week = date('l', $current_date); // Obtén el día de la semana

            if (in_array($day_of_week, $days_of_week)) {
                $date = date('Y-m-d', $current_date);
                $current_time = strtotime($start_time);
                $end_time_seconds = strtotime($end_time);

                while ($current_time < $end_time_seconds) {
                    $slot_start_time = date('H:i:s', $current_time);
                    $slot_end_time = date('H:i:s', strtotime('+30 minutes', $current_time));

                    // Inserta cada intervalo de 30 minutos en la tabla availability
                    $sql = "INSERT INTO availability (doctor_id, date, start_time, end_time, is_available) VALUES (?, ?, ?, ?, 1)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("isss", $doctor_id, $date, $slot_start_time, $slot_end_time);
                    $stmt->execute();

                    // Avanza al siguiente intervalo de 30 minutos
                    $current_time = strtotime('+30 minutes', $current_time);
                }

                $stmt->close();
            }

            $current_date = strtotime("+1 day", $current_date); // Pasa al siguiente día
        }

        echo "Template applied successfully!";
    } else {
        echo "Template not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply Availability Template</title>
</head>
<body>
    <h2>Apply Availability Template</h2>
    <form method="POST" action="">
        <label for="template_id">Select Template:</label><br>
        <select id="template_id" name="template_id" required>
            <?php
            // Obtén todas las plantillas del doctor actual
            $doctor_id = $_SESSION['user_id'];
            $sql = "SELECT id, template_name FROM availability_templates WHERE doctor_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $doctor_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['template_name'] . "</option>";
            }
            $stmt->close();
            ?>
        </select><br><br>

        <label for="start_date">Start Date:</label><br>
        <input type="date" id="start_date" name="start_date" required><br><br>

        <label for="end_date">End Date:</label><br>
        <input type="date" id="end_date" name="end_date" required><br><br>

        <button type="submit">Apply Template</button>
    </form>
</body>
</html>
