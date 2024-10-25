<?php
include('../connection/config.php');
session_start(); // Start the session at the beginning

// Fetch archived users
$archived_users = $conn->query("SELECT * FROM user_archive");

if (isset($_POST['retrieve'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("SELECT * FROM user_archive WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Retrieve user
        $query = "INSERT INTO user_form (first_name, last_name, email, gender, image, type, password) VALUES (:first_name, :last_name, :email, :gender, :image, :type, :password)";
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

        // Delete from archive
        $stmt = $conn->prepare("DELETE FROM user_archive WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    header("Location: userarchives.php");
    exit;
}

if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM user_archive WHERE id = :id");
    $stmt->execute([':id' => $id]);

    header("Location: userarchives.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Users</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-2xl mb-4">Archived Users</h1>
        <form action="usermanagement.php" method="get">
            <button type="submit" class="bg-gray-500 text-white py-2 px-4 rounded mb-4">Back to User Management</button>
        </form>
        <div class="bg-white shadow-md rounded">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 border">ID</th>
                        <th class="py-2 px-4 border">First Name</th>
                        <th class="py-2 px-4 border">Last Name</th>
                        <th class="py-2 px-4 border">Email</th>
                        <th class="py-2 px-4 border">Gender</th>
                        <th class="py-2 px-4 border">Type</th>
                        <th class="py-2 px-4 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $archived_users->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td class="py-2 px-4 border"><?php echo $row['id']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $row['first_name']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $row['last_name']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $row['email']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $row['gender']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $row['type']; ?></td>
                        <td class="py-2 px-4 border">
                            <form method="POST" action="userarchives.php" class="inline">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="retrieve" class="bg-green-500 text-white py-1 px-2 rounded">Retrieve</button>
                            </form>
                            <form method="POST" action="userarchives.php" class="inline">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete" class="bg-red-500 text-white py-1 px-2 rounded" onclick="return confirm('Are you sure you want to permanently delete this user?')">Delete Permanently</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
