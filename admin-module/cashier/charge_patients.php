<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../config.php');

if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 5) {
    header('Location: ../login.php');
    exit();
}

$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$charges = [];

$sql = "SELECT c.id, ud.first_name, ud.last_name, c.description, c.amount, c.status 
        FROM charges c 
        JOIN users u ON c.patient_id = u.id 
        JOIN user_details ud ON u.id = ud.user_id 
        WHERE c.status = 'pending'";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $charges[] = $row;
        }
    }
} else {
    echo "Error in query: " . $conn->error;
}

$conn->close();
?>
<div class="container mt-4 floating-card">
    <h2 class="mt-4">Charge Patients</h2>
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php elseif (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>
    <?php if (!empty($charges)): ?>
        <ul class="list-group">
            <?php foreach ($charges as $charge): ?>
                <li class="list-group-item floating-card mb-3">
                    <h5>Charge for <?php echo htmlspecialchars($charge['first_name'] . ' ' . $charge['last_name']); ?></h5>
                    <p>Description: <?php echo htmlspecialchars($charge['description']); ?></p>
                    <p>Amount: $<?php echo htmlspecialchars($charge['amount']); ?></p>
                    <form action="process_payment.php" method="POST">
                        <input type="hidden" name="charge_id" value="<?php echo $charge['id']; ?>">
                        <div class="form-group">
                            <label for="payment_method">Select Payment Method:</label>
                            <select name="payment_method" class="form-control" id="payment_method">
                                <option value="Debit Card">Debit Card</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="PayPal">PayPal</option>
                                <option value="Apple Pay">Apple Pay</option>
                                <option value="Google Pay">Google Pay</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Process Payment</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No pending charges found.</p>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
