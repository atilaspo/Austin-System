<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('../config.php');

if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 1) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$user_id = $_SESSION['user_id'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$specialty = $_POST['specialty'] ?? '';
$location = $_POST['location'] ?? '';
$contact = $_POST['contact'] ?? '';
$other_details = $_POST['other_details'] ?? '';

// Verifica si los detalles ya existen
$details_sql = "SELECT * FROM user_details WHERE user_id = ?";
$details_stmt = $conn->prepare($details_sql);
$details_stmt->bind_param("i", $user_id);
$details_stmt->execute();
$details_result = $details_stmt->get_result();
$details = $details_result->fetch_assoc();

if ($details) {
    // Actualizar los detalles existentes
    $update_sql = "UPDATE user_details SET first_name = ?, last_name = ?, specialty = ?, location = ?, contact = ?, other_details = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssssi", $first_name, $last_name, $specialty, $location, $contact, $other_details, $user_id);
    $result = $update_stmt->execute();
} else {
    // Insertar nuevos detalles si no existen
    $insert_sql = "INSERT INTO user_details (user_id, first_name, last_name, specialty, location, contact, other_details) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("issssss", $user_id, $first_name, $last_name, $specialty, $location, $contact, $other_details);
    $result = $insert_stmt->execute();
}

// Comprobar el resultado de la operación
if ($result) {
    echo json_encode(['status' => 'success', 'message' => 'Details updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error updating details']);
}

$conn->close();
exit();
?>
