<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('../config.php');

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 4) {
    header('Location: ../login.php');
    exit();
}

// Fetch user information
$user_id = $_SESSION['user_id'];  // Assuming user_id is stored in session
$user_name = 'Clinic';

// Fetch user details from the database
$query = "SELECT first_name, last_name FROM user_details WHERE user_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($first_name, $last_name);
    if ($stmt->fetch()) {
        $user_name = $first_name . ' ' . $last_name;
    }
    $stmt->close();
}

// Fetch total reports
$totalReports = 0;
$query = "SELECT COUNT(*) FROM reports";
if ($stmt = $conn->prepare($query)) {
    $stmt->execute();
    $stmt->bind_result($totalReports);
    $stmt->fetch();
    $stmt->close();
}

// Set user info in session
$user_info = [
    'username' => $user_name,
    'role' => 'Clinic',
    'total_reports' => $totalReports,
];
$_SESSION['user_info'] = $user_info;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div id="wrapper">

        <!-- Page Content -->
        <div>
            <div class="container-fluid" style="margin-top: -40px;">
                <?php 
                // Pass user information to the included page
                $user_info = [
                    'username' => $user_name,
                    'role' => 'Clinic',
                    'total_reports' => $totalReports
                ];
                $_SESSION['user_info'] = $user_info;
                ?>

                <div class="dashboard-container">
                    <h1>Welcome, <?php echo htmlspecialchars($user_info['username']); ?>! <i class="fas fa-hospital user-icon"></i></h1>
                    <br>
                    <br>
                    <div class="statistics">
                        <div class="stat">
                            <h3><i class="fas fa-file-alt"></i> Total Reports</h3>
                            <p><?php echo $user_info['total_reports']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
