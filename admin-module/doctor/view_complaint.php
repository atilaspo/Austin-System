<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../config.php');

if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$doctor_id = $_SESSION['user_id'];
$complaints = [];

$sql = "SELECT c.*, ud.first_name, ud.last_name 
        FROM complaints c 
        JOIN users u ON c.user_id = u.id 
        JOIN user_details ud ON u.id = ud.user_id
        WHERE c.doctor_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Prepare error: " . $conn->error);
}

$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $complaints[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Complaints</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container-fluid" id="content" style="margin-top: -40px;">
        <div class="floating-card">
            <h2 class="mt-4">View Complaints</h2>
            <?php if (!empty($complaints)): ?>
                <?php foreach ($complaints as $complaint): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Complaint ID: <?php echo $complaint['id']; ?></h5>
                            <p class="card-text"><strong>Date:</strong> <?php echo $complaint['date']; ?></p>
                            <p class="card-text"><strong>Patient:</strong> <?php echo htmlspecialchars($complaint['first_name'] . ' ' . $complaint['last_name']); ?></p>
                            <p class="card-text"><strong>Complaint:</strong> <?php echo htmlspecialchars($complaint['complaint']); ?></p>
                            <p class="card-text"><strong>Status:</strong> 
                                <?php if ($complaint['status'] == 'Open'): ?>
                                    <span class="badge badge-warning">Open</span>
                                    <a href="doctor_dashboard.php?page=respond_complaint&complaint_id=<?php echo $complaint['id']; ?>" class="btn btn-respond">Respond</a>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Closed</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No complaints found.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
