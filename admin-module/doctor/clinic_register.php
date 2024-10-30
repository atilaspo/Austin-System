<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../config.php');

if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

// Handle clinic registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['clinic_name'])) {
    $clinic_name = $_POST['clinic_name'];
    $clinic_address = $_POST['clinic_address'];
    $clinic_contact = $_POST['clinic_contact'];

    $conn = new mysqli($servername, $username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO clinics (clinic_name, clinic_address, clinic_contact, doctor_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Prepare error: ' . $conn->error);
    }
    $stmt->bind_param("sssi", $clinic_name, $clinic_address, $clinic_contact, $_SESSION['user_id']);
    if ($stmt->execute()) {
        $success_message = "Clinic registered successfully.";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}

// Handle clinic deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_clinic_id'])) {
    $clinic_id = $_POST['delete_clinic_id'];

    $conn = new mysqli($servername, $username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "DELETE FROM clinics WHERE id = ? AND doctor_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Prepare error: ' . $conn->error);
    }
    $stmt->bind_param("ii", $clinic_id, $_SESSION['user_id']);
    if ($stmt->execute()) {
        $delete_success_message = "Clinic deleted successfully.";
    } else {
        $delete_error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}

// Handle clinic search
$search_query = "";
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

$conn = new mysqli($servername, $username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, clinic_name, clinic_address, clinic_contact FROM clinics WHERE doctor_id = ? AND clinic_name LIKE ?";
$stmt = $conn->prepare($sql);
$search_param = "%" . $search_query . "%";
$stmt->bind_param("is", $_SESSION['user_id'], $search_param);
$stmt->execute();
$result = $stmt->get_result();
$clinics = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Clinic</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
<div class="container-fluid" id="content" style="margin-top: -40px;">
    <div class="floating-card">
        <h2 class="mt-4">Register Clinic</h2>
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <br>
        <form method="POST" action="doctor_dashboard.php?page=clinic_register">
            <div class="form-group">
                <label for="clinic_name">Clinic Name:</label>
                <input type="text" class="form-control" id="clinic_name" name="clinic_name" required>
            </div>
            <div class="form-group">
                <label for="clinic_address">Clinic Address:</label>
                <textarea class="form-control" id="clinic_address" name="clinic_address" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="clinic_contact">Clinic Contact:</label>
                <input type="text" class="form-control" id="clinic_contact" name="clinic_contact" required>
            </div>
            <button type="submit" class="btn btn-respond">Register Clinic</button>
        </form>
    </div>

    <div class="floating-card mt-5">
        <h2>Registered Clinics</h2>
        <?php if (isset($delete_success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $delete_success_message; ?>
            </div>
        <?php elseif (isset($delete_error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $delete_error_message; ?>
            </div>
        <?php endif; ?>
        <form method="GET" action="doctor_dashboard.php">
            <div class="form-group">
                <input type="hidden" name="page" value="clinic_register">
                <label for="search">Search by Clinic Name:</label>
                <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
            </div>
            <button type="submit" class="btn btn-respond">Search</button>
            <a href="doctor_dashboard.php?page=clinic_register" class="btn btn-secondary">Reset</a>
        </form>

        <?php if (count($clinics) > 0): ?>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Clinic Name</th>
                        <th>Clinic Address</th>
                        <th>Clinic Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clinics as $clinic): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($clinic['clinic_name']); ?></td>
                            <td><?php echo htmlspecialchars($clinic['clinic_address']); ?></td>
                            <td><?php echo htmlspecialchars($clinic['clinic_contact']); ?></td>
                            <td>
                                <form method="POST" action="doctor_dashboard.php?page=clinic_register" style="display:inline;">
                                    <input type="hidden" name="delete_clinic_id" value="<?php echo $clinic['id']; ?>">
                                    <button type="submit" class="btn btn-respond">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No clinics found.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
