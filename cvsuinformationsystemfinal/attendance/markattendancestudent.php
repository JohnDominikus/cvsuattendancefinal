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
if (isset($_POST['update_attendance'])) {
    if (isset($_POST['student_ids']) && isset($_POST['new_statuses'])) {
        $student_ids = $_POST['student_ids'];
        $new_statuses = $_POST['new_statuses'];

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
    <style>
        @media print {
            .no-print {
                display: none;
            }
            .table th, .table td {
                border: 1px solid #000 !important; /* Ensure borders are visible when printing */
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col text-center">
                <img src="../images/logo.png" alt="School Logo" style="max-height: 100px;">
                <h3>School Name</h3>
            </div>
        </div>

        <!-- Search form -->
        <form action="" method="GET" class="form-inline justify-content-center mb-3 no-print">
            <input type="text" name="search" placeholder="Search..." class="form-control mr-2" value="<?php echo htmlspecialchars($search); ?>">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
            <input type="hidden" name="type" value="<?php echo htmlspecialchars($user_type); ?>">
            <button type="submit" class="btn btn-primary btn-search">Search</button>
        </form>
        
        <!-- Update Attendance Form -->
        <form action="" method="POST">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Student ID</th>
                            <th>Image</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Gender</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
<?php foreach ($result as $row): ?>
    <tr>
        <td><input type="checkbox" name="student_ids[]" value="<?php echo htmlspecialchars($row['id']); ?>"></td>
        <td><?php echo htmlspecialchars($row['student_no']); ?></td>
        <td><img src="../images/student_image/<?php echo htmlspecialchars($row['image']); ?>" alt="Student Image" style="max-width: 100px;"></td>
        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
        <td><?php echo htmlspecialchars($row['gender']); ?></td>
        <td>
            <select name="new_statuses[]" class="form-control">
                <option value="excused">Excused</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
            </select>
        </td>
    </tr>
<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <button type="submit" name="update_attendance" class="btn btn-primary">Update Attendance</button>
        </form>

        <a href="../dashboards/teacherdash.php" class="btn btn-warning btn-block mt-3">Back</a>
    </div>

    <!-- Print Button -->
    <div class="text-center mt-3 no-print">
        <button type="button" class="btn btn-info" onclick="window.print()">Print</button>
    </div>
</body>
</html>
