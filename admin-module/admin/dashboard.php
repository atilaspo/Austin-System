<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Obtener la información del usuario desde la sesión
$user_info = $_SESSION['user_info'];
$user_name = $user_info['username'];
$totalUsers = $user_info['totalUsers'];
$totalDoctors = $user_info['totalDoctors'];
$totalPatients = $user_info['totalPatients'];
$totalCashiers = $user_info['totalCashiers'];
$totalClinics = $user_info['totalClinics'];
$totalAdmins = $user_info['totalAdmins'];
?>
<div class="main-content">
    <div id="dashboard-container" >
        <h2>Welcome, <?php echo $user_name; ?>!</h2>
        <div class="statistics">
            <div class="stat">
                <h3><i class="fas fa-users"></i> Total Users</h3>
                <p><?php echo $totalUsers; ?></p>
            </div>
            <div class="stat">
                <h3><i class="fas fa-user-md"></i> Doctors</h3>
                <p><?php echo $totalDoctors; ?></p>
            </div>
            <div class="stat">
                <h3><i class="fas fa-user-injured"></i> Patients</h3>
                <p><?php echo $totalPatients; ?></p>
            </div>
            <div class="stat">
                <h3><i class="fas fa-cash-register"></i> Cashiers</h3>
                <p><?php echo $totalCashiers; ?></p>
            </div>
            <div class="stat">
                <h3><i class="fas fa-hospital"></i> Clinics</h3>
                <p><?php echo $totalClinics; ?></p>
            </div>
            <div class="stat">
                <h3><i class="fas fa-user-shield"></i> Admins</h3>
                <p><?php echo $totalAdmins; ?></p>
            </div>
        </div>
    </div>
</div>
