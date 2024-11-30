<?php
function sendNotification($to, $subject, $message) {
    $headers = "From: no-reply@hospital.com\r\n";
    mail($to, $subject, $message, $headers);
}

// Ejemplo de uso después de aceptar o rechazar una cita
if ($action == 'Aceptar') {
    $message = "Su cita ha sido aceptada.";
} elseif ($action == 'Rechazar') {
    $message = "Su cita ha sido rechazada.";
}
sendNotification($patient_email, "Estado de su cita", $message);
?>
