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

// Determine the back URL based on user type
$back_url = "../dashboards/{$user_type}dash.php?user_id={$user_id}";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance Report - CVSU Theme</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Body background color */
        body {
            background-color: #f0f9eb; /* Light green background */
        }

        /* Container width */
        .container {
            max-width: 100%;
            padding: 0 20px;
        }

        /* CVSU logo */
        .cvsu-logo {
            max-width: 100px;
            height: auto;
        }

        /* School name */
        .school-name {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1a4731; /* Dark green text (CVSU green) */
        }

        /* Search form */
        .search-form {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        /* Search input field */
        .search-input {
            flex: 1;
            margin-right: 10px;
            padding: 8px;
            border: 1px solid #2c7a4b; /* Green border (CVSU green) */
            border-radius: 4px;
        }

        /* Search button */
        .search-btn {
            padding: 8px 16px;
            background-color: #2c7a4b; /* Green button (CVSU green) */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Table container */
        .table-container {
            background-color: #e6f4ea; /* Slight greenish background */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Table header row */
        .table-header {
            background-color: #2c7a4b; /* Dark green header (CVSU green) */
            color: white;
        }

        /* Table header cells */
        .table-header th {
            padding: 10px;
            text-align: center;
        }

        /* Table data cells */
        .table-data td {
            padding: 10px;
            text-align: center;
        }

        /* Print button */
        .print-btn,
        .back-btn {
            display: block;
            width: 100%;
            max-width: 200px;
            margin: 10px auto;
            padding: 10px 20px;
            background-color: #38a169; /* Tailwind green button */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto mt-4">
        <div class="flex flex-col md:flex-row items-center justify-between mb-4">
            <div class="text-center mb-4 md:mb-0">
                <img src="../images/logo.png" alt="CVSU Logo" class="cvsu-logo">
                <h3 class="school-name">Cavite State University</h3>
            </div>
            <div class="w-full md:w-auto md:ml-auto">
                <!-- Search form -->
                <form action="" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search..." class="search-input" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                    <input type="hidden" name="type" value="<?php echo htmlspecialchars($user_type); ?>">
                    <button type="submit" class="search-btn">Search</button>
                </form>
            </div>
        </div>

        <div class="table-container">
            <?php if (!empty($result)): ?>
            <table class="table table-bordered table-hover">
                <thead class="table-header">
                    <tr>
                        <th>Student ID</th>
                        <th>Image</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Gender</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="table-data">
                    <?php foreach ($result as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['student_no']); ?></td>
                        <td><img src="../images/student_image/<?php echo htmlspecialchars($row['image']); ?>" alt="Student Image" class="max-w-xs"  alt="Student Image" style="max-width: 100px;"></td>
                        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['student_status']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="text-center">
                <button type="button" class="print-btn" onclick="window.print()">Print Attendance</button>
                <a href="../dashboards/teacherdash.php?>" class="back-btn mt-2">Back</a>
            </div>
            <?php else: ?>
                <p class="text-center text-green-800">No results found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
