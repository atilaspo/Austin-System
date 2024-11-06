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

// Fetch user information
$user_id = $_SESSION['user_id'];  // Assuming user_id is stored in session
$role = 'Doctor';  // Since the role ID is 3, we know this user is a doctor
$user_name = 'Doctor';

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

// Fetch totals specific to the doctor
$total_complaints = 0;
$total_clinics = 0;
$total_aes_keys = 0;

// Total complaints for the logged-in doctor
$query = "SELECT COUNT(*) FROM complaints WHERE status = 'open' AND doctor_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($total_complaints);
    $stmt->fetch();
    $stmt->close();
}

// Total clinics registered by the logged-in doctor
$query = "SELECT COUNT(*) FROM clinics WHERE doctor_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($total_clinics);
    $stmt->fetch();
    $stmt->close();
}


// Total AES keys shared by the logged-in doctor
$query = "SELECT COUNT(*) FROM aes_keys WHERE doctor_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($total_aes_keys);
    $stmt->fetch();
    $stmt->close();
}



$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Sidebar para pantallas grandes */
        #sidebar-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #343a40;
            padding-top: 10px;
        }

        /* Ocultar sidebar en móviles */
        @media (max-width: 768px) {
            #sidebar-wrapper {
                display: none;
            }
        }

        /* Mostrar el contenido principal con margen en pantallas grandes */
        @media (min-width: 769px) {
            #page-content-wrapper {
                margin-left: 222px;
                width: calc(100% - 222px);
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar en pantallas grandes -->
    <div id="wrapper">
        <div class="bg-white d-none d-md-block" id="sidebar-wrapper">
            <div><img src="../assets/logo.png" alt="Austin Health Logo" width="230" class="logos"></div>
            <div class="list-group list-group-flush">
                <a href="doctor_dashboard.php?page=dashboard" class="list-group-item list-group-item-action bg-white">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="doctor_dashboard.php?page=doctor_profile" class="list-group-item list-group-item-action bg-white">
                    <i class="fas fa-user-md"></i> My Profile
                </a>
                <a href="doctor_dashboard.php?page=update_details" class="list-group-item list-group-item-action bg-white">
                    <i class="fas fa-user-edit"></i> Edit Profile
                </a>
                <a href="doctor_dashboard.php?page=view_complaint" class="list-group-item list-group-item-action bg-white">
                    <i class="fas fa-exclamation-circle"></i> View Complaint
                </a>
                <a href="doctor_dashboard.php?page=clinic_register" class="list-group-item list-group-item-action bg-white">
                    <i class="fas fa-hospital"></i> Clinic Register
                </a>
                <a href="doctor_dashboard.php?page=view_aes_key_sharing" class="list-group-item list-group-item-action bg-white">
                    <i class="fas fa-key"></i> View AES Key Sharing
                </a>
                <a href="doctor_dashboard.php?page=view_appointments" class="list-group-item list-group-item-action bg-white">
                    <i class="fas fa-calendar-alt"></i> Appointments
                </a>
                <a href="doctor_dashboard.php?page=view_accepted_appointments" class="list-group-item list-group-item-action bg-white">
                    <i class="fas fa-calendar-alt"></i> Accepted Appointments
                </a>
                <a href="doctor_dashboard.php?page=doctor_availability" class="list-group-item list-group-item-action bg-white">
                    <i class="fas fa-clock"></i> My Availability
                </a>
                <a href="../logout.php" class="list-group-item list-group-item-action bg-white text-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Navbar para móviles -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark d-md-none">
            <a class="navbar-brand" href="#">Doctor Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="doctor_dashboard.php?page=dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="doctor_dashboard.php?page=doctor_profile">My Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="doctor_dashboard.php?page=update_details">Edit Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="doctor_dashboard.php?page=view_complaint">View Complaint</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="doctor_dashboard.php?page=clinic_register">Clinic Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="doctor_dashboard.php?page=view_aes_key_sharing">View AES Key Sharing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="doctor_dashboard.php?page=view_appointments">Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="doctor_dashboard.php?page=doctor_availability">My Availability</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container" style="margin-left: 20px; margin-top: 40px;">
                <?php 
                // Pass user information to the included page
                $user_info = [
                    'username' => $user_name, 
                    'role' => $role, 
                    'total_complaints' => $total_complaints, 
                    'total_clinics' => $total_clinics, 
                    'total_aes_keys' => $total_aes_keys
                ];
                $_SESSION['user_info'] = $user_info;

                // Include the selected page or show a 404 message
                if (file_exists($page . '.php')) {
                    include($page . '.php');
                } else {
                    echo "<h2>Page not found</h2>";
                }
                ?>
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
