<?php
include('../config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$doctor_id = $_GET['doctor_id'];
$search_date = "";

// Check if a specific date is being searched
if (isset($_GET['search_date']) && !empty($_GET['search_date'])) {
    $search_date = $_GET['search_date'];
    $sql = "SELECT * FROM availability WHERE doctor_id = ? AND is_available = 1 AND date = ? ORDER BY date, start_time";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $doctor_id, $search_date);
} else {
    $sql = "SELECT * FROM availability WHERE doctor_id = ? AND is_available = 1 ORDER BY date, start_time";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
}

$stmt->execute();
$result = $stmt->get_result();

$availabilities = [];
while ($row = $result->fetch_assoc()) {
    $availabilities[$row['date']][] = $row;  // Group by date
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .calendar-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .day-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 280px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .day-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .day-card-header {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
        }
        .day-card-body {
            padding: 15px;
        }
        .slot {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .slot button {
            background-color: #491f5e;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .slot button:hover {
            background-color: #8d2957; 
        }
        .tooltip-text {
            cursor: pointer;
            position: relative;
            display: inline-block;
            color: #007bff;
            text-decoration: underline;
        }
        .tooltip-text:hover .tooltip-box {
            visibility: visible;
            opacity: 1;
        }
        .tooltip-box {
            visibility: hidden;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -60px;
            opacity: 0;
            transition: opacity 0.3s;
            width: 120px;
        }
        .tooltip-box::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-5">
        <h2 class="text-center mb-4">Select an Available Slot</h2>

        <!-- Search Form -->
        <form method="GET" action="patient_dashboard.php" class="form-inline mb-4 justify-content-center">
            <input type="hidden" name="page" value="book_appointment">
            <input type="hidden" name="doctor_id" value="<?php echo htmlspecialchars($doctor_id); ?>">
            <input type="date" name="search_date" class="form-control mr-2" value="<?php echo htmlspecialchars($search_date); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="patient_dashboard.php?page=book_appointment&doctor_id=<?php echo htmlspecialchars($doctor_id); ?>" class="btn btn-secondary ml-2">Reset</a>
        </form>

        <div class="calendar-container">
            <?php
            if (!empty($availabilities)) {
                foreach ($availabilities as $date => $slots) {
                    echo "<div class='day-card'>";
                    echo "<div class='day-card-header'>" . htmlspecialchars(date("D, d M Y", strtotime($date))) . "</div>";
                    echo "<div class='day-card-body'>";
                    foreach ($slots as $slot) {
                        echo "<div class='slot'>";
                        echo "<div class='tooltip-text'>";
                        echo "Time: " . htmlspecialchars($slot['start_time']) . " - " . htmlspecialchars($slot['end_time']);
                        echo "<span class='tooltip-box'>Click to book this slot</span>";
                        echo "</div>";
                        echo "<form action='patient_dashboard.php?page=confirm_appointment' method='post' class='mb-0'>";
                        echo "<input type='hidden' name='availability_id' value='" . htmlspecialchars($slot['id']) . "'>";
                        echo "<button type='submit'>Book</button>";
                        echo "</form>";
                        echo "</div>";
                    }
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<div class='alert alert-info text-center'>No available slots found.</div>";
            }
            ?>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
