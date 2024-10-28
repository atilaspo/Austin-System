<?php
include '../config.php'; // Incluye la conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    $patient_id = $_SESSION['user_id'];
    $doctor_id = $_POST['doctor_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $sql = "INSERT INTO reviews (patient_id, doctor_id, rating, comment) VALUES ('$patient_id', '$doctor_id', '$rating', '$comment')";

    if (mysqli_query($conn, $sql)) {
        echo "Reseña enviada con éxito.";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
    header("Location: doctor_profile.php?doctor_id=$doctor_id");
    exit();
}
?>
