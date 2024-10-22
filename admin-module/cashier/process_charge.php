<?php
session_start();
include('../config.php');

if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 5) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['charge_id'])) {
    $charge_id = intval($_POST['charge_id']);
    
    $conn = new mysqli($servername, $username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "UPDATE charges SET status = 'paid' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Prepare error: ' . $conn->error);
    }
    $stmt->bind_param("i", $charge_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Charge marked as paid.";
    } else {
        $_SESSION['error_message'] = "Error updating charge: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}

header('Location: cashier_dashboard.php?page=charge_patients');
exit();
?>
