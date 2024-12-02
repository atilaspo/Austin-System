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

// Variables para la búsqueda por fecha
$search_date = "";
$sql = "SELECT date, start_time, end_time, is_available FROM availability WHERE doctor_id = ? AND date >= CURDATE()";

if (isset($_GET['search_date']) && !empty($_GET['search_date'])) {
    $search_date = $_GET['search_date'];
    $sql .= " AND date = ?";
}

$sql .= " ORDER BY date, start_time LIMIT 20";
$stmt = $conn->prepare($sql);

if ($search_date) {
    $stmt->bind_param("is", $doctor_id, $search_date);
} else {
    $stmt->bind_param("i", $doctor_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Availability</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Your Current Availability</h2>

        <!-- Formulario de búsqueda por fecha -->
        <form method="GET" action="view_availability.php" class="form-inline mb-3">
            <input type="date" name="search_date" class="form-control mr-sm-2" value="<?php echo htmlspecialchars($search_date); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <!-- Botón para volver a la vista de disponibilidad -->
        <a href="doctor_dashboard.php?page=doctor_availability" class="btn btn-secondary mb-3">Back to Availability</a>

        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $formatted_date = date("d-m-Y", strtotime($row['date']));
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($formatted_date) . "</td>";
                        echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['end_time']) . "</td>";
                        echo "<td>" . ($row['is_available'] ? 'Available' : 'Booked') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No availability found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
