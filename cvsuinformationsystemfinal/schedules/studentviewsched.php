<?php
// subjects_list.php

// Include database connection
include '../connection/config.php';

// Query to fetch all subjects
$query = $conn->query("SELECT * FROM subjects");

// Define CVSU colors
$cvsu_primary_color = "#28a745"; // Green color as primary color
$cvsu_secondary_color = "#004d28"; // Darker green for secondary color
$cvsu_logo_url = "../images/logo.png"; // Path to CVSU logo

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CVSU Subjects List</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .cvsu-header {
            background-color: <?php echo $cvsu_primary_color; ?>;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .cvsu-logo {
            max-height: 80px;
        }
        .table-container {
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .back-button {
            background-color: <?php echo $cvsu_primary_color; ?>;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
        .back-button:hover {
            background-color: <?php echo $cvsu_secondary_color; ?>;
        }
        .table th {
            background-color: <?php echo $cvsu_secondary_color; ?>;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-5">
        <div class="cvsu-header">
            <img src="<?php echo $cvsu_logo_url; ?>" alt="CVSU Logo" class="cvsu-logo mx-auto">
            <h1 class="mt-2">Cavite State University</h1>
        </div>
        
        <h2 class="text-center mb-4">Subjects List</h2>

        <div class="table-container">
            <?php if ($query->rowCount() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Subject ID</th>
                                <th scope="col">Subject Name</th>
                                <th scope="col">Subject Code</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $query->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['subject_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center">No subjects found.</p>
            <?php endif; ?>
        </div>

        <div class="text-center mt-5">
            <a href="../dashboards/studentdash.php" class="back-button">Back</a>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
