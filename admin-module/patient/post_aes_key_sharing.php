<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('../config.php');

// Verificar si el usuario ha iniciado sesión y si es un paciente
if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}

$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$doctors = [];
$doctor_sql = "SELECT u.id, ud.first_name, ud.last_name FROM users u JOIN user_details ud ON u.id = ud.user_id WHERE u.role_id = 3";
$doctor_result = $conn->query($doctor_sql);
while ($row = $doctor_result->fetch_assoc()) {
    $doctors[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = $_POST['doctor_id'];
    $aes_key = $_POST['aes_key'];
    $password = $_POST['password'];

    // Encriptar la clave AES usando una contraseña
    $method = 'aes-256-cbc';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $encrypted_key = openssl_encrypt($aes_key, $method, $password, 0, $iv);
    $encrypted_data = base64_encode($encrypted_key . '::' . $iv);

    $stmt = $conn->prepare("INSERT INTO aes_keys (patient_id, doctor_id, aes_key) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $_SESSION['user_id'], $doctor_id, $encrypted_data);
    $stmt->execute();
    $stmt->close();
    echo "Key shared successfully!";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post AES Key Sharing</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="floating-card">
            <h2>Post AES Key Sharing</h2>
            <form method="post">
                <div class="form-group">
                    <label for="doctor_id">Select Doctor:</label>
                    <select class="form-control" id="doctor_id" name="doctor_id">
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?php echo htmlspecialchars($doctor['id']); ?>"><?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="aes_key">AES Key:</label>
                    <input type="text" class="form-control" id="aes_key" name="aes_key" required>
                </div>
                <div class="form-group">
                    <label for="password">Password for Encryption:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Share Key</button>
            </form>
        </div>
    </div>
</body>
</html>
