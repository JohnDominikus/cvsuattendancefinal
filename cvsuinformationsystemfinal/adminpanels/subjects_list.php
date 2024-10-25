<?php
// subjects_list.php

// Include database connection
include '../connection/config.php';

// Query to fetch all active subjects (not archived)
$query = $conn->query("SELECT * FROM subjects WHERE archived = 0");

// Check if there are any subjects
if ($query->rowCount() > 0) {
    $schoolName = "Cavite State University Bacoor Campus";
    $logoPath = "../dashboards/logo.png"; // Replace with actual path to your school logo

    // Start HTML content
    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>List of Subjects</title>";
    echo "<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>";
    echo "<style>
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
          </style>";
    echo "</head>";
    echo "<body>";

    // School Logo and Name
    echo "<div class='text-center'>";
    echo "<img src='$logoPath' alt='School Logo' class='print-logo'><br>";
    echo "<h3>$schoolName</h3>";
    echo "</div>";

    // Table of Subjects
    echo "<h2 class='mt-4'>List of Subjects</h2>";
    echo "<div class='table-responsive'>";
    echo "<table class='table table-bordered table-success'>";
    echo "<thead class='thead-dark'>";
    echo "<tr>";
    echo "<th scope='col'>Subject ID</th>";
    echo "<th scope='col'>Subject Name</th>";
    echo "<th scope='col'>Subject Code</th>";
    echo "<th scope='col'>Actions</th>"; // Added Actions column header
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    // Loop through each row (subject)
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['subject_id'] . "</td>";
        echo "<td>" . $row['subject_name'] . "</td>";
        echo "<td>" . $row['subject_code'] . "</td>";
        echo "<td>";
        echo "<a href='../schedules/edits.php?id=" . $row['subject_id'] . "' class='btn btn-primary btn-sm no-print'>Edit</a>"; // Edit button with subject_id as parameter
        echo " ";
        echo "<a href='delete_subject.php?id=" . $row['subject_id'] . "' class='btn btn-danger btn-sm no-print' onclick='return confirm(\"Are you sure you want to archive this subject?\");'>Archive</a>"; // Archive button with subject_id as parameter
        echo "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";

    // Back Button (hidden in print)
    echo "<a href='../dashboards/teacherdash.php' class='btn btn-warning btn-block mt-3 no-print'>Back</a>";

    // Print Button (hidden in print)
    echo "<button class='btn btn-info btn-block mt-3 no-print' onclick='window.print()'>Print</button>";

    // End HTML content
    echo "</body>";
    echo "</html>";

} else {
    echo "<h2>List of Subjects</h2>";
    echo "<p>No subjects found.</p>";
}
?>
