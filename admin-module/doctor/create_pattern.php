<?php
include('../config.php'); // Conexión a la base de datos
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doctor_id = $_SESSION['user_id'];
    $pattern_name = $_POST['pattern_name'];
    $days_of_week = json_encode($_POST['days']); 
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $sql = "INSERT INTO availability_patterns (doctor_id, pattern_name, days_of_week, start_time, end_time, start_date, end_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $doctor_id, $pattern_name, $days_of_week, $start_time, $end_time, $start_date, $end_date);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Availability pattern saved successfully!";
    } else {
        echo "Error saving availability pattern.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Availability Pattern</title>
</head>
<body>
    <form action="create_pattern.php" method="post">
        <label for="pattern_name">Pattern Name:</label>
        <input type="text" id="pattern_name" name="pattern_name" required>
        
        <label for="days_of_week">Select Days of the Week:</label><br>
        <input type="checkbox" name="days[]" value="Mon"> Monday<br>
        <input type="checkbox" name="days[]" value="Tue"> Tuesday<br>
        <input type="checkbox" name="days[]" value="Wed"> Wednesday<br>
        <input type="checkbox" name="days[]" value="Thu"> Thursday<br>
        <input type="checkbox" name="days[]" value="Fri"> Friday<br>

        <label for="start_time">Start Time:</label>
        <input type="time" id="start_time" name="start_time" required>
        
        <label for="end_time">End Time:</label>
        <input type="time" id="end_time" name="end_time" required>
        
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required>
        
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date">

        <button type="submit">Save Pattern</button>
    </form>
</body>
</html>
