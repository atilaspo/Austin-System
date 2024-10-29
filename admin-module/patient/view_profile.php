<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('../config.php');

if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
if (!$user_id) {
    die("User ID is not set in session.");
}

$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT first_name, last_name, location, contact FROM user_details WHERE user_id=?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare error: ' . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $location, $contact);
$stmt->fetch();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-details {
            list-style: none;
            padding: 0;
        }
        .profile-details li {
            margin-bottom: 10px;
            padding: 10px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .profile-details li span {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <h2><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>'s Profile</h2>
        </div>
        <ul class="profile-details">
            <li><span>First Name:</span> <?php echo htmlspecialchars($first_name); ?></li>
            <li><span>Last Name:</span> <?php echo htmlspecialchars($last_name); ?></li>
            <li><span>Location:</span> <?php echo htmlspecialchars($location); ?></li>
            <li><span>Contact:</span> <?php echo htmlspecialchars($contact); ?></li>
        </ul>
        <div class="text-center mt-4">
            <a href="patient_dashboard.php?page=update_details" class="btn btn-primary">Edit Details</a>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
