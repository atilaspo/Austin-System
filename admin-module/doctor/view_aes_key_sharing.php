<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('../config.php');

// Verificar si el usuario ha iniciado sesión y si es un doctor
if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

$doctor_id = $_SESSION['user_id'];

$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtener las claves AES compartidas con este doctor
$query = "SELECT ak.id, ak.aes_key, ud.first_name, ud.last_name 
          FROM aes_keys ak 
          JOIN users u ON ak.patient_id = u.id 
          JOIN user_details ud ON u.id = ud.user_id 
          WHERE ak.doctor_id = ?";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die("Prepare error: " . $conn->error);
}

$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View AES Key Sharing</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container" id="content" style="margin-top: -40px;">
        <div class="floating-card">
            <h2 class="mt-4">View AES Key Sharing</h2>
            <br>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Key ID: <?php echo htmlspecialchars($row['id']); ?></h5>
                            <p class="card-text"><strong>Patient:</strong> <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></p>
                            <form method="post">
                                <div class="form-group">
                                    <label for="password_<?php echo htmlspecialchars($row['id']); ?>">Password for Decryption:</label>
                                    <input type="password" class="form-control" id="password_<?php echo htmlspecialchars($row['id']); ?>" name="password">
                                </div>
                                <button type="submit" class="btn btn-respond" name="decrypt" value="<?php echo htmlspecialchars($row['id']); ?>">Decrypt Key</button>
                            </form>
                            <?php
                            if (isset($_POST['decrypt']) && $_POST['decrypt'] == $row['id']) {
                                $password = $_POST['password'];
                                list($encrypted_key, $iv) = explode('::', base64_decode($row['aes_key']), 2);
                                $decrypted_key = openssl_decrypt($encrypted_key, 'aes-256-cbc', $password, 0, $iv);
                                echo '<p class="card-text"><strong>Decrypted AES Key:</strong> ' . htmlspecialchars($decrypted_key) . '</p>';
                            }
                            ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No keys shared with you.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
