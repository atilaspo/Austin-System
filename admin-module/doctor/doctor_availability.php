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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Availability Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class='floating-card'>
            <h2 class="text-center">Manage Your Availability</h2>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Create Availability Template</h5>
                            <p class="card-text">Set up recurring availability patterns to be applied later.</p>
                            <a href="doctor_dashboard.php?page=save_template" class="btn btn-primary">Create Template</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Apply Availability Template</h5>
                            <p class="card-text">Apply a saved template to generate availability slots.</p>
                            <a href="doctor_dashboard.php?page=apply_template" class="btn btn-primary">Apply Template</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">View Current Availability</h5>
                            <p class="card-text">View and manage your current availability slots.</p>
                            <a href="doctor_dashboard.php?page=view_availability" class="btn btn-primary">View Availability</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Set Specific Availability</h5>
                            <p class="card-text">Manually set availability for specific dates and times.</p>
                            <a href="doctor_dashboard.php?page=set_availability" class="btn btn-primary">Set Availability</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Clear Availability</h5>
                            <p class="card-text">Remove all availability slots within a selected date range.</p>
                            <a href="clear_availability.php" class="btn btn-danger">Clear Availability</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
