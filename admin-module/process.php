<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = htmlspecialchars($_POST['userId']);
    $password = htmlspecialchars($_POST['password']);
    $role = intval($_POST['role']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $db_password = "";
    $dbname = "admin_module_db";

    // Crear conexión
    $conn = new mysqli($servername, $username, $db_password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Verificar si el rol es válido
    if ($role < 1 || $role > 5) {
        die("Invalid role selected.");
    }

    $sql = "INSERT INTO users (userId, password, role_id) VALUES ('$userId', '$hashed_password', $role)";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
