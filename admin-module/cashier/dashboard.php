<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fetch user information from session
$user_info = isset($_SESSION['user_info']) ? $_SESSION['user_info'] : ['username' => 'Guest', 'role' => 'Unknown', 'totalChargesPaid' => 0, 'totalChargesUnpaid' => 0];
$username = htmlspecialchars($user_info['username']);
$role = htmlspecialchars($user_info['role']);
$totalChargesPaid = $user_info['totalChargesPaid'];
$totalChargesUnpaid = $user_info['totalChargesUnpaid'];
?>
<div class="dashboard-container">
    <h1>Welcome, <?php echo $username; ?>! <i class="fas fa-cash-register user-icon"></i></h1>
    <br>
    <br>
    <div class="statistics">
        <div class="stat">
            <h3><i class="fas fa-money-check-alt"></i> Total Charges Paid</h3>
            <p><?php echo $totalChargesPaid; ?></p>
        </div>
        <div class="stat">
            <h3><i class="fas fa-money-bill-wave"></i> Total Charges Unpaid</h3>
            <p><?php echo $totalChargesUnpaid; ?></p>
        </div>
    </div>
</div>
