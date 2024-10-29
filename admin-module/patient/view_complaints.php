<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}
include('../config.php');

$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$complaints = [];

$sql = "SELECT c.id, c.complaint, c.date, c.response, c.status, c.clinic_details, c.prescribed_items 
        FROM complaints c 
        WHERE c.user_id = ?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        // Explode the prescribed_items string into an array
        $item_ids = explode(',', $row['prescribed_items']);
        $items = [];
        foreach ($item_ids as $item_id) {
            $item_id = trim($item_id);
            $item_sql = "SELECT name, cost FROM items WHERE id = ?";
            if ($item_stmt = $conn->prepare($item_sql)) {
                $item_stmt->bind_param("i", $item_id);
                $item_stmt->execute();
                $item_result = $item_stmt->get_result();
                if ($item_row = $item_result->fetch_assoc()) {
                    $items[] = $item_row['name'] . " ($" . $item_row['cost'] . ")";
                }
                $item_stmt->close();
            }
        }
        $row['items'] = $items;
        $complaints[] = $row;
    }
    $stmt->close();
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
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="floating-card">
            <h1 class="mt-4">View Complaints</h1>
            <?php foreach ($complaints as $complaint): ?>
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Complaint ID: <?php echo htmlspecialchars($complaint['id']); ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">Date: <?php echo htmlspecialchars($complaint['date']); ?></h6>
                        <p class="card-text"><strong>Complaint:</strong> <?php echo htmlspecialchars($complaint['complaint']); ?></p>
                        <?php if ($complaint['response']): ?>
                            <p class="card-text"><strong>Response:</strong> <span class="text-success"><?php echo htmlspecialchars($complaint['response']); ?></span></p>
                            <p class="card-text"><strong>Clinic Details:</strong> <?php echo htmlspecialchars($complaint['clinic_details']); ?></p>
                            <p class="card-text"><strong>Prescribed Items:</strong>
                                <ul>
                                    <?php foreach ($complaint['items'] as $item): ?>
                                        <li><?php echo htmlspecialchars($item); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </p>
                        <?php endif; ?>
                        <p class="card-text"><strong>Status:</strong> <?php echo htmlspecialchars($complaint['status']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
