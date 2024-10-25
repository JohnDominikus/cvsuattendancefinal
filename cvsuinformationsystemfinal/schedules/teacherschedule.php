<?php
// subjects_list.php

// Include database connection
include '../connection/config.php';

// Query to fetch all subjects
$query = $conn->query("SELECT * FROM subjects");

// Check if there are any subjects
if ($query->rowCount() > 0) {
    $schoolName = "Cavite State University Bacoor Campus";
    $logoPath = "../dashboards/logo.png"; // Replace with the actual path to your school logo
    ?>
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>List of Subjects</title>
        <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>
        <style>
            body {
                font-family: Arial, sans-serif;
                padding: 20px;
            }
            .print-logo {
                max-width: 150px;
            }
            .table-responsive {
                margin-top: 20px;
            }
            @media print {
                .no-print {
                    display: none !important; /* Hide elements with class 'no-print' when printing */
                }
            }
        </style>
    </head>
    <body>

    <!-- School Logo and Name -->
    <div class='text-center'>
        <img src='../images/logo.png' alt='School Logo' class='print-logo'style="max-width: 100px;"><br>
        <h3><?php echo htmlspecialchars($schoolName); ?></h3>
    </div>

    <!-- Table of Subjects -->
    <h2 class='mt-4'>List of Subjects</h2>
    <div class='table-responsive'>
        <table class='table table-bordered table-success'>
            <thead class='thead-dark'>
                <tr>
                    <th scope='col'>Subject ID</th>
                    <th scope='col'>Subject Name</th>
                    <th scope='col'>Subject Code</th>
                    <th scope='col'>Actions</th> <!-- Added Actions column header -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through each row (subject)
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['subject_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                        <td>
                            <a href='../schedules/edits.php?id=<?php echo htmlspecialchars($row['subject_id']); ?>' class='btn btn-primary btn-sm no-print'>Edit</a> <!-- Edit button with subject_id as parameter -->
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Back Button (hidden in print) -->
    <a href='../dashboards/teacherdash.php' class='btn btn-warning btn-block mt-3 no-print'>Back</a>

    <!-- Print Button with Confirmation Prompt -->
    <button class='btn btn-info btn-block mt-3 no-print' onclick='confirmPrint()'>Print</button>

    <!-- JavaScript function for confirmation prompt -->
    <script>
        function confirmPrint() {
            if (confirm('Are you sure you want to print?')) {
                window.print();
            }
        }
    </script>

    </body>
    </html>
    <?php
} else {
    ?>
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>List of Subjects</title>
        <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>
    </head>
    <body>
    <h2>List of Subjects</h2>
    <p>No subjects found.</p>
    </body>
    </html>
    <?php
}
?>
