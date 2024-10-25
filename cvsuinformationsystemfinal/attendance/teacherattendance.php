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
    $query = "SELECT * FROM students 
              WHERE CONCAT(`id`, `student_no`, `first_name`, `last_name`, `gender`, `student_status`, `email`) LIKE :search";
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
    $student_ids = $_POST['student_id'];
    $new_statuses = $_POST['new_status'];

    $update_query = "UPDATE students SET student_status = CASE id ";
    foreach ($student_ids as $key => $id) {
        $update_query .= "WHEN :id{$key} THEN :status{$key} ";
    }
    $update_query .= "END WHERE id IN (" . implode(',', $student_ids) . ")";

    $update_stmt = $conn->prepare($update_query);
    foreach ($student_ids as $key => $id) {
        $update_stmt->bindValue(":id{$key}", $id);
        $update_stmt->bindValue(":status{$key}", $new_statuses[$key]);
    }
    $update_stmt->execute();

    // Redirect back to the respective dashboard after updating
    $redirect_url = "../attendance/teacherattendance.php";
    header("Location: $redirect_url");
    exit();
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
            <input type="text" name="search" placeholder="Search..." class="form-control mr-2">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <input type="hidden" name="type" value="<?php echo $user_type; ?>">
            <button type="submit" class="btn btn-primary btn-search">Search</button>
        </form>
        <?php if (!empty($result)): ?> <!-- Check if there are any results -->
        <div class="table-responsive">
            <form action="" method="post"> <!-- Form to update student status -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- PHP loop for fetching student data -->
                        <?php foreach ($result as $row): ?>
                            <tr>
                                <td><?php echo $row['student_no']; ?></td>
                                <td><img src="../image/student_image/<?php echo $row['image']; ?>" alt="Student Image" style="max-width: 100px;"></td>
                                <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                                <td><?php echo $row['gender']; ?></td>
                                <td>
                                    <select name="new_status[]" class="form-control">
                                        <option value="present" <?php echo ($row['student_status'] == 'present') ? 'selected' : ''; ?>>Present</option>
                                        <option value="absent" <?php echo ($row['student_status'] == 'absent') ? 'selected' : ''; ?>>Absent</option>
                                        <option value="excused" <?php echo ($row['student_status'] == 'excused') ? 'selected' : ''; ?>>Excused</option>
                                    </select>
                                    <input type="hidden" name="student_id[]" value="<?php echo $row['id']; ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" name="update_student" class="btn btn-success">Update Status</button>
            </form>
        </div>
        <?php else: ?>
            <p>No results found.</p>
        <?php endif; ?>
        <a href="<?php echo $back_url; ?>" class="btn btn-warning btn-block mt-3">Back</a>
    </div>
</body>
</html>
