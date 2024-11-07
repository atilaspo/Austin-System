<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../config.php');

if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

$complaint_id = isset($_GET['complaint_id']) ? $_GET['complaint_id'] : '';
$response = isset($_POST['response']) ? $_POST['response'] : '';
$clinic_details = isset($_POST['clinic_details']) ? $_POST['clinic_details'] : '';
$prescribed_items = isset($_POST['prescribed_items']) ? $_POST['prescribed_items'] : [];

$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtener detalles de la queja y el ID del paciente
$complaint_sql = "SELECT c.*, ud.first_name, ud.last_name 
                  FROM complaints c 
                  JOIN user_details ud ON c.user_id = ud.user_id 
                  WHERE c.id = ?";
$complaint_stmt = $conn->prepare($complaint_sql);
if ($complaint_stmt === false) {
    die('Prepare error: ' . $conn->error);
}
$complaint_stmt->bind_param("i", $complaint_id);
$complaint_stmt->execute();
$complaint_result = $complaint_stmt->get_result();
if ($complaint_result->num_rows == 0) {
    die('Complaint not found.');
}
$complaint = $complaint_result->fetch_assoc();
$patient_id = $complaint['user_id'];
$complaint_stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response_sql = "UPDATE complaints SET response = ?, status = 'Closed', clinic_details = ?, prescribed_items = ? WHERE id = ?";
    $response_stmt = $conn->prepare($response_sql);
    if ($response_stmt === false) {
        die('Prepare error: ' . $conn->error);
    }
    $items_str = implode(", ", $prescribed_items);
    $response_stmt->bind_param("sssi", $response, $clinic_details, $items_str, $complaint_id);
    if ($response_stmt->execute()) {
        foreach ($prescribed_items as $item) {
            // Obtener detalles del ítem
            $item_sql = "SELECT * FROM items WHERE id = ?";
            $item_stmt = $conn->prepare($item_sql);
            if ($item_stmt === false) {
                die('Prepare error: ' . $conn->error);
            }
            $item_stmt->bind_param("i", $item);
            $item_stmt->execute();
            $item_result = $item_stmt->get_result();
            if ($item_result->num_rows > 0) {
                $item_details = $item_result->fetch_assoc();
                $charge_sql = "INSERT INTO charges (patient_id, doctor_id, complaint_id, description, amount, status) VALUES (?, ?, ?, ?, ?, 'pending')";
                $charge_stmt = $conn->prepare($charge_sql);
                if ($charge_stmt === false) {
                    die('Prepare error: ' . $conn->error);
                }
                $charge_stmt->bind_param("iiisd", $patient_id, $_SESSION['user_id'], $complaint_id, $item_details['name'], $item_details['cost']);
                $charge_stmt->execute();
                $charge_stmt->close();
            }
            $item_stmt->close();
        }

        // Generar el reporte
        $report_content = "Prescribed items:\n";
        foreach ($prescribed_items as $item) {
            $item_sql = "SELECT * FROM items WHERE id = ?";
            $item_stmt = $conn->prepare($item_sql);
            if ($item_stmt === false) {
                die('Prepare error: ' . $conn->error);
            }
            $item_stmt->bind_param("i", $item);
            $item_stmt->execute();
            $item_result = $item_stmt->get_result();
            if ($item_result->num_rows > 0) {
                $item_details = $item_result->fetch_assoc();
                $report_content .= $item_details['name'] . " - $" . $item_details['cost'] . "\n";
            }
            $item_stmt->close();
        }
        $report_sql = "INSERT INTO reports (patient_id, doctor_id, complaint_id, report) VALUES (?, ?, ?, ?)";
        $report_stmt = $conn->prepare($report_sql);
        if ($report_stmt === false) {
            die('Prepare error: ' . $conn->error);
        }
        $report_stmt->bind_param("iiis", $patient_id, $_SESSION['user_id'], $complaint_id, $report_content);
        $report_stmt->execute();
        $report_stmt->close();

        //header("Location: doctor_dashboard.php?page=view_complaint");
        exit();
    } else {
        echo "Error: " . $response_stmt->error;
    }
    $response_stmt->close();
}

$clinics = [];
$clinic_sql = "SELECT * FROM clinics WHERE doctor_id = ?";
$clinic_stmt = $conn->prepare($clinic_sql);
if ($clinic_stmt === false) {
    die('Prepare error: ' . $conn->error);
}
$clinic_stmt->bind_param("i", $_SESSION['user_id']);
$clinic_stmt->execute();
$clinic_result = $clinic_stmt->get_result();
while ($row = $clinic_result->fetch_assoc()) {
    $clinics[] = $row;
}
$clinic_stmt->close();

$items = [];
$item_sql = "SELECT * FROM items";
$item_result = $conn->query($item_sql);
while ($row = $item_result->fetch_assoc()) {
    $items[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respond to Complaint</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container-fluid" id="content" style="margin-top: -40px;">
        <div class="floating-card">
            <h2 class="mt-4">Respond to Complaint</h2>
            <form method="post">
                <div class="form-group">
                    <label for="complaint">Complaint:</label>
                    <p><?php echo htmlspecialchars($complaint['complaint']); ?></p>
                </div>
                <div class="form-group">
                    <label for="response">Response:</label>
                    <textarea class="form-control" id="response" name="response" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label for="clinic_details">Clinic:</label>
                    <select class="form-control" id="clinic_details" name="clinic_details">
                        <?php foreach ($clinics as $clinic): ?>
                            <option value="<?php echo htmlspecialchars($clinic['clinic_name'] . ' - ' . $clinic['clinic_address']); ?>"><?php echo htmlspecialchars($clinic['clinic_name'] . ' - ' . $clinic['clinic_address']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="prescribed_items">Prescribed Items:</label>
                    <select class="form-control" id="prescribed_items" name="prescribed_items[]" multiple>
                        <?php foreach ($items as $item): ?>
                            <option value="<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['name'] . ' ($' . $item['cost'] . ')'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-respond">Submit Response</button>
            </form>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
