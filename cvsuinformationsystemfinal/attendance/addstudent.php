<?php
session_start(); // Start the session at the beginning
include('../connection/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    // Retrieve and sanitize input data (ensure you sanitize to prevent SQL injection)
    $student_no = $_POST['student_no'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];

    $email = $_POST['email'];

    // Handle image upload
    $image_path = ''; // Initialize the variable to store the image path
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $temp_name = $_FILES['image']['tmp_name'];
        $image_name = basename($_FILES['image']['name']);
        $image_path = '../images/' . $image_name; // Path where the image will be stored

        if (move_uploaded_file($temp_name, $image_path)) {
            // Image moved successfully
            // Example query to insert a new student into the database with image path
            $insert_query = "INSERT INTO students (student_no, first_name, last_name, gender, email, image)
                            VALUES (:student_no, :first_name, :last_name, :gender,  :email, :image)";

            try {
                $stmt = $conn->prepare($insert_query);
                $stmt->bindParam(':student_no', $student_no);
                $stmt->bindParam(':first_name', $first_name);
                $stmt->bindParam(':last_name', $last_name);
                $stmt->bindParam(':gender', $gender);
   
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':image', $image_path);

                $stmt->execute();

                // Redirect to a success page or back to the previous page
                header('Location: ../attendance/studentattendance.php');
                exit();
            } catch(PDOException $e) {
                die("Query Failed: " . $e->getMessage());
            }
        } else {
            echo "Failed to move uploaded file.";
        }
    } else {
        echo "Image upload error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h3 class="text-center">Add New Student</h3>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="student_no">Student Number:</label>
                <input type="text" class="form-control" id="student_no" name="student_no" required>
            </div>
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="image">Student Image:</label>
                <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
            </div>
            <button type="submit" name="add_student" class="btn btn-primary">Add Student</button>
            <a href="../attendance/studentattendance.php" class="btn btn-warning btn-block mt-3">Back</a>
        </form>
    </div>
</body>
</html>
