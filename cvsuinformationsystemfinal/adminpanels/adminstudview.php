<?php
session_start(); // Start the session at the beginning
include('../connection/config.php');

// Capture user ID and type from URL parameters
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
$user_type = isset($_GET['type']) ? $_GET['type'] : '';

// Check if search query is set and not empty
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetching student data
if (!empty($search)) {
    $query = "SELECT * FROM students WHERE CONCAT(`id`, `student_no`, `image`, `first_name`, `last_name`, `gender`, `student_status`, `email`) LIKE :search";
} else {
    $query = "SELECT * FROM students";
}

try {
    // Prepare and execute the query
    $stmt = $conn->prepare($query);
    if (!empty($search)) {
        $stmt->bindValue(':search', "%$search%");
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Query Failed: " . $e->getMessage());
}

// Check if the form is submitted for updating student status
if (isset($_POST['update_student'])) {
    if (isset($_POST['student_id']) && isset($_POST['new_status'])) {
        $student_ids = $_POST['student_id'];
        $new_statuses = $_POST['new_status'];

        // Prepare update query
        $update_query = "UPDATE students SET student_status = CASE id ";
        foreach ($student_ids as $key => $id) {
            $update_query .= "WHEN :id{$key} THEN :status{$key} ";
        }
        $update_query .= "END WHERE id IN (" . implode(',', $student_ids) . ")";

        // Execute update query
        $update_stmt = $conn->prepare($update_query);
        foreach ($student_ids as $key => $id) {
            $update_stmt->bindValue(":id{$key}", $id, PDO::PARAM_INT);
            $update_stmt->bindValue(":status{$key}", $new_statuses[$key], PDO::PARAM_STR);
        }
        $update_stmt->execute();

        // Redirect back to the student attendance page after updating
        $redirect_url = "../attendance/studentattendance.php?user_id={$user_id}&type={$user_type}";
        header("Location: $redirect_url");
        exit();
    } else {
        echo "Error: No student IDs or statuses submitted for update.";
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
    <title>Student Attendance System</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h3 class="text-center">Student Attendance Report</h3>
       
        <!-- Search form -->
        <form action="" method="GET" class="form-inline justify-content-center mb-3">
            <input type="text" name="search" placeholder="Search..." class="form-control mr-2" value="<?php echo htmlspecialchars($search); ?>">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
            <input type="hidden" name="type" value="<?php echo htmlspecialchars($user_type); ?>">
            <button type="submit" class="btn btn-primary btn-search">Search</button>
        </form>
        
        <?php if (!empty($result)): ?>
        <div class="table-responsive">
            <form action="" method="post"> <!-- Form to update student status -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Image</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th>Update info</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- PHP loop for fetching student data -->
                        <!-- PHP loop for fetching student data -->
<?php foreach ($result as $row): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['student_no']); ?></td>
        <td>
            <?php
            $image_path = htmlspecialchars($row['image']);
            $image_src = ($image_path != '') ? "../images/student_image/$image_path" : "../images/default_student_image.jpg";
            ?>
            <img src="<?php echo $image_src; ?>" alt="Student Image" style="max-width: 70px;">
        </td>
        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
        <td><?php echo htmlspecialchars($row['gender']); ?></td>
        <td><?php echo htmlspecialchars($row['student_status']); ?></td>
        
        <td>
            <input type="hidden" name="student_id[]" value="<?php echo htmlspecialchars($row['id']); ?>">
            <a href="updatestudent.php?id=<?php echo htmlspecialchars($row['id']); ?>&user_id=<?php echo htmlspecialchars($user_id); ?>&type=<?php echo htmlspecialchars($user_type); ?>" class="btn btn-success">Update</a>
        </td>
    </tr>
<?php endforeach; ?>

                    </tbody>
                </table>
               <!-- <button type="submit" name="update_student" class="btn btn-success">Update Status</button> -->
            </form>
            <a href="addstudent.php" class="btn btn-info">Add Student</a> <!-- Link to addstudent.php for adding a new student -->
            <a href="deletestudent.php" class="btn btn-danger">Delete</a> <!-- Link to deletestudent.php for deleting a student -->
        </div>
        <?php else: ?>
            <p>No results found.</p>
        <?php endif; ?>
        
        <a href="../dashboards/admindash.php ?>" class="btn btn-warning btn-block mt-3">Back</a>
    </div>
</body>
</html>
