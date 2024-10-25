<?php
include 'connection/config.php';

$message = '';

if (isset($_POST['submit'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $image = $_FILES['image']['name'];
    $password = $_POST['password'];
    $type = $_POST['type'];

    // Image upload path
    $target_dir = "images/";
    $target_file = $target_dir . basename($image);

    // Ensure the directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        // Check if email already exists
        $select = $conn->prepare("SELECT * FROM `user_form` WHERE email = ?");
        $select->execute([$email]);
        if ($select->rowCount() > 0) {
            $message = 'Email already exists';
        } else {
            // Insert user data into database
            $insert = $conn->prepare("INSERT INTO `user_form` (first_name, last_name, email, gender, image, type, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert->execute([$first_name, $last_name, $email, $gender, $image, $type, $password]);
            if ($insert) {
                // Redirect to login page
                header('Location: login.php');
                exit;
            } else {
                $message = 'Registration failed';
            }
        }
    } else {
        $message = 'Failed to upload image';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Additional styles specific to this page */
        body {
            background-color: #6EE7B7; /* Adjusted background color */
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            width: 100%;
            max-width: 400px; /* Adjusted maximum width */
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent background */
            border-radius: 8px;
            box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.1); /* Soft shadow */
            padding: 2rem;
        }
        .form-container h3 {
            margin-bottom: 1.5rem;
            font-size: 1.5rem; /* Adjusted font size */
            font-weight: bold;
            text-align: center;
            color: #0D9488; /* Adjusted text color */
        }
        .form-container form {
            transition: background-color 0.3s, box-shadow 0.3s;
        }
        .form-container form:hover {
            background-color: rgba(255, 255, 255, 1); /* Full opacity on hover */
            box-shadow: 0px 8px 32px rgba(0, 0, 0, 0.2); /* Slightly stronger shadow on hover */
        }
        .form-container .error-message {
            background-color: #FECACA; /* Error message background color */
            border-color: #FEB2B2; /* Error message border color */
            color: #E53E3E; /* Error message text color */
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="form-container">
            <h3>Registration</h3>
            <?php if (!empty($message)) : ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 error-message" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"><?php echo $message; ?></span>
                </div>
            <?php endif; ?>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="first_name" class="block text-gray-700 text-sm font-bold mb-2">First Name</label>
                    <input type="text" name="first_name" id="first_name" placeholder="Enter first name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="last_name" class="block text-gray-700 text-sm font-bold mb-2">Last Name</label>
                    <input type="text" name="last_name" id="last_name" placeholder="Enter last name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" name="email" id="email" placeholder="Enter email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="gender" class="block text-gray-700 text-sm font-bold mb-2">Gender</label>
                    <select name="gender" id="gender" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="" disabled selected>Select gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Profile Image</label>
                    <input type="file" name="image" id="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="type" class="block text-gray-700 text-sm font-bold mb-2">User Type</label>
                    <select name="type" id="type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="" disabled selected>Select user type</option>
                        <option value="admin">Admin</option>
                        <option value="teacher">Teacher</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" name="submit" class="bg-green-700 hover:bg-green-900 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">Register</button>
                    <p class="text-sm"><span class="text-black">Already have an account?</span> <a href="login.php" class="text-blue-500 hover:text-blue-800">Login Now</a></p>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
