<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = htmlspecialchars($_POST['userId']);
    $password = htmlspecialchars($_POST['password']);

    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $db_password = "";
    $dbname = "admin_module_db";

    $conn = new mysqli($servername, $username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, password, role_id FROM users WHERE userId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['userId'] = $userId;
            $_SESSION['role_id'] = $row['role_id'];
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $row['id'];

            switch ($row['role_id']) {
                case 1:
                    header("Location: admin/admin_dashboard.php");
                    break;
                case 2:
                    header("Location: patient/patient_dashboard.php");
                    break;
                case 3:
                    header("Location: doctor/doctor_dashboard.php");
                    break;
                case 4:
                    header("Location: clinic/clinic_dashboard.php");
                    break;
                case 5:
                    header("Location: cashier/cashier_dashboard.php");
                    break;
                default:
                    $_SESSION['error_message'] = "Invalid role.";
                    break;
            }
            exit();
        } else {
            $_SESSION['error_message'] = "Invalid password.";
        }
    } else {
        $_SESSION['error_message'] = "No user found with that ID.";
    }

    $stmt->close();
    $conn->close();
    header("Location: login.php");
    exit();
}
