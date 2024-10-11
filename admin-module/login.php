<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../admin-module/styles/login.css">
</head>
<body class="bg-gradient">
    <div class="login-container">
        <div class="login-logo">
            <img src="./assets/logo.png" alt="Austin Health Logo" width="230" class="logos">
        </div>
        <h2 class="login-title">Login</h2>
        
        <?php
        session_start();
        if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                <?php echo $_SESSION['error_message']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php 
        unset($_SESSION['error_message']); // Elimina el mensaje de error después de mostrarlo
        endif; 
        ?>

        <div class="card-body">
            <form action="login_process.php" method="POST">
                <div class="form-group">
                    <label for="userId" class="input-label">Username</label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" class="form-control" id="userId" name="userId" placeholder="Username" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="input-label">Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                </div>
                <div class="loader" id="loader" style="display: none;"></div>
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>                
            </form>
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
                <p><a href="#" data-toggle="modal" data-target="#forgotPasswordModal">Forgot your password?</a></p>
            </div>
        </div>
    </div>

    <!-- Modal para Forgot Password -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Forgot Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>If you forgot your password, please contact one of the following administrators:</p>
                    <ul>
                        <li>Lunshwa Shakya - K231451 - <a href="mailto:k231451@student.kent.edu.au">k231451@student.kent.edu.au</a></li>
                        <li>Santiago Ortiz - K200370 - <a href="mailto:k200370@student.kent.edu.au">k200370@student.kent.edu.au</a></li>
                        <li>Sulav Homagai - K220028 - <a href="mailto:k220028@student.kent.edu.au">k220028@student.kent.edu.au</a></li>
                        <li>Willow Sienna - K191297 - <a href="mailto:k191297@student.kent.edu.au">k191297@student.kent.edu.au</a></li>
                        <li>Bianca Carvalho - K221552 - <a href="mailto:k221552@student.kent.edu.au">k221552@student.kent.edu.au</a></li>
                    </ul>
                    <p>One of the administrators will get in touch with you to help reset your password.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('loader').style.display = 'block';
            document.querySelector('button[type="submit"]').style.display = 'none';

            setTimeout(() => {
                event.target.submit(); // Submit the form after a short delay
            }, 10000); 
        });
    </script>    
</body>
</html>
