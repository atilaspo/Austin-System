<?php
include('../config.php'); // Incluye la conexión a la base de datos

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$doctor_id = $_SESSION['user_id']; // Obtiene el ID del doctor desde la sesión

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
            $fileNameNew = "profile" . $doctor_id . "." . $fileExt;
            $fileDestination = '../uploads/' . $fileNameNew;

            // Elimina la imagen anterior si existe
            if (file_exists($fileDestination)) {
                unlink($fileDestination);
            }

            // Mueve el archivo subido a la ubicación deseada
            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                $message = "Profile picture updated successfully.";
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

// Construye la ruta de la imagen basada en el ID del doctor y le agrega un timestamp para evitar el caché
$imagePath = "../uploads/profile$doctor_id.jpg?" . time();

// Obtiene los detalles del doctor
$sql = "SELECT first_name, last_name, specialty, location, contact 
        FROM user_details 
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

$doctor = $result->fetch_assoc() ?? []; // Si no hay resultados, se asigna un array vacío
$stmt->close();

// Obtiene las reseñas del doctor
$review_sql = "SELECT r.rating, r.comment, ud.first_name AS patient_first_name, ud.last_name AS patient_last_name 
               FROM reviews r 
               JOIN user_details ud ON r.patient_id = ud.user_id 
               WHERE r.doctor_id = ?";
$stmt = $conn->prepare($review_sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$reviews_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Profile</title>
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
        .star-rating {
            display: flex;
            direction: row-reverse;
            font-size: 2rem;
            justify-content: center;
            padding: 10px;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            color: #ccc;
            cursor: pointer;
        }
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #f5c518;
        }
        .star {
            font-size: 20px; 
            color: #f5c518; 
        }
        .star.empty {
            color: #ccc;   
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="floating-card">
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <h2>Doctor Profile</h2>
            <div class="card">
                <div class="card-body profile-container">
                    <div class="profile-info">
                        <h4 class="card-title">Dr. <?php echo htmlspecialchars(($doctor['first_name'] ?? 'Doctor') . ' ' . ($doctor['last_name'] ?? '')); ?></h4>
                        <p class="card-text">Specialty: <?php echo htmlspecialchars($doctor['specialty'] ?? 'N/A'); ?></p>
                        <p class="card-text">Location: <?php echo htmlspecialchars($doctor['location'] ?? 'N/A'); ?></p>
                        <p class="card-text">Contact: <?php echo htmlspecialchars($doctor['contact'] ?? 'N/A'); ?></p>
                        <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#uploadModal">
                            Upload Profile Picture
                        </button>
                    </div>
                    <img src="<?php echo $imagePath; ?>" alt="Profile Picture" class="profile-picture">
                </div>
            </div>
            <h3 class="mt-5">Reviews</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Rating</th>
                        <th>Comment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($reviews_result->num_rows > 0) {
                        while ($row = $reviews_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['patient_first_name'] . " " . $row['patient_last_name']) . "</td>";
                            echo "<td>";
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $row['rating']) {
                                    echo '<span class="star">&#9733;</span>'; // Estrella llena
                                } else {
                                    echo '<span class="star">&#9734;</span>'; // Estrella vacía
                                }
                            }
                            echo "</td>";
                            echo "<td>" . htmlspecialchars($row['comment']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>No reviews</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
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
