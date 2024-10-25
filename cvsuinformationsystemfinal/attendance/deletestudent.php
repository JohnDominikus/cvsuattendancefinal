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

    // Prepare select and insert queries for moving to archive
    $select_query = "SELECT * FROM students WHERE id IN (" . implode(',', $_POST['student_ids']) . ")";
    $select_stmt = $conn->query($select_query);
    $students_to_archive = $select_stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($students_to_archive)) {
        try {
            $conn->beginTransaction();

            // Insert into student_archive
            $insert_query = "INSERT INTO student_archive (student_no, first_name, last_name, email, gender, student_status, image, archived_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_query);

            foreach ($students_to_archive as $student) {
                $insert_stmt->execute([$student['student_no'], $student['first_name'], $student['last_name'], $student['email'], $student['gender'], $student['student_status'], $student['image']]);
            }

            // Delete from students table
            $delete_query = "DELETE FROM students WHERE id IN (" . implode(',', $_POST['student_ids']) . ")";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->execute();

            $conn->commit();
            $_SESSION['success_message'] = "Selected student(s) moved to archive successfully.";
        } catch (PDOException $e) {
            $conn->rollBack();
            $_SESSION['error_message'] = "Failed to move students to archive: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "No students selected or found.";
    }

    // Redirect back to student attendance page after deleting
    $redirect_url = "../attendance/studentattendance.php?user_id={$user_id}&type={$user_type}";
    header("Location: $redirect_url");
    exit();
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
        <h3 class="text-center">Delete Students</h3>

        <form action="" method="post">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>Student ID</th>
                        <th>Image</th>
                        <th>Full Name</th>
                        <th>Gender</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><input type="checkbox" name="student_ids[]" value="<?php echo $student['id']; ?>"></td>
                            <td><?php echo htmlspecialchars($student['student_no']); ?></td>
                            <td><img src="../images/student_image/<?php echo htmlspecialchars($student['image']); ?>" alt="Student Image" style="max-width: 100px;"></td>
                            <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['gender']); ?></td>
                            <td><?php echo htmlspecialchars($student['student_status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <button type="submit" name="delete_students" class="btn btn-danger">Delete Selected Students</button>
            <a href="../dashboards/teacherdash.php" class="btn btn-success">Back to the Dashboard</a>
        </form>
    </div>
</body>
</html>
