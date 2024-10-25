<?php
include '../connection/config.php';
session_start();

// Fetch current admin's details
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

// Handle addition of a teacher
if (isset($_POST['add_teacher'])) {
    $teacher_first_name = $_POST['teacher_first_name'];
    $teacher_last_name = $_POST['teacher_last_name'];
    $teacher_email = $_POST['teacher_email'];
    $teacher_gender = $_POST['teacher_gender'];
    $teacher_image = ''; // You can add image upload functionality here if needed
    $teacher_type = 'teacher';
    $teacher_password = password_hash($_POST['teacher_password'], PASSWORD_DEFAULT); // Hash the password

    // Insert new teacher into the database
    $insert = $conn->prepare("INSERT INTO user_form (first_name, last_name, email, gender, image, type, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insert->execute([$teacher_first_name, $teacher_last_name, $teacher_email, $teacher_gender, $teacher_image, $teacher_type, $teacher_password]);

    // Redirect to the same page after adding the teacher
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Handle deletion of selected teachers
if (isset($_POST['confirm_delete'])) {
    if (!empty($_POST['teachers_to_delete'])) {
        $teachers_to_delete = $_POST['teachers_to_delete'];
        $placeholders = implode(',', array_fill(0, count($teachers_to_delete), '?'));

        // Delete selected teachers from the database
        $delete = $conn->prepare("DELETE FROM user_form WHERE id IN ($placeholders)");
        $delete->execute($teachers_to_delete);

        // Redirect to the same page after deleting the teachers
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Handle updating of a teacher
if (isset($_POST['update_teacher'])) {
    $teacher_id = $_POST['teacher_id'];
    $teacher_first_name = $_POST['teacher_first_name'];
    $teacher_last_name = $_POST['teacher_last_name'];
    $teacher_email = $_POST['teacher_email'];
    $teacher_gender = $_POST['teacher_gender'];

    // Update teacher's information in the database
    $update = $conn->prepare("UPDATE user_form SET first_name = ?, last_name = ?, email = ?, gender = ? WHERE id = ?");
    $update->execute([$teacher_first_name, $teacher_last_name, $teacher_email, $teacher_gender, $teacher_id]);

    // Redirect to the same page after updating the teacher
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Teacher Management</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { 
        font-family: Arial, sans-serif; 
        background-color: #fff; 
    }
    .container { 
        margin-top: 10px; 
    }
    .card { 
        margin-bottom: 20px; 
    }
    .table { 
        background-color: #ffffff; 
    }
    th, td { 
        color: #333333; 
    }
    h2 { 
        color:white; 
        margin-top: 1px; /* Adjusted margin */
        background-color:green; /* Background color */
        padding: 10px; /* Added padding */
    }
    .btn-success { 
        background-color: #8AC926; 
        border-color: #8AC926; 
    }
    .btn-success:hover { 
        background-color: #6ABE45; 
        border-color: #6ABE45; 
    }
    .btn-danger { 
        background-color: #FF595E; 
        border-color: #FF595E; 
    }
    .btn-danger:hover { 
        background-color: #FF3C3C; 
        border-color: #FF3C3C; 
    }
    .bg-info { 
        background-color: #47c144; 
    }
    .bg-info th { 
        color: #000; 
    }
    .back-button { 
        position: left; 
        top: 20px; 
        right: 20px; 
        margin-top: 20px; 
        width: 150px; 
        height: 50px; 
        padding: 10px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        border-radius: 5px; 
        font-size: 18px; 
        transition: background-color 0.3s ease; /* Added transition effect */
        background-color: green; /* Gold background color */
    }
    .back-button i {
        margin-right: 5px;
    }
    .back-button:hover { 
        background-color: gold; /* Hover color: lighter gold */
    }
</style>
</head>
<body>
<a href="../dashboards/admindash.php" class="btn btn-success back-button">Back</a>
<div class="container">
    <h2 class="text-center mb-4">Teacher Management</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Add Teacher</h5>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <div class="form-group">
                            <label for="teacher_first_name">First Name</label>
                            <input type="text" class="form-control" id="teacher_first_name" name="teacher_first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="teacher_last_name">Last Name</label>
                            <input type="text" class="form-control" id="teacher_last_name" name="teacher_last_name" required>
                        </div>
                        <div class="form-group">
                            <label for="teacher_email">Email</label>
                            <input type="email" class="form-control" id="teacher_email" name="teacher_email" required>
                        </div>
                        <div class="form-group">
                            <label for="teacher_gender">Gender</label>
                            <select class="form-control" id="teacher_gender" name="teacher_gender" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="teacher_password">Password</label>
                            <input type="password" class="form-control" id="teacher_password" name="teacher_password" required>
                        </div>
                        <button type="submit" class="btn btn-success" name="add_teacher">Add Teacher</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Update Teacher</h5>
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                <div class="form-group">
                                    <label for="teacher_id">Select Teacher</label>
                                    <select class="form-control" id="teacher_id" name="teacher_id" required>
                                        <?php
                                        $teachers = $conn->query("SELECT id, first_name, last_name FROM user_form WHERE type = 'teacher'")->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($teachers as $teacher) {
                                            echo '<option value="' . $teacher['id'] . '">' . $teacher['first_name'] . ' ' . $teacher['last_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary" name="edit_teacher">Edit Teacher</button>
                            </form>
                            <?php
                            if (isset($_POST['edit_teacher'])) {
                                $teacher_id = $_POST['teacher_id'];
                                $select_teacher = $conn->prepare("SELECT * FROM user_form WHERE id = ?");
                                $select_teacher->execute([$teacher_id]);
                                $teacher_data = $select_teacher->fetch(PDO::FETCH_ASSOC);
                            ?>
                            <hr>
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                <input type="hidden" name="teacher_id" value="<?php echo $teacher_data['id']; ?>">
                                <div class="form-group">
                                    <label for="teacher_first_name">First Name</label>
                                    <input type="text" class="form-control" id="teacher_first_name" name="teacher_first_name" value="<?php echo $teacher_data['first_name']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="teacher_last_name">Last Name</label>
                                    <input type="text" class="form-control" id="teacher_last_name" name="teacher_last_name" value="<?php echo $teacher_data['last_name']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="teacher_email">Email</label>
                                    <input type="email" class="form-control" id="teacher_email" name="teacher_email" value="<?php echo $teacher_data['email']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="teacher_gender">Gender</label>
                                    <select class="form-control" id="teacher_gender" name="teacher_gender" required>
                                        <option value="Male" <?php echo ($teacher_data['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo ($teacher_data['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success" name="update_teacher">Update Teacher</button>
                            </form>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mt-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Delete Teachers</h5>
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                <div class="form-group">
                                    <label for="teachers_to_delete">Select Teachers to Delete</label>
                                    <select multiple class="form-control" id="teachers_to_delete" name="teachers_to_delete[]" required>
                                        <?php
                                        $teachers = $conn->query("SELECT id, first_name, last_name FROM user_form WHERE type = 'teacher'")->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($teachers as $teacher) {
                                            echo '<option value="' . $teacher['id'] . '">' . $teacher['first_name'] . ' ' . $teacher['last_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-danger" name="confirm_delete">Delete Selected Teachers</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Teacher List</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-info">
                                <tr>
                                    <th>ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Gender</th>
                              
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $teachers = $conn->query("SELECT * FROM user_form WHERE type = 'teacher'")->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($teachers as $teacher) {
                                    echo '<tr>';
                                    echo '<td>' . $teacher['id'] . '</td>';
                                    echo '<td>' . $teacher['first_name'] . '</td>';
                                    echo '<td>' . $teacher['last_name'] . '</td>';
                                    echo '<td>' . $teacher['email'] . '</td>';
                                    echo '<td>' . $teacher['gender'] . '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
