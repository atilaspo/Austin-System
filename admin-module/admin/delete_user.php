<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

include('../config.php');

$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];
$sql = "DELETE FROM users WHERE id='$id'";

if ($conn->query($sql) === TRUE) {
    header('Location: admin_dashboard.php?page=users');
    exit(); // Añadido exit() para asegurar que el script se detenga después de la redirección
} else {
    echo "Error deleting record: " . $conn->error;
}

$conn->close();
?>
