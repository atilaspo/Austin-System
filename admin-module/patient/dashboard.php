<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fetch user information from session
$user_info = isset($_SESSION['user_info']) ? $_SESSION['user_info'] : ['username' => 'Guest', 'role' => 'Unknown', 'totalComplaints' => 0, 'openComplaints' => 0, 'totalReports' => 0, 'totalAESKeysShared' => 0];
$username = htmlspecialchars($user_info['username']);
$role = htmlspecialchars($user_info['role']);
$totalComplaints = $user_info['totalComplaints'];
$openComplaints = $user_info['openComplaints'];
$totalReports = $user_info['totalReports'];
$totalAESKeysShared = $user_info['totalAESKeysShared'];
?>
<div class="dashboard-container">
    <h1>Welcome, <?php echo $username; ?>! <i class="fas fa-id-badge user-icon"></i></h1>
    <br>
    <br>
    <div class="statistics">
        <div class="stat">
            <h3><i class="fas fa-exclamation-circle"></i> Open Complaints</h3>
            <p><?php echo $openComplaints; ?></p>
        </div>
        <div class="stat">
            <h3><i class="fas fa-clipboard"></i> Total Complaints</h3>
            <p><?php echo $totalComplaints; ?></p>
        </div>
        <div class="stat">
            <h3><i class="fas fa-file-alt"></i> Total Reports</h3>
            <p><?php echo $totalReports; ?></p>
        </div>
        <div class="stat">
            <h3><i class="fas fa-key"></i> AES Keys Shared</h3>
            <p><?php echo $totalAESKeysShared; ?></p>
        </div>
    </div>
</div>
