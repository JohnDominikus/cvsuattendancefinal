<?php
include('../connection/config.php');
session_start(); // Start the session at the beginning

// Fetch users
$users = $conn->query("SELECT * FROM user_form");

if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("SELECT * FROM user_form WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Archive user
        $query = "INSERT INTO user_archive (first_name, last_name, email, gender, image, type, password) VALUES (:first_name, :last_name, :email, :gender, :image, :type, :password)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':first_name' => $user['first_name'],
            ':last_name' => $user['last_name'],
            ':email' => $user['email'],
            ':gender' => $user['gender'],
            ':image' => $user['image'],
            ':type' => $user['type'],
            ':password' => $user['password']
        ]);

        // Delete user
        $stmt = $conn->prepare("DELETE FROM user_form WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    header("Location: usermanagement.php");
    exit;
}

if (isset($_POST['archive'])) {
    header("Location: archive.php");
    exit;
}

if (isset($_POST['add_user'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $query = "INSERT INTO user_form (first_name, last_name, email, gender, image, type, password) VALUES (:first_name, :last_name, :email, :gender, :image, :type, :password)";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $email,
        ':gender' => $gender,
        ':image' => $image,
        ':type' => 'user', // Default type
        ':password' => $password
    ]);

    header("Location: usermanagement.php");
    exit;
}

if (isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $type = $_POST['type'];
    $password = $_POST['password'];
    
    // Handle image upload
    $image = $_POST['current_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $query = "UPDATE user_form SET first_name = :first_name, last_name = :last_name, email = :email, gender = :gender, image = :image, type = :type, password = :password WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $email,
        ':gender' => $gender,
        ':image' => $image,
        ':type' => $type,
        ':password' => $password,
        ':id' => $id
    ]);

    header("Location: usermanagement.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
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
            color: white;
            margin-top: 1px;
            background-color: green;
            padding: 10px;
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
            transition: background-color 0.3s ease;
            background-color: green;
        }
        .back-button i {
            margin-right: 5px;
        }
        .back-button:hover {
            background-color: gold;
        }
    </style>
</head>
<body>
<a href="../dashboards/admindash.php" class="btn btn-success back-button">Back</a>
<div class="container">
    <h2 class="text-center mb-4">User Management</h2>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Add User</h5>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePasswordVisibility('password')">
                                        <i class="fa fa-eye" id="togglePasswordIcon"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" class="form-control" id="image" name="image">
                        </div>
                        <button type="submit" class="btn btn-success" name="add_user">Add User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">User List</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-info">
                                <tr>
                                    <th>ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Gender</th>
                                   
                                    <th>Type</th>
                                    <th>Password</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['first_name']; ?></td>
                                    <td><?php echo $row['last_name']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['gender']; ?></td>
                                 
                                    <td><?php echo $row['type']; ?></td>
                                    <td>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password<?php echo $row['id']; ?>" value="<?php echo $row['password']; ?>" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text" onclick="togglePasswordVisibility('password<?php echo $row['id']; ?>')">
                                                    <i class="fa fa-eye" id="togglePasswordIcon<?php echo $row['id']; ?>"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <form method="POST" action="" enctype="multipart/form-data" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="current_image" value="<?php echo $row['image']; ?>">
                                            <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                        </form>
                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#updateModal<?php echo $row['id']; ?>">Edit</button>
                                    </td>
                                </tr>

                                <!-- Update Modal -->
                                <div class="modal fade" id="updateModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Update User</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="current_image" value="<?php echo $row['image']; ?>">
                                                    <div class="form-group">
                                                        <label for="first_name">First Name</label>
                                                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $row['first_name']; ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="last_name">Last Name</label>
                                                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $row['last_name']; ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="email">Email</label>
                                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $row['email']; ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="gender">Gender</label>
                                                        <select class="form-control" id="gender" name="gender" required>
                                                            <option value="Male" <?php if($row['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                                            <option value="Female" <?php if($row['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="type">Type</label>
                                                        <select class="form-control" id="type" name="type" required>
                                                            <option value="user" <?php if($row['type'] == 'user') echo 'selected'; ?>>User</option>
                                                            <option value="admin" <?php if($row['type'] == 'admin') echo 'selected'; ?>>Admin</option>
                                                            <option value="teacher" <?php if($row['type'] == 'teacher') echo 'selected'; ?>>Teacher</option>
                                                            <option value="student" <?php if($row['type'] == 'student') echo 'selected'; ?>>Student</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="password">Password</label>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control" id="passwordUpdate<?php echo $row['id']; ?>" name="password" value="<?php echo $row['password']; ?>" required>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text" onclick="togglePasswordVisibility('passwordUpdate<?php echo $row['id']; ?>')">
                                                                    <i class="fa fa-eye" id="togglePasswordIconUpdate<?php echo $row['id']; ?>"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="image">Image</label>
                                                        <input type="file" class="form-control" id="image" name="image">
                                                    </div>
                                                    <button type="submit" class="btn btn-success" name="update_user">Update User</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
function togglePasswordVisibility(id) {
    const passwordField = document.getElementById(id);
    const toggleIcon = document.querySelector(`#${id} + .input-group-append .input-group-text .fa`);

    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
    }
}
</script>
</body>
</html>
