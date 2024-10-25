<?php
include '../connection/config.php';

// Query to fetch all archived subjects
$query = $conn->query("SELECT * FROM archived_subjects");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Subjects</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 100px;
        }
        .header h1 {
            color: #007f3f;
            font-size: 2.5rem;
            margin-top: 10px;
        }
        .table thead {
            background-color: #007f3f;
            color: #fff;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn-success {
            background-color: #007f3f;
            border-color: #007f3f;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="../images/logo.png" alt="School Logo">
            <h1>Cavite State University</h1>
            <h2>Subject Archives</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Archived ID</th>
                        <th scope="col">Subject Code</th>
                        <th scope="col">Subject Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Time</th>
                        <th scope="col">Credits</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($query->rowCount() > 0) {
                        // Loop through each row (archived subject)
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['archived_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['subject_code']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['subject_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['time']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['credits']) . "</td>";
                            echo "<td>";
                            echo "<a href='restore_subject.php?id=" . $row['archived_id'] . "' class='btn btn-sm btn-success'>Restore</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No archived subjects found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <a href="../schedules/teacheradmin.php" class="btn btn-secondary">Back</a>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
