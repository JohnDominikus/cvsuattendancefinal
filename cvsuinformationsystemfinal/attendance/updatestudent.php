<?php
session_start();
include('../connection/config.php');

// Capture user ID and type from URL parameters
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
$user_type = isset($_GET['type']) ? $_GET['type'] : '';

// Check if student ID is provided via GET parameter
$student_id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($student_id)) {
    die("Student ID is required.");
}

// Fetch student details based on ID
$query = "SELECT * FROM students WHERE id = :student_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':student_id', $student_id, PDO::PARAM_INT);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student not found.");
}

// Handle form submission for updating student information
if (isset($_POST['update_student'])) {
    $new_first_name = $_POST['first_name'];
    $new_last_name = $_POST['last_name'];
    $new_gender = $_POST['gender'];
    $new_status = $_POST['student_status'];

    // Check if a new image file is uploaded
    if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $upload_dir = '../images/'; // Directory where images will be uploaded
        $image_path = $upload_dir . $image_name;

        // Move uploaded image to the specified directory
        if (move_uploaded_file($image_tmp, $image_path)) {
            // Update student record with new image path
            $update_query = "UPDATE students SET first_name = :first_name, last_name = :last_name, gender = :gender, student_status = :student_status, image = :image WHERE id = :student_id";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bindValue(':image', $image_path, PDO::PARAM_STR);
        } else {
            die("Failed to upload image.");
        }
    } else {
        // Prepare update query without image update
        $update_query = "UPDATE students SET first_name = :first_name, last_name = :last_name, gender = :gender, student_status = :student_status WHERE id = :student_id";
        $update_stmt = $conn->prepare($update_query);
    }

    // Bind values and execute update query
    $update_stmt->bindValue(':first_name', $new_first_name, PDO::PARAM_STR);
    $update_stmt->bindValue(':last_name', $new_last_name, PDO::PARAM_STR);
    $update_stmt->bindValue(':gender', $new_gender, PDO::PARAM_STR);
    $update_stmt->bindValue(':student_status', $new_status, PDO::PARAM_STR);
    $update_stmt->bindValue(':student_id', $student_id, PDO::PARAM_INT);

    if ($update_stmt->execute()) {
        // Redirect back to student attendance page after updating
        $redirect_url = "../attendance/studentattendance.php?user_id={$user_id}&type={$user_type}";
        header("Location: $redirect_url");
        exit();
    } else {
        die("Update failed.");
    }
}

// Determine the back URL based on user type
$back_url = "../dashboards/{$user_type}dash.php?user_id={$user_id}";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h3 class="text-center">Update Student</h3>
       
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['id']); ?>">

            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="Male" <?php echo ($student['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($student['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>

           

            <div class="form-group">
                <label for="image">Upload Image:</label>
                <input type="file" class="form-control-file" id="image" name="image">
            </div>

            <button type="submit" name="update_student" class="btn btn-primary">Update Student</button>
            <a href="../attendance/studentattendance.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</body>
</html>
