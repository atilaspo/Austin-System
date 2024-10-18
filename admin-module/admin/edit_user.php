<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

include('../config.php');

$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $userId = $_POST['userId'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role'];

    $sql = "UPDATE users SET userId='$userId', password='$password', role_id='$role_id' WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        header('Location: admin_dashboard.php?page=users');
        exit(); // Añadido exit() para asegurar que el script se detenga después de la redirección
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $conn->close();
} else {
    $id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id='$id'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="d-flex" id="wrapper">

        <!-- Page Content -->
        <div>
            <div class="container-fluid">
                <div class="floating-card">
                    <h1 class="mt-4">Edit User</h1>
                    <form action="edit_user.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                        <div class="form-group">
                            <label for="userId">User ID:</label>
                            <input type="text" class="form-control" id="userId" name="userId" value="<?php echo $user['userId']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Select Role:</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="1" <?php if ($user['role_id'] == 1) echo 'selected'; ?>>Admin</option>
                                <option value="2" <?php if ($user['role_id'] == 2) echo 'selected'; ?>>Patient</option>
                                <option value="3" <?php if ($user['role_id'] == 3) echo 'selected'; ?>>Doctor</option>
                                <option value="4" <?php if ($user['role_id'] == 4) echo 'selected'; ?>>Clinic</option>
                                <option value="5" <?php if ($user['role_id'] == 5) echo 'selected'; ?>>Cashier</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
