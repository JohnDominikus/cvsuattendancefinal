<?php
// Include your configuration file and start session if not already started
include '../connection/config.php';
session_start();

// Function to retrieve a student from archive
if (isset($_POST['retrieve_student'])) {
    $student_id = $_POST['student_id'];
    try {
        // Fetch student details from archive
        $select = $conn->prepare("SELECT student_no, first_name, last_name, email, gender, student_status, image FROM student_archive WHERE id = ?");
        $select->execute([$student_id]);
        $fetch = $select->fetch(PDO::FETCH_ASSOC);

        if ($fetch) {
            // Insert student details back into students table
            $insert = $conn->prepare("INSERT INTO students (student_no, first_name, last_name, email, gender, student_status, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert->execute([$fetch['student_no'], $fetch['first_name'], $fetch['last_name'], $fetch['email'], $fetch['gender'], $fetch['student_status'], $fetch['image']]);
            
            // Delete student from archive
            $delete = $conn->prepare("DELETE FROM student_archive WHERE id = ?");
            $delete->execute([$student_id]);

            $_SESSION['success_message'] = "Student retrieved successfully.";
        } else {
            $_SESSION['error_message'] = "Error retrieving student.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error retrieving student: " . $e->getMessage();
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Function to delete selected archived students
if (isset($_POST['delete_archived_students'])) {
    if (!empty($_POST['student_ids'])) {
        $students_to_delete = $_POST['student_ids'];
        try {
            // Delete students from archive
            $placeholders = implode(',', array_fill(0, count($students_to_delete), '?'));
            $delete = $conn->prepare("DELETE FROM student_archive WHERE id IN ($placeholders)");
            $delete->execute($students_to_delete);

            $_SESSION['success_message'] = count($students_to_delete) . " student(s) deleted from archive successfully.";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error deleting students from archive: " . $e->getMessage();
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['error_message'] = "Please select at least one student to delete from archive.";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Fetch archived students from student_archive table
$archived_students = $conn->query("SELECT id, student_no, first_name, last_name, email, gender, student_status, image, archived_at FROM student_archive")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Archived Students</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #fff; }
        .container { margin-top: 20px; }
        .table { background-color: #ffffff; }
        th, td { color: #333333; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Archived Students</h2>

        <?php if (isset($_SESSION['success_message']) && !empty($_SESSION['success_message'])): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']);                ?>
           <?php endif; ?>
           <?php if (isset($_SESSION['error_message']) && !empty($_SESSION['error_message'])): ?>
               <div class="alert alert-danger" role="alert">
                   <?php echo $_SESSION['error_message']; ?>
               </div>
               <?php unset($_SESSION['error_message']); ?>
           <?php endif; ?>

           <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
               <table class="table table-striped">
                   <thead>
                       <tr>
                           <th></th>
                           <th>Student ID</th>
                           <th>Image</th>
                           <th>First Name</th>
                           <th>Last Name</th>
                           <th>Email</th>
                           <th>Gender</th>
                           <th>Status</th>
                           <th>Archived At</th>
                           <th>Actions</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php foreach ($archived_students as $student): ?>
                           <tr>
                               <td><input type="checkbox" name="student_ids[]" value="<?php echo htmlspecialchars($student['id']); ?>"></td>
                               <td><?php echo htmlspecialchars($student['student_no']); ?></td>
                               <td>
                                   <?php if (!empty($student['image'])): ?>
                                       <img src="../images/student_image/<?php echo htmlspecialchars($student['image']); ?>" alt="Student Image" style="max-width: 100px;">
                                   <?php else: ?>
                                       No Image Available
                                   <?php endif; ?>
                               </td>
                               <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                               <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                               <td><?php echo htmlspecialchars($student['email']); ?></td>
                               <td><?php echo htmlspecialchars($student['gender']); ?></td>
                               <td><?php echo htmlspecialchars($student['student_status']); ?></td>
                               <td><?php echo htmlspecialchars($student['archived_at']); ?></td>
                               <td>
                                   <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display: inline;">
                                       <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['id']); ?>">
                                       <button type="submit" class="btn btn-primary" name="retrieve_student">Retrieve</button>
                                   </form>
                               </td>
                           </tr>
                       <?php endforeach; ?>
                   </tbody>
               </table>
               <button type="submit" class="btn btn-danger" name="delete_archived_students">Delete Selected Students from Archive</button>
           </form>

           <a href="../dashboards/admindash.php" class="btn btn-success mt-3">Back to Dashboard</a>
       </div>
   </body>
   </html>
