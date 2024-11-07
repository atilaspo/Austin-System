<?php
include('../config.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $response = $_POST['response'];

    if ($response == 'accept') {
        // Aceptar la cita y actualizar disponibilidad
        $sql = "UPDATE appointments SET status = 'Accepted' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            // Marcar la disponibilidad correspondiente como no disponible
            $sql = "UPDATE availability 
                    JOIN appointments ON availability.id = appointments.availability_id 
                    SET availability.is_available = 0 
                    WHERE appointments.id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $appointment_id);
            $stmt->execute();

            echo "Appointment accepted!";
        } else {
            echo "Error accepting appointment.";
        }
    } elseif ($response == 'reject') {
        // Rechazar la cita, pero no cambiar la disponibilidad

        // Marcar la disponibilidad correspondiente como disponible
        $sql = "UPDATE availability 
        JOIN appointments ON availability.id = appointments.availability_id 
        SET availability.is_available = 1 
        WHERE appointments.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();

        $sql = "UPDATE appointments SET status = 'Rejected' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Appointment rejected.";
        } else {
            echo "Error rejecting appointment.";
        }
    }
}
?>
