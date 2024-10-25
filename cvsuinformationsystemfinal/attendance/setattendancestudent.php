<?php
session_start();
include('../connection/config.php');

// Initialize variables
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
$user_type = isset($_GET['type']) ? $_GET['type'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch student data based on search query
$query = "SELECT * FROM students";
$params = [];
if (!empty($search)) {
    $query .= " WHERE CONCAT(id, student_no, image, first_name, last_name, gender, student_status, email) LIKE :search";
    $params[':search'] = "%$search%";
}

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Query Failed: " . $e->getMessage());
}

// Handle form submission to update student attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_attendance'])) {
    if (isset($_POST['student_ids'], $_POST['new_statuses'])) {
        $student_ids = $_POST['student_ids'];
        $new_statuses = $_POST['new_statuses'];

        // Prepare and execute batch update query
        $update_query = "UPDATE students SET student_status = :status WHERE id = :id";
        $update_stmt = $conn->prepare($update_query);

        foreach ($student_ids as $key => $student_id) {
            $status = $new_statuses[$key];
            $update_stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $update_stmt->bindParam(':id', $student_id, PDO::PARAM_INT);
            $update_stmt->execute();
        }

        // Redirect to the same page after update
        $redirect_url = "setattendancestudent.php?user_id={$user_id}&type={$user_type}&search={$search}";
        header("Location: $redirect_url");
        exit();
    } else {
        echo "Error: Missing student IDs or new statuses.";
    }
}

// Back URL based on user type
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
    <script>
        function confirmUpdate() {
            return confirm('Are you sure you want to update the attendance?');
        }
    </script>
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
        <form action="" method="POST" onsubmit="return confirmUpdate();">
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
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="student_ids[]" value="<?php echo $student['id']; ?>">
                                </td>
                                <td><?php echo htmlspecialchars($student['student_no']); ?></td>
                                <td>
                                    <img src="../images/student_image/<?php echo htmlspecialchars($student['image']); ?>" alt="Student Image" style="max-width: 100px;">
                                </td>
                                <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['gender']); ?></td>
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

        <!-- Back button -->
        <a href="../dashboards/studentdash.php" class="btn btn-warning btn-block mt-3">Back</a>
    </div>

    <!-- Print Button -->
    <div class="text-center mt-3 no-print">
        <button type="button" class="btn btn-info" onclick="window.print()">Print</button>
    </div>
</body>
</html>
