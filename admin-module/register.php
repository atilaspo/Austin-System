<?php
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = htmlspecialchars($_POST['userId']);
    $password = htmlspecialchars($_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 2; // Role ID for 'patient'

    // Database connection
    $servername = "localhost";
    $username = "root";
    $db_password = "";
    $dbname = "admin_module_db";

    $conn = new mysqli($servername, $username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO users (userId, password, role_id) VALUES ('$userId', '$hashed_password', $role)";

    if ($conn->query($sql) === TRUE) {
        $message = "New patient account created successfully. <a href='login.php'>Login here</a>";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #491f5e 50%, #8d2957 80%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Roboto', sans-serif;
        }
        .register-container {
            background-color: #ffffff;
            border-radius: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }
        .register-title {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
            color: #333;
            font-weight: 700;
        }
        .login-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .login-logo img {
            max-width: 200px;
        }
        .form-control {
            border-radius: 50px;
            padding: 20px;
            font-size: 16px;
            border: 2px solid #e3e3e3;
            font-weight: 400;
        }
        .form-control:focus {
            border-color: #8d2957;
            box-shadow: 0 0 0 0.2rem rgba(141, 41, 87, 0.25);
        }
        .btn-primary {
            border-radius: 50px;
            padding: 10px 30px;
            font-size: 18px;
            background-color: #491F5E;
            border: none;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #8d2957;
        }
        .success-message {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        .success-message a {
            color: #155724;
            font-weight: 700;
            text-decoration: none;
        }
        .loader {
        border: 4px solid #f3f3f3;
        border-radius: 50%;
        border-top: 4px solid #491F5E;
        width: 30px;
        height: 30px;
        animation: spin 2s linear infinite;
        margin: 0 auto;
        display: block;
        }

        @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="login-logo">
            <img src="./assets/logo.png" alt="Austin Health Logo" width="230" class="logos">
        </div>
        <h2 class="register-title">Register</h2>
        <?php if ($message): ?>
            <div class="success-message">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="userId">Username</label>
                <input type="text" class="form-control" id="userId" name="userId" placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="loader" id="loader" style="display: none;"></div>
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-user-plus"></i> Register
            </button>
        </form>
        <div class="text-center mt-3">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
        event.preventDefault(); // Stop the form from submitting immediately
        document.getElementById('loader').style.display = 'block';
        document.querySelector('button[type="submit"]').style.display = 'none';

        setTimeout(() => {
            event.target.submit(); // Submit the form after a short delay
        }, 1000); 
        });
    </script>
</body>
</html>
