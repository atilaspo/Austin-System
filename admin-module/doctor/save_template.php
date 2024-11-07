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
    $template_name = $_POST['template_name'];
    $days_of_week = json_encode($_POST['days_of_week']); 
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    
    $sql = "INSERT INTO availability_templates (doctor_id, template_name, days_of_week, start_time, end_time) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $doctor_id, $template_name, $days_of_week, $start_time, $end_time);

    if ($stmt->execute()) {
        echo "Template saved successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Availability Template</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <h2>Create Availability Template</h2>
    <div class='floating-card'>
    <form method="POST" action="">
        <label for="template_name">Template Name:</label><br>
        <input type="text" id="template_name" name="template_name" required><br><br>

        <label for="days_of_week">Days of the Week:</label><br>
        <input type="checkbox" name="days_of_week[]" value="Monday"> Monday<br>
        <input type="checkbox" name="days_of_week[]" value="Tuesday"> Tuesday<br>
        <input type="checkbox" name="days_of_week[]" value="Wednesday"> Wednesday<br>
        <input type="checkbox" name="days_of_week[]" value="Thursday"> Thursday<br>
        <input type="checkbox" name="days_of_week[]" value="Friday"> Friday<br>
        <input type="checkbox" name="days_of_week[]" value="Saturday"> Saturday<br>
        <input type="checkbox" name="days_of_week[]" value="Sunday"> Sunday<br><br>

        <label for="start_time">Start Time:</label><br>
        <input type="time" id="start_time" name="start_time" required><br><br>

        <label for="end_time">End Time:</label><br>
        <input type="time" id="end_time" name="end_time" required><br><br>

        <button type="submit" class="btn btn-primary">Save Template</button>
    </form>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
