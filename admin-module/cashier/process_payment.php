<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../config.php');

if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 5) {
    header('Location: ../login.php');
    exit();
}

$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $charge_id = $_POST['charge_id'];
    $payment_method = $_POST['payment_method'];

    $sql = "UPDATE charges SET status = 'paid', payment_method = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('si', $payment_method, $charge_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Payment processed successfully.";
        } else {
            $_SESSION['error_message'] = "Error processing payment.";
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Error preparing statement.";
    }
}

$conn->close();

header('Location: cashier_dashboard.php?page=charge_patients');
exit();
?>
