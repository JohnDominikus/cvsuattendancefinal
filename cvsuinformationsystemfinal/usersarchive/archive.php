<?php
session_start();
include '../connection/config.php';

// Function to archive users
function archiveUsers($user_ids, $user_type, $conn)
{
    // Determine the appropriate archive table based on user type
    $archive_table = "{$user_type}_archive";

    // Prepare select query to fetch users to be archived
    $placeholders = implode(',', array_fill(0, count($user_ids), '?'));

    $select_query = "SELECT * FROM user_form WHERE id IN ($placeholders) AND type = ?";
    $select_stmt = $conn->prepare($select_query);
    $select_stmt->bindValue(($placeholders + 1), $user_type, PDO::PARAM_STR);

    // Bind values and execute select query
    foreach ($user_ids as $key => $id) {
        $select_stmt->bindValue(($key + 1), $id, PDO::PARAM_INT);
    }

    $select_stmt->execute();
    $users_to_archive = $select_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Move users to archive table
    $archive_query = "INSERT INTO $archive_table (first_name, last_name, email, gender, image, type, password)
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
    $archive_stmt = $conn->prepare($archive_query);

    foreach ($users_to_archive as $user) {
        $archive_stmt->execute([
            $user['first_name'],
            $user['last_name'],
            $user['email'],
            $user['gender'],
            $user['image'],
            $user['type'],
            $user['password']
        ]);
    }

    // Prepare delete query to remove users from main table
    $delete_query = "DELETE FROM user_form WHERE id IN ($placeholders) AND type = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bindValue(($placeholders + 1), $user_type, PDO::PARAM_STR);

    // Bind values and execute delete query
    foreach ($user_ids as $key => $id) {
        $delete_stmt->bindValue(($key + 1), $id, PDO::PARAM_INT);
    }

    return $delete_stmt->execute();
}

// Capture admin's ID from session
$admin_id = $_SESSION['admin_id'];

// Check if form is submitted for archiving users
if (isset($_POST['archive_users'])) {
    // Ensure at least one user is selected for archiving
    if (!isset($_POST['user_ids']) || empty($_POST['user_ids'])) {
        die("Please select at least one user to archive.");
    }

    $user_ids = $_POST['user_ids'];
    $user_type = $_POST['user_type']; // Assuming you have a form element to select user type

    // Archive users
    if (archiveUsers($user_ids, $user_type, $conn)) {
        // Redirect back to archive page after archiving
        $redirect_url = "archive.php";
        header("Location: $redirect_url");
        exit();
    } else {
        die("Failed to archive users.");
    }
}

// Check if form is submitted for restoring users
if (isset($_POST['restore_users'])) {
    // Ensure at least one user is selected for restoration
    if (!isset($_POST['archived_user_ids']) || empty($_POST['archived_user_ids'])) {
        die("Please select at least one archived user to restore.");
    }

    $archived_user_ids = $_POST['archived_user_ids'];
    $user_type = $_POST['archived_user_type']; // Assuming you have a form element to select archived user type

    // Determine the appropriate archive table based on user type
    $archive_table = "{$user_type}_archive";

    // Restore users
    $placeholders = implode(',', array_fill(0, count($archived_user_ids), '?'));

    $restore_query = "INSERT INTO user_form (first_name, last_name, email, gender, image, type, password)
                      SELECT first_name, last_name, email, gender, image, type, password
                      FROM $archive_table
                      WHERE id IN ($placeholders)";
    $restore_stmt = $conn->prepare($restore_query);

    // Bind values and execute restore query
    foreach ($archived_user_ids as $key => $id) {
        $restore_stmt->bindValue(($key + 1), $id, PDO::PARAM_INT);
    }

    if ($restore_stmt->execute()) {
        // Delete restored users from archive table
        $delete_query = "DELETE FROM $archive_table WHERE id IN ($placeholders)";
        $delete_stmt = $conn->prepare($delete_query);

        // Bind values and execute delete query
        foreach ($archived_user_ids as $key => $id) {
            $delete_stmt->bindValue(($key + 1), $id, PDO::PARAM_INT);
        }

        if ($delete_stmt->execute()) {
            // Redirect back to archive page after restoring
            $redirect_url = "archive.php";
            header("Location: $redirect_url");
            exit();
        } else {
            die("Failed to delete restored users from archive.");
        }
    } else {
        die("Failed to restore users.");
    }
}

// Fetch all archived users for display
$query = "SELECT * FROM user_form WHERE type != 'admin'"; // Exclude admins from archive display if needed
$stmt = $conn->query($query);
$archived_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Archives</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3 class="text-center">User Archives</h3>

    <form action="" method="post">
        <label for="user_type">Select User Type:</label>
        <select name="user_type" id="user_type" class="form-control">
            <option value="admin">Admin</option>
            <option value="teacher">Teacher</option>
            <option value="student">Student</option>
        </select>

        <table class="table table-striped mt-3">
            <thead>
            <tr>
                <th></th>
                <th>User ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Gender</th>
                <th>Image</th>
                <th>Type</th>
                <th>Password</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($archived_users as $user): ?>
                <tr>
                    <td><input type="checkbox" name="archived_user_ids[]" value="<?php echo $user['id']; ?>"></td>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['gender']); ?></td>
                    <td><?php echo htmlspecialchars($user['image']); ?></td>
                    <td><?php echo htmlspecialchars($user['type']); ?></td>
                    <td><?php echo htmlspecialchars($user['password']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit" name="archive_users" class="btn btn-danger
