<?php
session_start();
include('../connection/config.php');

// Capture user ID and type from URL parameters
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
$user_type = isset($_GET['type']) ? $_GET['type'] : '';

// Check if form is submitted for deleting students
if (isset($_POST['delete_students'])) {
    // Ensure at least one student is selected for deletion
    if (!isset($_POST['student_ids']) || empty($_POST['student_ids'])) {
        die("Please select at least one student to delete.");
    }

    // Prepare delete query
    $delete_ids = $_POST['student_ids'];
    $placeholders = implode(',', array_fill(0, count($delete_ids), '?'));

    $delete_query = "DELETE FROM students WHERE id IN ($placeholders)";
    $delete_stmt = $conn->prepare($delete_query);

    // Bind values and execute delete query
    foreach ($delete_ids as $key => $id) {
        $delete_stmt->bindValue(($key + 1), $id, PDO::PARAM_INT);
    }

    if ($delete_stmt->execute()) {
        // Redirect back to student attendance page after deleting
        $redirect_url = "../attendance/studentattendance.php?user_id={$user_id}&type={$user_type}";
        header("Location: $redirect_url");
        exit();
    } else {
        die("Failed to delete students.");
    }
}

// Fetch all students for display
$query = "SELECT * FROM students";
$stmt = $conn->query($query);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Determine the back URL based on user type
$back_url = "../dashboards/{$user_type}dash.php?user_id={$user_id}";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Students</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h3 class="text-center">Remove  Student Panels</h3>

        <form action="" method="post">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>Student ID</th>
                        <th>Image</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Gender</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><input type="checkbox" name="student_ids[]" value="<?php echo $student['id']; ?>"></td>
                            <td><?php echo htmlspecialchars($student['student_no']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($student['image']); ?>" alt="Student Image" style="max-width: 100px;"></td>
                            <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['gender']); ?></td>
                            <td><?php echo htmlspecialchars($student['student_status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <button type="submit" name="delete_students" class="btn btn-danger">Delete Selected Students</button>
            <a href="../dashboards/admindash.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</body>
</html>
