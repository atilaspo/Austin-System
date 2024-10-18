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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_user'])) {
    $userId = $_POST['userId'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role'];

    $sql = "INSERT INTO users (userId, password, role_id) VALUES ('$userId', '$password', '$role_id')";

    if ($conn->query($sql) === TRUE) {
        header('Location: admin_dashboard.php?page=users');
        exit(); 
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Modificar la consulta para obtener el nombre del rol
$sql = "SELECT users.*, roles.role_name FROM users 
        LEFT JOIN roles ON users.role_id = roles.id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div id="wrapper">
        <div>
            <div class="container-fluid">
                <div class="floating-card">
                    <h1 class="mt-4">Users</h1>
                    <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#createUserModal">
                        Create New User
                    </button>

                    <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="users.php" method="POST">
                                        <input type="hidden" name="create_user" value="1">
                                        <div class="form-group">
                                            <label for="userId">User ID:</label>
                                            <input type="text" class="form-control" id="userId" name="userId" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password:</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="role">Select Role:</label>
                                            <select class="form-control" id="role" name="role" required>
                                                <option value="1">Admin</option>
                                                <option value="2">Patient</option>
                                                <option value="3">Doctor</option>
                                                <option value="4">Clinic</option>
                                                <option value="5">Cashier</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Create User</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla para listar los usuarios -->
                    <table class="table table-striped mt-4">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User ID</th>
                                <th>Role Name</th> <!-- Cambiado Role ID a Role Name -->
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row['id'] . "</td>";
                                    echo "<td>" . $row['userId'] . "</td>";
                                    echo "<td>" . $row['role_name'] . "</td>"; // Mostrar role_name
                                    echo "<td>" . $row['created_at'] . "</td>";
                                    echo "<td>";
                                    echo "<a href='admin_dashboard.php?page=edit_user&id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Edit</a>";
                                    echo "<a href='delete_user.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm'>Delete</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No users found</td></tr>";
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
