<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../config.php');

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

$doctor_id = $_SESSION['user_id'];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $sql = "INSERT INTO availability (doctor_id, date, start_time, end_time) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $doctor_id, $date, $start_time, $end_time);

    if ($stmt->execute()) {
        $message = "Availability set successfully!";
    } else {
        $message = "Error setting availability.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Specific Availability</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Set Specific Availability</h2>

        <?php if (!empty($message)) : ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        
        <form method="POST" action="doctor_dashboard.php?page=set_availability">
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" name="date" id="date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="start_time">Start Time</label>
                <input type="time" name="start_time" id="start_time" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="end_time">End Time</label>
                <input type="time" name="end_time" id="end_time" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Set Availability</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
