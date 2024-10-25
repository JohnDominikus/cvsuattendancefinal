<?php
// Include database connection
include '../connection/config.php';

// Initialize variables to hold form input values
$subjectName = "";
$subjectCode = "";
$time = "";
$credits = 0; // Default value

// Fetch subject details based on ID from URL parameter
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $subjectId = $_GET['id'];
    // Query to fetch subject details
    $stmt = $conn->prepare("SELECT * FROM subjects WHERE subject_id = :subject_id");
    $stmt->bindParam(':subject_id', $subjectId);
    $stmt->execute();
    $subject = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($subject) {
        // Assign fetched values to variables
        $subjectName = $subject['subject_name'];
        $subjectCode = $subject['subject_code'];
        $time = $subject['time'];
        $credits = $subject['credits']; // Assign credits value
    } else {
        // Handle subject not found
        echo "Subject not found.";
        exit();
    }
}

// Handling form submission to update subject
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs (you should add more robust validation)
    $subjectName = htmlspecialchars($_POST['subject_name']);
    $subjectCode = htmlspecialchars($_POST['subject_code']);
    $time = htmlspecialchars($_POST['time']);
    $credits = intval($_POST['credits']); // Ensure credits is an integer
    $subjectId = $_POST['subject_id']; // Hidden field to store subject ID

    // Update subject in database
    $stmt = $conn->prepare("UPDATE subjects SET subject_name = :subject_name, subject_code = :subject_code, time = :time, credits = :credits WHERE subject_id = :subject_id");
    $stmt->bindParam(':subject_name', $subjectName);
    $stmt->bindParam(':subject_code', $subjectCode);
    $stmt->bindParam(':time', $time);
    $stmt->bindParam(':credits', $credits); // Bind credits parameter
    $stmt->bindParam(':subject_id', $subjectId);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to refresh the subjects list after updating
        header("Location: teacheradmin.php");
        exit();
    } else {
        echo "Error updating subject.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subject</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <h5 class="card-header">Edit Subject</h5>
            <div class="card-body">
                <form action="edit_subject.php" method="POST">
                    <input type="hidden" name="subject_id" value="<?= $subjectId ?>">
                    <div class="form-group">
                        <label for="subject_name">Subject Name</label>
                        <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?= $subjectName ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="subject_code">Subject Code</label>
                        <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?= $subjectCode ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="time">Time</label>
                        <select class="form-control" id="time" name="time">
                            <option value="08:00" <?= ($time == '08:00') ? 'selected' : '' ?>>08:00 AM</option>
                            <option value="09:00" <?= ($time == '09:00') ? 'selected' : '' ?>>09:00 AM</option>
                            <option value="10:00" <?= ($time == '10:00') ? 'selected' : '' ?>>10:00 AM</option>
                            <option value="11:00" <?= ($time == '11:00') ? 'selected' : '' ?>>11:00 AM</option>
                            <option value="12:00" <?= ($time == '12:00') ? 'selected' : '' ?>>12:00 PM</option>
                            <option value="13:00" <?= ($time == '13:00') ? 'selected' : '' ?>>01:00 PM</option>
                            <option value="14:00" <?= ($time == '14:00') ? 'selected' : '' ?>>02:00 PM</option>
                            <option value="15:00" <?= ($time == '15:00') ? 'selected' : '' ?>>03:00 PM</option>
                            <option value="16:00" <?= ($time == '16:00') ? 'selected' : '' ?>>04:00 PM</option>
                            <option value="17:00" <?= ($time == '17:00') ? 'selected' : '' ?>>05:00 PM</option>
                            <option value="18:00" <?= ($time == '18:00') ? 'selected' : '' ?>>06:00 PM</option>
                            <option value="19:00" <?= ($time == '19:00') ? 'selected' : '' ?>>07:00 PM</option>
                            <option value="20:00" <?= ($time == '20:00') ? 'selected' : '' ?>>08:00 PM</option>
                            <!-- Add more time options as needed -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="credits">Credits</label>
                        <input type="number" class="form-control" id="credits" name="credits" value="<?= $credits ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Subject</button>
                    <a href="../schedules/teacheradmin.php" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
