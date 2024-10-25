<?php
// editschedule.php

// Include database connection
include '../connection/config.php';

// Initialize variables
$subject_id = null;
$subject_code = '';
$subject_name = '';
$description = '';
$time = '';
$credits = '';

// Check if ID parameter is set and numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $subject_id = $_GET['id'];

    // Query to fetch subject details based on ID
    $stmt = $conn->prepare("SELECT * FROM subjects WHERE subject_id = :subject_id");
    $stmt->bindParam(':subject_id', $subject_id);
    $stmt->execute();
    $subject = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$subject) {
        // Subject not found, handle error or redirect
        echo "Subject not found.";
        exit;
    }

    // Assign fetched values to variables
    $subject_code = $subject['subject_code'];
    $subject_name = $subject['subject_name'];
    $description = $subject['description'];
    $time = $subject['time'];
    $credits = $subject['credits'];
} else {
    // Redirect to teacherschedule.php if ID parameter is missing or invalid
    header("Location: teacherschedule.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Subject</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Subject</h2>
    <form action="update_subject.php" method="POST">
        <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
        <div class="form-group">
            <label for="subject_code">Subject Code</label>
            <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?php echo htmlspecialchars($subject_code); ?>">
        </div>
        <div class="form-group">
            <label for="subject_name">Subject Name</label>
            <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?php echo htmlspecialchars($subject_name); ?>">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description"><?php echo htmlspecialchars($description); ?></textarea>
        </div>
        <div class="form-group">
            <label for="time">Time</label>
            <input type="time" class="form-control" id="time" name="time" value="<?php echo htmlspecialchars($time); ?>">
        <div class="form-group">
            <label for="credits">Credits</label>
            <input type="number" class="form-control" id="credits" name="credits" value="<?php echo htmlspecialchars($credits); ?>">
        </div>
        <a href="../schedules/edits.php" class="btn btn-primary ml-2">Cancel</a>
        <a href="teacherschedule.php" class="btn btn-secondary ml-2">Cancel</a>
    </form>
</div>
</body>
</html>
