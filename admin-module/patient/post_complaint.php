<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../config.php');

if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}

if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success" role="alert">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}

$user_id = $_SESSION['user_id'];

$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$complaint = '';
$doctor_id = '';
$success_message = '';
$error_message = '';

// Fetch doctors for dropdown
$doctors = [];
$doctor_sql = "SELECT u.id, d.first_name, d.last_name FROM users u JOIN user_details d ON u.id = d.user_id WHERE u.role_id = 3";
$doctor_result = $conn->query($doctor_sql);

if ($doctor_result) {
    if ($doctor_result->num_rows > 0) {
        while ($row = $doctor_result->fetch_assoc()) {
            $doctors[] = $row;
        }
    } else {
        echo "No doctors found.";
    }
} else {
    echo "Error fetching doctors: " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $complaint = htmlspecialchars($_POST['complaint']);
    $doctor_id = (int)$_POST['doctor_id'];

    $sql = "INSERT INTO complaints (user_id, doctor_id, complaint) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Prepare error: ' . $conn->error);
    }
    $stmt->bind_param("iis", $user_id, $doctor_id, $complaint);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Complaint posted successfully";

        header('Location: patient_dashboard.php?page=post_complaint');
        exit(); 
    } else {
        $error_message = "Error posting complaint: " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Complaint</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="floating-card">
            <h2 class="mt-4">Post Complaint</h2>
            <?php if ($success_message): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <form action="post_complaint.php" method="POST">
                <div class="form-group">
                    <label for="complaint">Complaint:</label>
                    <textarea class="form-control" id="complaint" name="complaint" rows="5" required><?php echo htmlspecialchars($complaint); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="doctor_id">Select Doctor:</label>
                    <select class="form-control" id="doctor_id" name="doctor_id" required>
                        <option value="">Select a Doctor</option>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?php echo $doctor['id']; ?>" <?php echo $doctor_id == $doctor['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Post Complaint</button>
            </form>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
