<?php
include('../config.php'); // Incluye la conexión a la base de datos

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$doctor_id = $_GET['doctor_id']; // Obtiene el ID del doctor desde la URL
$patient_id = $_SESSION['user_id']; // Obtiene el ID del paciente desde la sesión

$message = "";

// Manejo del envío de reseñas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rating']) && isset($_POST['comment'])) {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $review_sql = "INSERT INTO reviews (doctor_id, patient_id, rating, comment) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($review_sql);
    if (!$stmt) {
        die("Query Error: " . $conn->error);
    }
    $stmt->bind_param("iiis", $doctor_id, $patient_id, $rating, $comment);

    if ($stmt->execute()) {
        $message = "Review submitted successfully.";
    } else {
        $message = "Error submitting review: " . $stmt->error;
    }

    $stmt->close();
}

// Obtiene los detalles del doctor
$sql = "SELECT first_name, last_name, specialty, location, contact, profile_picture 
        FROM user_details 
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

$doctor = $result->fetch_assoc();
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
                        <h4 class="card-title">Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?></h4>
                        <p class="card-text">Specialty: <?php echo htmlspecialchars($doctor['specialty']); ?></p>
                        <p class="card-text">Location: <?php echo htmlspecialchars($doctor['location']); ?></p>
                        <p class="card-text">Contact: <?php echo htmlspecialchars($doctor['contact']); ?></p>
                        <a href="patient_dashboard.php?page=post_complaint&doctor_id=<?php echo $doctor_id; ?>" class="btn btn-primary mt-3">Post a Complaint</a>
                        <a href="patient_dashboard.php?page=book_appointment&doctor_id=<?php echo $doctor_id; ?>" class="btn btn-secondary mt-3">Check Availability</a>
                    </div>
                    <?php 
                        $profile_picture_path = "../uploads/profile" . $doctor_id . ".jpg"; 
                        if (file_exists($profile_picture_path)): ?>
                        <img src="<?php echo htmlspecialchars($profile_picture_path); ?>" alt="Profile Picture" class="profile-picture">
                    <?php endif; ?>
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

            <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#reviewModal">
                Leave a Review
            </button>

            <a href="patient_dashboard.php?page=find_doctor" class="btn btn-secondary mt-3">Back to Find a Doctor</a>
        </div>
    </div>

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Leave a Review</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rating">Rating:</label>
                            <div class="star-rating">
                                <input type="radio" id="5-stars" name="rating" value="5" required /><label for="5-stars" class="star">&#9733;</label>
                                <input type="radio" id="4-stars" name="rating" value="4" /><label for="4-stars" class="star">&#9733;</label>
                                <input type="radio" id="3-stars" name="rating" value="3" /><label for="3-stars" class="star">&#9733;</label>
                                <input type="radio" id="2-stars" name="rating" value="2" /><label for="2-stars" class="star">&#9733;</label>
                                <input type="radio" id="1-star" name="rating" value="1" /><label for="1-star" class="star">&#9733;</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comment">Comment:</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
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
