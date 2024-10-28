<?php
include '../config.php'; // Incluye la conexión a la base de datos

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$patient_id = $_SESSION['user_id']; // Obtiene el ID del paciente desde la sesión

$message = "";

// Manejo de la subida de fotos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $profile_picture = $_FILES['profile_picture'];

    if ($profile_picture['error'] == 0) {
        $fileName = $profile_picture['name'];
        $fileTmpName = $profile_picture['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png');

        if (in_array($fileExt, $allowed)) {
            $fileNameNew = "profile".$patient_id.".".$fileExt;
            $fileDestination = '../uploads/'.$fileNameNew;

            // Mueve el archivo subido a la ubicación deseada
            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                $message = "The profile picture has been uploaded.";

                // Actualiza la ruta de la imagen después de subirla
                $imagePath = $fileDestination;
            } else {
                $message = "Failed to move uploaded file.";
            }
        } else {
            $message = "Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
        }
    } else {
        $message = "Error uploading file.";
    }
}

$imagePath = "../uploads/profile$patient_id.jpg";
if (!file_exists($imagePath)) {
    $imagePath = '../uploads/default.png'; // Ruta a una imagen predeterminada si no hay foto de perfil
}

// Obtiene los detalles del paciente
$sql = "SELECT d.first_name, d.last_name, d.location, d.contact 
        FROM user_details d 
        WHERE d.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$patient = $result->fetch_assoc();
$stmt->close();

// Verifica si las variables están establecidas antes de mostrarlas
$first_name = isset($patient['first_name']) ? htmlspecialchars($patient['first_name']) : "Not Available";
$last_name = isset($patient['last_name']) ? htmlspecialchars($patient['last_name']) : "Not Available";
$location = isset($patient['location']) ? htmlspecialchars($patient['location']) : "Not Available";
$contact = isset($patient['contact']) ? htmlspecialchars($patient['contact']) : "Not Available";

// Obtiene las quejas del paciente
$complaint_sql = "SELECT c.id, c.complaint, c.date, c.status 
                  FROM complaints c 
                  WHERE c.user_id = ?";
$stmt = $conn->prepare($complaint_sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$complaints_result = $stmt->get_result();
$stmt->close();

// Obtiene los informes del paciente
$report_sql = "SELECT r.id, r.report, r.date, d.first_name AS doctor_first_name, d.last_name AS doctor_last_name 
               FROM reports r 
               JOIN users u ON r.doctor_id = u.id 
               JOIN user_details d ON u.id = d.user_id 
               WHERE r.patient_id = ?";
$stmt = $conn->prepare($report_sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$reports_result = $stmt->get_result();
$stmt->close();

// Obtiene las citas del paciente
$appointment_sql = "SELECT a.id, a.created_at, a.status, d.first_name AS doctor_first_name, d.last_name AS doctor_last_name 
                    FROM appointments a 
                    JOIN users u ON a.doctor_id = u.id 
                    JOIN user_details d ON u.id = d.user_id 
                    WHERE a.patient_id = ?";
$stmt = $conn->prepare($appointment_sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$appointments_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .profile-container {
            display: flex;
            align-items: center;
        }
        .profile-info {
            flex: 1;
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-left: 20px;
        }
        .modal-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-5">
        <div class="floating-card">
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <h2>Patient Profile</h2>
            <div class="card">
                <div class="card-body profile-container">
                    <div class="profile-info">
                        <h4 class="card-title"><?php echo "$first_name $last_name"; ?></h4>
                        <p class="card-text">Location: <?php echo $location; ?></p>
                        <p class="card-text">Contact: <?php echo $contact; ?></p>
                        <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#uploadModal">
                            Upload Profile Picture
                        </button>
                    </div>
                        <img src="<?php echo $imagePath; ?>" alt="Profile Picture" class="profile-picture">
                    </div>
                </div>
            </div>
            <h3 class="mt-5">Complaints</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Complaint</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($complaints_result->num_rows > 0) {
                        while ($row = $complaints_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['complaint'] . "</td>";
                            echo "<td>" . $row['date'] . "</td>";
                            echo "<td>" . $row['status'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>No complaints</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <h3 class="mt-5">Reports</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Report</th>
                        <th>Date</th>
                        <th>Doctor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($reports_result->num_rows > 0) {
                        while ($row = $reports_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['report'] . "</td>";
                            echo "<td>" . $row['date'] . "</td>";
                            echo "<td>Dr. " . $row['doctor_first_name'] . " " . $row['doctor_last_name'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>No reports</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <h3 class="mt-5">Appointments</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Doctor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($appointments_result->num_rows > 0) {
                        while ($row = $appointments_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['created_at'] . "</td>";
                            echo "<td>" . $row['status'] . "</td>";
                            echo "<td>Dr. " . $row['doctor_first_name'] . " " . $row['doctor_last_name'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>No appointments</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <a href="patient_dashboard.php" class="btn btn-primary mt-3">Back to Dashboard</a>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Profile Picture</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="profile_picture">Choose File:</label>
                            <input type="file" name="profile_picture" id="profile_picture" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload Picture</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
