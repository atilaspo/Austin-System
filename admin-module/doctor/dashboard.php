<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fetch user information from session
$user_info = $_SESSION['user_info'];
$user_name = $user_info['username'];
$role = htmlspecialchars($user_info['role']);
$total_complaints = $user_info['total_complaints'];
$total_clinics = $user_info['total_clinics'];
$total_aes_keys = $user_info['total_aes_keys'];
?>
<div class="dashboard-container">
    <h1>Welcome, <?php echo $user_name; ?>! <i class="fas fa-user-md user-icon"></i></h1>
    <br>
    <br>
    <div class="statistics">
        <div class="stat">
            <h3><i class="fas fa-exclamation-circle"></i> Open Complaints</h3>
            <p><?php echo $total_complaints; ?></p>
        </div>
        <div class="stat">
            <h3><i class="fas fa-hospital"></i> Registered Clinics</h3>
            <p><?php echo $total_clinics; ?></p>
        </div>
        <div class="stat">
            <h3><i class="fas fa-key"></i> AES Keys Shared</h3>
            <p><?php echo $total_aes_keys; ?></p>
        </div>
    </div>
</div>
