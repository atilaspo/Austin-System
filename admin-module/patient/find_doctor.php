<?php
include('../config.php');

// Verificar si el usuario ha iniciado sesión y si es un paciente
if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}
$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtener especialidades únicas de la base de datos
$specialty_sql = "SELECT DISTINCT specialty FROM user_details WHERE specialty IS NOT NULL AND specialty != ''";
$specialty_result = $conn->query($specialty_sql);

$specialties = [];
if ($specialty_result->num_rows > 0) {
    while ($row = $specialty_result->fetch_assoc()) {
        $specialties[] = $row['specialty'];
    }
}

// Inicializar las variables
$specialty = isset($_POST['specialty']) ? htmlspecialchars($_POST['specialty']) : '';
$gender = isset($_POST['gender']) ? htmlspecialchars($_POST['gender']) : '';
$results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT u.id AS userId, d.first_name, d.last_name, d.specialty, d.gender, d.location, d.contact,
                   (SELECT AVG(rating) FROM reviews WHERE doctor_id = u.id) AS avg_rating
            FROM users u
            JOIN user_details d ON u.id = d.user_id
            WHERE u.role_id = 3";

    $conditions = [];
    if (!empty($specialty)) {
        $conditions[] = "d.specialty = '$specialty'";
    }
    if (!empty($gender)) {
        $conditions[] = "d.gender = '$gender'";
    }
    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }
} else {
    $sql = "SELECT u.id AS userId, d.first_name, d.last_name, d.specialty, d.gender, d.location, d.contact,
                   (SELECT AVG(rating) FROM reviews WHERE doctor_id = u.id) AS avg_rating
            FROM users u
            JOIN user_details d ON u.id = d.user_id
            WHERE u.role_id = 3";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
}

$conn->close();
?>

<div class="container-fluid">
    <div class="floating-card">
        <h2 class="mt-4">Find Doctor</h2>
        <form action="patient_dashboard.php?page=find_doctor" method="POST">
            <div class="form-group">
                <label for="specialty">Specialty:</label>
                <select class="form-control" id="specialty" name="specialty">
                    <option value="">Select Specialty</option>
                    <?php foreach ($specialties as $spec): ?>
                        <option value="<?php echo $spec; ?>" <?php if ($spec == $specialty) echo 'selected'; ?>><?php echo $spec; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select class="form-control" id="gender" name="gender">
                    <option value="">Select Gender</option>
                    <option value="Male" <?php if ($gender == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($gender == 'Female') echo 'selected'; ?>>Female</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="patient_dashboard.php?page=find_doctor" class="btn btn-secondary">Reset</a>
        </form>
        <div class="mt-5">
            <h3>Results:</h3>
            <?php if (!empty($results)): ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Specialty</th>
                            <th>Gender</th>
                            <th>Location</th>
                            <th>Contact</th>
                            <th>Average Rating</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $doctor): ?>
                            <tr>
                                <td class="align-middle"><?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?></td>
                                <td class="align-middle"><?php echo htmlspecialchars($doctor['specialty']); ?></td>
                                <td class="align-middle"><?php echo htmlspecialchars($doctor['gender']); ?></td>
                                <td class="align-middle"><?php echo htmlspecialchars($doctor['location']); ?></td>
                                <td class="align-middle"><?php echo htmlspecialchars($doctor['contact']); ?></td>
                                <td class="align-middle">
                                    <?php 
                                    if (is_null($doctor['avg_rating'])) {
                                        echo 'No reviews yet';
                                    } else {
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= round($doctor['avg_rating'])) {
                                                echo '<span class="star">&#9733;</span>'; // Estrella llena
                                            } else {
                                                echo '<span class="star">&#9734;</span>'; // Estrella vacía
                                            }
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="align-middle"><a href="patient_dashboard.php?page=doctor_profile&doctor_id=<?php echo $doctor['userId']; ?>" class="btn btn-primary">View Profile</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No doctors found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .align-middle {
        vertical-align: middle !important;
    }
    .star {
        font-size: 1.2rem;
        color: #f5c518;
    }
</style>
