<?php
include('../config.php');
session_start();

$doctor_id = $_SESSION['user_id'];

$sql = "SELECT * FROM availability_patterns WHERE doctor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

while ($pattern = $result->fetch_assoc()) {
    $start_date = new DateTime($pattern['start_date']);
    $end_date = new DateTime($pattern['end_date']);
    $days_of_week = json_decode($pattern['days_of_week'], true);
    $interval = new DateInterval('P1D');
    $date_range = new DatePeriod($start_date, $interval, $end_date->add($interval));

    foreach ($date_range as $date) {
        if (in_array($date->format('D'), $days_of_week)) {
            $sql = "INSERT INTO availability (doctor_id, date, start_time, end_time, is_available) 
                    VALUES (?, ?, ?, ?, 1)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isss", $doctor_id, $date->format('Y-m-d'), $pattern['start_time'], $pattern['end_time']);
            $stmt->execute();
        }
    }
}
?>
