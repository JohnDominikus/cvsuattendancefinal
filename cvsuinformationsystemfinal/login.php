<?php
include 'connection/config.php';

$message = '';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $select = $conn->prepare("SELECT * FROM `user_form` WHERE email = ? AND password = ?");
    $select->execute([$email, $password]);
    $user = $select->fetch();

    if ($user) {
        session_start();
        $_SESSION['user_email'] = $email;
        $user_type = $user['type'];
        switch ($user_type) {
            case 'admin':
                header('location: dashboards/admindash.php');
                exit;
                break;
            case 'teacher':
                header('location: dashboards/teacherdash.php');
                exit;
                break;
            case 'student':
                header('location: dashboards/studentdash.php');
                exit;
                break;
            default:
                $message = 'Invalid user type';
                break;
        }
    } else {
        $message = 'Invalid email or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-green-500 flex items-center justify-center h-screen">

    <div class="w-full max-w-md">
        <form action="" method="post" class="bg-white bg-opacity-80 hover:bg-opacity-90 shadow-md rounded-lg px-8 pt-6 pb-8 mb-4 transition duration-300">
            <h3 class="mb-4 text-center text-xl font-bold text-white">Login</h3>
            <?php if (!empty($message)) : ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"><?php echo $message; ?></span>
                </div>
            <?php endif; ?>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" name="submit" class="bg-green-700 hover:bg-green-900 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">Login</button>
                <p class="text-sm"><span class="text-black">Don't have an account?</span> <a href="registration.php" class="text-blue-500 hover:text-blue-800">Register Now</a></p>
            </div>
        </form>
    </div>

</body>

</html>
