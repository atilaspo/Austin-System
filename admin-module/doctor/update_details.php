<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ob_start(); // Inicia el buffer de salida

include('../config.php');

if (!isset($_SESSION['loggedin']) || $_SESSION['role_id'] != 3) {
    exit('Unauthorized access');
}

$user_id = $_SESSION['user_id'];
$details_sql = "SELECT * FROM user_details WHERE user_id = ?";
$details_stmt = $conn->prepare($details_sql);
$details_stmt->bind_param("i", $user_id);
$details_stmt->execute();
$details_result = $details_stmt->get_result();
$details = $details_result->fetch_assoc();

ob_end_flush(); // Finaliza el buffer de salida
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <div class="container">
            <div class="floating-card">
                <h2 class="mt-4">My Details</h2>

                <!-- Display current details -->
                <ul class="list-group" id="detailsList">
                    <li class="list-group-item"><strong>First Name:</strong> <span id="firstNameDisplay"><?php echo htmlspecialchars($details['first_name'] ?? '', ENT_QUOTES); ?></span></li>
                    <li class="list-group-item"><strong>Last Name:</strong> <span id="lastNameDisplay"><?php echo htmlspecialchars($details['last_name'] ?? '', ENT_QUOTES); ?></span></li>
                    <li class="list-group-item"><strong>Specialty:</strong> <span id="specialtyDisplay"><?php echo htmlspecialchars($details['specialty'] ?? '', ENT_QUOTES); ?></span></li>
                    <li class="list-group-item"><strong>Gender:</strong> <span id="genderDisplay"><?php echo htmlspecialchars($details['gender'] ?? '', ENT_QUOTES); ?></span></li>
                    <li class="list-group-item"><strong>Location:</strong> <span id="locationDisplay"><?php echo htmlspecialchars($details['location'] ?? '', ENT_QUOTES); ?></span></li>
                    <li class="list-group-item"><strong>Contact:</strong> <span id="contactDisplay"><?php echo htmlspecialchars($details['contact'] ?? '', ENT_QUOTES); ?></span></li>
                    <li class="list-group-item"><strong>Other Details:</strong> <span id="otherDetailsDisplay"><?php echo htmlspecialchars($details['other_details'] ?? '', ENT_QUOTES); ?></span></li>
                </ul>

                <!-- Button to open update modal -->
                <button type="button" class="btn btn-primary mt-4" data-toggle="modal" data-target="#updateModal">
                    Update
                </button>
            </div>
        </div>

        <!-- Modal to update details -->
        <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Update Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="updateForm">
                            <div class="form-group">
                                <label for="first_name">First Name:</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($details['first_name'] ?? '', ENT_QUOTES); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name:</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($details['last_name'] ?? '', ENT_QUOTES); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="specialty">Specialty:</label>
                                <input type="text" class="form-control" id="specialty" name="specialty" value="<?php echo htmlspecialchars($details['specialty'] ?? '', ENT_QUOTES); ?>">
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <select class="form-control" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo ($details['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo ($details['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo ($details['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="location">Location:</label>
                                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($details['location'] ?? '', ENT_QUOTES); ?>">
                            </div>
                            <div class="form-group">
                                <label for="contact">Contact:</label>
                                <input type="text" class="form-control" id="contact" name="contact" value="<?php echo htmlspecialchars($details['contact'] ?? '', ENT_QUOTES); ?>">
                            </div>
                            <div class="form-group">
                                <label for="other_details">Other Details:</label>
                                <textarea class="form-control" id="other_details" name="other_details"><?php echo htmlspecialchars($details['other_details'] ?? '', ENT_QUOTES); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        // Handle form submission with AJAX
        $('#updateForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                type: 'POST',
                url: 'update_details_action.php', // Separate PHP file to handle update
                data: $(this).serialize(),
                success: function(response) {
                    // Update displayed fields on the page
                    $('#firstNameDisplay').text($('#first_name').val());
                    $('#lastNameDisplay').text($('#last_name').val());
                    $('#specialtyDisplay').text($('#specialty').val());
                    $('#genderDisplay').text($('#gender').val());
                    $('#locationDisplay').text($('#location').val());
                    $('#contactDisplay').text($('#contact').val());
                    $('#otherDetailsDisplay').text($('#other_details').val());

                    // Close the modal
                    $('#updateModal').modal('hide');

                    // Show an optional success message
                    alert('Details updated successfully!');
                },
                error: function() {
                    alert('Error updating details. Please try again.');
                }
            });
        });
    </script>
</body>
</html>