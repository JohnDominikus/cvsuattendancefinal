<?php
include '../connection/config.php';
session_start();

// Fetch current admin's details
if (isset($_SESSION['user_email'])) {
    $user_email = $_SESSION['user_email'];
    $select = $conn->prepare("SELECT id, first_name, last_name, image FROM user_form WHERE email = ?");
    $select->execute([$user_email]);
    $fetch = $select->fetch(PDO::FETCH_ASSOC);

    // If admin details are fetched, proceed
    if ($fetch) {
        $admin_id = $fetch['id'];
        $first_name = $fetch['first_name'];
        $last_name = $fetch['last_name'];
        $image = $fetch['image'];
    }
}

// Handle addition of a student
if (isset($_POST['add_student'])) {
    $student_no = filter_var($_POST['student_no'], FILTER_SANITIZE_STRING);
    $student_first_name = filter_var($_POST['student_first_name'], FILTER_SANITIZE_STRING);
    $student_last_name = filter_var($_POST['student_last_name'], FILTER_SANITIZE_STRING);
    $student_email = filter_var($_POST['student_email'], FILTER_SANITIZE_EMAIL);
    $student_gender = $_POST['student_gender']; // Assuming select options are hardcoded, no need to sanitize
    $student_status = $_POST['student_status']; // Assuming select options are hardcoded, no need to sanitize

    // Handle file upload
    if ($_FILES['student_image']['error'] === UPLOAD_ERR_OK) {
        $image_name = $_FILES['student_image']['name'];
        $image_tmp_name = $_FILES['student_image']['tmp_name'];
        $image_type = $_FILES['student_image']['type'];
        $image_size = $_FILES['student_image']['size'];

        $upload_dir = '../images/student_image/';

        // Validate file type
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        if (in_array($image_type, $allowed_types) && $image_size > 0) {
            $image_path = $upload_dir . basename($image_name);
            if (move_uploaded_file($image_tmp_name, $image_path)) {
                // Proceed with database insert
                try {
                    $insert = $conn->prepare("INSERT INTO students (student_no, first_name, last_name, email, gender, student_status, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $insert->execute([$student_no, $student_first_name, $student_last_name, $student_email, $student_gender, $student_status, $image_name]);
                    $_SESSION['success_message'] = "Student added successfully.";
                } catch (PDOException $e) {
                    $_SESSION['error_message'] = "Error adding student: " . $e->getMessage();
                }
            } else {
                $_SESSION['error_message'] = "Error uploading image.";
            }
        } else {
            $_SESSION['error_message'] = "Invalid file format or file size too large.";
        }
    } else {
        $_SESSION['error_message'] = "Error uploading image: " . $_FILES['student_image']['error'];
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Handle deletion of selected students
// Handle deletion of selected students
if (isset($_POST['confirm_delete'])) {
    if (!empty($_POST['student_ids'])) {
        $students_to_delete = $_POST['student_ids'];
        try {
            // Start a transaction for atomicity
            $conn->beginTransaction();

            // Insert deleted students into student_archive table
            $placeholders = implode(',', array_fill(0, count($students_to_delete), '?'));
            $select_students = $conn->prepare("SELECT * FROM students WHERE id IN ($placeholders)");
            $select_students->execute($students_to_delete);
            $archived_students = $select_students->fetchAll(PDO::FETCH_ASSOC);

            $insert_archived_students = $conn->prepare("
                INSERT INTO student_archive (student_no, first_name, last_name, email, gender, student_status, image, archived_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($archived_students as $student) {
                $insert_archived_students->execute([
                    $student['student_no'],
                    $student['first_name'],
                    $student['last_name'],
                    $student['email'],
                    $student['gender'],
                    $student['student_status'],
                    $student['image'],
                    $admin_id // Assuming $admin_id is set from the current admin session
                ]);
            }

            // Delete students from students table
            $delete = $conn->prepare("DELETE FROM students WHERE id IN ($placeholders)");
            $delete->execute($students_to_delete);

            // Commit the transaction
            $conn->commit();

            $_SESSION['success_message'] = count($students_to_delete) . " student(s) archived successfully.";
        } catch (PDOException $e) {
            // Rollback the transaction on error
            $conn->rollBack();
            $_SESSION['error_message'] = "Error archiving students: " . $e->getMessage();
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['error_message'] = "Please select at least one student to archive.";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Handle updating of a student
if (isset($_POST['update_student'])) {
    $student_id = $_POST['student_id'];
    $student_no = $_POST['student_no'];
    $student_first_name = $_POST['student_first_name'];
    $student_last_name = $_POST['student_last_name'];
    $student_email = $_POST['student_email'];
    $student_gender = $_POST['student_gender'];
    $student_status = $_POST['student_status'];

    // Handle file upload for update
    if ($_FILES['update_student_image']['error'] === UPLOAD_ERR_OK) {
        $image_name = $_FILES['update_student_image']['name'];
        $image_tmp_name = $_FILES['update_student_image']['tmp_name'];
        $image_type = $_FILES['update_student_image']['type'];
        $image_size = $_FILES['update_student_image']['size'];

        $upload_dir = '../images/student_image/';

        // Validate file type
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        if (in_array($image_type, $allowed_types) && $image_size > 0) {
            $image_path = $upload_dir . basename($image_name);
            if (move_uploaded_file($image_tmp_name, $image_path)) {
                // Update database with image
                try {
                    $update = $conn->prepare("UPDATE students SET student_no = ?, first_name = ?, last_name = ?, email = ?, gender = ?, student_status = ?, image = ? WHERE id = ?");
                    $update->execute([$student_no, $student_first_name, $student_last_name, $student_email, $student_gender, $student_status, $image_name, $student_id]);
                    $_SESSION['success_message'] = "Student updated successfully.";
                } catch (PDOException $e) {
                    $_SESSION['error_message'] = "Error updating student: " . $e->getMessage();
                }
            } else {
                $_SESSION['error_message'] = "Error uploading image.";
            }
        } else {
            $_SESSION['error_message'] = "Invalid file format or file size too large.";
        }
    } else {
        // Update without changing image
        try {
            $update = $conn->prepare("UPDATE students SET student_no = ?, first_name = ?, last_name = ?, email = ?, gender = ?, student_status = ? WHERE id = ?");
            $update->execute([$student_no, $student_first_name, $student_last_name, $student_email, $student_gender, $student_status, $student_id]);
            $_SESSION['success_message'] = "Student updated successfully.";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error updating student: " . $e->getMessage();
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Reset the success and error messages after displaying
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #fff; }
        .container { margin-top: 10px; }
        .card { margin-bottom: 20px; }
        .table { background-color: #ffffff; }
        th, td { color: #333333; }
        h2 { color: #fff; margin-top: 20px; background-color: #28a745; padding: 10px; text-align: center; border-radius: 5px; }
        .btn-success { background-color: #28a745; border-color: #28a745; }
        .btn-success:hover { background-color: #218838; border-color: #218838; }
        .btn-danger { background-color: #dc3545; border-color: #dc3545; }
        .btn-danger:hover { background-color: #c82333; border-color: #c82333; }
        .bg-info { background-color: #17a2b8; }
        .bg-info th { color: #000; }
        .back-button { position: left; top: 20px; right: 20px; width: 150px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 5px; font-size: 18px; transition: background-color 0.3s ease; background-color: green; color: #fff; text-decoration: none; border: none; }
        .back-button i { margin-right: 5px; }
        .back-button:hover { background-color: #0056b3; color: #fff; }
    </style>
</head>
<body>
    <a href="../dashboards/admindash.php" class="btn btn-success back-button">Back</a>
    <div class="container">
        <h2>Student Management</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Student</h5>
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($success_message): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="student_no">Student Number</label>
                                <input type="text" class="form-control" id="student_no" name="student_no" required>
                            </div>
                            <div class="form-group">
                                <label for="student_first_name">First Name</label>
                                <input type="text" class="form-control" id="student_first_name" name="student_first_name" required>
                            </div>
                            <div class="form-group">
                                <label for="student_last_name">Last Name</label>
                                <input type="text" class="form-control" id="student_last_name" name="student_last_name" required>
                            </div>
                            <div class="form-group">
                                <label for="student_email">Email</label>
                                <input type="email" class="form-control" id="student_email" name="student_email" required>
                            </div>
                            <div class="form-group">
                                <label for="student_gender">Gender</label>
                                <select class="form-control" id="student_gender" name="student_gender" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="student_status">Student Status</label>
                                <select class="form-control" id="student_status" name="student_status" required>
                                    <option value="student">Student</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="student_image">Student Image</label>
                                <input type="file" class="form-control-file" id="student_image" name="student_image">
                            </div>
                            <button type="submit" class="btn btn-success" name="add_student">Add Student</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Update Student</h5>
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="student_id">Select Student</label>
                                <select class="form-control" id="student_id" name="student_id" required>
                                    <?php
                                    $students = $conn->query("SELECT id, first_name, last_name FROM students");
                                    foreach ($students as $student) {
                                        echo "<option value='{$student['id']}'>{$student['first_name']} {$student['last_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="student_no">Student Number</label>
                                <input type="text" class="form-control" id="student_no" name="student_no" required>
                            </div>
                            <div class="form-group">
                                <label for="student_first_name">First Name</label>
                                <input type="text" class="form-control" id="student_first_name" name="student_first_name" required>
                            </div>
                            <div class="form-group">
                                <label for="student_last_name">Last Name</label>
                                <input type="text" class="form-control" id="student_last_name" name="student_last_name" required>
                            </div>
                            <div class="form-group">
                                <label for="student_email">Email</label>
                                <input type="email" class="form-control" id="student_email" name="student_email" required>
                            </div>
                            <div class="form-group">
                                <label for="student_gender">Gender</label>
                                <select class="form-control" id="student_gender" name="student_gender" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="student_status">Student Status</label>
                                <select class="form-control" id="student_status" name="student_status" required>
                                    <option value="student">Student</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="update_student_image">Update Student Image</label>
                                <input type="file" class="form-control-file" id="update_student_image" name="update_student_image">
                            </div>
                            <button type="submit" class="btn btn-primary" name="update_student">Update Student</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Student Section -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Student List</h5>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success_message): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Image</th>
                                <th>Student ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Gender</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $students = $conn->query("SELECT id, image, student_no, first_name, last_name, email, gender, student_status FROM students");
                            foreach ($students as $student) {
                                echo "<tr>";
                                echo "<td><input type='checkbox' name='student_ids[]' value='{$student['id']}'></td>";
                                if (!empty($student['image'])) {
                                    echo "<td><img src='../images/student_image/{$student['image']}' alt='Student Image' style='max-width: 100px;'></td>";
                                } else {
                                    echo "<td>No Image Available</td>";
                                }
                                echo "<td>" . htmlspecialchars($student['student_no']) . "</td>";
                                echo "<td>" . htmlspecialchars($student['first_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($student['last_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($student['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($student['gender']) . "</td>";
                                echo "<td>" . htmlspecialchars($student['student_status']) . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-danger" name="confirm_delete">Delete Selected Students</button>
                </form>
            </div>
        </div>

    </div>
    <!-- Bootstrap JS and jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

